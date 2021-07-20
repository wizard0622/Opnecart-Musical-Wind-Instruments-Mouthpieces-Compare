<?php
echo $header; ?><?php echo $column_left; ?><?php echo $column_right; ?>
<div id="content"><?php echo $content_top; ?>


        
<script>
        $(document).ready(function() {
    $(".tabs-menu a").click(function(event) {
        event.preventDefault();
        $(this).parent().addClass("current");
        $(this).parent().siblings().removeClass("current");
        var tab = $(this).attr("href");
        $(".tab-content").not(tab).css("display", "none");
        $(tab).fadeIn();
    });
});
  </script>





    










<div class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
            <?php echo $breadcrumb['separator']; ?><a
            href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
        <?php } ?>
    </div>
 



<div class="product-info">
        <div class="right">
                <div style="display:table; margin-bottom: 0px; width: 100%; background-color:#fafafa; font-size: 1.1em;">
                    <div style="display: table-cell; width: 75%;  padding: 0px 25px;">

			
				<span xmlns:v="http://rdf.data-vocabulary.org/#">
				<?php foreach ($mbreadcrumbs as $mbreadcrumb) { ?>
				<span typeof="v:Breadcrumb"><a rel="v:url" property="v:title" href="<?php echo $mbreadcrumb['href']; ?>" alt="<?php echo $mbreadcrumb['text']; ?>"></a></span>
				<?php } ?>				
				</span>
			
				<span itemscope itemtype="http://schema.org/Product">
				<meta itemprop="url" content="<?php $mlink = end($breadcrumbs); echo $mlink['href']; ?>" >
				<meta itemprop="name" content="<?php echo $heading_title; ?>" >
				<meta itemprop="model" content="<?php echo $model; ?>" >
				<meta itemprop="manufacturer" content="<?php echo $manufacturer; ?>" >
				
				<?php if ($thumb) { ?>
				<meta itemprop="image" content="<?php echo $thumb; ?>" >
				<?php } ?>
				
				<?php if ($images) { foreach ($images as $image) {?>
				<meta itemprop="image" content="<?php echo $image['thumb']; ?>" >
				<?php } } ?>
				
				<span itemprop="offers" itemscope itemtype="http://schema.org/Offer">
				<meta itemprop="price" content="<?php echo ($special ? $special : $price); ?>" />
				<meta itemprop="priceCurrency" content="<?php echo $this->currency->getCode(); ?>" />
				<link itemprop="availability" href="http://schema.org/<?php echo (($quantity > 0) ? "InStock" : "OutOfStock") ?>" />
				</span>
				
				<span itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating">
				<meta itemprop="reviewCount" content="<?php echo $review_no; ?>">
				<meta itemprop="ratingValue" content="<?php echo $rating; ?>">
				</span></span>
            
			
                            <h1><?php echo $heading_title; ?></h1>
                
                <div><img src="catalog/view/theme/default/image/stars-<?php echo $rating; ?>.png" href="#" alt="<?php echo $reviews; ?>"/>&nbsp;&nbsp;<a id="rate" style="font-size:1.2em;" 
				
				>
                          <!--onclick="$('a[href=\'#tab-review\']').trigger('click');" -->
						  <?php echo $reviews; ?></a></div>
              <div ></div>
          
          
          
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
                       <div style="display: table-cell; width: 10%;  vertical-align:middle">
                        
                           
                                 <?php if(empty($this->session->data['compare']) || array_search($product_id, $this->session->data['compare']) === false) { ?>   
                                            <a class="" id="addp2c" onclick="CompareAction(this, '<?php echo $product_id; ?>');"><i class="fa fa-check-circle-o fa-5x" ></i></a>
                                <?php } else { ?>
                                            <a class=" minus" id="addp2c" onclick="CompareAction(this, '<?php echo $product_id; ?>');"><i class="fa fa-check-circle fa-5x" style="color:#00ff00;"></i></a>
                                <?php } ?>
                           
                           
						 
                        </div>
                </div>
        </div>
</div>
 


