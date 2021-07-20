<?php
class ControllerFeedSoforpFastSitemap extends Controller {

    private $error = array();

    protected function initBreadcrumbs($items) {
        $newItems = array_merge( array(array("common/home","text_home")), $items);

        $this->data['breadcrumbs'] = array();

        foreach( $newItems as $item ){
            $this->data['breadcrumbs'][] = array(
                'href'      => $this->url->link($item[0], 'token=' . $this->session->data['token'], 'SSL'),
                'text'      => $this->language->get($item[1]),
                'separator' => (count($this->data['breadcrumbs']) ==0 ? FALSE : ' :: ')
            );
        }
    }

    protected function log( $message ){
        file_put_contents(DIR_LOGS . $this->config->get("config_error_filename") , date("Y-m-d H:i:s - ") . "SOFORP FastSitemap " . $message . "\r\n", FILE_APPEND );
    }

    protected function initParams($items) {
        foreach( $items as $item ){
            $name = $item[0];
            if (isset($this->request->post[$name])) {
                $this->data[$name] = $this->request->post[$name];
                //$this->log("INIT FROM POST: " . $name . ": " . $this->request->post[$name]);
            } else if ($this->config->has($name)) {
                $this->data[$name] = $this->config->get($name);
                //$this->log("INIT FROM CONFIG: " . $name . ": " . $this->config->get($name));
            } else if(isset($item[1])){
                $this->data[$name] = $item[1]; // default value
                //$this->log("INIT FROM DEFAULTS: " . $name . ": " . $item[1]);
            }
        }
    }

    protected function initLanguage($module) {
        $this->data = array_merge( $this->data, $this->language->load($module) );
    }

    public function index() {
        $this->initLanguage('feed/soforp_fast_sitemap');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('setting/setting');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && ($this->validate())) {

            $this->model_setting_setting->editSetting('fast_sitemap', $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $this->redirect($this->url->link('extension/feed', 'token=' . $this->session->data['token'], 'SSL'));
        }

        if (isset($this->error['warning'])) {
            $this->data['error_warning'] = $this->error['warning'];
        } else {
            $this->data['error_warning'] = '';
        }

        $this->initBreadcrumbs(array(
            array("extension/feed","text_feed"),
            array("feed/soforp_fast_sitemap","heading_title")
        ));

        $this->data['action'] = $this->url->link('feed/soforp_fast_sitemap', 'token=' . $this->session->data['token'], 'SSL');

        $this->data['cancel'] = $this->url->link('extension/feed', 'token=' . $this->session->data['token'], 'SSL');

        $this->data['url'] = HTTP_CATALOG . 'index.php?route=feed/soforp_fast_sitemap';

        $this->initParams(array(
            array( "soforp_fast_sitemap_status", 1 ),
            array( "soforp_fast_sitemap_native_url_status", 0 ),
            array( "soforp_fast_sitemap_seo_status", 1 ),
            array( "soforp_fast_sitemap_filterpro_seo_status", 0 ),
            array( "soforp_fast_sitemap_category_brand_status", 0 ),
            array( "soforp_fast_sitemap_addresses_status", 0 ),
            array( "soforp_fast_sitemap_gzip_status", 0 ),
            array( "soforp_fast_sitemap_partition_status", 0 ),
            array( "soforp_fast_sitemap_multistore_status", 0 ),
            array( "soforp_fast_sitemap_blog_status", 0 ),
            array( "soforp_fast_sitemap_use_url_date", 1 ),
            array( "soforp_fast_sitemap_use_url_frequency", 1 ),
            array( "soforp_fast_sitemap_use_url_priority", 1 ),
        ));

        $sql = "show tables like 'search_adress'";
        $query = $this->db->query($sql);
        $this->data["hasAddresses"] = ( $query->num_rows > 0);

        $this->template = 'feed/soforp_fast_sitemap.tpl';
        $this->children = array(
            'common/header',
            'common/footer'
        );

        $this->response->setOutput($this->render(), $this->config->get('config_compression'));
    }

    private function validate() {
        if (!$this->user->hasPermission('modify', 'feed/soforp_fast_sitemap')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (!$this->error) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
}
?>
