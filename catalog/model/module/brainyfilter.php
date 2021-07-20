<?php
require_once DIR_SYSTEM . 'library/sqllib.php';

/**
 * Brainy Filter model class.
 * The class contains methods for calculating of MIN/MAX price, total products,
 * for retrieving list of attributes/manufacturers/filters/stock statuses/options 
 * and for applying filters to the methods from Category module.
 * 
 * Brainy Filter Ultimate 4.7.2, December 3, 2015 / brainyfilter.com
 * Copyright 2014-2015 Giant Leap Lab / www.giantleaplab.com
 * License: Commercial. Reselling of this software or its derivatives is not allowed. You may use this software for one website ONLY including all its subdomains if the top level domain belongs to you and all subdomains are parts of the same OpenCart store.
 * Support: support@giantleaplab.com
 */
final class ModelModuleBrainyFilter extends Model 
{
    
    /**
     * @const Name of the temporary DB table, which contains a list of filtered products with all necessary properties like rating, actual price and so on
     */
    const RESULTS_TABLE = 'bf_found_results';
    
    /**
     * @const DB table name which contains aggregated list of product properties like attributes, manufacturers, stock statuses, filters and options
     * 
     */
    const FILTERS_TABLE = 'bf_aggregate_filters';
    
    /**
     * @const DB table name which holds indexed attribute values
     */
    const ATTRIBUTE_VALUE_TABLE = 'bf_attribute_value';
    
    /**
     * Catalog top-category ID
     * @var int
     */
    private $topCategory = null;
    
    /**
     * Catalog sub-category ID
     * @var int 
     */
    private $subCategory = null;
    
    /**
     * Manufacturer ID. The parameter will trigger filtering of all attributes by manufacturer id.
     * It is set in case manufacturer_id GET parameter exsists
     * @var int
     */
    private $manufacturer = null;
    
    /**
     * Search query by product names
     * @var string
     */
    private $searchNameString = '';
    
    /**
     * Search query by product tags
     * @var string
     */
    private $searchTagString = '';
    
    /**
     * Search query by product descriptions
     * @var string
     */
    private $searchDescriptionString = '';
    
    /**
     * List of all selected filters with the following items:
     * <ul>
     * <li><b>price</b> - stdClass with properties <i>min</i> and <i>max</i></li>
     * <li><b>rating</b> - Array</li>
     * <li><b>attribute</b> - Array</li>
     * <li><b>manufacturer</b> - Array</li>
     * <li><b>stock_status</b> - Array</li>
     * <li><b>option</b> - Array</li>
     * <li><b>filter</b> - Array</li>
     * <li><b>search</b> - search string</li>
     * </ul>
     * @see ModelModuleBrainyFilter::getConditions() - getter
     * @property price
     * @var stdClass 
     */
    private $conditions  = null;
    
    /**
     * Aggregated array of selected attributes, filters, options, manufacturers and stock statuses.
     * The data is designed for the DB table self::FILTERS_TABLE
     * <br>scheme:
     * <pre>
     * array(
     *      [type: ATTRIBUTE, MANUFACTURER, etc.] => array(
     *          [group id] => array([values])
     *      )
     * )
     * </pre>
     * @var array
     */
    private $aggregate   = array();
    
    /**
     * Customer group ID
     * @var int
     */
    private $customerGroupId = null;
    
    /**
     * The property contains of most repeated subqueries. Basicly it has the following parts:
     * <ul>
     * <li><b>fixedTax</b> - subquery, which should be joined to the product table to find out total fixed tax for a particular product</li>
     * <li><b>percentTax</b> - subquery, which should be joined to the product table to find out total percent tax rate for a particular product</li>
     * <li><b>actualPrice</b> - IF condition for defining actual price of a particular product taking into account it price, special offers and discounts</li>
     * </ul> 
     * @var stdClass 
     */
    private $subquery = null;
    
    /**
     * Product limit per page 
     * @var int 
     */
    public $productsLimit = 20;
    
    /**
     * Product list offset
     * @var int 
     */
    public $productsStart = 0;

    /**
     * Product list sort order
     * @var array
     */
    public $sortOrder = array('LCASE(pd.name) ASC');
    /**
     * Flag which defines whether the result temporary db table was already created or not
     * @var boolean 
     */
    private static $TMP_TABLE_CREATED = false;
    
    private static $IN_STOCK_STATUS = 7;

    /**
     * Cache data which designed to prevent execution of similar sql queries in
     * case of multiple modules per page
     * @var array
     */
    private static $_cache = array( 'sliders' => array() );
    
    /**
     * Whether to hide out of stock products or not. In case the property is set to TRUE,
     * the filter will take into account stock status per each product option
     * @var boolean
     */
    private static $HIDE_OUT_OF_STOCK = false;

    /**
	 * Constructor
	 *
	 * @param array $registry 
	 */
    public function __construct($registry) {
        parent::__construct($registry);
        SqlStatement::$DB_PREFIX = DB_PREFIX;
        SqlStatement::$DB_CONNECTOR = $this->db;
        
        $bfSettings = $this->config->get('bf_layout_basic');
        self::$IN_STOCK_STATUS   = $bfSettings['global']['instock_status_id'];
        self::$HIDE_OUT_OF_STOCK = (bool)$bfSettings['global']['hide_out_of_stock'];
        
        $this->conditions = new stdClass();
        $this->conditions->manufacturer = array();
        $this->conditions->stock_status = array();
        $this->conditions->category = array();
        $this->conditions->filter = array();
        $this->conditions->attribute = array();
        $this->conditions->option = array();
        $this->conditions->price = null;
        $this->conditions->rating = array();
        $this->conditions->search = '';
		
        if (isset($this->request->get['manufacturer_id']) && !empty($this->request->get['manufacturer_id'])) {
            $this->manufacturer = (int) $this->request->get['manufacturer_id'];
        }

        // fill out the conditions property
        $this->_parseBFilterParam();
        
        if (count($this->aggregate)) {
            foreach ($this->aggregate as $type => $group) {
                if (empty($group)) {
                    unset($this->aggregate[$type]);
                }
            }
        }
        
        $this->customerGroupId = ($this->customer->isLogged()) 
                ? $this->customer->getCustomerGroupId()
                : $this->config->get('config_customer_group_id');
        
        // repeated subqueries
        $this->subquery = new stdClass();
        $this->_prepareSubQueries();
    }
    
