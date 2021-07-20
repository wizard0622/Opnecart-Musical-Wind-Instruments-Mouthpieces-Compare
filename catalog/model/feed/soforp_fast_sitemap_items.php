<?php

class ModelFeedSoforpFastSitemapItems extends Model {

    protected $_itemName = "";

    /*
     * Инициализация параметров
     */
    public function __construct($registry) {
        parent::__construct($registry);

        $this->includeSeoPath = $this->config->get('config_seo_url_include_path');
        $this->seoUrlPostfix = $this->config->get('config_seo_url_postfix');
        $this->seo_status = $this->config->get("soforp_fast_sitemap_seo_status");
        $this->filterpro_seo_status = $this->config->get("soforp_fast_sitemap_filterpro_seo_status");
        $this->category_brand_status = $this->config->get("soforp_fast_sitemap_category_brand_status");
        $this->includeAddresses = $this->config->get("soforp_fast_sitemap_addresses_status");
        $this->blog_status = $this->config->get("soforp_fast_sitemap_blog_status");
        $this->multistore_status = $this->config->get("soforp_fast_sitemap_multistore_status");
        $this->store_host = 'http' . ( (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']) ? "s" : "" ) . '://' . str_replace('www.', '', $_SERVER['HTTP_HOST']) . rtrim(dirname($_SERVER['PHP_SELF']), '/.\\') . '/';
        $this->store_id = (int)$this->config->get("config_store_id");
        $this->language_id = (int)$this->config->get('config_language_id');
        $this->customer_group_id = (int)$this->config->get('config_customer_group_id');
        $this->useUrlDate = $this->config->get('soforp_fast_sitemap_use_url_date');
        $this->useUrlFrequency = $this->config->get('soforp_fast_sitemap_use_url_frequency');
        $this->useUrlPriority = $this->config->get('soforp_fast_sitemap_use_url_priority');

        $this->partLimit = 50000;
    }

    protected function log( $message ){
        $line = date("Y-m-d H:i:s - ") . "SOFORP FastSitemap [" . $this->_itemName . "] " . $message;
        $line .= " Память: " . number_format(memory_get_usage()) . " / " . number_format(memory_get_usage(TRUE));
        $line .= "\r\n";
        file_put_contents(DIR_LOGS . $this->config->get("config_error_filename") , $line, FILE_APPEND );
    }

    /*
     * Главная функция
     */
    protected function getItem( $url, $date = "", $frequency = "weekly", $priority = "1.0" ) {

        $output = "<url>\n";
        $output .= "\t<loc>$url</loc>\n";
        if( $this->useUrlDate && $date ) // опциональная штука
            $output .= "\t<lastmod>$date</lastmod>\n";
        if( $this->useUrlFrequency )
            $output .= "\t<changefreq>$frequency</changefreq>\n";
        if( $this->useUrlPriority )
            $output .= "\t<priority>$priority</priority>\n";
        $output .= "</url>\n";

        return $output;
    }

    public function getPartsCount(){
        return 0; // поведение по умолчанию, для продуктов и категорий-брендов должно быть переопределено
    }

    public function getItemsCount(){
        return 0; // поведение по умолчанию
    }

    /*
     * Вытаскиваем из базы все зарегистрированные чпу для нужных разделов сайта
     * Пример - $this->getKeywords("product_id","продуктов");
     */
    protected function getItemKeywords($key,$name){
        $sql = "SELECT substr(query," . (strlen($key) + 2) . ") as keyword_id, keyword FROM `" . DB_PREFIX . "url_alias` WHERE query like \"$key=%\";";

        $queryBegin = microtime(true);
        $keywordsData = $this->db->query($sql);
        $this->log("Запрос по ключам $name выполнен за " . round(microtime(true) - $queryBegin,3)  . " сек. Получено " . $keywordsData->num_rows . " записей." );

        $keywords = array();

        $queryBegin = microtime(true);
        foreach( $keywordsData->rows as $keyword ){
            $keywords[$keyword["keyword_id"]] = rawurlencode($keyword["keyword"]);
        }
        unset($keywordsData);
        $this->log("Кеширование ключей $name выполнено за " . round(microtime(true) - $queryBegin,3)  . " сек" );

        return $keywords;
    }

    protected function getProductKeywords(){
        static $_productKeywords = -1;

        if( $_productKeywords != -1 )
            return $_productKeywords;

        $_productKeywords = $this->getItemKeywords("product_id","продуктов");
        return $_productKeywords;
    }

    protected function getManufacturerKeywords(){
        static $_manufacturerKeywords = -1;

        if( $_manufacturerKeywords != -1 )
            return $_manufacturerKeywords;

        $_manufacturerKeywords = $this->getItemKeywords("manufacturer_id","категорий");
        return $_manufacturerKeywords;
    }


    protected function getCategoryKeywords(){
        static $_categoryKeywords = -1;

        if( $_categoryKeywords != -1 )
            return $_categoryKeywords;

        $_categoryKeywords = $this->getItemKeywords("category_id","категорий");
        return $_categoryKeywords;
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

        $this->log("Кеширование дерева категорий выполнено за " . round(microtime(true) - $queryBegin,3)  . " сек" );
        return $_categories;
    }

    /*
     * Вытаскивает путь до категории в формате номер_номер_номер
     */
    protected function getCategoryPath($categoryId=0){
        if(!$categoryId)
            return "";

        static $path = null;
        if (!is_array($path))
            $path = array();

        if( isset($path[$categoryId]) )
            return $path[$categoryId];

        $categories = $this->getCategories();

        if( !isset($categories[$categoryId]) ) {
            $path[$categoryId] = "$categoryId";
            return "$categoryId";
        }

        $parentId = $categories[$categoryId];
        if( !$parentId ) {
            $path[$categoryId] = "$categoryId";
            return "$categoryId";
        }

        if( $parentId == $categoryId ) {
            // Такого быть не должно, иначе будет зациклирование.
            $this->log("Обнаружено зацикливание дерева категорий на категории $categoryId. Родитель категории является самой категорией" );
            throw new Exception("Обнаружено зацикливание дерева категорий на категории $categoryId. Родитель категории является самой категорией");
        }

        $path[$categoryId] = $this->getCategoryPath($parentId) . "_" . $categoryId;
        return $path[$categoryId];
    }
}
?>