$(document).ready(function(){
/*    $('a[onclick*="addToCompare"]').each(function(){
	var ajaxFunc = $(this).attr('onclick').replace('addToCompare', 'addToCompareAjax');
	$(this).removeAttr('onclick');
	$(this).attr({'onclick': ajaxFunc});
    });
*/    
    if($('#product_compare_wrapper').length){
    
	$('#product_compare_wrapper').load('index.php?route=product/ajaxcompare/compare_list', function(){
          $('#pList').slick({
/*          dots: false,
          arrows: true,
          draggable: false,
          infinite: true,
          speed: 300,
          slidesToShow: 4,
          touchMove: false,
          slidesToScroll: 1*/
            accessibility: true,
            dots: false,
            arrows: true,
            asNavFor: null,
            nextArrow: '<a class="slick-next" style="display: block;">',
            prevArrow: '<a class="slick-prev" style="display: block;">',
            draggable: false,
            infinite: true,
            speed: 300,
            slidesToShow: 4,
            touchMove: false,
            slidesToScroll: 1,
          
          });
  });
/*	var fixadent = $("#product_compare_wrapper"), pos = fixadent.offset();
	if (pos){
	    $(window).scroll(function() { 
		if($(this).scrollTop() > (pos.top)) 
		{ 
		    fixadent.addClass('fixed'); 
		}else if($(this).scrollTop() <= pos.top && fixadent.hasClass('fixed'))
		{ 
		    fixadent.removeClass('fixed'); 
		}
	    });
	}
*/	
    }
});

function addToCompareAjax(product_id){
    $.ajax({
	url: 'index.php?route=product/ajaxcompare/add',
	type: 'post',
	data: 'product_id=' + product_id,
	dataType: 'json',
	beforeSend: function(){
	    $('#pList .cProductInfo .noImage:first').addClass('cLoader');
	},
	success: function(json) {
	    $('.success, .warning, .attention, .information').remove();

	    if (json['compare']) {

		$('#compare-total').html(json['total']);
		
		$('#product_compare_wrapper').load('index.php?route=product/ajaxcompare/compare_list');
	    }	
	}
    });
}

function cProductRemove(obj, pId){
    
    $(obj).parent().stop().animate({marginLeft : '-200px', opacity: 0}, {queue: false, duration: 500, complete: function(){
	addCBlock();
	$(obj).parent().remove();
	$.ajax({
	    url: 'index.php?route=product/ajaxcompare&remove=' + pId,
	    success: function(){
		if($('#pList .cProductInfo .pImage img').length < 2){
		    $('#pCompareButton').addClass('disable');
		    $('#pCompareButton').attr({'href' : 'javascript:;'});
		}
	    }
	});
    }});

}

function cProductRemoveNew(pId){
    
    $('#compare-wrapper-cPI-'+pId).stop().animate({marginLeft : '-200px', opacity: 0}, {queue: false, duration: 500, complete: function(){
//	addCBlock();
	$('#compare-wrapper-cPI-'+pId).remove();
	$.ajax({
	    url: 'index.php?route=product/ajaxcompare&remove=' + pId,
	    success: function(){
		    $('#Cell-'+pId+' div.compare-icon').removeClass('minus');
		    $('#product_compare_wrapper').load('index.php?route=product/ajaxcompare/compare_list', function(){
          $('#pList').slick({
            accessibility: true,
            dots: false,
            arrows: true,
            asNavFor: null,
            nextArrow: '<a class="slick-next" style="display: block;">',
            prevArrow: '<a class="slick-prev" style="display: block;">',
            draggable: false,
            infinite: true,
            speed: 300,
            slidesToShow: 4,
            touchMove: false,
            slidesToScroll: 1,
          });
          $('a#addp2c').html('<i class="fa fa-check-circle-o fa-5x" >').removeClass("minus");
          
        });		
	    }
	});
    }});

}

function removeCProducts(){
    
    $('#product_compare_list').slideUp('fast', function(){
	$('#notification').html('');
	$('#cProductBoxRemove').remove();
	$('div.compare-icon').removeClass('minus');
	if($('#pList .cProductInfo .pImage img').length > 0){
	    $('#product_compare_wrapper').load('index.php?route=product/ajaxcompare/removeall');
	}
    });
    
}

function CompareAction(obj,product_id) {
  if($('#Cell-'+product_id + ' div.compare-icon').hasClass("minus")) {
    cProductRemoveNew(product_id);
  } else {
    Add2CompareAjax(product_id);
  }
  if($('a#addp2c').hasClass("minus")) {
    cProductRemoveNew(product_id);
  } else {
    Add2CompareAjax(product_id);
    $('a#addp2c').html('<i class="fa fa-check-circle fa-5x">').addClass("minus");
  }
};

function Add2CompareAjax(product_id)  {
   $.ajax({
	url: 'index.php?route=product/ajaxcompare/add',
	type: 'post',
	data: 'product_id=' + product_id,
	dataType: 'json',
	beforeSend: function(){
	    $('#pList .cProductInfo .noImage:first').addClass('cLoader');
	},
	success: function(json) {
	    $('.success, .warning, .attention, .information').remove();
      $('#Cell-'+product_id + ' div').addClass("minus");
	    if (json['compare']) {

		    $('#compare-total').html(json['total']);
		
		    $('#product_compare_wrapper').load('index.php?route=product/ajaxcompare/compare_list', function(){
          $('#pList').slick({
            accessibility: true,
            dots: false,
            arrows: true,
            asNavFor: null,
            nextArrow: '<a class="slick-next" style="display: block;">',
            prevArrow: '<a class="slick-prev" style="display: block;">',
            draggable: false,
            infinite: true,
            speed: 300,
            slidesToShow: 4,
            touchMove: false,
            slidesToScroll: 1,
          });
        });
   	   }	
	}
    });
}


