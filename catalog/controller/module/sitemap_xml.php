<?php

class ControllerModuleSitemapXml extends Controller {

    public function index() {
        if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
            $base = $this->config->get('config_ssl');
        } else {
            $base = $this->config->get('config_url');
        }

        if (!defined('HTTP_IMAGE')) {
            $server = $base . 'image/';
        } else {
            if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
                $server = HTTPS_IMAGE;
            } else {
                $server = HTTP_IMAGE;
            }
        }

        $xml_array = array();
        $xml_array[] = $this->getHome($base, $server);
        $xml_array = array_merge($xml_array, $this->getInformationPages());
        $xml_array = array_merge($xml_array, $this->getCategories(0, '', $server));
        $xml_array = array_merge($xml_array, $this->getProducts($server));

        $output = '<?xml version="1.0" encoding="UTF-8" ?>' . "\r\n";
        $output .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">' . "\r\n";

        foreach ($xml_array as $item) {
            $output .= $this->getSitemapItemString($item);
        }

        $output .= '</urlset>';

        $this->response->addHeader('Content-Type: text/xml;');
        $this->response->setOutput($output);
    }

    private function getHome($base, $server) {
        if ($this->config->get('config_logo') && file_exists(DIR_IMAGE . $this->config->get('config_logo'))) {
            $logo = $server . $this->config->get('config_logo');
        } else {
            $logo = '';
        }

        return array('loc' => $base, 'image' => $logo, 'priority' => 1, 'changefreq' => 'weekly');
    }

    private function getInformationPages() {
        $this->load->model('catalog/information');

        $xml_links = array();
        $info_pages = $this->model_catalog_information->getInformations();

        foreach ($info_pages as $page) {
            $url = $this->url->link('information/information', 'information_id=' . $page['information_id']);
            $xml_links[] = array('loc' => $url, 'image' => null, 'priority' => 0.5, 'changefreq' => 'monthly');
        }

        return $xml_links;
    }

    private function getCategories($parent_id, $path_str, $server) {
        $this->load->model('catalog/category');

        $xml_cats = array();
        $categories = $this->model_catalog_category->getCategories($parent_id, null, $server);

        foreach ($categories as $cat) {
            $image = null;
            if (!empty($cat['image']) && file_exists(DIR_IMAGE . $cat['image'])) {
                $image = $server . $cat['image'];
            }

            $url = $this->url->link('product/category', 'path=' . $path_str . $cat['category_id']);
            $xml_cats[] = array('loc' => $url, 'image' => $image, 'priority' => 0.5, 'changefreq' => 'weekly');

            $xml_child_cats = $this->getCategories($cat['category_id'], $path_str . $cat['category_id'] . '_', $server);
            array_merge($xml_cats, $xml_child_cats);
        }

        return $xml_cats;
    }

    private function getProducts($server) {
        $this->load->model('catalog/product');

        $xml_cats = array();
        $products = $this->model_catalog_product->getProducts();

        foreach ($products as $prod) {
            $image = null;
            if (file_exists(DIR_IMAGE . $prod['image'])) {
                $image = $server . $prod['image'];
            }

            $url = $this->url->link('product/product', 'product_id=' . $prod['product_id']);
            $xml_cats[] = array('loc' => $url, 'image' => $image, 'priority' => 0.5, 'changefreq' => 'monthly');
        }

        return $xml_cats;
    }

    private function getSitemapItemString($sitemap_item) {
        $item_string = "\t<url>\r\n";
        $item_string .= "\t\t<loc><![CDATA[" . html_entity_decode($sitemap_item["loc"]) . "]]></loc>\r\n";
        $item_string .= "\t\t<changefreq><![CDATA[" . $sitemap_item["changefreq"] . "]]></changefreq>\r\n";
        $item_string .= "\t\t<priority><![CDATA[" . $sitemap_item["priority"] . "]]></priority>\r\n";
        if (isset($sitemap_item["image"])) {
            $item_string .= "\t\t<image:image>\r\n";
            $item_string .= "\t\t\t<image:loc><![CDATA[" . $sitemap_item["image"] . "]]></image:loc>\r\n";
            $item_string .= "\t\t</image:image>\r\n";
        }
        $item_string .= "\t</url>\r\n";

        return $item_string;
    }

}

?>