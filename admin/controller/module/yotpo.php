<?php
class ControllerModuleYotpo extends Controller {
	private $error = array();
	private static $language_assigns = array('heading_title','heading_settings_title','heading_signup_title','button_save','button_cancel',
										 'entry_appkey','entry_secret','entry_language','entry_review_tab_name','entry_widget_location',
										 'entry_user_name','entry_password','entry_confirm_password','entry_email','entry_bottom_line','entry_past_orders','entry_sign_up_button',
										 'entry_widget_location_other','entry_widget_location_tab','entry_widget_location_footer','entry_yotpo_version','entry_completed_status','entry_language_info','entry_here');
	private static $error_assigns = array('error_appkey','error_secret','error_user_name','error_email','error_password',
									 	  'error_confirm_password','error_warning','error_language');
	private static $config_assigns = array('yotpo_appkey','yotpo_secret','yotpo_language','yotpo_user_name','yotpo_email',
									 	   'yotpo_password','yotpo_confirm_password','yotpo_review_tab_name','yotpo_widget_location','yotpo_bottom_line_enabled','yotpo_map_status');

	public function index() {
		//Load the language file for this module
		$this->language->load('module/yotpo');

		//Set the title from the language file $_['heading_title'] string
		$this->document->setTitle($this->language->get('heading_title'));

		//Load the settings model.
		$this->load->model('setting/setting');
		
		$this->load->model('localisation/order_status');
		$order_statuses = $this->model_localisation_order_status->getOrderStatuses();
		$this->data['order_statuses'] = $order_statuses;
		
		//Save the settings if the user has submitted the admin form (ie if someone has pressed save).
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$result = null;
			$success_text = $this->language->get('text_success');
			$is_custome_action = array_key_exists('action',$this->request->request);
			if($is_custome_action && $this->request->request['action'] == 'signup') {
				$this->load->model('tool/yotpo');
				$result = $this->model_tool_yotpo->signUp($this->request->post);
				if(isset($result['appkey']) && isset($result['secret'])) {
					$this->request->post['yotpo_appkey'] = $result['appkey'];
					$this->request->post['yotpo_secret'] = $result['secret'];
					$success_text = $this->language->get('text_signup_success');
				}
			}
				
			elseif ($is_custome_action && $this->request->request['action'] == 'past_orders') {
				$past_orders_sent = $this->config->get('yotpo_past_order_sent');
				if(empty($past_orders_sent)) {
					$this->request->post['yotpo_past_order_sent'] = 'true';
					$this->load->model('tool/yotpo');
					$result = $this->model_tool_yotpo->past_orders();
					if(is_null($result)) {
						$success_text = $this->language->get('text_past_orders_success');
					}
				}
			}
				
			if(is_null($result) || !isset($result['message'])) {
				$this->session->data['success'] = $success_text;
				$this->model_setting_setting->editSetting('yotpo', $this->request->post);
				$this->redirect($this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'));
			}
			else {
				$this->data['yotpo_error_warning'] = $result['message'];
			}
		}

		//Pull languages
		foreach (self::$language_assigns as $data_name) {
			$this->data[$data_name] = $this->language->get($data_name);
		}

		foreach (self::$error_assigns as $error_assign) {
			if (isset($this->error[$error_assign])) {
				$this->data[$error_assign] = $this->error[$error_assign];
			} else {
				$this->data[$error_assign] = '';
			}
		}

		//User entries
		foreach (self::$config_assigns as $config_assign) {
			if (isset($this->request->post[$config_assign])) {
				$this->data[$config_assign] = $this->request->post[$config_assign];
			} else {
				$this->data[$config_assign] = $this->config->get($config_assign);
			}
		}

		/* Default widget tab name: 'Reviews', Default widget tab location: footer Default widget language: english*/
		if(is_null($this->data['yotpo_review_tab_name']) || $this->data['yotpo_review_tab_name'] == '') {
			$this->data['yotpo_review_tab_name'] = $this->language->get('yotpo_default_reviews_tab_name');
		}

		if(is_null($this->data['yotpo_widget_location']) || $this->data['yotpo_widget_location'] == '') {
			$this->data['yotpo_widget_location'] = $this->language->get('yotpo_default_widget_location');
		}

		if(is_null($this->data['yotpo_language']) || $this->data['yotpo_language'] == '') {
			$this->data['yotpo_language'] = $this->language->get('yotpo_default_widget_language');
		}

		if(is_null($this->data['yotpo_map_status']) || $this->data['yotpo_map_status'] == '') {
			$this->data['yotpo_map_status'] = array();
			array_push($this->data['yotpo_map_status'], $this->config->get('config_complete_status_id'));
		}
		
		//SET UP BREADCRUMB TRAIL. YOU WILL NOT NEED TO MODIFY THIS.
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
			'href'      => $this->url->link('module/yotpo', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
      		);

      		$this->data['action'] = $this->url->link('module/yotpo', 'token=' . $this->session->data['token'], 'SSL');

      		$this->data['cancel'] = $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL');

      		$this->data['sign_up'] = $this->url->link('module/yotpo', 'token=' . $this->session->data['token'] . '&action=signup', 'SSL');

      		$this->data['past_orders'] = $this->url->link('module/yotpo', 'token=' . $this->session->data['token'] . '&action=past_orders', 'SSL');
      		$past_orders_enable = $this->config->get('yotpo_past_order_sent');

      		$this->data['yotpo_show_past_orders_button'] = empty($past_orders_enable) ? true : false;

      		$this->data['yotpo_show_dashborad_link'] = empty($this->data['yotpo_appkey']) || empty($this->data['yotpo_secret']) ? false : true;
      		if($this->data['yotpo_show_dashborad_link']) {
      			$this->data['yotpo_dashborad_link'] = 'https://api.yotpo.com/users/b2blogin?app_key='.$this->data['yotpo_appkey'].'&secret='.$this->data['yotpo_secret'];
      			$this->data['yotpo_dashborad_link_text'] = $this->language->get('text_customize_widget');
      			$this->data['yotpo_dashborad_text'] = $this->language->get('text_yotpo_dashboard');
      		}
      		else {
      			$this->data['text_yotpo_missing_app_key'] = $this->language->get('text_yotpo_missing_app_key');
      			$this->data['text_yotpo_log_in'] = $this->language->get('text_yotpo_log_in');
      			$this->data['yotpo_login_link'] = 'https://www.yotpo.com/?login=true';
      		}
      		if($this->data['yotpo_widget_location'] == 'other') {
      			$this->data['text_yotpo_widget_location_other'] = $this->language->get('text_yotpo_widget_location_other');
      		}
      		//Choose which template file will be used to display this request.
      		$this->template = 'module/yotpo.tpl';
      		$this->children = array(
			'common/header',
			'common/footer',
      		);

      		//Send the output.
      		$this->response->setOutput($this->render());
	}

