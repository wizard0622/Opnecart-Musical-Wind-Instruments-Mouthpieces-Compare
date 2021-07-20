<?php   
class ControllerModuleManufacturersdropdown extends Controller {
	protected function index() {
		$status = true;
		
		if ($this->config->get('store_admin')) {
			$this->load->library('user');
		
			$this->user = new User($this->registry);
			
			$status = $this->user->isLogged();
		}
		
		if ($status) {
			$this->language->load('module/manufacturersdropdown');
			
			$this->data['heading_title'] = $this->language->get('heading_title');			
			
			$this->load->model('catalog/manufacturer');
			
			$results = $this->model_catalog_manufacturer->getManufacturers();
			
			foreach ($results as $result) {
				$this->data['manufacturers'][] = array(
					'id' => $result['manufacturer_id'],
					'name'     => $result['name']					
				);
			}
			$this->data['href'] = HTTP_SERVER . 'index.php?route=product/manufacturer/product&manufacturer_id=';
			if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/module/manufacturersdropdown.tpl')) {
				$this->template = $this->config->get('config_template') . '/template/module/manufacturersdropdown.tpl';
			} else {
				$this->template = 'default/template/module/manufacturersdropdown.tpl';
			}
			
			$this->render();
		}
	}
}
?>