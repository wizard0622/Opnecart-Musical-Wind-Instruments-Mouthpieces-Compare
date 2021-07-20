<?php

class ModelFeedSoforpFastSitemapInformations  extends ModelFeedSoforpFastSitemapItems  {

    public function __construct($registry) {
        parent::__construct($registry);
        $this->_itemName = "Информация";
    }

    public function getItemsCount(){
        return 0; // не существенно
    }

    public function getPartsCount() {
        return 0; // все пихается в первую партию
    }

    public function getItems($part = -1) {

        $output = '';

        $begin = microtime(true);
        $this->log("Генерация информации стартовала!" );

        $sql = "select i.information_id from `" . DB_PREFIX . "information` i";
        if( "1" == $this->multistore_status ){
            $sql .= " inner join information_to_store `is` on (i.information_id = `is`.information_id) ";
        }
        $sql .= " where i.status=1 ";
        if( "1" == $this->multistore_status ){
            $sql .= " and `is`.store_id = " . (int)$this->store_id;
        }
        $informations = $this->db->query($sql);
        foreach ($informations->rows as $information) {
            if( "0" != $this->seo_status ) {
                $url = str_replace('&', '&amp;', str_replace('&amp;', '&', $this->url->link('information/information', 'information_id=' . $information['information_id'])));
            } else {
                $url = $this->store_host . "index.php?route=information/information&amp;information_id=" . $information['information_id'];
            }
            $output .= $this->getItem($url,"","weekly","0.5");
        }

        $this->log("Генерация информации выполнена за " . round(microtime(true) - $begin,3) . " сек" );

        return $output;
    }
}
?>