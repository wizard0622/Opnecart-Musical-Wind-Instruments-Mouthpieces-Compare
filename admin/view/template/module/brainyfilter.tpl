<?php
/**
 * Brainy Filter Ultimate 4.7.2, December 3, 2015 / brainyfilter.com
 * Copyright 2014-2015 Giant Leap Lab / www.giantleaplab.com
 * License: Commercial. Reselling of this software or its derivatives is not allowed. You may use this software for one website ONLY including all its subdomains if the top level domain belongs to you and all subdomains are parts of the same OpenCart store.
 * Support: support@giantleaplab.com
 */
?>
<?php echo $header; ?>

<div id="content">
    <div class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) : ?>
            <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
        <?php endforeach; ?>
    </div>
    <?php if ($success) : ?>
        <div class="success"><?php echo $success; ?></div>
    <?php endif; ?>
    <?php if (count($error_warning)) : ?>
        <?php foreach ($error_warning as $err) : ?>
            <div class="warning"><?php echo $err; ?></div>
        <?php endforeach; ?>
    <?php endif; ?>
    <div class="box">
        <div class="heading">
            <h1><img src="view/image/module.png" alt="" /> <?php echo $heading_title; ?></h1>
            <div class="buttons">
                <a onclick="jQuery('[name=action]').val('apply');BrainyFilterAdm.submitForm();" class="bf-button bf-apply-btn"><span class="icon"></span><?php echo $button_save; ?></a>
                <a onclick="BrainyFilterAdm.submitForm();" class="bf-button bf-save-btn"><span class="icon"></span><?php echo $button_save_n_close; ?></a>
                <a onclick="location = '<?php echo $cancel; ?>';" class="bf-button bf-cancel-btn"><span class="icon"></span><?php echo $button_cancel; ?></a>
            </div>
        </div>
    </div>
    <!-- settings block -->
    <form action="<?php echo $action; ?>" method="post" enctype="application/x-www-form-urlencoded" id="form">
        <input type="hidden" name="action" value="save" />
        <input type="hidden" name="bf" value="" />
    </form>
    <form action="" id="bf-form">
        <!-- main menu -->
        <div id="bf-adm-main-menu">
            <ul class="clearfix">
                <li class="tab selected" data-target="#bf-adm-basic-settings">
                    <span class="icon basic"></span>
                    <?php echo $text_mm_basic_settings; ?>
                </li>
                <li class="tab" data-target="#bf-adm-instances">
                    <span class="icon layouts"></span>
                    <?php echo $text_mm_module_instances; ?>
                </li>
                <li class="tab" data-target="#bf-adm-faq">
                    <span class="icon faq"></span>
                    <?php echo $text_mm_help; ?>
                </li>
            </ul>
            <div class="bf-right">
                <button onclick="BrainyFilterAdm.refreshDB();return false;" class="bf-button" id="bf-refresh-db">
                    <span class="icon bf-update"></span><span class="lbl"><?php echo $text_update_cache; ?></span>
                </button>
            </div>
        </div>
        <!-- main menu block end -->
        
        <div id="bf-adm-main-container">
            
            <!-- basic settings container -->
            <div id="bf-adm-basic-settings" class="tab-content" data-group="main">
                <p class="bf-tab-description"><span class="bf-info icon"></span><?php echo $text_info_basic_settings; ?></p>
                <div class="bf-panel">
                    <div id="bf-create-instance-alert" class="bf-alert">
                        <?php echo $text_new_instance_alert; ?>
                    </div>
                    <div class="tab-content-inner">

                    </div>
                </div>
            </div>
            <!-- module instances container -->
            <div id="bf-adm-instances" class="tab-content" data-group="main">
                <p class="bf-tab-description"><span class="bf-info icon"></span><?php echo $text_info_module_instances; ?></p>
                <div class="bf-panel">
                    <div class="bf-panel-row clearfix">
                        <div class="left">
                            <label for="bf-layout-selector"><?php echo $entry_select_instance; ?></label>
                            <select name="bf_layout" id="bf-layout-selector">
                        
                            </select>
                        </div>
                        <div class="right">
                            <label>&nbsp;</label>
                            <button id="add-instance-btn"><span class="bf-plus icon"></span> <?php echo $text_add_new_instance; ?></button>
                        </div>
                        <div style="clear:both;"></div>
                        <div class="bf-notice"></div>
                    </div>
                    <div class="tab-content-inner">

                    </div>
                </div>
            </div>
            
            <!-- FAQ container -->
            <div id="bf-adm-faq" class="tab-content" data-group="main">
                <div class="bf-panel">
                    <ul class="tabs clearfix">
                        <li class="tab selected" data-target="#bf-faq-n-troubleshooting"><?php echo $text_faq_n_trouleshooting; ?></li>
                        <li class="tab" data-target="#bf-about"><?php echo $text_about; ?></li>
                    </ul>
                    <div data-group="help-tabs" id="bf-faq-n-troubleshooting" class="tab-content with-border selected">
                        <iframe src="http://docs.brainyfilter.com/faq-and-troubleshooting.html"></iframe>
                    </div>
                    <div data-group="help-tabs" id="bf-about" class="tab-content with-border">
                        <div class="bf-about-text">
                            <?php echo $text_about_content; ?>
                            <hr />
                            <p>Brainy Filter Ultimate 4.7.2. <a href="http://giantleaplab.com/">Giant Leap Lab</a> &copy; 2014</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <div class="bf-signature"><?php echo $bf_signature; ?></div>
    <!--                -->
</div>