	/*
	 *
	 * This function is called to ensure that the settings chosen by the admin user are allowed/valid.
	 * You can add checks in here of your own.
	 *
	 */
	private function validate() {
		if (!$this->user->hasPermission('modify', 'module/yotpo')) {
			$this->error['error_permission'] = $this->language->get('error_permission');
		}

		if(isset($this->request->request['action']) && $this->request->request['action'] == 'signup') {
			if (!$this->request->post['yotpo_user_name']) {
				$this->error['error_user_name'] = $this->language->get('error_user_name');
			}

			if (!$this->request->post['yotpo_email']) {
				$this->error['error_email'] = $this->language->get('error_email');
			}

			if (!$this->request->post['yotpo_password'] || strlen($this->request->post['yotpo_password']) < 6 || strlen($this->request->post['yotpo_password']) > 128) {
				$this->error['error_password'] = $this->language->get('error_password');
			}

			if ($this->request->post['yotpo_confirm_password'] != $this->request->post['yotpo_password']) {
				$this->error['error_confirm_password'] = $this->language->get('error_confirm_password');
			}
		}
		else {
			if (!$this->request->post['yotpo_appkey']) {
				$this->error['error_appkey'] = $this->language->get('error_appkey');
			}

			if (!$this->request->post['yotpo_secret']) {
				$this->error['error_secret'] = $this->language->get('error_secret');
			}
					
			if (!$this->request->post['yotpo_language']  || strlen($this->request->post['yotpo_language']) > 6 || strlen($this->request->post['yotpo_language']) < 2) {
				$this->error['error_language'] = $this->language->get('error_language');
			}
		}
		if (!$this->error) {
			return TRUE;
		} else {
			return FALSE;
		}
	}
}
?>