    /**
     * Parse BrainyFilter Param
	 * <br />
     * The method explodes bfilter GET parameter to the list of selected filters
     * and fills out $this->conditions and $this->aggregate properties
     * 
     * @return void
     */
	private function _parseBFilterParam()
	{
        $map = array('a' => 'attribute', 'm' => 'manufacturer', 's' => 'stock_status', 'f' => 'filter', 'o' => 'option', 'r' => 'rating', 'c' => 'category');
        
        if (!isset($this->request->get['bfilter'])) {
            
            return;
        }
		$bfilter = $this->request->get['bfilter'];

		$params = explode(';', $bfilter);
        
		foreach ($params as $param) 
        {
            if (!empty($param)) 
            {
                $p = explode(':', $param);
                $pName  = $p[0];
                $pValue = $p[1];
                if ($pName === 'price') 
                {
                    $p = explode('-', $pValue);
                    if ((int)$p[0] > 0 || (int)$p[1] > 0) {
                        $this->conditions->price = new stdClass();
                        $this->conditions->price->min = null;
                        $this->conditions->price->max = null;
                        $this->conditions->price->inputMin = null;
                        $this->conditions->price->inputMax = null;
                    }
                    if ((int)$p[0] > 0) {
                        $this->conditions->price->min = $this->currency->convert($p[0], $this->currency->getCode(), $this->config->get('config_currency'));
                        $this->conditions->price->inputMin = $p[0];
                    }
                    if ((int)$p[1] > 0) {
                        $this->conditions->price->max = $this->currency->convert($p[1], $this->currency->getCode(), $this->config->get('config_currency'));
                        $this->conditions->price->inputMax = $p[1];
                    }
                } 
                elseif ($pName === 'rating') 
                {
                    $this->conditions->rating = explode(',', $pValue);
                } 
                elseif ($pName === 'search')
                {
                    $this->conditions->search = $pValue;
                    $this->searchNameString = $pValue;
                    $this->searchTagString = $pValue;
                    $this->searchDescriptionString = $pValue;
                }
                else 
                {
                        $type = $map[substr($pName, 0, 1)];
                        $groupId = (int)substr($pName, 1);
                    if ($type) {
                        if (strpos($pValue, '-') !== false) {
                            $p = explode('-', $pValue);
                            if (isset($p[0]) && isset($p[1])) {
                                $this->conditions->{$type}[$groupId] = array('min' => $p[0], 'max' => $p[1]);
                            }
                        } else {
                            $this->conditions->{$type}[$groupId] = explode(',', $pValue);
                        }

                        if ($type !== 'rating') {
                            $type = strtoupper($type);
                            if (!isset($this->aggregate[$type])) {
                                $this->aggregate[$type] = array();
                            }
                            if (strpos($pValue, '-') !== false) {
                                $p = explode('-', $pValue);
                                $range = $this->_getSliderIntermediateValues($type, $groupId, $p[0], $p[1]);
                                if (!empty($range)) {
                                    $this->aggregate[$type][$groupId] = $range;
                                }
                            } else {
                                $this->aggregate[$type][$groupId] = explode(',', $pValue);
                            }
                        }
                    }
                }
            }
		}
	}
    
    private function _getSliderIntermediateValues($type, $id, $min, $max)
    {
        if (isset(self::$_cache['sliders']["$type-$id-$min-$max"])) {
            return self::$_cache['sliders']["$type-$id-$min-$max"];
        }
        
        $sql = new SqlStatement();
        
        if ($type === 'ATTRIBUTE') {
            $sql->select(array('id' => 'attribute_value_id'))
                ->from(self::ATTRIBUTE_VALUE_TABLE)
                ->where('attribute_id = ?', array($id))
                ->where('language_id = ?', (int)$this->config->get('config_language_id'));
        } elseif ($type === 'OPTION') {
            $sql->select(array('id' => 'option_value_id'))
                ->from('option_value')
                ->where('option_id = ?', array($id));
        } elseif ($type === 'FILTER') {
            $sql->select(array('id' => 'filter_id'))
                ->from('filter')
                ->where('filter_group_id = ?', array($id));
        } else {
            return;
        }
        
        $sql->order(array('sort_order'));
        
        if (!empty($min) && $min !== 'na') {
            $sql->where('sort_order >= ?', (int)$min);
        }
        if (!empty($max) && $max !== 'na') {
            $sql->where('sort_order <= ?', (int)$max);
        }
        
        $res = $this->db->query($sql);
        
        $output = array();
        if ($res->num_rows) {
            foreach ($res->rows as $row) {
                $output[] = $row['id'];
            }
        }
        self::$_cache['sliders']["$type-$id-$min-$max"] = $output;
        
        return $output;
    }
    
