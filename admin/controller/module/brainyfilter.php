<?php
/**
 * Brainy Filter Ultimate 4.7.2, December 3, 2015 / brainyfilter.com
 * Copyright 2014-2015 Giant Leap Lab / www.giantleaplab.com
 * License: Commercial. Reselling of this software or its derivatives is not allowed. You may use this software for one website ONLY including all its subdomains if the top level domain belongs to you and all subdomains are parts of the same OpenCart store.
 * Support: support@giantleaplab.com
 */
/**
 * @property ModelModuleBrainyFilter $model_module_brainyfilter
 */
class ControllerModuleBrainyFilter extends Controller {
	private $error = array(); 
    private $_data = array();
	
	const SUBMIT_TYPE_AUTO    = "auto";
	const SUBMIT_TYPE_DELAY   = "delay";
	const SUBMIT_TYPE_BUTTON  = "button";
	const SUBMIT_BUTTON_FLOAT = "float";
	const SUBMIT_BUTTON_FIXED = "fixed";
    
    private $default = array(
        'basic' => array(
            'behaviour' => array(
                'containerSelector' => '#content .row:nth-last-of-type(2)',
                'paginatorSelector' => '#content .row:nth-last-of-type(1)',
                'attribute_groups' => 1,
                'product_count' => 1,
                'hide_empty' => 0,
                'limit_height' => array(
                    'enabled' => 0,
                    'height' => 144,
                ),
                'limit_items' => array (
                    'enabled' => 0,
                    'number_to_show' => 4,
                    'number_to_hide' => 2,
                ),
                'sections' => array(
                    'search' => array(
                        'enabled' => 0,
                        'collapsed' => 0,
                    ),
                    'price' => array(
                        'enabled' => 1,
                        'collapsed' => 0,
                        'control' => 'slider',
                    ),
                    'category' => array(
                        'enabled' => 1,
                        'collapsed' => 0,
                        'control' => 'checkbox',
                    ),
                    'stock_status' => array(
                        'enabled' => 1,
                        'collapsed' => 0,
                    ),
                    'manufacturer' => array(
                        'enabled' => 1,
                        'collapsed' => 0,
                        'control' => 'checkbox',
                    ),
                    'attribute' => array(
                        'enabled' => 1,
                        'collapsed' => 0,
                    ),
                    'option' => array(
                        'enabled' => 0,
                        'collapsed' => 0,
                    ),
                    'filter' => array(
                        'enabled' => 0,
                        'collapsed' => 0,
                    ),
                    'rating' => array(
                        'enabled' => 0,
                        'collapsed' => 0,
                    ),
                ),
                'sort_order' => array(
                    'enabled'      => 0,
                    'search'       => 0,
                    'price'        => 1,
                    'category'     => 2,
                    'stock_status' => 3,
                    'manufacturer' => 4,
                    'attribute'    => 5,
                    'option'       => 6,
                    'filter'       => 7,
                    'rating'       => 8,
                ),
            ),
            'submission' => array(
                'submit_type'                   => 'button',
                'submit_button_type'            => 'float',
                'submit_delay_time'             => 1000,
                'hide_panel'                    => 1,
            ),
            'global' => array(
                'instock_status_id'             => 7,
                'subcategories_fix'             => 0,
                'multiple_attributes'           => 0,
                'attribute_separator'           => ',',
                'hide_out_of_stock'             => 0,
            ),
            'style' => array(
                'block_header_background'       => array('val' =>'f7f7f7'),
                'block_header_text'             => array('val' =>'000000'),
                'product_quantity_background'   => array('val' =>'F46234'),
                'product_quantity_text'         => array('val' =>'ffffff'),
                'price_slider_background'       => array('val' =>'eeeeee'),
                'price_slider_area_background'  => array('val' =>'f6a828'),
                'price_slider_border'           => array('val' =>'dddddd'),
                'price_slider_handle_background'=> array('val' =>'f6f6f6'),
                'price_slider_handle_border'    => array('val' =>'cccccc'),
                'group_block_header_background' => array('val' =>'CECBCB'),
                'group_block_header_text'       => array('val' =>'000000'),
                'resp_show_btn_color'           => array('val' =>'19A3DF'),
                'resp_reset_btn_color'          => array('val' =>'F53838'),
                
                'responsive' => array(
                    'enabled' => 0,
                    'collapsed' => 1,
                    'max_screen_width' => 768,
                    'max_width' => 300,
                    'position' => 'left',
                    'offset' => 80,
                ),
            ),
            'attributes' => array(),
            'options' => array(),
            'filters' => array(),
        ),
    );
    
