<?php

class ModelCatalogIsearch extends Model {

    public $cache_cat_id = '';

    public function updateViewed($product_id) {
        $this->db->query("UPDATE " . DB_PREFIX . "product SET viewed = (viewed + 1) WHERE product_id = '" . (int) $product_id . "'");
    }

    public function iSearch($keywords = '', $searchIn, $useImages = true, $strictSearch = false, $singularize = false, $spellCheck = array(), $sort = '', $order = 'ASC', $category_id = 0, $sub_category = false, $excludeTerms, $excludeRules/* {HOOK_SET_SEARCH_IN_CACHE} */) {

        if (function_exists('mb_internal_encoding'))
            mb_internal_encoding("UTF-8");
        foreach ($searchIn as $k => $s) {
            if (empty($s))
                $searchIn[$k] = false;
        }
        $this->fixParameters($sort, $order, $category_id, $sub_category);
        $keywords = $this->convertKeywords($keywords, $singularize, $spellCheck, $excludeTerms, $strictSearch);
        $customer_price = $this->config->get('config_customer_price');
        $attributes = compact('keywords', 'searchIn', 'useImages', 'strictSearch', 'singularize', 'spellCheck', 'customer_price', 'sort', 'order', 'category_id', 'sub_category', 'excludeTerms', 'excludeRules'/* {HOOK_ISEARCH_CACHE_VARIABLE} */);

        if ($this->customer->isLogged()) {
            $customer_group_id = $this->customer->getCustomerGroupId();
        } else {
            $customer_group_id = $this->config->get('config_customer_group_id');
        }

        $attributes['customer_group_id'] = $customer_group_id;
        $cache = md5(http_build_query($attributes));
        $products = $this->cache->get('product.' . (int) $this->config->get('config_language_id') . '.' . (int) $this->config->get('config_store_id') . '.' . (int) $customer_group_id . '.' . $cache);

        if (!$products || $this->cache_cat_id != $category_id) {

            $this->cache_cat_id = $category_id;
            $keywords = trim($this->isearch_strtolower($keywords));

            $products = array();
            if ($this->isearch_strlen($keywords) >= 2) {

                $words = explode(' ', $keywords);

                if ($strictSearch === true) {
                    $words = array($keywords);
                }

                //{HOOK_CHECK_SEARCH_IN_CACHE}

                $cond = '';

                // $cond .= 'p2c.category_id = '.$category_id;
                foreach ($words as $word) {
                    if ($searchIn['name'] === true) {
                        $cond .= ' AND (LOWER(pd.name) LIKE "%' . $this->db->escape($word) . '%"';
                    } else {
                        $cond .= ' AND (1=0';
                    }
                    if ($searchIn['model'] === true) {
                        $cond .= ' OR LOWER(p.model) LIKE "%' . $this->db->escape($word) . '%"';
                    }
                    if ($searchIn['sku'] === true) {
                        $cond .= ' OR LOWER(p.sku) LIKE "' . $this->db->escape($word) . '%"';
                    }
                    if ($searchIn['upc'] === true) {
                        $cond .= ' OR LOWER(p.upc) LIKE "' . $this->db->escape($word) . '%"';
                    }
                    if ($searchIn['ean'] === true) {
                        $cond .= ' OR LOWER(p.ean) LIKE "' . $this->db->escape($word) . '%"';
                    }
                    if ($searchIn['jan'] === true) {
                        $cond .= ' OR LOWER(p.jan) LIKE "' . $this->db->escape($word) . '%"';
                    }
                    if ($searchIn['isbn'] === true) {
                        $cond .= ' OR LOWER(p.isbn) LIKE "' . $this->db->escape($word) . '%"';
                    }
                    if ($searchIn['mpn'] === true) {
                        $cond .= ' OR LOWER(p.mpn) LIKE "' . $this->db->escape($word) . '%"';
                    }
                    if ($searchIn['location'] === true) {
                        $cond .= ' OR LOWER(p.location) LIKE "%' . $this->db->escape($word) . '%"';
                    }
                    if ($searchIn['tags'] === true) {
                        $cond .= ' OR LOWER(ptag.tag) LIKE "%' . $this->db->escape($word) . '%"';
                    }
                    if ($searchIn['manufacturer'] === true) {
                        $cond .= ' OR LOWER(manu.name) LIKE "%' . $this->db->escape($word) . '%"';
                    }
                    if ($searchIn['attributes'] === true) {
                        $cond .= ' OR LOWER(ad.name) LIKE "%' . $this->db->escape($word) . '%"';
                    }
                    if ($searchIn['attributes_values'] === true) {
                        $cond .= ' OR LOWER(pa.text) LIKE "%' . $this->db->escape($word) . '%"';
                    }
                    if ($searchIn['categories'] === true) {
                        $cond .= ' OR LOWER(cd.name) LIKE "%' . $this->db->escape($word) . '%"';
                    }
                    if ($searchIn['filters'] === true) {
                        $cond .= ' OR LOWER(fd.name) LIKE "%' . $this->db->escape($word) . '%"';
                    }
                    if ($searchIn['description'] === true) {
                        $cond .= ' OR LOWER(pd.description) LIKE "%' . $this->db->escape($word) . '%"';
                    }
                    if ($searchIn['optionname'] === true) {
                        $cond .= ' OR LOWER(ode.name) LIKE "%' . $this->db->escape($word) . '%"';
                    }
                    if ($searchIn['optionvalue'] === true) {
                        $cond .= ' OR LOWER(ovde.name) LIKE "%' . $this->db->escape($word) . '%"';
                    }
                    if ($searchIn['metadescription'] === true) {
                        $cond .= ' OR LOWER(pd.meta_description) LIKE "%' . $this->db->escape($word) . '%"';
                    }
                    if ($searchIn['metakeyword'] === true) {
                        $cond .= ' OR LOWER(pd.meta_keyword) LIKE "%' . $this->db->escape($word) . '%"';
                    }
                    $cond .= ')';
                }
                $cond = $this->isearch_substr($cond, 4);

                $extra_sql = '';
                $extra_select = '';

                if ($searchIn['manufacturer'] === true) {
                    $extra_select .= 'manu.name AS manufacturer, ';
                    $extra_sql .= 'LEFT JOIN ' . DB_PREFIX . 'manufacturer AS manu ON manu.manufacturer_id = p.manufacturer_id ';
                }

                if ($searchIn['tags'] === true) {
                    $extra_select .= 'ptag.tag AS tag, ';
                    $extra_sql .= 'LEFT JOIN ' . DB_PREFIX . 'product_tag AS ptag ON (ptag.product_id = pd.product_id AND pd.language_id = ptag.language_id) ';
                }

                //$needs_category_join = false;
                $needs_category_join = true;

                if (!empty($excludeRules)) {
                    $cond_exclude = array();

                    foreach ($excludeRules as $rule) {
                        $operator = '';
                        switch ($rule['operator']) {
                            case 'lt' : $operator = '<';
                                break;
                            case 'gt' : $operator = '>';
                                break;
                            case 'eq' : $operator = '=';
                                break;
                        }

                        switch ($rule['type']) {
                            case 'category_status' : {
                                    $cond_exclude[] = 'cat.status ' . $operator . ' ' . trim($rule['value']);
                                } break;
                            case 'status' : {
                                    $cond_exclude[] = 'p.status ' . $operator . ' ' . trim($rule['value']);
                                } break;
                            case 'quantity' : {
                                    $cond_exclude[] = 'p.quantity ' . $operator . ' ' . trim($rule['value']);
                                } break;
                        }
                        if ($rule['type'] == 'category_status') {
                            $needs_category_join = true;
                        }
                    }
                } else {
                    $cond_exclude = array('p.status = 0');
                }

                if ($searchIn['categories'] === true || $needs_category_join) {

                    $extra_select .= 'cd.name AS category, ';
                    $extra_sql .= 'LEFT JOIN ' . DB_PREFIX . 'product_to_category AS p2c ON p2c.product_id = pd.product_id ';
                    if ($category_id == 0) {
                        $extra_sql .= 'LEFT JOIN ' . DB_PREFIX . 'category_description AS cd ON (cd.category_id = p2c.category_id AND pd.language_id = cd.language_id) ';
                    } else {
                        $extra_sql .= 'LEFT JOIN ' . DB_PREFIX . 'category_description AS cd ON (cd.category_id = p2c.category_id AND cd.category_id = ' . (int) $category_id . ' AND pd.language_id = cd.language_id) ';
                        $cond .= 'AND p2c.category_id = ' . $category_id;
                    }
                }

                if ($needs_category_join) {

                    if ($category_id == 0) {
                        $extra_sql .= 'LEFT JOIN ' . DB_PREFIX . 'category AS cat ON (cat.category_id = p2c.category_id AND p2c.product_id = pd.product_id) ';
                    } else {
                        $extra_sql .= 'LEFT JOIN ' . DB_PREFIX . 'category AS cat ON (cat.category_id = p2c.category_id AND cat.category_id = ' . (int) $category_id . ' AND p2c.product_id = pd.product_id) ';
                    }
                }

                if ($searchIn['filters'] === true) {
                    $extra_select .= 'fd.name AS filter, ';
                    $extra_sql .= 'LEFT JOIN ' . DB_PREFIX . 'product_filter AS p2f ON p2f.product_id = pd.product_id ';
                    $extra_sql .= 'LEFT JOIN ' . DB_PREFIX . 'filter_description AS fd ON (fd.filter_id = p2f.filter_id AND pd.language_id = fd.language_id) ';
                }

                if ($searchIn['attributes'] === true || $searchIn['attributes_values'] === true) {
                    $extra_select .= 'ad.name AS attribute, ';
                    $extra_sql .= 'LEFT JOIN ' . DB_PREFIX . 'product_attribute AS pa ON pa.product_id = pd.product_id ';
                    $extra_sql .= 'LEFT JOIN ' . DB_PREFIX . 'attribute_description AS ad ON (ad.attribute_id = pa.attribute_id AND pd.language_id = ad.language_id) ';
                }

                if ($searchIn['location'] === true) {
                    $extra_select .= 'p.location, ';
                }

                if ($searchIn['model'] === true) {
                    $extra_select .= '';
                }

                if ($searchIn['name'] === true) {
                    $extra_select .= '';
                }

                if ($searchIn['optionname'] === true) {
                    $extra_select .= 'ode.name AS optionname, ';
                    $extra_sql .= 'LEFT JOIN ' . DB_PREFIX . 'product_option AS popt ON popt.product_id = pd.product_id ';
                    $extra_sql .= 'LEFT JOIN ' . DB_PREFIX . 'option_description AS ode ON (ode.option_id = popt.option_id AND pd.language_id = ode.language_id)';
                }

                if ($searchIn['optionvalue'] === true) {
                    $extra_select .= 'ovde.name AS optionvalue, ';
                    if ($searchIn['optionname'] !== true) {
                        $extra_sql .= 'LEFT JOIN ' . DB_PREFIX . 'product_option AS popt ON popt.product_id = pd.product_id ';
                    }
                    $extra_sql .= 'LEFT JOIN ' . DB_PREFIX . 'option_value_description AS ovde ON (ovde.option_id = popt.option_id AND pd.language_id = ovde.language_id)';
                }

                if ($useImages === true) {
                    $extra_select .= 'p.image, ';
                }

                if ($keywords == 'no-ajax-mode') {
                    $cond = '1=1';
                    $sql_limit = ' LIMIT 1000';
                } else {
                    $sql_limit = ' LIMIT 20';
                }

                if (defined('VERSION') && VERSION >= '1.5.4') {
                    if ($searchIn['tags'] === true) {
                        $extra_sql = str_replace('LEFT JOIN ' . DB_PREFIX . 'product_tag AS ptag ON (ptag.product_id = pd.product_id AND pd.language_id = ptag.language_id) ', '', $extra_sql);
                        $extra_select = str_replace('ptag.tag AS tag, ', 'pd.tag AS tag, ', $extra_select);
                        $cond = str_replace('ptag.tag', 'pd.tag', $cond);
                    }
                }

                $sql = 'SELECT pd.product_id, p.model, pd.name,' . $extra_select . ' p.price, p.tax_class_id, pspe.price AS special FROM ' . DB_PREFIX . 'product_description AS pd ';
                $sql .= 'LEFT JOIN ' . DB_PREFIX . 'product AS p ON p.product_id = pd.product_id ';
                $sql .= 'LEFT JOIN ' . DB_PREFIX . 'product_to_store AS ptostore ON ptostore.product_id = pd.product_id ';
                $sql .= 'LEFT JOIN ' . DB_PREFIX . 'product_special AS pspe ON (pspe.product_id = pd.product_id AND pspe.customer_group_id = "' . $customer_group_id . '" AND (pspe.date_start = "0000-00-00" OR pspe.date_start < NOW()) AND (pspe.date_end = "0000-00-00" OR pspe.date_end > NOW()) AND pspe.priority = (SELECT priority FROM ' . DB_PREFIX . 'product_special WHERE product_id = pd.product_id AND customer_group_id = "' . $customer_group_id . '" AND (date_start = "0000-00-00" OR date_start < NOW()) AND (date_end = "0000-00-00" OR date_end > NOW()) ORDER BY priority ASC, price ASC LIMIT 0,1) ) ';
                $sql .= $extra_sql;

                $sql .= 'WHERE ' . $cond . ' AND !(' . implode(' OR ', $cond_exclude) . ') ';
                $sql .= 'AND pd.language_id = ' . (int) $this->config->get('config_language_id');
                $sql .= ' AND ptostore.store_id =  ' . (int) $this->config->get('config_store_id');

                //if (($searchIn['categories'] === true || $searchIn['description'] === true ) && $searchIn['name'] === true)
                $sql .= ' GROUP BY pd.product_id';

                $sql .= ' ORDER BY Length(pd.name), pd.name ASC';

                $sql .= $sql_limit;
                //echo $sql;
                //exit;
                //{HOOK_SEARCH_IN_CACHE_ISEARCH}

                $result = $this->db->query($sql);

                //{HOOK_SEARCH_AGAIN_ISEARCH}

                if ($result) {
                    $products = (isset($result->rows)) ? $result->rows : $result->row;

                    if ($useImages === true) {
                        $this->load->model('tool/image');
                    }

                    foreach ($products as $k => $result) {
                        $image = (!empty($result['image'])) ? $result['image'] : '';

                        if ($useImages === true) {
                            $config = $this->config->get('iSearch');
                            $image = (!empty($result['image'])) ? $this->model_tool_image->resize($result['image'], empty($config['InstantResultsImageWidth']) ? 80 : $config['InstantResultsImageWidth'], empty($config['InstantResultsImageHeight']) ? 80 : $config['InstantResultsImageHeight']) : $this->model_tool_image->resize('no_image.jpg', empty($config['InstantResultsImageWidth']) ? 80 : $config['InstantResultsImageWidth'], empty($config['InstantResultsImageHeight']) ? 80 : $config['InstantResultsImageHeight']);
                        } else {
                            $image = false;
                        }


                        $special = ((float) $result['special']) ? $this->currency->format($this->tax->calculate($result['special'], $result['tax_class_id'], $this->config->get('config_tax'))) : false;

                        $model = (!empty($result['model'])) ? $result['model'] : '';

                        $price = $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax')));

                        $products[$k] = array(
                            'name' => htmlspecialchars_decode($result['name'], ENT_QUOTES),
                            'model' => $model,
                            'image' => $image,
                            'price' => (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) ? $price : '',
                            'special' => (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) ? $special : '',
                            'href' => htmlspecialchars_decode($this->url->link('product/product', 'product_id=' . $result['product_id'] . '&' . (defined('VERSION') && VERSION >= '1.5.5' ? 'search' : 'filter_name') . '=' . urlencode($keywords), '', 'SSL'), ENT_QUOTES)
                        );
                    }
                }
            }

            $products = $this->sortProducts($products, $keywords, $searchIn);

            $this->cache->set('product.' . (int) $this->config->get('config_language_id') . '.' . (int) $this->config->get('config_store_id') . '.' . (int) $customer_group_id . '.' . $cache, $products);
        }

        if (empty($products[0]))
            $products = array();
        return $products;
    }

