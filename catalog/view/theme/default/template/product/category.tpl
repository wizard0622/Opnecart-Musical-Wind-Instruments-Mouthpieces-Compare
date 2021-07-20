<?php echo $header; ?>
  <div class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
    <?php } ?>
  </div>
    <h1><?php echo $heading_title; ?> <?php echo 'Mouthpiece Comparison Chart'; ?> </h1>
    <div id="descr" style="line-height:1.5em;">
	This <?php echo $heading_title; ?>Mouthpiece Comparison Chart description.id 010..</br></br>



	</div>

<?php echo $column_left; ?><?php echo $column_right; ?>



    <div id="content"><?php echo $content_top; ?>

    <?php if ($thumb || $description) { ?>
        <div class="category-info">
            <?php if ($thumb) { ?>
                <div class="image"><img src="<?php echo $thumb; ?>" alt="<?php echo $heading_title; ?>"/></div>
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


Size in:
                <select>
                    <option value=="mm" selected><b>mm</b></option>
                    <option value="inch">inch</option>
                </select>
        
        
            <div class="limit"><b><?php echo $text_limit; ?></b>
                <select onchange="location = this.value;">
                    <?php foreach ($limits as $limits) { ?>
                        <?php if ($limits['value'] == $limit) { ?>
                            <option value="<?php echo $limits['href']; ?>"
                                    selected="selected"><?php echo $limits['text']; ?></option>
                        <?php } else { ?>
                            <option value="<?php echo $limits['href']; ?>"><?php echo $limits['text']; ?></option>
                        <?php } ?>
                    <?php } ?>
                </select>
            </div>
        </div>
     
  






        <table id="mouthpiecetable" style="width:99%;">
                     <tr class="theader">
                                                <?php foreach ($sorts as $sorts) { ?>
                                                    <?php if ($sorts['value'] == $sort . '-' . $order) { ?>

                                                        <td class="theader">
                                                            <a href="<?php echo $sorts['href'] . '&order=DESC'; ?>"><?php echo $sorts['text'] . '&#9660;'; ?></a>
														
                                                        </td>

                                                    <?php } else { ?>
                                                        <td class="theader">
                                                          <a href="<?php echo $sorts['href'] . '&order=ASC'; ?>"><?php echo $sorts['text'] . '&#9650;'; ?></a>
														 
                                                        </td>
                                                    <?php } ?>
                                                <?php } ?>

                                                <td class="theader"> 
                                                <?php echo 'Compare'; ?>
                                                    
                                                </td>
                                       
                                                
                                            </tr>


            <?php foreach ($products as $product) {

                $bore_mm = '';
				$bore_inch = '';
				
                $diameter_mm = '';
				$diameter_inch = '';
				
                $odiameter_mm = '';
				$odiameter_inch = '';
                
				$cup = '';
  

                foreach ($product['attribute_groups'] as $attribute_group) {
                    foreach ($attribute_group['attribute'] as $attribute) {
                        
                        
                                            if ($attribute['attribute_id'] == '29') {
                            $bore_inch = $attribute['text'];
                        }
						if ($attribute['attribute_id'] == '26') {
                            $bore_mm = $attribute['text'];
                        }
            
            
                        if ($attribute['attribute_id'] == '24') {
                            $diameter_inch = $attribute['text'];
                        }
						if ($attribute['attribute_id'] == '23') {
                            $diameter_mm = $attribute['text'];
                        }
                       if ($attribute['attribute_id'] == '34') {
                            $cup = $attribute['text'];
                        }
            
            
                        
            
           
                    }
                }

                ?>

                <tr>

                   
                   
                   
                    <td class="Cell" style="width:auto;">
            
            
            
                    <div class="name"><?php echo $product['pmmanufacturer']; ?></div>
                    </td>
                    <td class="Cell" style="width:auto";>
                    <div class="name"><?php echo $product['sku']; ?></div>
                    </td>


                     



                    <td class="Cell" style="width:auto;">
                        <div class="name"><a href="<?php echo $product['href']; ?>" ><?php echo $product['model']; ?></a></div>
                    </td>
                 
                    <?php
                    if (isset($this->request->cookie['size']) && ($this->request->cookie['size'] == 'inch')) {
                        ?>
                        <td class="Cell" style="width:auto;">
                            <div class="name">
                                <?php

                                echo $bore_inch;

                                ?>
                            </div>
                        </td>
                        <td class="Cell" style="width:auto;">
                            <div class="name">
                                <?php

                                echo $diameter_inch;

                                ?>
                            </div>
                        </td>
					<td class="Cell" style="width:auto;">
                        <div class="name"><?php echo $product['jan']; ?></a></div>
                    </td>

                     

                    <?php
                    } else {
                        ?>

                        <td class="Cell" style="width:auto;">
                            <div class="name">
							<?php 
							    echo $bore_mm; 
							?>
							</div>
                        </td>
                        <td class="Cell" style="width:auto;">
                            <div class="name">
                                <?php
                                echo $diameter_mm;
                                ?>
                            </div>
                        </td>
						
                        <td class="Cell" style="width:auto;">
                             <div class="name"><?php echo $product['jan']; ?></a></div>
                        </td>

                                           
				
						
                    <?php } ?>

                   <?php if(0) {?>
        <td class="Cell" style="width:auto;">

<input type="image" value="Send to comparator" style="padding: 5px;" src="../image/minus.png"
                               onclick="addToCompare('<?php echo $product['product_id']; ?>');" />

                    </td>









<?php } ?>
            <td class="Cell" id="Cell-<?php echo $product['product_id']; ?>" onclick="CompareAction(this,<?php echo $product['product_id']; ?>)">
        <div class="compare-icon<?php if(isset($this->session->data['compare']) && in_array($product['product_id'],$this->session->data['compare'])) {?> minus<?php } ?>" style="width:auto;">
         </div>
                    
 <td class="Cell" style="width:auto;">
                             <div class="name"><?php echo $product['length']; ?></a></div>
                        </td>
 <td class="Cell" style="width:auto;">
                             <div class="name"><?php echo $product['width']; ?></a></div>
                        </td>
 <td class="Cell" style="width:auto;">
                             <div class="name"><?php echo $product['height']; ?></a></div>
                        </td>
                </tr>
            <?php } ?>
			<tr><td colspan=7><br>


        <div class="pagination"><?php echo $pagination; ?></div></td></tr>
    
    
        </table>
    
      <div style="float:right; padding-bottom:150px;">
     	<input type="submit" value="Compare now!" onclick="window.location='/index.php?route=product/compare';">
     	<a href="<?php echo $compare; ?>" id="compare-total"><?php echo $text_compare; ?></a>

	 </div>
 

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

                $('.product-list > div').each(function (index, element) {
                    html = '<div class="right">';
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
                        html += '<div class="price">' + price + '</div>';
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

                $('.product-grid > div').each(function (index, element) {
                    html = '';

                    var image = $(element).find('.image').html();

                    if (image != null) {
                        html += '<div class="image">' + image + '</div>';
                    }

                    html += '<div class="name">' + $(element).find('.name').html() + '</div>';
                    html += '<div class="description">' + $(element).find('.description').html() + '</div>';

                    var price = $(element).find('.price').html();

                    if (price != null) {
                        html += '<div class="price">' + price + '</div>';
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