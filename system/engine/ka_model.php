<?php
/*
	Project: Ka-Extensions
	Author : karapuz <support@ka-station.com>

	Version: 2 ($Revision: 35 $)
*/

abstract class KaModel extends Model {

	protected $params;
	protected $lastError;

	function __construct(&$registry) {
		parent::__construct($registry);

		$class = get_class($this);
		if (!isset($this->session->data["ka_params_$class"])) {
			$this->session->data["ka_params_$class"] = array();
		}
		$this->params = &$this->session->data["ka_params_$class"];
		
		$this->onLoad();
	}

	
	protected function loadLanguage($language) {
		if (version_compare(VERSION, '1.5.5', '>=')) {
			$ret = $this->language->load($language);
		} else {
			$ret = $this->load->language($language);
		}
		
		return $ret;
	}
	
	
	public function getLastError() {
		return $this->lastError;
	}
	
	protected function onLoad() {
		return true;
	}
	
}
?>