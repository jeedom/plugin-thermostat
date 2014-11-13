<?php

/* This file is part of Jeedom.
 *
 * Jeedom is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Jeedom is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Jeedom. If not, see <http://www.gnu.org/licenses/>.
 */

/* * ***************************Includes********************************* */
require_once dirname(__FILE__) . '/../../../../core/php/core.inc.php';

class thermostat extends eqLogic {
    /*     * *************************Attributs****************************** */

    /*     * ***********************Methode static*************************** */

    public static function pull($_options) {
        $thermostat = thermostat::byId($_options['thermostat_id']);
        if (is_object($thermostat)) {
            if (isset($_options['stop']) && $_options['stop'] == 1) {
                $consigne = $thermostat->getCmd(null, 'order')->execCmd();
                $temp = jeedom::evaluateExpression($thermostat->getConfiguration('temperature_indoor'));
                if ($thermostat->getConfiguration('lastState') == 'heat' && $temp < ($consigne - 1)) {
                    $thermostat->setConfiguration('coeff_indoor_heat', $thermostat->getConfiguration('coeff_indoor_heat') + 10);
                    $thermostat->setConfiguration('coeff_outdoor', $thermostat->getConfiguration('coeff_outdoor') + 5);
                    self::temporal($_options);
                } else {
                    $thermostat->stop();
                }
                $cron = cron::byClassAndFunction('thermostat', 'stop', $_options);
                if (is_object($cron)) {
                    $cron->remove();
                }
            } else {
                self::temporal($_options);
            }
        } else {
            $cron = cron::byClassAndFunction('thermostat', 'pull', $_options);
            if (is_object($cron)) {
                $cron->remove();
            }
            throw new Exception('Thermostat ID non trouvé : ' . $_options['thermostat_id'] . '. Tache supprimé');
        }
    }

    public static function hysteresis($_options) {
        $thermostat = thermostat::byId($_options['thermostat_id']);
        if (is_object($thermostat)) {
            $status = $thermostat->getCmd(null, 'status')->execCmd();
            if ($thermostat->getCmd(null, 'mode')->execCmd() == __('Off', __FILE__)) {
                if ($status != __('Arrêté', __FILE__)) {
                    $thermostat->stop();
                }
                return;
            }
            if ($status == __('Suspendu', __FILE__)) {
                return;
            }
            $windows = $thermostat->getConfiguration('window');
            foreach ($windows as $window) {
                $cmd = cmd::byId(str_replace('#', '', $window['cmd']));
                if (is_object($cmd) && $cmd->execCmd() == 1) {
                    return;
                }
            }
            $temp = jeedom::evaluateExpression($thermostat->getConfiguration('temperature_indoor'));
            $consigne = $thermostat->getCmd(null, 'order')->execCmd();
            $thermostat->getCmd(null, 'order')->addHistoryValue($consigne);
            $hysteresis_low = $consigne - $thermostat->getConfiguration('hysteresis_threshold', 1);
            $hysteresis_hight = $consigne + $thermostat->getConfiguration('hysteresis_threshold', 1);
            $action = 'none';
            if ($temp < $hysteresis_low) {
                $action = 'heat';
            }
            if ($temp > $hysteresis_hight) {
                $action = 'cool';
            }
            if ($action == 'heat' && $thermostat->getConfiguration('lastState') == 'cool' && ($consigne - 2 * $thermostat->getConfiguration('hysteresis_threshold')) < $temp) {
                $action = 'none';
            }
            if ($action == 'cool' && $thermostat->getConfiguration('lastState') == 'heat' && ($consigne + 2 * $thermostat->getConfiguration('hysteresis_threshold')) > $temp) {
                $action = 'none';
            }
            if ($status == __('Chauffage', __FILE__) && $temp > $hysteresis_hight) {
                $action = 'stop';
            }
            if ($status == __('Climatisation', __FILE__) && $temp < $hysteresis_low) {
                $action = 'stop';
            }

            if ($action == 'heat') {
                if ($status != __('Chauffage', __FILE__)) {
                    $thermostat->heat();
                }
            } elseif ($action == 'cool') {
                if ($status != __('Climatisation', __FILE__)) {
                    $thermostat->cool();
                }
            } elseif ($action == 'stop') {
                if ($status != __('Arrêté', __FILE__)) {
                    $thermostat->stop();
                }
            }
        }
    }

