<?php

echo $header; ?><?php echo $column_left; ?><?php echo $column_right; ?>
<div id="content"><?php echo $content_top; ?>

<script>
                    var toggle = function() {
                        var mydiv = document.getElementById('newpost');
                        if (mydiv.style.display === 'block' || mydiv.style.display === '')
                            mydiv.style.display = 'none';
                        else
                            mydiv.style.display = 'block'
                    }
                </script>
    <div class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
            <?php echo $breadcrumb['separator']; ?><a
            href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
        <?php } ?>
    </div>







            <div style="display:table;  width: 100%;">
                <div style="display: table-cell; width: 98%;">
                       <h1><?php echo $heading_title; ?></h1>
                </div>
                <div style="display: table-cell; width: 2%; padding-left: 0%; vertical-align:middle">
                        <a onclick="addToWishList('<?php echo $product_id; ?>');"><img src="/image/favorite-3-24.png"/ width="24px" height="24px"></a>
                </div>
            </div>


    <div class="product-info">

        <div class="right">
            <div style="display:table; margin-bottom: 20px; width: 100%; background-color: #f1f1f1; font-size: 1.1em; padding: 10px;">
                <div style="display: table-cell; width: 65%;">
                        <div class="description">
                                    <?php if ($manufacturer) { ?>
                                            <span><?php echo $text_manufacturer; ?></span> <?php echo $manufacturer; ?><br/>
                                    <?php } ?>
                                    <?php if ($sku) { ?>
                                            <span>Series:</span> <?php echo $sku; ?><br/>
                                    <?php } ?>
                                            <span><?php echo $text_model; ?></span> <?php echo $model; ?><br/>
                                    <?php if ($reward) { ?>
                                            <span><?php echo $text_reward; ?></span> <?php echo $reward; ?><br/>
                                    <?php } ?>
                        </div>
                </div>
                <div style="display: table-cell; width: 30%; padding-left: 5%; vertical-align:middle">


                                 <?php if(empty($this->session->data['compare']) || array_search($product_id, $this->session->data['compare']) === false) { ?>   
                                            <a class="button" id="addp2c" onclick="CompareAction(this, '<?php echo $product_id; ?>');">Add to comparator</a>
                                <?php } else { ?>
                                            <a class="button minus" id="addp2c" onclick="CompareAction(this, '<?php echo $product_id; ?>');">Remove from comparator</a>
                                <?php } ?>
                            

                </div>
            </div>


            <div style="display:table; padding-bottom: 10px; border-bottom: 1px solid #E7E7E7; width: 100%;">
                <div style="display: table-cell; width: 40%; border-right: 1px solid #E7E7E7;"><h3>Specifications</h3>



                    <form action="" method="post" enctype="multipart/form-data">
                        <div style="position: relative; left: 207px; top: -28px; height: 0px;">
    <?php if (!isset($this->request->cookie['size']) || ($this->request->cookie['size'] == 'mm')) { ?>
                            <a title="<?php echo 'mm'; ?>"><b><?php echo 'mm'; ?></b></a>
                            / <a title="<?php echo 'inch'; ?>" onclick="$('input[name=\'size\']').attr('value', '<?php echo 'inch'; ?>');
                                    $(this).parent().parent().submit();"><?php echo 'inch'; ?></a>
    <?php } else { ?>
                            <a title="<?php echo 'mm'; ?>" onclick="$('input[name=\'size\']').attr('value', '<?php echo 'mm'; ?>');
                                    $(this).parent().parent().submit();"><?php echo 'mm'; ?></a>
                            / <a title="<?php echo 'inch'; ?>"><b><?php echo 'inch'; ?></b></a>
    <?php } ?>
                            <input type="hidden" name="size" value="" />
                            <input type="hidden" name="redirect" value="#" />
                        </div>
                    </form>






        <?php
        if (!isset($this->request->cookie['size']) || ($this->request->cookie['size'] == 'mm')) {
            ?>







                    <!-- Start Additional Info -->
            <?php if ($attribute_groups) { ?>
                    <div id="" class="">
                        <table class="">

                        <?php foreach ($attribute_groups as $attribute_group) { ?>
                            <?php if ($attribute_group['name'] == 'Dimensions (mm)') { ?>


                                <?php foreach ($attribute_group['attribute'] as $attribute) { ?>
                            <tbody>
                                <tr width="auto">
                                    <td style="color:#38B0E3; width:200px;"><?php echo $attribute['name']; ?></td>
                                    <td><?php echo html_entity_decode($attribute['text']); ?></td>
                                </tr>
                            </tbody>
                                <?php } ?>
                            <?php } ?>
                        <?php } ?>
                        </table>
                        <br>

                    </div>
            <?php } ?>
                    <!-- End Additional Info -->

        <?php } else { ?>

                    <!-- Start Additional Info -->
            <?php if ($attribute_groups) { ?>
                    <div id="" class="">
                        <table class="">
                        <?php foreach ($attribute_groups as $attribute_group) { ?>
                            <?php if ($attribute_group['name'] == 'Dimensions (inch)') { ?>


                                <?php foreach ($attribute_group['attribute'] as $attribute) { ?>
                            <tbody>
                                <tr width="auto">
                                    <td style="color:#38B0E3; width:200px;"><?php echo $attribute['name']; ?></td>
                                    <td><?php echo html_entity_decode($attribute['text']); ?></td>
                                </tr>
                            </tbody>
                                <?php } ?>
                            <?php } ?>
                        <?php } ?>
                        </table>
                        <br>
                    </div>
            <?php } ?>
                    <!-- End Additional Info -->

        <?php } ?>


                    <!-- Start Additional Info -->
        <?php if ($attribute_groups) { ?>
                    <div id="" class="">
                        <table class="">
                    <?php foreach ($attribute_groups as $attribute_group) { ?>
                        <?php if ($attribute_group['name'] == 'common') { ?>


                            <?php foreach ($attribute_group['attribute'] as $attribute) { ?>
                            <tbody>
                                <tr width="auto">
                                    <td style="color:#38B0E3; width:200px;"><?php echo $attribute['name']; ?></td>
                                    <td><?php echo html_entity_decode($attribute['text']); ?></td>
                                </tr>
                            </tbody>
                            <?php } ?>
                        <?php } ?>
                    <?php } ?>
                        </table>
                        <br>
                    </div>
        <?php } ?>
                    <!-- End Additional Info -->





