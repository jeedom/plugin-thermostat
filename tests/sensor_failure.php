<?php
// Run with: php tests/sensor_failure.php (no Jeedom installation required).
if (PHP_SAPI !== 'cli') {
	http_response_code(404);
	exit;
}

error_reporting(E_ALL);
set_error_handler(function ($severity, $message, $file, $line) {
	throw new ErrorException($message, 0, $severity, $file, $line);
});

function __($text, $file) {
	return $text;
}

class cmd {
	public $value;
	public $collectDate;
	public $onEvent;

	public function __construct($value = '', $collectDate = '') {
		$this->value = $value;
		$this->collectDate = $collectDate;
	}
	public function execCmd() { return $this->value; }
	public function getCollectDate() { return $this->collectDate; }
	public function event($value) {
		$this->value = $value;
		if (is_callable($this->onEvent)) {
			call_user_func($this->onEvent);
		}
	}
	public function addHistoryValue($value) {}
}

class eqLogic {
	public static $instances = array();
	public $configuration = array();
	public $cache = array();
	public $commands = array();
	public function getId() { return 1; }
	public function getIsEnable() { return 1; }
	public function getHumanName() { return '[Maison][Salon]'; }
	public function getCmd($type, $id) { return $this->commands[$id]; }
	public function getConfiguration($key, $default = '') { return isset($this->configuration[$key]) ? $this->configuration[$key] : $default; }
	public function getCache($key, $default = '') { return isset($this->cache[$key]) ? $this->cache[$key] : $default; }
	public function setCache($key, $value) { $this->cache[$key] = $value; }
	public function checkAndUpdateCmd($key, $value) {
		if ($this->commands[$key]->value !== $value) {
			$this->commands[$key]->event($value);
		}
	}
	public static function byId($id) { return self::$instances[$id]; }
	public static function byType($type, $enabled = false) { return self::$instances; }
}

class log {
	public static $errors = array();
	public static function add($class, $level, $message) {
		if ($level === 'error') {
			self::$errors[] = $message;
		}
	}
}

class scenarioExpression {
	public static $calls = array();
	public static $onAction;
	public static function createAndExec($type, $command, $options) {
		self::$calls[] = $options;
		if (is_callable(self::$onAction)) {
			call_user_func(self::$onAction);
		}
	}
}

// Load the real plugin classes, replacing only the unavailable Jeedom bootstrap.
$source = file_get_contents(__DIR__ . '/../core/class/thermostat.class.php');
$source = preg_replace('/^require_once .*core\.inc\.php.*;\r?$/m', '', $source, 1, $bootstrapCount);
if ($bootstrapCount !== 1) {
	throw new RuntimeException('Jeedom bootstrap not found');
}
eval(substr($source, 5));

class TestThermostat extends thermostat {
	public $regulationActions = 0;
	public function reschedule($_next = null, $_stop = false, $_smartThermostat = false) {}
	public function heat($_repeat = false) {
		$this->regulationActions++;
		$this->commands['status']->event('Chauffage');
		return true;
	}
	public function cool($_repeat = false) { $this->regulationActions++; return true; }
	public function stopThermostat($_repeat = false, $_suspend = false) { $this->regulationActions++; }
}

function fixture($temperature = 21, $ageMinutes = 0) {
	$t = new TestThermostat();
	$t->configuration = array(
		'engine' => 'hysteresis', 'maxTimeUpdateTemp' => 30,
		'temperature_indoor_min' => 18, 'temperature_indoor_max' => 28,
		'failure' => array(array('cmd' => 'notify', 'options' => array(
			'title' => '#failure_reason#', 'message' => '#failure_message#',
			'temperature' => '#temperature#', 'date' => '#last_collect#', 'slider' => '#slider#',
			'nested' => array('message' => '#failure_message#', 'enabled' => true),
		))),
	);
	foreach (array('mode' => 'Confort', 'status' => 'Chauffage', 'order' => 21, 'temperature' => $temperature, 'temperature_outdoor' => 10, 'failure_reason' => '', 'failure_message' => '') as $id => $value) {
		$t->commands[$id] = new cmd($value);
	}
	$t->commands['temperature']->collectDate = date('Y-m-d H:i:s', time() - $ageMinutes * 60);
	eqLogic::$instances = array(1 => $t);
	scenarioExpression::$calls = array();
	scenarioExpression::$onAction = null;
	log::$errors = array();
	return $t;
}