    public static function temporal($_options) {
        $thermostat = thermostat::byId($_options['thermostat_id']);
        if (is_object($thermostat)) {
            $thermostat->reschedule(date('Y-m-d H:i:s', strtotime('+' . $thermostat->getConfiguration('cycle') . ' min ' . date('Y-m-d H:i:s'))));
            $mode = $thermostat->getCmd(null, 'mode')->execCmd();
            $status = $thermostat->getCmd(null, 'status')->execCmd();
            if ($mode == 'Off') {
                if ($status != __('Arrêté', __FILE__)) {
                    $thermostat->stop();
                }
                return;
            }
            if ($status == __('Suspendu', __FILE__)) {
                return;
            }
            $windows = $thermostat->getConfiguration('window');
            foreach ($windows as $window) {
                $cmd = cmd::byId(str_replace('#', '', $window['cmd']));
                if (is_object($cmd) && $cmd->execCmd() == 1) {
                    return;
                }
            }
            $temp_in = jeedom::evaluateExpression($thermostat->getConfiguration('temperature_indoor'));
            $temp_out = jeedom::evaluateExpression($thermostat->getConfiguration('temperature_outdoor'));

            if (!is_numeric($temp_in)) {
                return;
            }
            if ($thermostat->getConfiguration('autolearn') == 1 && strtotime($thermostat->getConfiguration('endDate')) < strtotime('now')) {
                if ($thermostat->getConfiguration('last_power') < 100 && $thermostat->getConfiguration('last_power') > 0) {
                    $learn_outdoor = false;
                    if ($thermostat->getConfiguration('lastState') == 'heat') {
                        if ($temp_in >= $thermostat->getConfiguration('lastTempIn')) {
                            $coeff_indoor_heat = $thermostat->getConfiguration('coeff_indoor_heat') * (($thermostat->getConfiguration('lastOrder') - $thermostat->getConfiguration('lastTempIn')) / ($temp_in - $thermostat->getConfiguration('lastTempIn') ));
                            $coeff_indoor_heat = ($thermostat->getConfiguration('coeff_indoor_heat') * $thermostat->getConfiguration('coeff_indoor_heat_autolearn') + $coeff_indoor_heat) / ($thermostat->getConfiguration('coeff_indoor_heat_autolearn') + 1);
                            $thermostat->setConfiguration('coeff_indoor_heat_autolearn', min($thermostat->getConfiguration('coeff_indoor_heat_autolearn') + 1, 50));
                            if ($coeff_indoor_heat < 0) {
                                $coeff_indoor_heat = 0;
                            }
                            $thermostat->setConfiguration('coeff_indoor_heat', round($coeff_indoor_heat, 2));
                        } else {
                            $learn_outdoor = true;
                        }
                    }
                    if ($thermostat->getConfiguration('lastState') == 'cool') {
                        if ($temp_in <= $thermostat->getConfiguration('lastTempIn')) {
                            $coeff_indoor_cool = $thermostat->getConfiguration('coeff_indoor_cool') * (($thermostat->getConfiguration('lastTempIn') - $thermostat->getConfiguration('lastOrder')) / ($thermostat->getConfiguration('lastTempIn') - $temp_in ));
                            $coeff_indoor_cool = ($thermostat->getConfiguration('coeff_indoor_cool') * $thermostat->getConfiguration('coeff_indoor_cool_autolearn') + $coeff_indoor_cool) / ($thermostat->getConfiguration('coeff_indoor_cool_autolearn') + 1);
                            $thermostat->setConfiguration('coeff_indoor_cool_autolearn', min($thermostat->getConfiguration('coeff_indoor_cool_autolearn') + 1, 50));
                            if ($coeff_indoor_cool < 0) {
                                $coeff_indoor_cool = 0;
                            }
                            $thermostat->setConfiguration('coeff_indoor_cool', round($coeff_indoor_cool, 2));
                        } else {
                            $learn_outdoor = true;
                        }
                    }
                    if ($learn_outdoor) {
                        $coeff_in = 1;
                        if ($thermostat->getConfiguration('lastState') == 'heat') {
                            $coeff_in = $thermostat->getConfiguration('coeff_indoor_heat');
                        }
                        if ($thermostat->getConfiguration('lastState') == 'cool') {
                            $coeff_in = $thermostat->getConfiguration('coeff_indoor_cool');
                        }
                        $coeff_outdoor = $thermostat->getConfiguration('coeff_outdoor') + ($coeff_in * (($thermostat->getConfiguration('lastOrder') - $temp_out) / ($temp_in - $thermostat->getConfiguration('lastTempIn') )));
                        $coeff_outdoor = ($thermostat->getConfiguration('coeff_outdoor') * $thermostat->getConfiguration('coeff_outdoor_autolearn') + $coeff_outdoor) / ($thermostat->getConfiguration('coeff_outdoor_autolearn') + 1);
                        $thermostat->setConfiguration('coeff_outdoor_autolearn', min($thermostat->getConfiguration('coeff_outdoor_autolearn') + 1, 50));
                        if ($coeff_outdoor < 0) {
                            $coeff_outdoor = 0;
                        }
                        $thermostat->setConfiguration('coeff_outdoor', round($coeff_outdoor, 2));
                    }
                }
            }
            $thermostat->setConfiguration('lastState', 'stop');
            $consigne = $thermostat->getCmd(null, 'order')->execCmd();
            $thermostat->getCmd(null, 'order')->addHistoryValue($consigne);

            if (!is_numeric($temp_out)) {
                $temp_out = $consigne;
            }

            $diff_in = abs($consigne - $temp_in);
            $diff_out = $consigne - $temp_out;
            $direction = ($consigne > $temp_in) ? +1 : -1;

            $thermostat->setConfiguration('lastOrder', $consigne);
            $thermostat->setConfiguration('lastTempIn', $temp_in);
            $thermostat->setConfiguration('lastTempOut', $temp_out);
            $coeff_out = $thermostat->getConfiguration('coeff_outdoor');
            $coeff_in = ($direction > 0) ? $thermostat->getConfiguration('coeff_indoor_heat') : $thermostat->getConfiguration('coeff_indoor_cool');
            $power = ($diff_in * $coeff_in) + ($diff_out * $coeff_out);
            log::add('thermostat', 'debug', 'Power calcul : (' . $diff_in . ' * ' . $coeff_in . ') + (' . $diff_out . ' * ' . $coeff_out . ')');
            if ($power > 100) {
                $power = 100;
            }
            if ($power < 0) {
                $power = 0;
            }
            $thermostat->setConfiguration('last_power', $power);
            $cycle = jeedom::evaluateExpression($thermostat->getConfiguration('cycle'));
            $thermostat->setConfiguration('endDate', date('Y-m-d H:i:s', strtotime('+' . ceil($cycle * 0.9) . ' min ' . date('Y-m-d H:i:s'))));
            if ($power < $thermostat->getConfiguration('minCycleDuration', 5)) {
                return;
            }
            $duration = ($power * $cycle) / 100;
            log::add('thermostat', 'debug', 'Cycle duration : ' . $duration);
            $thermostat->save();
            $thermostat->reschedule(date('Y-m-d H:i:s', strtotime('+' . round($duration) . ' min ' . date('Y-m-d H:i:s'))), true);
            if ($direction > 0) {
                if ($status != __('Chauffage', __FILE__)) {
                    $thermostat->heat();
                }
            } else {
                if ($status != __('Climatisation', __FILE__)) {
                    $thermostat->cool();
                }
            }
        }
    }

