<?php

class ModelFeedSoforpFastSitemapAddresses  extends ModelFeedSoforpFastSitemapItems  {

    public function __construct($registry) {
        parent::__construct($registry);
        $this->_itemName = "Адреса";
    }

    public function getItemsCount(){
        return 0; // не существенно
    }

    public function getPartsCount() {
        return 0; // все пихается в первую партию
    }

    public function isAvailable(){
        $sql = "show tables like 'search_adress'";
        $query = $this->db->query($sql);
        return ( $query->num_rows > 0 );
    }

    public function getItems($part = -1) {

        $output = '';

        $begin = microtime(true);
        $this->log("Генерация поисковых ссылок стартовала!" );

        $sql = "select s.streetName, s.houseNumber, s.corpusNumber from `search_adress` s";
        $query = $this->db->query($sql);
        foreach ($query->rows as $address) {
            $url = $this->store_host . "dostupnie-internet-provayderi-po-adresu/?street=" . rawurlencode( $address['streetName'] ). "&amp;building=" . $address['houseNumber'] . "&amp;corpus=" . $address['corpusNumber'];
            $output .= $this->getItem($url,"","weekly","0.5");
        }

        $this->log("Генерация поисковых ссылок выполнена за " . round(microtime(true) - $begin,3) . " сек" );

        return $output;
    }
}
?>