<div id="tabs-container">
    <ul class="tabs-menu">
        <li class="current"><a href="#tab-1">Specifications</a></li>
        <li><a href="#tab-2">Equivalences</a></li>
        <li><a href="#">I play it</a></li>
        <li><a href="#"onclick="addToWishList('<?php echo $product_id; ?>');">Save</a></li> 
        <li><a href="#tab-3">Rate it</a></li>
        <li><a href="#">Share</a></li>
        <li><a href="#">Print</a></li>
        
       
     
    </ul>
    <div class="tab">
        <div id="tab-1" class="tab-content">
           

                <div style="display:table; padding-bottom: 10px;width: 100%;">
                    
                    <!-- SPECIFICATIONS START -->
                    <div style="display: table-cell; width: 55%; border-right: 1px solid #E7E7E7; padding-right:20px;">
                            <h3><?php echo $heading_title; ?></h3>
                            <div style="padding 10px 0px"><?php echo $description; ?></div>
                </br>

                                                                          


                                                                            <?php
                                                                            if (!isset($this->request->cookie['size']) || ($this->request->cookie['size'] == 'mm')) {
                                                                                ?>

                                                                                                    <!-- Start Additional Info -->
                                                                                            <?php if ($attribute_groups) { ?>
                                                                                                    <div class="leftspecifications">
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
                                                              

                                                                                            <?php } else { ?>

                                                                                            <!-- Start Additional Info -->
                                                                                            <?php if ($attribute_groups) { ?>
                                                                <div>



                                                 

                                                                                        <table class="leftspecifications">
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

                                                                                 <!-- Start specification Info -->
                                                                                        <?php if ($attribute_groups) { ?>
                                                                                                    <div>
                                                                                                        <table class="leftspecifications">
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
                                                                                                       
                    </div>
            

                                                                                        <?php } ?>
                    <!-- SPECIFICATIONS END -->



                                        <!-- MORE -->
                                        <h3>More</h3>

                                                                                                                <div class="leftspecifications">
                                                                                                                            <a href = '<?php echo $manufacturers; ?>'"><?php echo $manufacturer; ?> classic mouthpieces chart</a></br> 
                                                                                                                        
                                                                                                                            <!-- need to hide if empty -->
                                                                                                                            <a href="/brochures/<?php echo $mpn; ?>.pdf"><?php echo $manufacturer; ?> mouthpiece manual (.pdf)</a>
                                                                                                                        <!--  need to hide if empty -->
                                                                                                                </div>

                                                                                         <!-- MORE -->


                        </div>

                        <!-- RIGHT-->  
                        <div>
                                  
                                            <div style="width:410px; display:table-cell; padding-left:20px;">

                                                    <!-- May we recommend-->
                                                   
                                                    <h3>May we recommend</h3> 

 
                                                                                   <!--  dodac filter wyswietl tylko dla marek Curry, Denis Wick, Jet Tone, Kelly, Laskey, Marcinkiewicz, Monette, PArduba, Schilke, Vicent Bach, Warburton, Yamaha
                                                                                        z amazona else reklama amazona  dodac nazwe instrumentu przed slowem mouthpiece dla atrybutu amzn_assoc_default_search_key
                                                                                                    powinien szukac juz przy ladowaniu, nie po chwili a wyszukiwarke mozna ukryc. -->
                                                        <div class="amazon4mc" style="background:#fff;">
                                                                    <script charset="utf-8" type="text/javascript">
                                                                    amzn_assoc_ad_type = "responsive_search_widget";
                                                                    amzn_assoc_tracking_id = "widgetsamazon-20";
                                                                    amzn_assoc_link_id = "BKWKK3WLCLPIYV53";
                                                                    amzn_assoc_marketplace = "amazon";
                                                                    amzn_assoc_region = "US";
                                                                    amzn_assoc_placement = "";
                                                                    amzn_assoc_search_type = "search_widget";
                                                                    amzn_assoc_width = "auto";
                                                                    amzn_assoc_height = "auto";
                                                                    amzn_assoc_default_search_category = "MusicalInstruments";
                                                                    amzn_assoc_default_search_key = "<?php echo $manufacturer; ?> mouthpiece <?php echo $model; ?> ";
                                                                    amzn_assoc_theme = "light";
                                                                    amzn_assoc_bg_color = "FFFFFF";
                                                                    </script>
                                                                    <script src="//z-na.amazon-adsystem.com/widgets/q?ServiceVersion=20070822&Operation=GetScript&ID=OneJS&WS=1&MarketPlace=US"></script>
                                                        </div>
                                                </div>
                               
                                                          <!-- May we recommend-->
                        </div>     
                        <!-- RIGHT-->
                            </div>


                                        


                                        
                                    </div>

        <div id="tab-2" class="tab-content">
            
                                  
 
                    <div style="display: table-cell; width: 58%; padding-left: 2%; background;#ffcc00: ">

                    <div style="  padding:10px 10px; ">
            
                    This table shows the most similar mouthpieces by other manufacturers based on different parameters. Change those parameters to expand or narrow the result.</br>
                                                    </br>
                            
  <div style="width:980px; background:#ffcc00; margin-left: auto; margin-right: auto;">
    <div style="float: left; width: 43%;">
                                            <form id="similar-accuracy" action="" method="post" enctype="multipart/form-data">           
                                            <select name="manu" href="javascript:void(0)" onchange="FindSimilar();">
                                                                            <option value="0" selected="selected">All brands</option>
                                                    <?php if (!isset($this->request->cookie['size']) || ($this->request->cookie['size'] == 'mm')) { ?>
                                                                                                            <?php 
                                                                                                            $man = $this->model_catalog_manufacturer->getManufacturersByCatID($product_category_id);
                                                                                                            foreach($man as $w=>$row)
                                                                                                            {
                                                                                                                    echo '<option value='.$row['manufacturer_id'].'>'.$row['name'].'</option>';
                                                                                                            }
                                                            ?>
                                                        <?php } ?>
                                            </select>
    </div>
    <div style="float: left; width: 45%;">
                                  <!--            <label><input type="checkbox" class="ios-switch" /></label> -->
                                        <input type="hidden" name="product_diameter" value="<?php echo $ean; ?>">
                                        <input type="hidden" name="product_cupdepth" value="<?php echo $jan; ?>">
                                        <input type="hidden" name="product_bore" value="<?php echo $isbn; ?>">
                                        <input type="hidden" name="category" value="<?php echo $category_id; ?>">
                                        <input type="hidden" name="mminch" value="<?php echo (isset($this->request->cookie['size'])? $this->request->cookie['size']: "mm");?>">
                                            <p class="accuracy" style="display:inline; margin-right:100px;"> 
                                                <select name="diameter" href="javascript:void(0)" onchange="FindSimilar();">
                                                    <?php if (!isset($this->request->cookie['size']) || ($this->request->cookie['size'] == 'mm')) { ?>
                                                                                        <option value="0"  selected="selected">Exact Match</option>
                                                    <option value="10">+/- 0,10 mm</option>
                                                    <option value="20">+/- 0,20 mm</option>
                                                    <option value="30">+/- 0,30 mm</option>
                                                    <option value="40">+/- 0,40 mm</option>
                                                    <option value="50">+/- 0,50 mm</option>
                                                    <?php } else { ?>
                                                                                        <option value="0"  selected="selected">0,00 mm</option>
                                                    <option value="12.7">+/- 0,0050 inch</option>
                                                    <option value="25.4">+/- 0,0100 inch</option>
                                                    <option value="38.1">+/- 0,0150 inch</option>
                                                    <option value="50.8">+/- 0,0200 inch</option>

                                                    <?php } ?>

                                                </select>
                                            </p> 

                                            <p class="accuracy" style="display:inline; margin-right:90px;">        
                                                <select name="cutdepth" href="javascript:void(0)" onchange="FindSimilar();">
                                                    <option value="1" >on</option>
                                                    <option value="0" selected="selected">off</option>
                                                </select>
                                            </p>
                                            <p class="accuracy" style="display:inline; margin-right:100px;">
                                                <select name="bore" href="javascript:void(0)" onchange="FindSimilar();">
                                                        <?php if (!isset($this->request->cookie['size']) || ($this->request->cookie['size'] == 'mm')) { ?>
                                                                                                <option value="0">Exact Match</option>
                                                    <option value="10">+/- 0,10 mm</option>
                                                    <option value="20">+/- 0,20 mm</option>
                                                    <option value="30">+/- 0,30 mm</option>
                                                    <option value="40">+/- 0,40 mm</option>
                                                    <option value="50">+/- 0,50 mm</option>
                                                    <option value="off"  selected="selected">off</option>
                                                    <?php } else { ?>
                                                                                        <option value="0" >0,00 mm</option>
                                                    <option value="12.7">+/- 0,0050 inch</option>
                                                    <option value="25.4">+/- 0,0100 inch</option>
                                                    <option value="38.1">+/- 0,0150 inch</option>
                                                    <option value="50.8">+/- 0,0200 inch</option>
                                                    <option value="off" selected="selected">off</option>
                                                    <?php } ?>

                                                </select>
                                            </p>
                                                                    
                                       <!-- <p class="accuracy"><a class="button" href="javascript:void(0)" onclick="FindSimilar();">Refresh</a></p> -->
                                    </form> 


                    </div>
                </div>
            </div>
<div style="clear:both;"></div>


                    <table id="table-similar-products" style="width:100%; ">


                        <tr style="color:#38B0E3;">
                            <td class="theader1">
                                Manufacturer
                            </td>
                           <td class="theader1">
                                Series
                            </td>
                            <td class="theader1">
                                Model
                            </td>
                            <td class="theader1">
                                Diameter
                            </td>
                            <td class="theader1">
                                Cup Depth*
                            </td>
                            <td class="theader1">
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
                                        <td class="Cell1">
                                            <div class="name"><?php echo $s['man']; ?></div>
                                       
                                        <td class="Cell1">
                                            <div class="name"><a href="<?php echo $s['link']; ?>"><?php echo $s['model']; ?></a></div>
                                        </td>
                                        <td class="Cell1">
                                            <div class="name"><?php echo $s['diam']; ?></div>
                                        </td>

                                        <td class="Cell1">
                                            <div class="name"><?php echo $s['cup']; ?></div>
                                        </td>

                                        <td class="Cell1">
                                            <div class="name"><?php echo $s['bore']; ?></div>
                                        </td>


                                                </tr>

                        <?php } */ ?>

                                    </table>
                                </div>
        </div>
		 <div id="tab-3" class="tab-content">
		 <h2 id="review-title"><?php echo $text_write; ?></h2>
                    <b><?php echo $entry_name; ?></b><br/>
                    <input type="text" name="name" value=""/>
                    <br/>
                    <br/>
                <b><?php echo $entry_review; ?></b>
                <textarea name="text" cols="40" rows="8" style="width: 98%;"></textarea>
                <span style="font-size: 11px;"><?php echo $text_note; ?></span><br />
                <br />
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
                            </div>
                        </div>
<div style="clear:both;"></div>

   <!-- BOTTOM-->

   
    <div class="product-info">
        <div>

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
      <div style="padding:10px;">

                   <h3>Share</h3>
            

<div class='shareaholic-canvas' data-app='share_buttons' data-app-id=''></div>
       
       
    </div>
       <div style="padding:10px;" id="rate">

                   <h3>Reviews</h3>
                    <div id="review"></div>
                    
  
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
new AjaxUpload('#button-option-<?php echo $option['product_option_id']; ?>', {
	action: 'index.php?route=product/product/upload',
	name: 'file',
	autoSubmit: true,
	responseType: 'json',
	onSubmit: function(file, extension) {
	$('#button-option-<?php echo $option['product_option_id']; ?>').after('<img src="catalog/view/theme/default/image/loading.gif" class="loading" style="padding-left: 5px;" />');
		$('#button-option-<?php echo $option['product_option_id']; ?>').attr('disabled', true);
	},
	onComplete: function(file, json) {
		$('#button-option-<?php echo $option['product_option_id']; ?>').attr('disabled', false);
		
		$('.error').remove();
		
		if (json['success']) {
			alert(json['success']);
			
			$('input[name=\'option[<?php echo $option['product_option_id']; ?>]\']').attr('value', json['file']);
		}
		
		if (json['error']) {
			$('#option-<?php echo $option['product_option_id']; ?>').after('<span class="error">' + json['error'] + '</span>');
		}
		
		$('.loading').remove();	
	}
});
//--></script>
        <?php } ?>
    <?php } ?>
<?php } ?>

<script type="text/javascript"><!--



    function FindSimilar() {
        var form = $('form#similar-accuracy');
// alert(form.serialize());
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