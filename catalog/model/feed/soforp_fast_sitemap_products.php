<?php

require_once ( dirname(__FILE__) . "/soforp_fast_sitemap_items.php");

class ModelFeedSoforpFastSitemapProducts  extends ModelFeedSoforpFastSitemapItems  {

    public function __construct($registry) {
        parent::__construct($registry);
        $this->_itemName = "Продукты";
    }

    public function getItemsCount(){

        static $_productsCount = -1;

        if( $_productsCount != -1 )
            return $_productsCount;

        $sql = "select count(p.product_id) as `cnt` from `" . DB_PREFIX . "product` p ";
        if( "1" == $this->multistore_status ){
            $sql .= " inner join product_to_store ps on (p.product_id = ps.product_id) ";
        }

        $sql .= " WHERE p.status=1 ";
        if( "1" == $this->multistore_status ){
            $sql .= " and ps.store_id = " . (int)$this->store_id;
        }

        $query = $this->db->query($sql);
        if(!$query->rows) {
            $_productsCount = 0;
            return $_productsCount;
        }

        $_productsCount = (int)$query->rows[0]["cnt"];
        return $_productsCount;
    }

    public function getPartsCount() {
        return ceil( $this->getItemsCount() / $this->partLimit );
    }


    /*
     * Вытаскивает из базы дерево категорий в формате ассоциативного списка категория => родитель
     */
    protected function getCategories(){

        static $_categories = -1;

        if( $_categories != -1 )
            return $_categories;

        // От главной страницы каждый товар может отделять целая пачка категорий
        // При этом мы знаем только ту, в которой товар находится.
        // А чтобы построить ЧПУ, нужно знать все.
        $queryBegin = microtime(true);
        $sql = "SELECT category_id, parent_id FROM `" . DB_PREFIX . "category`";
        $this->log("Запрос по категориям выполнен за " . round(microtime(true) - $queryBegin,3)  . " сек" );

        $queryBegin = microtime(true);
        $categories = array();
        foreach($this->db->query($sql)->rows as $row ){
            $categories[$row['category_id']] = $row['parent_id'];
        }
        $_categories = $categories;

        $this->log("Кеширование путей категорий выполнено за " . round(microtime(true) - $queryBegin,3)  . " сек" );
        return $_categories;
    }

    protected function getItemsNoSeo($part = -1){
        $output = "";

        $this->log("Генерация продуктов стартовала!" );
        $begin = microtime(true);

        $sql = "select p.product_id, 0 as category_id, p.date_added, p.date_modified from `" . DB_PREFIX . "product` p ";
        if( "1" == $this->multistore_status ){
            $sql .= " inner join product_to_store ps on (p.product_id = ps.product_id) ";
        }
        $sql .= " WHERE p.status=1 ";
        if( "1" == $this->multistore_status ){
            $sql .= " and ps.store_id = " . (int)$this->store_id;
        }
        if( $part != -1 ){
            $sql .= " ORDER BY p.product_id ASC ";
            $sql .= " limit " . (($part-1)*$this->product_part) . ", " . ($part*$this->product_part - 1);
        }

        $queryBegin = microtime(true);
        $query = $this->db->query($sql);
        $this->log("Запрос по продуктам выполнен за " . round(microtime(true) - $queryBegin,3)  . " сек. Получено " . $query->num_rows . " записей." );

        $count = 1;
        $partBegin = microtime(true);
        foreach ($query->rows as $product) {

            $url = $this->store_host . "index.php?route=product/product&amp;product_id=" . $product['product_id'];

            $output .= $this->getItem( $url, substr(max($product['date_added'], $product['date_modified']), 0, 10), "weekly", "1.0");

            // дополнительные замеры производительности
            $count++;
            if( ($count % 5000) == 0 ) {
                $this->log("Обработано 5000 продуктов за  " . round(microtime(true) - $partBegin,3)  . " сек" );
                $partBegin = microtime(true);
            }
        }

        $this->log("Генерация продуктов выполнена за " . round(microtime(true) - $begin,3)  . " сек" );

        unset($query);
        return $output;
    }

    protected function getItemsSeoUrl($part = -1){
        $output = "";

        $this->log("Генерация продуктов стартовала!" );
        $begin = microtime(true);

        $sql = "select p.product_id, p.date_added, p.date_modified from `" . DB_PREFIX . "product` p ";
        if( "1" == $this->multistore_status ){
            $sql .= " inner join product_to_store ps on (p.product_id = ps.product_id) ";
        }
        $sql .= " WHERE p.status=1 ";
        if( "1" == $this->multistore_status ){
            $sql .= " and ps.store_id = " . (int)$this->store_id;
        }
        if( $part != -1 ){
            $sql .= " ORDER BY p.product_id ASC ";
            $sql .= " limit " . (($part-1)*$this->product_part) . ", " . ($part*$this->product_part - 1);
        }

        $queryBegin = microtime(true);
        $query = $this->db->query($sql);
        $this->log("Запрос по продуктам выполнен за " . round(microtime(true) - $queryBegin,3)  . " сек. Получено " . $query->num_rows . " записей." );

        $count = 1;
        $partBegin = microtime(true);
        $productKeywords = $this->getProductKeywords();
        $this->registry->set("product_keyword_cache",1);
        foreach ($query->rows as $product) {

            if( isset($productKeywords[$product["product_id"]])){
                $this->registry->set("product_keyword", $productKeywords[$product["product_id"]] );
                $this->registry->set("product_keyword_not_empty",1);
            } else {
                $this->registry->set("product_keyword_not_empty",0);
            }

            $this->registry->set("product_vqmod_patch", 0 );
            $url = str_replace('&', '&amp;', str_replace('&amp;', '&', $this->url->link('product/product', 'product_id=' . $product['product_id'])));
            if( "0" == $this->registry->get("product_vqmod_patch") ) {
                $this->log("ERROR! Патч seo_url не работает!" );
                // exit();
            }

            $output .= $this->getItem( $url, substr(max($product['date_added'], $product['date_modified']), 0, 10), "weekly", "1.0");

            // дополнительные замеры производительности
            $count++;
            if( ($count % 5000) == 0 ) {
                $this->log("Обработано 5000 продуктов за  " . round(microtime(true) - $partBegin,3)  . " сек" );
                $partBegin = microtime(true);
            }
        }

        $this->log("Генерация продуктов выполнена за " . round(microtime(true) - $begin,3)  . " сек" );

        unset($query);
        unset($keywords);
        return $output;
    }

