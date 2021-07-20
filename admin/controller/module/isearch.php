<?php
class ControllerModuleIsearch extends Controller {
	private $error = array(); 
	
	public function index() {   
		$this->load->language('module/isearch');

		$this->document->setTitle($this->language->get('heading_title'));
		$this->document->addStyle('view/stylesheet/isearch.css');
		
		$this->load->model('setting/setting');
				
		$this->data['error_warning'] = '';
		if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
			
			if (!$this->user->hasPermission('modify', 'module/isearch')) {
				$this->redirect($this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'));
			}
			
			//{HOOK_CHECK_IF_CACHE_DISABLED}
			
			$this->model_setting_setting->editSetting('isearch', $this->request->post);		
			
			$this->cache->delete('product');
			$this->cache->delete('productstandard');
			
			$this->session->data['success'] = $this->language->get('text_success');
			
			//{HOOK_REFRESH_CACHE_ON_ENABLE}
			
			if (!empty($_GET['activate'])) {
				$this->session->data['success'] = $this->language->get('text_success_activation');
			}
			
			$selectedTab = (empty($this->request->post['selectedTab'])) ? 0 : $this->request->post['selectedTab'];
			$this->redirect($this->url->link('module/isearch', 'token=' . $this->session->data['token'] . '&tab='.$selectedTab, 'SSL'));
		}

				
		$this->data['heading_title'] = $this->language->get('heading_title');

		$this->data['text_enabled'] = $this->language->get('text_enabled');
		$this->data['text_disabled'] = $this->language->get('text_disabled');
		$this->data['text_content_top'] = $this->language->get('text_content_top');
		$this->data['text_content_bottom'] = $this->language->get('text_content_bottom');		
		$this->data['text_column_left'] = $this->language->get('text_column_left');
		$this->data['text_column_right'] = $this->language->get('text_column_right');
		
		$this->data['entry_code'] = $this->language->get('entry_code');
	
		
		$this->data['button_save'] = $this->language->get('button_save');
		$this->data['button_cancel'] = $this->language->get('button_cancel');
		$this->data['button_add_module'] = $this->language->get('button_add_module');
		$this->data['button_remove'] = $this->language->get('button_remove');
		$this->data['entry_layouts_active'] = $this->language->get('entry_layouts_active');
		$this->data['entry_highlightcolor'] = $this->language->get('entry_highlightcolor');
			
		
 		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}
		
		
  		$this->data['breadcrumbs'] = array();

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => false
   		);

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_module'),
			'href'      => $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);
		
   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('module/isearch', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);
		
		$this->data['action'] = $this->url->link('module/isearch', 'token=' . $this->session->data['token'], 'SSL');
		
		$this->data['cancel'] = $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL');

		if (isset($this->request->post['iSearch'])) {
			foreach ($this->request->post['iSearch'] as $key => $value) {
				$this->data['data']['iSearch'][$key] = $this->request->post['iSearch'][$key];
			}
		} else {
			$configValue = $this->config->get('iSearch');
			$this->data['data']['iSearch'] = $configValue;
		}
			
		$this->data['currenttemplate'] =  $this->config->get('config_template');
		
		$this->data['modules'] = array();
		
		if (isset($this->request->post['isearch_module'])) {
			$this->data['modules'] = $this->request->post['isearch_module'];
		} elseif ($this->config->get('isearch_module')) { 
			$this->data['modules'] = $this->config->get('isearch_module');
		}		
			
		$this->load->model('localisation/language');
		$this->data['languages'] = $this->model_localisation_language->getLanguages();
			
		$this->load->model('design/layout');
		
		$this->data['layouts'] = $this->model_design_layout->getLayouts();

		$this->template = 'module/isearch.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}
	
	//{HOOK_CACHE_BUILDING_FUNCTIONS}
	
	private function validate() {
		if (!$this->user->hasPermission('modify', 'module/isearch')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		
		if (!$this->error) {
			return true;
		} else {
			return false;
		}	
	}
}
?>