<?php

require_once('utils/dao.php');

doit();
function doit() {

    if (PHP_SAPI !== 'cli') die('cli only');

    $dao = new cli_dao();
    $status = $dao->getStatus();

    if ($status !== 'listening') die('unk status');

    $r   = fopen('/var/kwynn/fifo', 'r');
    $dat = trim(fgets($r));
    if ($dat === 'kill') $dao->exit();

    $dao->newStatus('spawning');

    exec('php ' . __FILE__ . ' > /dev/null &');
    $cmd = 'scrcpy 2>&1';
    $res = shell_exec($cmd);
    $dao->putRes($res);

    $dao->exit();
}