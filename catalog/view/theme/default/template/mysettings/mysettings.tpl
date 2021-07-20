<?php 
    echo $header;
    echo $column_left;
    echo $column_right; ?>
    <div id="content">
        <?php 
            echo $content_top;
            echo $content_bottom;
        ?>
    <div>

    </div>
  
    <form action="" method="post" enctype="multipart/form-data">
  <div id="size" style="position:relative; left:0px; width:100%;">
        <h1> Settings </h1></br><h3>Measurement method</h3>
    <p>Please select measurment method you want to use sitewide:</p>

    <?php if (!isset($this->request->cookie['size']) || ($this->request->cookie['size'] == 'mm')) { ?>
    <a title="<?php echo 'mm'; ?>"><b><?php echo 'Millimeters'; ?></b></a>
	<a title="<?php echo 'inch'; ?>" onclick="$('input[name=\'size\']').attr('value', '<?php echo 'inch'; ?>'); $(this).parent().parent().submit();"><?php echo 'Inches'; ?></a>
    <?php } else { ?>
    <a title="<?php echo 'mm'; ?>" onclick="$('input[name=\'size\']').attr('value', '<?php echo 'mm'; ?>'); $(this).parent().parent().submit();"><?php echo 'Millimeters'; ?></a>
    <a title="<?php echo 'inch'; ?>"><b><?php echo 'Inches'; ?></b></a>
    <?php } ?>
	<input type="hidden" name="size" value="" />
    <input type="hidden" name="redirect" value="#" />
  </div>
</form>




<!--- this should be saved for logged in users
  <div>
        <h3>Equivalent mouthpieces setting</h3>
        <h6>Diameter tolerance</h6>

        exact / medium / big
  </div>

--->




    </div>
<?php echo $footer; ?>