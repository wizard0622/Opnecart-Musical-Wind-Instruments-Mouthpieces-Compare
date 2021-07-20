<?php

class ControllerFeedSoforpFastSitemap extends Controller {

    protected function log( $message ){
        $line = date("Y-m-d H:i:s - ") . "SOFORP FastSitemap " . $message;
        $line .= " Память: " . number_format(memory_get_usage()) . " / " . number_format(memory_get_usage(TRUE));
        $line .= "\r\n";
        file_put_contents(DIR_LOGS . $this->config->get("config_error_filename") , $line, FILE_APPEND );
    }

    protected function getSitemapIndex(){

        $begin = microtime(true);
        $this->log("Генерация индекса карт сайта стартовала!" );

        $output  = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
        $output .= "<sitemapindex xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n";

        $this->load->model("feed/soforp_fast_sitemap_products");
        $this->load->model("feed/soforp_fast_sitemap_categories_manufacturers");
        $parts =  1 + $this->model_feed_soforp_fast_sitemap_products->getPartsCount();
        if( $this->category_brand_status )
            $parts += $this->model_feed_soforp_fast_sitemap_categories_manufacturers->getPartsCount();

        for($i = 0; $i < $parts; $i++ ){
            $output .= "\t<sitemap>\n";
            if( !$this->nativeUrlStatus ) {
                $output .= "\t\t<loc>" . $this->store_host . 'index.php?route=feed/soforp_fast_sitemap&amp;index=' . $i . "</loc>\n";
            } else {
                $output .= "\t\t<loc>" . $this->store_host . 'sitemap' . $i . ".xml</loc>\n";
            }
            if( 1 == $this->useUrlDate )
                $output .= "\t\t<lastmod>" . date("Y-m-d") . "</lastmod>\n";
            $output .= "\t</sitemap>\n";
        }

        $output .= "</sitemapindex>\n";

        $this->log("Генерация индекса карт сайта выполнена за " . round(microtime(true) - $begin,3) . " сек" );

        return $output;
    }

    protected function getSitemapPart($part){

        $begin = microtime(true);

        $this->log("Генерация части №" . $part . " карты сайта стартовала!" );

        $output  = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
        $output .= "<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n";

        $this->load->model("feed/soforp_fast_sitemap_home");
        $this->load->model("feed/soforp_fast_sitemap_products");
        $this->load->model("feed/soforp_fast_sitemap_categories");
        $this->load->model("feed/soforp_fast_sitemap_categories_manufacturers");
        $this->load->model("feed/soforp_fast_sitemap_manufacturers");
        $this->load->model("feed/soforp_fast_sitemap_informations");
        $this->load->model("feed/soforp_fast_sitemap_blog_occms");
        $this->load->model("feed/soforp_fast_sitemap_blog_pavo");
        $this->load->model("feed/soforp_fast_sitemap_addresses");
        $this->load->model("feed/soforp_fast_sitemap_filterpro_seo");

        $productParts = $this->model_feed_soforp_fast_sitemap_products->getPartsCount();

        if( "0" == $part ) {
            $output .= $this->model_feed_soforp_fast_sitemap_home->getItems();
            $output .= $this->model_feed_soforp_fast_sitemap_categories->getItems();
            $output .= $this->model_feed_soforp_fast_sitemap_manufacturers->getItems();
            $output .= $this->model_feed_soforp_fast_sitemap_informations->getItems();
            if( 1 == $this->filterpro_seo_status )
                $output .= $this->model_feed_soforp_fast_sitemap_filterpro_seo->getItems();

            if( 1 == $this->blog_status ) {
                $this->getChild('common/seoblog');
                $output .= $this->model_feed_soforp_fast_sitemap_blog_occms->getItems();
            } else if( 2 == $this->blog_status ){
                $output .= $this->model_feed_soforp_fast_sitemap_blog_pavo->getItems();
            }

            if( $this->includeAddresses && $this->model_feed_soforp_fast_sitemap_addresses->isAvailable() )
                $output .= $this->$this->model_feed_soforp_fast_sitemap_blog_pavo->getItems();
        } else if ( (int)$part >= 1 && (int)$part <= $productParts ) {
            $output .= $this->model_feed_soforp_fast_sitemap_products->getItems($part);
        } else {
            $output .= $this->model_feed_soforp_fast_sitemap_categories_manufacturers->getItems($part-$productParts);
        }

        $output .= "</urlset>";

        $this->log("Генерация части №" . $part . " карты сайта выполнена за " . round(microtime(true) - $begin,3) . " сек" );

        return $output;
    }