function same($expected, $actual) {
	if ($expected !== $actual) {
		throw new RuntimeException('Expected ' . var_export($expected, true) . ', got ' . var_export($actual, true));
	}
}

function contains($needle, $actual) {
	if (strpos($actual, $needle) === false) {
		throw new RuntimeException('Missing ' . $needle . ' in ' . $actual);
	}
}

function check($t, $notifyThresholds = true) {
	return $t->checkSensorFailure($t->commands['temperature'], $t->commands['temperature']->value, $notifyThresholds);
}

$tests = array();
$tests['low threshold, context tags and unchanged saved actions'] = function () {
	$t = fixture(16);
	$configuration = $t->configuration;
	thermostat::cron();
	same(1, count(scenarioExpression::$calls));
	$options = scenarioExpression::$calls[0];
	same('temperature_low', $options['title']);
	contains('16 °C', $options['message']);
	contains('18 °C', $options['message']);
	contains('[Maison][Salon]', $options['message']);
	same('16', $options['temperature']);
	same('21', $options['slider']);
	same($t->commands['temperature']->collectDate, $options['date']);
	same($options['message'], $options['nested']['message']);
	same(true, $options['nested']['enabled']);
	same('Défaillance sonde', $t->commands['status']->value);
	same($configuration, $t->configuration);
};
$tests['high threshold includes measured value and configured limit'] = function () {
	$t = fixture(32);
	check($t);
	same('temperature_high', $t->commands['failure_reason']->value);
	contains('32 °C', $t->commands['failure_message']->value);
	contains('28 °C', $t->commands['failure_message']->value);
};
$tests['timeout includes elapsed time, allowed time and last collection'] = function () {
	$t = fixture(21, 47);
	check($t);
	same('timeout', $t->commands['failure_reason']->value);
	contains('47 minutes', scenarioExpression::$calls[0]['message']);
	contains('30 minutes', scenarioExpression::$calls[0]['message']);
	contains($t->commands['temperature']->collectDate, scenarioExpression::$calls[0]['message']);
};
$tests['simultaneous faults generate one action and one error log across all callers'] = function () {
	$t = fixture(16, 47);
	thermostat::cron();
	thermostat::hysteresis(array('thermostat_id' => 1));
	thermostat::temporal(array('thermostat_id' => 1));
	thermostat::cron();
	same('timeout,temperature_low', $t->commands['failure_reason']->value);
	same(1, count(scenarioExpression::$calls));
	same(1, count(log::$errors));
	same(0, $t->regulationActions);
	contains('minimum autorisé', scenarioExpression::$calls[0]['message']);
	contains('dernière réception', scenarioExpression::$calls[0]['message']);
};
$tests['new cause is reported without an intermediate healthy state'] = function () {
	$t = fixture(16);
	check($t);
	$t->commands['temperature']->value = 21;
	$t->commands['temperature']->collectDate = date('Y-m-d H:i:s', time() - 3600);
	check($t);
	same(2, count(scenarioExpression::$calls));
	same('timeout', scenarioExpression::$calls[1]['title']);
};
$tests['new threshold during timeout is reported by cron only once'] = function () {
	$t = fixture(21, 47);
	check($t, false);
	$t->commands['temperature']->value = 32;
	check($t, false);
	same(1, count(scenarioExpression::$calls));
	thermostat::cron();
	check($t, false);
	thermostat::cron();
	same(2, count(scenarioExpression::$calls));
	same('timeout,temperature_high', scenarioExpression::$calls[1]['title']);
};
$tests['partial recovery rearms only the recovered cause'] = function () {
	$t = fixture(16, 47);
	check($t);
	$t->commands['temperature']->collectDate = date('Y-m-d H:i:s');
	check($t, false);
	check($t);
	same('temperature_low', $t->commands['failure_reason']->value);
	same(1, count(scenarioExpression::$calls));
	$t->commands['temperature']->collectDate = date('Y-m-d H:i:s', time() - 3600);
	check($t);
	same(2, count(scenarioExpression::$calls));
};
$tests['full recovery clears details and rearms the same fault'] = function () {
	$t = fixture(32);
	check($t);
	$t->commands['temperature']->value = 21;
	check($t);
	same('', $t->commands['failure_reason']->value);
	same('', $t->commands['failure_message']->value);
	same(array(), $t->getCache('sensor_failure_causes'));
	$t->commands['temperature']->value = 33;
	check($t);
	same(2, count(scenarioExpression::$calls));
};
$tests['a persistent threshold survives regulation and old cache flags'] = function () {
	$t = fixture(16);
	$t->cache['temp_threshold'] = 1;
	$t->cache['temporal_threshold'] = 1;
	thermostat::hysteresis(array('thermostat_id' => 1));
	same(0, count(scenarioExpression::$calls));
	thermostat::cron();
	thermostat::hysteresis(array('thermostat_id' => 1));
	thermostat::cron();
	same(1, count(scenarioExpression::$calls));
	same('temperature_low', $t->commands['failure_reason']->value);
};
$tests['temporal calculation cannot mask sensor faults'] = function () {
	$t = fixture(24);
	$data = $t->calculTemporalData(21);
	same(0, $data['power']);
	same(1, $t->getCache('temporal_threshold'));
	$t->commands['temperature']->value = 32;
	check($t);
	same(1, count(scenarioExpression::$calls));
};
$tests['fault status and details exist without configured actions'] = function () {
	$t = fixture(32);
	$t->configuration['failure'] = array();
	check($t);
	same('Défaillance sonde', $t->commands['status']->value);
	same('temperature_high', $t->commands['failure_reason']->value);
	same(0, count(scenarioExpression::$calls));
};
$tests['empty, zero and invalid timeout settings do not raise false timeouts'] = function () {
	foreach (array('', 0, '0', -1, 'invalid') as $timeout) {
		$t = fixture(21, 120);
		$t->configuration['maxTimeUpdateTemp'] = $timeout;
		thermostat::hysteresis(array('thermostat_id' => 1));
		thermostat::cron();
		same(0, count(scenarioExpression::$calls));
	}
};
$tests['zero-degree thresholds work and exact limits are allowed'] = function () {
	$t = fixture(-1);
	$t->configuration['temperature_indoor_min'] = 0;
	check($t);
	same('temperature_low', $t->commands['failure_reason']->value);
	$t = fixture(1);
	$t->configuration['temperature_indoor_min'] = '';
	$t->configuration['temperature_indoor_max'] = 0;
	check($t);
	same('temperature_high', $t->commands['failure_reason']->value);
	foreach (array(18, 28) as $temperature) {
		$t = fixture($temperature);
		check($t);
		same(0, count(scenarioExpression::$calls));
	}
};
$tests['invalid values are distinct and block both engines'] = function () {
	foreach (array('hysteresis', 'temporal') as $engine) {
		$t = fixture('unavailable');
		thermostat::$engine(array('thermostat_id' => 1));
		thermostat::cron();
		same('invalid_temperature', $t->commands['failure_reason']->value);
		same(1, count(scenarioExpression::$calls));
		same(0, $t->regulationActions);
	}
};
$tests['off and suspended thermostats do not notify'] = function () {
	foreach (array('mode' => 'Off', 'status' => 'Suspendu') as $command => $state) {
		$t = fixture(32, 47);
		$t->commands[$command]->value = $state;
		thermostat::cron();
		thermostat::hysteresis(array('thermostat_id' => 1));
		same(0, count(scenarioExpression::$calls));
	}
};
$tests['events and actions can reenter detection without duplicate notifications'] = function () {
	$t = fixture(32);
	$t->commands['failure_reason']->onEvent = function () use ($t) { check($t); };
	scenarioExpression::$onAction = function () use ($t) { check($t); };
	check($t);
	same(1, count(scenarioExpression::$calls));
	same(1, count(log::$errors));
};
$tests['failure actions can stop the thermostat without their status being overwritten'] = function () {
	$t = fixture(21, 47);
	scenarioExpression::$onAction = function () use ($t) {
		$t->commands['mode']->event('Off');
		$t->commands['status']->event('Arrêté');
	};
	thermostat::hysteresis(array('thermostat_id' => 1));
	same('Arrêté', $t->commands['status']->value);
	same('Off', $t->commands['mode']->value);
};
$tests['legacy failure calls still substitute the setpoint'] = function () {
	$t = fixture();
	$t->failure(123);
	same('21', scenarioExpression::$calls[0]['slider']);
};

foreach ($tests as $name => $test) {
	$test();
	echo 'OK: ' . $name . PHP_EOL;
}
echo count($tests) . ' sensor failure tests passed.' . PHP_EOL;
