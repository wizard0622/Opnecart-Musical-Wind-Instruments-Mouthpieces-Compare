<?php

class ControllerProductManufacturer extends Controller {

    public function index() {

        $this->language->load('product/manufacturer');

        $this->load->model('catalog/manufacturer');

        $this->load->model('tool/image');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->data['heading_title'] = $this->language->get('heading_title');

        $this->data['text_index'] = $this->language->get('text_index');
        $this->data['text_empty'] = $this->language->get('text_empty');

        $this->data['button_continue'] = $this->language->get('button_continue');

        $this->data['breadcrumbs'] = array();

        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/home'),
            'separator' => false
        );

        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_brand'),
            'href' => $this->url->link('product/manufacturer'),
            'separator' => $this->language->get('text_separator')
        );

        $results = '';
        $category_id = '';

        if (isset($this->request->get['category_id'])) {
            $category_id = $this->request->get['category_id'];
            $results = $this->model_catalog_manufacturer->getManufacturersByCatID($category_id);
            if (!empty($results)) {
                $this->redirect($this->url->link('product/manufacturer/product', 'category_id=' . $category_id . '&manufacturer_id=' . $results[0]['manufacturer_id']));
            }
        }
        if (isset($this->request->get['category_id'])) {
            $category_id = $this->request->get['category_id'];
            $results = $this->model_catalog_manufacturer->getManufacturersByCatID($category_id);
        } elseif (isset($this->request->cookie['category'])) {
            $category_id = $this->request->cookie['category'];
            $results = $this->model_catalog_manufacturer->getManufacturersByCatID($category_id);
        } else {
            $results = $this->model_catalog_manufacturer->getManufacturers();
        }

        $this->data['categories'] = array();

        foreach ($results as $result) {
            if (is_int(substr($result['name'], 0, 1))) {
                $key = '0 - 9';
            } else {
                $key = substr(strtoupper($result['name']), 0, 1);
            }

            if (!isset($this->data['manufacturers'][$key])) {
                $this->data['categories'][$key]['name'] = $key;
            }

            $this->data['categories'][$key]['manufacturer'][] = array(
                'name' => $result['name'],
                'href' => $this->url->link('product/manufacturer/product', 'category_id=' . $category_id . '&manufacturer_id=' . $result['manufacturer_id'])
            );
        }

        $this->data['continue'] = $this->url->link('common/home');

        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/product/manufacturer_list.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/product/manufacturer_list.tpl';
        } else {
            $this->template = 'default/template/product/manufacturer_list.tpl';
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

    public function product() {
		
		

        $this->language->load('product/manufacturer');

        $this->load->model('catalog/manufacturer');

        $this->load->model('catalog/product');

        $this->load->model('tool/image');

        // Loaded for category filtering
        $this->load->model('catalog/category');

        if (!empty($this->request->get['category_id']))
            $this->data['manufacturer_list'] = $this->model_catalog_manufacturer->getManufacturersByCatID($this->request->get['category_id']);
        else
            $this->data['manufacturer_list'] = $this->model_catalog_manufacturer->getManufacturers();

        if (isset($this->request->get['manufacturer_id'])) {
            $manufacturer_id = $this->request->get['manufacturer_id'];
        } else {
            $manufacturer_id = 0;
        }

        if (isset($this->request->get['filter_series'])) {
            $filter_series = $this->request->get['filter_series'];
        } else {
            $filter_series = 0;
        }

        if (isset($this->request->get['sort'])) {
            $sort = $this->request->get['sort'];
        } else {
            $sort = 'p.sort_order';
        }

        if (isset($this->request->get['order'])) {
            $order = $this->request->get['order'];
        } else {
            $order = 'ASC';
        }

        if (isset($this->request->get['page'])) {
            $page = $this->request->get['page'];
        } else {
            $page = 1;
        }

        if (isset($this->request->get['limit'])) {
            $limit = $this->request->get['limit'];
        } else {
            $limit = $this->config->get('config_catalog_limit');
        }

        // Filtering by category

        if (isset($this->request->get['category_id'])) {
            $category_info = $this->model_catalog_category->getCategory($this->request->get['category_id']);
            //var_dump($category_info);
            if (!$category_info)
                unset($category_info);
        }
        elseif (isset($this->request->cookie['cat'])) {
            $category_info = $this->model_catalog_category->getCategory($this->request->cookie['cat']);
            if (!$category_info)
                unset($category_info);
        }



        $this->data['breadcrumbs'] = array();

        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/home'),
            'separator' => false
        );

        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_brand'),
            'href' => $this->url->link('product/manufacturer'),
            'separator' => $this->language->get('text_separator')
        );

        $manufacturer_info = $this->model_catalog_manufacturer->getManufacturer($manufacturer_id);

        if ($manufacturer_info) {
            
            //var_dump($manufacturer_info);

            if (isset($category_info)) {
                $this->document->setTitle($manufacturer_info['name'] . " " . $category_info['name'] . " Mouthpiece Chart");
            } else {
                $this->document->setTitle($manufacturer_info['name']);
            }

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

            if (isset($this->request->get['filter_series'])) {
                $url .= '&filter_series=' . $this->request->get['filter_series'];
            }

            // Category filtering of manufacturer listings

            if (isset($category_info) && isset($this->request->get['category_id'])) {
                $url .= '&category_id=' . $this->request->get['category_id'];
            } elseif (isset($category_info) && isset($this->request->cookie['cat'])) {
                $url .= '&category_id=' . $this->request->cookie['cat'];
            }


            $this->data['breadcrumbs'][] = array(
                'text' => $manufacturer_info['name'],
                'href' => $this->url->link('product/manufacturer/product', 'manufacturer_id=' . $this->request->get['manufacturer_id'] . $url),
                'separator' => $this->language->get('text_separator')
            );

            if (isset($category_info)) {
                $this->data['heading_title'] = $manufacturer_info['name'] . " " . $category_info['name'] . " Mouthpiece Chart";
            } else {
                $this->data['heading_title'] = $manufacturer_info['name'] . " Mouthpiece Chart";
            }

            $this->data['text_empty'] = $this->language->get('text_empty');
            $this->data['text_quantity'] = $this->language->get('text_quantity');
            $this->data['text_manufacturer'] = $this->language->get('text_manufacturer');
            $this->data['text_model'] = $this->language->get('text_model');
            $this->data['text_price'] = $this->language->get('text_price');
            $this->data['text_tax'] = $this->language->get('text_tax');
            $this->data['text_points'] = $this->language->get('text_points');
            $this->data['text_compare'] = sprintf($this->language->get('text_compare'), (isset($this->session->data['compare']) ? count($this->session->data['compare']) : 0));
            $this->data['text_display'] = $this->language->get('text_display');
            $this->data['text_comparet'] = $this->language->get('text_comparet');
            $this->data['text_list'] = $this->language->get('text_list');
            $this->data['text_grid'] = $this->language->get('text_grid');
            $this->data['text_sort'] = $this->language->get('text_sort');
            $this->data['text_limit'] = $this->language->get('text_limit');

            $this->data['button_cart'] = $this->language->get('button_cart');
            $this->data['button_wishlist'] = $this->language->get('button_wishlist');
            $this->data['button_compare'] = $this->language->get('button_compare');
            $this->data['button_continue'] = $this->language->get('button_continue');

            $this->data['compare'] = $this->url->link('product/compare');

            $this->data['products'] = array();

            $data = array(
                'filter_manufacturer_id' => $manufacturer_id,
//				'filter_name'            => $filter_series,
                'filter_series' => $filter_series,
                'sort' => $sort,
                'order' => $order,
                'start' => ($page - 1) * $limit,
                'limit' => $limit
            );

            // Category filtering of manufacturers

            if (isset($category_info) && isset($this->request->get['category_id'])) {
                $cid = explode("_", $this->request->get['category_id']);
                $cid = $cid[(count($cid) - 1)];
                $data['filter_category_id'] = $cid;
            } elseif (isset($category_info) && isset($this->request->cookie['cat'])) {
                $cid = explode("_", $this->request->cookie['cat']);
                $cid = $cid[(count($cid) - 1)];
                $data['filter_category_id'] = $cid;
            }


            $product_total = $this->model_catalog_product->getTotalProducts($data);

            $results = $this->model_catalog_product->getProducts($data);

            foreach ($results as $result) {
                if ($result['image']) {
                    $image = $this->model_tool_image->resize($result['image'], $this->config->get('config_image_product_width'), $this->config->get('config_image_product_height'));
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

                if ($this->config->get('config_tax')) {
                    $tax = $this->currency->format((float) $result['special'] ? $result['special'] : $result['price']);
                } else {
                    $tax = false;
                }

                if ($this->config->get('config_review')) {
                    $rating = (int) $result['rating'];
                } else {
                    $rating = false;
                }

                $this->data['products'][] = array(
                    'product_id' => $result['product_id'],
                    'thumb' => $image,
                    'name' => $result['name'],
                    'model' => $result['model'],
                    'sku' => $result['sku'],
                    'ean' => $result['ean'],
                    'jan' => $result['jan'],
                    'isbn' => $result['isbn'],
                    'sku' => $result['sku'],
                    'mpn' => $result['mpn'],
                    'description' => $result['description'],
                    'price' => $price,
                    'special' => $special,
                    'tax' => $tax,
                    'pmmanufacturer' => $result['manufacturer'],
                    'rating' => $result['rating'],
                    'reviews' => sprintf($this->language->get('text_reviews'), (int) $result['reviews']),
                    'attribute_groups' => $this->model_catalog_product->getProductAttributes($result['product_id']),
                    'href' => $this->url->link('product/product', $url . '&manufacturer_id=' . $result['manufacturer_id'] . '&product_id=' . $result['product_id'])
                );
            }

            $url = '';

            if (isset($this->request->get['limit'])) {
                $url .= '&limit=' . $this->request->get['limit'];
            }

            if (isset($this->request->get['category_id'])) {
                $url .= '&category_id=' . $this->request->get['category_id'];
            }

            $this->data['sorts'] = array();

            $series = $this->model_catalog_manufacturer->getSeries($manufacturer_id, isset($this->request->get['category_id']) ? $this->request->get['category_id'] : 0);
            $this->data['series']['All'] = array(
                'url' => $this->url->link('product/manufacturer/product', 'manufacturer_id=' . $manufacturer_id . (isset($this->request->get['category_id']) ? '&category_id=' . $this->request->get['category_id'] : '')),
                'selected' => 0,
            );
            foreach ($series as $serie) {
                $this->data['series'][$serie['sku']] = array(
                    'url' => $this->url->link('product/manufacturer/product', 'manufacturer_id=' . $manufacturer_id . (isset($this->request->get['category_id']) ? '&category_id=' . $this->request->get['category_id'] : '') . '&filter_series=' . $serie['sku']),
                    'selected' => (isset($this->request->get['filter_series']) && $this->request->get['filter_series'] == $serie['sku'] ? 1 : 0),
                );
            }

            //$this->data['sorts'][] = array(
            //	'text'  => $this->language->get('text_default'),
            //	'value' => 'p.sort_order-ASC',
            //	'href'  => $this->url->link('product/manufacturer/product', 'manufacturer_id=' . $this->request->get['manufacturer_id'] . '&sort=p.sort_order&order=ASC' . $url)
            // );
            //	$this->data['sorts'][] = array(
            //	'text'  => $this->language->get('text_name_asc'),
            //		'value' => 'pd.name-ASC',
            //		'href'  => $this->url->link('product/manufacturer/product', 'manufacturer_id=' . $this->request->get['manufacturer_id'] . '&sort=pd.name&order=ASC' . $url)
            //	); 
            // $this->data['sorts'][] = array(
            // 'text'  => 'Series',
            // 'value' => 'p.sku-ASC',
            // 'href'  => $this->url->link('product/manufacturer/product', 'manufacturer_id=' . $this->request->get['manufacturer_id'] . '&sort=p.sku' . $url)
            // );
            //	$this->data['sorts'][] = array(
            //		'text'  => $this->language->get('text_price_asc'),
            //		'value' => 'p.price-ASC',
            //		'href'  => $this->url->link('product/manufacturer/product', 'manufacturer_id=' . $this->request->get['manufacturer_id'] . '&sort=p.price&order=ASC' . $url)
            //	); 
            //	$this->data['sorts'][] = array(
            //		'text'  => $this->language->get('text_rating_asc'),
            //		'value' => 'rating-ASC',
            //		'href'  => $this->url->link('product/manufacturer/product', 'manufacturer_id=' . $this->request->get['manufacturer_id'] . '&sort=rating&order=ASC' . $url)
            //	);

            $this->data['sorts'][] = array(
                'text' => 'Model',
                'value' => 'p.upc-ASC',
                'href' => $this->url->link('product/manufacturer/product', 'manufacturer_id=' . $this->request->get['manufacturer_id'] . '&sort=p.upc' . $url)
            );


            $this->data['sorts'][] = array(
                'text' => 'Bore',
                'value' => 'p.isbn-ASC',
                'href' => $this->url->link('product/manufacturer/product', 'manufacturer_id=' . $this->request->get['manufacturer_id'] . '&sort=p.isbn' . $url)
            );

            $this->data['sorts'][] = array(
                'text' => 'Diameter',
                'value' => 'p.ean-ASC',
                'href' => $this->url->link('product/manufacturer/product', 'manufacturer_id=' . $this->request->get['manufacturer_id'] . '&sort=p.ean' . $url)
            );

            $this->data['sorts'][] = array(
                'text' => 'Cup depth',
                'value' => 'p.jan-ASC',
                'href' => $this->url->link('product/manufacturer/product', 'manufacturer_id=' . $this->request->get['manufacturer_id'] . '&sort=p.jan' . $url)
            );


            $url = '';

            if (isset($this->request->get['filter_series'])) {
                $url .= '&filter_series=' . $this->request->get['filter_series'];
            }


            if (isset($this->request->get['sort'])) {
                $url .= '&sort=' . $this->request->get['sort'];
            }

            if (isset($this->request->get['order'])) {
                $url .= '&order=' . $this->request->get['order'];
            }

            if (isset($this->request->get['category_id'])) {
                $url .= '&category_id=' . $this->request->get['category_id'];
            }

            $this->data['limits'] = array();

            /* 			$this->data['limits'][] = array(
              'text'  => $this->config->get('config_catalog_limit'),
              'value' => $this->config->get('config_catalog_limit'),
              'href'  => $this->url->link('product/manufacturer/product', (isset($this->request->get['category_id']) ? 'category_id='. $this->request->get['category_id'] .'&' : '' ) . 'manufacturer_id=' . $this->request->get['manufacturer_id'] . $url . '&limit=' . $this->config->get('config_catalog_limit'))
              ); */

            $this->data['limits'][] = array(
                'text' => 30,
                'value' => 30,
                'href' => $this->url->link('product/manufacturer/product', (isset($this->request->get['category_id']) ? 'category_id=' . $this->request->get['category_id'] . '&' : '' ) . 'manufacturer_id=' . $this->request->get['manufacturer_id'] . $url . '&limit=30')
            );

            $this->data['limits'][] = array(
                'text' => 60,
                'value' => 60,
                'href' => $this->url->link('product/manufacturer/product', (isset($this->request->get['category_id']) ? 'category_id=' . $this->request->get['category_id'] . '&' : '' ) . 'manufacturer_id=' . $this->request->get['manufacturer_id'] . $url . '&limit=60')
            );

            /* 			$this->data['limits'][] = array(
              'text'  => 75,
              'value' => 75,
              'href'  => $this->url->link('product/manufacturer/product', (isset($this->request->get['category_id']) ? 'category_id='. $this->request->get['category_id'] .'&' : '' ). 'manufacturer_id=' . $this->request->get['manufacturer_id'] . $url . '&limit=75')
              ); */

            $this->data['limits'][] = array(
                'text' => 90,
                'value' => 90,
                'href' => $this->url->link('product/manufacturer/product', (isset($this->request->get['category_id']) ? 'category_id=' . $this->request->get['category_id'] . '&' : '' ) . 'manufacturer_id=' . $this->request->get['manufacturer_id'] . $url . '&limit=90')
            );
            $this->data['limits'][] = array(
                'text' => 120,
                'value' => 120,
                'href' => $this->url->link('product/manufacturer/product', (isset($this->request->get['category_id']) ? 'category_id=' . $this->request->get['category_id'] . '&' : '' ) . 'manufacturer_id=' . $this->request->get['manufacturer_id'] . $url . '&limit=120')
            );


            $url = '';

            if (isset($this->request->get['sort'])) {
                $url .= '&sort=' . $this->request->get['sort'];
            }

            if (isset($this->request->get['order'])) {
                $url .= '&order=' . $this->request->get['order'];
            }

            if (isset($this->request->get['limit'])) {
                $url .= '&limit=' . $this->request->get['limit'];
            }

            if (isset($this->request->get['filter_series'])) {
                $url .= '&filter_series=' . $this->request->get['filter_series'];
            }

            if (isset($this->request->get['category_id'])) {
                $url .= '&category_id=' . $this->request->get['category_id'];
            }

            $pagination = new Pagination();
            $pagination->total = $product_total;
            $pagination->page = $page;
            $pagination->limit = $limit;
            $pagination->text = $this->language->get('text_pagination');
            $pagination->url = $this->url->link('product/manufacturer/product', 'manufacturer_id=' . $this->request->get['manufacturer_id'] . $url . '&page={page}');

            $this->data['pagination'] = $pagination->render();

            $this->data['sort'] = $sort;
            $this->data['order'] = $order;
            $this->data['limit'] = $limit;

            $this->data['continue'] = $this->url->link('common/home');

            if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/product/manufacturer_info.tpl')) {
                $this->template = $this->config->get('config_template') . '/template/product/manufacturer_info.tpl';
            } else {
                $this->template = 'default/template/product/manufacturer_info.tpl';
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
        } else {
            
            $url = '';

            if (isset($this->request->get['manufacturer_id'])) {
                $url .= '&manufacturer_id=' . $this->request->get['manufacturer_id'];
            }

            if (isset($this->request->get['category_id'])) {
                $url .= '&category_id=' . $this->request->get['category_id'];
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

            if (isset($this->request->get['filter_series'])) {
                $url .= '&filter_series=' . $this->request->get['filter_series'];
            }

            $this->data['breadcrumbs'][] = array(
                'text' => $this->language->get('text_error'),
                'href' => $this->url->link('product/category', $url),
                'separator' => $this->language->get('text_separator')
            );

            $this->document->setTitle($this->language->get('text_error'));

            $this->data['heading_title'] = $this->language->get('text_error');

            $this->data['text_error'] = $this->language->get('text_error');

            $this->data['button_continue'] = $this->language->get('button_continue');

            $this->data['continue'] = $this->url->link('common/home');

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

}

?>