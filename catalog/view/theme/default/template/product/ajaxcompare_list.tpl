<?php if(count($compare_list) > 0) { ?>
 

 
    
    
    
<div id="product_compare_list">
    
    <div id="pList">
	<?php for($i = 0; $i < 8; $i++) { ?>

	    <?php if(isset($compare_list[$i])) { ?> 
		<div class="cProductInfo" id="compare-wrapper-cPI-<?php echo $compare_list[$i]['product_id']; ?>">
		    <div class="pImage">
			<img src="<?php echo $compare_list[$i]['product_image']; ?>" />
		    </div>
		    <div>
            
            <div class="pName" style=" ">
               
              <div class="mouthpiecebox"><?php echo $compare_list[$i]['product_model']; ?> </div>
              <div style="padding:5px 5px;   color:#ffcc00;   overflow: hidden; clear:both;  font-size: 16px; text-align: center;"><?php echo $compare_list[$i]['product_manufacturer']; ?> </div>
                <div style="padding:5px 5px;    clear: both; font-size: 13px;text-align: center;"><?php echo $compare_list[$i]['product_sku']; ?> </div>
 
          </div>
        
        
        </div>
                 
		    <a href="javascript:;" class="cProductRemove" onclick="cProductRemoveNew(<?php echo $compare_list[$i]['product_id']; ?>);"></a>
		</div>
	    <?php } else { ?>

		<div class="cProductInfo">
		    <div class="pImage noImage"><?php echo $text_add_sign; ?></div>
		    <div class="pName"><img style="padding:5px 20px;" src="/image/logo.png" /></div>
		</div>

	    <?php } ?>

	<?php } ?>
    </div>
    
    <?php if(count($compare_list) >= 2) { ?>
	<a href="<?php echo $compare_url; ?>" id="pCompareButton" class="button"><?php echo $button_product_compare; ?></a>
    <?php } else { ?>
	<a href="javascript:;" id="pCompareButton" class="button disable" target="_blank"><?php echo $button_product_compare; ?></a>
    <?php } ?>
</div>

<a href="javascript:;" id="cProductBoxRemove" onclick="removeCProducts()"></a>

<script type="text/javascript"><!--
function addCBlock(){
    
    var htmlContent = "";
    
    htmlContent += '<div class="cProductInfo">';
	htmlContent += '<div class="pImage noImage"><?php echo $text_add_sign; ?></div>';
	htmlContent += '<div class="pName"><img style="padding:5px 20px;" src="/image/logo.png" /></div>';
    htmlContent += '</div>';
    
    $('#pList').append(htmlContent);
}
//--></script>

<?php } ?>