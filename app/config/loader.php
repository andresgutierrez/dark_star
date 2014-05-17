<?php

// Prepend a base path if Predis is not available in your "include_path".
require '../library/Predis/autoload.php';

Predis\Autoloader::register();