    /**
     * Fill Temporary Table
     * <br>
     * Core method, which fills out the results DB table with filtered products<br>
     * <b>Note:</b> price and rating filters aren't applied on that stage
     * 
     * @return void
     */
    private function _fillTmpTable()
    {
        $sql = new SqlStatement();
        $sql->select()
            ->from(array('p' => 'product'), array('p.*'));
        
        if ($this->subCategory) 
        {
            $sql->innerJoin(array('p2c' => 'product_to_category'), 'p.product_id = p2c.product_id')
                ->innerJoin(array('cp' => 'category_path'), 'cp.category_id = p2c.category_id')
                ->where('cp.path_id = ?', array($this->subCategory));
        }
        elseif ($this->topCategory) 
        {
            $sql->innerJoin(array('p2c' => 'product_to_category'), 'p.product_id = p2c.product_id')
                ->where('p2c.category_id = ?', array($this->topCategory));
        }
        
        $searchConditions = $this->_prepareSearchConditions();
        if (count($searchConditions)) {
            $sql->innerJoin(array('pd' => 'product_description'), 'pd.product_id = p.product_id')
                ->multipleWhere($searchConditions, 'OR');
        }
        
        $sql2 = new SqlStatement();
        $sql2->select(array(
                        'p.product_id',
                        'p.price',
                        'discount' => 'MIN(pd2.price)',
                        'special' => 'MIN(ps.price)',
                        'total' => 'AVG(rating)',
                        //@fixed_tax
                        //@percent_tax
                    ))
                ->from(array('p' => $sql))
                ->innerJoin(array('p2s' => 'product_to_store'), 'p.product_id = p2s.product_id')
                ->where('p2s.store_id = ?', (int)$this->config->get('config_store_id'))
                ->where("p.status = '1'")
                ->where('p.date_available <= NOW()')
                ->group(array('p.product_id'));
        
        if (count($this->aggregate)) {
            foreach ($this->aggregate as $type => $group) {
                foreach ($group as $groupId => $values) {
                    $tblAlias = strtolower(substr($type, 0, 1)) . $groupId;
                    $sql2->innerJoin(array($tblAlias => self::FILTERS_TABLE), 'p.product_id = ' . $tblAlias . '.product_id');
                    $sql2->where($tblAlias . '.type = ?', array($type));
                    $sql2->where($tblAlias . '.group_id = ?', array($groupId));
                    if ($type !== 'STOCK_STATUS') {
                        foreach ($values as $k => $val) {
                            $values[$k] = '\'' . $this->db->escape($val) . '\'';
                        }
                        $sql2->where($tblAlias . '.value IN (' . implode(', ', $values) . ')');
                    } else {
                        $terms = array();
                        foreach ($values as $stockSt) {
                            if ($stockSt == self::$IN_STOCK_STATUS) {
                                $terms[] = '(p.quantity > 0 OR p.stock_status_id = ' . self::$IN_STOCK_STATUS . ')';
                            } else {
                                $terms[] = '(' . $tblAlias . '.value = \'' . $this->db->escape($stockSt) . '\' AND p.quantity = 0)';
                            }
                        }

                        $sql2->where('(' . implode(' OR ', $terms) . ')');
                    }
                }
            }
        }
        
        if ( self::$HIDE_OUT_OF_STOCK ) {
            $vals = array();
            if (isset($this->aggregate['OPTION'])) {
                foreach ($this->aggregate['OPTION'] as $values) {
                    $vals = array_merge($vals, $values);
                }
            }
            $on = count($vals) 
                ? "p.product_id = pov.product_id AND pov.option_value_id IN (" . implode(',', $vals) . ")" 
                : "p.product_id = pov.product_id";
            
            $sql2->leftJoin(array('pov' => 'product_option_value'), $on)
                 ->where('( (pov.quantity IS NULL AND p.quantity > 0) OR pov.quantity > 0 )');
        }
        
        $sql2->leftJoin(array('pd2' => 'product_discount'), "pd2.product_id = p.product_id 
                        AND pd2.quantity = '1'
                        AND (pd2.date_start = '0000-00-00' OR pd2.date_start < NOW())
                        AND (pd2.date_end = '0000-00-00' OR pd2.date_end > NOW())
                        AND pd2.customer_group_id = '{$this->customerGroupId}'")
                ->leftJoin(array('ps' => 'product_special'), "ps.product_id = p.product_id 
                        AND (ps.date_end = '0000-00-00' OR ps.date_end > NOW())
                        AND (ps.date_start = '0000-00-00' OR ps.date_start < NOW())
                        AND ps.customer_group_id = '{$this->customerGroupId}'")
                ->leftJoin(array('r1' => 'review'), 'r1.product_id = p.product_id AND r1.status = 1');

        if ($this->config->get('config_tax')) {
            $sql2->select(array('fixed_tax', 'percent_tax'))
                 ->leftJoin(array('tr1' => $this->subquery->fixedTax), 'tr1.tax_class_id = p.tax_class_id')
                 ->leftJoin(array('tr2' => $this->subquery->percentTax), 'tr2.tax_class_id = p.tax_class_id');
        } else {
            $sql2->select(array('fixed_tax' => '0', 'percent_tax' => '0'));
        }
        $this->db->query('DROP TABLE IF EXISTS ' . DB_PREFIX . self::RESULTS_TABLE);
        $this->db->query('CREATE TEMPORARY TABLE ' . DB_PREFIX . self::RESULTS_TABLE . ' (PRIMARY KEY (`product_id`)) ' . $sql2);
    }
    
    /**
     * Calculates products amount per each filter INSIDE the list of filtered products
     * @return array Returns count per each type/group/value combination
     */
    private function _calculateTotalsIn()
    {
        $inStockStatus = self::$IN_STOCK_STATUS;
        $sql = new SqlStatement();
        $sql->select(array(
                'af.group_id', 
                'af.product_id', 
                'af.type',
                'value' => 'IF(type = \'STOCK_STATUS\' AND p.quantity > 0, ' . $inStockStatus . ', value)'))
            ->from(array('af' => self::FILTERS_TABLE))
            ->innerJoin(array('p' => 'product'), 'af.product_id = p.product_id')
            ->innerJoin(array('exclude' => self::RESULTS_TABLE), 'af.product_id = exclude.product_id');
        
        if ($this->conditions->price) {
            if ($this->conditions->price->min) {
                $sql->where("{$this->subquery->actualPrice} >= ?", array($this->conditions->price->min));
            }
            if ($this->conditions->price->max) {
                $sql->where("{$this->subquery->actualPrice} <= ?", array($this->conditions->price->max));
            }
        }
        
        if ($this->conditions->rating) {
            $sql->where('ROUND(total) IN ('. implode(',', $this->conditions->rating[0]) . ')');
        }
        
        if ( self::$HIDE_OUT_OF_STOCK ) {
            $sql->leftJoin(array('pov' => 'product_option_value'), 'af.product_id = pov.product_id AND af.value = pov.option_value_id AND af.type = "OPTION"')
                ->leftJoin(array('pov1' => 'product_option_value'), 'p.product_id = pov1.product_id')
                ->where('( (pov1.quantity IS NULL AND p.quantity > 0) OR '
                    . '(pov1.quantity > 0 AND af.type != "OPTION") OR '
                    . 'pov.quantity > 0 )')
                ->group(array('af.type', 'group_id', 'value', 'p.product_id'));
        }
        
        $sql2 = new SqlStatement();
        $sql2->select(array(
                'id' => "af.group_id", 
                'val' => 'af.value', 
                'c' => 'COUNT(*)',
                'type' => 'af.type',
            ))
            ->from(array('af' => $sql))
            ->group(array('af.type', 'af.group_id', 'af.value'));
        
        if (count($this->aggregate)) {
            foreach ($this->aggregate as $type => $group) {
                $sql2->where("((af.type = '$type' AND af.group_id NOT IN (" . implode(',', array_keys($group)) . ")) OR af.type != '$type')");
            }
        }
        
        $res = $this->db->query($sql2);
        
        return $res->rows;
    }
    
    /**
     * Calculates products amount per each filter OUTSIDE the list of filtered products
     * @return array Returns count per each type/group/value combination
     */
    private function _calculateTotalsOut()
    {
        $prodSql = new SqlStatement();
        $prodSql->select()
            ->distinct()
            ->from(array('p' => 'product'), array('p.*'));
        
        if ($this->subCategory) 
        {
            $prodSql->innerJoin(array('p2c' => 'product_to_category'), 'p.product_id = p2c.product_id')
                ->innerJoin(array('cp' => 'category_path'), 'cp.category_id = p2c.category_id')
                ->where('cp.path_id = ?', array($this->subCategory));
        }
        elseif ($this->topCategory) 
        {
            $prodSql->innerJoin(array('p2c' => 'product_to_category'), 'p.product_id = p2c.product_id')
                ->where('p2c.category_id = ?', array($this->topCategory));
        }
        
        $searchConditions = $this->_prepareSearchConditions();
        if (count($searchConditions)) {
            $prodSql->innerJoin(array('pd' => 'product_description'), 'pd.product_id = p.product_id')
                ->multipleWhere($searchConditions, 'OR');
        }
        
        if ($this->conditions->price) {
            $prodSql->leftJoin(array('pd2' => 'product_discount'), 'pd2.product_id = p.product_id', array('discount' => 'MIN(pd2.price)'))
                    ->leftJoin(array('ps' => 'product_special'), 'ps.product_id = p.product_id', array('special' => 'MIN(ps.price)'))
                    ->where("(pd2.product_id IS NULL OR (
                            pd2.quantity = '1'
                            AND (pd2.date_start = '0000-00-00' OR pd2.date_start < NOW())
                            AND (pd2.date_end = '0000-00-00' OR pd2.date_end > NOW())
                            AND pd2.customer_group_id = '{$this->customerGroupId}') )")
                    ->where("(ps.product_id IS NULL OR (
                            (ps.date_end = '0000-00-00' OR ps.date_end > NOW())
                            AND (ps.date_start = '0000-00-00' OR ps.date_start < NOW())
                            AND ps.customer_group_id = '{$this->customerGroupId}' ) )")
                    ->group(array('p.product_id'));
            
            if ($this->config->get('config_tax')) {
                $prodSql->select(array('fixed_tax', 'percent_tax'))
                     ->leftJoin(array('tr1' => $this->subquery->fixedTax), 'tr1.tax_class_id = p.tax_class_id')
                     ->leftJoin(array('tr2' => $this->subquery->percentTax), 'tr2.tax_class_id = p.tax_class_id');
            } else {
                $prodSql->select(array('fixed_tax' => '0', 'percent_tax' => '0'));
            }
            if ($this->conditions->price->min) {
                $prodSql->having("{$this->subquery->actualPrice} >= ?", array($this->conditions->price->min));
            }
            if ($this->conditions->price->max) {
                $prodSql->having("{$this->subquery->actualPrice} <= ?", array($this->conditions->price->max));
            }
        }
        
        if ($this->conditions->rating) {
            $prodSql->innerJoin(array('r1' => 'review'), 'r1.product_id = p.product_id', array('total' => 'AVG(rating)'))
                    ->where("r1.status = '1'")
                    ->having('ROUND(total) IN ('. implode(',', $this->conditions->rating[0]) . ')')
                    ->group(array('p.product_id'));
        }        

        if ($this->conditions->price || $this->conditions->rating) {
            $exclude = new SqlStatement();
            $exclude->select()->from(array('p' => self::RESULTS_TABLE));
            if ($this->conditions->price) {
                if ($this->conditions->price->min) {
                    $exclude->where("{$this->subquery->actualPrice} >= ?", array($this->conditions->price->min));
                }
                if ($this->conditions->price->max) {
                    $exclude->where("{$this->subquery->actualPrice} <= ?", array($this->conditions->price->max));
                }
            }

            if ($this->conditions->rating) {
                $exclude->where('ROUND(total) IN ('. implode(',', $this->conditions->rating[0]) . ')');
            }
        } else {
            $exclude = self::RESULTS_TABLE;
        }
        
        
        $inStock  = (int)self::$IN_STOCK_STATUS;
        
        $sql = new SqlStatement();
        $sql->select(array(
                'id'   => "af.group_id", 
                'val'  => "IF (af.type = 'STOCK_STATUS' AND p.quantity > 0, {$inStock}, af.value)", 
                'type' => 'af.type',
            ))
            ->from(array('af' => self::FILTERS_TABLE))
            ->innerJoin(array('p' => $prodSql), 'af.product_id = p.product_id')
            ->innerJoin(array('p2s' => 'product_to_store'), 'p.product_id = p2s.product_id')
            ->leftJoin(array('exclude' => $exclude), 'af.product_id = exclude.product_id')
            ->where('exclude.product_id IS NULL')
            ->where('p2s.store_id = ?', (int)$this->config->get('config_store_id'))
            ->where("p.status = '1'")
            ->where('p.date_available <= NOW()')
            ->group(array('af.product_id', 'af.type', 'af.group_id', 'af.value'));
        
        if (count($this->aggregate)) {
            $con = array();
            foreach ($this->aggregate as $type => $group) {
                foreach ($group as $groupId => $values) {
                    $a = strtolower(substr($type, 0, 1)) . $groupId;
                    $sql->innerJoin(array($a => self::FILTERS_TABLE), 'p.product_id = ' . $a . '.product_id')
                        ->where($a . '.type = ?', array($type))
                        ->where($a . '.group_id = ?', array($groupId));
                    // the following mess in conditions is caused by a need 
                    // of handling the correct stock status "In stock".
                    if ($type !== 'STOCK_STATUS') {
                        $where = "$a.value IN (" . implode(', ', $values) . ")";
                    } else {
                        $where = array();
                        foreach ($values as $val) {
                            $where[] = "($a.value = {$val}" . ($val != $inStock ? ' AND p.quantity = 0' : ' OR p.quantity > 0') . ")";
                        }
                        $where = "(" . implode(' OR ', $where) . ")";
                    }
                    $sql->where("({$where} OR af.aggregate_filter_id = $a.aggregate_filter_id)");
                }
                $con[] = "(af.type = '$type' AND af.group_id IN (" . implode(',', array_keys($group)) . "))";
            }
            $sql->multipleWhere($con);
        }
        
        
        if ( self::$HIDE_OUT_OF_STOCK ) {
            $sql->leftJoin(array('pov' => 'product_option_value'), 'af.product_id = pov.product_id AND af.value = pov.option_value_id AND af.type = "OPTION"')
                ->leftJoin(array('pov1' => 'product_option_value'), 'p.product_id = pov1.product_id');
            $vals = array();
            if (isset($this->aggregate['OPTION'])) {
                foreach ($this->aggregate['OPTION'] as $values) {
                    $vals = array_merge($vals, $values);
                }
            }
            $or = array(
                '(pov1.quantity IS NULL AND p.quantity > 0)',
                'pov.quantity > 0'
            );
            if (count($vals)) {
                $or[] = '(pov1.quantity > 0 AND pov1.option_value_id IN (' . implode(',', $vals) . '))';
            } else {
                $or[] = '(pov1.quantity > 0 AND af.type != "OPTION")';
            }
            $sql->where('(' . implode(' OR ', $or) . ')');
        }
        
        $groupSql = new SqlStatement();
        $groupSql->select(array('id', 'val', 'c' => 'COUNT(*)', 'type'))
                ->from(array('tt' => $sql))
                ->group(array('type', 'id', 'val'));
        
        $res = $this->db->query($groupSql);

        return $res->rows;
    }
    
