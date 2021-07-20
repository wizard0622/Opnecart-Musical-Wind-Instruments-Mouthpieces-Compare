<?php  
class ControllerProductAjaxCompare extends Controller {
    
    public function index() { 
	
	$this->language->load('product/compare');
	
	if (isset($this->request->get['remove'])) {
	    $key = array_search($this->request->get['remove'], $this->session->data['compare']);

	    if ($key !== false) {
		unset($this->session->data['compare'][$key]);
	    }

	    $this->session->data['success'] = $this->language->get('text_remove');

	    echo "removed";
	    exit;
	}
    }
    
    public function compare_list() {
	$this->load->model('catalog/product');
	$this->load->model('tool/image');
        
	$this->language->load('product/ajaxcompare');
	
	$this->data['compare_list'] = array();
	$this->data['compare_url'] = $this->url->link('product/compare');
	
	$this->data['text_add_sign'] = $this->language->get('text_add_sign');
	$this->data['text_add_another'] = $this->language->get('text_add_another');
	$this->data['button_product_compare'] = $this->language->get('button_product_compare');
	
	if (isset($this->session->data['compare'])) {
	    $product_ids = $this->session->data['compare'];
	    foreach($product_ids as $product_id){
		$product_info = $this->model_catalog_product->getProduct($product_id);

		$this->data['compare_list'][] = array(
		    'product_id' => $product_info['product_id'],
		     'product_name' => $product_info['name'],
                     'product_sku' => $product_info['sku'],
                     'product_manufacturer' => $product_info['manufacturer'],
                     'product_model' => $product_info['model'],
		     'product_image' => $this->model_tool_image->resize($product_info['image'], 60, 80)
		);
	    }
	}
	
	if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/product/ajaxcompare_list.tpl')) {
		$this->template = $this->config->get('config_template') . '/template/product/ajaxcompare_list.tpl';
	} else {
		$this->template = 'default/template/product/ajaxcompare_list.tpl';
	}

	$this->response->setOutput($this->render());
    }
    
    public function add() { 
	$this->language->load('product/compare');

	$json = array();

	if (!isset($this->session->data['compare'])) {
	    $this->session->data['compare'] = array();
	}

	if (isset($this->request->post['product_id'])) {
	    $product_id = $this->request->post['product_id'];
	} else {
	    $product_id = 0;
	}

	$this->load->model('catalog/product');
	$this->load->model('tool/image');

	$product_info = $this->model_catalog_product->getProduct($product_id);

	if ($product_info) {
	    
	    if (!in_array($this->request->post['product_id'], $this->session->data['compare'])) {	
		if (count($this->session->data['compare']) >= 8) {
		    array_shift($this->session->data['compare']);
		}

		$this->session->data['compare'][] = $this->request->post['product_id'];
	    }

	    $json['total'] = sprintf($this->language->get('text_compare'), (isset($this->session->data['compare']) ? count($this->session->data['compare']) : 0));
	    
	    $json['product_id'] = $product_info['product_id'];
	    $json['product_name'] = $product_info['name'];
	    $json['product_image'] = $this->model_tool_image->resize($product_info['image'], 80, 100);
	    $json['compare'] = (isset($this->session->data['compare']) ? count($this->session->data['compare']) : 0);
	}	

	$this->response->setOutput(json_encode($json));
    }
    
    public function removeall(){
	
	unset($this->session->data['compare']);
	
	$this->data['compare_list'] = array();
	
	if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/product/ajaxcompare_list.tpl')) {
		$this->template = $this->config->get('config_template') . '/template/product/ajaxcompare_list.tpl';
	} else {
		$this->template = 'default/template/product/ajaxcompare_list.tpl';
	}

	$this->response->setOutput($this->render());
    }

  public function addNew() { 
  	$this->language->load('product/compare');
  
  	$json = array();
  
  	if (!isset($this->session->data['compare'])) {
  	    $this->session->data['compare'] = array();
  	}
  
  	if (isset($this->request->post['product_id'])) {
  	    $product_id = $this->request->post['product_id'];
  	} else {
  	    $product_id = 0;
  	}
  
/*  	$this->load->model('catalog/product');
  	$this->load->model('tool/image');
  
  	$product_info = $this->model_catalog_product->getProduct($product_id);
  
*/
  	if ($product_info) {
  	    
  	    if (!in_array($this->request->post['product_id'], $this->session->data['compare'])) 	
      		$this->session->data['compare'][] = $this->request->post['product_id'];
  	    
        $html_text = $this->compare_list();
  	    $json['total'] = sprintf($this->language->get('text_compare'), (isset($this->session->data['compare']) ? count($this->session->data['compare']) : 0));
  	    
  	    $json['html_text'] = $html_text;
  	    $json['empty'] = empty($html_text)? 1 : 0 ;
/* ??? */
  	    $json['product_id'] = $product_info['product_id'];
  	    $json['product_name'] = $product_info['name'];
  	    $json['product_image'] = $this->model_tool_image->resize($product_info['image'], 80, 100);
/* ???? */
  	    
  	    $json['compare'] = (isset($this->session->data['compare']) ? count($this->session->data['compare']) : 0);
        
  	}	
  
  	$this->response->setOutput(json_encode($json));
      }
   
}
?>