    private $defaultLayoutSettings = array(
        'position'   => 'column_left',
        'sort_order' => '0',
        'status'     => '1',
    );
    
    public function install() {
        $this->load->model('module/brainyfilter');
        
        $this->model_module_brainyfilter->createAttributeValueTable();
        $this->model_module_brainyfilter->createBFilterTable();
        
        $this->model_module_brainyfilter->fillBFiltersTable();
        
        $this->model_module_brainyfilter->addDefaultLayout();
        
        $this->model_module_brainyfilter->addCustomIndexes();
    }
    
    public function uninstall() {
        $this->load->model('module/brainyfilter');
        
        $this->model_module_brainyfilter->dropAttributeValueTable();
        $this->model_module_brainyfilter->dropBFilterTable();
        
        $this->model_module_brainyfilter->removeDefaultLayout();
        
        $this->model_module_brainyfilter->removeCustomIndexes();
    }
    
    public function index()
    {
        $this->load->model('setting/setting');
        $this->load->model('module/brainyfilter');
	    $this->load->model('localisation/stock_status');
        $this->load->model('localisation/language');
        $this->load->model('design/layout');
        $this->load->model('catalog/category');
        $this->_setupLanguage();
        
        $isMijoShop = class_exists('MijoShop') && defined('JPATH_MIJOSHOP_OC');
        if ($isMijoShop) {
            MijoShop::get('base')->addHeader(JPATH_MIJOSHOP_OC . '/admin/view/javascript/brainyfilter.js', false);
            MijoShop::get('base')->addHeader(JPATH_MIJOSHOP_OC . '/admin/view/javascript/colorpicker/js/colorpicker.js', false);
        } else {
            $this->document->addScript('view/javascript/brainyfilter.js');
            $this->document->addScript('view/javascript/colorpicker/js/colorpicker.js');
        }
        
        if (isset($this->request->post['bf'])) {
            $post = $this->_parsePostData();
            if ($this->_validate($post)) {
                $this->_saveSettings($post);
                	
                if ($this->request->post['action'] == 'apply') {
                    $this->redirect($this->url->link('module/brainyfilter', 'token=' . $this->session->data['token'], 'SSL'));
                } else {
                    $this->redirect($this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'));
                }
            }
        }

	$this->document->setTitle($this->language->get('heading_title'));
	$this->document->addStyle('view/stylesheet/brainyfilter.css');
        $this->document->addStyle('view/javascript/colorpicker/css/colorpicker.css');
        $this->_data['heading_title'] = $this->language->get('heading_title');
        
        $this->_data['breadcrumbs'] = array();

   		$this->_data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => false
   		);

   		$this->_data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_module'),
			'href'      => $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);
		
   		$this->_data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('module/brainyfilter', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);
        
        if (isset($this->session->data['success'])) {
			$this->_data['success'] = $this->session->data['success'];
		
			unset($this->session->data['success']);
		} else {
			$this->_data['success'] = '';
		}
        
        
        $this->_data['error_warning'] = $this->error;
        