    /**
     * Calculates products amount per each rating score
     * @return array Returns count per each rating score
     */
    private function _calculateTotalsByRating()
    {
        $sql = new SqlStatement();
        $sql->select(array(
                'id'   => 'ROUND(total)',
                'val'  => 'ROUND(total)',
                'c'    => 'COUNT(*)',
                'type' => "'RATING'"))
            ->from(array('p' => self::RESULTS_TABLE))
            ->group(array('ROUND(total)'));
        
        if ($this->conditions->price) {
            if ($this->conditions->price->min) {
                $sql->where("{$this->subquery->actualPrice} >= ?", array($this->conditions->price->min));
            }
            if ($this->conditions->price->max) {
                $sql->where("{$this->subquery->actualPrice} <= ?", array($this->conditions->price->max));
            }
        }
        
        $res = $this->db->query($sql);
//        echo $sql;
        return $res->rows;
    }
    
    /**
     * Prepare sub-queries
     * @see $subquery property
     * @return void 
     */
    private function _prepareSubQueries()
    {
        $arr = array();
        $shipCountryId = isset($this->session->data['shipping_country_id']) ? $this->session->data['shipping_country_id'] : (($this->config->get('config_tax_default') == 'shipping') ? $this->config->get('config_country_id') : null); 
        $shipZoneId    = isset($this->session->data['shipping_zone_id']) ? $this->session->data['shipping_zone_id'] : (($this->config->get('config_tax_default') == 'shipping') ? $this->config->get('config_zone_id') : null); 
        $paymCountryId = isset($this->session->data['payment_country_id']) ? $this->session->data['payment_country_id'] : (($this->config->get('config_tax_default') == 'payment') ? $this->config->get('config_country_id') : null); 
        $paymZoneId    = isset($this->session->data['payment_zone_id']) ? $this->session->data['payment_zone_id'] : (($this->config->get('config_tax_default') == 'payment') ? $this->config->get('config_zone_id') : null); 
        
        if ($shipCountryId || $shipZoneId) {
            $arr[] = "tr1.based = 'shipping' AND z2gz.country_id = '" . (int)$shipCountryId . "' AND z2gz.zone_id IN ('0', '" . (int)$shipZoneId . "')";
        }
        if ($paymCountryId || $paymZoneId) {
            $arr[] = "tr1.based = 'payment' AND z2gz.country_id = '" . (int)$paymCountryId . "' AND z2gz.zone_id IN ('0', '" . (int)$paymZoneId . "')";
        }
        
        $arr[] = "tr1.based = 'store' AND z2gz.country_id = '" . (int)$this->config->get('config_country_id') . "' AND z2gz.zone_id IN ('0', '" . (int)$this->config->get('config_zone_id') . "')";
        
        // subquery for retrieving fixed tax rates

        $sql = new SqlStatement();
        $sub = new SqlStatement();

		$sub->select(array('tr1.tax_class_id', 'rate'))
            ->distinct()
			->from(array('tr1' => 'tax_rule'))
			->leftJoin(array('tr2' => 'tax_rate'), 'tr1.tax_rate_id = tr2.tax_rate_id')
			->innerJoin(array('tr2cg' => 'tax_rate_to_customer_group'), 'tr2.tax_rate_id = tr2cg.tax_rate_id')
			->leftJoin(array('z2gz' => 'zone_to_geo_zone'), 'tr2.geo_zone_id = z2gz.geo_zone_id')
			->leftJoin(array('gz' => 'geo_zone'), 'tr2.geo_zone_id = gz.geo_zone_id')
			->where('tr2.type = \'F\'')
			->where('tr2cg.customer_group_id = ?', $this->customerGroupId);
			
		if (count($arr)) {
			$terms = '((' . implode(') OR (', $arr) . '))';
			$sub->where($terms);
		} else {
			$sub->where(0);
		}
        $sql->select(array('fixed_tax' => 'SUM(t.rate)', 't.tax_class_id'))
            ->from(array('t' => $sub))
            ->group(array('t.tax_class_id'));
        $this->subquery->fixedTax = $sql;
        
        // subquery for retrieving percent tax rates
        
        $sql = new SqlStatement();
        $sub = new SqlStatement();
        
        $sub->select(array('tr1.tax_class_id', 'rate'))
            ->distinct()
            ->from(array('tr1' => 'tax_rule'))
			->leftJoin(array('tr2' => 'tax_rate'), 'tr1.tax_rate_id = tr2.tax_rate_id')
			->innerJoin(array('tr2cg' => 'tax_rate_to_customer_group'), 'tr2.tax_rate_id = tr2cg.tax_rate_id')
			->leftJoin(array('z2gz' => 'zone_to_geo_zone'), 'tr2.geo_zone_id = z2gz.geo_zone_id')
			->leftJoin(array('gz' => 'geo_zone'), 'tr2.geo_zone_id = gz.geo_zone_id')
			->where('tr2.type = \'P\'')
			->where('tr2cg.customer_group_id = ?', $this->customerGroupId);
			
		if (count($arr)) {
			$terms = '((' . implode(') OR (', $arr) . '))';
			$sub->where($terms);
		} else {
			$sub->where(0);
		}
		$sql->select(array('tax_class_id', 'percent_tax' => 'SUM(rate)'))
            ->from(array('t' => $sub))
            ->group(array('t.tax_class_id'));
        $this->subquery->percentTax = $sql;

        // calculation for actual price (taking into account taxes, specials and discounts)
        
        $this->subquery->actualPrice = "(IF(special IS NOT NULL, special, "
                . "IF(discount IS NOT NULL, discount, p.price)) "
                . "* (1 + IFNULL(percent_tax, 0)/100) + IFNULL(fixed_tax, 0))";
    }
    
