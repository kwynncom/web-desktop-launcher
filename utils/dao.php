<?php

require_once('kwutils.php');

class cli_dao extends dao_generic {
    
    const dbName = 'web_launcher';
    
    public function __construct() {
	parent::__construct(self::dbName);
	$this->pcoll = $this->client->selectCollection(self::dbName, 'processes');
	
	kwas(pcntl_signal(SIGTERM, [$this, 'exit']), 'signal handler set failed');
	
	$this->rcoll = $this->client->selectCollection(self::dbName, 'result');
	$this->setIndexes();
	$this->limOrDie();
	$this->regme();
    }
    
    public function putRes($rin) { $this->rcoll->insertOne(['res' => $rin]); }
    
    private function setIndexes() {
	$res = $this->pcoll->findOne();
	if ($res) return;
	$this->pcoll->createIndex(['status' => 1], ['unique' => true]);	
    }
    
    private function limOrDie() {
	
	// not running yet because...
	return; // 2 processes of different types will run within microseconds
	
	$nowu = microtime(1);
	$lim  = 2;
	$since  = $nowu - $lim;
	
	$cnt = $this->pcoll->count(['tsu' => ['$gt' => $since]]);	
	kwas($cnt === 0, 'too many processes');
    }
    
    public function newStatus($sin) { 	$this->pcoll->upsert(['seq' => $this->seq], ['status' => $sin]);    }
    
    public function regme() {
	
	$this->limOrDie();
	
	$cnt = $this->pcoll->count(['status'  => 'listening']);
	if ($cnt === 0) $status = 'listening';
	else	        kwas(0, 'unknown status');

	$seq = $this->getSeq('processes');

	$dat = [
	    'pid'  => getmypid(),
	    'r'    => date('r'),
	    'tsu'  =>  microtime(1),
	    'seq'  => $seq,
	    'status' => $status
	];
	
	$this->pcoll->insertOne($dat);
	
	$this->seq = $seq;
	$this->pstatus = $status;
    }
    
    public  function exit()       { $this->pcoll->deleteOne(['seq' => $this->seq]); exit(0); }
    public function getStatus() { return $this->pstatus; }
}