<h3>More</h3>


  <a href = '<?php echo $manufacturers; ?>'"><?php echo $manufacturer; ?> classic mouthpieces chart</a>
    </br> 
                    <!-- need to hide if empty -->
            
      <a href="/brochures/<?php echo $mpn; ?>.pdf"><?php echo $manufacturer; ?> mouthpiece manual (.pdf)</a>
      
                    <!--  need to hide if empty -->





<h3 style="padding-top:30px;">Advertisement</h3>
<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
<!-- Mouthpiece Comparator -->
<ins class="adsbygoogle"
     style="display:block"
     data-ad-client="ca-pub-0360785646121859"
     data-ad-slot="9787353170"
     data-ad-format="auto"></ins>
<script>
(adsbygoogle = window.adsbygoogle || []).push({});
</script>
                 

    </div>
<!-- right column start -->

                




                        <div style="display: table-cell; width: 55%; padding-left: 5%; ">

       
       
        <!-- Do not display this at the moment
                                        <form action="" method="post" enctype="multipart/form-data">
                                            <div><b><?php echo 'Size in:'; ?></b><br />
                                                <?php if (!isset($this->request->cookie['size']) || ($this->request->cookie['size'] == 'mm')) { ?>
                                                <a title="<?php echo 'mm'; ?>"><b><?php echo 'mm'; ?></b></a>
                                                <a title="<?php echo 'inch'; ?>" onclick="$('input[name=\'size\']').attr('value', '<?php echo 'inch'; ?>');
                                                        $(this).parent().parent().submit();"><?php echo 'inch'; ?></a>
                                                <?php } else { ?>
                                                <a title="<?php echo 'mm'; ?>" onclick="$('input[name=\'size\']').attr('value', '<?php echo 'mm'; ?>');
                                                        $(this).parent().parent().submit();"><?php echo 'mm'; ?></a>
                                                <a title="<?php echo 'inch'; ?>"><b><?php echo 'inch'; ?></b></a>
                                                <?php } ?>
                                                <input type="hidden" name="size" value="" />
                                                <input type="hidden" name="redirect" value="#" />
                                            </div>
                                        </form>
                    
                    -->
                    
                    
            
            
            <h3><?php echo $manufacturer; ?> <?php echo $model; ?> equivalences:  
     
    
    <select name="brand">
                                     
                                    <option value="all" selected="selected">All brands</option>
                                    <option value="Yamaha">Yamaha</option>
                                    <option value="Stomvi">Stomvi</option>
                            

                                </select>
                       

   <input type="button" class="buttons" name="mouthpiece tolerance settings" width="16" height="16" border="0px" value="settings" onclick="toggle();"></input> 

                        </h3>        
             
      
  




                    <div id="newpost" style="background:#f9f9f9; padding:10px 10px; display:none; border:1px solid #ddd;">
                        By default, this table shows the most similar mouthpieces by other manufacturers base only on two parameters: cup inner diameter and cup depth.
                     You can also turn on Bore parameter to get more precise results.
             </br></br>