    public function iSearchStandard($keywords = '', $searchIn, $strictSearch = false, $singularize = false, $spellCheck = array(), $sort = '', $order = 'ASC', $category_id = 0, $sub_category = false, $excludeTerms, $excludeRules/* {HOOK_SET_SEARCH_IN_CACHE} */) {

        if (function_exists('mb_internal_encoding'))
            mb_internal_encoding("UTF-8");
        foreach ($searchIn as $k => $s) {
            if (empty($s))
                $searchIn[$k] = false;
        }
        $this->fixParameters($sort, $order, $category_id, $sub_category);
        $keywords = $this->convertKeywords($keywords, $singularize, $spellCheck, $excludeTerms, $strictSearch);
        $customer_price = $this->config->get('config_customer_price');
        $attributes = compact('keywords', 'searchIn', 'strictSearch', 'singularize', 'spellCheck', 'customer_price', 'sort', 'order', 'category_id', 'sub_category', 'excludeTerms', 'excludeRules'/* {HOOK_ISEARCH_CACHE_VARIABLE} */);

        if ($this->customer->isLogged()) {
            $customer_group_id = $this->customer->getCustomerGroupId();
        } else {
            $customer_group_id = $this->config->get('config_customer_group_id');
        }
        $attributes['customer_group_id'] = $customer_group_id;
        $cache = md5(http_build_query($attributes));
        $products = $this->cache->get('productstandard.' . (int) $this->config->get('config_language_id') . '.' . (int) $this->config->get('config_store_id') . '.' . (int) $customer_group_id . '.' . $cache);

        if (!$products) {

            $keywords = trim($this->isearch_strtolower($keywords));

            $products = array();
            if ($this->isearch_strlen($keywords) >= 2) {
                $words = explode(' ', $keywords);

                if ($strictSearch === true) {
                    $words = array($keywords);
                }

                //{HOOK_CHECK_SEARCH_IN_CACHE}

                $cond = '';
                foreach ($words as $word) {
                    if ($searchIn['name'] === true) {
                        $cond .= ' AND (LOWER(pd.name) LIKE "%' . $this->db->escape($word) . '%"';
                    } else {
                        $cond .= ' AND (1=0';
                    }
                    if ($searchIn['model'] === true) {
                        $cond .= ' OR LOWER(p.model) LIKE "%' . $this->db->escape($word) . '%"';
                    }
                    if ($searchIn['sku'] === true) {
                        $cond .= ' OR LOWER(p.sku) LIKE "' . $this->db->escape($word) . '%"';
                    }
                    if ($searchIn['upc'] === true) {
                        $cond .= ' OR LOWER(p.upc) LIKE "' . $this->db->escape($word) . '%"';
                    }
                    if ($searchIn['ean'] === true) {
                        $cond .= ' OR LOWER(p.ean) LIKE "' . $this->db->escape($word) . '%"';
                    }
                    if ($searchIn['jan'] === true) {
                        $cond .= ' OR LOWER(p.jan) LIKE "' . $this->db->escape($word) . '%"';
                    }
                    if ($searchIn['isbn'] === true) {
                        $cond .= ' OR LOWER(p.isbn) LIKE "' . $this->db->escape($word) . '%"';
                    }
                    if ($searchIn['mpn'] === true) {
                        $cond .= ' OR LOWER(p.mpn) LIKE "' . $this->db->escape($word) . '%"';
                    }
                    if ($searchIn['location'] === true) {
                        $cond .= ' OR LOWER(p.location) LIKE "%' . $this->db->escape($word) . '%"';
                    }
                    if ($searchIn['tags'] === true) {
                        $cond .= ' OR LOWER(ptag.tag) LIKE "%' . $this->db->escape($word) . '%"';
                    }
                    if ($searchIn['manufacturer'] === true) {
                        $cond .= ' OR LOWER(manu.name) LIKE "%' . $this->db->escape($word) . '%"';
                    }
                    if ($searchIn['attributes'] === true) {
                        $cond .= ' OR LOWER(ad.name) LIKE "%' . $this->db->escape($word) . '%"';
                    }
                    if ($searchIn['attributes_values'] === true) {
                        $cond .= ' OR LOWER(pa.text) LIKE "%' . $this->db->escape($word) . '%"';
                    }
                    if ($searchIn['categories'] === true) {
                        $cond .= ' OR LOWER(cd.name) LIKE "%' . $this->db->escape($word) . '%"';
                    }
                    if ($searchIn['filters'] === true) {
                        $cond .= ' OR LOWER(fd.name) LIKE "%' . $this->db->escape($word) . '%"';
                    }
                    if ($searchIn['description'] === true) {
                        $cond .= ' OR LOWER(pd.description) LIKE "%' . $this->db->escape($word) . '%"';
                    }
                    if ($searchIn['optionname'] === true) {
                        $cond .= ' OR LOWER(ode.name) LIKE "%' . $this->db->escape($word) . '%"';
                    }
                    if ($searchIn['optionvalue'] === true) {
                        $cond .= ' OR LOWER(ovde.name) LIKE "%' . $this->db->escape($word) . '%"';
                    }
                    if ($searchIn['metadescription'] === true) {
                        $cond .= ' OR LOWER(pd.meta_description) LIKE "%' . $this->db->escape($word) . '%"';
                    }
                    if ($searchIn['metakeyword'] === true) {
                        $cond .= ' OR LOWER(pd.meta_keyword) LIKE "%' . $this->db->escape($word) . '%"';
                    }
                    $cond .= ')';
                }
                $cond = $this->isearch_substr($cond, 4);

                $extra_sql = '';
                $extra_select = '';

                if ($searchIn['manufacturer'] === true) {
                    $extra_select .= 'manu.name AS manufacturer, ';
                    $extra_sql .= 'LEFT JOIN ' . DB_PREFIX . 'manufacturer AS manu ON manu.manufacturer_id = p.manufacturer_id ';
                }

                if ($searchIn['tags'] === true) {
                    $extra_select .= 'ptag.tag AS tag, ';
                    $extra_sql .= 'LEFT JOIN ' . DB_PREFIX . 'product_tag AS ptag ON (ptag.product_id = pd.product_id AND pd.language_id = ptag.language_id) ';
                }

                $needs_category_join = false;

                if (!empty($excludeRules)) {
                    $cond_exclude = array();

                    foreach ($excludeRules as $rule) {
                        $operator = '';
                        switch ($rule['operator']) {
                            case 'lt' : $operator = '<';
                                break;
                            case 'gt' : $operator = '>';
                                break;
                            case 'eq' : $operator = '=';
                                break;
                        }

                        switch ($rule['type']) {
                            case 'category_status' : {
                                    $cond_exclude[] = 'cat.status ' . $operator . ' ' . trim($rule['value']);
                                } break;
                            case 'status' : {
                                    $cond_exclude[] = 'p.status ' . $operator . ' ' . trim($rule['value']);
                                } break;
                            case 'quantity' : {
                                    $cond_exclude[] = 'p.quantity ' . $operator . ' ' . trim($rule['value']);
                                } break;
                        }
                        if ($rule['type'] == 'category_status') {
                            $needs_category_join = true;
                        }
                    }
                } else {
                    $cond_exclude = array('p.status = 0');
                }

                if ($searchIn['categories'] === true || !empty($category_id) || $needs_category_join) {
                    $extra_sql .= 'LEFT JOIN ' . DB_PREFIX . 'product_to_category AS p2c ON p2c.product_id = pd.product_id ';
                }

                if ($needs_category_join) {
                    $extra_sql .= 'LEFT JOIN ' . DB_PREFIX . 'category AS cat ON (cat.category_id = p2c.category_id AND p2c.product_id = pd.product_id) ';
                }

                if ($searchIn['filters'] === true) {
                    $extra_select .= 'fd.name AS filter, ';
                    $extra_sql .= 'LEFT JOIN ' . DB_PREFIX . 'product_filter AS p2f ON p2f.product_id = pd.product_id ';
                    $extra_sql .= 'LEFT JOIN ' . DB_PREFIX . 'filter_description AS fd ON (fd.filter_id = p2f.filter_id AND pd.language_id = fd.language_id) ';
                }

                if (defined('VERSION') && VERSION >= '1.5.5') {

                    if (!empty($category_id)) {
                        if (!empty($sub_category)) {
                            $extra_sql .= " LEFT JOIN " . DB_PREFIX . "category_path cp ON (cp.category_id = p2c.category_id)";
                            $cond .= " AND cp.path_id = '" . (int) $category_id . "'";
                        } else {
                            $cond .= " AND p2c.category_id = '" . (int) $category_id . "'";
                        }
                    }
                } else {

                    if (!empty($category_id)) {
                        if (!empty($sub_category)) {
                            $implode_data = array();

                            $implode_data[] = (int) $category_id;

                            $this->load->model('catalog/category');

                            $categories = $this->model_catalog_category->getCategoriesByParentId($category_id);

                            foreach ($categories as $my_category_id) {
                                $implode_data[] = (int) $my_category_id;
                            }

                            $cond .= " AND p2c.category_id IN (" . implode(', ', $implode_data) . ")";
                        } else {
                            $cond .= " AND p2c.category_id = '" . (int) $category_id . "'";
                        }
                    }
                }

                if ($searchIn['categories'] === true) {
                    $extra_select .= 'cd.name AS category, ';
                    $extra_sql .= 'LEFT JOIN ' . DB_PREFIX . 'category_description AS cd ON (cd.category_id = p2c.category_id AND pd.language_id = cd.language_id) ';
                }

                if ($searchIn['attributes'] === true || $searchIn['attributes_values'] === true) {
                    $extra_select .= 'ad.name AS attribute, ';
                    $extra_sql .= 'LEFT JOIN ' . DB_PREFIX . 'product_attribute AS pa ON pa.product_id = pd.product_id ';
                    $extra_sql .= 'LEFT JOIN ' . DB_PREFIX . 'attribute_description AS ad ON (ad.attribute_id = pa.attribute_id AND pd.language_id = ad.language_id) ';
                }

                if ($searchIn['location'] === true) {
                    $extra_select .= 'p.location, ';
                }

                if ($searchIn['model'] === true) {
                    $extra_select .= '';
                }

                if ($searchIn['name'] === true) {
                    $extra_select .= '';
                }

                if ($searchIn['optionname'] === true) {
                    $extra_select .= 'ode.name AS optionname, ';
                    $extra_sql .= 'LEFT JOIN ' . DB_PREFIX . 'product_option AS popt ON popt.product_id = pd.product_id ';
                    $extra_sql .= 'LEFT JOIN ' . DB_PREFIX . 'option_description AS ode ON (ode.option_id = popt.option_id AND pd.language_id = ode.language_id)';
                }

                if ($searchIn['optionvalue'] === true) {
                    $extra_select .= 'ovde.name AS optionvalue, ';
                    if ($searchIn['optionname'] !== true) {
                        $extra_sql .= 'LEFT JOIN ' . DB_PREFIX . 'product_option AS popt ON popt.product_id = pd.product_id ';
                    }
                    $extra_sql .= 'LEFT JOIN ' . DB_PREFIX . 'option_value_description AS ovde ON (ovde.option_id = popt.option_id AND pd.language_id = ovde.language_id)';
                }

                $extra_select .= 'p.image, ';


                $sql_limit = ' '; //LIMIT 1000';

                if (defined('VERSION') && VERSION >= '1.5.4') {
                    if ($searchIn['tags'] === true) {
                        $extra_sql = str_replace('LEFT JOIN ' . DB_PREFIX . 'product_tag AS ptag ON (ptag.product_id = pd.product_id AND pd.language_id = ptag.language_id) ', '', $extra_sql);
                        $extra_select = str_replace('ptag.tag AS tag, ', 'pd.tag AS tag, ', $extra_select);
                        $cond = str_replace('ptag.tag', 'pd.tag', $cond);
                    }
                }

                $sql = 'SELECT pd.product_id, pd.description AS description, AVG(r1.rating) AS rating, COUNT(r1.rating) AS reviews, p.model, pd.name,' . $extra_select . ' p.price, p.tax_class_id, pspe.price AS special, pspe.date_end AS ps_date_end, pspe.date_start AS ps_date_start FROM ' . DB_PREFIX . 'product_description AS pd ';
                $sql .= 'LEFT JOIN ' . DB_PREFIX . 'product AS p ON p.product_id = pd.product_id ';
                $sql .= 'LEFT JOIN ' . DB_PREFIX . 'review AS r1 ON r1.product_id = pd.product_id AND r1.status = 1 ';
                $sql .= 'LEFT JOIN ' . DB_PREFIX . 'product_to_store AS ptostore ON ptostore.product_id = pd.product_id ';
                $sql .= 'LEFT JOIN ' . DB_PREFIX . 'product_special AS pspe ON (pspe.product_id = pd.product_id AND pspe.customer_group_id = "' . $customer_group_id . '" AND (pspe.date_start = "0000-00-00" OR pspe.date_start < NOW()) AND (pspe.date_end = "0000-00-00" OR pspe.date_end > NOW()) AND pspe.priority = (SELECT priority FROM ' . DB_PREFIX . 'product_special WHERE product_id = pd.product_id AND customer_group_id = "' . $customer_group_id . '" AND (date_start = "0000-00-00" OR date_start < NOW()) AND (date_end = "0000-00-00" OR date_end > NOW()) ORDER BY priority ASC, price ASC LIMIT 0,1) ) ';
                $sql .= $extra_sql;

                $sql .= 'WHERE ' . $cond . ' AND !(' . implode(' OR ', $cond_exclude) . ') ';
                $sql .= 'AND pd.language_id = ' . (int) $this->config->get('config_language_id');
                $sql .= ' AND ptostore.store_id =  ' . (int) $this->config->get('config_store_id');

                $sql .= ' GROUP BY pd.product_id';

                if (empty($sort)) {
                    $sql .= ' ORDER BY Length(pd.name), pd.name ASC';
                } else {
                    $sql .= ' ORDER BY ' . $sort . ' ' . $order;
                }
                $sql .= $sql_limit;

                //{HOOK_SEARCH_IN_CACHE_ISEARCHSTANDARD}

                $result = $this->db->query($sql);

                //{HOOK_SEARCH_AGAIN_ISEARCHSTANDARD}

                if ($result) {
                    $products = (isset($result->rows)) ? $result->rows : $result->row;
                }
            }

            if (empty($sort))
                $products = $this->sortProducts($products, $keywords, $searchIn);

            $this->cache->set('productstandard.' . (int) $this->config->get('config_language_id') . '.' . (int) $this->config->get('config_store_id') . '.' . (int) $customer_group_id . '.' . $cache, $products);
        }
        if (empty($products[0]))
            $products = array();
        return $products;
    }

