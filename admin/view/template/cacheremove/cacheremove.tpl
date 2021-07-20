<?php echo $header; ?>
<div id="content">
    <div class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
        <?php } ?>
    </div>
    <div class="box">
        <div class="heading">
            <h1><img alt="" src="view/image/shipping.png"><?php echo "Cache remove"; ?></h1>
        </div>
        <div class="content">
            <div class='response-div'></div>
            <form id="form1" action="<?php echo $action; ?>"  method="post">
                <table class="form">
                    <tbody>
                        <tr>
                            <td>System Cache remove:</td>
                            <td><button class='delete_cache' data-isfile='0' data-file="<?php echo $mod_system; ?>">Cache remove</button></td>
                        </tr>                        
                        <tr>
                            <td>Vqmode Cache remove:</td>
                            <td><button class='delete_cache' data-isfile='0' data-file="<?php echo $mod_vqcache; ?>">Cache remove</button></td>
                        </tr>
                        <tr>
                            <td>Mods Cache remove:</td>
                            <td>
                                <button class='delete_cache' data-isfile='1' data-file="<?php echo $mod_cache; ?>">Cache remove</button>
                            </td>
                        </tr>              
                    </tbody></table>
            </form>
        </div>
    </div>
</div>
<?php echo $footer; ?>
<script>
    $('.delete_cache').on('click', function(e){
        e.preventDefault();
        var file =$(this).attr('data-file');
        var is_file =$(this).attr('data-isfile');
        $.ajax({
            url: 'index.php?route=cacheremove/cacheremove/delete&token=<?php echo $token; ?>',
            dataType: 'json',
            type:"POST",
            data:{'file':file,'is_file':is_file},
            success: function(json) {		
               $(".response-div").html(json.error);
               $(".response-div").addClass(json.type);
            }
        });
    });
</script>
<style>
.delete_menu_cache,.delete_cache {
     background: linear-gradient(to bottom, rgb(30, 87, 153) 0%, rgb(14, 82, 128) 0%, rgb(32, 124, 202) 100%, rgb(38, 131, 188) 100%) repeat scroll 0 0 rgba(0, 0, 0, 0);
    border: medium none;
    border-radius: 7px;
    color: rgb(255, 255, 255);
    padding: 2px 6px;
    font-size: 12px;
    cursor: pointer;
}
</style>