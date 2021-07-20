

<div id="footer" style="line-height: 2;">
      
            <div class="column">
                        <h3><?php echo $text_information; ?></h3>
                        <ul>
                                  <li><a href="http://mouthpiececomparator.com/about-mouthpiece-comparator">About us</a></li>
                                
                                  <li><a href="http://mouthpiececomparator.com/terms-of-use">Terms of use</a></li>
                                    <li><a href="http://mouthpiececomparator.com/advertising">Advertising</a></li>
                                <li><a href="http://mouthpiececomparator.com/contribute">Contribute</a></li>
                                <li><a href="http://mouthpiececomparator.com/contact-us">Contact us</a></li>

                        </ul>
						       
              </div>
       
       
              
              <div class="column">
                <h3><?php echo $text_extra; ?></h3>
                <ul>
                  <li><a href="<?php echo $manufacturer; ?>"><?php echo $text_manufacturer; ?></a></li>
                        <li><a href="http://mouthpiececomparator.com/mouthpiece-guide">Manufacturers index</a></li>
      
             
                </ul>
              </div>
              
                <div class="column">
                                <h3>Follow us on</h3>
                              <a href="https://twitter.com/mouthpiececomp"><img src="/image/twitter-32.png" alt="twitter"></img></a>&nbsp;<a href="https://www.facebook.com/mouthpiececomparator"><img src="/image/facebook-32.png" alt="facebook"></img></a>
                </div>

                <div class="column">
                                <h3>Join the email list</h3>
                                <ul>
                                        <!-- Begin MailChimp Signup Form -->
                                            <div id="mc_embed_signup">
                                            <form action="//mouthpiececomparator.us11.list-manage.com/subscribe/post?u=d451dc618185d8bf263f45ddd&amp;id=fe1182cbdb" method="post" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" class="validate" target="_blank" novalidate>
                                                <div id="mc_embed_signup_scroll">
                                                    
                                            <div class="mc-field-group">
                                                    <input type="email" value="" name="EMAIL" class="required email" id="mce-EMAIL" style="color:#000; border:0px; font-weight:normal; height:24px;" >
                                                <input type="submit" value="Sign up" name="subscribe" id="mc-embedded-subscribe" class="button" style="background:#fff; color:#000; margin:0px; border-radius:0px; height: 26px;
                                                padding: 0px 7px;">
                                            </div>
                                                    <div id="mce-responses" class="clear">
                                                            <div class="response" id="mce-error-response" style="display:none"></div>
                                                            <div class="response" id="mce-success-response" style="display:none"></div>
                                                    </div>    <!-- real people should not fill this in and expect good things - do not remove this or risk form bot signups-->
                                                <div style="position: absolute; left: -5000px;"><input type="text" name="b_d451dc618185d8bf263f45ddd_fe1182cbdb" tabindex="-1" value=""></div>
                                                <div class="clear"></div>
                                                </div>
                                            </form>
                                            </div>

                                            <!--End mc_embed_signup-->
                                </ul>
        
              </div>
  
     
				<div style="float:right;">
				<form action="index.php" method="post" enctype="multipart/form-data">
                                <div id="size" style='position:inherit'><b><?php echo 'Size in:'; ?>
                        
                                    <?php 
									
									echo $this->request->cookie['size'];
									if (!isset($this->request->cookie['size']) || ($this->request->cookie['size'] == 'mm')) { ?>
                                                            <a title="<?php echo 'mm'; ?>"><b><?php echo 'mm'; ?></b></a>
                                                            <a title="<?php echo 'inch'; ?>" href="?size=inch"><?php echo 'inch'; ?></a>
                                                               <?php } else { ?>
                                                            <a title="<?php echo 'mm'; ?>" href="?size=mm"><?php echo 'mm'; ?></a>
                                                            <a title="<?php echo 'inch'; ?>"><b><?php echo 'inch'; ?></b></a>
                                    <?php } ?>
                                    <input type="hidden" name="size" value="" />
                                    <input type="hidden" name="redirect" value="?" />
                                </div>
                        
                </form> 
				</div>
     
</div>
 
 <div style="padding:10px; text-align: center; ">Copyright © 2015  Mouthpiece Comparator. All rights reserved.</div> 


</div>
</body></html>