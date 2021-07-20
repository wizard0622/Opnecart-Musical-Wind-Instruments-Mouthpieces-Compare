<style type="text/css">
    
</style>
<?php echo $header; ?><?php echo $column_left; ?><?php echo $column_right; ?>
<div id="content"><?php echo $content_top; ?>
  <h1><?php echo $heading_title; ?> mouthpiece comparision chart</h1>
  <?php if ($thumb || $description) { ?>
  <div class="category-info">
    <?php if ($thumb) { ?>
    <div class="image"><img src="<?php echo $thumb; ?>" alt="<?php echo $heading_title; ?>" /></div>
    <?php } ?>
    <?php if ($description) { ?>
    <?php echo $description; ?>
    <?php } ?>
  </div>
  <?php } ?>
  <?php if ($categories) { ?>
  <h2><?php echo $text_refine; ?></h2>
  
  
  



<div class="category-list">
    <?php if (count($categories) <= 5) { ?>
    <ul>
      <?php foreach ($categories as $category) { ?>
      <li><a href="<?php echo $category['href']; ?>"><?php echo $category['name']; ?></a></li>
      <?php } ?>
    </ul>
    <?php } else { ?>
    <?php for ($i = 0; $i < count($categories);) { ?>
    <ul>
      <?php $j = $i + ceil(count($categories) / 4); ?>
      <?php for (; $i < $j; $i++) { ?>
      <?php if (isset($categories[$i])) { ?>
      <li><a href="<?php echo $categories[$i]['href']; ?>"><?php echo $categories[$i]['name']; ?></a></li>
      <?php } ?>
      <?php } ?>
    </ul>
    <?php } ?>
    <?php } ?>
  </div>
  <?php } ?>
  
  

</select>

  <?php if ($products) { ?>
  <div class="product-filter">
    
    <div class="limit"><b><?php echo $text_limit; ?></b>
      <select onchange="location = this.value;">
        <?php foreach ($limits as $limits) { ?>
        <?php if ($limits['value'] == $limit) { ?>
        <option value="<?php echo $limits['href']; ?>" selected="selected"><?php echo $limits['text']; ?></option>
        <?php } else { ?>
        <option value="<?php echo $limits['href']; ?>"><?php echo $limits['text']; ?></option>
        <?php } ?>
        <?php } ?>
      </select>
    </div>
    <div class="sort"><b><?php echo $text_sort; ?></b>
      <select onchange="location = this.value;">
        <?php foreach ($sorts as $sorts) { ?>
        <?php if ($sorts['value'] == $sort . '-' . $order) { ?>
        <option value="<?php echo $sorts['href']; ?>" selected="selected"><?php echo $sorts['text']; ?></option>
        <?php } else { ?>
        <option value="<?php echo $sorts['href']; ?>"><?php echo $sorts['text']; ?></option>
        <?php } ?>
        <?php } ?>
      </select>
    </div>
  </div>
  <div class="product-compare"><a href="<?php echo $compare; ?>" id="compare-total"><?php echo $text_compare; ?></a></div>
  
  
  <div class="Tablemain">
	  <div style="Table" style="width:785px;">
				<div class="Cell" style="width:90px; font-weight:bold; padding-bottom:5px;">
					Brand
				</div>
				<div class="Cell" style="width:90px; font-weight:bold; padding-bottom:5px;">
				Series
				</div>
				<div class="Cell" style="width:150px; font-weight:bold; padding-bottom:5px;">
				Model 
				</div>
				<div class="Cell" style="width:60px; font-weight:bold; padding-bottom:5px;">
				Bore
				</div>
					<div class="Cell" style="width:60px; font-weight:bold; padding-bottom:5px;">
				Diameter
				</div>
				<div class="Cell" style="width:60px; font-weight:bold; padding-bottom:5px;">
				Cup depth
				</div>
			
			
				<div class="Cell" style="width:50px; font-weight:bold; padding-bottom:5px;">
				Details
				</div>
				<div class="Cell" style="width:100px; font-weight:bold; padding-bottom:5px;">
				Compare	
				</div>
			
						
				 
			</div>
			
			
			<?php foreach ($products as $product) { ?>  

			
			<div class="Table" style="width:785px;">
				<div class="Cell" style="width:90px;">
					<div class="name"><?php echo $product['pmmanufacturer']; ?></div>
				</div> 
				<div class="Cell" style="width:90px";>
					<div class="name"><?php echo $product['sku'] ; ?></div>
				</div> 
				<div class="Cell" style="width:150px;">
					<div class="name"><a href="<?php echo $product['href']; ?>"><?php echo $product['model'] ; ?></a></div>
				</div> 
					<div class="Cell" style="width:60px;">
					<div class="name"><a href="<?php echo $product['href']; ?>"><?php echo $product['isbn'] ; ?></a></div>
				</div> 
				<div class="Cell" style="width:60px;">
					<div class="name"><a href="<?php echo $product['href']; ?>"><?php echo $product['ean'] ; ?></a></div>
				</div> 
			<div class="Cell" style="width:60px;">
					<div class="name"><a href="<?php echo $product['href']; ?>"><?php echo $product['jan'] ; ?></a></div>
				</div> 
				<div class="Cell" style="width:50px;">
						<input type="button" value="info" onclick="location.href='<?php echo $product['href']; ?>'" class="button" />
				 
				</div> 
				<div class="Cell" style="width:100px" >
					
							<input type="button" value="Send to comparator" onclick="addToCompare('<?php echo $product['product_id']; ?>');" class="button2" />
				</div>
						  <div></div>
						
					
			</div>
			<?php } ?>
	  </div>
	  
	 
	  
  <div class="pagination"><?php echo $pagination; ?></div>
  <?php } ?>
  <?php if (!$categories && !$products) { ?>
  <div class="content"><?php echo $text_empty; ?></div>
  <div class="buttons">
    <div class="right"><a href="<?php echo $continue; ?>" class="button"><?php echo $button_continue; ?></a></div>
  </div>
  <?php } ?>
  <?php echo $content_bottom; ?></div>