    function iSearchFilter(&$products, $keywords, $searchIn, $strictSearch) {

        $keywords = trim($this->isearch_strtolower($keywords));

        if ($this->isearch_strlen($keywords) >= 2) {
            $words = explode(' ', $keywords);

            if ($strictSearch === true) {
                $words = array($keywords);
            }

            $searchMap = array();
            foreach ($searchIn as $searchField => $searchable) {
                if ($searchable) {
                    if ($searchField == 'metadescription')
                        $searchMap[] = 'meta_description';
                    else if ($searchField == 'metakeyword')
                        $searchMap[] = 'meta_keyword';
                    else if ($searchField == 'tags')
                        $searchMap[] = 'tag';
                    else
                        $searchMap[] = $searchField;
                }
            }

            foreach ($products as $index => $product) {

                $found = false;
                foreach ($searchMap as $field) {
                    foreach ($words as $word) {
                        if (!empty($product[$field]) && !empty($word)) {
                            //print_r($product['name'].' - '.$product[$field].' - '.$word.'<br>');
                            $ast = (in_array($field, array('sku', 'upc', 'ean', 'jan', 'isbn', 'mpn'))) ? '^' : $ast = '(.*)';
                            //$ast = '';
                            //print_r('/'.$ast.'('.$this->db->escape($word).')(.*)/mi'); 
                            if (preg_match('/' . $ast . '(' . $this->db->escape($word) . ')(.*)/mi', $product[$field])) {
                                $found = true;
                            }
                        }
                    }
                }
                if (!$found) {

                    unset($products[$index]);
                }
            }
        }
    }