<b>Set Tolerance</b></br>

                        <form id="similar-accuracy" action="" method="post" enctype="multipart/form-data">
                      <!--            <label><input type="checkbox" class="ios-switch" /></label> -->
                            <input type="hidden" name="product_diameter" value="<?php echo $ean; ?>">
                            <input type="hidden" name="product_cupdepth" value="<?php echo $jan; ?>">
							<input type="hidden" name="manu" value="<?php echo $manufacturer_id; ?>">
                            <input type="hidden" name="product_bore" value="<?php echo $isbn; ?>">
                            <input type="hidden" name="category" value="<?php echo $category_id; ?>">
                            <input type="hidden" name="mminch" value="<?php echo (isset($this->request->cookie['size'])? $this->request->cookie['size']: "mm");?>">
                            <p class="accuracy" style="display:inline"> Cup inner diameter 
                                <select name="diameter" href="javascript:void(0)" onchange="FindSimilar();">
                                    <?php if (!isset($this->request->cookie['size']) || ($this->request->cookie['size'] == 'mm')) { ?>
                                    <option value="10"  selected="selected">0,10 mm</option>
                                    <option value="20">0,20 mm</option>
                                    <option value="30">0,30 mm</option>
                                    <option value="40">0,40 mm</option>
                                    <option value="50">0,50 mm</option>
                                    <?php } else { ?>
                                    <option value="12.7" selected="selected">0,0050 inch</option>
                                    <option value="25.4">0,0100 inch</option>
                                    <option value="38.1">0,0150 inch</option>
                                    <option value="50.8">0,0200 inch</option>

                                    <?php } ?>

                                </select>
                            </p> 

                            <p class="accuracy" style="display:inline"> Cup depth        
                                <select name="cutdepth" href="javascript:void(0)" onchange="FindSimilar();">
                                    <option value="1" selected="selected">on</option>
                                    <option value="0">off</option>
                                </select>
                            </p>
                            <p class="accuracy" style="display:inline" > Bore
                                <select name="bore" href="javascript:void(0)" onchange="FindSimilar();">
                                        <?php if (!isset($this->request->cookie['size']) || ($this->request->cookie['size'] == 'mm')) { ?>
                                    <option value="10" selected="selected">0,10 mm</option>
                                    <option value="20">0,20 mm</option>
                                    <option value="30">0,30 mm</option>
                                    <option value="40">0,40 mm</option>
                                    <option value="50">0,50 mm</option>
                                    <option value="0">off</option>
                                    <?php } else { ?>
                                    <option value="12.7" selected="selected">0,0050 inch</option>
                                    <option value="25.4">0,0100 inch</option>
                                    <option value="38.1">0,0150 inch</option>
                                    <option value="50.8">0,0200 inch</option>
                                    <option value="0">off</option>
                                    <?php } ?>

                                </select>
                            </p>
                           <!-- <p class="accuracy"><a class="button" href="javascript:void(0)" onclick="FindSimilar();">Refresh</a></p> -->
                        </form> 


                    </div>





                    <table id="table-similar-products" style="width:100%; ">


                        <tr style="color:#38B0E3;width:20%;">
                            <td class="theader1">
                                Manufacturer
                            </td>
                           <td class="theader1" style="width:20%;">
                                Series
                            </td>
                            <td class="theader1"  style="width:15%;">
                                Model
                            </td>
                            <td class="theader1"  style="width:15%;">
                                Diameter
                            </td>
                            <td class="theader1"  style="width:15%;">
                                Cup Depth*
                            </td>
                            <td class="theader1"  style="width:15%;">
                                Bore
                            </td>


                        </tr>

				

            <?php /*
            $man = $this->model_catalog_manufacturer->getManufacturersByCatID($product_category_id);
            $cup = $jan;
            $similars = array();

            foreach ($man as $m) {

                $prod = $this->model_catalog_product->getSimilarProducts($product_category_id, $m['manufacturer_id'], $ean, $cup, 1);
                    
                foreach ($prod as $p) {
                   if ($p['manufacturer'] != $manufacturer) {
                        $cup_s = $p['jan'];
                                    array_push($similars, array('man' => $p['manufacturer'],'model' => $p['model'],'diam' => $p['ean'],'cup' => $cup_s,'bore' => $p['isbn'],'link' => $p['href']));
                    }
                }
            }

            foreach ($similars as $s) { ?>
                                    <tr style='display:hidden'>
                                        <td class="Cell1" style="width:20%;">
                                            <div class="name"><?php echo $s['man']; ?></div>
                                       
                                        <td class="Cell1" style="width:20%";>
                                            <div class="name"><a href="<?php echo $s['link']; ?>"><?php echo $s['model']; ?></a></div>
                                        </td>
                                        <td class="Cell1" style="width:20%;">
                                            <div class="name"><?php echo $s['diam']; ?></div>
                                        </td>

                                        <td class="Cell1" style="width:20%;">
                                            <div class="name"><?php echo $s['cup']; ?></div>
                                        </td>

                                        <td class="Cell1" style="width:20%;">
                                            <div class="name"><?php echo $s['bore']; ?></div>
                                        </td>


                                    </tr>

            <?php } */ ?>

                        </table>

                    </div>

                </div>

                <br>

    <?php if ($profiles): ?>
            <div class="option">
                <h2><span class="required">*</span><?php echo $text_payment_profile ?></h2>
                <br/>
                <select name="profile_id">
                    <option value=""><?php echo $text_select; ?></option>
                <?php foreach ($profiles as $profile): ?>
                    <option value="<?php echo $profile['profile_id'] ?>"><?php echo $profile['name'] ?></option>
                <?php endforeach; ?>
                </select>
                <br/>
                <br/>
                <span id="profile-description"></span>
                <br/>
                <br/>
            </div>
    <?php endif; ?>
    <?php if ($options) { ?>

            <div class="options">
                <h2><?php echo $text_option; ?></h2>
                <br/>
            <?php foreach ($options as $option) { ?>
                <?php if ($option['type'] == 'select') { ?>
                <div id="option-<?php echo $option['product_option_id']; ?>" class="option">
                        <?php if ($option['required']) { ?>
                    <span class="required">*</span>
                        <?php } ?>
                    <b><?php echo $option['name']; ?>:</b><br/>
                    <select name="option[<?php echo $option['product_option_id']; ?>]">
                        <option value=""><?php echo $text_select; ?></option>
                            <?php foreach ($option['option_value'] as $option_value) { ?>
                        <option
                            value="<?php echo $option_value['product_option_value_id']; ?>"><?php echo $option_value['name']; ?>
                                    <?php if ($option_value['price']) { ?>
                            (<?php echo $option_value['price_prefix']; ?><?php echo $option_value['price']; ?>)
                                    <?php } ?>
                        </option>
                            <?php } ?>
                    </select>
                </div>
                <br/>
                <?php } ?>
                <?php if ($option['type'] == 'radio') { ?>
                <div id="option-<?php echo $option['product_option_id']; ?>" class="option">
                        <?php if ($option['required']) { ?>
                    <span class="required">*</span>
                        <?php } ?>
                    <b><?php echo $option['name']; ?>:</b><br/>
                        <?php foreach ($option['option_value'] as $option_value) { ?>
                    <input type="radio" name="option[<?php echo $option['product_option_id']; ?>]"
                           value="<?php echo $option_value['product_option_value_id']; ?>"
                           id="option-value-<?php echo $option_value['product_option_value_id']; ?>"/>
                    <label
                        for="option-value-<?php echo $option_value['product_option_value_id']; ?>"><?php echo $option_value['name']; ?>
                                <?php if ($option_value['price']) { ?>
                        (<?php echo $option_value['price_prefix']; ?><?php echo $option_value['price']; ?>)
                                <?php } ?>
                    </label>
                    <br/>
                        <?php } ?>
                </div>
                <br/>
                <?php } ?>
                <?php if ($option['type'] == 'checkbox') { ?>
                <div id="option-<?php echo $option['product_option_id']; ?>" class="option">
                        <?php if ($option['required']) { ?>
                    <span class="required">*</span>
                        <?php } ?>
                    <b><?php echo $option['name']; ?>:</b><br/>
                        <?php foreach ($option['option_value'] as $option_value) { ?>
                    <input type="checkbox" name="option[<?php echo $option['product_option_id']; ?>][]"
                           value="<?php echo $option_value['product_option_value_id']; ?>"
                           id="option-value-<?php echo $option_value['product_option_value_id']; ?>"/>
                    <label
                        for="option-value-<?php echo $option_value['product_option_value_id']; ?>"><?php echo $option_value['name']; ?>
                                <?php if ($option_value['price']) { ?>
                        (<?php echo $option_value['price_prefix']; ?><?php echo $option_value['price']; ?>)
                                <?php } ?>
                    </label>
                    <br/>
                        <?php } ?>
                </div>
                <br/>
                <?php } ?>
                <?php if ($option['type'] == 'image') { ?>
                <div id="option-<?php echo $option['product_option_id']; ?>" class="option">
                        <?php if ($option['required']) { ?>
                    <span class="required">*</span>
                        <?php } ?>
                    <b><?php echo $option['name']; ?>:</b><br/>
                    <table class="option-image">
                            <?php foreach ($option['option_value'] as $option_value) { ?>
                        <tr>
                            <td style="width: 1px;"><input type="radio"
                                                           name="option[<?php echo $option['product_option_id']; ?>]"
                                                           value="<?php echo $option_value['product_option_value_id']; ?>"
                                                           id="option-value-<?php echo $option_value['product_option_value_id']; ?>"/>
                            </td>
                            <td><label
                                    for="option-value-<?php echo $option_value['product_option_value_id']; ?>"><img
                                        src="<?php echo $option_value['image']; ?>"
                                        alt="<?php echo $option_value['name'] . ($option_value['price'] ? ' ' . $option_value['price_prefix'] . $option_value['price'] : ''); ?>"/></label>
                            </td>
                            <td><label
                                    for="option-value-<?php echo $option_value['product_option_value_id']; ?>"><?php echo $option_value['name']; ?>
                                            <?php if ($option_value['price']) { ?>
                                    (<?php echo $option_value['price_prefix']; ?><?php echo $option_value['price']; ?>)
                                            <?php } ?>
                                </label></td>
                        </tr>
                            <?php } ?>
                    </table>
                </div>
                <br/>
                <?php } ?>
                <?php if ($option['type'] == 'text') { ?>
                <div id="option-<?php echo $option['product_option_id']; ?>" class="option">
                        <?php if ($option['required']) { ?>
                    <span class="required">*</span>
                        <?php } ?>
                    <b><?php echo $option['name']; ?>:</b><br/>
                    <input type="text" name="option[<?php echo $option['product_option_id']; ?>]"
                           value="<?php echo $option['option_value']; ?>"/>
                </div>
                <br/>
                <?php } ?>
                <?php if ($option['type'] == 'textarea') { ?>
                <div id="option-<?php echo $option['product_option_id']; ?>" class="option">
                        <?php if ($option['required']) { ?>
                    <span class="required">*</span>
                        <?php } ?>
                    <b><?php echo $option['name']; ?>:</b><br/>
                    <textarea name="option[<?php echo $option['product_option_id']; ?>]" cols="40"
                              rows="5"><?php echo $option['option_value']; ?></textarea>
                </div>
                <br/>
                <?php } ?>
                <?php if ($option['type'] == 'file') { ?>
                <div id="option-<?php echo $option['product_option_id']; ?>" class="option">
                        <?php if ($option['required']) { ?>
                    <span class="required">*</span>
                        <?php } ?>
                    <b><?php echo $option['name']; ?>:</b><br/>
                    <input type="button" value="<?php echo $button_upload; ?>"
                           id="button-option-<?php echo $option['product_option_id']; ?>" class="button">
                    <input type="hidden" name="option[<?php echo $option['product_option_id']; ?>]" value=""/>
                </div>
                <br/>
                <?php } ?>
                <?php if ($option['type'] == 'date') { ?>
                <div id="option-<?php echo $option['product_option_id']; ?>" class="option">
                        <?php if ($option['required']) { ?>
                    <span class="required">*</span>
                        <?php } ?>
                    <b><?php echo $option['name']; ?>:</b><br/>
                    <input type="text" name="option[<?php echo $option['product_option_id']; ?>]"
                           value="<?php echo $option['option_value']; ?>" class="date"/>
                </div>
                <br/>
                <?php } ?>
                <?php if ($option['type'] == 'datetime') { ?>
                <div id="option-<?php echo $option['product_option_id']; ?>" class="option">
                        <?php if ($option['required']) { ?>
                    <span class="required">*</span>
                        <?php } ?>
                    <b><?php echo $option['name']; ?>:</b><br/>
                    <input type="text" name="option[<?php echo $option['product_option_id']; ?>]"
                           value="<?php echo $option['option_value']; ?>" class="datetime"/>
                </div>
                <br/>
                <?php } ?>
                <?php if ($option['type'] == 'time') { ?>
                <div id="option-<?php echo $option['product_option_id']; ?>" class="option">
                        <?php if ($option['required']) { ?>
                    <span class="required">*</span>
                        <?php } ?>
                    <b><?php echo $option['name']; ?>:</b><br/>
                    <input type="text" name="option[<?php echo $option['product_option_id']; ?>]"
                           value="<?php echo $option['option_value']; ?>" class="time"/>
                </div>

                <br/>
                <?php } ?>
            <?php } ?>
            </div>

    <?php } ?>

      <div class="cart">
        <div> 
          <span class="links"><a onclick="addToWishList('<?php echo $product_id; ?>');">Add to favorites</a><br />
          <a onclick="addToCompare('<?php echo $product_id; ?>');">Add to comparator</a></span>
        
		<a style="float: right;" class="button" onclick="history.go(-1)">Go back</a>
	</div>
		
	
		
		
		
        <?php if ($minimum > 1) { ?>
                <div class="minimum"><?php echo $text_minimum; ?></div>
        <?php } ?>


            </div>

    <?php if ($review_status) { ?>

            <div class="review">
                <div><img src="catalog/view/theme/default/image/stars-<?php echo $rating; ?>.png"
                          alt="<?php echo $reviews; ?>"/>&nbsp;&nbsp;<a
                          onclick="$('a[href=\'#tab-review\']').trigger('click');"><?php echo $reviews; ?></a>&nbsp;&nbsp;|&nbsp;&nbsp;<a
                          onclick="$('a[href=\'#tab-review\']').trigger('click');"><?php echo $text_write; ?></a>&nbsp;&nbsp;|&nbsp;&nbsp;
              
              
              
              </div>
               
               
               
               
               
               
            </div>
    <?php } ?>
        </div>
    </div>
    <div id="tabs" class="htabs"><a href="#tab-description"><?php echo $tab_description; ?></a>

        <?php if ($review_status) { ?>
        <a href="#tab-review"><?php echo $tab_review; ?></a>
        <?php } ?>
        <?php if ($products) { ?>
        <a href="#tab-related"><?php echo $tab_related; ?> (<?php echo count($products); ?>)</a>
        <?php } ?>
    </div>
    <div id="tab-description" class="tab-content"><?php echo $description; ?></div>

    <?php if ($review_status) { ?>
    <div id="tab-review" class="tab-content">
        <div id="review"></div>
        <h2 id="review-title"><?php echo $text_write; ?></h2>
        <b><?php echo $entry_name; ?></b><br/>
        <input type="text" name="name" value=""/>
        <br/>
        <br/>
        <b><?php echo $entry_review; ?></b>
        <textarea name="text" cols="40" rows="8" style="width: 95%;"></textarea>
        <span style="font-size: 11px;"><?php echo $text_note; ?></span><br/>
        <br/>
        <b><?php echo $entry_rating; ?></b> <span><?php echo $entry_bad; ?></span>&nbsp;
        <input type="radio" name="rating" value="1"/>
        &nbsp;
        <input type="radio" name="rating" value="2"/>
        &nbsp;
        <input type="radio" name="rating" value="3"/>
        &nbsp;
        <input type="radio" name="rating" value="4"/>
        &nbsp;
        <input type="radio" name="rating" value="5"/>
        &nbsp;<span><?php echo $entry_good; ?></span><br/>
        <br/>
        <b><?php echo $entry_captcha; ?></b><br/>
        <input type="text" name="captcha" value=""/>
        <br/>
        <img src="index.php?route=product/product/captcha" alt="" id="captcha"/><br/>
        <br/>

        <div class="buttons">
            <div class="right"><a id="button-review" class="button"><?php echo $button_continue; ?></a></div>
        </div>
    </div>
    <?php } ?>
    <?php if ($products) { ?>
    <div id="tab-related" class="tab-content">
        <div class="box-product">
                <?php foreach ($products as $product) { ?>
            <div>
                        <?php if ($product['thumb']) { ?>
                <div class="image"><a href="<?php echo $product['href']; ?>"><img
                            src="<?php echo $product['thumb']; ?>"
                            alt="<?php echo $product['name']; ?>"/></a></div>
                        <?php } ?>
                <div class="name"><a href="<?php echo $product['href']; ?>"><?php echo $product['name']; ?></a>
                </div>
                        <?php if ($product['price']) { ?>
                <div class="price">
                                <?php if (!$product['special']) { ?>
                                    <?php echo $product['price']; ?>
                                <?php } else { ?>
                    <span class="price-old"><?php echo $product['price']; ?></span> <span
                        class="price-new"><?php echo $product['special']; ?></span>
                                <?php } ?>
                </div>
                        <?php } ?>
                        <?php if ($product['rating']) { ?>
                <div class="rating"><img
                        src="catalog/view/theme/default/image/stars-<?php echo $product['rating']; ?>.png"
                        alt="<?php echo $product['reviews']; ?>"/></div>
                        <?php } ?>
                <a onclick="addToCart('<?php echo $product['product_id']; ?>');"
                   class="button"><?php echo $button_cart; ?></a></div>
                <?php } ?>

        </div>
    </div>
    <?php } ?>
    <?php if ($tags) { ?>
    <div class="tags"><b><?php echo $text_tags; ?></b>
            <?php for ($i = 0; $i < count($tags); $i++) { ?>
                <?php if ($i < (count($tags) - 1)) { ?>
        <a href="<?php echo $tags[$i]['href']; ?>"><?php echo $tags[$i]['tag']; ?></a>,
                <?php } else { ?>
        <a href="<?php echo $tags[$i]['href']; ?>"><?php echo $tags[$i]['tag']; ?></a>
                <?php } ?>
            <?php } ?>
    </div>
    <?php } ?>
    <?php echo $content_bottom; ?></div>