    public static function window($_option) {
        $thermostat = thermostat::byId($_option['thermostat_id']);
        if (is_object($thermostat) && $thermostat->getIsEnable() == 1) {
            $windows = $thermostat->getConfiguration('window');
            foreach ($windows as $window) {
                if ('#' . $_option['event_id'] . '#' == $window['cmd']) {
                    if ($_option['value'] == 0) {
                        $thermostat->windowClose($window);
                    }
                    if ($_option['value'] == 1) {
                        $thermostat->windowOpen($_option['event_id']);
                    }
                }
            }
        }
    }

    public static function cron() {
        foreach (thermostat::byType('thermostat') as $thermostat) {
            if ($thermostat->getIsEnable() == 1) {
                if ($thermostat->getConfiguration('engine', 'temporal') == 'temporal') {
                    $cron = cron::byClassAndFunction('thermostat', 'pull', array('thermostat_id' => intval($thermostat->getId())));
                    if (!is_object($cron)) {
                        $thermostat->reschedule(date('Y-m-d H:i:s', strtotime('+1 min ' . date('Y-m-d H:i:s'))));
                    } else {
                        if ($cron->getState() != 'run') {
                            try {
                                $cron->getNextRunDate();
                            } catch (Exception $ex) {
                                log::add('thermostat', 'debug', 'Reschedule temporal cron');
                                $thermostat->reschedule(date('Y-m-d H:i:s', strtotime('+1 min ' . date('Y-m-d H:i:s'))));
                            }
                        }
                    }
                }
                if ($thermostat->getConfiguration('engine', 'temporal') == 'hysteresis' && $thermostat->getConfiguration('hysteresis_cron') != '') {
                    try {
                        $c = new Cron\CronExpression($thermostat->getConfiguration('hysteresis_cron'), new Cron\FieldFactory);
                        if ($c->isDue()) {
                            thermostat::hysteresis(array('thermostat_id' => $thermostat->getId()));
                        }
                    } catch (Exception $e) {
                        log::add('thermostat', 'error', $thermostat->getHumanName() . ' : ' . $e->getMessage());
                    }
                }
                if ($thermostat->getConfiguration('repeat_commande_cron') != '') {
                    try {
                        $c = new Cron\CronExpression($thermostat->getConfiguration('repeat_commande_cron'), new Cron\FieldFactory);
                        if ($c->isDue()) {
                            switch ($thermostat->getCmd(null, 'status')) {
                                case __('Chauffage', __FILE__):
                                    $thermostat->heat(true);
                                    break;
                                case __('Arrêté', __FILE__):
                                    $thermostat->stop(true);
                                    break;
                                case __('Climatisation', __FILE__):
                                    $thermostat->cool(true);
                                    break;
                            }
                        }
                    } catch (Exception $e) {
                        log::add('thermostat', 'error', $thermostat->getHumanName() . ' : ' . $e->getMessage());
                    }
                }
            }
        }
    }

    public static function start() {
        foreach (thermostat::byType('thermostat') as $thermostat) {
            if ($thermostat->getIsEnable() == 1) {
                if (strtolower($thermostat->getCmd(null, 'mode')->execCmd()) == 'off') {
                    continue;
                }
                $thermostat->stop();
                if ($thermostat->getConfiguration('engine', 'temporal') == 'temporal') {
                    thermostat::temporal(array('thermostat_id' => $thermostat->getId()));
                }
                if ($thermostat->getConfiguration('engine', 'temporal') == 'hysteresis') {
                    thermostat::hysteresis(array('thermostat_id' => $thermostat->getId()));
                }
            }
        }
    }

    /*     * *********************Methode d'instance************************* */

    public function windowClose($window) {
        if ($this->getCmd(null, 'status')->execCmd() != __('Suspendu', __FILE__)) {
            return;
        }
        $windows = $this->getConfiguration('window');
        foreach ($windows as $window_state) {
            $cmd = cmd::byId(str_replace('#', '', $window_state['cmd']));
            if (is_object($cmd) && $cmd->execCmd() == 1) {
                return;
            }
        }
        $restartTime = (isset($window['restartTime']) && $window['restartTime'] != '') ? $window['restartTime'] : 0;
        if (is_numeric($restartTime) && $restartTime > 0) {
            sleep($restartTime * 60);
        }
        $this->getCmd(null, 'status')->event(__('Calcul', __FILE__));
        if ($this->getConfiguration('engine', 'temporal') == 'temporal') {
            thermostat::temporal(array('thermostat_id' => $this->getId()));
        }
        if ($this->getConfiguration('engine', 'temporal') == 'hysteresis') {
            thermostat::hysteresis(array('thermostat_id' => $this->getId()));
        }
    }

    public function windowOpen($_trigger_id) {
        if ($this->getCmd(null, 'mode')->execCmd() == __('Off', __FILE__) || $this->getCmd(null, 'status')->execCmd() == __('Suspendu', __FILE__)) {
            return;
        }
        $windows = $this->getConfiguration('window');
        foreach ($windows as $window) {
            if ('#' . $_trigger_id . '#' == $window['cmd']) {
                $stopTime = (isset($window['stopTime']) && $window['stopTime'] != '') ? $window['stopTime'] : 0;
                if (is_numeric($stopTime) && $stopTime > 0) {
                    sleep($stopTime * 60);
                }
                $cmd = cmd::byId($_trigger_id);
                if (is_object($cmd) && $cmd->execCmd() == 1) {
                    $this->stop();
                    $this->getCmd(null, 'status')->event(__('Suspendu', __FILE__));
                }
            }
        }
        return true;
    }

