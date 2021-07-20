<!DOCTYPE html>
<html>
    <head>
        <title><?php echo $title; ?></title>
        <meta charset="UTF-8" />
        <base href="<?php echo $base; ?>" />
        <?php if ($description) { ?>
        <meta name="description" content="<?php echo $description; ?>" />
        <?php } ?>
        <?php if ($keywords) { ?>
        <meta name="keywords" content="<?php echo $keywords; ?>" />
        <?php } ?>
        <?php if ($icon) { ?>
        <link href="<?php echo $icon; ?>" rel="icon" />
        <?php } ?>
        <?php foreach ($links as $link) { ?>
        <link href="<?php echo $link['href']; ?>" rel="<?php echo $link['rel']; ?>" />
        <?php } ?>
        <link rel="stylesheet" type="text/css" href="catalog/view/theme/default/stylesheet/stylesheet.css" />
        <?php foreach ($styles as $style) { ?>
        <link rel="<?php echo $style['rel']; ?>" type="text/css" href="<?php echo $style['href']; ?>" media="<?php echo $style['media']; ?>" />
        <?php } ?>
        <script type="text/javascript" src="catalog/view/javascript/jquery/jquery-1.7.1.min.js"></script>
        <script type="text/javascript" src="catalog/view/javascript/jquery/ui/jquery-ui-1.8.16.custom.min.js"></script>
        <link rel="stylesheet" type="text/css" href="catalog/view/javascript/jquery/ui/themes/ui-lightness/jquery-ui-1.8.16.custom.css" />
        <script type="text/javascript" src="catalog/view/javascript/common.js"></script>
		          <link rel="stylesheet" type="text/css" href="catalog/view/fontawesome/css/font-awesome.min.css" media="screen" />
        <?php foreach ($scripts as $script) { ?>
        <script type="text/javascript" src="<?php echo $script; ?>"></script>
        <?php } ?>
        <!--[if IE 7]> 
        <link rel="stylesheet" type="text/css" href="catalog/view/theme/default/stylesheet/ie7.css" />
        <![endif]-->
        <!--[if lt IE 7]>
        <link rel="stylesheet" type="text/css" href="catalog/view/theme/default/stylesheet/ie6.css" />
        <script type="text/javascript" src="catalog/view/javascript/DD_belatedPNG_0.0.8a-min.js"></script>
      

        <script type="text/javascript">
        DD_belatedPNG.fix('#logo img');
        </script>
        <![endif]-->
        <?php if ($stores) { ?>
        <script type="text/javascript"><!--
        $(document).ready(function() {
        <?php foreach ($stores as $store) { ?>
                $('body').prepend('<iframe src="<?php echo $store; ?>" style=""></iframe>');
        <?php } ?>
            });
            //--></script>
        <?php } ?>
        <?php echo $google_analytics; ?>