<!-- Form template for a single instance -->
<div id="bf-adm-template" style="display: none;" data-layout-id="">
    <div class="bf-panel-row bf-local-settings clearfix">
        <div class="left">
            <label for="bf-layout-id-{m}">Layout</label>
            <select name="bf[{m}][layout_id]" id="bf-layout-id-{m}" class="bf-layout-select bf-w195">
                <option value="0" selected="selected"><?php echo $entry_select; ?></option>
                <?php foreach ($layouts as $id => $layout) : ?>
                    <option value="<?php echo $id; ?>"><?php echo $layout; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="left">
            <label for="bf-layout-position-{m}">Position</label>
            <select name="bf[{m}][layout_position]" id="bf-layout-position-{m}" class="bf-layout-position bf-w195">
                <option value="content_top"><?php echo $text_content_top; ?></option>
                <option value="content_bottom"><?php echo $text_content_bottom; ?></option>
                <option value="column_left"><?php echo $text_column_left; ?></option>
                <option value="column_right"><?php echo $text_column_right; ?></option>
            </select>
        </div>
        <div class="left">
            <label for="bf-layout-sort-order-{m}">Sort Order</label>
            <input type="text" name="bf[{m}][layout_sort_order]" id="bf-layout-sort-order-{m}" class="bf-layout-sort bf-w65" />
        </div>
        <div class="left">
            <span class="bf-label center">Enabled</span>
            <div class="bf-layout-enable bf-switcher yesno">
                <input id="bf-layout-{m}-off" type="radio" name="bf[{m}][layout_enabled]" value="0" checked="checked" />
                <label for="bf-layout-{m}-off"><?php echo $text_no; ?></label>
                <input id="bf-layout-{m}-on" type="radio" name="bf[{m}][layout_enabled]" value="1" />
                <label for="bf-layout-{m}-on"><?php echo $text_yes; ?></label>
            </div>
        </div>
        <div class="left">
            <span class="bf-label">&nbsp;</span>
            <a class="bf-remove-layout"><?php echo $text_remove; ?></a>
        </div>

    </div>
    
    <!-- Basic section tabs -->
    <ul class="tabs clearfix">
        <li class="tab selected" data-target="#bf-filter-behaviour-{m}"><?php echo $text_tab_filter_behaviour; ?></li>
        <li class="tab" data-target="#bf-data-submission-{m}"><?php echo $text_tab_data_submission; ?></li>
        <li class="tab" data-target="#bf-attributes-{m}"><?php echo $text_tab_attributes; ?></li>
        <li class="tab" data-target="#bf-options-{m}"><?php echo $text_tab_options; ?></li>
        <li class="tab" data-target="#bf-filters-{m}"><?php echo $text_tab_filters; ?></li>
        <li class="tab" data-target="#bf-style-{m}"><?php echo $text_tab_style; ?></li>
        <li class="tab bf-global-settings" data-target="#bf-opencart-{m}"><?php echo $text_tab_opencart_related; ?></li>
    </ul>
    <!-- Behaviour Tab --> 
    <div id="bf-filter-behaviour-{m}" class="tab-content with-border" data-group="settings-{m}">
        <div class="bf-th-header expanded"><span class="icon bf-arrow"></span><?php echo $text_dom_selectors; ?></div>
        <div>
            <div class="bf-alert" style="display: block;"><?php echo $text_dom_selectors_warning; ?></div>
        <table class="bf-adm-table">
            <tr>
                <td class="bf-adm-label-td">
                    <span class="bf-wrapper"><label for="bf-container-selector-{m}"><?php echo $text_container_selector; ?></label></span>
                </td>
                <td>
                    <input style="width: 290px;" type="text" name="bf[{m}][behaviour][containerSelector]" value="" id="bf-container-selector-{m}" placeholder="<?php echo $settings['basic']['behaviour']['containerSelector']; ?>" />
                </td>
            </tr>
            <tr>
                <td class="bf-adm-label-td">
                    <span class="bf-wrapper"><label for="bf-paginator-selector-{m}"><?php echo $text_paginator_selector; ?></label></span>
                </td>
                <td>
                    <input style="width: 290px;" type="text" name="bf[{m}][behaviour][paginatorSelector]" value="" id="bf-paginator-selector-{m}" placeholder="<?php echo $settings['basic']['behaviour']['paginatorSelector']; ?>" />
                </td>
            </tr>
        </table>
        </div>
        <div class="bf-filter-title">
            <div class="bf-th-header expanded"><span class="icon bf-arrow"></span><?php echo $text_filter_name; ?></div>
            <div>
                 <table class="bf-adm-table">
                    <tr>
                      <div class="ltabs">
                            <?php foreach ($languages as $language) : ?>
                                <a class="tab" data-target="#tab-language-<?php echo $language['language_id']; ?>-{m}" style="display: inline;">
                                    <img src="view/image/flags/<?php echo $language['image']; ?>" /> 
                                    <?php echo $language['name']; ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </tr>
                    <tr>
                        <td colspan="3" class="bf-adm-label-td bf-filter-title-inputs"> 
                            <?php foreach ($languages as $language) : ?>
                            <div id="tab-language-<?php echo $language['language_id']; ?>-{m}" class="tab-content bf-w195" data-group="filter-title-{m}">
                                   <input type="text" size="4" name="bf[{m}][behaviour][filter_name][<?php echo $language['language_id']; ?>]" value="" data-adv-group="filter-name-{m}"  class="bf-w195" /> 
                            </div>
                            <?php endforeach; ?>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="bf-global-settings">
            <div class="bf-th-header expanded"><span class="icon bf-arrow"></span><?php echo $entry_products_attributes_handling; ?></div>
            <div>
            <table class="bf-adm-table">
                
                <tr>
                    <th></th>
                    <th class="center"><?php echo $text_enabled; ?></th>
                    <th></th>
                </tr>

                <tr>
                    <td class="bf-adm-label-td"><span class="bf-wrapper"><?php echo $entry_hide_products_with_empty_stock; ?></span></td>
                    <td class="bf-switcher" id="bf-multi-attr-switch">
                        <input id="bf-hide-out-of-stock-off" type="radio" name="bf[basic][global][hide_out_of_stock]" value="0" />
                        <label for="bf-hide-out-of-stock-off"><?php echo $text_no; ?></label>
                        <input id="bf-hide-out-of-stock-on" type="radio" name="bf[basic][global][hide_out_of_stock]" value="1" />
                        <label for="bf-hide-out-of-stock-on"><?php echo $text_yes; ?></label>
                    </td>
                    <td></td>
                </tr>
                <tr>
                    <td class="bf-adm-label-td"><span class="bf-wrapper"><?php echo $entry_enable_multiple_attributes; ?></span></td>
                    <td class="bf-switcher" id="bf-multi-attr-switch">
                        <input id="bf-multiple-attributes-off" type="radio" name="bf[basic][global][multiple_attributes]" value="0" data-disable-adv="attr-separator" />
                        <label for="bf-multiple-attributes-off"><?php echo $text_no; ?></label>
                        <input id="bf-multiple-attributes-on" type="radio" name="bf[basic][global][multiple_attributes]" value="1" data-enable-adv="attr-separator" />
                        <label for="bf-multiple-attributes-on"><?php echo $text_yes; ?></label>
                    </td>
                    <td>
                        <label for="bf-attr-separator"><?php echo $entry_separator; ?></label>
                        <input id="bf-attr-separator" type="text" name="bf[basic][global][attribute_separator]" value="" readonly="readonly" size="4" data-adv-group="attr-separator" />
                        <?php echo $entry_multiple_attribute_separator; ?>
                    </td>
                </tr>
            </table>
        </div>
        </div>
        <div class="bf-th-header expanded"><span class="icon bf-arrow"></span><?php echo $text_attributes_display; ?></div>
        <div>
        <table class="bf-adm-table">
            <tr>
                <th></th>
                <th class="center"><?php echo $text_enabled; ?></th>
                <th class="bf-w100">
                    <?php echo $text_sort_order; ?>
                    <div  class="bf-local-settings bf-group-actions">
                        <input type="checkbox" name="bf[{m}][behaviour][sort_order][enabled]" value="1" id="bf-use-manual-sort-{m}" class="bf-override-default" />
                        <label for="bf-use-manual-sort-{m}"><?php echo $entry_override_default; ?></label>
                    </div>
                </th>
                <th class="bf-collapse-td"><?php echo $text_colapse; ?></th>
                <th></th>
            </tr>
            <?php foreach ($filterBlocks as $filter) : ?>
            <tr class="bf-sort">
                <td class="bf-adm-label-td">
                    <span class="bf-wrapper"><?php echo $filter['label']; ?></span>
                </td>
                <td class="bf-switcher">
                    <input id="bf-<?php echo $filter['name']; ?>-filter-{m}-off" type="radio" name="bf[{m}][behaviour][sections][<?php echo $filter['name']; ?>][enabled]" value="0" data-disable-adv="section-{m}-<?php echo $filter['name']; ?>" />
                    <label for="bf-<?php echo $filter['name']; ?>-filter-{m}-off"><?php echo $text_no; ?></label>
                    <input id="bf-<?php echo $filter['name']; ?>-filter-{m}-on" type="radio" name="bf[{m}][behaviour][sections][<?php echo $filter['name']; ?>][enabled]" value="1" data-enable-adv="section-{m}-<?php echo $filter['name']; ?>" />
                    <label for="bf-<?php echo $filter['name']; ?>-filter-{m}-on"><?php echo $text_yes; ?></label>
                </td>
                <td class="center">
                    <input id="bf-<?php echo $filter['name']; ?>-sort-{m}" class="bf-sort-input" type="text" name="bf[{m}][behaviour][sort_order][<?php echo $filter['name']; ?>]" readonly="readonly" />
                </td>
                <td class="bf-collapse-td">
                    <input id="bf-<?php echo $filter['name']; ?>-collapse-{m}" type="checkbox" name="bf[{m}][behaviour][sections][<?php echo $filter['name']; ?>][collapsed]" value="1" data-adv-group="section-{m}-<?php echo $filter['name']; ?>" disabled="disabled" />
                </td>
                <td>
                    <?php if (isset($filter['control']) && isset($possible_controls[$filter['name']])) : ?>
                    <select name="bf[{m}][behaviour][sections][<?php echo $filter['name']; ?>][control]" data-adv-group="section-{m}-<?php echo $filter['name']; ?>" disabled="disabled">
                        <?php foreach ($possible_controls[$filter['name']] as $val => $lbl) : ?>
                        <option value="<?php echo $val; ?>"><?php echo $lbl; ?></option>
                        <?php endforeach; ?>
                    </select>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        </div>
        <div class="bf-th-header expanded"><span class="icon bf-arrow"></span><?php echo $text_filter_layout_view; ?></div>
        <div>
        <table class="bf-adm-table" style="margin-bottom:0;">
            <tr>
                <th></th>
                <th class="center"><?php echo $text_enabled; ?></th>
                <th></th>
                <th></th>
                <th></th>
            </tr>
            <tr>
                <td class="bf-adm-label-td">
                    <span class="bf-wrapper">
                    <?php echo $entry_group_by; ?>
                    </span>
                </td>
                <td class="bf-switcher">
                    <input id="bf-attr-group-{m}-off" type="radio" name="bf[{m}][behaviour][attribute_groups]" value="0" />
                    <label for="bf-attr-group-{m}-off"><?php echo $text_no; ?></label>
                    <input id="bf-attr-group-{m}-on" type="radio" name="bf[{m}][behaviour][attribute_groups]" value="1" />
                    <label for="bf-attr-group-{m}-on"><?php echo $text_yes; ?></label>
                </td>
                <td colspan="3"></td>
            </tr>
        </table>
        <table class="bf-adm-table bf-adv-group-cont" style="margin-bottom:0;">
            <tr>
                <td class="bf-adm-label-td"><span class="bf-wrapper">
                    <?php echo $entry_product_count; ?></span>
                </td>
                <td class="bf-switcher">
                    <input id="bf-product-count-{m}-off" type="radio" name="bf[{m}][behaviour][product_count]" value="0" data-disable-adv="hide-empty-{m}" />
                    <label for="bf-product-count-{m}-off"><?php echo $text_no; ?></label>
                    <input id="bf-product-count-{m}-on" type="radio" name="bf[{m}][behaviour][product_count]" value="1" data-enable-adv="hide-empty-{m}" />
                    <label for="bf-product-count-{m}-on"><?php echo $text_yes; ?></label>
                </td>
                <td colspan="3"></td>
            </tr>
            <tr>
                <td class="bf-adm-label-td"><span class="bf-wrapper">
                    <?php echo $entry_hide_empty_attr; ?></span>
                </td>
                <td class="bf-switcher">
                    <input id="bf-hide-empty-{m}-off" type="radio" name="bf[{m}][behaviour][hide_empty]" value="0" data-adv-group="hide-empty-{m}" disabled="disabled" />
                    <label for="bf-hide-empty-{m}-off"><?php echo $text_no; ?></label>
                    <input id="bf-hide-empty-{m}-on" type="radio" name="bf[{m}][behaviour][hide_empty]" value="1" data-adv-group="hide-empty-{m}" disabled="disabled" />
                    <label for="bf-hide-empty-{m}-on"><?php echo $text_yes; ?></label>
                </td>
                <td colspan="3"></td>
            </tr>
        </table>
        <table class="bf-adm-table bf-intersect-cont">
            <tr>
                <td class="bf-adm-label-td"><span class="bf-wrapper">
                    <?php echo $entry_sliding; ?></span>
                </td>
                <td class="bf-switcher bf-intersect">
                    <input id="bf-sliding-{m}-off" type="radio" name="bf[{m}][behaviour][limit_items][enabled]" value="0" data-disable-adv="sliding-{m}" />
                    <label for="bf-sliding-{m}-off"><?php echo $text_no; ?></label>
                    <input id="bf-sliding-{m}-on" type="radio" name="bf[{m}][behaviour][limit_items][enabled]" value="1" data-enable-adv="sliding-{m}" />
                    <label for="bf-sliding-{m}-on"><?php echo $text_yes; ?></label>
                </td>
                <td colspan="3"> 
                    <input id="bf-number-to-show-{m}" type="text" size="4" name="bf[{m}][behaviour][limit_items][number_to_show]" value="" readonly="readonly" data-adv-group="sliding-{m}" />
                    <label for="bf-number-to-show-{m}"><?php echo $entry_sliding_opts; ?></label>
                    <div class="bf-suboption">
                        <input id="bf-number-to-hide-{m}" type="text" size="4" name="bf[{m}][behaviour][limit_items][number_to_hide]" value="" readonly="readonly" data-adv-group="sliding-{m}" /> 
                        <label for="bf-number-to-hide-{m}"><?php echo $entry_sliding_min; ?></label>
                    </div>
                </td>
            </tr>
            <tr>
                <td class="bf-adm-label-td"><span class="bf-wrapper">
                    <?php echo $entry_limit_height; ?></span>
                </td>
                <td class="bf-switcher bf-intersect">
                    <input id="bf-limit-height-{m}-off" type="radio" name="bf[{m}][behaviour][limit_height][enabled]" value="0" data-disable-adv="limit-height-{m}" />
                    <label for="bf-limit-height-{m}-off"><?php echo $text_no; ?></label>
                    <input id="bf-limit-height-{m}-on" type="radio" name="bf[{m}][behaviour][limit_height][enabled]" value="1" data-enable-adv="limit-height-{m}" />
                    <label for="bf-limit-height-{m}-on"><?php echo $text_yes; ?></label>
                </td>
                <td colspan="3"> 
                    <input id="bf-limit-height-{m}" type="text" size="4" name="bf[{m}][behaviour][limit_height][height]" value="" readonly="readonly" data-adv-group="limit-height-{m}" /> 
                    <label for="bf-limit-height-{m}"><?php echo $entry_limit_height_opts; ?></label>
                </td>
            </tr>

        </table>
    </div>
    </div>
    <!-- Data Submission Tab -->
    <div id="bf-data-submission-{m}" class="tab-content with-border" data-group="settings-{m}">
        <div class="bf-th-header expanded"><span class="icon bf-arrow"></span><?php echo $entry_define; ?></div>
        <div>
        <table class="bf-adm-table">
            <tr>
                <th></th>
                <th class="center">Enabled</th>
                <th></th>
            </tr>
            <tr>
                <td class="bf-adm-label-td"><span class="bf-wrapper">
                    <label for="bf-submit-auto"><?php echo $entry_auto_submission; ?></label></span>
                </td>
                <td class="center">
                    <input id="bf-submit-auto-{m}" type="radio" value="auto" name="bf[{m}][submission][submit_type]" data-enable-adv="submit-type-{m}" />
                </td>
                <td></td>
            </tr>
            <tr>
                <td class="bf-adm-label-td"><span class="bf-wrapper">
                    <label for="bf-submit-delay-{m}"><?php echo $entry_auto_submission_delay; ?></label></span>
                </td>
                <td class="center">
                    <input id="bf-submit-delay-{m}" type="radio" value="delay" name="bf[{m}][submission][submit_type]" data-enable-adv="submit-type-{m}" />
                </td>
                <td>
                    <input id="bf-submit-delay-time-{m}" type="text" name="bf[{m}][submission][submit_delay_time]" readonly="readonly" value="" size="4" maxlength="4" data-adv-group="submit-type-{m}" />
                    <label for="bf-submit-delay-time-{m}"><?php echo $entry_auto_time; ?></label>
                </td>
            </tr>
            <tr>
                <td class="bf-adm-label-td"><span class="bf-wrapper"> 
                        <label for="bf-submit-btn-{m}"><?php echo $entry_button; ?></label></span>
                </td>
                <td class="center"> 
                    <input id="bf-submit-btn-{m}" type="radio" value="button" name="bf[{m}][submission][submit_type]" data-enable-adv="submit-type-{m}" />
                </td>
                <td>
                    <input id="bf-submit-btn-fixed-{m}" type="radio" name="bf[{m}][submission][submit_button_type]" value="fix" disabled="disabled" data-adv-group="submit-type-{m}" />
                    <label for="bf-submit-btn-fixed-{m}"><?php echo $entry_fixed; ?></label>
                    <div class="bf-suboption">
                        <input id="bf-submit-btn-float-{m}" type="radio" name="bf[{m}][submission][submit_button_type]" value="float" disabled="disabled" data-adv-group="submit-type-{m}" />
                        <label for="bf-submit-btn-float-{m}"><?php echo $entry_float; ?></label>
                    </div>
                </td>
            </tr>
            <tr class="bf-local-settings">
                <td class="bf-adm-label-td">
                    <span class="bf-wrapper">
                        <label for="bf-submit-default-{m}"><?php echo $entry_default_submission; ?></label>
                    </span>
                </td>
                <td class="center">
                    <input id="bf-submit-default-{m}" type="radio" value="default" name="bf[{m}][submission][submit_type]" data-enable-adv="submit-type-{m}" class="bf-default" />
                </td>
                <td></td>
            </tr>
            <tr>
                <td class="bf-adm-label-td"><span class="bf-wrapper">
                    <?php echo $entry_hide_panel; ?></span>
                </td>
                <td class="bf-switcher">
                    <input id="bf-hide-layout-{m}-off" type="radio" name="bf[{m}][submission][hide_panel]" value="0" />
                    <label for="bf-hide-layout-{m}-off"><?php echo $text_no; ?></label>
                    <input id="bf-hide-layout-{m}-on" type="radio" name="bf[{m}][submission][hide_panel]" value="1" />
                    <label for="bf-hide-layout-{m}-on"><?php echo $text_yes; ?></label>
                </td>
                <td></td>
            </tr>
        </table>
    </div>
    </div>
    <!-- Attributes Tab -->
    <div id="bf-attributes-{m}" class="tab-content with-border" data-group="settings-{m}" data-select-all-group="attributes-{m}">
        <div class="bf-group-actions bf-global-attr">
            <a data-select-all="attributes-{m}" data-select-all-val="default" class="bf-local-settings"><?php echo $text_set_all_default; ?></a>
            <span class="bf-local-settings">/</span>
            <a data-select-all="attributes-{m}" data-select-all-val="0"><?php echo $text_disable_all; ?></a>
            <span>/</span>
            <a data-select-all="attributes-{m}" data-select-all-val="1"><?php echo $text_enable_all; ?></a>
        </div>            

            <?php if (count($attrGroups)) : ?>
                <?php foreach ($attrGroups as $groupName => $group) : ?>
        <div class="bf-th-header"><span class="icon bf-arrow"></span><?php echo $groupName; ?></div>
        <div>
            <table class="bf-adm-table" data-select-all-group="attributes-{m}">
                <tr>
                    <th></th>
                    <th class="center">
                        <?php echo $text_enabled; ?>
                        <div class="bf-group-actions">
                            <a data-select-all="attributes-{m}" data-select-all-val="default" class="bf-local-settings"><?php echo $text_set_all_default; ?></a>
                            <span class="bf-local-settings">/</span>
                            <a data-select-all="attributes-{m}" data-select-all-val="0"><?php echo $text_disable_all; ?></a>
                            <span>/</span>
                            <a data-select-all="attributes-{m}" data-select-all-val="1"><?php echo $text_enable_all; ?></a>
                        </div> 
                    </th>
                    <th class="bf-w165"><?php echo $text_control; ?></th>
                    <th></th>
                </tr>
                    <?php foreach ($group as $attr) : ?>
                <tr>
                    <td class="bf-adm-label-td"><span class="bf-wrapper"><?php echo $attr['name']; ?></span></td>
                    <td class="center bf-w135 bf-switcher">
                        <input id="bf-attr-<?php echo $attr['id']; ?>-{m}-off" type="radio" name="bf[{m}][attributes][<?php echo $attr['id']; ?>][enabled]" value="0" data-disable-adv="attr-<?php echo $attr['id']; ?>-{m}" <?php if (!isset($settings['basic']['attributes'][$attr['id']])) : ?>checked="true"<?php endif; ?> />
                        <label for="bf-attr-<?php echo $attr['id']; ?>-{m}-off"><?php echo $text_no; ?></label>
                        <input id="bf-attr-<?php echo $attr['id']; ?>-{m}-on" type="radio" name="bf[{m}][attributes][<?php echo $attr['id']; ?>][enabled]" value="1" data-enable-adv="attr-<?php echo $attr['id']; ?>-{m}" />
                        <label for="bf-attr-<?php echo $attr['id']; ?>-{m}-on"><?php echo $text_yes; ?></label>
                    </td>
                    <td class="center">
                        <select name="bf[{m}][attributes][<?php echo $attr['id']; ?>][control]" data-adv-group="attr-<?php echo $attr['id']; ?>-{m}" disabled="disabled">
                            <option value="checkbox"><?php echo $entry_checkbox; ?></option>
                            <option value="radio"><?php echo $entry_radio; ?></option>
                            <option value="select"><?php echo $entry_selectbox; ?></option>
                            <option value="slider"><?php echo $entry_slider; ?></option>
                            <option value="slider_lbl"><?php echo $entry_slider_labels_only; ?></option>
                            <option value="slider_lbl_inp"><?php echo $entry_slider_labels_and_inputs; ?></option>
                        </select>
                    </td>
                    <td>
                        <a onclick="BrainyFilterAdm.attrValues.openPopup(<?php echo $attr['id']; ?>);return false;"><?php echo $text_edit_values_sort_order; ?></a>
                    </td>
                </tr>
                    <?php endforeach; ?>
            </table>
        </div>
                <?php endforeach; ?>
            <?php endif; ?>
    </div>
    <!-- Options Tab -->
    <div id="bf-options-{m}" class="tab-content with-border" data-group="settings-{m}">
        <table class="bf-adm-table" data-select-all-group="options-{m}">
            <?php if (count($options)) : ?>
            <tr>
                <th></th>
                <th class="center">
                    <?php echo $text_enabled; ?>
                    <div class="bf-group-actions">
                        <a data-select-all="options-{m}" data-select-all-val="default" class="bf-local-settings"><?php echo $text_set_all_default; ?></a>
                        <span class="bf-local-settings">/</span>
                        <a data-select-all="options-{m}" data-select-all-val="0"><?php echo $text_disable_all; ?></a>
                        <span>/</span>
                        <a data-select-all="options-{m}" data-select-all-val="1"><?php echo $text_enable_all; ?></a>
                    </div>  
                </th>
                <th class="bf-w165"><?php echo $text_control; ?></th>
                <th class="bf-w195"><?php echo $text_view_mode; ?></th>
                <th></th>
            </tr>
            <?php foreach ($options as $opt) : ?>
            <tr>
                <td class="bf-adm-label-td"><span class="bf-wrapper"><?php echo $opt['name']; ?></span></td>
                <td class="center bf-w135 bf-switcher">
                    <input id="bf-opt-<?php echo $opt['id']; ?>-{m}-off" type="radio" name="bf[{m}][options][<?php echo $opt['id']; ?>][enabled]" value="0" data-disable-adv="opt-<?php echo $opt['id']; ?>-{m}" <?php if (!isset($settings['basic']['options'][$opt['id']])) : ?>checked="true"<?php endif; ?> />
                    <label for="bf-opt-<?php echo $opt['id']; ?>-{m}-off"><?php echo $text_no; ?></label>
                    <input id="bf-opt-<?php echo $opt['id']; ?>-{m}-on" type="radio" name="bf[{m}][options][<?php echo $opt['id']; ?>][enabled]" value="1" data-enable-adv="opt-<?php echo $opt['id']; ?>-{m}" />
                    <label for="bf-opt-<?php echo $opt['id']; ?>-{m}-on"><?php echo $text_yes; ?></label>
                </td>
                <td class="center">
                    <select name="bf[{m}][options][<?php echo $opt['id']; ?>][control]" class="bf-opt-control" data-adv-group="opt-<?php echo $opt['id']; ?>-{m}" disabled="disabled">
                        <option value="checkbox"><?php echo $entry_checkbox; ?></option>
                        <option value="radio"><?php echo $entry_radio; ?></option>
                        <option value="select"><?php echo $entry_selectbox; ?></option>
                        <option value="slider"><?php echo $entry_slider; ?></option>
                        <option value="slider_lbl"><?php echo $entry_slider_labels_only; ?></option>
                        <option value="slider_lbl_inp"><?php echo $entry_slider_labels_and_inputs; ?></option>
                        <option value="grid"><?php echo $entry_grid_of_images; ?></option>
                    </select>
                </td>
                <td class="center">
                    <select name="bf[{m}][options][<?php echo $opt['id']; ?>][mode]" class="bf-opt-mode" data-adv-group="opt-<?php echo $opt['id']; ?>-{m}" disabled="disabled">
                        <option value="label"><?php echo $entry_label; ?></option>
                        <option value="img_label"><?php echo $entry_image_and_label; ?></option>
                        <option value="img"><?php echo $entry_image; ?></option>
                    </select>
                </td>
                <td></td>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
        </table>
    </div>
    <!-- Filters Tab -->
    <div id="bf-filters-{m}" class="tab-content with-border" data-group="settings-{m}">
        <?php if (count($filters)) : ?>
        <table class="bf-adm-table" data-select-all-group="filters-{m}">
            <tr>
                <th></th>
                <th class="center">
                    <?php echo $text_enabled; ?>
                    <div class="bf-group-actions">
                        <a data-select-all="filters-{m}" data-select-all-val="default" class="bf-local-settings"><?php echo $text_set_all_default; ?></a>
                        <span class="bf-local-settings">/</span>
                        <a data-select-all="filters-{m}" data-select-all-val="0"><?php echo $text_disable_all; ?></a>
                        <span>/</span>
                        <a data-select-all="filters-{m}" data-select-all-val="1"><?php echo $text_enable_all; ?></a>
                    </div> 
                </th>
                <th class="bf-w165"><?php echo $text_control; ?></th>
                <th></th>
            </tr>
        <?php foreach ($filters as $filter) : ?>
            <tr>
                <td class="bf-adm-label-td"><span class="bf-wrapper"><?php echo $filter['name']; ?></span></td>
                <td class="center bf-w135 bf-switcher">
                    <input id="bf-filter-<?php echo $filter['id']; ?>-{m}-off" type="radio" name="bf[{m}][filters][<?php echo $filter['id']; ?>][enabled]" value="0" data-disable-adv="filter-<?php echo $filter['id']; ?>-{m}" <?php if (!isset($settings['basic']['filters'][$filter['id']])) : ?>checked="true"<?php endif; ?> />
                    <label for="bf-filter-<?php echo $filter['id']; ?>-{m}-off"><?php echo $text_no; ?></label>
                    <input id="bf-filter-<?php echo $filter['id']; ?>-{m}-on" type="radio" name="bf[{m}][filters][<?php echo $filter['id']; ?>][enabled]" value="1" data-enable-adv="filter-<?php echo $filter['id']; ?>-{m}" />
                    <label for="bf-filter-<?php echo $filter['id']; ?>-{m}-on"><?php echo $text_yes; ?></label>
                </td>
                <td class="center">
                    <select name="bf[{m}][filters][<?php echo $filter['id']; ?>][control]" data-adv-group="filter-<?php echo $filter['id']; ?>-{m}" disabled="disabled">
                        <option value="checkbox"><?php echo $entry_checkbox; ?></option>
                        <option value="radio"><?php echo $entry_radio; ?></option>
                        <option value="select"><?php echo $entry_selectbox; ?></option>
                        <option value="slider"><?php echo $entry_slider; ?></option>
                        <option value="slider_lbl"><?php echo $entry_slider_labels_only; ?></option>
                        <option value="slider_lbl_inp"><?php echo $entry_slider_labels_and_inputs; ?></option>
                    </select>
                </td>
                <td></td>
            </tr>
        <?php endforeach; ?>
        </table>
        <?php endif; ?>

    </div>
    <!-- Theme Tab -->
     <div id="bf-style-{m}" class="tab-content with-border" data-group="settings-{m}">
        <div class="bf-th-header expanded"><span class="icon bf-arrow"></span><?php echo $text_responsive_view; ?></div>
        <div>
            <table class="bf-adm-table bf-adv-group-cont">
                <tr>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th><span class="bf-local-settings"><?php echo $entry_default; ?></span></th>
                    <th></th>
                </tr>
                <tr>
                    <td class="bf-adm-label-td"><span class="bf-wrapper"><?php echo $entry_enable_responsive_mode; ?></span></td> 
                    <td class="bf-switcher">
                        <input id="bf-responsive-{m}-off" type="radio" name="bf[{m}][style][responsive][enabled]" value="0" data-disable-adv="responsive-{m}" />
                        <label for="bf-responsive-{m}-off"><?php echo $text_no; ?></label>
                        <input id="bf-responsive-{m}-on" type="radio" name="bf[{m}][style][responsive][enabled]" value="1" data-enable-adv="responsive-{m}" />
                        <label for="bf-responsive-{m}-on"><?php echo $text_yes; ?></label>
                    </td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td class="bf-adm-label-td"><span class="bf-wrapper"><?php echo $entry_collapse_sections; ?></span></td> 
                    <td class="bf-switcher">
                        <input id="bf-responsive-collapse-{m}-off" type="radio" name="bf[{m}][style][responsive][collapsed]" value="0" data-adv-group="responsive-{m}" />
                        <label for="bf-responsive-collapse-{m}-off"><?php echo $text_no; ?></label>
                        <input id="bf-responsive-collapse-{m}-on" type="radio" name="bf[{m}][style][responsive][collapsed]" value="1" data-adv-group="responsive-{m}" />
                        <label for="bf-responsive-collapse-{m}-on"><?php echo $text_yes; ?></label>
                    </td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td class="bf-adm-label-td"><span class="bf-wrapper"><?php echo $entry_responsive_max_width; ?></span></td> 
                    <td class="center">
                        <input type="text" name="bf[{m}][style][responsive][max_width]" value="" readonly="readonly" data-adv-group="responsive-{m}" class="bf-w65" />
                    </td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td class="bf-adm-label-td"><span class="bf-wrapper"><?php echo $entry_responsive_max_screen_width; ?></span></td> 
                    <td class="center">
                        <input type="text" name="bf[{m}][style][responsive][max_screen_width]" value="" readonly="readonly" data-adv-group="responsive-{m}" class="bf-w65" />
                    </td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td class="bf-adm-label-td"><span class="bf-wrapper"><?php echo $entry_responsive_position; ?></span></td> 
                    <td class="center">
                        <input id="bf-responsive-position-left-{m}" type="radio" name="bf[{m}][style][responsive][position]" value="left" disabled="disabled" data-adv-group="responsive-{m}" />
                        <label for="bf-responsive-position-left-{m}"><?php echo $text_left; ?></label>
                    </td>
                    <td>
                        <input id="bf-responsive-position-right-{m}" type="radio" name="bf[{m}][style][responsive][position]" value="right" disabled="disabled" data-adv-group="responsive-{m}" />
                        <label for="bf-responsive-position-right-{m}"><?php echo $text_right; ?></label>
                    </td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td class="bf-adm-label-td"><span class="bf-wrapper"><?php echo $entry_responsive_offset; ?></span></td> 
                    <td class="center">
                        <input id="bf-responsive-offset-{m}" type="text" name="bf[{m}][style][responsive][offset]" value="" readonly="readonly" data-adv-group="responsive-{m}" class="bf-w65" />
                    </td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td class="bf-adm-label-td"><span class="bf-wrapper"><?php echo $entry_resp_show_btn_color; ?></span></td>
                     <td class="center">
                        <input class="bf-w65 entry color-pick" type="text" name="bf[{m}][style][resp_show_btn_color][val]" />
                     <td class="bf-w65">
                        <span class="bf-color-pick"></span>
                    </td>
                    </td>
                    <td class="bf-w65 center"><input type="checkbox" name="bf[{m}][style][resp_show_btn_color][default]" value="1" class="bf-chkbox-def bf-local-settings" onchange="if (jQuery(this).is(':checked')) {BrainyFilterAdm.changeDefault('resp_show_btn_color', this);}" /></td>
                    <td></td>
                </tr>
                <tr>
                    <td class="bf-adm-label-td"><span class="bf-wrapper"><?php echo $entry_resp_reset_btn_color; ?></span></td>
                     <td class="center">
                        <input class="bf-w65 entry color-pick" type="text" name="bf[{m}][style][resp_reset_btn_color][val]" />
                    <td class="bf-w65">
                        <span class="bf-color-pick"></span>
                    </td>
                    </td>
                    <td class="bf-w65 center"><input type="checkbox" name="bf[{m}][style][resp_reset_btn_color][default]" value="1" class="bf-chkbox-def bf-local-settings" onchange="if (jQuery(this).is(':checked')) {BrainyFilterAdm.changeDefault('resp_reset_btn_color', this);}" /></td>
                    <td></td>
                </tr>
            </table>
        </div> 
        <div class="bf-th-header expanded"><span class="icon bf-arrow"></span><?php echo $text_block_header; ?></div>
        <div>
            <table class="bf-adm-table">
                <tr>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th><span class="bf-local-settings"><?php echo $entry_default; ?></span></th>
                    <th></th>
                </tr>
                <tr>
                    <td class="bf-adm-label-td"><span class="bf-wrapper"><?php echo $entry_background; ?></span></td>
                     <td class="center bf-w170">
                        <input id="bf-style-block-header-background-{m}" class="bf-w65 entry color-pick" type="text" name="bf[{m}][style][block_header_background][val]" />
                    </td>
                    <td class="bf-w65"><span  class="bf-color-pick"></span></td>
                    <td class="bf-w65 center"><input type="checkbox" name="bf[{m}][style][block_header_background][default]" value="1" class="bf-chkbox-def bf-local-settings" onchange = 'if (jQuery(this).is(":checked")) {BrainyFilterAdm.changeDefault("block_header_background", this)}'/></td>
                    <td></td>
                </tr>
                <tr>
                    <td class="bf-adm-label-td"><span class="bf-wrapper"><?php echo $entry_text; ?></span></td>
                    <td class="center bf-w170">
                        <input id="bf-style-block-header-text-{m}" class="bf-w65 entry color-pick" type="text" name="bf[{m}][style][block_header_text][val]" /> 
                    </td>
                    <td class="bf-w65"><span  class="bf-color-pick"></span></td>
                    <td class="bf-w65 center"><input type="checkbox" name="bf[{m}][style][block_header_text][default]" value="1" class="bf-chkbox-def bf-local-settings" onchange = 'if (jQuery(this).is(":checked")) {BrainyFilterAdm.changeDefault("block_header_text", this)}'/></td>
                    <td></td>
                </tr>
            </table>
        </div>
        <div class="bf-th-header expanded"><span class="icon bf-arrow"></span><?php echo $text_product_quantity; ?></div>
        <div>
        <table class="bf-adm-table">
            <tr>
                <th></th>
                <th></th>
                <th></th>
                <th><span class="bf-local-settings"><?php echo $entry_default; ?></span></th>
                <th></th>
            </tr>
             <tr>
                <td class="bf-adm-label-td"><span class="bf-wrapper"><?php echo $entry_background; ?></span></td>
                <td class="center bf-w170">
                    <input id="bf-style-product-quantity-background-{m}" class="bf-w65 entry color-pick" type="text" name="bf[{m}][style][product_quantity_background][val]"/> 
                </td>
                <td class="bf-w65"><span  class="bf-color-pick"></span></td>
                <td class="bf-w65 center"><input type="checkbox" name="bf[{m}][style][product_quantity_background][default]" value="1" class="bf-chkbox-def bf-local-settings" onchange = 'if (jQuery(this).is(":checked")) {BrainyFilterAdm.changeDefault("product_quantity_background", this)}'/></td>
                <td></td>
            </tr>
            <tr>
                <td class="bf-adm-label-td"><span class="bf-wrapper"><?php echo $entry_text; ?></span></td>
                <td class="center bf-w170">
                    <input id="bf-style-product-quantity-text-{m}" class="bf-w65 entry color-pick" type="text" name="bf[{m}][style][product_quantity_text][val]" /> 
                </td>
                <td class="bf-w65"><span  class="bf-color-pick"></span></td>
                <td class="bf-w65 center"><input type="checkbox" name="bf[{m}][style][product_quantity_text][default]" value="1" class="bf-chkbox-def bf-local-settings" onchange = 'if (jQuery(this).is(":checked")) {BrainyFilterAdm.changeDefault("product_quantity_text", this)}' /></td>
                <td></td>
            </tr>
            </table>
            </div>
            <div class="bf-th-header expanded"><span class="icon bf-arrow"></span><?php echo $text_price_slider; ?></div>
        <div>
        <table class="bf-adm-table">
            <tr>
                <th></th>
                <th></th>
                <th></th>
                <th><span class="bf-local-settings"><?php echo $entry_default; ?></span></th>
                <th></th>
            </tr>
             <tr>
                <td class="bf-adm-label-td"><span class="bf-wrapper"><?php echo $entry_background; ?></span></td>
                <td class="center bf-w170">
                    <input id="bf-style-price-slider-background-{m}" class="bf-w65 entry color-pick" type="text" name="bf[{m}][style][price_slider_background][val]" /> 
                </td>
                <td class="bf-w65"><span  class="bf-color-pick"></span></td>
                <td class="bf-w65 center"><input type="checkbox" name="bf[{m}][style][price_slider_background][default]" value="1" class="bf-chkbox-def bf-local-settings" onchange = 'if (jQuery(this).is(":checked")) {BrainyFilterAdm.changeDefault("price_slider_background", this)}' /></td>
                <td></td>
            </tr>
            <tr>
                <td class="bf-adm-label-td"><span class="bf-wrapper"><?php echo $entry_active_area_background; ?></span></td>
                <td class="center bf-w170">
                    <input id="bf-style-price-slider-area-background-{m}" class="bf-w65 entry color-pick" type="text" name="bf[{m}][style][price_slider_area_background][val]" /> 
                </td>
                <td class="bf-w65"><span  class="bf-color-pick"></span></td>
                <td class="bf-w65 center"><input type="checkbox" name="bf[{m}][style][price_slider_area_background][default]" value="1" class="bf-chkbox-def bf-local-settings" onchange = 'if (jQuery(this).is(":checked")) {BrainyFilterAdm.changeDefault("price_slider_area_background", this)}' /></td>
                <td></td>
            </tr>
            <tr>
                <td class="bf-adm-label-td"><span class="bf-wrapper"><?php echo $entry_border; ?></span></td>
                <td class="center bf-w170">
                    <input id="bf-style-price-slider-border-{m}" class="bf-w65 entry color-pick" type="text" name="bf[{m}][style][price_slider_border][val]" /> 
                </td>
                <td class="bf-w65"><span  class="bf-color-pick"></span></td>
                <td class="bf-w65 center"><input type="checkbox" name="bf[{m}][style][price_slider_border][default]" value="1" class="bf-chkbox-def bf-local-settings"  onchange = 'if (jQuery(this).is(":checked")) {BrainyFilterAdm.changeDefault("price_slider_border", this)}'/></td>
                <td></td>
            </tr>
            <tr>
                <td class="bf-adm-label-td"><span class="bf-wrapper"><?php echo $entry_slider_handle_background; ?></span></td>
                <td class="center bf-w170">
                    <input id="bf-style-price-slider-handle-background-{m}" class="bf-w65 entry color-pick" type="text" name="bf[{m}][style][price_slider_handle_background][val]" /> 
                </td>
                <td class="bf-w65"><span  class="bf-color-pick"></span></td>
                <td class="bf-w65 center"><input type="checkbox" name="bf[{m}][style][price_slider_handle_background][default]" value="1" class="bf-chkbox-def bf-local-settings"  onchange = 'if (jQuery(this).is(":checked")) {BrainyFilterAdm.changeDefault("price_slider_handle_background", this)}'/></td>
                <td></td>
            </tr>
            <tr>
                <td class="bf-adm-label-td"><span class="bf-wrapper"><?php echo $entry_slider_handle_border; ?></span></td>
                <td class="center bf-w170">
                    <input id="bf-style-price-slider-handle-border-{m}" class="bf-w65 entry color-pick" type="text" name="bf[{m}][style][price_slider_handle_border][val]" /> 
                </td>
                <td class="bf-w65"><span  class="bf-color-pick"></span></td>
                <td class="bf-w65 center"><input type="checkbox" name="bf[{m}][style][price_slider_handle_border][default]" value="1" class="bf-chkbox-def bf-local-settings" onchange = 'if (jQuery(this).is(":checked")) {BrainyFilterAdm.changeDefault("price_slider_handle_border", this)}'/></td>
                <td></td>
            </tr>
                
        </table>
        </div>
         <div class="bf-th-header expanded"><span class="icon bf-arrow"></span><?php echo $text_group_block_header; ?></div>
        <div>
        <table class="bf-adm-table">
            <tr>
                <th></th>
                <th></th>
                <th></th>
                <th><span class="bf-local-settings"><?php echo $entry_default; ?></span></th>
                <th></th>
            </tr>
             <tr>
                <td class="bf-adm-label-td"><span class="bf-wrapper"><?php echo $entry_background; ?></span></td>
                <td class="center bf-w170">
                    <input id="bf-style-group-block-header-background-{m}" class="bf-w65 entry color-pick" type="text" name="bf[{m}][style][group_block_header_background][val]" /> 
                </td>
                <td class="bf-w65"><span  class="bf-color-pick"></span></td>
                <td class="bf-w65 center"><input type="checkbox" name="bf[{m}][style][group_block_header_background][default]" value="1" class="bf-chkbox-def bf-local-settings"  onchange = 'if (jQuery(this).is(":checked")) {BrainyFilterAdm.changeDefault("group_block_header_background", this)}'/></td>
                <td></td>
            </tr>
            <tr>
                <td class="bf-adm-label-td"><span class="bf-wrapper"><?php echo $entry_text; ?></span></td>
                <td class="center bf-w170">
                    <input id="bf-style-group-block-header-text-{m}" class="bf-w65 entry color-pick" type="text" name="bf[{m}][style][group_block_header_text][val]"  /> 
                </td>
                <td class="bf-w65"><span  class="bf-color-pick"></span></td>
                <td class="bf-w65 center"><input type="checkbox" name="bf[{m}][style][group_block_header_text][default]" value="1" class="bf-chkbox-def bf-local-settings" onchange = 'if (jQuery(this).is(":checked")) {BrainyFilterAdm.changeDefault("group_block_header_text", this)}' /></td>
                <td></td>
            </tr>
            </table>
            </div>
    </div>
    <!-- Opencart Related Tab -->
    <div id="bf-opencart-{m}" class="tab-content with-border bf-global-settings" data-group="settings-{m}">
        <table class="bf-adm-table">
            <tr>
                <td class="bf-adm-label-td long"><span class="bf-wrapper">
                    <?php echo $entry_stock_status_id; ?></span>
                </td>
                <td class="center">
                    <select name="bf[basic][global][instock_status_id]">
                        <?php foreach ($stockStatuses as $status) : ?>
                                <option value="<?php echo $status['stock_status_id']; ?>"><?php echo $status['name']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
                <td></td>
            </tr>
            <tr>
                <td class="bf-adm-label-td long"><span class="bf-wrapper">
                    <?php echo $entry_subcats_fix; ?></span>
                </td>
                <td class="bf-switcher">
                    <input id="bf-subcategories-off" type="radio" name="bf[basic][global][subcategories_fix]" value="0" />
                    <label for="bf-subcategories-off"><?php echo $text_no; ?></label>
                    <input id="bf-subcategories-on" type="radio" name="bf[basic][global][subcategories_fix]" value="1" />
                    <label for="bf-subcategories-on"><?php echo $text_yes; ?></label>
                </td>
                <td></td>
            </tr>
        </table>                    
    </div>
