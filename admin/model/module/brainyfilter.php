<?php
require_once DIR_SYSTEM . 'library/sqllib.php';
/**
 * Brainy Filter model class for back-end.
 * 
 * Brainy Filter Ultimate 4.7.2, December 3, 2015 / brainyfilter.com
 * Copyright 2014-2015 Giant Leap Lab / www.giantleaplab.com
 * License: Commercial. Reselling of this software or its derivatives is not allowed. You may use this software for one website ONLY including all its subdomains if the top level domain belongs to you and all subdomains are parts of the same OpenCart store.
 * Support: support@giantleaplab.com
 */
class ModelModuleBrainyFilter extends Model 
{
    const FILTERS_TABLE = 'bf_aggregate_filters';
    const ATTRIBUTE_VALUE_TABLE = 'bf_attribute_value';
    const ATTRIBUTE_VALUE_TABLE_BACKUP = 'bf_attribute_value_backup';
    const TMP_TABLE = 'bf_tmp';
    
    public $attrSeparator = false;
    
    public function __construct($registry) {
        parent::__construct($registry);
        
        $settings = $this->config->get('bf_layout_basic');
        if (!is_null($settings) 
                && isset($settings['global']['multiple_attributes']) 
                && $settings['global']['multiple_attributes'] == 1
                && !empty($settings['global']['attribute_separator'])) {
            $this->attrSeparator = $settings['global']['attribute_separator'];
        }
        
        SqlStatement::$DB_PREFIX = DB_PREFIX;
        SqlStatement::$DB_CONNECTOR = $this->db;
    }
    
    /**
     * Fill out bf_aggregate_filters DB table by properties of the given product
     * 
     * @param int $productId Product ID
     * @param boolean $clean Whether to remove existing filters for the given product before insert or not
     * @return void
     */
    public function addProductProperties($productId, $clean = true)
    {
        $sql = new SqlStatement();
        
        // clean all the properties for the given product
        if ($clean) {
            $this->deleteProductProperties($productId);
        }
        
        // product data
        $sql->clean()->select()->from('product')->where('product_id = ?', $productId);
        $res = $this->db->query($sql);
        $product = $res->row;
        
        // attributes
        $attrVals = $this->addAttributeValues($productId);
        if (count($attrVals)) {
            $values = array();
            foreach ($attrVals as $v) {
                $values[] = array(
                    'type'       => 'ATTRIBUTE',
                    'group_id'   => $v['attribute_id'],
                    'product_id' => $productId,
                    'value'      => $v['attribute_value_id']
                );
            }
            $sql->clean();
            $sql->insertInto(self::FILTERS_TABLE, $values)->ignore();
            
            $this->db->query($sql);
        }
        
        // options
        $select = new SqlStatement();
        $select->select(array('\'OPTION\'', 'option_id', 'product_id', 'option_value_id'))
                ->from('product_option_value')
                ->where('product_id = ?', $productId);
        
        $sql->clean()->insertInto(self::FILTERS_TABLE, $select, array('type', 'group_id', 'product_id', 'value'))->ignore();
        
        $this->db->query($sql);
        
        // filters
        $select->clean()
            ->select(array('\'FILTER\'', 'filter_group_id', 'product_id', 'pf.filter_id'))
            ->from(array('pf' => 'product_filter'))
            ->innerJoin(array('f' => 'filter'), 'pf.filter_id = f.filter_id')
            ->where('pf.product_id = ?', $productId);
        
        $sql->clean()->insertInto(self::FILTERS_TABLE, $select, array('type', 'group_id', 'product_id', 'value'))->ignore();
        
        $this->db->query($sql);
        
        // categories
        $select->clean()
            ->select(array('\'CATEGORY\'', '0', 'pc.product_id', 'cp.path_id'))
            ->from(array('pc' => 'product_to_category'))
            ->innerJoin(array('cp' => 'category_path'), 'pc.category_id = cp.category_id')
            ->where('pc.product_id = ?', $productId);
        
        $sql->clean()->insertInto(self::FILTERS_TABLE, $select, array('type', 'group_id', 'product_id', 'value'))->ignore();
        
        $this->db->query($sql);
        
        // manufacturer and stock status
        $values = array(
            array(
                'type' => 'MANUFACTURER',
                'group_id' => 0,
                'product_id' => $productId,
                'value' => $product['manufacturer_id']
            ),
            array(
                'type' => 'STOCK_STATUS',
                'group_id' => 0,
                'product_id' => $productId,
                'value' => $product['stock_status_id']
            )
        );
        
        $sql->clean()->insertInto(self::FILTERS_TABLE, $values)->ignore();
        
        $this->db->query($sql);
        
    }
    
