<?php  
class ControllerMySettingsMysettings extends Controller {
  public function index() {
    // set title of the page
    $this->document->setTitle("User settings");
     
    // define template file
    if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/mysettings/mysettings.tpl')) {
      $this->template = $this->config->get('config_template') . '/template/mysettings/mysettings.tpl';
    } else {
      $this->template = 'default/template/mysettings/mysettings.tpl';
    }
     
    // define children templates
    $this->children = array(
      'common/column_left',
      'common/column_right',
      'common/content_top',
      'common/content_bottom',
      'common/footer',
      'common/header'
    );
     
    // set data to the variable
    $this->data['my_custom_text'] = "This is my custom page.";
 
    // call the "View" to render the output
    $this->response->setOutput($this->render());
  }
}
?>