    function sortProducts($products, $keywords, $searchIn) {

        if ($searchIn['description'] === true || $searchIn['categories'] === true || $searchIn['attributes'] === true || $searchIn['optionname'] === true || $searchIn['optionvalue'] === true || $searchIn['tags'] === true || $searchIn['manufacturer'] === true || $searchIn['location'] === true) {
            $first = array_shift($products);

            array_unshift($products, $first);
            if (!empty($first)) {

                $words = explode(' ', $keywords);
                //print_r($words);
                if (!empty($words[0])) {
                    if ($this->isearch_strstr($first['name'], $words[0]) === false) {
                        $sortedProducts = array();
                        $otherProducts = array();
                        foreach ($products as $k => $p) {

                            if ($this->isearch_strstr($this->isearch_strtolower($p['name']), $this->isearch_strtolower($words[0])) !== false) {

                                array_push($sortedProducts, $p);
                            } else {
                                array_push($otherProducts, $p);
                            }
                        }
                        $sortedProducts = array_merge($sortedProducts, $otherProducts);
                        return $sortedProducts;
                    }
                }
            }
        }
        return $products;
    }

    public function addJoinsFromVersions(&$searchIn) {

        $return = '';
        if ($searchIn['attributes'] == true)
            $return .= 'LEFT JOIN product_attribute AS pa ON pa.product_id = pd.product_id LEFT JOIN attribute_description AS ad ON (ad.attribute_id = pa.attribute_id AND pd.language_id = ad.language_id) ';
        if ($searchIn['categories'] === true) {
            $return .= 'LEFT JOIN ' . DB_PREFIX . 'product_to_category AS p2c ON p2c.product_id = pd.product_id ';
            $return .= 'LEFT JOIN category_description AS cd ON (cd.category_id = p2c.category_id AND pd.language_id = cd.language_id) ';
        }

        if ($searchIn['optionname'] === true) {
            $return .= 'LEFT JOIN ' . DB_PREFIX . 'product_option AS popt ON popt.product_id = pd.product_id ';
            $return .= 'LEFT JOIN ' . DB_PREFIX . 'option_description AS ode ON (ode.option_id = popt.option_id AND pd.language_id = ode.language_id) ';
        }

        if ($searchIn['optionvalue'] === true) {
            if ($searchIn['optionname'] !== true) {
                $return .= 'LEFT JOIN ' . DB_PREFIX . 'product_option AS popt ON popt.product_id = pd.product_id ';
            }
            $return .= 'LEFT JOIN ' . DB_PREFIX . 'option_value_description AS ovde ON (ovde.option_id = popt.option_id AND pd.language_id = ovde.language_id) ';
        }

        if ($searchIn['tags'] === true)
            $return .= 'LEFT JOIN ' . DB_PREFIX . 'product_tag AS ptag ON (ptag.product_id = pd.product_id AND pd.language_id = ptag.language_id) ';

        if (defined('VERSION') && VERSION >= '1.5.4') {
            if ($searchIn['tags'] === true) {
                $return = str_replace('LEFT JOIN ' . DB_PREFIX . 'product_tag AS ptag ON (ptag.product_id = pd.product_id AND pd.language_id = ptag.language_id) ', '', $return);
            }
        }

        return $return;
    }

