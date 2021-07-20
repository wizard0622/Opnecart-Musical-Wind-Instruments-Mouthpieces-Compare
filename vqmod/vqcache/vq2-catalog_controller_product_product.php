<?php

class ControllerProductProduct extends Controller {

    private $error = array();

    public function index() {


        $this->language->load('product/product');

        $this->data['breadcrumbs'] = array();

        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->config->get('config_url'),
            'separator' => false
        );

        $this->load->model('catalog/category');

        if (isset($this->request->get['path'])) {
            $path = '';

            $parts = explode('_', (string) $this->request->get['path']);

            $category_id = (int) array_pop($parts);

            foreach ($parts as $path_id) {
                if (!$path) {
                    $path = $path_id;
                } else {
                    $path .= '_' . $path_id;
                }

                $category_info = $this->model_catalog_category->getCategory($path_id);

                if ($category_info) {
                    $this->data['breadcrumbs'][] = array(
                        'text' => $category_info['name'],
                        'href' => $this->url->link('product/category', 'path=' . $path),
                        'separator' => $this->language->get('text_separator')
                    );
                }
            }

            // Set the last category breadcrumb
            $category_info = $this->model_catalog_category->getCategory($category_id);

            if ($category_info) {
                $url = '';

                if (isset($this->request->get['sort'])) {
                    $url .= '&sort=' . $this->request->get['sort'];
                }

                if (isset($this->request->get['order'])) {
                    $url .= '&order=' . $this->request->get['order'];
                }

                if (isset($this->request->get['page'])) {
                    $url .= '&page=' . $this->request->get['page'];
                }

                if (isset($this->request->get['limit'])) {
                    $url .= '&limit=' . $this->request->get['limit'];
                }

                $this->data['breadcrumbs'][] = array(
                    'text' => $category_info['name'],
                    'href' => $this->url->link('product/category', 'path=' . $this->request->get['path'] . $url),
                    'separator' => $this->language->get('text_separator')
                );
            }
        }

        $this->load->model('catalog/manufacturer');

