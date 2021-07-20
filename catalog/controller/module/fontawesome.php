<?php  
class ControllerModuleFontawesome extends Controller {
	protected function index($setting) {

		$this->document->addStyle("catalog/view/fontawesome/css/font-awesome.min.css");	
		
		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/module/fontawesome.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/module/fontawesome.tpl';
		} else {
			$this->template = 'default/template/module/fontawesome.tpl';
		}
		
		$this->render();
  	}
}
?>