    public function addSelectFromVersions(&$searchIn) {
        $return = '';
        if ($searchIn['description'] == true)
            $return .= ' pd.description AS description,';
        if ($searchIn['attributes'] == true)
            $return .= ' ad.name AS attribute,';
        if ($searchIn['categories'] === true)
            $return .= ' cd.name AS category,';
        if ($searchIn['optionvalue'] === true)
            $return .= ' ovde.name AS optionvalue,';
        if ($searchIn['optionname'] === true)
            $return .= ' ode.name AS optionname,';
        if ($searchIn['tags'] === true)
            $return .= ' ptag.tag AS tag,';

        if (defined('VERSION') && VERSION >= '1.5.4') {
            if ($searchIn['tags'] === true) {
                $return = str_replace('ptag.tag AS tag,', 'pd.tag AS tag,', $return);
            }
        }

        return $return;
    }

    public function addConditionsFromVersions(&$searchIn, $keywords, $strictSearch) {
        $return = '';

        $keywords = trim($this->isearch_strtolower($keywords));

        if ($this->isearch_strlen($keywords) >= 2) {
            $words = explode(' ', $keywords);

            if ($strictSearch === true) {
                $words = array($keywords);
            }


            if (in_array(true, $searchIn)) {
                foreach ($words as $word) {
                    if ($searchIn['name'] === true) {
                        $return .= ' AND (LOWER(pd.name) LIKE "%' . $this->db->escape($word) . '%"';
                    } else {
                        $return .= ' AND (1=0';
                    }
                    if ($searchIn['model'] === true) {
                        $return .= ' OR LOWER(p.model) LIKE "%' . $this->db->escape($word) . '%"';
                    }
                    if ($searchIn['sku'] === true) {
                        $return .= ' OR LOWER(p.sku) LIKE "' . $this->db->escape($word) . '%"';
                    }
                    if ($searchIn['upc'] === true) {
                        $return .= ' OR LOWER(p.upc) LIKE "' . $this->db->escape($word) . '%"';
                    }
                    if ($searchIn['location'] === true) {
                        $return .= ' OR LOWER(p.location) LIKE "%' . $this->db->escape($word) . '%"';
                    }
                    if ($searchIn['tags'] === true) {
                        $return .= ' OR LOWER(ptag.tag) LIKE "%' . $this->db->escape($word) . '%"';
                    }
                    if ($searchIn['manufacturer'] === true) {
                        $return .= ' OR LOWER(m.name) LIKE "%' . $this->db->escape($word) . '%"';
                    }
                    if ($searchIn['attributes'] === true) {
                        $return .= ' OR LOWER(ad.name) LIKE "%' . $this->db->escape($word) . '%"';
                    }
                    if ($searchIn['categories'] === true) {
                        $return .= ' OR LOWER(cd.name) LIKE "%' . $this->db->escape($word) . '%"';
                    }
                    if ($searchIn['description'] === true) {
                        $return .= ' OR LOWER(pd.description) LIKE "%' . $this->db->escape($word) . '%"';
                    }
                    if ($searchIn['optionname'] === true) {
                        $return .= ' OR LOWER(ode.name) LIKE "%' . $this->db->escape($word) . '%"';
                    }
                    if ($searchIn['optionvalue'] === true) {
                        $return .= ' OR LOWER(ovde.name) LIKE "%' . $this->db->escape($word) . '%"';
                    }
                    if ($searchIn['metadescription'] === true) {
                        $return .= ' OR LOWER(pd.meta_description) LIKE "%' . $this->db->escape($word) . '%"';
                    }
                    if ($searchIn['metakeyword'] === true) {
                        $return .= ' OR LOWER(pd.meta_keyword) LIKE "%' . $this->db->escape($word) . '%"';
                    }
                    $return .= ')';
                }
                $return = $this->isearch_substr($return, 4) . ' AND';
            }

            if (defined('VERSION') && VERSION >= '1.5.4') {
                if ($searchIn['tags'] === true) {
                    $return = str_replace('ptag.tag', 'pd.tag', $return);
                }
            }
        }
        return $return;
    }