    private function _prepareSearchConditions()
    {
        $search = array();
        
        if (!empty($this->searchNameString)) {
            $words = explode(' ', trim(preg_replace('/\s\s+/', ' ', $this->searchNameString)));
            $nameCond = array();
            foreach ($words as $word) {
                $nameCond[] = "pd.name LIKE '%" . $this->db->escape($word) . "%'";
            }
            $search = array('(' . implode(' AND ', $nameCond) . ')');
            
            $search[] = array('LCASE(p.model) = ?', $this->searchNameString);
            $search[] = array('LCASE(p.sku) = ?', $this->searchNameString);
            $search[] = array('LCASE(p.upc) = ?', $this->searchNameString);
            $search[] = array('LCASE(p.ean) = ?', $this->searchNameString);
            $search[] = array('LCASE(p.jan) = ?', $this->searchNameString);
            $search[] = array('LCASE(p.isbn) = ?', $this->searchNameString);
            $search[] = array('LCASE(p.mpn) = ?', $this->searchNameString);
            
        }
        if (!empty($this->searchTagString)) {
            $search[] = array('pd.tag LIKE "%' . $this->db->escape($this->searchTagString) . '%"');
        }
        if (!empty($this->searchDescriptionString)) {
            $search[] = array('pd.description LIKE "%' . $this->db->escape($this->searchDescriptionString) . '%"');
        }
        
        return $search;
    }
    
