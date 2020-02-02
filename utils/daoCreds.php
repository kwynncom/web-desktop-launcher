<?php

require_once('kwutils.php');

class dao_creds extends dao_generic {
    const dbName = 'creds';
    
    public function __construct() {
	parent::__construct(self::dbName);
	$this->ccoll = $this->client->selectCollection(self::dbName, 'creds');
    }
    
    public function get($type) { return $this->ccoll->findOne(['type' => $type]); }
}