        if (isset($this->request->get['manufacturer_id'])) {
            $this->data['breadcrumbs'][] = array(
                'text' => $this->language->get('text_brand'),
                'href' => $this->url->link('product/manufacturer'),
                'separator' => $this->language->get('text_separator')
            );

            $url = '';

            if (isset($this->request->get['sort'])) {
                $url .= '&sort=' . $this->request->get['sort'];
            }

            if (isset($this->request->get['order'])) {
                $url .= '&order=' . $this->request->get['order'];
            }

            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            if (isset($this->request->get['limit'])) {
                $url .= '&limit=' . $this->request->get['limit'];
            }

            $manufacturer_info = $this->model_catalog_manufacturer->getManufacturer($this->request->get['manufacturer_id']);

            if ($manufacturer_info) {
                $this->data['breadcrumbs'][] = array(
                    'text' => $manufacturer_info['name'],
                    'href' => $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $this->request->get['manufacturer_id'] . $url),
                    'separator' => $this->language->get('text_separator')
                );
            }
        }

        if (isset($this->request->get['search']) || isset($this->request->get['tag'])) {
            $url = '';

            if (isset($this->request->get['search'])) {
                $url .= '&search=' . $this->request->get['search'];
            }

            if (isset($this->request->get['tag'])) {
                $url .= '&tag=' . $this->request->get['tag'];
            }

            if (isset($this->request->get['description'])) {
                $url .= '&description=' . $this->request->get['description'];
            }

            if (isset($this->request->get['category_id'])) {
                $url .= '&category_id=' . $this->request->get['category_id'];
            }

            if (isset($this->request->get['sub_category'])) {
                $url .= '&sub_category=' . $this->request->get['sub_category'];
            }

            if (isset($this->request->get['sort'])) {
                $url .= '&sort=' . $this->request->get['sort'];
            }

            if (isset($this->request->get['order'])) {
                $url .= '&order=' . $this->request->get['order'];
            }

            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            if (isset($this->request->get['limit'])) {
                $url .= '&limit=' . $this->request->get['limit'];
            }

            $this->data['breadcrumbs'][] = array(
                'text' => $this->language->get('text_search'),
                'href' => $this->url->link('product/search', $url),
                'separator' => $this->language->get('text_separator')
            );
        }

        if (isset($this->request->get['product_id'])) {
            $product_id = (int) $this->request->get['product_id'];
        } else {
            $product_id = 0;
        }

        $this->load->model('catalog/product');

        $product_info = $this->model_catalog_product->getProduct($product_id);

        if ($product_info) {

            $categories = $this->model_catalog_product->getCategories($product_info['product_id']);
            if ($categories) {
                $categories_info = $this->model_catalog_category->getCategory($categories[0]['category_id']);
                $this->data['product_category_id'] = $categories_info['category_id'];
            }

            $url = '';

            if (isset($this->request->get['path'])) {
                $url .= '&path=' . $this->request->get['path'];
            }

            if (isset($this->request->get['filter'])) {
                $url .= '&filter=' . $this->request->get['filter'];
            }

            if (isset($this->request->get['manufacturer_id'])) {
                $url .= '&manufacturer_id=' . $this->request->get['manufacturer_id'];
            }

            if (isset($this->request->get['search'])) {
                $url .= '&search=' . $this->request->get['search'];
            }

            if (isset($this->request->get['tag'])) {
                $url .= '&tag=' . $this->request->get['tag'];
            }

            if (isset($this->request->get['description'])) {
                $url .= '&description=' . $this->request->get['description'];
            }

            if (isset($this->request->get['category_id'])) {
                $url .= '&category_id=' . $this->request->get['category_id'];
            }

            if (isset($this->request->get['sub_category'])) {
                $url .= '&sub_category=' . $this->request->get['sub_category'];
            }

            if (isset($this->request->get['sort'])) {
                $url .= '&sort=' . $this->request->get['sort'];
            }

            if (isset($this->request->get['order'])) {
                $url .= '&order=' . $this->request->get['order'];
            }

            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            if (isset($this->request->get['limit'])) {
                $url .= '&limit=' . $this->request->get['limit'];
            }

            $this->data['breadcrumbs'][] = array(
                'text' => $product_info['name'],
                'href' => $this->url->link('product/product', $url . '&product_id=' . $this->request->get['product_id']),
                'separator' => $this->language->get('text_separator')
            );

            ($product_info['custom_title'] == '')?$this->document->setTitle(((isset($category_info['name']))?($category_info['name'].' : '):'').$product_info['name']):$this->document->setTitle($product_info['custom_title']);
            $this->document->setDescription($product_info['meta_description']);
            $this->document->setKeywords($product_info['meta_keyword']);
            $this->document->addLink($this->url->link('product/product', 'product_id=' . $this->request->get['product_id']), 'canonical');
            $this->document->addScript('catalog/view/javascript/jquery/tabs.js');
            $this->document->addScript('catalog/view/javascript/jquery/colorbox/jquery.colorbox-min.js');
            $this->document->addStyle('catalog/view/javascript/jquery/colorbox/colorbox.css');

            $this->data['heading_title'] = ($product_info['custom_h1'] <> '')?$product_info['custom_h1']:$product_info['name'];

$this->data['custom_alt'] = $product_info['custom_alt'];
            $this->data['text_select'] = $this->language->get('text_select');
            $this->data['text_manufacturer'] = $this->language->get('text_manufacturer');
            $this->data['text_model'] = $this->language->get('text_model');
            $this->data['text_reward'] = $this->language->get('text_reward');
            $this->data['text_points'] = $this->language->get('text_points');
            $this->data['text_discount'] = $this->language->get('text_discount');
            $this->data['text_stock'] = $this->language->get('text_stock');
            $this->data['text_price'] = $this->language->get('text_price');
            $this->data['text_tax'] = $this->language->get('text_tax');
            $this->data['text_discount'] = $this->language->get('text_discount');
            $this->data['text_option'] = $this->language->get('text_option');
            $this->data['text_qty'] = $this->language->get('text_qty');
            $this->data['text_minimum'] = sprintf($this->language->get('text_minimum'), $product_info['minimum']);
            $this->data['text_or'] = $this->language->get('text_or');
            $this->data['text_write'] = $this->language->get('text_write');
            $this->data['text_note'] = $this->language->get('text_note');
            $this->data['text_share'] = $this->language->get('text_share');
            $this->data['text_wait'] = $this->language->get('text_wait');
            $this->data['text_tags'] = $this->language->get('text_tags');

            $this->data['entry_name'] = $this->language->get('entry_name');
            $this->data['entry_review'] = $this->language->get('entry_review');
            $this->data['entry_rating'] = $this->language->get('entry_rating');
            $this->data['entry_good'] = $this->language->get('entry_good');
            $this->data['entry_bad'] = $this->language->get('entry_bad');
            $this->data['entry_captcha'] = $this->language->get('entry_captcha');

            $this->data['button_cart'] = $this->language->get('button_cart');
            $this->data['button_wishlist'] = $this->language->get('button_wishlist');
            $this->data['button_compare'] = $this->language->get('button_compare');
            $this->data['button_upload'] = $this->language->get('button_upload');
            $this->data['button_continue'] = $this->language->get('button_continue');

            $this->load->model('catalog/review');

            $this->data['tab_description'] = $this->language->get('tab_description');
            $this->data['tab_attribute'] = $this->language->get('tab_attribute');
            $this->data['tab_review'] = sprintf($this->language->get('tab_review'), $product_info['reviews']);
            $this->data['tab_related'] = $this->language->get('tab_related');

            $this->data['product_id'] = $this->request->get['product_id'];
            $this->data['manufacturer'] = $product_info['manufacturer'];
            $this->data['ean'] = $product_info['ean'];
            $this->data['mpn'] = $product_info['mpn'];
            $this->data['jan'] = $product_info['jan'];
            $this->data['sku'] = $product_info['sku'];
			  
			  
			  
			  $wish=0;
			  for($i=0;$i<count($this->session->data['wishlist']);$i++)
			  {
				  if($this->session->data['wishlist'][$i]==$this->request->get['product_id'])
				  {
					  $wish=1;
				  }
			  }
			  
			  
			  
			$this->data['wish'] = $wish;
            $this->data['isbn'] = $product_info['isbn'];
            $this->data['manufacturer_id'] = $product_info['manufacturer_id'];
            $this->data['manufacturers'] = $this->url->link('product/manufacturer/product', 'manufacturer_id=' . $product_info['manufacturer_id']);
            $this->data['model'] = $product_info['model'];
            $this->data['reward'] = $product_info['reward'];
            $this->data['points'] = $product_info['points'];

			
				$this->data['mbreadcrumbs'] = array();

				$this->data['mbreadcrumbs'][] = array(
					'text'      => $this->language->get('text_home'),
					'href'      => $this->url->link('common/home')
				);
				
				if ($this->model_catalog_product->getFullPath($this->request->get['product_id'])) {
					
					$path = '';
			
					$parts = explode('_', (string)$this->model_catalog_product->getFullPath($this->request->get['product_id']));
					
					$category_id = (int)array_pop($parts);
											
					foreach ($parts as $path_id) {
						if (!$path) {
							$path = $path_id;
						} else {
							$path .= '_' . $path_id;
						}
						
						$category_info = $this->model_catalog_category->getCategory($path_id);
						
						if ($category_info) {
							$this->data['mbreadcrumbs'][] = array(
								'text'      => $category_info['name'],
								'href'      => $this->url->link('product/category', 'path=' . $path)								
							);
						}
					}
					
					$category_info = $this->model_catalog_category->getCategory($category_id);
					
					if ($category_info) {			
						$url = '';
											
						$this->data['mbreadcrumbs'][] = array(
							'text'      => $category_info['name'],
							'href'      => $this->url->link('product/category', 'path=' . $this->model_catalog_product->getFullPath($this->request->get['product_id']))						
						);
					}
			
				
				} else {
				$this->data['mbreadcrumb'] = false;
				}
				
				$this->data['review_no'] = $product_info['reviews'];		
				$this->data['quantity'] = $product_info['quantity'];						
			
			
            $this->data['category_id'] = $category_id;
            if ($product_info['quantity'] <= 0) {
                $this->data['stock'] = $product_info['stock_status'];
            } elseif ($this->config->get('config_stock_display')) {
                $this->data['stock'] = $product_info['quantity'];
            } else {
                $this->data['stock'] = $this->language->get('text_instock');
            }

            $this->load->model('tool/image');

            if ($product_info['image']) {
                $this->data['popup'] = $this->model_tool_image->resize($product_info['image'], $this->config->get('config_image_popup_width'), $this->config->get('config_image_popup_height'));
            } else {
                $this->data['popup'] = '';
            }

            if ($product_info['image']) {
                $this->data['thumb'] = $this->model_tool_image->resize($product_info['image'], $this->config->get('config_image_thumb_width'), $this->config->get('config_image_thumb_height'));
            } else {
                $this->data['thumb'] = '';
            }

            $this->data['images'] = array();

            $results = $this->model_catalog_product->getProductImages($this->request->get['product_id']);

            foreach ($results as $result) {
                $this->data['images'][] = array(
                    'popup' => $this->model_tool_image->resize($result['image'], $this->config->get('config_image_popup_width'), $this->config->get('config_image_popup_height')),
                    'thumb' => $this->model_tool_image->resize($result['image'], $this->config->get('config_image_additional_width'), $this->config->get('config_image_additional_height'))
                );
            }

            if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
                $this->data['price'] = $this->currency->format($this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax')));
            } else {
                $this->data['price'] = false;
            }

            if ((float) $product_info['special']) {
                $this->data['special'] = $this->currency->format($this->tax->calculate($product_info['special'], $product_info['tax_class_id'], $this->config->get('config_tax')));
            } else {
                $this->data['special'] = false;
            }

            if ($this->config->get('config_tax')) {
                $this->data['tax'] = $this->currency->format((float) $product_info['special'] ? $product_info['special'] : $product_info['price']);
            } else {
                $this->data['tax'] = false;
            }

            $discounts = $this->model_catalog_product->getProductDiscounts($this->request->get['product_id']);

            $this->data['discounts'] = array();

            foreach ($discounts as $discount) {
                $this->data['discounts'][] = array(
                    'quantity' => $discount['quantity'],
                    'price' => $this->currency->format($this->tax->calculate($discount['price'], $product_info['tax_class_id'], $this->config->get('config_tax')))
                );
            }

            $this->data['options'] = array();

            foreach ($this->model_catalog_product->getProductOptions($this->request->get['product_id']) as $option) {
                if ($option['type'] == 'select' || $option['type'] == 'radio' || $option['type'] == 'checkbox' || $option['type'] == 'image') {
                    $option_value_data = array();

                    foreach ($option['option_value'] as $option_value) {
                        if (!$option_value['subtract'] || ($option_value['quantity'] > 0)) {
                            if ((($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) && (float) $option_value['price']) {
                                $price = $this->currency->format($this->tax->calculate($option_value['price'], $product_info['tax_class_id'], $this->config->get('config_tax')));
                            } else {
                                $price = false;
                            }

                            $option_value_data[] = array(
                                'product_option_value_id' => $option_value['product_option_value_id'],
                                'option_value_id' => $option_value['option_value_id'],
                                'name' => $option_value['name'],
                                'image' => $this->model_tool_image->resize($option_value['image'], 50, 50),
                                'price' => $price,
                                'price_prefix' => $option_value['price_prefix']
                            );
                        }
                    }

                    $this->data['options'][] = array(
                        'product_option_id' => $option['product_option_id'],
                        'option_id' => $option['option_id'],
                        'name' => $option['name'],
                        'type' => $option['type'],
                        'option_value' => $option_value_data,
                        'required' => $option['required']
                    );
                } elseif ($option['type'] == 'text' || $option['type'] == 'textarea' || $option['type'] == 'file' || $option['type'] == 'date' || $option['type'] == 'datetime' || $option['type'] == 'time') {
                    $this->data['options'][] = array(
                        'product_option_id' => $option['product_option_id'],
                        'option_id' => $option['option_id'],
                        'name' => $option['name'],
                        'type' => $option['type'],
                        'option_value' => $option['option_value'],
                        'required' => $option['required']
                    );
                }
            }

            if ($product_info['minimum']) {
                $this->data['minimum'] = $product_info['minimum'];
            } else {
                $this->data['minimum'] = 1;
            }

            $this->data['review_status'] = $this->config->get('config_review_status');
            $this->data['reviews'] = sprintf($this->language->get('text_reviews'), (int) $product_info['reviews']);
            $this->data['rating'] = (int) $product_info['rating'];
            $this->data['description'] = $product_info['description'];

				$autolinks = $this->config->get('autolinks'); 
				
				if (isset($autolinks) && (strpos($this->data['description'], 'iframe') == false) && (strpos($this->data['description'], 'object') == false)){
				$xdescription = mb_convert_encoding(html_entity_decode($this->data['description'], ENT_COMPAT, "UTF-8"), 'HTML-ENTITIES', "UTF-8"); 
				
				libxml_use_internal_errors(true);
				$dom = new DOMDocument; 			
				$dom->loadHTML('<div>'.$xdescription.'</div>');				
				libxml_use_internal_errors(false);

				
				$xpath = new DOMXPath($dom);
								
				foreach ($autolinks as $autolink)
				{	
					$keyword = $autolink['keyword'];
					$xlink = mb_convert_encoding(html_entity_decode($autolink['link'], ENT_COMPAT, "UTF-8"), 'HTML-ENTITIES', "UTF-8");
					$target = $autolink['target'];
					$tooltip = isset($autolink['tooltip']);
													
					$pTexts = $xpath->query(
						sprintf('///text()[contains(., "%s")]', $keyword)
					);
					
					foreach ($pTexts as $pText) {
						$this->parseText($pText, $keyword, $dom, $xlink, $target, $tooltip);
					}

									
				}
						
				$this->data['description'] = $dom->saveXML($dom->documentElement);
				
				}
				
			
            $this->data['attribute_groups'] = $this->model_catalog_product->getProductAttributes($this->request->get['product_id']);

            $this->data['products'] = array();

            $results = $this->model_catalog_product->getProductRelated($this->request->get['product_id']);

            foreach ($results as $result) {
                if ($result['image']) {
                    $image = $this->model_tool_image->resize($result['image'], $this->config->get('config_image_related_width'), $this->config->get('config_image_related_height'));
                } else {
                    $image = false;
                }

                if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
                    $price = $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax')));
                } else {
                    $price = false;
                }

                if ((float) $result['special']) {
                    $special = $this->currency->format($this->tax->calculate($result['special'], $result['tax_class_id'], $this->config->get('config_tax')));
                } else {
                    $special = false;
                }

                if ($this->config->get('config_review_status')) {
                    $rating = (int) $result['rating'];
                } else {
                    $rating = false;
                }

                $this->data['products'][] = array(
                    'product_id' => $result['product_id'],
                    'thumb' => $image,
                    'name' => $result['name'],
                    'price' => $price,
                    'special' => $special,
                    'rating' => $rating,
                    'reviews' => sprintf($this->language->get('text_reviews'), (int) $result['reviews']),
                    'href' => $this->url->link('product/product', 'product_id=' . $result['product_id'])
                );
            }

            $this->data['tags'] = array();

            if ($product_info['tag']) {
                $tags = explode(',', $product_info['tag']);

                foreach ($tags as $tag) {
                    $this->data['tags'][] = array(
                        'tag' => trim($tag),
                        'href' => $this->url->link('product/search', 'tag=' . trim($tag))
                    );
                }
            }

            $this->data['text_payment_profile'] = $this->language->get('text_payment_profile');
            $this->data['profiles'] = $this->model_catalog_product->getProfiles($product_info['product_id']);

            $this->model_catalog_product->updateViewed($this->request->get['product_id']);

            if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/product/product.tpl')) {
                $this->template = $this->config->get('config_template') . '/template/product/product.tpl';
            } else {
                $this->template = 'default/template/product/product.tpl';
            }

            $this->children = array(
                'common/column_left',
                'common/column_right',
                'common/content_top',
                'common/content_bottom',
                'common/footer',
                'common/header'
            );


            $this->data['appkey'] = $this->config->get('yotpo_appkey');
            $this->data['language'] = $this->config->get('yotpo_language');
            $this->data['domain'] = HTTP_SERVER;
            $this->data['product_id'] = $this->request->get['product_id'];
            $this->data['product_name'] = strip_tags(html_entity_decode($this->data['heading_title']));
            $this->data['product_description'] = substr(strip_tags(html_entity_decode($this->data['description'])),0,1000);
            $this->data['product_url'] = HTTP_SERVER . 'index.php?route=product/product&product_id=' . $this->data['product_id'];
 	       
      	  	$yotpo_bread_crumbs = array();
          	foreach ($this->data['breadcrumbs'] as $breadcrumb) {
    	 		$yotpo_bread_crumbs[] = $breadcrumb['text'];
    	  	}
         	$this->data['yotpo_bread_crumbs'] = implode(';', $yotpo_bread_crumbs); 	       
            $this->data['product_image_url'] = $this->data['thumb'];
            
            $product = $this->model_catalog_product->getProduct($this->data['product_id']);
                      
            $this->data['product_models'] = $product['model'];

            $this->data['yotpo_review_tab_name'] = $this->config->get('yotpo_review_tab_name');
            $this->data['yotpo_widget_location'] = $this->config->get('yotpo_widget_location');            
          	$this->data['yotpo_bottom_line_enabled'] = $this->config->get('yotpo_bottom_line_enabled');
        
            $this->response->setOutput($this->render());
        } else {
            $url = '';

            if (isset($this->request->get['path'])) {
                $url .= '&path=' . $this->request->get['path'];
            }

            if (isset($this->request->get['filter'])) {
                $url .= '&filter=' . $this->request->get['filter'];
            }

            if (isset($this->request->get['manufacturer_id'])) {
                $url .= '&manufacturer_id=' . $this->request->get['manufacturer_id'];
            }

            if (isset($this->request->get['search'])) {
                $url .= '&search=' . $this->request->get['search'];
            }

            if (isset($this->request->get['tag'])) {
                $url .= '&tag=' . $this->request->get['tag'];
            }

            if (isset($this->request->get['description'])) {
                $url .= '&description=' . $this->request->get['description'];
            }

            if (isset($this->request->get['category_id'])) {
                $url .= '&category_id=' . $this->request->get['category_id'];
            }

            if (isset($this->request->get['sub_category'])) {
                $url .= '&sub_category=' . $this->request->get['sub_category'];
            }

            if (isset($this->request->get['sort'])) {
                $url .= '&sort=' . $this->request->get['sort'];
            }

            if (isset($this->request->get['order'])) {
                $url .= '&order=' . $this->request->get['order'];
            }

            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            if (isset($this->request->get['limit'])) {
                $url .= '&limit=' . $this->request->get['limit'];
            }

            $this->data['breadcrumbs'][] = array(
                'text' => $this->language->get('text_error'),
                'href' => $this->url->link('product/product', $url . '&product_id=' . $product_id),
                'separator' => $this->language->get('text_separator')
            );

            $this->document->setTitle($this->language->get('text_error'));

            $this->data['heading_title'] = $this->language->get('text_error');

            $this->data['text_error'] = $this->language->get('text_error');

            $this->data['button_continue'] = $this->language->get('button_continue');

            $this->data['continue'] = $this->config->get('config_url');

            $this->response->addHeader($this->request->server['SERVER_PROTOCOL'] . '/1.1 404 Not Found');

            if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/error/not_found.tpl')) {
                $this->template = $this->config->get('config_template') . '/template/error/not_found.tpl';
            } else {
                $this->template = 'default/template/error/not_found.tpl';
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
    }

    public function review() {
        $this->language->load('product/product');

        $this->load->model('catalog/review');

        $this->data['text_on'] = $this->language->get('text_on');
        $this->data['text_no_reviews'] = $this->language->get('text_no_reviews');

        if (isset($this->request->get['page'])) {
            $page = $this->request->get['page'];
        } else {
            $page = 1;
        }

        $this->data['reviews'] = array();

        $review_total = $this->model_catalog_review->getTotalReviewsByProductId($this->request->get['product_id']);

        $results = $this->model_catalog_review->getReviewsByProductId($this->request->get['product_id'], ($page - 1) * 5, 5);

        foreach ($results as $result) {
            $this->data['reviews'][] = array(
                'author' => $result['author'],
                'text' => $result['text'],
                'rating' => (int) $result['rating'],
                'reviews' => sprintf($this->language->get('text_reviews'), (int) $review_total),
                'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added']))
            );
        }

        $pagination = new Pagination();
        $pagination->total = $review_total;
        $pagination->page = $page;
        $pagination->limit = 5;
        $pagination->text = $this->language->get('text_pagination');
        $pagination->url = $this->url->link('product/product/review', 'product_id=' . $this->request->get['product_id'] . '&page={page}');

        $this->data['pagination'] = $pagination->render();

        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/product/review.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/product/review.tpl';
        } else {
            $this->template = 'default/template/product/review.tpl';
        }

        $this->response->setOutput($this->render());
    }

    public function getRecurringDescription() {
        $this->language->load('product/product');
        $this->load->model('catalog/product');

        if (isset($this->request->post['product_id'])) {
            $product_id = $this->request->post['product_id'];
        } else {
            $product_id = 0;
        }

        if (isset($this->request->post['profile_id'])) {
            $profile_id = $this->request->post['profile_id'];
        } else {
            $profile_id = 0;
        }

        if (isset($this->request->post['quantity'])) {
            $quantity = $this->request->post['quantity'];
        } else {
            $quantity = 1;
        }

        $product_info = $this->model_catalog_product->getProduct($product_id);
        $profile_info = $this->model_catalog_product->getProfile($product_id, $profile_id);

        $json = array();

        if ($product_info && $profile_info) {

            if (!$json) {
                $frequencies = array(
                    'day' => $this->language->get('text_day'),
                    'week' => $this->language->get('text_week'),
                    'semi_month' => $this->language->get('text_semi_month'),
                    'month' => $this->language->get('text_month'),
                    'year' => $this->language->get('text_year'),
                );

                if ($profile_info['trial_status'] == 1) {
                    $price = $this->currency->format($this->tax->calculate($profile_info['trial_price'] * $quantity, $product_info['tax_class_id'], $this->config->get('config_tax')));
                    $trial_text = sprintf($this->language->get('text_trial_description'), $price, $profile_info['trial_cycle'], $frequencies[$profile_info['trial_frequency']], $profile_info['trial_duration']) . ' ';
                } else {
                    $trial_text = '';
                }

                $price = $this->currency->format($this->tax->calculate($profile_info['price'] * $quantity, $product_info['tax_class_id'], $this->config->get('config_tax')));

                if ($profile_info['duration']) {
                    $text = $trial_text . sprintf($this->language->get('text_payment_description'), $price, $profile_info['cycle'], $frequencies[$profile_info['frequency']], $profile_info['duration']);
                } else {
                    $text = $trial_text . sprintf($this->language->get('text_payment_until_canceled_description'), $price, $profile_info['cycle'], $frequencies[$profile_info['frequency']], $profile_info['duration']);
                }

                $json['success'] = $text;
            }
        }

        $this->response->setOutput(json_encode($json));
    }

    public function write() {
        $this->language->load('product/product');

        $this->load->model('catalog/review');

        $json = array();

        if ($this->request->server['REQUEST_METHOD'] == 'POST') {
            if ((utf8_strlen($this->request->post['name']) < 3) || (utf8_strlen($this->request->post['name']) > 25)) {
                $json['error'] = $this->language->get('error_name');
            }

            if ((utf8_strlen($this->request->post['text']) < 25) || (utf8_strlen($this->request->post['text']) > 1000)) {
                $json['error'] = $this->language->get('error_text');
            }

            if (empty($this->request->post['rating'])) {
                $json['error'] = $this->language->get('error_rating');
            }

            if (empty($this->session->data['captcha']) || ($this->session->data['captcha'] != $this->request->post['captcha'])) {
                $json['error'] = $this->language->get('error_captcha');
            }

            if (!isset($json['error'])) {
                $this->model_catalog_review->addReview($this->request->get['product_id'], $this->request->post);

                $json['success'] = $this->language->get('text_success');
            }
        }

        $this->response->setOutput(json_encode($json));
    }


			private function parseText($node, $keyword, $dom, $link, $target='', $tooltip = 0)
			{
				if (mb_strpos($node->nodeValue, $keyword) !== false)
					{
						$keywordOffset = mb_strpos($node->nodeValue, $keyword, 0, 'UTF-8');
						$newNode = $node->splitText($keywordOffset);
						$newNode->deleteData(0, mb_strlen($keyword, 'UTF-8'));
						$span = $dom->createElement('a', $keyword);
						if ($tooltip)
							{
								$span->setAttribute('href', '#');
								$span->setAttribute('style', 'text-decoration:none');
								$span->setAttribute('class', 'title');
								$span->setAttribute('title', $keyword.'|'.$link);
							}
							else
							{
								$span->setAttribute('href', $link);
								$span->setAttribute('target', $target);
								$span->setAttribute('style', 'text-decoration:none');
							}							
						
						$node->parentNode->insertBefore($span, $newNode);
						$this->parseText($newNode ,$keyword, $dom, $link, $target, $tooltip);
					}					
			}
			
			

			
    public function captcha() {
        $this->load->library('captcha');

        $captcha = new Captcha();

        $this->session->data['captcha'] = $captcha->getCode();

        $captcha->showImage();
    }

    public function upload() {
        $this->language->load('product/product');

        $json = array();

        if (!empty($this->request->files['file']['name'])) {
            $filename = basename(preg_replace('/[^a-zA-Z0-9\.\-\s+]/', '', html_entity_decode($this->request->files['file']['name'], ENT_QUOTES, 'UTF-8')));

            if ((utf8_strlen($filename) < 3) || (utf8_strlen($filename) > 64)) {
                $json['error'] = $this->language->get('error_filename');
            }

            // Allowed file extension types
            $allowed = array();

            $filetypes = explode("\n", $this->config->get('config_file_extension_allowed'));

            foreach ($filetypes as $filetype) {
                $allowed[] = trim($filetype);
            }

            if (!in_array(substr(strrchr($filename, '.'), 1), $allowed)) {
                $json['error'] = $this->language->get('error_filetype');
            }

            // Allowed file mime types
            $allowed = array();

            $filetypes = explode("\n", $this->config->get('config_file_mime_allowed'));

            foreach ($filetypes as $filetype) {
                $allowed[] = trim($filetype);
            }

            if (!in_array($this->request->files['file']['type'], $allowed)) {
                $json['error'] = $this->language->get('error_filetype');
            }

            // Check to see if any PHP files are trying to be uploaded
            $content = file_get_contents($this->request->files['file']['tmp_name']);

            if (preg_match('/\<\?php/i', $content)) {
                $json['error'] = $this->language->get('error_filetype');
            }

            if ($this->request->files['file']['error'] != UPLOAD_ERR_OK) {
                $json['error'] = $this->language->get('error_upload_' . $this->request->files['file']['error']);
            }
        } else {
            $json['error'] = $this->language->get('error_upload');
        }

        if (!$json && is_uploaded_file($this->request->files['file']['tmp_name']) && file_exists($this->request->files['file']['tmp_name'])) {
            $file = basename($filename) . '.' . md5(mt_rand());

            // Hide the uploaded file name so people can not link to it directly.
            $json['file'] = $this->encryption->encrypt($file);

            move_uploaded_file($this->request->files['file']['tmp_name'], DIR_DOWNLOAD . $file);

            $json['success'] = $this->language->get('text_upload');
        }

        $this->response->setOutput(json_encode($json));
    }

    public function autocomplete() {
        $json = array();

        if (isset($this->request->get['filter_name']) || isset($this->request->get['filter_model']) || isset($this->request->get['filter_category_id'])) {
            $this->load->model('catalog/product');

            if (isset($this->request->get['filter_name'])) {
                $filter_name = $this->request->get['filter_name'];
            } else {
                $filter_name = '';
            }

            if (isset($this->request->get['filter_model'])) {
                $filter_model = $this->request->get['filter_model'];
            } else {
                $filter_model = '';
            }

            if (isset($this->request->get['limit'])) {
                $limit = $this->request->get['limit'];
            } else {
                $limit = 20;
            }

            $data = array(
                'filter_name' => $filter_name,
                'filter_model' => $filter_model,
                'start' => 0,
                'limit' => $limit
            );

            $results = $this->model_catalog_product->getProducts($data);

            foreach ($results as $result) {
                $option_data = array();

                $product_options = $this->model_catalog_product->getProductOptions($result['product_id']);

                foreach ($product_options as $product_option) {
                    $option_info = $this->model_catalog_product->getOption($product_option['option_id']);

                    if ($option_info) {
                        if ($option_info['type'] == 'select' || $option_info['type'] == 'radio' || $option_info['type'] == 'checkbox' || $option_info['type'] == 'image') {
                            $option_value_data = array();

                            foreach ($product_option['product_option_value'] as $product_option_value) {
                                $option_value_info = $this->model_catalog_option->getOptionValue($product_option_value['option_value_id']);

                                if ($option_value_info) {
                                    $option_value_data[] = array(
                                        'product_option_value_id' => $product_option_value['product_option_value_id'],
                                        'option_value_id' => $product_option_value['option_value_id'],
                                        'name' => $option_value_info['name'],
                                        'price' => (float) $product_option_value['price'] ? $this->currency->format($product_option_value['price'], $this->config->get('config_currency')) : false,
                                        'price_prefix' => $product_option_value['price_prefix']
                                    );
                                }
                            }

                            $option_data[] = array(
                                'product_option_id' => $product_option['product_option_id'],
                                'option_id' => $product_option['option_id'],
                                'name' => $option_info['name'],
                                'type' => $option_info['type'],
                                'option_value' => $option_value_data,
                                'required' => $product_option['required']
                            );
                        } else {
                            $option_data[] = array(
                                'product_option_id' => $product_option['product_option_id'],
                                'option_id' => $product_option['option_id'],
                                'name' => $option_info['name'],
                                'type' => $option_info['type'],
                                'option_value' => $product_option['option_value'],
                                'required' => $product_option['required']
                            );
                        }
                    }
                }

                $json[] = array(
                    'product_id' => $result['product_id'],
                    'name' => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8')),
                    'model' => $result['model'],
                    'option' => $option_data,
                    'price' => $result['price']
                );
            }
        }

        $this->response->setOutput(json_encode($json));
    }

    public function similar() {
        $diameter_d = isset($this->request->post['diameter']) ? (int) ($this->request->post['diameter']) : 0;
        $cutdepth_d = (isset($this->request->post['cutdepth'])) ? (int) $this->request->post['cutdepth'] : 0;
        $bore_d = (isset($this->request->post['bore'])) ? $this->request->post['bore'] : 0;
		$manu = (isset($this->request->post['manu'])) ? (int) $this->request->post['manu'] : 0;
        $product_diameter = isset($this->request->post['product_diameter']) ? (float) $this->request->post['product_diameter'] : 0;

        $product_cupdepth = isset($this->request->post['product_cupdepth']) ? $this->request->post['product_cupdepth'] : '';
        $product_bore = isset($this->request->post['product_bore']) ? (float) $this->request->post['product_bore'] : 0;
        $mminch = isset($this->request->post['mminch']) ? $this->request->post['mminch'] : 'mm';
        $category = isset($this->request->post['category']) ? (int) $this->request->post['category'] : 0;
		
        /* if($mminch == 'inch') {
          $product_diameter *= 25.4;
          $product_bore *= 25.4;
          } */
//    $html = $this->SimilarRender($category, $product_diameter,$product_cupdepth); 
        $html = $this->SimilarRender($manu,$category, $product_diameter, $diameter_d, $product_cupdepth, $cutdepth_d, $product_bore, $bore_d);
        $json = array(
            'similar_products' => $html,
            'success' => 1,
        );

        $this->response->setOutput(json_encode($json));
    }