    public function reschedule($_next = null, $_stop = false) {
        $options = array('thermostat_id' => intval($this->getId()));
        if ($_stop) {
            $options['stop'] = intval(1);
        }
        $cron = cron::byClassAndFunction('thermostat', 'pull', $options);
        if ($_next != null) {
            if (!is_object($cron)) {
                $cron = new cron();
                $cron->setClass('thermostat');
                $cron->setFunction('pull');
                $cron->setOption($options);
                $cron->setLastRun(date('Y-m-d H:i:s'));
            }
            $_next = strtotime($_next);
            $cron->setTimeout($this->getConfiguration('cycle', 60) + 10);
            $cron->setSchedule(date('i', $_next) . ' ' . date('H', $_next) . ' ' . date('d', $_next) . ' ' . date('m', $_next) . ' * ' . date('Y', $_next));
            $cron->save();
        } else {
            if (is_object($cron)) {
                $cron->remove();
            }
        }
    }

    public function preRemove() {
        $cron = cron::byClassAndFunction('thermostat', 'pull', array('thermostat_id' => intval($this->getId())));
        if (is_object($cron)) {
            $cron->remove();
        }
        $cron = cron::byClassAndFunction('thermostat', 'pull', array('thermostat_id' => intval($this->getId()), 'stop' => intval(1)));
        if (is_object($cron)) {
            $cron->remove();
        }
        $listener = listener::byClassAndFunction('thermostat', 'window', array('thermostat_id' => intval($this->getId())));
        if (is_object($listener)) {
            $listener->remove();
        }
        $listener = listener::byClassAndFunction('thermostat', 'hysteresis', array('thermostat_id' => intval($this->getId())));
        if (is_object($listener)) {
            $listener->remove();
        }
    }

    public function preSave() {
        if ($this->getConfiguration('order_max') === '') {
            $this->setConfiguration('order_max', 28);
        }
        if ($this->getConfiguration('order_min') === '') {
            $this->setConfiguration('order_min', 15);
        }
        if ($this->getConfiguration('order_min') > $this->getConfiguration('order_max')) {
            throw new Exception(__('La température de consigne minimum ne peut être supérieur à la consigne maximum', __FILE__));
        }
        if ($this->getConfiguration('coeff_indoor_heat') === '') {
            $this->setConfiguration('coeff_indoor_heat', 10);
        }
        if ($this->getConfiguration('coeff_indoor_cool') === '') {
            $this->setConfiguration('coeff_indoor_cool', 10);
        }
        if ($this->getConfiguration('coeff_outdoor') === '') {
            $this->setConfiguration('coeff_outdoor', 0.5);
        }
        if ($this->getConfiguration('minCycleDuration') === '') {
            $this->setConfiguration('minCycleDuration', 5);
        }
        if ($this->getConfiguration('minCycleDuration') < 5 || $this->getConfiguration('minCycleDuration') > 90) {
            throw new Exception(__('Le temps de chauffe minimum doit etre compris entre 5% et 90%', __FILE__));
        }
        if ($this->getConfiguration('cycle') === '') {
            $this->setConfiguration('cycle', 60);
        }
        if ($this->getConfiguration('cycle') < 15) {
            throw new Exception(__('Le temps de cycle doit etre supérieur à 15min', __FILE__));
        }
        if ($this->getConfiguration('autolearn') === '') {
            $this->setConfiguration('autolearn', 1);
        }
        if ($this->getConfiguration('coeff_indoor_cool') === '') {
            $this->setConfiguration('coeff_indoor_cool', 0);
        }
        if ($this->getConfiguration('coeff_indoor_cool_autolearn') === '') {
            $this->setConfiguration('coeff_indoor_cool_autolearn', 0);
        }
        if ($this->getConfiguration('coeff_indoor_heat_autolearn') === '') {
            $this->setConfiguration('coeff_indoor_heat_autolearn', 0);
        }
        if ($this->getConfiguration('coeff_outdoor_autolearn') === '') {
            $this->setConfiguration('coeff_outdoor_autolearn', 0);
        }
        foreach ($this->getConfiguration('existingMode') as $existingMode) {
            if (strtolower($existingMode['name']) == __('off', __FILE__)) {
                throw new Exception(__('Vous ne pouvez faire un mode s\'appelant Off car une commande Off est automatiquement creer', __FILE__));
            }
            if (strtolower($existingMode['name']) == __('status', __FILE__)) {
                throw new Exception(__('Vous ne pouvez faire un mode s\'appelant Status car une commande Off est automatiquement creer', __FILE__));
            }
            if (strtolower($existingMode['name']) == __('thermostat', __FILE__)) {
                throw new Exception(__('Vous ne pouvez faire un mode s\'appelant Thermostat car une commande Off est automatiquement creer', __FILE__));
            }
        }
        $this->setCategory('heating', 1);
    }