    /**
     * Set Data
     * 
     * @param array $data initial data for the model
     * @return void 
     */
    public function setData($data = array())
    {
        if (isset($data['filter_category_id'])) {
            if (isset($data['filter_sub_category']) && $data['filter_sub_category']) { 
                $this->subCategory = (int)$data['filter_category_id'];
            } else {
                $this->topCategory = (int)$data['filter_category_id'];
            }
        }
        if (isset($data['filter_name']) && empty($this->searchNameString)) {
            $this->searchNameString = utf8_strtolower($data['filter_name']);
        }
        if (isset($data['filter_tag']) && empty($this->searchTagString)) {
            $this->searchTagString = utf8_strtolower($data['filter_tag']);
        }
        if (isset($data['filter_description']) && empty($this->searchDescriptionString)) {
            $this->searchDescriptionString = utf8_strtolower($data['filter_name']);
        }
        if (empty($this->conditions->manufacturer)) {
            if (isset($data['filter_manufacturer_id'])
                    && !empty($data['filter_manufacturer_id'])) {
                $this->conditions->manufacturer[0] = array((int) $data['filter_manufacturer_id']);
                $this->aggregate['MANUFACTURER'] = array();
                $this->aggregate['MANUFACTURER'][0] = array((int) $data['filter_manufacturer_id']);
            // hack - in order to pass the parameter to our model the global GET variable 
            // is created, since there is no more convenient way to do this thougth
            // product/category controller and product model
            } elseif (isset($this->request->get['manufacturer_id']) 
                    && !empty($this->request->get['manufacturer_id'])) {
                $this->conditions->manufacturer[0] = array((int) $this->request->get['manufacturer_id']);
                $this->aggregate['MANUFACTURER'] = array();
                $this->aggregate['MANUFACTURER'][0] = array((int) $this->request->get['manufacturer_id']);
            }
        }
        
        if (!self::$TMP_TABLE_CREATED) {
            $this->_fillTmpTable();
            self::$TMP_TABLE_CREATED = true;
        }
        
        if (isset($data['limit'])) {
            $this->productsLimit = $data['limit'];
        }
        if (isset($data['start'])) {
            $this->productsStart = $data['start'];
        }
        
        $sortFields = array(
			'LCASE(pd.name)'  => 'pd.name',
			'LCASE(pp.model)' => 'p.model',
			'pp.quantity'     => 'p.quantity',
            "{$this->subquery->actualPrice}" => 'p.price',
			'p.total'         => 'rating',
			'pp.sort_order'   => 'p.sort_order',
			'pp.date_added'   =>'p.date_added'
		);	
		
        $order = (isset($data['order']) && strtoupper($data['order']) == 'DESC') ? 'DESC' : 'ASC';
        
        if (isset($data['sort'])) {
            $field = array_search($data['sort'], $sortFields);
            if ($field !== false) {
                $this->sortOrder = array();
                $this->sortOrder[] = "$field $order";
                if ($field !== 'pd.name') {
                    $this->sortOrder[] = "LCASE(pd.name) $order";
                }
            }
        }
    }
    
    /**
     * Get Conditions
     * <br>
     * Getter for the private property $conditions
     * @return stdClass
     */
    public function getConditions()
    {
        return $this->conditions;
    }
    
    /**
     * Calculates amount of products per each filter
     * <br>
     * Returns Array with the following structure
     * <pre>
     * array(
     *      array( 
     *          'id'  => [first letter of type + group ID], 
     *          'val' => [value],
     *          'c'   => [count of products]
     *      )
     * )
     * </pre>
     * @return array 
     */
    public function calculateTotals()
    {
        $in  = $this->_calculateTotalsIn();
        $out = $this->_calculateTotalsOut();
        $rating = $this->_calculateTotalsByRating();
        
        $result = array_merge($in, $out, $rating);
        $output = array();
        
        if (count($result)) {
            foreach ($result as $row) {
                $id = in_array($row['type'], array('MANUFACTURER', 'STOCK_STATUS', 'RATING')) ? '0' : $row['id'];
                $gid = strtolower(substr($row['type'], 0, 1)) . $id;
                if (!isset($output[$gid])) {
                    $output[$gid] = array();
                }
                $output[$gid][$row['val']] = $row['c'];
            }
        }
        
        return $output;
    }
    
    /**
     * Prepare Query String For Category
	 * <br />
     * The method applies BrainyFilter conditions to the query for products.
     * It is injected to the ModelCatalogProduct::getProducts() via vQmod
     * 
     * @return string SQL query string
	 * @todo Make sure the new method is compatable with ocstore
     */
    public function prepareQueryForCategory()
    {
        $sql = new SqlStatement();
        $sql->select(array('p.*'))
            ->from(array('p' => self::RESULTS_TABLE))
            ->innerJoin(array('pp' => 'product'), 'p.product_id = pp.product_id')
            ->innerJoin(array('pd' => 'product_description'), 'p.product_id = pd.product_id')
            ->where('pd.language_id = ?', (int)$this->config->get('config_language_id'))
            ->order($this->sortOrder)
            ->limit($this->productsLimit, $this->productsStart);
        
        if ($this->conditions->price) {
            if ($this->conditions->price->min) {
                $sql->where("{$this->subquery->actualPrice} >= ?", array($this->conditions->price->min));
            }
            if ($this->conditions->price->max) {
                $sql->where("{$this->subquery->actualPrice} <= ?", array($this->conditions->price->max));
            }
        }
        
        if ($this->conditions->rating) {
            $sql->where('ROUND(total) IN ('. implode(',', $this->conditions->rating[0]) . ')');
        }   
        
        return $sql;
    }
    
	/**
     * Prepare Query For Total
	 * <br />
     * Generates query string for calculation of total amount of found products
     * @return string SQL query string
     */
    public function prepareQueryForTotal()
    {
        $sql = $this->prepareQueryForCategory();
        $sql->clean('limit')->clean('select')->select(array('total' => 'COUNT(*)'));
        
        return $sql;
    }
    