<script type="text/javascript"><!--
    $(document).ready(function() {
        $('.colorbox').colorbox({
            overlayClose: true,
            opacity: 0.5,
            rel: "colorbox"
        });
    });
    //--></script>
<script type="text/javascript"><!--

    $('select[name="profile_id"], input[name="quantity"]').change(function() {
        $.ajax({
            url: 'index.php?route=product/product/getRecurringDescription',
            type: 'post',
            data: $('input[name="product_id"], input[name="quantity"], select[name="profile_id"]'),
            dataType: 'json',
            beforeSend: function() {
                $('#profile-description').html('');
            },
            success: function(json) {
                $('.success, .warning, .attention, information, .error').remove();

                if (json['success']) {
                    $('#profile-description').html(json['success']);
                }
            }
        });
    });

    $('#button-cart').bind('click', function() {
        $.ajax({
            url: 'index.php?route=checkout/cart/add',
            type: 'post',
            data: $('.product-info input[type=\'text\'], .product-info input[type=\'hidden\'], .product-info input[type=\'radio\']:checked, .product-info input[type=\'checkbox\']:checked, .product-info select, .product-info textarea'),
            dataType: 'json',
            success: function(json) {
                $('.success, .warning, .attention, information, .error').remove();

                if (json['error']) {
                    if (json['error']['option']) {
                        for (i in json['error']['option']) {
                            $('#option-' + i).after('<span class="error">' + json['error']['option'][i] + '</span>');
                        }
                    }

                    if (json['error']['profile']) {
                        $('select[name="profile_id"]').after('<span class="error">' + json['error']['profile'] + '</span>');
                    }
                }

                if (json['success']) {
                    $('#notification').html('<div class="success" style="display: none;">' + json['success'] + '<img src="catalog/view/theme/default/image/close.png" alt="" class="close" /></div>');

                    $('.success').fadeIn('slow');

                    $('#cart-total').html(json['total']);

                    $('html, body').animate({scrollTop: 0}, 'slow');
                }
            }
        });
    });
    //--></script>