    public function deleteProductProperties($productId)
    {
        $sql = new SqlStatement();
        $sql->delete()
            ->from(self::FILTERS_TABLE)
            ->where('product_id = ?', $productId);

        $this->db->query($sql);
    }
    
    /**
     * Fill out bf_attribute_value DB table by attribute values specified for
     * the given product
     * 
     * @param int $productId Product ID
     * @return mixed
     */
    public function addAttributeValues($productId)
    {
        
        $sql = new SqlStatement();
        
        // retrieve product attributes
        $sql->select(array('attribute_id', 'language_id', 'text'))
            ->from('product_attribute')
            ->where('product_id = ?', $productId);
        
        $res = $this->db->query($sql);
        
        if (!$res || !count($res->rows)) {
            return;
        }
        
        $values = array();
        // explode values by separator if necessary
        foreach ($res->rows as $row) {
            $subVals = ($this->attrSeparator) 
                    ? explode($this->attrSeparator, $row['text']) 
                    : array($row['text']);
            foreach ($subVals as $v) {
                $v = preg_replace('/(^[\s]+)|([\s]+$)/us', '', $v);
                $values[] = array(
                    'attribute_id' => $row['attribute_id'],
                    'language_id'  => $row['language_id'],
                    'value'        => $v
                );
            }
        }
        
        $sql->clean();
        $sql->insertInto(self::ATTRIBUTE_VALUE_TABLE, $values)->ignore();
        
        // try to insert all the values into bf_attribute_value DB table
        $this->db->query($sql);
        
        // next step is to get back IDs of the attribute values
        $mv = array();
        foreach ($values as $v) {
            $mv[] = "attribute_id = " . $v['attribute_id'] 
                  . " AND language_id = " . $v['language_id'] 
                  . " AND value = '" . $this->db->escape($v['value']) . "'";
        }
        
        $sql->clean();
        $sql->select(array('attribute_value_id', 'attribute_id'))
            ->from(self::ATTRIBUTE_VALUE_TABLE)
            ->multipleWhere($mv);
        
        $res = $this->db->query($sql);
        
        return $res->rows;
    }
    
    public function createBFilterTable()
    {
        $this->dropBFilterTable();
        
        $this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . self::FILTERS_TABLE . "` (
            `aggregate_filter_id` int(11) NOT NULL AUTO_INCREMENT,
            `type` enum('ATTRIBUTE','MANUFACTURER','STOCK_STATUS','FILTER','OPTION','CATEGORY') COLLATE utf8_general_ci NOT NULL,
            `group_id` int(11) NOT NULL,
            `product_id` int(11) NOT NULL,
            `value` int(11) NOT NULL,
            PRIMARY KEY (`aggregate_filter_id`),
            UNIQUE KEY `product_id` (`product_id`,`type`,`group_id`,`value`),
            KEY `type` (`type`,`group_id`,`value`)
          ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1 ;");
    }
    
    public function dropBFilterTable()
    {
        $this->db->query('DROP TABLE IF EXISTS ' . DB_PREFIX . self::FILTERS_TABLE);
    }
    
    public function createAttributeValueTable()
    {
        $this->dropAttributeValueTable();
        
        $this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . self::ATTRIBUTE_VALUE_TABLE . "` (
            `attribute_value_id` int(11) NOT NULL AUTO_INCREMENT,
            `attribute_id` int(11) NOT NULL,
            `language_id` int(11) NOT NULL,
            `value` varchar(200) CHARACTER SET utf8 NOT NULL,
            `sort_order` int(11) NOT NULL,
            PRIMARY KEY (`attribute_value_id`),
            UNIQUE KEY `attribute_id` (`attribute_id`,`language_id`,`value`),
            KEY `sort_order` (`sort_order`)
          ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1 ;");
    }
    
