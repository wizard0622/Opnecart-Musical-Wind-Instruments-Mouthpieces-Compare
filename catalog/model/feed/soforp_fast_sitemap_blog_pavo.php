<?php

class ModelFeedSoforpFastSitemapBlogPavo extends ModelFeedSoforpFastSitemapItems  {

    public function __construct($registry) {
        parent::__construct($registry);
        $this->_itemName = "PavoBlog";
    }

    public function getItemsCount(){
        return 0; // не существенно
    }

    public function getPartsCount() {
        return 0; // все пихается в первую партию
    }

    protected function getBlogCategories()
    {
        $output  = '';

        $sql = "SELECT c.category_id as blog_id FROM " . DB_PREFIX . "pavblog_category c ";
        $sql .= " WHERE c.published = '1' ";
        if( "1" == $this->multistore_status ){
            $sql .= " AND c.store_id = " . (int)$this->store_id;
        }
        $results = $this->db->query($sql);

        foreach ($results->rows as $blog) {
            if( 1 == (int)$blog['blog_id'] )
                continue;

            $url = $this->store_host . "index.php?route=pavblog/category&amp;id=" . $blog['blog_id'];
            $output .= $this->getItem($url,"","weekly","0.7");

        } //$results as $result
        return $output;
    }

    protected function getBlogRecords()
    {
        $output  = '';

        $sql = "SELECT r.blog_id as record_id, r.date_modified FROM " . DB_PREFIX . "pavblog_blog r ";
        if( "1" == $this->multistore_status ){
            $sql .= " inner join pavblog_category c on (r.category_id = c.category_id) ";
        }
        $sql .= " WHERE r.status = '1' ";
        if( "1" == $this->multistore_status ){
            $sql .= " and c.store_id = " . (int)$this->store_id;
        }
        $results = $this->db->query($sql);

        foreach ($results->rows as $record) {
            $url = $this->store_host . "index.php?route=pavblog/blog&amp;id=" . $record['record_id'];
            $date = substr( $record['date_modified'], 0, 10);
            $output .= $this->getItem($url,$date,"weekly","1.0");
        }

        return $output;
    }

    public function getItems($part = -1) {

        $output = '';

        $begin = microtime(true);
        $this->log("Генерация стартовала!" );

        $output .= $this->getBlogCategories();
        $output .= $this->getBlogRecords();

        $this->log("Генерация выполнена за " . round(microtime(true) - $begin,3) . " сек" );

        return $output;
    }
}
?>