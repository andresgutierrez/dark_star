<?php

// Prepend a base path if Predis is not available in your "include_path".
require '../app/library/Predis/autoload.php';
Predis\Autoloader::register();