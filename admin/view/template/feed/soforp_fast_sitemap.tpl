<?php echo $header; ?>

<div id="content">
    <div class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
        <?php } ?>
    </div>
    <?php if ($error_warning) { ?>
    <div class="warning"><?php echo $error_warning; ?></div>
    <?php } ?>
    <?php if (isset($success) && $success) { ?>
    <div class="success"><?php echo $success; ?></div>
    <?php } ?>
    <div class="box">
        <div class="heading">
            <h1><img src="view/image/module.png" alt="" /> <?php echo $heading_title; ?></h1>
            <div class="buttons"><a onclick="$('#form').submit();" class="button"><span><?php echo $button_save; ?></span></a><a onclick="location = '<?php echo $cancel; ?>';" class="button"><span><?php echo $button_cancel; ?></span></a></div>
        </div>

        <div class="content">
            <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
                <table class="form">
                    <tr>
                        <td><?php echo $entry_status; ?></td>
                        <td><select name="soforp_fast_sitemap_status">
                                <?php if (isset($soforp_fast_sitemap_status) && $soforp_fast_sitemap_status) { ?>
                                <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                                <option value="0"><?php echo $text_disabled; ?></option>
                                <?php } else { ?>
                                <option value="1"><?php echo $text_enabled; ?></option>
                                <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                                <?php } ?>
                            </select></td>
                    </tr>
                    <tr>
                        <td><?php echo $entry_native_url_status; ?></td>
                        <td><select name="soforp_fast_sitemap_native_url_status">
                                <option value="0" <?php if ( $soforp_fast_sitemap_native_url_status == "0") echo "selected=\"selected\""; ?> ><?php echo $text_disabled; ?></option>
                                <option value="1" <?php if ( $soforp_fast_sitemap_native_url_status == "1") echo "selected=\"selected\""; ?> ><?php echo $text_enabled; ?></option>
                            </select></td>
                    </tr>
                    <tr>
                        <td><?php echo $entry_status_seo; ?></td>
                        <td><select name="soforp_fast_sitemap_seo_status">
                                <option value="0" <?php if ( $soforp_fast_sitemap_seo_status == "0") echo "selected=\"selected\""; ?> ><?php echo $text_seo_0; ?></option>
                                <option value="1" <?php if ( $soforp_fast_sitemap_seo_status == "1") echo "selected=\"selected\""; ?> ><?php echo $text_seo_1; ?></option>
                                <option value="2" <?php if ( $soforp_fast_sitemap_seo_status == "2") echo "selected=\"selected\""; ?> ><?php echo $text_seo_2; ?></option>
                            </select></td>
                    </tr>
                    <tr>
                        <td><?php echo $entry_filterpro_status_seo; ?></td>
                        <td><select name="soforp_fast_sitemap_filterpro_seo_status">
                                <option value="0" <?php if ( $soforp_fast_sitemap_filterpro_seo_status == "0") echo "selected=\"selected\""; ?> ><?php echo $text_disabled; ?></option>
                                <option value="1" <?php if ( $soforp_fast_sitemap_filterpro_seo_status == "1") echo "selected=\"selected\""; ?> ><?php echo $text_enabled; ?></option>
                            </select></td>
                    </tr>
                    <?php if( $hasAddresses ) { ?>
                    <tr>
                        <td><?php echo $entry_status_addresses; ?></td>
                        <td><select name="soforp_fast_sitemap_addresses_status">
                                <option value="0" <?php if ( $soforp_fast_sitemap_addresses_status == "0") echo "selected=\"selected\""; ?> ><?php echo $text_disabled; ?></option>
                                <option value="1" <?php if ( $soforp_fast_sitemap_addresses_status == "1") echo "selected=\"selected\""; ?> ><?php echo $text_enabled; ?></option>
                            </select></td>
                    </tr>
                    <?php } ?>
                    <tr>
                        <td><?php echo $entry_category_brand_status; ?></td>
                        <td><select name="soforp_fast_sitemap_category_brand_status">
                                <option value="0" <?php if ( $soforp_fast_sitemap_category_brand_status == "0") echo "selected=\"selected\""; ?> ><?php echo $text_disabled; ?></option>
                                <option value="1" <?php if ( $soforp_fast_sitemap_category_brand_status == "1") echo "selected=\"selected\""; ?> ><?php echo $text_enabled; ?></option>
                            </select></td>
                    </tr>
                    <tr>
                        <td><?php echo $entry_status_partition; ?></td>
                        <td><select name="soforp_fast_sitemap_partition_status">
                                <?php if (isset($soforp_fast_sitemap_partition_status) && $soforp_fast_sitemap_partition_status) { ?>
                                <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                                <option value="0"><?php echo $text_disabled; ?></option>
                                <?php } else { ?>
                                <option value="1"><?php echo $text_enabled; ?></option>
                                <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                                <?php } ?>
                            </select></td>
                    </tr>
                    <tr>
                        <td><?php echo $entry_status_multistore; ?></td>
                        <td><select name="soforp_fast_sitemap_multistore_status">
                                <?php if (isset($soforp_fast_sitemap_multistore_status) && $soforp_fast_sitemap_multistore_status) { ?>
                                <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                                <option value="0"><?php echo $text_disabled; ?></option>
                                <?php } else { ?>
                                <option value="1"><?php echo $text_enabled; ?></option>
                                <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                                <?php } ?>
                            </select></td>
                    </tr>
                    <tr>
                        <td><?php echo $entry_status_gzip; ?></td>
                        <td><select name="soforp_fast_sitemap_gzip_status">
                                <option value="0" <?php if ( $soforp_fast_sitemap_gzip_status == "0") echo "selected=\"selected\""; ?> ><?php echo $text_gzip_0; ?></option>
                                <option value="1" <?php if ( $soforp_fast_sitemap_gzip_status == "1") echo "selected=\"selected\""; ?> ><?php echo $text_gzip_1; ?></option>
                                <option value="2" <?php if ( $soforp_fast_sitemap_gzip_status == "2") echo "selected=\"selected\""; ?> ><?php echo $text_gzip_2; ?></option>
                                <option value="3" <?php if ( $soforp_fast_sitemap_gzip_status == "3") echo "selected=\"selected\""; ?> ><?php echo $text_gzip_3; ?></option>
                                <option value="4" <?php if ( $soforp_fast_sitemap_gzip_status == "4") echo "selected=\"selected\""; ?> ><?php echo $text_gzip_4; ?></option>
                                <option value="5" <?php if ( $soforp_fast_sitemap_gzip_status == "5") echo "selected=\"selected\""; ?> ><?php echo $text_gzip_5; ?></option>
                                <option value="6" <?php if ( $soforp_fast_sitemap_gzip_status == "6") echo "selected=\"selected\""; ?> ><?php echo $text_gzip_6; ?></option>
                                <option value="7" <?php if ( $soforp_fast_sitemap_gzip_status == "7") echo "selected=\"selected\""; ?> ><?php echo $text_gzip_7; ?></option>
                                <option value="8" <?php if ( $soforp_fast_sitemap_gzip_status == "8") echo "selected=\"selected\""; ?> ><?php echo $text_gzip_8; ?></option>
                                <option value="9" <?php if ( $soforp_fast_sitemap_gzip_status == "9") echo "selected=\"selected\""; ?> ><?php echo $text_gzip_9; ?></option>
                            </select></td>
                    </tr>
                    <tr>
                        <td><?php echo $entry_status_blog; ?></td>
                        <td><select name="soforp_fast_sitemap_blog_status">
                                <option value="0" <?php if ( $soforp_fast_sitemap_blog_status == "0") echo "selected=\"selected\""; ?> ><?php echo $text_blog_0; ?></option>
                                <option value="1" <?php if ( $soforp_fast_sitemap_blog_status == "1") echo "selected=\"selected\""; ?> ><?php echo $text_blog_1; ?></option>
                                <option value="2" <?php if ( $soforp_fast_sitemap_blog_status == "2") echo "selected=\"selected\""; ?> ><?php echo $text_blog_2; ?></option>
                            </select></td>
                    </tr>
                    <tr>
                        <td><?php echo $entry_use_url_date; ?></td>
                        <td><select name="soforp_fast_sitemap_use_url_date">
                                <option value="0" <?php if ( $soforp_fast_sitemap_use_url_date == "0") echo "selected=\"selected\""; ?> ><?php echo $text_disabled; ?></option>
                                <option value="1" <?php if ( $soforp_fast_sitemap_use_url_date == "1") echo "selected=\"selected\""; ?> ><?php echo $text_enabled ?></option>
                            </select></td>
                    </tr>
                    <tr>
                        <td><?php echo $entry_use_url_frequency; ?></td>
                        <td><select name="soforp_fast_sitemap_use_url_frequency">
                                <option value="0" <?php if ( $soforp_fast_sitemap_use_url_frequency == "0") echo "selected=\"selected\""; ?> ><?php echo $text_disabled; ?></option>
                                <option value="1" <?php if ( $soforp_fast_sitemap_use_url_frequency == "1") echo "selected=\"selected\""; ?> ><?php echo $text_enabled ?></option>
                            </select></td>
                    </tr>
                    <tr>
                        <td><?php echo $entry_use_url_priority; ?></td>
                        <td><select name="soforp_fast_sitemap_use_url_priority">
                                <option value="0" <?php if ( $soforp_fast_sitemap_use_url_priority == "0") echo "selected=\"selected\""; ?> ><?php echo $text_disabled; ?></option>
                                <option value="1" <?php if ( $soforp_fast_sitemap_use_url_priority== "1") echo "selected=\"selected\""; ?> ><?php echo $text_enabled ?></option>
                            </select></td>
                    </tr>

                    <tr>
                        <td><?php echo $entry_url; ?></td>
                        <td><a href="<?php echo $url; ?>" target="_new"><?php echo $url; ?></a></td>
                    </tr>
                </table>
            </form>
        </div>
    </div>
</div>

<?php echo $footer; ?>