    public function postSave() {
        if ($this->getIsEnable() == 1) {
            $order = $this->getCmd(null, 'order');
            if (!is_object($order)) {
                $order = new thermostatCmd();
                $order->setIsVisible(0);
            }
            $order->setEqLogic_id($this->getId());
            $order->setName(__('Consigne', __FILE__));
            $order->setType('info');
            $order->setSubType('numeric');
            $order->setIsHistorized(1);
            $order->setLogicalId('order');
            $order->setUnite('°C');
            $order->getConfiguration('historyDefaultValue', '#previsous#');
            $order->setConfiguration('maxValue', $this->getConfiguration('order_max'));
            $order->setConfiguration('minValue', $this->getConfiguration('order_min'));
            $order->setEventOnly(1);
            $order->setOrder(1);
            $order->save();

            $thermostat = $this->getCmd(null, 'thermostat');
            if (!is_object($thermostat)) {
                $thermostat = new thermostatCmd();
                $thermostat->setTemplate('dashboard', 'thermostat');
                $thermostat->setTemplate('mobile', 'thermostat');
            }
            $thermostat->setEqLogic_id($this->getId());
            $thermostat->setName(__('Thermostat', __FILE__));
            $thermostat->setConfiguration('maxValue', $this->getConfiguration('order_max'));
            $thermostat->setConfiguration('minValue', $this->getConfiguration('order_min'));
            $thermostat->setType('action');
            $thermostat->setSubType('slider');
            $thermostat->setUnite('°C');
            $thermostat->setLogicalId('thermostat');
            $thermostat->setIsVisible(1);
            $thermostat->setOrder(6);
            $thermostat->setValue($order->getId());
            $thermostat->save();

            $status = $this->getCmd(null, 'status');
            if (!is_object($status)) {
                $status = new thermostatCmd();
            }
            $status->setEqLogic_id($this->getId());
            $status->setName(__('Status', __FILE__));
            $status->setType('info');
            $status->setSubType('string');
            $status->setLogicalId('status');
            $status->setIsVisible(1);
            $status->setEventOnly(1);
            $status->setOrder(5);
            $status->save();

            $actif = $this->getCmd(null, 'actif');
            if (!is_object($actif)) {
                $actif = new thermostatCmd();
            }
            $actif->setEqLogic_id($this->getId());
            $actif->setName(__('Actif', __FILE__));
            $actif->setType('info');
            $actif->setSubType('binary');
            $actif->setLogicalId('actif');
            $actif->setIsVisible(0);
            $actif->setEventOnly(1);
            $actif->setIsHistorized(1);
            $actif->save();

            $lockState = $this->getCmd(null, 'lock_state');
            if (!is_object($lockState)) {
                $lockState = new thermostatCmd();
                $lockState->setTemplate('dashboard', 'lock');
                $lockState->setTemplate('mobile', 'lock');
            }
            $lockState->setEqLogic_id($this->getId());
            $lockState->setName(__('Verrouillé', __FILE__));
            $lockState->setType('info');
            $lockState->setSubType('binary');
            $lockState->setLogicalId('lock_state');
            $lockState->setIsVisible(0);
            $lockState->setEventOnly(1);
            $lockState->setOrder(7);
            $lockState->save();

            $lock = $this->getCmd(null, 'lock');
            if (!is_object($lock)) {
                $lock = new thermostatCmd();
                $lock->setTemplate('dashboard', 'smallLock');
                $lock->setTemplate('mobile', 'lock');
            }
            $lock->setEqLogic_id($this->getId());
            $lock->setName('lock');
            $lock->setType('action');
            $lock->setSubType('other');
            $lock->setLogicalId('lock');
            $lock->setIsVisible(1);
            $lock->setValue($lockState->getId());
            $lock->setOrder(7);
            $lock->save();

            $unlock = $this->getCmd(null, 'unlock');
            if (!is_object($unlock)) {
                $unlock = new thermostatCmd();
                $unlock->setTemplate('dashboard', 'smallLock');
                $unlock->setTemplate('mobile', 'lock');
            }
            $unlock->setEqLogic_id($this->getId());
            $unlock->setName('unlock');
            $unlock->setType('action');
            $unlock->setSubType('other');
            $unlock->setLogicalId('unlock');

            $unlock->setIsVisible(1);
            $unlock->setValue($lockState->getId());
            $unlock->setOrder(7);
            $unlock->save();

            $temperature = $this->getCmd(null, 'temperature');
            if (!is_object($temperature)) {
                $temperature = new thermostatCmd();
                $temperature->setTemplate('dashboard', 'badge');
                $temperature->setTemplate('mobile', 'badge');
                $temperature->setDisplay('parameters', array('displayHistory' => 'display : none;'));
            }
            $temperature->setEqLogic_id($this->getId());
            $temperature->setName(__('Temperature', __FILE__));
            $temperature->setType('info');
            $temperature->setSubType('numeric');
            $temperature->setLogicalId('temperature');
            $temperature->setOrder(0);
            $temperature->setEventOnly(0);
            $temperature->setUnite('°C');
            $temperature->setIsVisible(1);
            $temperature->setIsHistorized(1);

            $value = '';
            preg_match_all("/#([0-9]*)#/", $this->getConfiguration('temperature_indoor'), $matches);
            foreach ($matches[1] as $cmd_id) {
                if (is_numeric($cmd_id)) {
                    $cmd = cmd::byId($cmd_id);
                    if (is_object($cmd) && $cmd->getType() == 'info') {
                        $value .= '#' . $cmd_id . '#';
                        break;
                    }
                }
            }
            $temperature->setValue($value);
            $temperature->save();
            $temperature->event(jeedom::evaluateExpression($this->getConfiguration('temperature_indoor')));

            $heatOnly = $this->getCmd(null, 'heat_only');
            if (!is_object($heatOnly)) {
                $heatOnly = new thermostatCmd();
            }
            $heatOnly->setEqLogic_id($this->getId());
            $heatOnly->setName(__('Chauffage seulement', __FILE__));
            $heatOnly->setType('action');
            $heatOnly->setSubType('other');
            $heatOnly->setLogicalId('heat_only');
            $heatOnly->setIsVisible(0);
            $heatOnly->save();

            $coolOnly = $this->getCmd(null, 'cool_only');
            if (!is_object($coolOnly)) {
                $coolOnly = new thermostatCmd();
            }
            $coolOnly->setEqLogic_id($this->getId());
            $coolOnly->setName(__('Climatisation seulement', __FILE__));
            $coolOnly->setType('action');
            $coolOnly->setSubType('other');
            $coolOnly->setLogicalId('cool_only');
            $coolOnly->setIsVisible(0);
            $coolOnly->save();

            $allAllow = $this->getCmd(null, 'all_allow');
            if (!is_object($allAllow)) {
                $allAllow = new thermostatCmd();
            }
            $allAllow->setEqLogic_id($this->getId());
            $allAllow->setName(__('Tout autorisé', __FILE__));
            $allAllow->setType('action');
            $allAllow->setSubType('other');
            $allAllow->setLogicalId('all_allow');
            $allAllow->setIsVisible(0);
            $allAllow->save();


            $mode = $this->getCmd(null, 'mode');
            if (!is_object($mode)) {
                $mode = new thermostatCmd();
            }
            $mode->setEqLogic_id($this->getId());
            $mode->setName(__('Mode', __FILE__));
            $mode->setType('info');
            $mode->setSubType('string');
            $mode->setLogicalId('mode');
            $mode->setOrder(3);
            $mode->setIsVisible(1);
            $mode->setEventOnly(1);
            $mode->save();

            $off = $this->getCmd(null, 'off');
            if (!is_object($off)) {
                $off = new thermostatCmd();
            }
            $off->setEqLogic_id($this->getId());
            $off->setName(__('Off', __FILE__));
            $off->setType('action');
            $off->setSubType('other');
            $off->setLogicalId('off');
            $off->setOrder(1);
            $off->setIsVisible(1);
            $off->save();
        }
        $knowModes = array();
        foreach ($this->getConfiguration('existingMode') as $existingMode) {
            $knowModes[$existingMode['name']] = $existingMode;
        }
        foreach ($this->getCmd() as $cmd) {
            if ($cmd->getLogicalId() == 'modeAction') {
                if (isset($knowModes[$cmd->getName()])) {
                    $cmd->setOrder(1);
                    if (isset($knowModes[$cmd->getName()]['isVisible'])) {
                        $cmd->setIsVisible($knowModes[$cmd->getName()]['isVisible']);
                    }
                    $cmd->save();
                    unset($knowModes[$cmd->getName()]);
                } else {
                    $cmd->remove();
                }
            }
        }
        foreach ($knowModes as $knowMode) {
            $mode = new thermostatCmd();
            $mode->setEqLogic_id($this->getId());
            $mode->setName($knowMode['name']);
            $mode->setType('action');
            $mode->setSubType('other');
            $mode->setLogicalId('modeAction');
            if (isset($knowMode['isVisible'])) {
                $mode->setIsVisible($knowMode['isVisible']);
            }
            $mode->setOrder(1);
            $mode->save();
        }

        if ($this->getIsEnable() == 1) {
            $windows = $this->getConfiguration('window');
            if (count($windows) > 0) {
                $listener = listener::byClassAndFunction('thermostat', 'window', array('thermostat_id' => intval($this->getId())));
                if (!is_object($listener)) {
                    $listener = new listener();
                }
                $listener->setClass('thermostat');
                $listener->setFunction('window');
                $listener->setOption(array('thermostat_id' => intval($this->getId())));
                $listener->emptyEvent();
                foreach ($windows as $window) {
                    $listener->addEvent($window['cmd']);
                }
                $listener->save();
            }

            if ($this->getConfiguration('engine', 'temporal') == 'hysteresis') {
                $listener = listener::byClassAndFunction('thermostat', 'hysteresis', array('thermostat_id' => intval($this->getId())));
                if (!is_object($listener)) {
                    $listener = new listener();
                }
                $listener->setClass('thermostat');
                $listener->setFunction('hysteresis');
                $listener->setOption(array('thermostat_id' => intval($this->getId())));
                $listener->emptyEvent();
                preg_match_all("/#([0-9]*)#/", $this->getConfiguration('temperature_indoor'), $matches);
                foreach ($matches[1] as $cmd_id) {
                    $listener->addEvent($cmd_id);
                }
                $listener->save();
            } else {
                $listener = listener::byClassAndFunction('thermostat', 'hysteresis', array('thermostat_id' => intval($this->getId())));
                if (is_object($listener)) {
                    $listener->remove();
                }
            }
            if ($this->getConfiguration('engine', 'temporal') != 'temporal' || $this->getIsEnable() != 1) {
                $cron = cron::byClassAndFunction('thermostat', 'pull', array('thermostat_id' => intval($this->getId())));
                if (is_object($cron)) {
                    $cron->stop();
                    $cron->remove();
                }
            }
        } else {
            $cron = cron::byClassAndFunction('thermostat', 'pull', array('thermostat_id' => intval($this->getId())));
            if (is_object($cron)) {
                $cron->remove();
            }
            $cron = cron::byClassAndFunction('thermostat', 'pull', array('thermostat_id' => intval($this->getId()), 'stop' => intval(1)));
            if (is_object($cron)) {
                $cron->remove();
            }
            $listener = listener::byClassAndFunction('thermostat', 'window', array('thermostat_id' => intval($this->getId())));
            if (is_object($listener)) {
                $listener->remove();
            }
            $listener = listener::byClassAndFunction('thermostat', 'hysteresis', array('thermostat_id' => intval($this->getId())));
            if (is_object($listener)) {
                $listener->remove();
            }
        }
    }

