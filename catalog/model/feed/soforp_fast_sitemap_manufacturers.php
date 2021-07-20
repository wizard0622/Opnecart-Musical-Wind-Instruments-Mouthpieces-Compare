<?php

class ModelFeedSoforpFastSitemapManufacturers  extends ModelFeedSoforpFastSitemapItems  {

    public function __construct($registry) {
        parent::__construct($registry);
        $this->_itemName = "Производители";
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
        $this->log("Генерация производителей стартовала!" );

        $sql = "select m.manufacturer_id from `" . DB_PREFIX . "manufacturer` m ";
        if( "1" == $this->multistore_status ){
            $sql .= " inner join manufacturer_to_store ms on (m.manufacturer_id = ms.manufacturer_id) where ms.store_id = " . (int)$this->store_id;
        }

        $queryBegin = microtime(true);
        $query = $this->db->query($sql);
        $this->log("Запрос по производителям выполнен за " . round(microtime(true) - $queryBegin,3)  . " сек" );

        $manufacturerKeywords = $this->getManufacturerKeywords();

        foreach ($query->rows as $manufacturer) {

            $manufacturerid = $manufacturer['manufacturer_id'];
            if( "0" == $this->seo_status || !isset($manufacturerKeywords[$manufacturerid])) {
                $url = str_replace('&', '&amp;', str_replace('&amp;', '&', $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $manufacturerid)));
            } else {
                // Тут полная иммитация режима SEO_PRO
                // $url = str_replace('&', '&amp;', str_replace('&amp;', '&', $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $manufacturer['manufacturer_id'])));
                $url = $this->store_host . $manufacturerKeywords[$manufacturerid] . $this->seoUrlPostfix;
            }
            $output .= $this->getItem($url,"","weekly","0.7");
        }

        $this->log("Генерация производителей выполнена за " .  ( round(microtime(true) - $begin,3) ) . " сек" );

        unset($query);
        return $output;
    }
}
?>