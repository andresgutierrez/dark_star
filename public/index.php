<?php

error_reporting(E_ALL);

use Phalcon\Mvc\Micro as App;

define ('APP_PATH', realpath('../app'));

require APP_PATH . '/config/config.php';
require APP_PATH . '/config/loader.php';
require APP_PATH . '/config/services.php';

// Prepend a base path if Predis is not available in your "include_path".
require_once APP_PATH . '/library/Predis/autoload.php';
Predis\Autoloader::register();

try {

	$app = new App();

	/**
	 * Return a coordinates by a file initial
	 * $param string $type Example: file, rest
	 */
	$app->get('/coordinates', function() use ($url) {

		$content = file_get_contents($url);
		$client = new Predis\Client();
		$arrayTemp = array();
		foreach (explode(";", $content) as $n => $coords) {
			if (!$n) {
				continue;				
			}

			$c = explode(",", $coords);
			var_dump($c);
			$index = $c[0];
			if ($index=="P" || $index=="S" || $index=="C") {
				echo $index;
				unset($c[0]);		
				$arrayTemp[$index][] = array_values($c);
			}
			unset($index, $c, $n, $coords);
		}

		foreach ($arrayTemp as $index => $a) {
			$client->lpush($index, json_encode($a));
		}
		$client->lpush('db', $content);
	});

	/**
	 * Return a coordinates by a file initial
	 * $param string $type Example: file, rest
	 */
	$app->get('/lastCoordinate/{tipo}', function($tipo) use ($url) {
		$client = new Predis\Client();
		$val = $client->lrange($tipo, -1, -1);
		echo $val[0];
	});

	/**
	 * Return a coordinates by a file initial
	 * $param string $type Example: file, rest
	 */
	$app->get('/randCoordinate/{tipo}', function($tipo) use ($url) {

		$client = new Predis\Client();

		$val = $client->lrange($tipo, rand(1,1000)*-1, -1);
		echo $val[0];
	});

	$app->handle();

} catch (Exception $e) {
	echo $e->getMessage();
}