    public function heat($_force = false) {
        if (!$_force) {
            if ($this->getCmd(null, 'mode')->execCmd() == __('Off', __FILE__) || $this->getCmd(null, 'status')->execCmd() == __('Suspendu', __FILE__)) {
                return;
            }
            if ($this->getConfiguration('allow_mode', 'all') != 'all' && $this->getConfiguration('allow_mode', 'all') != 'heat') {
                $this->stop();
                return false;
            }
            if (count($this->getConfiguration('heating')) == 0) {
                $this->stop();
                return false;
            }
        }
        $consigne = $this->getCmd(null, 'order')->execCmd();
        foreach ($this->getConfiguration('heating') as $action) {
            $cmd = cmd::byId(str_replace('#', '', $action['cmd']));
            if (is_object($cmd)) {
                $executeCmd = true;
                foreach ($cmd->getEqLogic()->getCmd() as $cmd_find) {
                    $eqLogics = eqLogic::byTypeAndSearhConfiguration('thermostat', '#' . $cmd_find->getId() . '#');
                    if (is_array($eqLogics) && count($eqLogics) != 0) {
                        foreach ($eqLogics as $eqLogic) {
                            if ($eqLogic->getId() != $this->getId() && $eqLogic->getCmd(null, 'status')->execCmd() == __('Climatisation', __FILE__)) {
                                $executeCmd = false;
                                break(2);
                            }
                        }
                    }
                }
                if ($executeCmd) {
                    try {
                        $options = array();
                        if (isset($action['options'])) {
                            $options = $action['options'];
                            foreach ($options as $key => $value) {
                                $options[$key] = str_replace('#slider#', $consigne, jeedom::evaluateExpression($value));
                            }
                        }
                        $cmd->execCmd($options);
                    } catch (Exception $e) {
                        log::add('thermostat', 'error', __('Erreur lors de l\'éxecution de ', __FILE__) . $cmd->getHumanName() . __('. Détails : ', __FILE__) . $e->getMessage());
                    }
                }
            } else {
                log::add('thermostat', 'error', __('Impossible de trouver la commande ', __FILE__) . $action['cmd']);
            }
        }
        $this->refresh();
        $this->getCmd(null, 'status')->event(__('Chauffage', __FILE__));
        $this->setConfiguration('lastState', 'heat');
        $this->save();
        $this->getCmd(null, 'actif')->event(1);
    }