<script type="text/javascript"><!--
function display(view) {
	if (view == 'list') {
		$('.product-grid').attr('class', 'product-list');
		
		$('.product-list > div').each(function(index, element) {
			html  = '<div class="right">';
			html += '  <div class="cart">' + $(element).find('.cart').html() + '</div>';
			html += '  <div class="wishlist">' + $(element).find('.wishlist').html() + '</div>';
			html += '  <div class="compare">' + $(element).find('.compare').html() + '</div>';
			html += '</div>';			
			
			html += '<div class="left">';
			
			var image = $(element).find('.image').html();
			
			if (image != null) { 
				html += '<div class="image">' + image + '</div>';
			}
			
			var price = $(element).find('.price').html();
			
			if (price != null) {
				html += '<div class="price">' + price  + '</div>';
			}
					
			html += '  <div class="name">' + $(element).find('.name').html() + '</div>';
			html += '  <div class="description">' + $(element).find('.description').html() + '</div>';
			
			var rating = $(element).find('.rating').html();
			
			if (rating != null) {
				html += '<div class="rating">' + rating + '</div>';
			}
				
			html += '</div>';
						
			$(element).html(html);
		});		
		
		$('.display').html('<b><?php echo $text_display; ?></b> <?php echo $text_list; ?> <b>/</b> <a onclick="display(\'grid\');"><?php echo $text_grid; ?></a>');
		
		$.totalStorage('display', 'list'); 
	} else {
		$('.product-list').attr('class', 'product-grid');
		
		$('.product-grid > div').each(function(index, element) {
			html = '';
			
			var image = $(element).find('.image').html();
			
			if (image != null) {
				html += '<div class="image">' + image + '</div>';
			}
			
			html += '<div class="name">' + $(element).find('.name').html() + '</div>';
			html += '<div class="description">' + $(element).find('.description').html() + '</div>';
			
			var price = $(element).find('.price').html();
			
			if (price != null) {
				html += '<div class="price">' + price  + '</div>';
			}
			
			var rating = $(element).find('.rating').html();
			
			if (rating != null) {
				html += '<div class="rating">' + rating + '</div>';
			}
						
			html += '<div class="cart">' + $(element).find('.cart').html() + '</div>';
			html += '<div class="wishlist">' + $(element).find('.wishlist').html() + '</div>';
			html += '<div class="compare">' + $(element).find('.compare').html() + '</div>';
			
			$(element).html(html);
		});	
					
		$('.display').html('<b><?php echo $text_display; ?></b> <a onclick="display(\'list\');"><?php echo $text_list; ?></a> <b>/</b> <?php echo $text_grid; ?>');
		
		$.totalStorage('display', 'grid');
	}
}

view = $.totalStorage('display');

if (view) {
	display(view);
} else {
	display('list');
}
//--></script> 
<?php echo $footer; ?>