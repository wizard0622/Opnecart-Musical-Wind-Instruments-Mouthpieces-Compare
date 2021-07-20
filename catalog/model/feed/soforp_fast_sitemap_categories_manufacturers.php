<?php

class ModelFeedSoforpFastSitemapCategoriesManufacturers  extends ModelFeedSoforpFastSitemapItems  {

    public function __construct($registry) {
        parent::__construct($registry);
        $this->_itemName = "Категории и производители";
    }

    protected function getManufacturers(){

        static $_manufacturers = -1;
        if( $_manufacturers != -1 )
            return $_manufacturers;

        $sql = "SELECT manufacturer_id FROM `" . DB_PREFIX . "manufacturer`;";

        $queryBegin = microtime(true);
        $query = $this->db->query($sql);
        $this->log("Запрос по производителям выполнен за " . round(microtime(true) - $queryBegin,3)  . " сек" );

        $ids = array();

        $queryBegin = microtime(true);
        foreach( $query->rows as $keyword ){
            $ids[] = $keyword["manufacturer_id"];
        }
        unset($query);
        $this->log("Кеширование производителей выполнено за " . round(microtime(true) - $queryBegin,3)  . " сек" );

        $_manufacturers = $ids;
        return $_manufacturers;
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
        $sql = "SELECT c.category_id, c.parent_id FROM " . DB_PREFIX . "category c ";
        if( "1" == $this->multistore_status ){
            $sql .= " inner join category_to_store cs on (c.category_id = cs.category_id) ";
        }
        $sql .= " WHERE c.status = '1' ";
        if( "1" == $this->multistore_status ){
            $sql .= " AND cs.store_id = " . (int)$this->store_id;
        }
        $query = $this->db->query($sql);
        $this->log("Запрос по категориям выполнен за " . round(microtime(true) - $queryBegin,3)  . " сек" );

        $queryBegin = microtime(true);
        $categories = array();
        foreach($query->rows as $row ){
            $categories[$row['category_id']] = $row['parent_id'];
        }
        $_categories = $categories;
        $this->log("Кеширование категорий выполнено за " . round(microtime(true) - $queryBegin,3)  . " сек" );

        return $_categories;
    }


    public function getItemsCount(){
        return count($this->getCategories()) * count($this->getManufacturers());
    }

    public function getPartsCount() {
        // Считаем сколько категорий с производителями вмещается целиком в одну партию
        $categoriesInPart = $this->partLimit / count($this->getManufacturers());
        $categoriesCount = count($this->getCategories());
        if( $categoriesInPart >= $categoriesCount )
            return 1;
        return ceil( $categoriesCount / $categoriesInPart );
    }

    public function getItems($part = -1) {

        $output = '';

        $begin = microtime(true);
        $this->log("Генерация категорий с производителями стартовала!" );

        $manufacturers = $this->getManufacturers(); // PHP дико тормозит при использовании массивов через $this->
        $manufacturerKeywords = $this->getManufacturerKeywords(); // PHP дико тормозит при использовании массивов через $this->

        $sql = "SELECT c.category_id, c.date_added, c.date_modified FROM " . DB_PREFIX . "category c ";
        if( "1" == $this->multistore_status ){
            $sql .= " inner join category_to_store cs on (c.category_id = cs.category_id) ";
        }
        $sql .= " WHERE c.status = '1' ";
        if( "1" == $this->multistore_status ){
            $sql .= " AND cs.store_id = " . (int)$this->store_id;
        }
        if( $part != -1 ) {
            $categoriesInPart = $this->partLimit / count($manufacturers);
            $categories = $this->getCategories(); // PHP дико тормозит при использовании массивов через $this->
            if( $categoriesInPart < $categories ) {
                $from = $categoriesInPart * ($part - 1); // нумерация идет с единицы
                $to = $categoriesInPart * $part;
                $sql .= " LIMIT " . (int)$from . ", " . (int)$to . " ";
            }
        }

        $queryBegin = microtime(true);
        $query = $this->db->query($sql);
        $this->log("Запрос по категориям выполнен за " . round(microtime(true) - $queryBegin,3)  . " сек" );

        $categoryKeywords = $this->getCategoryKeywords();

        foreach( $query->rows as $category){

            // Урл самой категории
            $path = $this->getCategoryPath($category['category_id']);
            $date = substr(max($category['date_added'], $category['date_modified']), 0, 10);

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

            // Урлы подчиненных производителей
            $isSeoUrl = (false === strpos($url,"&") );
            foreach( $manufacturers as $manufacturer_id ) {
                if( !$isSeoUrl || !isset($manufacturerKeywords[$manufacturer_id])) {
                    // ЧПУ тут не видать в любом случае
                    $mUrl = $url . "&amp;manufacturer_id=" . $manufacturer_id;
                } else {
                    $mUrl = $url . $manufacturerKeywords[$manufacturer_id] . $this->seoUrlPostfix;
                }
                $output .= $this->getItem($mUrl, $date, "weekly", "0.7");
            }
        }

        $this->log("Генерация категорий c производителями выполнена за " . round(microtime(true) - $begin,3) . " сек" );

        return $output;
    }
}
?>