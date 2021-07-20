<?php echo $header; ?>
<?php if ($success) { ?>
<div class="success"><?php echo $success; ?><img src="catalog/view/theme/default/image/close.png" alt="" class="close" /></div>
<?php } ?>
<?php echo $column_left; ?><?php echo $column_right; ?>
<div id="content" style="padding-left:0px;"><?php echo $content_top; ?>
  <div class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
    <?php } ?>
  </div>
 
 
 <?php
				if($_GET['route']=='product/compare'){ ?>
				<style>
					.product_compare_wrapper{display:none}
					#product_compare_wrapper{display:none}
				</style>
				
			<?php } ?>
 
  <?php if ($products) { ?>
  
    
    <div style="">
    <table class="compare-info" style="">
    <thead>
          <tr>
       <h1>Mouthpiece Comparator</h1>
            <td class="compare-product" colspan="<?php echo count($products) + 1; ?>">
       
        
        </td>
          </tr>
    </thead>
    <tbody>
     
      <tr>
        <td><?php echo $text_manufacturer; ?></td>
        <?php foreach ($products as $product) { ?>
        <td><?php echo $products[$product['product_id']]['manufacturer']; ?></td>
        <?php } ?>
      </tr>
        <tr>
        <td>Series</td>
        <?php foreach ($products as $product) { ?>
        <td><?php echo $products[$product['product_id']]['sku']; ?> </td>
        <?php } ?>
      </tr>
     <tr>
        <td><?php echo $text_model; ?></td>
        <?php foreach ($products as $product) { ?>
        <td><a href="<?php echo $products[$product['product_id']]['href']; ?>" style="font-weight: lighter; font-family: Georgia; font-size: 22px;"><?php echo $products[$product['product_id']]['model']; ?></a></td>
        <?php } ?>
      </tr>
	  <?php if ($review_status) { ?>
      <tr>
        <td><?php echo $text_rating; ?></td>
        <?php foreach ($products as $product) { ?>
        <td><img src="catalog/view/theme/default/image/stars-<?php echo $products[$product['product_id']]['rating']; ?>.png" alt="<?php echo $products[$product['product_id']]['reviews']; ?>" /><br />
          <?php echo $products[$product['product_id']]['reviews']; ?></td>
        <?php } ?>
      </tr>
      <?php } ?>
	  <tr>
        <td><?php echo $text_summary; ?></td>
        <?php foreach ($products as $product) { ?>
        <td class="description"><?php echo $products[$product['product_id']]['description']; ?></td>
        <?php } ?>
      </tr>
    </tbody>
	
	<?php if (!isset($this->request->cookie['size']) || ($this->request->cookie['size'] == 'mm')) { ?>
	
    <?php foreach ($attribute_groups as $attribute_group) { 
	if ($attribute_group['name'] != 'przedzial' && $attribute_group['name'] != 'Dimensions (inch)') {
	?>
                    <!--This is a comment. Comments are not displayed in the browser
                        <thead>
                          <tr>
                            <td class="compare-attribute" colspan="<?php echo count($products) + 1; ?>"><?php echo $attribute_group['name']; ?> </td>
                          </tr>
                        </thead>
                    -->

    <?php foreach ($attribute_group['attribute'] as $key => $attribute) { ?>
    <tbody>
      <tr>
        <td><?php echo $attribute['name']; ?></td>
        <?php foreach ($products as $product) { ?>
        <?php if (isset($products[$product['product_id']]['attribute'][$key])) { ?>
        <td><?php echo $products[$product['product_id']]['attribute'][$key]; ?></td>
        <?php } else { ?>
        <td></td>
        <?php } ?>
        <?php } ?>
      </tr>
    </tbody>
    <?php } ?>
	<?php } ?>
    <?php } ?>
    
	
	<?php } else { ?>
	
	<?php foreach ($attribute_groups as $attribute_group) { 
	if ($attribute_group['name'] != 'przedzial' && $attribute_group['name'] != 'Dimensions (mm)') {
	?>
    

    <thead>
      <tr>
        <td class="compare-attribute" colspan="<?php echo count($products) + 1; ?>"><?php echo $attribute_group['name']; ?></td>
      </tr>
    </thead>
 
    <?php foreach ($attribute_group['attribute'] as $key => $attribute) { ?>
    <tbody>
      <tr>
        <td><?php echo $attribute['name']; ?></td>
        <?php foreach ($products as $product) { ?>
        <?php if (isset($products[$product['product_id']]['attribute'][$key])) { ?>
        <td><?php echo $products[$product['product_id']]['attribute'][$key]; ?></td>
        <?php } else { ?>
        <td></td>
        <?php } ?>
        <?php } ?>
      </tr>
    </tbody>
    <?php } ?>
	<?php } ?>
    <?php } ?>
	
	 <?php } ?>
	      

    <tr>
      <td></td>
      <?php foreach ($products as $product) { ?>
      <td><a href="<?php echo $product['remove']; ?>"><img src="/image/cp_close.png"></a></td>
      <?php } ?>
    </tr>
  </table>
  </div>




  <div class="buttons">
    <div class="right"><a href="javascript:history.back()" onMouseOver="window.status='Back';return true"  class="button">Add another mouthpiece</a></div>
  </div>
  <?php } else { ?>
  <div class="content"><?php echo $text_empty; ?></div>
  <div class="buttons">
    <div class="right"><a href="javascript:history.back()" onMouseOver="window.status='Back';return true" class="button"><?php echo $button_continue; ?></a></div>
  <?php } ?>
  <?php echo $content_bottom; ?></div>
<?php echo $footer; ?>