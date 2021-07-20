<?php echo $header; ?>

<!-- Facebook share --->
<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.5&appId=150169648654784";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>
<!-- Facebook share --->




<?php echo $column_left; ?><?php echo $column_right; ?>
<div id="content"><?php echo $content_top; ?>
  <div class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
    <?php } ?>
  </div>
  <h1><?php echo $heading_title; ?></h1>
  
  <?php 
	
	if (isset($this->request->get['manufacturer_id'])) {
			$manufacturer_id = $this->request->get['manufacturer_id'];
		} else {
			$manufacturer_id = 0;
		} 
		if (isset($this->request->get['category_id'])) {
		$category_id = $this->request->get['category_id'];
		}
		elseif (isset($this->request->cookie['cat'])) {
			$category_id = $this->request->cookie['cat'];
		}
		
		if (isset($this->request->get['filter_series'])) {
	        $filter_series = $this->request->get['filter_series'];
		} else {
			$filter_series = 'All';
		} 
		
		/*$this->load->model('catalog/manufacturer');
		
		$series = $this->model_catalog_manufacturer->getSeries($manufacturer_id, $category_id);*/
		
	?>
  
  <?php if ($products) { ?>
  <div class="product-filter">
  
<div style="float: left;">
    
        <?php echo 'Brand: ';  ?>
        <select onchange="location = this.value;">
            <?php foreach($manufacturer_list as $manufacturer) { ?>
              <option value="<?php echo $this->url->link('product/manufacturer/product', (isset($this->request->get['category_id']) ? 'category_id='. $this->request->get['category_id'] .'&' : '' ) . 'manufacturer_id=' . $manufacturer['manufacturer_id']); ?>"<?php if($manufacturer_id == $manufacturer['manufacturer_id']) {?>selected="selected"<?php } ?>><?php echo $manufacturer['name']; ?></option>
            <?php } ?>
        </select>
        <!--
        <?php var_dump($this->session->data['compare']); ?>
        <?php var_dump($this->request->get['category_id']); ?>
        -->
                    
                    
                    
                    
                    
	<?php echo 'Series: ';  ?>
        <?php if(0) {?>
              <select onchange="location = this.value;">
                  <option value="<?php echo $this->url->link('product/manufacturer/product', 'manufacturer_id=' . $manufacturer_id); ?>">All</option>
                <?php foreach ($series as $s) { ?>
                 <?php if ($s['sku'] == $filter_series) { ?>
                <option value="<?php echo $this->url->link('product/manufacturer/product', 'manufacturer_id=' . $manufacturer_id.'&filter_series='.$s['sku']); ?>" selected="selected"><?php echo $s['sku']; ?></option>
                <?php } else { ?>
                <option value="<?php echo $this->url->link('product/manufacturer/product', 'manufacturer_id=' . $manufacturer_id.'&filter_series='.$s['sku']); ?>"><?php echo $s['sku']; ?></option>
                <?php } ?>
                <?php } ?>
              </select>
        <?php } ?>
              <select onchange="location = this.value;">
              <?php foreach ($series as $sku=>$serie) { ?>
                <option value="<?php echo $serie['url'];?>"<?php if($serie['selected']) { ?> selected="selected"<?php } ?>><?php echo $sku; ?></option>
              <?php } ?>
              </select>

    </div>

	<?php 
	$data = array(); 
	
	
	 foreach ($products as $prod) {
	 
	            $bore = '';
                $idiameter = '';
                $odiameter = '';

                foreach ($prod['attribute_groups'] as $attribute_group) {
                    foreach ($attribute_group['attribute'] as $attribute) {
                        
                        
                        if ((isset($this->request->cookie['size']) && ($this->request->cookie['size'] == 'inch')) && $attribute['attribute_id'] == '29') {
                            $bore = $attribute['text'];
                        } elseif ((!isset($this->request->cookie['size']) || ($this->request->cookie['size'] != 'inch')) && $attribute['attribute_id'] == '26') {
                            $bore = $attribute['text'];
                        }
            
                        if ((isset($this->request->cookie['size']) && ($this->request->cookie['size'] == 'inch')) && $attribute['attribute_id'] == '24') {
                            $idiameter = $attribute['text'];
                        } elseif ((!isset($this->request->cookie['size']) || ($this->request->cookie['size'] != 'inch')) && $attribute['attribute_id'] == '23') {
                            $idiameter = $attribute['text'];
                        }
			
			            if ((isset($this->request->cookie['size']) && ($this->request->cookie['size'] == 'inch')) && $attribute['attribute_id'] == '16') {
                            $odiameter = $attribute['text'];
                        } elseif ((!isset($this->request->cookie['size']) || ($this->request->cookie['size'] != 'inch')) && $attribute['attribute_id'] == '17') {
                            $odiameter = $attribute['text'];
                        }
			             
                    }
                }
	 
	 
	 //echo '<pre>';
	 //var_dump($prod);
	    $data = array_merge($data, array(array('model' => $prod['model'], 'bore' => $bore, 'idiam' => $idiameter, 'odiam' => $odiameter, 'depth' => $prod['jan'], 'desc' => $prod['description'])));
	 }
	?>
	<div style="float: left; padding: 2px 10px;">
	<form action="pdf.php" method="POST">
	<input type="hidden" name="name" value="<?php echo base64_encode($heading_title);?>">
	<input type="hidden" name="series" value="<?php echo $filter_series;?>">
	<input type="hidden" name="data" value="<?php echo base64_encode(json_encode($data));?>">
    <input type="image" src="http://www.adobe.com/images/pdficon_small.png">
	</form>
	
	<form action="pdf.php" method="POST" target="_blank">
	<input type="hidden" name="name" value="<?php echo base64_encode($heading_title);?>">
	<input type="hidden" name="series" value="<?php echo $filter_series;?>">
	<input type="hidden" name="data" value="<?php echo base64_encode(json_encode($data));?>">
	<input type="hidden" name="print" value="1">
    <input type="image" src="/image/printer.png">
	</form>
	
	<?php 
	
	$data = json_decode(base64_decode(base64_encode(json_encode($data))));
	
	$email = '
<a href="http://mouthpiececomparator.com/"><img src="http://mouthpiececomparator.com/moutpiece_logo.jpg" title="Mouthpiece Comparator" alt="Mouthpiece Comparator"></a>	
<table border="1" cellpadding="5">

 <tr>
  <td align="center" width="15%" rowspan="2"><b>Model<br>Suffix</b></td>
  <td align="center" width="10%" rowspan="2"><b>Bore</b></td>
  <td align="center" width="20%" colspan="2"><b>Cup Diameter</b></td>
  <td align="center" width="15%" rowspan="2"><b>Cup<br>Depth</b></td>
  <td width="40%" rowspan="2"><b>Description</b></td>
 </tr>
 <tr>
  <td align="center">Inside</td>
  <td align="center">Outside</td>
 </tr>
';


foreach($data as $row) {

if ($row->desc == '') {
$row->desc = 'N/A';
}

$email .= "<tr>
  <td align=\"center\">$row->model</td>
  <td align=\"center\">$row->bore</td>
  <td align=\"center\">$row->idiam</td>
  <td align=\"center\">$row->odiam</td>
  <td align=\"center\">$row->depth</td>
  <td>$row->desc</td>
 </tr>";
}

$email .= '</table>';
	
	?>


<style type="text/css">
#inline { display: none; width: 500px; }

label { margin-right: 12px; margin-bottom: 9px; font-family: Georgia, serif; color: #646464; font-size: 1.2em; }

.txt { 
display: inline-block; 
color: #676767;
width: 420px; 
font-family: Arial, Tahoma, sans-serif; 
margin-bottom: 10px; 
border: 1px dotted #ccc; 
padding: 5px 9px;
font-size: 1.2em;
line-height: 1.4em;
}

.txtarea { 
display: block; 
resize: none;
color: #676767;
font-family: Arial, Tahoma, sans-serif; 
margin-bottom: 10px; 
width: 500px; 
height: 150px;
border: 1px dotted #ccc;
padding: 5px 9px; 
font-size: 1.2em;
line-height: 1.4em;
}

.txt:focus, .txtarea:focus { border-style: solid; border-color: #bababa; color: #444; }

input.error, textarea.error { border-color: #973d3d; border-style: solid; background: #f0bebe; color: #a35959; }
input.error:focus, textarea.error:focus { border-color: #973d3d; color: #a35959; }

#send { 
color: #dee5f0;
display: block;
cursor: pointer;
padding: 5px 11px;
font-size: 1.2em;
border: solid 1px #224983;
border-radius: 5px;
background: #1e4c99; 
background: -webkit-gradient(linear, left top, left bottom, from(#2f52b7), to(#0e3a7d)); 
background: -moz-linear-gradient(top, #2f52b7, #0e3a7d); 
background: -webkit-linear-gradient(top, #2f52b7, #0e3a7d);
background: -o-linear-gradient(top, #2f52b7, #0e3a7d);
background: -ms-linear-gradient(top, #2f52b7, #0e3a7d);
background: linear-gradient(top, #2f52b7, #0e3a7d);
filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#2f52b7', endColorstr='#0e3a7d'); 
}
#send:hover {
background: #183d80; 
background: -webkit-gradient(linear, left top, left bottom, from(#284f9d), to(#0c2b6b)); 
background: -moz-linear-gradient(top,  #284f9d, #0c2b6b); 
background: -webkit-linear-gradient(top, #284f9d, #0c2b6b);
background: -o-linear-gradient(top, #284f9d, #0c2b6b);
background: -ms-linear-gradient(top, #284f9d, #0c2b6b);
background: linear-gradient(top, #284f9d, #0c2b6b);
filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#284f9d', endColorstr='#0c2b6b');
}
#send:active {
color: #8c9dc0; 
background: -webkit-gradient(linear, left top, left bottom, from(#0e387d), to(#2f55b7)); 
background: -moz-linear-gradient(top,  #0e387d,  #2f55b7);
background: -webkit-linear-gradient(top, #0e387d, #2f55b7);
background: -o-linear-gradient(top, #0e387d, #2f55b7);
background: -ms-linear-gradient(top, #0e387d, #2f55b7);
background: linear-gradient(top, #0e387d, #2f55b7);
filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#0e387d', endColorstr='#2f55b7');
}
</style>
<link rel="stylesheet" type="text/css" media="all" href="fancybox/jquery.fancybox.css">	
<script type="text/javascript" src="fancybox/jquery.fancybox.js?v=2.0.6"></script>
<a class="modalbox" href="#inline"><img src="/image/email.png"></a>

<div id="inline">
	<h2>Email To Friend</h2>

	<form id="contact" name="contact" action="#" method="post">
		<label for="email">From:</label><br>
		<input type="email" id="email1" name="from" class="txt">
		<br>
		<label for="email">To:</label><br>
		<input type="email" id="email2" name="to" class="txt">
		<br>
		<input type="hidden" name="subject" value="<?php echo $heading_title; ?>">
		<input type="hidden" id="msg" name="msg" value='<?php echo $email; ?>'>
		<button id="send">Send E-mail</button>
	</form>
</div>

<script type="text/javascript">
	function validateEmail(email) { 
		var reg = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
		return reg.test(email);
	}

	$(document).ready(function() {
		$(".modalbox").fancybox();
		$("#contact").submit(function() { return false; });

		
		$("#send").on("click", function(){
			var emailval1  = $("#email1").val();
			var emailval2  = $("#email2").val();
			var msgval    = $("#msg").val();
			var msglen    = msgval.length;
			var mailvalid1 = validateEmail(emailval1);
			var mailvalid2 = validateEmail(emailval2);
			
			if(mailvalid1 == false) {
				$("#email1").addClass("error");
			}
			else if(mailvalid1 == true){
				$("#email1").removeClass("error");
			}
			
			if(mailvalid2 == false) {
				$("#email2").addClass("error");
			}
			else if(mailvalid2 == true){
				$("#email2").removeClass("error");
			}
			
			if(msglen < 4) {
				$("#msg").addClass("error");
			}
			else if(msglen >= 4){
				$("#msg").removeClass("error");
			}
			
			if(mailvalid1 == true && mailvalid2 == true && msglen >= 4) {
			
				$("#send").replaceWith("<em>sending...</em>");
				
				$.ajax({
					type: 'POST',
					url: 'email.php',
					data: $("#contact").serialize(),
					success: function(data) {
						if(data == "true") {
							$("#contact").fadeOut("fast", function(){
								$(this).before("<p><strong>Success!</strong></p>");
								setTimeout("$.fancybox.close()", 1000);
							});
						} else {
						$("#contact").fadeOut("fast", function(){
								$(this).before("<p><strong>Failed!</strong></p>");
								setTimeout("$.fancybox.close()", 1000);
							});
						}
					}
				});
			}
		});
	});
</script>


         </div>

<div style="overflow:visible;">    
<div class="fb-send" data-href="<?php echo $breadcrumb['href']; ?>"></div>

</div>     

           
	


    <div class="limit" style="width:200px;">
    Size in: <select><option value=="mm" selected><b>mm</b></option>
                    <option value="inch">inch</option>
            </select>
    
                    <!-- <?php var_dump($limits); ?> -->
    <?php echo $text_limit; ?> <select onchange="location = this.value;">
                <?php foreach ($limits as $limit_item) { ?>
                <?php if ($limit_item['value'] == $limit) { ?>
                <option value="<?php echo $limit_item['href']; ?>" selected="selected"><?php echo $limit_item['text']; ?></option>
                <?php } else { ?>
                <option value="<?php echo $limit_item['href']; ?>"><?php echo $limit_item['text']; ?></option>
                <?php } ?>
                <?php } ?>
            </select>
  
    </div>
  </div>
        
      
  
        
        
        
<table class="" style="width:100%;">
       
            <td class="theader">Compare</td>
            <td class="theader";><div class="name">Series</div></td>
                           


                <?php foreach ($sorts as $sorts) { ?>
                <?php if ($sorts['value'] == $sort . '-' . $order) { ?>

             <td class="theader">
                                <!--<a href="<?php echo $sorts['href'] . '&order=DESC'; ?>"><?php echo $sorts['text'] . '&#9660;'; ?></a> -->
                                <?php echo $sorts['text'] . ''; ?>
            </td>

                <?php } else { ?>
            <td class="theader">
                                <!--<a href="<?php echo $sorts['href'] . '&order=ASC'; ?>"><?php echo $sorts['text'] . '&#9650;'; ?></a> -->
                                <?php echo $sorts['text'] . ''; ?>
            </td>
                <?php } ?>
                <?php } ?>

            <td class="theader">Rating</td>
        </td>
    
    
        <!-- naglowek koniec */ -->

             <?php foreach ($products as $product) { 
             
             $bore = '';
                            $idiameter = '';
                            $odiameter = '';

                            foreach ($product['attribute_groups'] as $attribute_group) {
                                foreach ($attribute_group['attribute'] as $attribute) {
                                    
                                    
                                    if ((isset($this->request->cookie['size']) && ($this->request->cookie['size'] == 'inch')) && $attribute['attribute_id'] == '29') {
                                        $bore = $attribute['text'];
                                    } elseif ((!isset($this->request->cookie['size']) || ($this->request->cookie['size'] != 'inch')) && $attribute['attribute_id'] == '26') {
                                        $bore = $attribute['text'];
                                    }
                        
                                    if ((isset($this->request->cookie['size']) && ($this->request->cookie['size'] == 'inch')) && $attribute['attribute_id'] == '24') {
                                        $idiameter = $attribute['text'];
                                    } elseif ((!isset($this->request->cookie['size']) || ($this->request->cookie['size'] != 'inch')) && $attribute['attribute_id'] == '23') {
                                        $idiameter = $attribute['text'];
                                    }
                                    
                                                if ((isset($this->request->cookie['size']) && ($this->request->cookie['size'] == 'inch')) && $attribute['attribute_id'] == '16') {
                                        $odiameter = $attribute['text'];
                                    } elseif ((!isset($this->request->cookie['size']) || ($this->request->cookie['size'] != 'inch')) && $attribute['attribute_id'] == '17') {
                                        $odiameter = $attribute['text'];
                                    }
                                                 
                                }
                            }
             
             ?>
             
                                         <?php if(0) {?>
                                            <?php if(isset($this->session->data['compare']) && in_array($product['product_id'],$this->session->data['compare'])) {?> 
                <tr class="compared">
                      <td class="Cell" style="width:5px;" onmouseout="querySelector('input').src='catalog/view/theme/default/image/success.png'" onmouseover="querySelector('input').src='catalog/view/theme/default/image/cp_close.png'">
                        <input type="image" value="Send to comparator" style="padding: 10px;" src="catalog/view/theme/default/image/success.png" id="icon-<?php echo $product['product_id']; ?>" onclick="location='http://www.mouthpiececomparator.com/index.php?route=product/compare&remove=<?php echo $product['product_id']; ?>'" />
                      </td>
                                  <?php } else {?>                
                <tr>
                      <td class="Cell" style="width:5px;">
                                <input type="image" value="Send to comparator" style="padding: 10px;" src="../image/minus.png"  id="icon-<?php echo $product['product_id']; ?>"
                               onclick="SetComparedIcon(<?php echo $product['product_id']; ?>); addToCompareAjax('<?php echo $product['product_id'];?>');javascript:location.reload();" />
                                            <?php } ?>
                                            <?php } ?><?php /**********************/ ?>
                   
                      </td>
             
                <tr>
                        <td class="Cell" id="Cell-<?php echo $product['product_id']; ?>" onclick="CompareAction(this,<?php echo $product['product_id']; ?>)">
                            <div class="compare-icon<?php if(isset($this->session->data['compare']) && in_array($product['product_id'],$this->session->data['compare'])) {?> minus<?php } ?>"></div><?php /**********************/ ?>            
                        </td>
            
                    <td class="Cell" style="width:auto" background:#ffcc00;>
                        <div class="name"><?php echo $product['sku']; ?></div>
                    </td>
                    <td class="Cell" style="width:auto;">
                        <div class="name"><a href="<?php echo $product['href']; ?>"><?php echo $product['model']; ?></a></div>
                    </td>
                     <td class="Cell" style="width:auto;">
                        <div class="name"><?php echo $bore; ?></div>
                    </td>
                    <td class="Cell" style="width:auto;">
                            <div class="name"><?php echo $idiameter; ?></div>
                    </td>
                    <td class="Cell" style="width:auto;">
                        <div class="name"><?php echo $product['jan']; ?></a></div>
                    </td>
                    <td class="Cell" style="width:auto;">
                           <div class="rating"><img src="catalog/view/theme/default/image/stars-<?php echo $product['rating']; ?>.png" alt="<?php echo $product['reviews']; ?>"/></div>
                    </td>
                </td>
        <?php } ?>


   


</table>
    
                         
                   
                
<div style="padding-top:15px">
       
        <div class="fb-like" style="width:800px; float:left;"data-href="<?php echo $breadcrumb['href']; ?>" data-width="900" data-layout="standard" data-action="like" data-show-faces="true" data-share="true"></div>
         <div style="width:120px; float:right;"><input type="submit" value="Compare now!" onclick="window.location='/index.php?route=product/compare';"> <?php echo $text_comparet; ?><a href="<?php echo $compare; ?>" id="compare-total"><?php echo $text_compare; ?></a></div>
</div>

                  
  

  <div class="pagination"><?php echo $pagination; ?></div>
  <?php } else { ?>
  <div class="content"><?php echo $text_empty; ?></div>
  <div class="buttons">
    <div class="right"><a href="<?php echo $continue; ?>" class="button"><?php echo $button_continue; ?></a></div>
  </div>
  
  <?php }?>
  

  
  <?php echo $content_bottom; ?></div>
<script type="text/javascript">
/*function SetComparedIcon(id) {
  $('#icon-'+id).attr("src","catalog/view/theme/default/image/success.png");
} */
/*function RemoveComparedIcon(id) {
  $('#icon-'+id).attr("src","../image/minus.png");
}*/
</script>
<?php echo $footer; ?>