    public function cool($_force = false) {
        if (!$_force) {
            if ($this->getCmd(null, 'mode')->execCmd() == __('Off', __FILE__) || $this->getCmd(null, 'status')->execCmd() == __('Suspendu', __FILE__)) {
                return;
            }
            if ($this->getConfiguration('allow_mode', 'all') != 'all' && $this->getConfiguration('allow_mode', 'all') != 'cool') {
                $this->stop();
                return false;
            }
            if (count($this->getConfiguration('cooling')) == 0) {
                $this->stop();
                return false;
            }
        }
        $consigne = $this->getCmd(null, 'order')->execCmd();
        foreach ($this->getConfiguration('cooling') as $action) {
            $cmd = cmd::byId(str_replace('#', '', $action['cmd']));
            if (is_object($cmd)) {
                $executeCmd = true;
                foreach ($cmd->getEqLogic()->getCmd() as $cmd_find) {
                    $eqLogics = eqLogic::byTypeAndSearhConfiguration('thermostat', '#' . $cmd_find->getId() . '#');
                    if (is_array($eqLogics) && count($eqLogics) != 0) {
                        foreach ($eqLogics as $eqLogic) {
                            if ($eqLogic->getId() != $this->getId() && $eqLogic->getCmd(null, 'status')->execCmd() == __('Chauffage', __FILE__)) {
                                $executeCmd = false;
                                break(2);
                            }
                        }
                    }
                }
                if ($executeCmd) {
                    try {
                        $options = array();
                        if (isset($action['options'])) {
                            $options = $action['options'];
                            foreach ($options as $key => $value) {
                                $options[$key] = str_replace('#slider#', $consigne, jeedom::evaluateExpression($value));
                            }
                        }
                        $cmd->execCmd($options);
                    } catch (Exception $e) {
                        log::add('thermostat', 'error', __('Erreur lors de l\'éxecution de ', __FILE__) . $cmd->getHumanName() . __('. Détails : ', __FILE__) . $e->getMessage());
                    }
                }
            }
        }
        $this->refresh();
        $this->getCmd(null, 'status')->event(__('Climatisation', __FILE__));
        $this->setConfiguration('lastState', 'cool');
        $this->save();
        $this->getCmd(null, 'actif')->event(1);
    }

    public function stop($_force = false) {
        if (!$_force) {
            if ($this->getCmd(null, 'status')->execCmd() == __('Arrêté', __FILE__)) {
                return;
            }
        }
        $consigne = $this->getCmd(null, 'order')->execCmd();
        foreach ($this->getConfiguration('stoping') as $action) {
            $cmd = cmd::byId(str_replace('#', '', $action['cmd']));
            if (is_object($cmd)) {
                $executeCmd = true;
                foreach ($cmd->getEqLogic()->getCmd() as $cmd_find) {
                    $eqLogics = eqLogic::byTypeAndSearhConfiguration('thermostat', '#' . $cmd_find->getId() . '#');
                    if (is_array($eqLogics) && count($eqLogics) != 0) {
                        foreach ($eqLogics as $eqLogic) {
                            if ($eqLogic->getId() != $this->getId() && $eqLogic->getCmd(null, 'status')->execCmd() != __('Arrêté', __FILE__)) {
                                $executeCmd = false;
                                break(2);
                            }
                        }
                    }
                }
                if ($executeCmd) {
                    try {
                        $options = array();
                        if (isset($action['options'])) {
                            $options = $action['options'];
                            foreach ($options as $key => $value) {
                                $options[$key] = str_replace('#slider#', $consigne, jeedom::evaluateExpression($value));
                            }
                        }
                        $cmd->execCmd($options);
                    } catch (Exception $e) {
                        log::add('thermostat', 'error', __('Erreur lors de l\'éxecution de ', __FILE__) . $cmd->getHumanName() . __('. Détails : ', __FILE__) . $e->getMessage());
                    }
                }
            }
        }
        $this->refresh();
        $this->getCmd(null, 'status')->event(__('Arrêté', __FILE__));
        $this->save();
        $this->getCmd(null, 'actif')->event(0);
    }