</div>

<!-- End of form template -->

<!-- Attribute values popup -->

<div id="bf-attr-value-popup" style="display: none;">
    <div class="box">
        <div class="heading clearfix">
            <h3><?php echo $text_edit_values_sort_order; ?></h3>
            <div class="buttons">
                <button class="bf-auto-sort" data-type="number">0..9</button>
                <button class="bf-auto-sort" data-type="string">A..Z</button>
                <a onclick="BrainyFilterAdm.attrValues.save();" class="bf-button bf-save-btn" title="<?php echo $button_save_n_close; ?>"><span class="icon"></span></a>
                <a onclick="BrainyFilterAdm.attrValues.closePopup();" class="bf-button bf-cancel-btn" title="<?php echo $button_cancel; ?>"><span class="icon"></span></a>
            </div>
        </div>
    </div>
    <div class="bf-content">
        <div class="htabs">
        <?php foreach ($languages as $language) : ?>
            <a class="tab" data-target="#tab-language-<?php echo $language['language_id']; ?>" style="display: inline;">
                <img src="view/image/flags/<?php echo $language['image']; ?>" /> 
                <?php echo $language['name']; ?>
            </a>
        <?php endforeach; ?>
        </div>
        <form action="">
            <?php foreach ($languages as $language) : ?>
            <div id="tab-language-<?php echo $language['language_id']; ?>" class="tab-content" data-group="attr-value-lang">
                <table class="bf-adm-table">
                    <tr>
                        <th></th>
                        <th><?php echo $text_sort_order; ?></th>
                    </tr>
                </table>
            </div>
            <?php endforeach; ?>
        </form>
    </div>
