<?php

require_once __DIR__ . '/AutoLoader.php';

$autoLoader = new Bootstrap\AutoLoader();
$autoLoader->addPsr0Directory(__DIR__. '/../../src/');
$autoLoader->addPsr0Directory(__DIR__. '/../');

spl_autoload_register(array($autoLoader, 'loadClass'));