        $this->_data['cancel'] = $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL');
        $this->_data['action'] = $this->url->link('module/brainyfilter', 'token=' . $this->session->data['token'], 'SSL');
        $this->_data['refreshAction'] = $this->url->link('module/brainyfilter/refresh', 'token=' . $this->session->data['token'], 'SSL');
        $this->_data['attributeValuesAction'] = $this->url->link('module/brainyfilter/attributeValues', 'token=' . $this->session->data['token'], 'SSL');
        $this->_data['modRefreshAction'] = $this->url->link('module/brainyfilter/modRefreshTrigger', 'token=' . $this->session->data['token'], 'SSL');
        
	    $this->_data['stockStatuses'] = $this->model_localisation_stock_status->getStockStatuses();

        $this->_data['attrGroups'] = $this->model_module_brainyfilter->getAttributes();
        
        $this->_data['filters'] = $this->model_module_brainyfilter->getFilters();
        
	    $this->_data['options'] = $this->model_module_brainyfilter->getOptions();
        
        $this->_data['categories'] = $this->model_catalog_category->getCategories(array('start' => 0, 'limit' => 1000));
        $this->_data['category_layouts'] = $this->model_module_brainyfilter->detectCategoryLayouts();
        
        $this->_data['defaultLayout'] = $this->defaultLayoutSettings;
        $this->_data['defaultLayout']['layout_id'] = $this->model_module_brainyfilter->getDefaultLayout();

        $this->_data['possible_controls'] = array(
            'price' => array(
                'slider' => $this->_data['entry_slider'],
                'slider_lbl' => $this->_data['entry_slider_labels_only'],
                'slider_lbl_inp' => $this->_data['entry_slider_labels_and_inputs']
            ),
            'manufacturer' => array(
                'checkbox' => $this->_data['entry_checkbox'],
                'radio' => $this->_data['entry_radio'],
                'select' => $this->_data['entry_selectbox'],
            ),
            'category' => array(
                'checkbox' => $this->_data['entry_checkbox'],
                'radio' => $this->_data['entry_radio'],
                'select' => $this->_data['entry_selectbox'],
            ),
        );

	    $layoutsArr = $this->model_design_layout->getLayouts();
        $layouts = array();
        foreach ($layoutsArr as $l) {
            $layouts[$l['layout_id']] = $l['name'];
        }
        $this->_data['layouts'] = $layouts;
        
        $settings = $this->_applySettings();
        $this->_data['settings'] = $settings;
        $this->_data['basic_settings'] = array();
        $this->_data['isFirstLaunch']  = !$this->config->get('brainyfilter_layout_basic') ? 'true' : 'false';

        foreach ($settings['basic']['behaviour']['sections'] as $section => $set) {
            $item = array(
                'label'     => $this->language->get("entry_filter_{$section}"),
                'name'      => $section,
            );
            if (isset($set['control'])) {
                $item['control'] = $set['control'];
            }
            $this->_data['filterBlocks'][] = $item;
        }

        $this->_data['layoutsCount'] = count($settings) - 1;
        
        $this->_data['languages'] = $this->model_localisation_language->getLanguages();

        $this->template = 'module/brainyfilter.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);
				