</div>

<!-- End of attribute values popup -->

<!-- Category list template -->

<div id="bf-category-list-tpl" class="bf-category-list" style="display: none;">
    <div class="bf-label">
        <?php echo $text_categories; ?>
        (<a onclick="jQuery(this).closest('.bf-category-list').find('input').removeAttr('checked')"><?php echo $text_disable_all; ?></a>
        <span>/</span>
        <a onclick="jQuery(this).closest('.bf-category-list').find('input').attr('checked', 'checked')"><?php echo $text_enable_all; ?></a>)
    </div>
    <div class="bf-cat-list-cont">
        <ul data-select-all-group="categories-{m}">
            <?php foreach ($categories as $cat) : ?>
            <li>
                <label>
                    <input type="checkbox" name="bf[{m}][categories][<?php echo $cat['category_id']; ?>]" value="1" />
                    <?php echo $cat['name']; ?>
                </label>
            </li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>

<!-- End of Category list template -->

<script>
BrainyFilterAdm.settings = <?php echo json_encode($settings); ?>;
BrainyFilterAdm.layoutsCnt = <?php echo $layoutsCount; ?>;
BrainyFilterAdm.layouts = <?php echo json_encode($layouts); ?>;
BrainyFilterAdm.defaultLayout = <?php echo json_encode($defaultLayout); ?>;
BrainyFilterAdm.refreshActionUrl = '<?php echo $refreshAction; ?>';
BrainyFilterAdm.attrValActionUrl = '<?php echo $attributeValuesAction; ?>';
BrainyFilterAdm.categoryLayouts = <?php echo json_encode($category_layouts); ?>;
BrainyFilterAdm.lang = {
        'select_all' : '<?php echo $entry_select_all; ?>',
        'unselect_all' : '<?php echo $entry_unselect_all; ?>',
        'default' : '<?php echo $entry_default; ?>',
        'filter_name' : '<?php echo $entry_filter_at_layout; ?>',
        'new_instance' : '<?php echo $entry_new_instance; ?>',
        'error_layout_not_set' : '<?php echo $bf_error_layout_not_set; ?>',
        'error_cant_remove_default' : '<?php echo $text_remove_default_layout; ?>',
        'notice_new_layout' : '<?php echo $text_notice_new_layout; ?>',
        'default_layout' : '<?php echo $text_default_layout; ?>',
        'confirm_remove_layout' : '<?php echo $text_confirm_remove_layout; ?>',
        'updating' : '<?php echo $text_updating; ?>',
        'confirm_refresh_db' : '<?php echo $text_confirm_refresh_db; ?>',
        'content_top' : '<?php echo $text_content_top; ?>',
        'column_left' : '<?php echo $text_column_left; ?>',
        'column_right' : '<?php echo $text_column_right; ?>',
        'content_bottom' : '<?php echo $text_content_bottom; ?>'
    };
jQuery(document).ready(BrainyFilterAdm.init());
</script>

<?php echo $footer;