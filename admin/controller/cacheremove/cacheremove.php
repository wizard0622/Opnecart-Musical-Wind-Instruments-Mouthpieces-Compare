<?php

class Controllercacheremovecacheremove extends Controller {

    public function index() {
        $this->document->setTitle($this->language->get('Cache module'));
        $this->language->load('cacheremove/cacheremove');
        // VARS
        $this->data['heading_title'] = $this->language->get('text_cacheremove');
        $this->data['text_mode'] = $this->language->get('text_mode');
        $this->data['text_vqcache'] = $this->language->get('text_vqcache');
        $this->data['text_system'] = $this->language->get('text_system');
        $this->template = 'cacheremove/cacheremove.tpl';
        $this->children = array(
            'common/header',
            'common/footer'
        );
        $this->data['token'] = $this->session->data['token'];
        $this->data['breadcrumbs'] = array();
        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => false
        );
        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_cacheremove'),
            'href' => $this->url->link('cacheremove/cacheremove', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => ' :: '
        );
        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('cacheremove/cacheremove', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => ' :: '
        );
        if ($this->request->server['REQUEST_METHOD'] == 'POST') {
            $this->model_setting_setting->editSetting('cacheremove', $this->request->post);
            $this->redirect($this->url->link('cacheremove/cacheremove', 'token=' . $this->session->data['token'], 'SSL'));
        }
        $this->data['action'] = $this->url->link('cacheremove/cacheremove', 'token=' . $this->session->data['token'], 'SSL');
        $this->data['cancel'] = $this->url->link('cacheremove/cacheremove', 'token=' . $this->session->data['token'], 'SSL');
        $this->data['mod_cache'] = "vqmod/mods.cache";
        $this->data['mod_vqcache'] = "vqmod/vqcache";
        $this->data['mod_system'] = "system/cache";

        $this->response->setOutput($this->render());
    }

    public function delete() {
        if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
            $is_file = $this->request->post['is_file'];
            if ($is_file) {
                $this->_deleteFile();
            } else {
                $this->_truncateDir();
            }
            $data['error']="Cache Content deleted.";
            $data['type']="success";
            echo json_encode($data);
        }
    }

    private function _deleteFile() {
        $file = $this->request->post['file'];
        $full_path = DIR_CATALOG . $file;
        $full_path = str_replace('/catalog', '', $full_path);
        if (is_writable($full_path)) {
            file_exists($full_path);
            unlink($full_path);
        } else {
            echo $file . " " . 'file is not writable Please contact your System administrator';
        }
    }

    private function _truncateDir() {
        $dir = $this->request->post['file'];
        $full_path = DIR_CATALOG . $dir;
        $full_path = str_replace('/catalog', '', $full_path);
        $files = glob($full_path . '/*'); // get all file names
        foreach ($files as $file) { // iterate files
            if (is_file($file))
                unlink($file); // delete file
        }
    }
}
