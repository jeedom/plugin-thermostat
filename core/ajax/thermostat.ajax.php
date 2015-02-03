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

try {
    require_once dirname(__FILE__) . '/../../../../core/php/core.inc.php';
    include_file('core', 'authentification', 'php');

    if (!isConnect()) {
        throw new Exception(__('401 - Accès non autorisé', __FILE__));
    }

    if (init('action') == 'getThermostat') {
        if (init('object_id') == '') {
            $_GET['object_id'] = $_SESSION['user']->getOptions('defaultDashboardObject');
        }
        $object = object::byId(init('object_id'));
        if (!is_object($object)) {
            $object = object::rootObject();
        }
        if (!is_object($object)) {
            throw new Exception(__('Aucun objet racine trouvé',__FILE__));
        }
        $return = array('object' => utils::o2a($object));

        $date = array(
            'start' => init('dateStart'),
            'end' => init('dateEnd'),
            );
        
        if ($date['start'] == '') {
            $date['start'] = date('Y-m-d', strtotime('-6 days' . date('Y-m-d')));
        }
        if ($date['end'] == '') {
            $date['end'] = date('Y-m-d', strtotime('+1 days' . date('Y-m-d')));
        }
        $return['date'] = $date;
        foreach ($object->getEqLogic() as $eqLogic) {
            if ($eqLogic->getIsVisible() == '1' && $eqLogic->getEqType_name() == 'thermostat') {
                $return['eqLogics'][] = array('eqLogic' => utils::o2a($eqLogic), 'html' => $eqLogic->toHtml(init('version')));
            }
        }
        ajax::success($return);
    }
    

    if (init('action') == 'getLinkCalendar') {
        $thermostat = thermostat::byId(init('id'));
        if (!is_object($thermostat)) {
            throw new Exception(__('Thermostat non trouvé : ',__FILE__).init('id'));
        }
        $return = array();
        foreach ($thermostat->getCmd(null, 'modeAction', null, true) as $mode) {
            $return = array_merge (calendar_event::searchByCmd($mode->getId()),$return);
        }
        $thermostat_cmd = $thermostat->getCmd(null, 'thermostat');
        $return = array_merge (calendar_event::searchByCmd($thermostat_cmd->getId()),$return);
        ajax::success(utils::o2a($return));
    }

    throw new Exception(__('Aucune methode correspondante à : ', __FILE__) . init('action'));
} catch (Exception $e) {
    ajax::error(displayExeption($e), $e->getCode());
}