    protected function getFullSitemap(){

        $begin = microtime(true);
        $this->log("Генерация полной карты сайта стартовала!" );

        $output  = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
        $output .= "<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n";

        $this->load->model("feed/soforp_fast_sitemap_home");
        $this->load->model("feed/soforp_fast_sitemap_products");
        $this->load->model("feed/soforp_fast_sitemap_categories");
        $this->load->model("feed/soforp_fast_sitemap_categories_manufacturers");
        $this->load->model("feed/soforp_fast_sitemap_manufacturers");
        $this->load->model("feed/soforp_fast_sitemap_informations");
        $this->load->model("feed/soforp_fast_sitemap_blog_occms");
        $this->load->model("feed/soforp_fast_sitemap_blog_pavo");
        $this->load->model("feed/soforp_fast_sitemap_addresses");
        $this->load->model("feed/soforp_fast_sitemap_filterpro_seo");


        $output .= $this->model_feed_soforp_fast_sitemap_home->getItems();
        $output .= $this->model_feed_soforp_fast_sitemap_categories->getItems();
        $output .= $this->model_feed_soforp_fast_sitemap_manufacturers->getItems();
        $output .= $this->model_feed_soforp_fast_sitemap_informations->getItems();
        if( 1 == $this->filterpro_seo_status )
            $output .= $this->model_feed_soforp_fast_sitemap_filterpro_seo->getItems();
        if( 1 == $this->blog_status ) {
            $this->getChild('common/seoblog');
            $output .= $this->model_feed_soforp_fast_sitemap_blog_occms->getItems();
        } else if( 2 == $this->blog_status ){
            $output .= $this->model_feed_soforp_fast_sitemap_blog_pavo->getItems();
        }

        if( $this->includeAddresses && $this->model_feed_soforp_fast_sitemap_addresses->isAvailable() )
            $output .= $this->$this->model_feed_soforp_fast_sitemap_blog_pavo->getItems();

        $output .= $this->model_feed_soforp_fast_sitemap_products->getItems();
        if( $this->category_brand_status )
            $output .= $this->model_feed_soforp_fast_sitemap_categories_manufacturers->getItems();

        $output .= '</urlset>';

        $this->log("Генерация полной карты сайта выполнена за " . round(microtime(true) - $begin,3) . " сек" );

        return $output;
    }

	public function index() {
		if ("1" != $this->config->get('soforp_fast_sitemap_status')) {
            $this->log("Карта сайта отключена и не будет построена");
            exit();
        }

        $this->includeSeoPath = $this->config->get('config_seo_url_include_path');
        $this->nativeUrlStatus = $this->config->get("soforp_fast_sitemap_native_url_status");
        $this->seo_status = $this->config->get("soforp_fast_sitemap_seo_status");
        $this->filterpro_seo_status = $this->config->get("soforp_fast_sitemap_filterpro_seo_status");
        $this->category_brand_status = $this->config->get("soforp_fast_sitemap_category_brand_status");
        $this->includeAddresses = $this->config->get("soforp_fast_sitemap_addresses_status");
        $this->log("ЧПУ: " . $this->seo_status);
        $this->blog_status = $this->config->get("soforp_fast_sitemap_blog_status");
        $this->useUrlDate = $this->config->get('soforp_fast_sitemap_use_url_date');
        $this->useUrlFrequency = $this->config->get('soforp_fast_sitemap_use_url_frequency');
        $this->useUrlPriority = $this->config->get('soforp_fast_sitemap_use_url_priority');


        if( "1" == $this->config->get("soforp_fast_sitemap_partition_status")){
            if( !isset($this->request->get["index"])){
                $output = $this->getSitemapIndex();
            } else {
                $output = $this->getSitemapPart($this->request->get["index"]);
            }
        } else {
            $output = $this->getFullSitemap();
        }
        $this->response->addHeader('Content-Type: application/xml');
        if( "0" == $this->config->get("soforp_fast_sitemap_gzip_status") ) {
            $this->response->setOutput($output);
        } else {
            if( !function_exists("gzencode")){
                $this->log("Отсутствует функция gzencode! Обратитесь к хостеру. Ответ будет отдан без сжатия!");
                $this->response->setOutput($output);
            } else {
                $data = gzencode($output,$this->config->get("soforp_fast_sitemap_gzip_status"));
                if( !$data ){
                    $this->log("Не удалось выполнить сжатие. Обратитесь к разработчику с логами веб-сервера. Ответ будет отдан без сжатия!");
                    $this->response->setOutput($output);
                } else {
                    $this->log("Сжатие отработало успешно, размер ответа - " . strlen($data) . " байт");
                    $this->response->addHeader('Content-Encoding: gzip');
                    //$this->response->addHeader('Content-length: ' . strlen($data) );
                    $this->response->setOutput($data);
                }
            }
        }
	}


}
?>