    public function orderChange() {
        if ($this->getCmd(null, 'mode')->execCmd() == __('Off', __FILE__) || $this->getCmd(null, 'status')->execCmd() == __('Suspendu', __FILE__)) {
            return;
        }
        if (count($this->getConfiguration('orderChange')) > 0) {
            $consigne = $this->getCmd(null, 'order')->execCmd();
            foreach ($this->getConfiguration('orderChange') as $action) {
                $cmd = cmd::byId(str_replace('#', '', $action['cmd']));
                if (is_object($cmd)) {
                    try {
                        $options = array();
                        if (isset($action['options'])) {
                            $options = $action['options'];
                            foreach ($options as $key => $value) {
                                $options[$key] = jeedom::evaluateExpression(str_replace('#slider#', $consigne, $value));
                            }
                        }
                        $cmd->execCmd($options);
                    } catch (Exception $e) {
                        log::add('thermostat', 'error', __('Erreur lors de l\'éxecution de ', __FILE__) . $cmd->getHumanName() . __('. Détails : ', __FILE__) . $e->getMessage());
                    }
                }
            }
        }
    }

    public function executeMode($_name) {
        foreach ($this->getConfiguration('existingMode') as $existingMode) {
            if ($_name == $existingMode['name']) {
                foreach ($existingMode['actions'] as $action) {
                    $cmd = cmd::byId(str_replace('#', '', $action['cmd']));
                    if (is_object($cmd)) {
                        if ($cmd->getEqLogic_id() == $this->getId() && $cmd->getLogicalId() == 'modeAction') {
                            continue;
                        }
                        try {
                            $consigne = $this->getCmd(null, 'order')->execCmd();
                            $options = array();
                            if (isset($action['options'])) {
                                $options = $action['options'];
                                foreach ($options as $key => $value) {
                                    $options[$key] = jeedom::evaluateExpression(str_replace('#slider#', $consigne, $value));
                                }
                            }
                            $cmd->execCmd($options);
                        } catch (Exception $e) {
                            log::add('thermostat', 'error', __('Erreur lors de l\'éxecution de ', __FILE__) . $cmd->getHumanName() . __('. Détails : ', __FILE__) . $e->getMessage());
                        }
                    }
                }
            }
        }
        $this->getCmd(null, 'mode')->event($_name);
    }

}

class thermostatCmd extends cmd {
    /*     * *************************Attributs****************************** */

    public function dontRemoveCmd() {
        return true;
    }

    public function preSave() {
        
    }

    public function execute($_options = array()) {
        $eqLogic = $this->getEqLogic();
        $lockState = $eqLogic->getCmd(null, 'lock_state');
        if ($this->getLogicalId() == 'lock') {
            $lockState->event(1);
            return;
        }
        if ($this->getLogicalId() == 'unlock') {
            $lockState->event(0);
            return;
        }
        if ($this->getLogicalId() == 'lock_state') {
            return 0;
        }
        if ($this->getLogicalId() == 'temperature') {
            return jeedom::evaluateExpression($eqLogic->getConfiguration('temperature_indoor'));
        }
        if ($this->getLogicalId() == 'cool_only') {
            $eqLogic->setConfiguration('allow_mode', 'cool');
            $eqLogic->save();
            return;
        }
        if ($this->getLogicalId() == 'heat_only') {
            $eqLogic->setConfiguration('allow_mode', 'heat');
            $eqLogic->save();
            return;
        }
        if ($this->getLogicalId() == 'all_allow') {
            $eqLogic->setConfiguration('allow_mode', 'all');
            $eqLogic->save();
            return;
        }

        if ($this->getLogicalId() == 'thermostat') {
            if (!is_object($lockState) || $lockState->execCmd() != 1) {
                $min = $this->getConfiguration('minValue');
                $max = $this->getConfiguration('maxValue');
                if (!isset($_options['slider']) || $_options['slider'] == '' || !is_numeric(intval($_options['slider']))) {
                    $_options['slider'] = (($max - $min) / 2) + $min;
                }
                if ($_options['slider'] > $max) {
                    $_options['slider'] = $max;
                }
                if ($_options['slider'] < $min) {
                    $_options['slider'] = $min;
                }
                $eqLogic->getCmd(null, 'order')->event($_options['slider']);
                $eqLogic->getCmd(null, 'mode')->event(__('Aucun', __FILE__));
                $eqLogic->orderChange();
                if ($eqLogic->getCmd(null, 'status')->execCmd() != __('Suspendu', __FILE__)) {
                    if ($eqLogic->getConfiguration('engine', 'temporal') == 'temporal') {
                        thermostat::temporal(array('thermostat_id' => $eqLogic->getId()));
                    }
                    if ($eqLogic->getConfiguration('engine', 'temporal') == 'hysteresis') {
                        thermostat::hysteresis(array('thermostat_id' => $eqLogic->getId()));
                    }
                }
            } else {
                $thermostat = $eqLogic->getCmd(null, 'thermostat');
                nodejs::pushUpdate('eventCmd', array('cmd_id' => $thermostat->getId(), 'eqLogic_id' => $thermostat->getEqLogic_id(), 'object_id' => $thermostat->getEqLogic()->getObject_id()));
            }
            return '';
        }
        if (!is_object($lockState) || $lockState->execCmd() == 0) {
            if ($this->getLogicalId() == 'modeAction') {
                $eqLogic->executeMode($this->getName());
            }
            if ($this->getLogicalId() == 'off') {
                $eqLogic->getCmd(null, 'mode')->event(__('Off', __FILE__));
                $eqLogic->stop();
            }
        }
        return '';
    }

    /*     * ***********************Methode static*************************** */

    /*     * *********************Methode d'instance************************* */
}

?>
