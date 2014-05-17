<?php

error_reporting(E_ALL);

use Phalcon\Mvc\Micro as App;

define ('APP_PATH', realpath('../app'));

require APP_PATH . '/config/config.php';
require APP_PATH . '/config/loader.php';
require APP_PATH . '/config/services.php';

try {

	$app = new App();

	//echo Phalcon\Version::get();

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
			$index = $c[0];
			if ($index=="P" || $index=="M" || $index=="O") {
				unset($c[0]);
				if (($c[1]>-1000 && $c[1]<=1000) || ($c[2]>-1000 && $c[2]<=1000) || ($c[3]>-1000 && $c[3]<=1000)) {
					$c[4] = 1;
				}
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
	 * Return a coordinates in redis
	 * $param string $type Example: file, rest
	 */
	$app->get('/lastCoordinate/{tipo}', function($tipo) {
		$client = new Predis\Client();
		echo $val = $client->lpop($tipo);
	});

	/**
	 * Return a coordinates by a file initial
	 * $param string $type Example: P, M, O
	 */
	$app->get('/randCoordinate/{tipo}', function($tipo) {

		$client = new Predis\Client();
		$val = $client->lrange($tipo, rand(1,1000)*-1, -1);
		echo $val[0];
	});

	$app->handle();

} catch (Exception $e) {
	echo $e->getMessage();
}