<?php if ($options) { ?>
<script type="text/javascript" src="catalog/view/javascript/jquery/ajaxupload.js"></script>
    <?php foreach ($options as $option) { ?>
        <?php if ($option['type'] == 'file') { ?>
<script type="text/javascript"><!--
    new AjaxUpload('#button-option-<?php echo $option['
product_option_id
']; ?>', {
        action: 'index.php?route=product/product/upload',
        name: 'file',
        autoSubmit: true,
        responseType: 'json',
        onSubmit: function(file, extension) {
            $('#button-option-<?php echo $option['
product_option_id
']; ?>'
                    ).
                    after('<img src="catalog/view/theme/default/image/loading.gif" class="loading" style="padding-left: 5px;" />');
            $('#button-option-<?php echo $option['
product_option_id
']; ?>'
                    ).
                    attr('disabled', true);
        },
        onComplete: function(file, json) {
            $('#button-option-<?php echo $option['
product_option_id
']; ?>'
                    ).
                    attr('disabled', false);

            $('.error').remove();

            if (json['success']) {
                alert(json['success']);

                $('input[name=\'option[<?php echo $option['
    product_option_id
    ']; ?>]\']'
                        ).
                        attr('value', json['file']);
            }

            if (json['error']) {
                $('#option-<?php echo $option['
    product_option_id
    ']; ?>'
                        ).
                        after('<span class="error">' + json['error'] + '</span>');
            }

            $('.loading').remove();
        }
    }
    )
            ;
    //--></script>
        <?php } ?>
    <?php } ?>
<?php } ?>

