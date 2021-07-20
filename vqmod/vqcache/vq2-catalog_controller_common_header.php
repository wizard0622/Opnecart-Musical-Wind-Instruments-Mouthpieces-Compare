<?php   
class ControllerCommonHeader extends Controller {
	protected function index() {

        	 $this->data['yotpo_app_key'] = $this->config->get('yotpo_appkey');	
        
		$this->data['title'] = $this->document->getTitle();

		if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
			$server = $this->config->get('config_ssl');
		} else {
			$server = $this->config->get('config_url');
		}

		if (isset($this->session->data['error']) && !empty($this->session->data['error'])) {
			$this->data['error'] = $this->session->data['error'];

			unset($this->session->data['error']);
		} else {
			$this->data['error'] = '';
		}

		if(isset($_COOKIE['category']))
		{
			$this->data['cookiecat']=$_COOKIE['category'];
			setcookie('category', $_COOKIE['category'], time() + 60 * 60 * 24 * 30, '/', $this->request->server['HTTP_HOST']);
		}
		else $this->data['cookiecat']=60;
		
		$this->data['base'] = $server;
		$this->data['description'] = $this->document->getDescription();
		$this->data['keywords'] = $this->document->getKeywords();
		$this->data['links'] = $this->document->getLinks();	 
		$this->data['styles'] = $this->document->getStyles();
		$this->data['scripts'] = $this->document->getScripts();

		// code according to https://developers.google.com/webmasters/richsnippets/sitelinkssearch
		if(count($this->request->get) == 0 || (isset($this->request->get['route']) && $this->request->get['route'] == 'common/home'))
		   $this->data['sitelinks_search_box'] = '
<script type="application/ld+json">
{
   "@context": "http://schema.org",
   "@type": "WebSite",
   "url": "'.$server.'",
   "potentialAction": {
     "@type": "SearchAction",
     "target": "' . $this->url->link('product/search?q={search_term_string}') . '",
     "query-input": "required name=search_term_string"
   }
}
</script>';
		else $this->data['sitelinks_search_box'] = false;


		$this->data['lang'] = $this->language->get('code');
		$this->data['direction'] = $this->language->get('direction');
		$this->data['google_analytics'] = html_entity_decode($this->config->get('config_google_analytics'), ENT_QUOTES, 'UTF-8');
		$this->data['name'] = $this->config->get('config_name');

		if ($this->config->get('config_icon') && file_exists(DIR_IMAGE . $this->config->get('config_icon'))) {
			$this->data['icon'] = $server . 'image/' . $this->config->get('config_icon');
		} else {
			$this->data['icon'] = '';
		}

		if ($this->config->get('config_logo') && file_exists(DIR_IMAGE . $this->config->get('config_logo'))) {
			$this->data['logo'] = $server . 'image/' . $this->config->get('config_logo');
		} else {
			$this->data['logo'] = '';
		}		

		$this->language->load('common/header');

		$this->data['text_home'] = $this->language->get('text_home');
		$this->data['text_wishlist'] = sprintf($this->language->get('text_wishlist'), (isset($this->session->data['wishlist']) ? count($this->session->data['wishlist']) : 0));
		$this->data['text_shopping_cart'] = $this->language->get('text_shopping_cart');
		$this->data['text_search'] = $this->language->get('text_search');
		$this->data['text_welcome'] = sprintf($this->language->get('text_welcome'), $this->url->link('account/login', '', 'SSL'), $this->url->link('account/register', '', 'SSL'));
		$this->data['text_logged'] = sprintf($this->language->get('text_logged'), $this->url->link('account/account', '', 'SSL'), $this->customer->getFirstName(), $this->url->link('account/logout', '', 'SSL'));
		$this->data['text_account'] = $this->language->get('text_account');
		$this->data['text_checkout'] = $this->language->get('text_checkout');
    $this->data['text_compare'] = sprintf($this->language->get('text_compare'), (isset($this->session->data['compare']) ? count($this->session->data['compare']) : 0));
    $this->data['text_comparet'] = $this->language->get('text_comparet');
    $this->data['compare'] = $this->url->link('product/compare');
    
		$this->data['home'] = $this->url->link('common/home');
		$this->data['wishlist'] = $this->url->link('account/wishlist', '', 'SSL');
		$this->data['logged'] = $this->customer->isLogged();
		$this->data['account'] = $this->url->link('account/account', '', 'SSL');
		$this->data['shopping_cart'] = $this->url->link('checkout/cart');
		$this->data['checkout'] = $this->url->link('checkout/checkout', '', 'SSL');

