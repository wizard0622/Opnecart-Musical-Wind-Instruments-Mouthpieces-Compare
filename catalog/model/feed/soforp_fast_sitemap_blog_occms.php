<?php

class ModelFeedSoforpFastSitemapBlogOccms extends ModelFeedSoforpFastSitemapItems  {

    public function __construct($registry) {
        parent::__construct($registry);
        $this->_itemName = "OcCMS";
    }

    public function getItemsCount(){
        return 0; // не существенно
    }

    public function getPartsCount() {
        return 0; // все пихается в первую партию
    }

    protected function getBlogCategories($parent_id, $current_path = '')
    {
        $output  = '';

        $sql = "SELECT b.blog_id, b.date_added, b.date_modified FROM " . DB_PREFIX . "blog b ";
        if( "1" == $this->multistore_status ){
            $sql .= " inner join blog_to_store bs on (b.blog_id = bs.blog_id) ";
        }
        $sql .= " WHERE b.parent_id = '" . $parent_id . "' AND b.status = '1' AND b.customer_group_id = '" . (int)$this->customer_group_id. "'";
        if( "1" == $this->multistore_status ){
            $sql .= " and bs.store_id = " . (int)$this->store_id;
        }
        $results = $this->db->query($sql);

        foreach ($results->rows as $blog) {
            if (!$current_path) {
                $new_path = $blog['blog_id'];
            } else {
                $new_path = $current_path . '_' . $blog['blog_id'];
            }
            if( "0" != $this->seo_status ) {
                $url = str_replace('&', '&amp;', str_replace('&amp;', '&', $this->url->link('record/blog', 'blog_id=' . $new_path)));
            } else {
                $url = $this->store_host . "index.php?route=record/blog&amp;blog_id=" . $blog['blog_id'];
            }
            $date = substr(max($blog['date_added'], $blog['date_modified']), 0, 10);
            $output .= $this->getItem($url,$date,"weekly","0.7");

            $output .= $this->getBlogCategories($blog['blog_id'], $new_path);
        } //$results as $result
        return $output;
    }

    protected function getBlogRecords()
    {
        $output  = '';

        $sql = "SELECT r.record_id, r.date_modified, r.date_available FROM " . DB_PREFIX . "record r ";
        if( "1" == $this->multistore_status ){
            $sql .= " inner join record_to_store rs on (r.blog_id = rs.blog_id) ";
        }
        $sql .= " WHERE r.status = '1' AND r.customer_group_id = '" . (int)$this->customer_group_id. "' AND NOW() BETWEEN r.date_available AND r.date_end ";
        if( "1" == $this->multistore_status ){
            $sql .= " and rs.store_id = " . (int)$this->store_id;
        }
        $results = $this->db->query($sql);

        foreach ($results->rows as $record) {
            if( "0" != $this->seo_status ) {
                $url = str_replace('&', '&amp;', str_replace('&amp;', '&', $this->url->link('record/record', 'record_id=' . $record["record_id"])));
            } else {
                $url = $this->store_host . "index.php?route=record/record&amp;record_id=" . $record['record_id'];
            }
            $date = substr(max($record['date_available'], $record['date_modified']), 0, 10);
            $output .= $this->getItem($url,$date,"weekly","1.0");
        }

        return $output;
    }

    public function getItems($part = -1) {

        $output = '';

        $begin = microtime(true);
        $this->log("Генерация стартовала!" );

        $output .= $this->getBlogCategories(0);
        $output .= $this->getBlogRecords();

        $this->log("Генерация выполнена за " . round(microtime(true) - $begin,3) . " сек" );

        return $output;
    }
}
?>