<script type="text/javascript"><!--
    function FindSimilar() {
        var form = $('form#similar-accuracy');
//  alert(form.serialize());
        $.ajax({
            url: 'index.php?route=product/product/similar',
            type: 'post',
            dataType: 'json',
            data: form.serialize(),
            success: function(resp) {
                $('table#table-similar-products').html(resp.similar_products);
                //alert(data);
            },
        });
//  alert('Send');
    }
//--></script>

<script type="text/javascript"><!--
    $('#review .pagination a').live('click', function() {
        $('#review').fadeOut('slow');

        $('#review').load(this.href);

        $('#review').fadeIn('slow');

        return false;
    });

    $('#review').load('index.php?route=product/product/review&product_id=<?php echo $product_id; ?>');

    $('#button-review').bind('click', function() {
        $.ajax({
            url: 'index.php?route=product/product/write&product_id=<?php echo $product_id; ?>',
            type: 'post',
            dataType: 'json',
            data: 'name=' + encodeURIComponent($('input[name=\'name\']').val()) + '&text=' + encodeURIComponent($('textarea[name=\'text\']').val()) + '&rating=' + encodeURIComponent($('input[name=\'rating\']:checked').val() ? $('input[name=\'rating\']:checked').val() : '') + '&captcha=' + encodeURIComponent($('input[name=\'captcha\']').val()),
            beforeSend: function() {
                $('.success, .warning').remove();
                $('#button-review').attr('disabled', true);
                $('#review-title').after('<div class="attention"><img src="catalog/view/theme/default/image/loading.gif" alt="" /> <?php echo $text_wait; ?></div>');
            },
            complete: function() {
                $('#button-review').attr('disabled', false);
                $('.attention').remove();
            },
            success: function(data) {
                if (data['error']) {
                    $('#review-title').after('<div class="warning">' + data['error'] + '</div>');
                }

                if (data['success']) {
                    $('#review-title').after('<div class="success">' + data['success'] + '</div>');

                    $('input[name=\'name\']').val('');
                    $('textarea[name=\'text\']').val('');
                    $('input[name=\'rating\']:checked').attr('checked', '');
                    $('input[name=\'captcha\']').val('');
                }
            }
        });
    });
    //--></script>
<script type="text/javascript"><!--
    $('#tabs a').tabs();
    //--></script>
<script type="text/javascript" src="catalog/view/javascript/jquery/ui/jquery-ui-timepicker-addon.js"></script>
<script type="text/javascript"><!--
    $(document).ready(function() {
        if ($.browser.msie && $.browser.version == 6) {
            $('.date, .datetime, .time').bgIframe();
        }

        $('.date').datepicker({dateFormat: 'yy-mm-dd'});
        $('.datetime').datetimepicker({
            dateFormat: 'yy-mm-dd',
            timeFormat: 'h:m'
        });
        $('.time').timepicker({timeFormat: 'h:m'});
    });
    //--></script>
<script type="text/javascript"><!--
    $(document).ready(function() {
        FindSimilar();
    });
    //--></script>
<?php echo $footer; ?>