		// Daniel's robot detector
		$status = true;

		if (isset($this->request->server['HTTP_USER_AGENT'])) {
			$robots = explode("\n", trim($this->config->get('config_robots')));

			foreach ($robots as $robot) {
				if ($robot && strpos($this->request->server['HTTP_USER_AGENT'], trim($robot)) !== false) {
					$status = false;

					break;
				}
			}
		}

		// A dirty hack to try to set a cookie for the multi-store feature
		$this->load->model('setting/store');

		$this->data['stores'] = array();

		if ($this->config->get('config_shared') && $status) {
			$this->data['stores'][] = $server . 'catalog/view/javascript/crossdomain.php?session_id=' . $this->session->getId();

			$stores = $this->model_setting_store->getStores();

			foreach ($stores as $store) {
				$this->data['stores'][] = $store['url'] . 'catalog/view/javascript/crossdomain.php?session_id=' . $this->session->getId();
			}
		}

		// Search		
		if (isset($this->request->get['search'])) {
			$this->data['search'] = $this->request->get['search'];
		} else {
			$this->data['search'] = '';
		}

		// Menu
		$this->load->model('catalog/category');

		$this->load->model('catalog/product');

		$this->data['categories'] = array();

		$categories = $this->model_catalog_category->getCategories(0);

		foreach ($categories as $category) {
			if ($category['top']) {
				// Level 2
				$children_data = array();

				$children = $this->model_catalog_category->getCategories($category['category_id']);

				foreach ($children as $child) {
					$data = array(
						'filter_category_id'  => $child['category_id'],
						'filter_sub_category' => true
					);

					$product_total = $this->model_catalog_product->getTotalProducts($data);

					$children_data[] = array(
						'name'  => $child['name'] . ($this->config->get('config_product_count') ? ' (' . $product_total . ')' : ''),
						'href'  => $this->url->link('product/category', 'path=' . $category['category_id'] . '_' . $child['category_id'])
					);						
				}

				// Level 1
				$this->data['categories'][] = array(
'sort_order'  => $category['sort_order'],
				    'id'       => $category['category_id'],
					'name'     => $category['name'],
					'children' => $children_data,
					'column'   => $category['column'] ? $category['column'] : 1,
					'href'     => $this->url->link('product/category', 'path=' . $category['category_id'])
				);
			}
		}

		$this->children = array(
			'module/language',
			'module/currency',
			'module/cart'
		);


				
				
				if (isset($this->request->get['category_id'])) {
		$category_id = $this->request->get['category_id'];
		}
		elseif (isset($this->request->cookie['cat'])) {
			$category_id = $this->request->cookie['cat'];
		} else {
	setcookie('cat', '60', time() + 60 * 60 * 24 * 30, '/', $this->request->server['HTTP_HOST']);
	$this->redirect('/');
	}
				
				//load manufacturer
				$this->load->model('catalog/manufacturer');
				$this->data['manufacturer'] = array();
				$manufacturer_data = array();
				$manufacturers = $this->model_catalog_manufacturer->getManufacturersByCatID($category_id);
				if($manufacturers){
				  //$first_man_id = $manufacturers[0]['manufacturer_id'];
					foreach($manufacturers as $manufacturer){
						$manufacturer_data[] = array(
							'name' => $manufacturer['name'],
							'href' => $this->url->link('product/manufacturer/product', 'category_id='.$category_id.'&manufacturer_id='.$manufacturer['manufacturer_id'])
						);
					}}
				$this->data['manufacturer'][] = array(
					'sort_order' => 1,
					'name' => $this->language->get('text_manufacturer'),
					'children' => $manufacturer_data,
					'column'   => 1,
          'href'     => $this->url->link('product/manufacturer/product','category_id='.$category_id.'&manufacturer_id='.$manufacturers[0]['manufacturer_id']),
				);
				$this->data['categories'] = array_merge($this->data['categories'],$this->data['manufacturer']);
				$sort_order = array(); 
	  
				foreach ($this->data['categories'] as $key => $value) {
		      		$sort_order[$key] = $value['sort_order'];
		    	}
				
				array_multisort($sort_order, SORT_ASC, $this->data['categories']);
				
			
		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/common/header.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/common/header.tpl';
		} else {
			$this->template = 'default/template/common/header.tpl';
		}

		$this->render();
	} 	
}
?>