    public function createAttributeValueTableBackup()
    {
        $this->dropAttributeValueTableBackup();
        

        $this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . self::ATTRIBUTE_VALUE_TABLE_BACKUP . "` (
            `attribute_value_id` int(11) NOT NULL AUTO_INCREMENT,
            `attribute_id` int(11) NOT NULL,
            `language_id` int(11) NOT NULL,
            `value` varchar(200) CHARACTER SET utf8 NOT NULL,
            `sort_order` int(11) NOT NULL,
            PRIMARY KEY (`attribute_value_id`),
            UNIQUE KEY `attribute_id` (`attribute_id`,`language_id`,`value`),
            KEY `sort_order` (`sort_order`)
          ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1 ;");
        
        $this->db->query("SET SQL_MODE =  'NO_AUTO_VALUE_ON_ZERO'");
        
        $this->db->query("INSERT INTO  `" . DB_PREFIX . self::ATTRIBUTE_VALUE_TABLE_BACKUP . "` 
            SELECT * 
            FROM  `" . DB_PREFIX . self::ATTRIBUTE_VALUE_TABLE . "`");
        
    }
    
    public function dropAttributeValueTable()
    {
        $this->db->query('DROP TABLE IF EXISTS ' . DB_PREFIX . self::ATTRIBUTE_VALUE_TABLE);
    }
    
    public function dropAttributeValueTableBackup()
    {
        $this->db->query('DROP TABLE IF EXISTS ' . DB_PREFIX . self::ATTRIBUTE_VALUE_TABLE_BACKUP);
    }
    
    public function fillBFiltersTable()
    {
        $sql = new SqlStatement();
        
        $sql->clean()->delete()->from(self::FILTERS_TABLE);
        $this->db->query($sql);
        
        $this->updateProductAttributes();
        
        $select = new SqlStatement();
        $select->select(array('\'OPTION\'', 'option_id', 'product_id', 'option_value_id'))
                ->distinct()
                ->from('product_option_value');
        
        $sql->clean()->insertInto(self::FILTERS_TABLE, $select, array('type', 'group_id', 'product_id', 'value'))->ignore();
        $this->db->query($sql);
        
        $select->clean()
            ->distinct()
            ->select(array('\'FILTER\'', 'filter_group_id', 'product_id', 'pf.filter_id'))
            ->from(array('pf' => 'product_filter'))
            ->innerJoin(array('f' => 'filter'), 'pf.filter_id = f.filter_id');
        
        $sql->clean()->insertInto(self::FILTERS_TABLE, $select, array('type', 'group_id', 'product_id', 'value'))->ignore();
        $this->db->query($sql);
        
        $select->clean()
            ->distinct()
            ->select(array('\'CATEGORY\'', '0', 'pc.product_id', 'cp.path_id'))
            ->from(array('pc' => 'product_to_category'))
            ->innerJoin(array('cp' => 'category_path'), 'pc.category_id = cp.category_id');
        
        $sql->clean()->insertInto(self::FILTERS_TABLE, $select, array('type', 'group_id', 'product_id', 'value'))->ignore();
        $this->db->query($sql);
        

        
        $sel1 = new SqlStatement();
        $sel1->select(array('\'MANUFACTURER\'', '0', 'product_id', 'manufacturer_id'))
            ->from(array('p' => 'product'));
        
        $sel2 = new SqlStatement();
        $sel2->select(array('\'STOCK_STATUS\'', '0', 'product_id', 'stock_status_id'))
            ->from(array('p' => 'product'));
        
        $sql->clean()->insertInto(self::FILTERS_TABLE, $sel1 . ' UNION ' . $sel2, array('type', 'group_id', 'product_id', 'value'))->ignore();
        $this->db->query($sql);
        
        $this->db->query($sql);
    }
    
    /**
     * The method updates product attribute values.
     * Use this method when changing to/from multiple attribute mode
     * 
     * @return void
     */
    public function updateProductAttributes()
    {
        $sql = new SqlStatement();
        
        $this->db->query('TRUNCATE TABLE ' . DB_PREFIX . self::ATTRIBUTE_VALUE_TABLE);
        $sql->clean()->delete()->from(self::FILTERS_TABLE)->where('type = \'ATTRIBUTE\'');
        $this->db->query($sql);
        // To handle relations between original attribute values and separated attribute values
        // we use temporary table.
        $this->db->query('CREATE TEMPORARY TABLE IF NOT EXISTS ' . DB_PREFIX . self::TMP_TABLE 
                . '(attribute_id int(11), language_id int(11), value varchar(100) CHARACTER SET utf8, product_id int(11), KEY k(attribute_id, language_id, value)) DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci');
        
        $getAttrSql = new SqlStatement();
        $getAttrSql->clean()->select(array('attribute_id', 'language_id', 'value' => 'text', 'product_id'))
                ->distinct()
                ->from('product_attribute');
        
        $step = 0;
        $res = $this->db->query($getAttrSql->limit(10000, $step * 10000));
        
         while (count($res->rows)) {
            
            $values = array();
            $tmpTblVals = array();
            
            if ($this->attrSeparator) {
                foreach ($res->rows as $k => $row) {
                    $expl = explode($this->attrSeparator, $row['value']);
                    foreach ($expl as $val) {
                        $val = preg_replace('/(^[\s]+)|([\s]+$)/us', '', $val);
                        $values[] = array('attribute_id' => $row['attribute_id'], 'language_id' => $row['language_id'], 'value' => $val);
                        $tmpTblVals[] = array('attribute_id' => $row['attribute_id'], 'language_id' => $row['language_id'], 'value' => $val, 'product_id' => $row['product_id']);
                    }
                }
            } else {
                foreach ($res->rows as $row) {
                    $val = preg_replace('/(^[\s]+)|([\s]+$)/us', '', $row['value']);
                    $values[] = array('attribute_id' => $row['attribute_id'], 'language_id' => $row['language_id'], 'value' => $val);
                    $tmpTblVals[] = array('attribute_id' => $row['attribute_id'], 'language_id' => $row['language_id'], 'value' => $val, 'product_id' => $row['product_id']);
                }
            }
            $sql->clean()->insertInto(self::ATTRIBUTE_VALUE_TABLE, $values)->ignore();
            $this->db->query($sql);
            
            $sql->clean()->insertInto(self::TMP_TABLE, $tmpTblVals)->ignore();
            $this->db->query($sql);

            $sel = new SqlStatement();

            if (!empty($tmpTblVals)) {
                $sel->select(array('\'ATTRIBUTE\'', 'av.attribute_id', 'product_id', 'attribute_value_id'))
                    ->from(array('pa' => self::TMP_TABLE))
                    ->leftJoin(array('av' => self::ATTRIBUTE_VALUE_TABLE), 
                            'pa.value = av.value AND av.language_id = pa.language_id AND av.attribute_id = pa.attribute_id')
                    ->where('av.value IS NOT NULL');
            }

            $sql->clean()->insertInto(self::FILTERS_TABLE, $sel, array('type', 'group_id', 'product_id', 'value'))->ignore();

            $this->db->query($sql);
            
            $this->db->query('TRUNCATE TABLE ' . DB_PREFIX . self::TMP_TABLE);
            
            $step ++;
            $res = $this->db->query($getAttrSql->limit(10000, $step * 10000));
        }
        
    }
    
    public function recoverAttributeValueSortOrder()
    {
        // try to recover attribute values sort order
        $sqlStr = " UPDATE " . DB_PREFIX . self::ATTRIBUTE_VALUE_TABLE . " AS a1"
                . " SET sort_order = (SELECT sort_order "
                . "     FROM " . DB_PREFIX . self::ATTRIBUTE_VALUE_TABLE_BACKUP . " AS a2 "
                . "     WHERE a1.attribute_id = a2.attribute_id "
                . "         AND a1.language_id = a2.language_id "
                . "         AND a1.value = a2.value)";

        $this->db->query($sqlStr);
        $this->dropAttributeValueTableBackup();
    }
    
    public function getFilters()
    {
        $sql = new SqlStatement();
        $sql->select(array('id' => 'fg.filter_group_id', 'name' => 'fgd.name'))
            ->from(array('fg' => 'filter_group'))
            ->innerJoin(array('fgd' => 'filter_group_description'), 'fg.filter_group_id = fgd.filter_group_id')
            ->where('fgd.language_id = ?', (int)$this->config->get('config_language_id'))
            ->order(array('fg.sort_order'));
        
        $res = $this->db->query($sql);
		
        return $res->num_rows ? $res->rows : array();;
    }
    
    public function getOptions()
    {
        $sql = new SqlStatement();
        $sql->select(array('id' => 'o.option_id', 'name' => 'od.name'))
            ->from(array('o' => 'option'))
            ->innerJoin(array('od' => 'option_description'), 'o.option_id = od.option_id')
            ->where('od.language_id = ?', (int)$this->config->get('config_language_id'))
            ->order(array('o.sort_order'));
        
        $res = $this->db->query($sql);
        
        return $res->num_rows ? $res->rows : array();
    }
    
    public function getAttributes()
    {
        $sql = new SqlStatement();
        $sql->select(array('id' => 'a.attribute_id', 'name' => 'ad.name', 'grp' => 'agd.name'))
            ->from(array('a' => 'attribute'))
            ->innerJoin(array('ad' => 'attribute_description'), 'a.attribute_id = ad.attribute_id')
            ->innerJoin(array('ag' => 'attribute_group'), 'ag.attribute_group_id = a.attribute_group_id')
            ->innerJoin(array('agd' => 'attribute_group_description'), 'agd.attribute_group_id = a.attribute_group_id')
            ->where('agd.language_id = ?', (int)$this->config->get('config_language_id'))
            ->where('ad.language_id = ?', (int)$this->config->get('config_language_id'))
            ->order(array('ag.sort_order', 'a.sort_order'));
        
        $res = $this->db->query($sql);
		
        $attrGroups = array();
        if ($res->num_rows) {
            foreach ($res->rows as $row) {
                if (!isset($attrGroups[$row['grp']])) {
                    $attrGroups[$row['grp']] = array();
                }
                $attrGroups[$row['grp']][] = array(
                    'id' => $row['id'],
                    'name' => $row['name']
                );
            }
        }
        
        return $attrGroups;
    }
    
    public function getAttributeValues($attrId)
    {
        $sql = new SqlStatement();
        $sql->select()
            ->from(self::ATTRIBUTE_VALUE_TABLE)
            ->where('attribute_id = ?', $attrId)
            ->order(array('sort_order', 'value'));
        
        $res = $this->db->query($sql);
        $values = array();
        if ($res->num_rows) {
            foreach ($res->rows as $row) {
                $lang = $row['language_id'];
                if (!isset($values[$lang])) {
                    $values[$lang] = array();
                }
                unset($row['language_id']);
                $values[$lang][] = $row;
            }
        }
        return $values;
    }
    
    public function changeAttrValuesSortOrder($data) {
        if (is_array($data) && count($data)) {
            foreach ($data as $id => $sort) {
                $sql = ' UPDATE ' . DB_PREFIX . self::ATTRIBUTE_VALUE_TABLE 
                     . ' SET `sort_order` = "' . $this->db->escape($sort) . '"'
                     . ' WHERE attribute_value_id = "' . $this->db->escape($id) . '"';
                $this->db->query($sql);
            }
        }
    }
    
    public function getDefaultLayout()
    {
        $sql = new SqlStatement();
        $sql->select(array('layout_id'))
            ->from('layout_route')
            ->where('route = ?', 'module/brainyfilter/filter')
            ->where('store_id = 0');
        
        $res = $this->db->query($sql);
        
        if ($res->num_rows) {
            return $res->row['layout_id'];
        } else {
            return null;
        }
    }
    
    public function addDefaultLayout()
    {
        $defaultLayout = $this->getDefaultLayout();
        if ($defaultLayout) {
            $this->removeDefaultLayout();
        }
        
        $data = array(
            'name' => 'Brainy Filter Layout',
            'layout_route' => array (
                array(
                    'route' => 'module/brainyfilter/filter',
                    'store_id' => '0',
                )
            )
        );
        
        $this->load->model('design/layout');
        $this->model_design_layout->addLayout($data);
    }
    
    public function removeDefaultLayout()
    {
        $defaultLayout = $this->getDefaultLayout();
        if ($defaultLayout) {
            $this->load->model('design/layout');
            $this->model_design_layout->deleteLayout($defaultLayout);
        }
    }
    
    public function detectCategoryLayouts()
    {
        $sql = new SqlStatement();
        $sql->select(array('layout_id'))
            ->from('layout_route')
            ->where('route = "product/category"');
        
        $res = $this->db->query($sql);
        $output = array();
        if ($res->num_rows) {
            foreach ($res->rows as $row) {
                $output[] = $row['layout_id'];
            }
        }
        return $output;
    }
    
    public function addCustomIndexes()
    {
        $this->db->query('ALTER TABLE ' . DB_PREFIX . 'product_option_value ADD INDEX  bf_product_option_value (  product_id ,  option_value_id )');
    }
    
    public function removeCustomIndexes()
    {
        $res = $this->db->query('SHOW INDEX FROM ' . DB_PREFIX . 'product_option_value WHERE KEY_NAME = "bf_product_option_value"');
        if ($res->num_rows) {
            $this->db->query('ALTER TABLE ' . DB_PREFIX . 'product_option_value DROP INDEX bf_product_option_value');
        }
    }
}