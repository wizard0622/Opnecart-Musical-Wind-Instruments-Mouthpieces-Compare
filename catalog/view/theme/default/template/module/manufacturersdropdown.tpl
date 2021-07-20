<script type="text/javascript">
  function goto_path(path){
    if(path!=''){
      window.location = "<?php echo $href?>"+path;
    }
  }
</script>

<div class="box">
  <div class="box-heading"><?php echo $heading_title; ?></div>
  <div class="box-content">
    <div class="box-category">     
      Shop by Brand<br /><br />
      <ul><select name='manufacturers' onchange="goto_path(this.value)">
      <option value=""> -- Search by -- </option>
      <option value=""></option>
        <?php foreach ($manufacturers as $manufacturer) { ?>        
        <li>
          <option value="<?php echo $manufacturer['id']; ?>"><?php echo $manufacturer['name']; ?></option>
        </li>
        <?php } ?>
        </select>
      </ul>
    </div>
  </div>
</div>