        $this->data = $this->_data;
		$this->response->setOutput($this->render());
    }

    public function refresh()
    {
        $this->load->model('module/brainyfilter');
        $this->model_module_brainyfilter->createAttributeValueTableBackup();
        $this->model_module_brainyfilter->createAttributeValueTable();
        $this->model_module_brainyfilter->createBFilterTable();
        $this->model_module_brainyfilter->fillBFiltersTable();
        $this->model_module_brainyfilter->recoverAttributeValueSortOrder();
        
        die('done');
    }
    
    public function attributeValues()
    {
        $this->load->model('module/brainyfilter');

        // returns atribute values
        if (isset($this->request->get['attr_id'])) {
            $values = $this->model_module_brainyfilter->getAttributeValues($this->request->get['attr_id']);
            die(json_encode($values) );
        }
        
        // modifies sort order
        if (isset($this->request->post['sort_order'])) {
            $this->model_module_brainyfilter->changeAttrValuesSortOrder($this->request->post['sort_order']);
            die('done');
        }

    }
    
    private function _parsePostData() {
        if(get_magic_quotes_gpc()){
            $this->request->post['bf'] = stripslashes($this->request->post['bf']);
        }
        $json = str_replace('&quot;', '"', $this->request->post['bf']);
        $data = json_decode($json, true);
        return $data;
    }
    
	/**
	 * Data validation
	 * The method validates the given POST data
	 * @todo Implement the method
	 * @return boolean
	 */
    private function _validate($data) {
        if (!$this->user->hasPermission('modify', 'module/brainyfilter')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
        
        if (!$this->error) {
			return true;
		} else {
			return false;
		}	
    }
    
    private function _saveSettings($data) 
    {
        // regeneration of cache tables if attribute mode was switched
        $conf = $this->config->get('bf_layout_basic');
        $multiMode = $data['basic']['global']['multiple_attributes'];
        $isFirstEnabling = !$conf && $multiMode;
        $modeChanged = $conf && $multiMode != $conf['global']['multiple_attributes'];
        if ($modeChanged || $isFirstEnabling) {
            if ($multiMode) {
                $separator = $data['basic']['global']['attribute_separator'];
                $this->model_module_brainyfilter->attrSeparator = $separator;
            } else {
                $this->model_module_brainyfilter->attrSeparator = false;
            }
            $this->model_module_brainyfilter->createAttributeValueTableBackup();
            $this->model_module_brainyfilter->updateProductAttributes();
            $this->model_module_brainyfilter->recoverAttributeValueSortOrder();
        }

        // prepearing module settings
        $modules = array();
        $settings = array();
        
        $data = $this->_removeDefaultSettings($data);
        
        foreach ($data as $id => $set) {
            if ($id !== 'basic') {
                $modules[] = array(
                    'layout_id'  => $set['layout_id'],
                    'position'   => $set['layout_position'],
                    'sort_order' => $set['layout_sort_order'],
                    'status'     => $set['layout_enabled'],
                    'bf_layout_id' => $id,
                );
            }
            if (empty($set['behaviour']['containerSelector'])) {
                unset($set['behaviour']['containerSelector']);
            }
            if (empty($set['behaviour']['paginatorSelector'])) {
                unset($set['behaviour']['paginatorSelector']);
            }
            $settings['bf_layout_' . $id] = $set;
        }
        $settings['brainyfilter_module'] = $modules;
        
        $this->model_setting_setting->editSetting('brainyfilter', $settings);
        
        $this->session->data['success'] = $this->language->get('text_success');
    }
	
    private function _removeDefaultSettings($arr)
    {
        if (is_array($arr)) {
            if (isset($arr['enabled']) && $arr['enabled'] === 'default') {
                $arr = null;
            } else {
                foreach ($arr as $k => $val) {
                    $arr[$k] = $this->_removeDefaultSettings($arr[$k]);
                    if (is_null($arr[$k])) {
                        unset($arr[$k]);
                    }
                }
            }
        } elseif ($arr === 'default') {
            $arr = null;
        } 
        if(is_array($arr) && isset($arr['default']) && $arr['default'] == 1){
            if (isset($arr['val'])) {
               unset($arr['val']);
            }  
        }
        return $arr;
    }

    private function _applyBasicLanguageValues(&$arr)
    {

        $this->load->model('localisation/language');
        $languages = $this->model_localisation_language->getLanguages();
        foreach ($languages as  $value) {
            if (!isset($arr['basic']['behaviour']['filter_name'][$value['language_id']])) {
               $arr['basic']['behaviour']['filter_name'][$value['language_id']] = 'Brainy Filter';
            }
        }        
    }
    
    private function _applyBasicSettings($arr, $basic)
    {
        foreach ($basic['behaviour']['sections'] as $section => $set) {
            if (!isset($arr['behaviour']['sections'][$section])) {
                $set['enabled'] = 'default';
                $arr['behaviour']['sections'][$section] = $set;
            }
        }
        if (!isset($arr['behaviour']['limit_height'])) {
            $arr['behaviour']['limit_height'] = $basic['behaviour']['limit_height'];
            $arr['behaviour']['limit_height']['enabled'] = 'default';
        }
        if (!isset($arr['behaviour']['limit_items'])) {
            $arr['behaviour']['limit_items'] = $basic['behaviour']['limit_items'];
            $arr['behaviour']['limit_items']['enabled'] = 'default';
        }
        if (!isset($arr['behaviour']['sort_order'])) {
            $arr['behaviour']['sort_order'] = $basic['behaviour']['sort_order'];
            $arr['behaviour']['sort_order']['enabled'] = 'default';
        }
        if (!isset($arr['submission']['submit_type'])) {
            $arr['submission']['submit_type'] = 'default';
            $arr['submission']['submit_button_type'] = $basic['submission']['submit_button_type'];
            $arr['submission']['submit_delay_time'] = $basic['submission']['submit_delay_time'];
        }
        if (!isset($arr['style']['responsive'])) {
            $arr['style']['responsive'] = $basic['style']['responsive'];
            $arr['style']['responsive']['enabled'] = 'default';
            $arr['style']['responsive']['collapsed'] = 'default';
        }
        
        return $arr;
    }
    
	/**
	 * Apply Settings
	 *
	 * @param array $data Associative array of settings which should be changed
	 * @return void 
	 */
    private function _applySettings() 
    {
        $this->default = $this->_adjustSettingsForTheme($this->default);
        
        if (isset($this->request->post['bf']) && is_array($this->request->post['bf'])) {
            return self::_arrayReplaceRecursive($this->default, $this->request->post['bf']);
        } else {
            $this->_applyBasicLanguageValues($this->default);
            $settings = array();
            if ($this->config->get('bf_layout_basic')) {
                $settings['basic'] = $this->config->get('bf_layout_basic');
            }
            $i = 0;
            while ($set = $this->config->get('bf_layout_' . $i)) {
                $settings[$i] = $this->_applyBasicSettings($set, $settings['basic']);
                $i ++;
            }
            if (!empty($settings)) {
                return self::_arrayReplaceRecursive($this->default, $settings);
            }
        }
        return $this->default;
    }

	/**
	 * Set Up Language variables
	 * 
	 * @return void
	 */
	private function _setupLanguage()
	{
        $lang = $this->load->language('module/brainyfilter');

        if (count($lang)) {
            foreach ($lang as $var => $val) {
                $this->_data[$var] = $val;
            }
        }
        // language variables from other files
		$this->_data['text_yes'] = $this->language->get('text_yes');
		$this->_data['text_no']  = $this->language->get('text_no');
	}
    
    /**
     * An alternative of PHP native function array_replace_recursive(), which is designed
     * to bring similar functionality for PHP versions lower then 5.3. <br>
     * <b>Note</b>: unlike PHP native function the method holds only two arrays as parameters.
     * @param array $array An original array
     * @param array $array1 Replacement
     * @return array
     */
    private static function _arrayReplaceRecursive($array, $array1)
    {
        foreach ($array1 as $key => $value) {
            if (!isset($array[$key]) || (isset($array[$key]) && !is_array($array[$key]))) {
                $array[$key] = array();
            }

            if (is_array($value)) {
                $value = self::_arrayReplaceRecursive($array[$key], $value);
            }
            $array[$key] = $value;
        }
        return $array;
    }
    
    public function ocmodManager()
    {
        $this->load->model('module/brainyfilter');
        $this->load->model('extension/modification');
        
        if (isset($this->request->post['bf'])) {
            $data = array(
                'status' => (int)$this->request->post['bf']['enabled'],
                'xml' => html_entity_decode($this->request->post['bf']['xml'])
            );
            $this->model_module_brainyfilter->updateMod($data);
            $this->response->redirect($this->url->link('module/brainyfilter/ocmodManager', 'token=' . $this->session->data['token'], 'SSL'));
        }
        
        $this->_setupLanguage();
		$this->document->setTitle($this->language->get('heading_title'));
        
        $isMijoShop = class_exists('MijoShop') && defined('JPATH_MIJOSHOP_OC');
		if ($isMijoShop) {
			$this->document->addStyle('admin/view/stylesheet/brainyfilter.css');
		} else {
			$this->document->addStyle('view/stylesheet/brainyfilter.css');
		}
        $this->document->addStyle('view/javascript/colorpicker/css/colorpicker.css');
        $this->_data['heading_title'] = $this->language->get('heading_title');
        
        if (isset($this->session->data['success'])) {
			$this->_data['success'] = $this->session->data['success'];
		
			unset($this->session->data['success']);
		} else {
			$this->_data['success'] = '';
		}
        
        $this->_data['header'] = $this->load->controller('common/header');
		$this->_data['column_left'] = $this->load->controller('common/column_left');
		$this->_data['footer'] = $this->load->controller('common/footer');
        $this->_data['modRefreshAction'] = $this->url->link('module/brainyfilter/modRefreshTrigger', 'token=' . $this->session->data['token'], 'SSL');
        
        $mod = $this->model_extension_modification->getModificationByCode('brainyfilter');
        $this->_data['enabled'] = (bool)$mod['status'];
        $this->_data['xml'] = htmlentities($mod['xml']);
				
		$this->response->setOutput($this->load->view('module/brainyfilter_ocmod.tpl', $this->_data));
    }

    /**
     * enable/disable OCMOD modification
     * The method triggers the refresh function (extension/modification/refresh)
     * 
     */
    public function modRefreshTrigger()
    {
        $this->load->model('module/brainyfilter');
        if (isset($this->request->post['enable'])) {
            $enable = $this->request->post['enable'] === 'true';
        } else {
            $enable = true;
        }
        if ($this->model_module_brainyfilter->enableMod($enable)) {
            $this->response->redirect($this->url->link('extension/modification/refresh', 'token=' . $this->session->data['token'], 'SSL'));
        }
    }
    
    protected function _adjustSettingsForTheme($settings) {
        $theme = $this->config->get('config_template');
        
        if ( preg_match('/^default/i', $theme) ) {
            $set = array(
                'containerSelector' => '#content .row:nth-last-of-type(2)',
                'paginatorSelector' => '#content .row:nth-last-of-type(1)',
            );
        } elseif ( preg_match('/^journal/i', $theme) ) {
            $set = array(
                'containerSelector' => '.main-products.product-list, .main-products.product-grid',
                'paginatorSelector' => '.pagination',
            );
        } elseif (preg_match('/^shoppica/i', $theme)) {
            $set = array(
                'containerSelector' => '#listing_options + .clear + .s_listing',
                'paginatorSelector' => '.pagination',
            );
        } elseif (preg_match('/^sellegance/i', $theme)) {
            $set = array(
                'containerSelector' => '.product-filter + .row',
                'paginatorSelector' => '.pagination',
            );
        } elseif (preg_match('/^pavilion/i', $theme)) {
            $set = array(
                'containerSelector' => '.tb_products',
                'paginatorSelector' => '.pagination',
            );
        } else {
            /* Most spread set of selectors. 
             * Themes which have such selectors:
             * oxy, aquacart, bigshop, sellya, beautyshop, pav_fashion, megashop
             */
            $set = array(
                'containerSelector' => '.product-list, .product-grid',
                'paginatorSelector' => '.pagination, .paging',
            );
        }
        
        $settings['basic']['behaviour'] = array_merge($settings['basic']['behaviour'], $set);
        
        return $settings;
    }
}