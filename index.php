<?php

require_once('utils/utils.php');

if (1) {
    $bi = kwcli::battInfo();
    if (PHP_SAPI === 'cli') var_dump($bi);
    else {
	header('Content-Type: application/json');
	echo json_encode($bi);
    }

    kwcli::scrcpy();

    unset($bi);
}