    /**
     * Get MIN/MAX category price
	 * <br />
     * The method calculates min/max price taking into account special offers, 
     * discounts and taxes
     * 
     * @return array Associative array with min and max fields
     */
    public function getMinMaxCategoryPrice()
    {
        if (isset(self::$_cache['minmaxprice'])) {
            return self::$_cache['minmaxprice'];
        }
        $sql = new SqlStatement();
        $sql->select(array(
            'min' => "MIN({$this->subquery->actualPrice})",
            'max' => "MAX({$this->subquery->actualPrice})"))
            ->from(array('p' => self::RESULTS_TABLE));
        
        if ($this->conditions->rating) {
            $sql->where('ROUND(total) IN ('. implode(',', $this->conditions->rating[0]) . ')');
        } 
        
        $res = $this->db->query($sql);
        self::$_cache['minmaxprice'] = $res->row;
        
        return $res->row;
    }

    /**
     * Get Attributes
     * 
     * @return array Returns array of existed attributes in the given category 
     * and all their values
     */
    public function getAttributes()
    {
        if (isset(self::$_cache['attributes'])) {
            return self::$_cache['attributes'];
        }
        $prodSql = new SqlStatement();
        $prodSql->select()
                ->from(array('p' => 'product'), array('p.*'));
        if ($this->subCategory) 
        {
            $prodSql->innerJoin(array('p2c' => 'product_to_category'), 'p.product_id = p2c.product_id')
                ->innerJoin(array('cp' => 'category_path'), 'cp.category_id = p2c.category_id')
                ->where('cp.path_id = ?', array($this->subCategory));
        }
        elseif ($this->topCategory) 
        {
            $prodSql->innerJoin(array('p2c' => 'product_to_category'), 'p.product_id = p2c.product_id')
                ->where('p2c.category_id = ?', array($this->topCategory));
        }
        if ($this->manufacturer)
        {
            $prodSql->where('p.manufacturer_id = ?', array($this->manufacturer));
        }
            
        $sql = new SqlStatement();
        $sql->select(array(
                'group_id'   => 'a.attribute_group_id', 
                'attr_id'    => 'av.attribute_id', 
                'val_id'     => 'av.attribute_value_id', 
                'group_name' => 'agd.name',
                'attr_name'  => 'ad.name',
                'val_sort'   => 'av.sort_order',
                'av.value',
            ))
//            ->distinct()
            ->from(array('af' => self::FILTERS_TABLE))
            ->innerJoin(array('p' => $prodSql), 'af.product_id = p.product_id')
            ->innerJoin(array('av' => self::ATTRIBUTE_VALUE_TABLE), 'af.value = av.attribute_value_id')
            ->innerJoin(array('a' => 'attribute'), 'a.attribute_id = av.attribute_id')
            ->innerJoin(array('ad' => 'attribute_description'), 'ad.attribute_id = a.attribute_id')
            ->innerJoin(array('ag' => 'attribute_group'), 'ag.attribute_group_id = a.attribute_group_id')
            ->innerJoin(array('agd' => 'attribute_group_description'), 'agd.attribute_group_id = a.attribute_group_id')
            ->innerJoin(array('ps' => 'product_to_store'), 'p.product_id = ps.product_id')
            ->where('agd.language_id = ?', (int)$this->config->get('config_language_id'))
            ->where('ad.language_id = ?', (int)$this->config->get('config_language_id'))
            ->where('av.language_id = ?', (int)$this->config->get('config_language_id'))
            ->where('ps.store_id = ?', (int)$this->config->get('config_store_id'))
            ->where('af.type = ?', 'ATTRIBUTE')
            ->where('p.status = 1')
            ->group(array('av.attribute_value_id'))
            ->order(array('ag.sort_order', 'ag.attribute_group_id', 'a.sort_order', 'ad.name', 'av.sort_order', 'av.value'));
        
        $res = $this->db->query($sql);
        
        $output = array();
        
        if (count($res->rows)) {
            foreach ($res->rows as $row) {
                    $r = array(
                        'name' => $row['value'],
                        'id' => $row['val_id'],
                        'sort' => $row['val_sort'],
                    );

                    if (!isset($output[$row['attr_id']])) {
                        $output[$row['attr_id']] = array(
                            'name' => $row['attr_name'],
                            'group_id' => $row['group_id'],
                            'group' => $row['group_name'],
                            'values' => array()
                        );
                    }
                    $output[$row['attr_id']]['values'][] = $r;
            }
        }
        self::$_cache['attributes'] = $output;

        return $output;
    }
    
    /**
     * Get Manufacturers
	 * <br />
     * Retrieves a list of manufacturers for the given category ID
     * 
     * @param array $data Input parameters
     * @return mixed Array of manufacturers for the given category ID if found. 
     * Otherwise returns FALSE
     */
	public function getManufacturers()
	{
        if (isset(self::$_cache['manufacturers'])) {
            return self::$_cache['manufacturers'];
        }
        $sql = new SqlStatement();
        $sql->select(array('id' => 'm.manufacturer_id', 'm.name'))
            ->distinct()
            ->from(array('m' => 'manufacturer'))
            ->innerJoin(array('p' => 'product'), 'm.manufacturer_id = p.manufacturer_id')
            ->innerJoin(array('m2s' => 'manufacturer_to_store'), 'm.manufacturer_id = m2s.manufacturer_id')
            ->where('p.status = 1')
            ->where('p.date_available <= NOW()')
            ->where('m2s.store_id = ?', (int) $this->config->get('config_store_id'))
            ->order(array('m.sort_order', 'm.name')); 
        
        if ($this->subCategory) 
        {
            $sql->innerJoin(array('p2c' => 'product_to_category'), 'p.product_id = p2c.product_id')
                ->innerJoin(array('cp' => 'category_path'), 'cp.category_id = p2c.category_id')
                ->where('cp.path_id = ?', array($this->subCategory));
        }
        elseif ($this->topCategory) 
        {
            $sql->innerJoin(array('p2c' => 'product_to_category'), 'p.product_id = p2c.product_id')
                ->where('p2c.category_id = ?', array($this->topCategory));
        }

		$query = $this->db->query($sql);
        self::$_cache['manufacturers'] = $query->rows;
        
        return $query->rows;
	}
    
    /**
     * Get Stock Statuses
     * 
     * @return array Returns array of existed stock statuses
     */
	public function getStockStatuses()
	{
        if (isset(self::$_cache['stock_statuses'])) {
            return self::$_cache['stock_statuses'];
        }
		$sql = new SqlStatement();
        $sql->select(array('id' => 'stock_status_id', 'name'))
                ->from('stock_status')
                ->where('language_id = ?', (int) $this->config->get('config_language_id'));
		
		$query = $this->db->query($sql);
		
        self::$_cache['stock_statuses'] = $query->rows;
        
        return $query->rows;
	}
    
