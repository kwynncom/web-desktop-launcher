<?php

require_once('kwutils.php');
require_once('daoCreds.php');

class kwcli {
    
    public static function scrcpy() {
	kwas(self::isScrcpyRun() === 0 , 'scrcpy already running / in process list');
	file_put_contents('/var/kwynn/fifo', 'blah');
    }
    
    public static function isScrcpyRun() {
	$gc = 'grep scrcpy';
	$r = shell_exec("ps -Af | $gc");
	$pre = '/\b\w+\s+\d+ \d+.+';
	$ck = $gc;
	kwas(preg_match("$pre$ck\n/", $r), 'is ScrcpyRun first grep fail');
	$isrun = preg_match("$pre\/usr\/local\/bin\/scrcpy\n/", $r);
	return $isrun;
    }
    
    public static function battInfo($att = 1) {
	
	if ($att > 3) return false;
	
	try {
	    $l = self::battlev();
	    $c = self::battttch();
	    return array_merge(['msg' => '', 'lev' => $l], $c);
	} catch (Exception $ex) { 
	    
	    $msg = $ex->getMessage();
	    if (strpos($msg, 'error: no devices/emulators found') !== false) {
		self::adbTCP();
		$r2 = self::battInfo($att + 1);
		if ($r2) return $r2;
	    } else if ($msg === 'error: device offline') {
		sleep(2);
		$r3 = self::battInfo($att + 1);
		if ($r3) return $r3;	
	    }
	    
	    return ['msg' => $msg];	
	    
	}
    }
    
    public static function battlev() { return self::intRes('adb shell cat /sys/class/power_supply/battery/capacity'); }
    
    public static function battttch() {  
	$s = self::intRes('adb shell cat /sys/class/power_supply/battery/time_to_full_now');	 
	if ($s < 0) return ['charging' => 0];
	$now = time();
	$at = date('g:i:s' , $now + $s);
	
	$huf = round($s / 3600, 1);
	
	return ['full_at' => $at, 'hrs_until_full' => $huf, 'charging' => 1];
	
    }
    
    private static function intRes($cmd) {
	$o = trim(shell_exec($cmd . ' 2>&1 '));
	kwas(is_numeric($o), $o);
	return intval($o);
    }
    
    public static function adbTCP($ipin = false) {
	if (!$ipin) {
	    $dao = new dao_creds();
	    $ipa = $dao->get('cell_phone_internal_ip_addr');
	    kwas(isset($ipa['ip_addr']), 'no ip address to find phone');
	    $ip =      $ipa['ip_addr'];
	} else $ip = $ipin;
	shell_exec("adb connect $ip");
    }
}

