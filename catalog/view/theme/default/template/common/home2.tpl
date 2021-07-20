<?php echo $header; ?><?php echo $column_left; ?><?php echo $column_right; ?>
<div id="content"><?php echo $content_top; ?>
<h1 style="display: none;"><?php echo $heading_title; ?></h1>

  	<?php if(!isset($this->request->get['route']) || $this->request->get['route'] == 'common/home') { 
	?>
	
<style type="text/css">
#header {
	height: 90px;
	margin-bottom: 7px;
	padding-bottom: 4px;
	position: relative;
	z-index: 99;
 
}
#header #logo {
	position: absolute;
	top: 25px;
	left: 15px;
}   
#search {
	top: 100px;
	left: 0px;
	width: 570px;
    	padding:25px;
	margin-bottom:5px;
	z-index: 15;
}

#header #welcome {
	position: absolute;
	top: 47px;
	right: 0px;
	z-index: 5;
	width: 298px;
	text-align: right;
	color: #999999;
}
#header .links {
	position: absolute;
	right: 0px;
	bottom: 3px;
	font-size: 10px;
	padding-right: 10px;
}
#header .links a {
	float: left;
	display: block;
	padding: 0px 0px 0px 7px;
	color: #38B0E3;
	text-decoration: none;
	font-size: 12px;
}
#header .links a + a {
	margin-left: 8px;
	border-left: 1px solid #CCC;
}
#content {
background:#f9f9f9;
}
</style>
	
<style>
.search-wrapper {
    width: 500px;
}
.search-wrapper input {
    border: 0 none;
    float: left;
    height: 20px;
    padding: 9px 5px;
    width: 378px;
    border:1px solid #ddd;
}
.search-wrapper input:focus {
    outline: 0 none;
    border: 1px solid #F78D3F;
box-shadow: 0 0 5px #F78D3F;

}
.search-wrapper input:-moz-placeholder {
    color: #999999;
    font-style: italic;
    font-weight: normal;
}
.search-wrapper button {
    background: none repeat scroll 0 0 #F78D3F;
    border: 0 none;
    color: #FFFFFF;
    cursor: pointer;
    float: right;
    font: bold 15px/40px 'lucida sans','trebuchet MS','Tahoma';
    height: 40px;
    overflow: visible;
    padding: 0;
    position: relative;
    text-shadow: 0 -1px 0 rgba(0, 0, 0, 0.3);
    text-transform: uppercase;
    width: 110px;
}
.search-wrapper button:hover {
    background: none repeat scroll 0 0 #E54040;
}
.search-wrapper button:active, .search-wrapper button:focus {
    background: none repeat scroll 0 0 #E54040;
    outline: 0 none;
}
.search-wrapper button:before {
    border-color: rgba(0, 0, 0, 0) #F78D3F;
    border-style: solid solid solid none;
    border-width: 8px 8px 8px 0;
    content: "";
    left: -6px;
    position: absolute;
    top: 12px;
}
.search-wrapper button:hover:before {
    border-right-color: #E54040;
}
.search-wrapper button:focus:before, .search-wrapper button:active:before {
    border-right-color: #E54040;
}
.search-wrapper button::-moz-focus-inner {
    border: 0 none;
    padding: 0; 
}
</style>


<style>
.compare-wrapper {
    width: 765px;
}
.compare-wrapper input {
    border: 0 none;
    float: left;
    height: 20px;
    padding: 9px 5px;
    width: 300px;
    border:1px solid #ddd;
}
.compare-wrapper input:focus {
    outline: 0 none;
    border: 1px solid #F78D3F;
box-shadow: 0 0 5px #F78D3F;

}
.compare-wrapper input:-moz-placeholder {
    color: #999999;
    font-style: italic;
    font-weight: normal;
}
.compare-wrapper button {
    background: none repeat scroll 0 0 #F78D3F;
    border: 0 none;
    color: #FFFFFF;
    cursor: pointer;
    float: right;
    font: bold 15px/40px 'lucida sans','trebuchet MS','Tahoma';
    height: 40px;
    overflow: visible;
    position: relative;
    text-shadow: 0 -1px 0 rgba(0, 0, 0, 0.3);
    text-transform: uppercase;
    width: 110px;
}
.compare-wrapper button:hover {
    background: none repeat scroll 0 0 #E54040;
}
.compare-wrapper button:active, .search-wrapper button:focus {
    background: none repeat scroll 0 0 #E54040;
    outline: 0 none;
}

.compare-wrapper button::-moz-focus-inner {
    border: 0 none;
    padding: 0; 
}

.compare-box li:hover{
background:#f1f1f1;
border-bottom:1px solid #ccc;
color:#000
font-weight:bold;
}

.compare-box{
padding:0px;
}

.compare-box li {
padding:0px 7px;
margin:0px;
border-bottom:1px solid #ccc;
background:#f1f1f1;
}

.compare-box li:hover {
background:#ccc;
}

</style>

<div style="padding-top:140px; width:800px; margin:0px auto;">

<div class="home-wrapper">
<h1 style="text-align: center; margin-top:-100px;">Mouthpiece Comparator</h1>
<div>
	Welcome to the Mouthpiece Comparator. This is beta version.
</div>
</div>
</div>


<script type="text/javascript">
$('input[name=\'quick_compare_1\']').focus(function() { $(this).select(); });

$('input[name=\'quick_compare_1\']').autocomplete({
    delay: 500,
    source: function (request, response) {
        $.ajax({
            url: 'index.php?route=product/product/autocomplete&filter_name=' + encodeURIComponent(request.term),
            dataType: 'json',
            success: function (json) {
                response($.map(json, function (item) {
                    return {
                        label: item.name,
                        value: item.product_id

                    }
                }));
            }
        });
		
    },
    select: function (event, ui) {
		$('input[name=\'quick_compare_1\']').attr('value', ui.item['label']);
		$.ajax({
		url: 'index.php?route=product/compare/add',
		type: 'post',
		data: 'product_id=' + ui.item['value'],
		dataType: 'json',
	    });
        return false;
    }
});

$('input[name=\'quick_compare_2\']').autocomplete({
    delay: 500,
    source: function (request, response) {
        $.ajax({
            url: 'index.php?route=product/product/autocomplete&filter_name=' + encodeURIComponent(request.term),
            dataType: 'json',
            success: function (json) {
                response($.map(json, function (item) {
                    return {
                        label: item.name,
                        value: item.product_id

                    }
                }));
            }
        });
		
    },
    select: function (event, ui) {
		$('input[name=\'quick_compare_2\']').attr('value', ui.item['label']);
		$.ajax({
		url: 'index.php?route=product/compare/add',
		type: 'post',
		data: 'product_id=' + ui.item['value'],
		dataType: 'json',
	    });
        return false;
    }
});
</script>
<?php } ?>



<?php echo $content_bottom; ?></div>
<?php echo $footer; ?>