</head>
<body>
 


        <div id="container">
            <div id="header">
                <div id="search" style="border-bottom:1px solid #f9f9f9;">
        
                <div style="float: left; margin-right: 20px;">
        
                         <?php if ($logo) { ?>
                                <div><a href="<?php echo $home; ?>"><img src="<?php echo $logo; ?>" title="<?php echo $name; ?>" alt="<?php echo $name; ?>" /></a></div>
                                <?php } ?>
                         </div>








        

        		  <form method="post" action="index.php?route=product/search">
				
                     <div  style="float: left; margin-right: 10px;">

			 <input type="hidden" name="redirect" value="<?php echo $_SERVER[REQUEST_URI];  ?>" />  
					
                        <select name="cat" onchange="submit();" style="font-size: 15px;">
						<?php    $a=0;   foreach ($categories as $category) { if($a>0)
{						?>
<?php if($category['id']>0){?>
                                  <option value="<?php echo $category['id'];?>" <?php if($category['id']==$cookiecat) {echo 'selected';  $tcat=$category['id'];} ?>  ><?php echo $category['name']; ?></option>
								  <?php } } $a=$a+1;  } ?>
                       </select>
					   
			
                     </div>
            
            	
				 
                    <div class="search-wrapper" style="float: left;">
                                <?php
                                //  echo $this->request->get["category_id"]; 
                                // echo  $this->request->post("cat"); 
                                ?>
                        <!--<input type="hidden" name="category_id"  value="<?php echo $this->request->cookie["cat"]; ?>" /> -->
                        <input type="text" name="search" placeholder="<?php echo $text_search; ?>" value="<?php echo $search; ?>" autocomplete="off" />
                        <button class="button-search">Search</button>
                    </div>
					
					   <select name="size">
                    <option value=="mm" <?php	if(!isset($this->request->cookie['size']) || ($this->request->cookie['size'] == 'mm')) { echo 'selected'; } ?> ><b>mm</b></option>
                    <option value="inch" <?php	if($this->request->cookie['size'] == 'inch') { echo 'selected'; } ?> >inch</option>
                </select>
   </form>

             
                    <div style="float: right;  padding:5px 0px;">
                         <a href="/index.php?route=product/compare"  ><i class="fa fa-check-circle fa-3x" style="padding:0px 5px;"></i></a> 
                         <a href="/wishlist"  ><i class="fa fa-heart fa-3x" style="padding:0px 5px;"></i></a> 
                        <a href="/account"  ><i class="fa fa-user fa-3x" style="padding:0px 5px;"></i></a> 



                     </div>


                </div>
        
 
               

                <?php
                if (isset($this->request->post['size'])) {
                    $size = $this->request->post['size'];
                    if (!isset($this->request->cookie['size']) || ($this->request->cookie['size'] != $size)) {
                        setcookie('size', $size, time() + 60 * 60 * 24 * 30, '/', $this->request->server['HTTP_HOST']);
                    }
                    if (isset($this->request->post['redirect'])) {
                      //  $this->redirect($this->request->post['redirect']);
                    }
                }
                
                if (isset($this->request->post['cat'])) {
                    echo 'herllooo';
                    
                    $cat = $this->request->post['cat'];
                    if (!isset($this->request->cookie['cat']) || ($this->request->cookie['cat'] != $cat)) {
                        setcookie('cat', $cat, time() + 60 * 60 * 24 * 30, '/', $this->request->server['HTTP_HOST']);
                    }
                    if (isset($this->request->post['redirect'])) {
                      //  $this->redirect($this->request->post['redirect']);
                    }
                }elseif (isset($this->request->get['category_id'])) {
                    
                    $r_url = $_SERVER['REQUEST_URI'];
                    
                    
                    if(strpos($r_url, 'category_id') != FALSE){
                        $cat = $this->request->get['category_id'];
                        setcookie('cat', $cat, time() + 60 * 60 * 24 * 30, '/', $this->request->server['HTTP_HOST']);
                    }else{
                       $r_url_size = strlen($r_url);
                       if($r_url_size > 0){
                         $cat = $this->request->get['category_id'];
                        setcookie('cat', $cat, time() + 60 * 60 * 24 * 30, '/', $this->request->server['HTTP_HOST']);  
                       }else{
                           echo 'hello';
                           $this->request->get['category_id'] = 60;
                           $cat = 60;
                        setcookie('cat',$cat, time() + 60 * 60 * 24 * 30, '/', $this->request->server['HTTP_HOST']);
                       }
                    }
                    //$cat = $this->request->get['category_id'];
                    //if (!isset($this->request->cookie['cat']) || ($this->request->cookie['cat'] != $cat)) {
                        //setcookie('cat', $cat, time() + 60 * 60 * 24 * 30, '/', $this->request->server['HTTP_HOST']);
                    //}
                    /*if (isset($this->request->post['redirect'])) {
                        $this->redirect($this->request->post['redirect']);
                    }
                     * 
                     */
                }else{
                      
                         // $this->request->get['category_id'] = 60;
                     //      $cat = 60;
                     //   setcookie('cat',$cat, time() + 60 * 60 * 24 * 30, '/', $this->request->server['HTTP_HOST']);
                }
		
                ?>
                <?php $currentcat = array(); ?>
                <?php if ($categories) { ?>

                <div id="cat">
                         

                            </div>

            <?php } ?>
    
    

                <?php
                $currentpage = $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
                 /*if(isset($this->request->get['route'])){
                     echo 'hiii';
                     echo $this->request->get['route'];
                 }else{
                     echo 'hello';
                     
                 }
                echo $this->request->cookie['cat'].'asdkl';
                  * 
                  */
                if ($this->request->get['route'] || (!empty($this->request->get['route']) && (strstr($this->request->get['route'], 'product') || strstr($this->request->get['route'], 'information'))) || (empty($this->request->get['route']) && $currentpage != $_SERVER["SERVER_NAME"] . '/')|| ((isset($this->request->get['route']) && $this->request->get['route'] == '') && (isset($this->request->get['category_id']) || isset($this->request->post['cat']) || isset($this->request->cookie['cat'])))) {
                    
                    ?>
                <style>
                    
                </style>


 
<?php } ?>

            </div>

            <?php
            if ($categories) {

                if (isset($this->request->get['category_id'])) {
                    $category_id = $this->request->get['category_id'];
                } elseif (isset($this->request->cookie['cat'])) {
                    $category_id = $this->request->cookie['cat'];
                }
                ?>


            <div id="menu">


                <ul>
                    <li><a href="/"><?php echo 'Home'; ?></a></li>
                    <li><a href="<? foreach ($categories as $category) { if($category['id']==$cookiecat) echo $category['href']; }?>"><?php echo 'Mouthpiece Database'; ?></a>
                            <?php
                            foreach ($categories as $category) {
                                if ($category['name'] == $this->language->get('text_manufacturer')) {
                                    ?>
                    <li><a href="<?php echo $category['href']; ?>"><?php echo $category['name']; ?></a>
                                        <?php if ($category['children']) { ?>
                        <div>
                                                <?php for ($i = 0; $i < count($category['children']);) { ?>
                            <ul>
                                                    <?php $j = $i + ceil(count($category['children']) / $category['column']); ?>
                                                    <?php for (; $i < $j; $i++) { ?>
                                                        <?php if (isset($category['children'][$i])) { ?>
                                <li><a href="<?php echo $category['children'][$i]['href']; ?>"><?php echo $category['children'][$i]['name']; ?></a></li>
                                                    <?php } ?>
                                                <?php } ?>
                            </ul>
                                        <?php } ?>
                        </div>
                                <?php } ?>
                    </li>
                    
        <?php }
    }
    ?>
                </ul>
               
            </div>

            <?php } ?>


            <?php if ($error) { ?>

            <div class="warning"><?php echo $error ?><img src="catalog/view/theme/default/image/close.png" alt="" class="close" /></div>

<?php } 		
			 ?>
            <div id="notification"></div>
