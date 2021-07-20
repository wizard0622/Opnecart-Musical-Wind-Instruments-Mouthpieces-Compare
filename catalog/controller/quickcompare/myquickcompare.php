<?php  
class ControllerQuickcompareMyquickcompare extends Controller {
  public function index() {
    // set title of the page
    $this->document->setTitle("Quick Compare");
     
    // define template file
    if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/quickcompare/myquickcompare.tpl')) {
      $this->template = $this->config->get('config_template') . '/template/quickcompare/myquickcompare.tpl';
    } else {
      $this->template = 'default/template/quickcompare/myquickcompare.tpl';
	
    }

$this->document->addStyle('catalog/view/theme/default/stylesheet/quickcompare.css');
     
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
    $this->data['my_custom_text'] = "This is my quickcompare page.";
    $this->data['my_custom_text'] = "content";
 
    // call the "View" to render the output
    $this->response->setOutput($this->render());
  }
}
?>