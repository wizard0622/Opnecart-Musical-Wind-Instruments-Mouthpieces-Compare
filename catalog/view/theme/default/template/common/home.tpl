<?php echo $header; ?><?php echo $column_left; ?><?php echo $column_right; ?>
<div id="content"><?php echo $content_top; ?>
<h1 style="display: none;"><?php echo $heading_title; ?></h1>

					<?php
					
		
					
if($this->request->get['category_id']>0)
{

	setcookie('cat', $this->request->get['category_id'], time() + 60 * 60 * 24 * 30, '/', $this->request->server['HTTP_HOST']);
	$cat_id=$this->request->get['category_id'];
	
			$d=explode("_",$cat_id);
			$cat_id=$d['0'];

}
elseif($this->request->cookie['cat'])
{
$cat_id=$this->request->cookie['cat'];
}
else
{
 setcookie('cat', '60', time() + 60 * 60 * 24 * 30, '/', $this->request->server['HTTP_HOST']);
 $this->redirect('/');
}
$tcat=0;

?>


<style type="text/css">
        .homeleft {float: left; width: 415px; height:300px; padding:25px; border:1px solid #fafafa; color:#000;} 
        .homeright {float: right; width: 415px; height:300px; padding:25px; border:1px solid #fafafa; color:#000;} 
    .homeleft:hover {background:#f1f1f1; border:1px solid #ccc;} 
 .homeright:hover {background:#f1f1f1; border:1px solid #ccc; } 
</style>


<div style="margin: 0;width: 980px; padding:25px; text-align:left;">

            <a href="<?php foreach ($categories as $category) { if($category['id']==$cat_id) echo $category['href']; }?>">
        <div class="homeleft">
              <h2><?php echo 'Mouthpiece Comparison Chart'; ?></h2>
                 Ultimate mouthpiece chart for various brass instruments.
                 <br>Compare easily almost any mouthpiece avilable on the market.<br>
                 <img src="/image/mouthpiececomparator.png" style="padding-top:20px;" alt="Mouthpiece Comparator" width="300px"/> 
            </div>
        </a>

        <a href="/manufacturer">
            <div class="homeright">
                 <h2>Manufacturer Charts</h2>
                 Classic charts by diferent maouthpiece manufacturers.
                  <img src="/image/brandcharts.png" style="padding-top:20px;" alt="Mouthpiece Comparison Chart"  width="300px"/>
            </div>
        </a>
</div>


       
  
 




<?php echo $content_bottom; ?></div>





<?php echo $footer; ?>