    /**
     * Get Options
     * 
     * @return array Returns array of existed options in the given category 
     * and all their values
     */
	public function getOptions()
	{
        if (isset(self::$_cache['options'])) {
            return self::$_cache['options'];
        }
		$output = array();
        
        $sql = new SqlStatement();
        
        $columns = array('namegroup' => 'od.name', 'ovd.name', 'ovd.option_value_id', 'pov.option_id', 'ov.image', 'ov.sort_order');
        
        $sql->select($columns)
            ->from(array('p' => 'product'))
            ->innerJoin(array('p2s' => 'product_to_store'), 'p.product_id = p2s.product_id')
            ->innerJoin(array('pov' => 'product_option_value'), 'p.product_id = pov.product_id')
            ->innerJoin(array('od' => 'option_description'), 'pov.option_id = od.option_id')
            ->innerJoin(array('ovd' => 'option_value_description'), 'pov.option_value_id = ovd.option_value_id')
            ->innerJoin(array('o' => 'option'), 'pov.option_id = o.option_id')
            ->innerJoin(array('ov' => 'option_value'), 'pov.option_value_id = ov.option_value_id')
            ->where('p.status = 1')
            ->where('p.date_available <= NOW()')
            ->where('ovd.language_id = ?', (int) $this->config->get('config_language_id'))
            ->where('od.language_id = ?', (int) $this->config->get('config_language_id'))
            ->where('p2s.store_id = ?', (int) $this->config->get('config_store_id'))
            ->group(array('pov.option_value_id'))
            ->order(array('o.sort_order', 'ov.sort_order', 'od.name', 'ovd.name')); 
 
        if ($this->subCategory) 
        {
            $sql->innerJoin(array('p2c' => 'product_to_category'), 'p.product_id = p2c.product_id')
                ->innerJoin(array('cp' => 'category_path'), 'cp.category_id = p2c.category_id')
                ->where('cp.path_id = ?', array($this->subCategory));
        }
        elseif ($this->topCategory) 
        {
            $sql->innerJoin(array('p2c' => 'product_to_category'), 'p.product_id = p2c.product_id')
                ->where('p2c.category_id = ?', array($this->topCategory));
        }
        if ($this->manufacturer)
        {
            $sql->where('p.manufacturer_id = ?', array($this->manufacturer));
        }
		$query = $this->db->query($sql);

		foreach ($query->rows as $row) {

            $r = array(
                'name' => $row['name'],
                'id' => $row['option_value_id'],
                'sort' => $row['sort_order']
            );
            
            if (isset($row['image'])) {
                $r['image'] = $row['image'];
            }
            if (!isset($output[$row['option_id']])) {
                $output[$row['option_id']] = array(
                    'name' => $row['namegroup'],
                    'values' => array()
                );
            }
            $output[$row['option_id']]['values'][] = $r;
        }
        self::$_cache['options'] = $output;
        
        return $output;
	}
    
    /**
     * Get Filters
     * 
     * @return array Returns array of existed filters in the given category 
     * and all their values
     */
    public function getFilters()
    {
        if (isset(self::$_cache['filters'])) {
            return self::$_cache['filters'];
        }
        $sql = new SqlStatement();
        
        $sql->select(array('namegroup' => 'fgd.name', 'fd.name', 'f.filter_id', 'fg.filter_group_id', 'f.sort_order'))
            ->from(array('p' => 'product'))
            ->innerJoin(array('pf' => 'product_filter'), 'p.product_id = pf.product_id')
            ->innerJoin(array('f' => 'filter'), 'f.filter_id = pf.filter_id')
            ->innerJoin(array('fd' => 'filter_description'), 'fd.filter_id = pf.filter_id')
            ->innerJoin(array('fg' => 'filter_group'), 'fg.filter_group_id = fd.filter_group_id')
            ->innerJoin(array('fgd' => 'filter_group_description'), 'fd.filter_group_id = fgd.filter_group_id')
            ->innerJoin(array('p2s' => 'product_to_store'), 'p.product_id = p2s.product_id')
            ->where('p.status = 1')
            ->where('p.date_available <= NOW()')
            ->where('fd.language_id = ?', (int) $this->config->get('config_language_id'))
            ->where('fgd.language_id = ?', (int) $this->config->get('config_language_id'))
            ->where('p2s.store_id = ?', (int) $this->config->get('config_store_id'))
            ->group(array('f.filter_id'))
            ->order(array('fg.sort_order', 'f.sort_order', 'fgd.name', 'fd.name'));
        
        if ($this->subCategory) 
        {
            $sql->innerJoin(array('p2c' => 'product_to_category'), 'p.product_id = p2c.product_id')
                ->innerJoin(array('cp' => 'category_path'), 'cp.category_id = p2c.category_id')
                ->where('cp.path_id = ?', array($this->subCategory));
        }
        elseif ($this->topCategory) 
        {
            $sql->innerJoin(array('p2c' => 'product_to_category'), 'p.product_id = p2c.product_id')
                ->where('p2c.category_id = ?', array($this->topCategory));
        }
        if ($this->manufacturer)
        {
            $sql->where('p.manufacturer_id = ?', array($this->manufacturer));
        }
        $query = $this->db->query($sql);
        
        $output = array();
        
		foreach ($query->rows as $row) {

            $r = array(
                'name' => $row['name'],
                'id' => $row['filter_id'],
                'sort' => $row['sort_order']
            );
            
            if (!isset($output[$row['filter_group_id']])) {
                $output[$row['filter_group_id']] = array(
                    'name' => $row['namegroup'],
                    'values' => array()
                );
            }
            $output[$row['filter_group_id']]['values'][] = $r;
        }
        self::$_cache['filters'] = $output;
        
        return $output;
    }
    
    public function getCategories()
    {
        $sql = new SqlStatement();
        
        $sql->select(array('cd.name', 'id' => 'c.category_id', 'pid' => 'c.parent_id'))
            ->from(array('c' => 'category'))
            ->innerJoin(array('cd' => 'category_description'), 'c.category_id = cd.category_id')
            ->where('cd.language_id = ?', (int) $this->config->get('config_language_id'))
            ->order(array('c.parent_id', 'c.sort_order', 'LCASE(cd.name)'));
        
        $res = $this->db->query($sql);
        $output = array();
        foreach ($res->rows as $row) {
            $output[$row['id']] = array(
                'name' => $row['name'],
                'pid'  => $row['pid'],
            );
        }
        return $output;
    }
}