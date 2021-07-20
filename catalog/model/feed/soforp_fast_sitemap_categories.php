<?php

class ModelFeedSoforpFastSitemapCategories  extends ModelFeedSoforpFastSitemapItems  {

    public function __construct($registry) {
        parent::__construct($registry);
        $this->_itemName = "Категории";
    }

    public function getItemsCount(){

        static $_count = -1;

        if( $_count != -1 )
            return $_count;

        $sql = "select count(c.category_id) as `cnt` from `" . DB_PREFIX . "category` c ";
        if( "1" == $this->multistore_status ){
            $sql .= " inner join category_to_store cs on (c.category_id = cs.category_id) ";
        }
        $sql .= " WHERE c.status = '1' ";
        if( "1" == $this->multistore_status ){
            $sql .= " AND cs.store_id = " . (int)$this->store_id;
        }

        $query = $this->db->query($sql);
        if(!$query->rows) {
            $_count = 0;
            return $_count;
        }

        $_count = (int)$query->rows[0]["cnt"];
        return $_count;
    }

    public function getPartsCount() {
        return 0;//все пихается в первую партию
        //return ceil( $this->getItemsCount() / $this->partLimit );
    }

    public function getItems($part = -1) {

        $output = '';

        $begin = microtime(true);
        $this->log("Генерация категорий стартовала!" );

        $sql = "SELECT c.category_id, c.date_added, c.date_modified FROM " . DB_PREFIX . "category c ";
        if( "1" == $this->multistore_status ){
            $sql .= " inner join category_to_store cs on (c.category_id = cs.category_id) ";
        }
        $sql .= " WHERE c.status = '1' ";
        if( "1" == $this->multistore_status ){
            $sql .= " and cs.store_id = " . (int)$this->store_id;
        }

        $queryBegin = microtime(true);
        $query = $this->db->query($sql);
        $this->log("Запрос по категориям выполнен за " . round(microtime(true) - $queryBegin,3)  . " сек" );
        $categoryKeywords = $this->getCategoryKeywords();

        foreach ($query->rows as $category) {
            $path = $this->getCategoryPath($category['category_id']);


            if( "0" == $this->seo_status ) {
                $url = $this->store_host . "index.php?route=product/category&amp;path=" . $path;
            } else {
                // Тут полная иммитация режима SEO_PRO
                // $url = str_replace('&', '&amp;', str_replace('&amp;', '&', $this->url->link('product/category', 'path=' . $path)));
                $url = $this->store_host;
                $categories = explode("_",$path);
                foreach($categories as $categoryId){
                    if(!isset($categoryKeywords[$categoryId])) {
                        // Одна из категорий не содержит ЧПУ - не сложилось
                        $url = $this->store_host . "index.php?route=product/category&amp;path=" . $path;
                        break;
                    }
                    $url .= $categoryKeywords[$categoryId] . "/";
                }
            }

            $date = substr(max($category['date_added'], $category['date_modified']), 0, 10);
            $output .= $this->getItem($url, $date, "weekly", "0.7");
        }

        $this->log("Генерация категорий выполнена за " . round(microtime(true) - $begin,3) . " сек" );

        return $output;
    }
}
?>