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
        <?php foreach ($scripts as $script) { ?>
        <script type="text/javascript" src="<?php echo $script; ?>"></script>
        <?php } ?>
        <!--[if IE 7]> 
        <link rel="stylesheet" type="text/css" href="catalog/view/theme/default/stylesheet/ie7.css" />
        <![endif]-->
        <!--[if lt IE 7]>
        <link rel="stylesheet" type="text/css" href="catalog/view/theme/default/stylesheet/ie6.css" />
        <script type="text/javascript" src="catalog/view/javascript/DD_belatedPNG_0.0.8a-min.js"></script>
        <script>
          (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
          (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
          m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
          })(window,document,'script','//www.google-analytics.com/analytics.js','ga');
        
          ga('create', 'UA-53075236-1', 'auto');
          ga('send', 'pageview');
        
        </script>
        <script type="text/javascript">
        DD_belatedPNG.fix('#logo img');
        </script>
        <![endif]-->
        <?php if ($stores) { ?>
        <script type="text/javascript"><!--
        $(document).ready(function() {
<?php foreach ($stores as $store) { ?>
                $('body').prepend('<iframe src="<?php echo $store; ?>" style="display: none;"></iframe>');
<?php } ?>
            });
            //--></script>
        <?php } ?>
        <?php echo $google_analytics; ?>
    </head>
    <body>
        <div id="container">

            <div id="header">
                <?php if ($logo) { ?>
                <div id="logo"><a href="<?php echo $home; ?>"><img src="<?php echo $logo; ?>" title="<?php echo $name; ?>" alt="<?php echo $name; ?>" /></a></div>
                <?php } ?>




                <style type="text/css">
                    #size {
                        width: 75px;
                        position: absolute;
                        top: 35px;
                        right: 0px;
                        color: #999;
                        line-height: 17px;
                    }
                    #size a {
                        display: inline-block;
                        padding: 2px 4px;
                        border: 1px solid #CCC;
                        color: #999;
                        text-decoration: none;
                        margin-right: 2px;
                        margin-bottom: 2px;
                    }
                    #size a b {
                        color: #000;
                        text-decoration: none;
                    }

                    #cat {
                        width: 60%;
                        position: absolute;
                        top: 55px;
                        left: 300px;
                        color: #999;
                        float: left;
                    }
                    #cat a {
                        display: inline-block;
                        padding: 2px 4px;
                        border: 1px solid #CCC;
                        color: #999;
                        text-decoration: none;
                        margin-right: 2px;
                        margin-bottom: 2px;
                    }
                    #cat a b {
                        color: #000;
                        text-decoration: none;
                    }
                </style>

                <?php
                if (isset($this->request->post['size'])) {
                    $size = $this->request->post['size'];
                    if (!isset($this->request->cookie['size']) || ($this->request->cookie['size'] != $size)) {
                        setcookie('size', $size, time() + 60 * 60 * 24 * 30, '/', $this->request->server['HTTP_HOST']);
                    }
                    if (isset($this->request->post['redirect'])) {
                        $this->redirect($this->request->post['redirect']);
                    }
                }
                
                if (isset($this->request->post['cat'])) {
                    echo 'herllooo';
                    
                    $cat = $this->request->post['cat'];
                    if (!isset($this->request->cookie['cat']) || ($this->request->cookie['cat'] != $cat)) {
                        setcookie('cat', $cat, time() + 60 * 60 * 24 * 30, '/', $this->request->server['HTTP_HOST']);
                    }
                    if (isset($this->request->post['redirect'])) {
                        $this->redirect($this->request->post['redirect']);
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

                        <?php
                        foreach ($categories as $category) {
                            $redirect = '';

                            if ($category['name'] != $this->language->get('text_manufacturer')) {
                                ?>

                                <?php
                                if (isset($this->request->get['category_id']) && isset($this->request->get['manufacturer_id'])) {
                                    $redirect = $this->url->link('product/manufacturer/product', 'category_id=' . $category['id'] . '&manufacturer_id=' . $this->request->get['manufacturer_id']);
                                } elseif (isset($this->request->get['path'])) {
                                    $redirect = $category['href'];
                                } elseif (isset($this->request->get['manufacturer_id'])) {

                                    $redirect = $this->url->link('product/manufacturer/product', 'manufacturer_id=' . $this->request->get['manufacturer_id']);
                                } elseif (isset($this->request->get['product_id']) && isset($this->request->get['manufacturer_id'])) {

                                    $redirect = $this->url->link('product/manufacturer/product', 'manufacturer_id=' . $this->request->get['manufacturer_id']);
                                } elseif (isset($this->request->get['product_id'])) {

                                    $redirect = $this->url->link('product/manufacturer');
                                } else if (isset($this->request->get['route']) && $this->request->get['route'] = 'product/search' && isset($this->request->get['search'])) {
                                    /* if(isset($this->request->post['cat'])){
                                      $redirect = $this->url->link('product/search','search='.$this->request->get['search'].'&category_id='.$this->request->post['cat']);
                                      }else if(isset($this->request->get['category_id'])){
                                      $redirect = $this->url->link('product/search','search='.$this->request->get['search'].'&category_id='.$this->request->get['category_id']);
                                      }else{ */

                                    $redirect = $this->url->link('product/search', 'search=' . $this->request->get['search']);
                                    // }
                                } else {
                                    $redirect = '?';
                                }



                                if (isset($this->request->get['category_id'])) {
                    
                                    setcookie('cat', $this->request->get['category_id'], time() + 60 * 60 * 24 * 30, '/', $this->request->server['HTTP_HOST']);
                                    if ($this->request->get['category_id'] == $category['id']) {

                                        $currentcat = array('href' => $category['href'], 'name' => $category['name']);
                                        ?>

                    <form action="" method="post" enctype="multipart/form-data">
                        <a title="<?php echo $category['name']; ?>"><b><?php echo $category['name']; ?></b></a>
                    </form>
                                    <?php } else {
                                       //echo $this->request->get['category_id'];exit;
                                        setcookie('cat', $this->request->get['category_id'], time() + 60 * 60 * 24 * 30, '/', $this->request->server['HTTP_HOST']);
                                       //echo $this->request->cookie['cat']; 
                                        $finalid= explode("_",$this->request->get['category_id']);
                                          //echo $this->request->cookie['cat'];exit;
                                         $finalcatid=$finalid[0];
                                        //echo $category['id'];exit;
                                        if ($finalcatid == $category['id']) {
                                            ?>
                    <form action="" method="post" enctype="multipart/form-data">
                        <a title="<?php echo $category['name']; ?>"><b><?php echo $category['name']; ?></b></a>
                    </form>
                                        <?php } else {
                                            ?>
                    <form action="" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="cat" value="" />

                        <input type="hidden" name="redirect" value="<?php
                                                if ($this->request->get['route'] = 'product/search') {
                                                    echo $redirect . '&category_id=' . $category['id'];
                                                } else {
                                                    echo $redirect;
                                                }
                                                ?>" />  
                        <a title="<?php echo $category['name']; ?>"
                           onclick="$('input[name=\'cat\']').attr('value', '<?php echo $category['id']; ?>');
                                   $(this).parent().submit();"><?php echo $category['name']; ?></a>
                    </form>
                                            <?php
                                        }
                                    }
                                } elseif (isset($this->request->cookie['cat'])) {
                                   
								// echo 'ddadd';
								   
                                    //setcookie('cat', $this->request->get['category_id'], time() + 60 * 60 * 24 * 30, '/', $this->request->server['HTTP_HOST']);
                                    if ($this->request->cookie['cat'] == $category['id']) {
                                        $currentcat = array('href' => $category['href'], 'name' => $category['name']);
                                        ?>

                    <form action="" method="post" enctype="multipart/form-data">
                        <a title="<?php echo $category['name']; ?>"><b><?php echo $category['name']; ?></b></a>
                    </form>
                                    <?php } else {  ?>

                    <form action="" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="cat" value="" />
                        <input type="hidden" name="redirect" value="<?php
                                            if ($this->request->get['route'] = 'product/search') {
                                                echo $redirect . '&category_id=' . $category['id'];
                                            } else {
                                                echo $redirect;
                                            }
                                            ?>" />  
                        <a title="<?php echo $category['name']; ?>" onclick="$(this).closest('form').find('input[name=\'cat\']').attr('value', '<?php echo $category['id']; ?>');
                                $(this).parent().submit();"><?php echo $category['name']; ?></a>
                    </form>
                                        <?php
                                    }
                                } else {echo 'addd';
                                    setcookie('cat', '60', time() + 60 * 60 * 24 * 30, '/', $this->request->server['HTTP_HOST']);
                                    $this->redirect('/');
                                }
                                ?>


                            <?php }
                        }
                        ?>

                </div>

<?php } ?>

                <form action="" method="post" enctype="multipart/form-data">
                    <div id="size"><b><?php echo 'Size in:'; ?></b><br />
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
                        <input type="hidden" name="redirect" value="?" />
                    </div>
                </form>

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
                    .search-wrapper {
                        width: 600px;
                    }
                    .search-wrapper input {
                        border: 0 none;
                        float: left;
                        height: 20px;
                        padding: 10px 5px;
                        width: 480px;
                    }
                    .search-wrapper input:focus {
                        outline: 0 none;
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

    <?php if ((isset($this->request->get['manufacturer_id']) && $this->request->get['manufacturer_id'] != '') || (isset($this->request->get['route']) && $this->request->get['route'] == 'product/manufacturer')) {  ?> 
                <div id="search">
                    <div class="search-wrapper">
                        <input type="hidden" name="category_id"  value="<?php echo $this->request->cookie["cat"]; ?>" />
                        <input type="text" name="search" placeholder="<?php echo $text_search; ?>" value="<?php echo $search; ?>" />

                        <button class="button-search">Search</button>
                    </div>
                </div>
          

    <?php } else {  ?>

                <div id="search">
                    <div class="search-wrapper">
                                <?php
                                //  echo $this->request->get["category_id"]; 
                                // echo  $this->request->post("cat"); 
                                ?>
                        <input type="hidden" name="category_id"  value="<?php echo $this->request->cookie["cat"]; ?>" />
                        <input type="text" name="search" placeholder="<?php echo $text_search; ?>" value="<?php echo $search; ?>" />
                        <button class="button-search">Search</button>
                    </div>
                </div>
                    <?php } ?>

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
                    <li><a href="<?php echo $currentcat['href']; ?>"><?php echo 'Mouthpiece database'; ?></a>
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
                    <li style="float:right; color:#fff;"><a href="/index.php?route=product/compare" class="button">My comparisons</a></li>
        <?php }
    }
    ?>
                </ul>
                <ul> 

                </ul>








            </div>

            <?php } ?>


            <?php if ($error) { ?>

            <div class="warning"><?php echo $error ?><img src="catalog/view/theme/default/image/close.png" alt="" class="close" /></div>

<?php } ?>
            <div id="notification"></div>
