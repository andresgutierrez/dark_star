<?php

error_reporting(E_ALL);

use Phalcon\Mvc\Micro as App;

define ('APP_PATH', realpath('../app'));

require APP_PATH . '/config/config.php';
require APP_PATH . '/config/loader.php';
require APP_PATH . '/config/services.php';

try {

	$app = new App();

	/**
	 * Return a coordinates by a file initial
	 * $param string $type Example: file, rest
	 */
	$app->get('/coordinates', function() use ($url) {

		$content = file_get_contents($url);

		$client = new Predis\Client();

		$client->lpush('last', $content);

		$val = $client->lrange('lastFront', 0, -1);
		$val = $client->lrange('lastDB', 0, -1);
		print_r($val);
	});

	/**
	 * Return a coordinates by a file initial
	 * $param string $type Example: file, rest
	 */
	$app->get('/lastCoordinateF', function() use ($url) {

		$client = new Predis\Client();

		$val = $client->lrange('lastFront', -1, -1);
		echo $val[0];
	});

	/**
	 * Return a coordinates by a file initial
	 * $param string $type Example: file, rest
	 */
	$app->get('/lastCoordinateD', function() use ($url) {

		$client = new Predis\Client();

		$val = $client->lrange('lastDB', -1, -1);
		echo $val[0];
	});

	/**
	 * Return a coordinates by a file initial
	 * $param string $type Example: file, rest
	 */
	$app->get('/randCoordinate', function() use ($url) {

		$client = new Predis\Client();

		$val = $client->lrange('lastFront', rand(1,1000)*-1, -1);
		echo $val[0];
	});

	$app->handle();

} catch (Exception $e) {
	echo $e->getMessage();
}