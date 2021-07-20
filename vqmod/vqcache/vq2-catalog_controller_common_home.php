<?php  
class ControllerCommonHome extends Controller {
	public function index() {
		$this->document->setTitle($this->config->get('config_title'));

				$this->document->addLink($this->config->get('config_url'), 'canonical');
$this->document->setKeywords($this->config->get('config_meta_keywords'));
		$this->document->setDescription($this->config->get('config_meta_description'));

		$this->data['heading_title'] = $this->config->get('config_title');
		
		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/common/home.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/common/home.tpl';
		} else {
			$this->template = 'default/template/common/home.tpl';
		}
	
		
		$this->language->load('common/header');
		$this->data['text_search'] = $this->language->get('text_search');
		if (isset($this->request->get['search'])) {
			$this->data['search'] = $this->request->get['search'];
		} else {
			$this->data['search'] = '';
		}
		
			$this->load->model('catalog/manufacturer');

			$this->data['manu'] = $this->model_catalog_manufacturer->getManufacturersByCatID($_COOKIE['category']);
		
		
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
				    'id'       => $category['category_id'],
					'name'     => $category['name'],
					'children' => $children_data,
					'column'   => $category['column'] ? $category['column'] : 1,
					'href'     => $this->url->link('product/category', 'path=' . $category['category_id'])
				);
			}
		}
		
		
		$this->children = array(
			'common/column_left',
			'common/column_right',
			'common/content_top',
			'common/content_bottom',
			'common/footer',
			'common/header'
		);
										
		$this->response->setOutput($this->render());
	}
	
	
	public function compare()
	{
		$data['brand1']=$_POST['brand1'];
		$data['brand2']=$_POST['brand2'];
		
		$this->load->model('catalog/product');
		$value = $this->model_catalog_product->getProductsByManyManufacturers($data);
	
		for($i=0;$i<count($value);$i++)
		{
			if($i==0)
			{
				$dane .='<div style="width:100%;text-align:center; margin: 0 auto;">'.$value[$i]['main']['ean'].' mm </div>
							<div style="clear:both"></div>
								<ul class="timeline">
						';					
						
			}
			
			if($i==0) {$inv='';$inv2='';}
			
		if($value[$i]['poz']>0)
		{
			$dane.=' <li>
						<div class="timeline-badge">
							<a><i class="fa fa-circle "  title="'.$value[$i]['main']['ean'].'"></i></a>
						</div>
						<div class="timeline-panel">          
						<div class="timeline-body"><p style="text-align:right">';
						$dane2='';
					
						foreach($value[$i]['poz']->rows as $result)
						{		
						
							if($result['model']!="")
							{
								$link=$this->url->link('product/product', $path. '&product_id=' . $result['product_id'] . $url);					
								$dane2 .='<a href="'.$link.'" style="color:gray">'.$result['model'].'</a>&nbsp; ';
							}
						}			
							
						$dane .=$dane2.'</p></div>           
						</div>
					</li>';
		}			
			if($value[$i]['poz2']>0)
			{
			$dane.=' <li class="timeline-inverted">
						<div class="timeline-badge">
							<a><i class="fa fa-circle invert" title="'.$value[$i]['main']['ean'].'" ></i></a>
						</div>
						<div class="timeline-panel">          
						<div class="timeline-body"><p>';
						$dane2='';
						foreach($value[$i]['poz2']->rows as $result)
						{		
							if($result['model']!="")
							{
								$link=$this->url->link('product/product', $path. '&product_id=' . $result['product_id'] . $url);					
								$dane2 .='<a href="'.$link.'" style="color:gray">'.$result['model'].'</a>&nbsp; ';
							}
							
						}			
							
						$dane .=$dane2.'</p></div>           
						</div>
					</li>';
			}		
		
		
			
			
			
			if(count($value)-1==$i)
			{
				$dane .='  
				</ul>
				
				<div style="clear:both;width:100%;text-align:center; margin: 0 auto;">'.$value[$i]['main']['ean'].' mm </div>';
			}
		}
		
		echo $dane;
		
	}
}
?>