//  public function SimilarRender($cat,$ean,$cup) {
    public function SimilarRender($manu,$category, $product_diameter, $diameter_d, $product_cupdepth, $cutdepth_d, $product_bore, $bore_d, $quantity = 2) {
        $inch = isset($this->request->post['mminch']) && $this->request->post['mminch'] == 'inch' ? 1 / 25.4 : 1;
        $this->load->model('catalog/manufacturer');
        $this->load->model('catalog/product');
        $this->data['products'] = array();
//    var_dump($cat. " " . $ean . " " .$cup);    	 
//    if($cat && $ean && $cup) {
        if ($category && $product_diameter && $product_cupdepth && $product_bore) {
            $manufacturers = $this->model_catalog_manufacturer->getManufacturersByCatID($category);
//            foreach ($manufacturers as $manufacturer) {

                $products = $this->model_catalog_product->getSimilarProductsNew($manu,$category, 0, $product_diameter, $diameter_d, $product_cupdepth, $cutdepth_d, $product_bore, $bore_d, $quantity);
                foreach ($products as $product) {
                    $this->data['products'][] = array(
                        'man' => $product['manufacturer'],
                        'model' => $product['model'],
                        'diam' => ($inch == 1 ? $product['ean'] : sprintf("%01.3f", (float) ($product['ean']) * $inch)),
                        'cup' => $product['jan'],
						'sku' => $product['sku'],
                        'bore' => ($inch == 1 ? $product['isbn'] : sprintf("%01.3f", (float) ($product['isbn']) * $inch)),
                        'link' => $product['href'],
                    );
                }
  //          }
        }
        //	$this->response->setOutput(json_encode($json));
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/product/similarproducts.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/product/similarproducts.tpl';
        } else {
            $this->template = 'default/template/product/similarproducts.tpl';
        }

        //$this->response->setOutput($this->render());
        return $this->render();
    }

}

?>