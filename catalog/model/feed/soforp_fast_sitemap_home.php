<?php

require_once ( dirname(__FILE__) . "/soforp_fast_sitemap_items.php");

class ModelFeedSoforpFastSitemapHome  extends ModelFeedSoforpFastSitemapItems  {

    public function __construct($registry) {
        parent::__construct($registry);
        $this->_itemName = "Главная";
    }

    public function getItemsCount(){
        return 1;
    }

    public function getPartsCount() {
        return ceil( $this->getItemsCount() / $this->partLimit );
    }

    public function getItems($part = -1){
        return $this->getItem( HTTP_SERVER, "", "weekly", "1.0");
    }
}
?>