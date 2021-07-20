<?php
/*
	Project: Ka-Extensions
	Author : karapuz <support@ka-station.com>

	Version: 2 ($Revision: 35 $)
*/

abstract class KaController extends Controller {

	protected $params;

	function __construct(&$registry) {
		parent::__construct($registry);

		$class = get_class($this);
		if (!isset($this->session->data["ka_params_$class"])) {
			$this->session->data["ka_params_$class"] = array();
		}
		$this->params = &$this->session->data["ka_params_$class"];
		
		$this->onLoad();
	}

	
	protected function addTopMessage($msg, $type = 'I') {
		$this->session->data['ka_top_messages'][] = array(
			'type'    => $type,
			'content' => $msg
		);
	}

	
	protected function getTopMessages($clear = true) {
		
		if (isset($this->session->data['ka_top_messages'])) {
			$top = $this->session->data['ka_top_messages'];
		} else {
			$top = null;
		}

		if ($clear) {
			$this->session->data['ka_top_messages'] = null;
		}
		return $top;
	}

	
	protected function findTemplate($template) {
		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . $template)) {
			$template = $this->config->get('config_template') . $template;
		} else {
			$template = 'default' . $template;
		}
		
		return $template;
	}

			
	protected function render() {

		$this->data['top_messages'] = $this->getTopMessages();
		$this->data['ka_top'] = $this->getChild('common/ka_top', $this->data);

		return parent::render();
	}
	
	
	protected function loadLanguage($language) {
		if (version_compare(VERSION, '1.5.5', '>=')) {
			$ret = $this->language->load($language);
		} else {
			$ret = $this->load->language($language);
		}
		
		return $ret;
	}
	
	
	protected function onLoad() {
		return true;
	}
		
}
?>