    private function convertKeywords($keywords, $singularize, $spellCheck, $excludeTerms, $strictSearch) {
        if (function_exists('mb_convert_encoding'))
            $keywords = mb_convert_encoding($keywords, 'UTF-8');
        if (!empty($spellCheck)) {
            foreach ($spellCheck as $values) {
                if (!empty($values) && !empty($values['incorrect']) && !empty($values['correct'])) {
                    if ($values['incorrect'][0] == '/') {
                        $keywords = preg_replace($values['incorrect'], $values['correct'], $keywords);
                    } else {
                        $keywords = str_replace($values['incorrect'], $values['correct'], $keywords);
                    }
                }
            }
        }

        if ($singularize && !$strictSearch) {
            $words = explode(' ', $keywords);
            foreach ($words as $i => $word) {
                $words[$i] = preg_replace('/(s|es)$/', '', $word);
            }
            $keywords = implode(' ', $words);
        }

        if (!empty($excludeTerms)) {
            $terms = array_filter(explode("\n", $excludeTerms));
            foreach ($terms as &$term) {
                $term = trim(trim($term), "\r");
                $keywords = str_replace($term, '', $keywords);
            }

            $keywords = preg_replace('/\s+/', ' ', $keywords);
        }

        return $keywords;
    }

    private function fixParameters(&$sort, &$order, &$category_id, &$sub_category) {
        if (strpos($category_id, '_') !== false) {
            $cat_id_array = explode('_',$category_id);
            $category_id =$cat_id_array[0];
            
        }
        if (!in_array($sort, array('pd.name', 'p.price', 'rating', 'p.model')))
            $sort = '';
        if (!in_array($order, array('ASC', 'DESC')))
            $order = 'ASC';
        if (!is_numeric($category_id)) {
            $category_id = 0;
        }
        $sub_category = !empty($sub_category);
    }

    /* Multibyte functions */

    public function isearch_strtolower($string) {
        return (function_exists('mb_strtolower')) ? mb_strtolower($string) : strtolower($string);
    }

    public function isearch_strlen($string) {
        return (function_exists('mb_strlen')) ? mb_strlen($string) : strlen($string);
    }

    public function isearch_substr($string, $start) {
        $arg = func_get_args();
        if (isset($arg[2]))
            return (function_exists('mb_substr')) ? mb_substr($string, $start, $arg[2]) : substr($string, $start, $arg[2]);
        else
            return (function_exists('mb_substr')) ? mb_substr($string, $start) : substr($string, $start);
    }

    public function isearch_strstr($string, $needle) {
        $arg = func_get_args();
        if (isset($arg[2]))
            return (function_exists('mb_strstr')) ? mb_strstr($string, $needle, $arg[2]) : strstr($string, $needle, $arg[2]);
        else
            return (function_exists('mb_strstr')) ? mb_strstr($string, $needle) : strstr($string, $needle);
    }

}

?>