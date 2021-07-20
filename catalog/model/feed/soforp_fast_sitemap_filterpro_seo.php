<?php

class ModelFeedSoforpFastSitemapFilterproSeo extends ModelFeedSoforpFastSitemapItems  {

    public function __construct($registry) {
        parent::__construct($registry);
        $this->_itemName = "FilterPro SEO";
    }

    public function getItemsCount(){

        static $_count = -1;

        if( $_count != -1 )
            return $_count;

        $sql = "select count(*) as `cnt` from `" . DB_PREFIX . "filterpro_seo` ";

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
        $this->log("Генерация filterpro seo стартовала!" );

        $sql = "SELECT * FROM " . DB_PREFIX . "filterpro_seo";

        $queryBegin = microtime(true);
        $query = $this->db->query($sql);
        $this->log("Запрос по filterpro seo выполнен за " . round(microtime(true) - $queryBegin,3)  . " сек" );

        foreach ($query->rows as $row) {
            $filter_url = $row['url'];
            $filterpro_data = unserialize($row['data']);

            $data = array();
            parse_str(str_replace('&amp;', '&', $filterpro_data['url']), $data);
            if(isset($data['route'])) {
                if($data['route'] == 'product/category') {
                    $url = $this->url->link($data['route'], 'path=' . (isset($data['path']) ? $data['path'] : $data['category_id']) . '&' . $filter_url);
                } elseif($data['route'] == 'product/manufacturer/info') {
                    $url = $this->url->link($data['route'], 'manufacturer_id=' . $data['manufacturer_id'] . '&' . $filter_url);
                } else {
                    $url = $this->url->link($data['route'], $filter_url);
                }
                $output .= $this->getItem($url, "", "weekly", "0.7");
            }
        }

        $this->log("Генерация filterpro seo выполнена за " . round(microtime(true) - $begin,3) . " сек" );

        return $output;
    }
}
?>