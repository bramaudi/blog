<?php

require_once 'config.php';

foreach (glob('app/core/*.php') as $file) {
	require_once 'core/' .basename($file);
}

foreach (glob('app/libs/*.php') as $file) {
	require_once 'libs/' .basename($file);
}

$app = new Core(DEBUG_MODE);
// $app->debug();