    protected function getItemsSeoPro($part = -1){
        $output = "";

        $this->log("Генерация продуктов стартовала!" );
        $begin = microtime(true);

        //$sql = "select p.product_id, (SELECT pc1.category_id FROM `" . DB_PREFIX . "product_to_category` pc1 WHERE pc1.product_id = p.product_id and pc1.main_category = 1 LIMIT 1) as main_category_id, (SELECT pc2.category_id FROM `" . DB_PREFIX . "product_to_category` pc2 WHERE pc2.product_id = p.product_id and pc2.main_category = 0 LIMIT 1) as slave_category_id, p.date_added, p.date_modified from `" . DB_PREFIX . "product` p ";
        $sql = "select p.product_id, (SELECT pc1.category_id FROM `" . DB_PREFIX . "product_to_category` pc1 WHERE pc1.product_id = p.product_id and pc1.main_category = 1 LIMIT 1) as main_category_id, (SELECT pc2.category_id FROM `" . DB_PREFIX . "product_to_category` pc2 WHERE pc2.product_id = p.product_id and pc2.main_category = 0 LIMIT 1) as slave_category_id, substring(if(p.date_added> p.date_modified,p.date_added,p.date_modified),1,10) as date_modified from `" . DB_PREFIX . "product` p ";
        if( "1" == $this->multistore_status ){
            $sql .= " inner join product_to_store ps on (p.product_id = ps.product_id) ";
        }
        $sql .= " WHERE p.status=1 ";
        if( "1" == $this->multistore_status ){
            $sql .= " and ps.store_id = " . (int)$this->store_id;
        }
        if( $part != -1 ){
            $sql .= " ORDER BY p.product_id ASC ";
            $sql .= " limit " . (($part-1)*$this->partLimit) . ", " . ($part*$this->partLimit + 1);
        }

        $queryBegin = microtime(true);
        $query = $this->db->query($sql);
        $this->log("Запрос по продуктам выполнен за " . round(microtime(true) - $queryBegin,3)  . " сек. Получено " . $query->num_rows . " записей." );

        $productKeywords = $this->getProductKeywords();
        $categoryKeywords = $this->getCategoryKeywords();

        $count = 1;
        $partBegin = microtime(true);
        foreach ($query->rows as $product) {
            $product_id = $product['product_id'];

            if( isset($product["main_category_id"]) ) {
                $category_id = $product["main_category_id"];
                //$this->log("Используем главную категорию товара: $category_id");
            } else if( isset($product["slave_category_id"]) ) {
                $category_id = $product["slave_category_id"];
                //$this->log("Используем подчиненную категорию товара: $category_id");
            } else {
                $category_id = 0;
                //$this->log("Внимание!!! По товару №" . $product['product_id'] . " не заданы категории, используем корневую категорию");
            }

            // Тут полная иммитация режима SEO_PRO
            if(!isset($productKeywords[$product_id])) {
                // У нас нет ЧПУ даже для товара.
                $url = $this->store_host . "index.php?route=product/product&amp;product_id=" . $product['product_id'];
            } else if ( !$category_id || !$this->includeSeoPath ) {
                // Родительская категория отсутствует, либо она не требуется в ЧПУ
                $url = $this->store_host . $productKeywords[$product_id] . $this->seoUrlPostfix;
            } else {
                // Полный вариант работы - формируем ЧПУ
                $url = $this->store_host;
                $categories = explode("_",$this->getCategoryPath($category_id));
                $broken = false;
                foreach($categories as $categoryId){
                    if(!isset($categoryKeywords[$categoryId])) {
                        // Одна из категорий не содержит ЧПУ - не сложилось
                        $url = $this->store_host . "index.php?route=product/product&amp;product_id=" . $product['product_id'];
                        $broken = true;
                        break;
                    }
                    $url .= $categoryKeywords[$categoryId] . "/";
                }
                if( !$broken )
                    $url .= $productKeywords[$product_id] . $this->seoUrlPostfix;
            }

            //$output .= $this->getItem( $url, substr(max($product['date_added'], $product['date_modified']), 0, 10), "weekly", "1.0");
            $output .= $this->getItem( $url, $product['date_modified'], "weekly", "1.0");

            // дополнительные замеры производительности
            $count++;
            if( ($count % 5000) == 0 ) {
                $this->log("Обработано 5000 продуктов за  " . round(microtime(true) - $partBegin,3)  . " сек" );
                $partBegin = microtime(true);
            }
        }

        $this->log("Генерация продуктов выполнена за " . round(microtime(true) - $begin,3)  . " сек" );

        unset($query);
        unset($productKeywords);
        unset($categoryKeywords);

        return $output;
    }

    public function getItems($part = -1){
        if( "2" == $this->seo_status ) {
            return $this->getItemsSeoUrl($part);
        } else if( "1" == $this->seo_status ) {
            return $this->getItemsSeoPro($part);
        } else {
            return $this->getItemsNoSeo($part);
        }
    }
}
?>