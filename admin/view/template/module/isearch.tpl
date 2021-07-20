<?php echo $header; ?>
<?php $iModuleNotActivated = empty($data['iSearch']['Enabled']); ?>

<div id="content" class="iModuleContent">
  <div class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
    <?php } ?>
  </div>
  <?php if (!empty($this->session->data['success'])) { ?>
  <div class="success autoSlideUp"><?php echo $this->session->data['success']; ?></div>
  <script> $('.autoSlideUp').delay(3000).fadeOut(600, function(){ $(this).show().css({'visibility':'hidden'}); }).slideUp(600);</script>
  <?php $this->session->data['success'] = null; } ?>
  <?php if ($error_warning) { ?>
  <div class="warning"><?php echo $error_warning; ?></div>
  <?php } ?>
  <div class="box">
    <div class="heading">
    <h1><img src="view/image/imodules.png" style="margin-top: -3px;" alt="" /> <span class="iModulesTitle"><?php echo $heading_title; ?></span>
    <ul class="iModuleAdminSuperMenu">
    	<li class="selected">Control Panel</li>
    	<li>Customization</li>
    	<li>Improving Results</li>
    	<li>Support</li>
    </ul>
    </h1>
      <div class="buttons"><a onclick="$('#form').submit();" class="button submitButton"><?php echo $button_save; ?></a><a onclick="location = '<?php echo $cancel; ?>';" class="button"><?php echo $button_cancel; ?></a></div>
    </div>
     <?php if ($iModuleNotActivated==true){ ?>
    <style> .iModuleContent .box .content { display:none } </style>
    <script> $('.submitButton').html('Activate'); </script>
    <div class="notActivatedContent">
    	<h1>iSearch is not activated.</h1>
        <a href="javascript:void(0)" onclick="$('#form').attr('action',$('#form').attr('action')+'&activate=true'); $('#form').submit();" class="iModuleActivateButton">Activate iSearch</a>
    </div>
    <?php } ?>
    <div class="content">
      <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
      <ul class="iModuleAdminSuperWrappers">
      	<li>
            <table class="form">
              <tr>
                <td><span class="required">*</span> <?php echo $entry_code; ?></td>
                <td valign="top">
                    <select name="iSearch[Enabled]" class="iSearchEnabled">
                        <option value="yes" <?php echo ($data['iSearch']['Enabled'] == 'yes') ? 'selected=selected' : ''?>>Enabled</option>
                        <option value="no" <?php echo ($data['iSearch']['Enabled'] == 'no') ? 'selected=selected' : ''?>>Disabled</option>
                    </select>
               </td>
              </tr>
              <tr>
              	<td valign="top"><span class="required">*</span> Search in: <span class="help">Choose only the parameters you need.</span></td>
                <td>
                <span class="searchInSpan">
                	<input type="checkbox" id="searchIn1" name="iSearch[SearchIn][ProductName]" <?php if(!empty($data['iSearch']['SearchIn']['ProductName'])) { echo ($data['iSearch']['SearchIn']['ProductName'] == 'on') ? 'checked=checked' : ''; } else { echo ($iModuleNotActivated == true) ? 'checked=checked' : ''; } ?> /><label for="searchIn1">Product Name</label>
                </span>
                <span class="searchInSpan">
                	<input type="checkbox" id="searchIn2" name="iSearch[SearchIn][ProductModel]"  <?php if(!empty($data['iSearch']['SearchIn']['ProductModel'])) echo ($data['iSearch']['SearchIn']['ProductModel'] == 'on') ? 'checked=checked' : ''?> /><label for="searchIn2">Product Model</label>
                </span>
                <span class="searchInSpan onlyUseAJAX">
                	<input type="checkbox" id="searchIn3" name="iSearch[SearchIn][UPC]" <?php if(!empty($data['iSearch']['SearchIn']['UPC'])) echo ($data['iSearch']['SearchIn']['UPC'] == 'on') ? 'checked=checked' : ''?> /><label for="searchIn3">UPC</label>		</span>
                <span class="searchInSpan onlyUseAJAX">
                	<input type="checkbox" id="searchIn10" name="iSearch[SearchIn][SKU]" <?php if(!empty($data['iSearch']['SearchIn']['SKU'])) echo ($data['iSearch']['SearchIn']['SKU'] == 'on') ? 'checked=checked' : ''?> /><label for="searchIn10">SKU</label>		</span>
                <span class="searchInSpan onlyUseAJAX">
                	<input type="checkbox" id="searchIn15" name="iSearch[SearchIn][EAN]" <?php if(!empty($data['iSearch']['SearchIn']['EAN'])) echo ($data['iSearch']['SearchIn']['EAN'] == 'on') ? 'checked=checked' : ''?> /><label for="searchIn15">EAN</label>		</span>
                <span class="searchInSpan onlyUseAJAX">
                	<input type="checkbox" id="searchIn16" name="iSearch[SearchIn][JAN]" <?php if(!empty($data['iSearch']['SearchIn']['JAN'])) echo ($data['iSearch']['SearchIn']['JAN'] == 'on') ? 'checked=checked' : ''?> /><label for="searchIn16">JAN</label>		</span>
                <span class="searchInSpan onlyUseAJAX">
                	<input type="checkbox" id="searchIn17" name="iSearch[SearchIn][ISBN]" <?php if(!empty($data['iSearch']['SearchIn']['ISBN'])) echo ($data['iSearch']['SearchIn']['ISBN'] == 'on') ? 'checked=checked' : ''?> /><label for="searchIn17">ISBN</label>		</span>
                <span class="searchInSpan onlyUseAJAX">
                	<input type="checkbox" id="searchIn18" name="iSearch[SearchIn][MPN]" <?php if(!empty($data['iSearch']['SearchIn']['MPN'])) echo ($data['iSearch']['SearchIn']['MPN'] == 'on') ? 'checked=checked' : ''?> /><label for="searchIn18">MPN</label>		</span>
                <span class="searchInSpan onlyUseAJAX">
                	<input type="checkbox" id="searchIn4" name="iSearch[SearchIn][Manufacturer]" <?php if(!empty($data['iSearch']['SearchIn']['Manufacturer'])) echo ($data['iSearch']['SearchIn']['Manufacturer'] == 'on') ? 'checked=checked' : ''?> /><label for="searchIn4">Manufacturer</label>		
                </span>
                <span class="searchInSpan onlyUseAJAX">
                	<input type="checkbox" id="searchIn7" name="iSearch[SearchIn][AttributeNames]" <?php if(!empty($data['iSearch']['SearchIn']['AttributeNames'])) echo ($data['iSearch']['SearchIn']['AttributeNames'] == 'on') ? 'checked=checked' : ''?> /><label for="searchIn7">Attribute Names</label>		</span>
                <span class="searchInSpan onlyUseAJAX">
                	<input type="checkbox" id="searchIn7_1" name="iSearch[SearchIn][AttributeValues]" <?php if(!empty($data['iSearch']['SearchIn']['AttributeValues'])) echo ($data['iSearch']['SearchIn']['AttributeValues'] == 'on') ? 'checked=checked' : ''?> /><label for="searchIn7_1">Attribute Values</label>		</span>
                <span class="searchInSpan onlyUseAJAX">
                	<input type="checkbox" id="searchIn8" name="iSearch[SearchIn][Categories]" <?php if(!empty($data['iSearch']['SearchIn']['Categories'])) echo ($data['iSearch']['SearchIn']['Categories'] == 'on') ? 'checked=checked' : ''?> /><label for="searchIn8">Categories</label>		</span>
                <span class="searchInSpan onlyUseAJAX">
                	<input type="checkbox" id="searchIn19" name="iSearch[SearchIn][Filters]" <?php if(!empty($data['iSearch']['SearchIn']['Filters'])) echo ($data['iSearch']['SearchIn']['Filters'] == 'on') ? 'checked=checked' : ''?> /><label for="searchIn19">Filters</label>		</span>
                <span class="searchInSpan onlyUseAJAX">
                	<input type="checkbox" id="searchIn9" name="iSearch[SearchIn][Description]" <?php if(!empty($data['iSearch']['SearchIn']['Description'])) echo ($data['iSearch']['SearchIn']['Description'] == 'on') ? 'checked=checked' : ''?> /><label for="searchIn9">Description</label>		</span>
                <span class="searchInSpan onlyUseAJAX">
                	<input type="checkbox" id="searchIn5" name="iSearch[SearchIn][Tags]" <?php if(!empty($data['iSearch']['SearchIn']['Tags'])) echo ($data['iSearch']['SearchIn']['Tags'] == 'on') ? 'checked=checked' : ''?> /><label for="searchIn5">Tags</label>		</span>
                <span class="searchInSpan onlyUseAJAX">
                	<input type="checkbox" id="searchIn6" name="iSearch[SearchIn][Location]" <?php if(!empty($data['iSearch']['SearchIn']['Location'])) echo ($data['iSearch']['SearchIn']['Location'] == 'on') ? 'checked=checked' : ''?> /><label for="searchIn6">Location</label>		
                </span>
                <span class="searchInSpan onlyUseAJAX">
                	<input type="checkbox" id="searchIn11" name="iSearch[SearchIn][OptionName]" <?php if(!empty($data['iSearch']['SearchIn']['OptionName'])) echo ($data['iSearch']['SearchIn']['OptionName'] == 'on') ? 'checked=checked' : ''?> /><label for="searchIn11">Option Name</label>		
                </span>
                <span class="searchInSpan onlyUseAJAX">
                	<input type="checkbox" id="searchIn12" name="iSearch[SearchIn][OptionValue]" <?php if(!empty($data['iSearch']['SearchIn']['OptionValue'])) echo ($data['iSearch']['SearchIn']['OptionValue'] == 'on') ? 'checked=checked' : ''?> /><label for="searchIn12">Option Value</label>		
                </span>
                <span class="searchInSpan onlyUseAJAX">
                	<input type="checkbox" id="searchIn13" name="iSearch[SearchIn][MetaDescription]" <?php if(!empty($data['iSearch']['SearchIn']['MetaDescription'])) echo ($data['iSearch']['SearchIn']['MetaDescription'] == 'on') ? 'checked=checked' : ''?> /><label for="searchIn13">Meta Description</label>		
                </span>
                <span class="searchInSpan onlyUseAJAX">
                	<input type="checkbox" id="searchIn14" name="iSearch[SearchIn][MetaKeyword]" <?php if(!empty($data['iSearch']['SearchIn']['MetaKeyword'])) echo ($data['iSearch']['SearchIn']['MetaKeyword'] == 'on') ? 'checked=checked' : ''?> /><label for="searchIn14">Meta Keyword</label>		
                </span>
                </td>
              </tr>
              <tr>
                <td><span class="required">*</span> Responsive Design <span class="help">Select &quot;Yes&quot; if you want to make iSearch Results width fit your responsive design theme Search Field</span></td>
                <td valign="top">
                    <select name="iSearch[ResponsiveDesign]" class="ResponsiveDesign">
                        <option value="no" <?php echo ($data['iSearch']['ResponsiveDesign'] == 'no') ? 'selected=selected' : ''?>>No</option>
                    	<option value="yes" <?php echo ($data['iSearch']['ResponsiveDesign'] == 'yes') ? 'selected=selected' : ''?>>Yes</option>
                    </select>
               </td>
              </tr>
              <tr>
                <td><span class="required">*</span> Use AJAX <span class="help">Select &quot;Yes&quot; if you want to load the search results asynchronously from the server on typing, or &quot;No&quot; to cache them on page load first (some features are limited in non-AJAX mode due to performance considerations)</span></td>
                <td valign="top">
                    <select name="iSearch[UseAJAX]" class="UseAJAX">
                    	<option value="yes" <?php echo ($data['iSearch']['UseAJAX'] == 'yes') ? 'selected=selected' : ''?>>Yes</option>
                        <option value="no" <?php echo ($data['iSearch']['UseAJAX'] == 'no') ? 'selected=selected' : ''?>>No</option>
                    </select>
                    
                    <script>
					$('select.UseAJAX').change(function() {
						if($(this).val() == 'no') {
							$('.onlyUseAJAX').slideUp();
						} else { 
							$('.onlyUseAJAX').slideDown();
						}
					});
					
					var useAJAX = '<?php echo $data['iSearch']['UseAJAX']; ?>';
					if (useAJAX == 'no') {
						$('.onlyUseAJAX').hide();
					}
					
					</script>
               </td>
              </tr>
              <tr>
                <td><span class="required">*</span> Use Strict Search<span class="help">Strict Search searches for your query strictly the whole phrase as-it-is (example: &quot;blue jeans&quot; will match all products that have the full phrase &quot;blue jeans&quot;). If set to &quot;No&quot;, it will search the whole phrase as well as the separate words (example: &quot;blue jeans&quot; will match all products that have &quot;blue&quot; and/or &quot;jeans&quot;).</span></td>
                <td valign="top">
                    <select name="iSearch[UseStrictSearch]" class="UseStrictSearch">
                    	<option value="yes" <?php echo ($data['iSearch']['UseStrictSearch'] == 'yes') ? 'selected=selected' : ''?>>Yes</option>
                        <option value="no" <?php echo ($data['iSearch']['UseStrictSearch'] == 'no') ? 'selected=selected' : ''?>>No</option>
                    </select>
               </td>
              </tr>
              <tr>
                <td><span class="required">*</span> Search Engine on hitting 'Enter' <span class="help">Choose which search engine you prefer to be used. If you choose the default OpenCart search engine, the module will do the instant search, and on submit will produce your original OpenCart search results.</span></td>
                <td valign="top">
                    <select name="iSearch[AfterHittingEnter]" class="AfterHittingEnter">
                    	<option value="default" <?php echo ($data['iSearch']['AfterHittingEnter'] == 'default') ? 'selected=selected' : ''?>>Default OpenCart engine</option>
                        <option value="isearchengine1551" <?php echo ($data['iSearch']['AfterHittingEnter'] == 'isearchengine1551') ? 'selected=selected' : ''?>>iSearch engine for OpenCart 1.5.5.1+</option>
                        <option value="isearchengine1541" <?php echo ($data['iSearch']['AfterHittingEnter'] == 'isearchengine1541') ? 'selected=selected' : ''?>>iSearch engine for OpenCart 1.5.0 - 1.5.4.1</option>
                    </select>

					
                    <span class="help searchTemplateDisclaimer" style="padding: 10px 0 0 5px;">Disclaimer: <br />
                    In case your theme is heavily modified and you have chosen iSearch engine for OpenCart, there may be conflicts between the files of iSearch and the theme files.
                    </span>
                    
               </td>
              </tr>
              <tr>
                <td><span class="required">*</span> Load Images on Instant Search<span class="help"></span></td>
                <td>
                    <select name="iSearch[LoadImagesOnInstantSearch]" class="iSearchLoadImages">
                        <option value="yes" <?php echo ($data['iSearch']['LoadImagesOnInstantSearch'] == 'yes') ? 'selected=selected' : ''?>>Yes</option>
                        <option value="no" <?php echo ($data['iSearch']['LoadImagesOnInstantSearch'] == 'no') ? 'selected=selected' : ''?>>No</option>
                    </select>
               </td>
              </tr>
              <tr class="iSearchActiveTR">
                <td><span class="required">*</span> <?php echo $entry_layouts_active; ?></td>
                <td>
                <?php $i=0;?>
                <?php foreach ($layouts as $layout) { ?>
                <?php 
                    foreach ($modules as $module) {
                        if(!empty($module)) {
                            if ($module['layout_id'] == $layout['layout_id']) {
                                $status = $module['status'];
                                if ((int)$status == 1) {
                                    $checked = ' checked=checked';
                                } else { 
                                    $checked = '';
                                }
                                
                            }
                        } 
                      
                      }
                      
                      if (!isset($status)) {
                            $status = 1;
                            $checked = ' checked=checked';
                      }
                ?>
                <div class="iSearchLayout">
                    <input type="checkbox" value="<?php echo $layout['layout_id']; ?>" id="iSearchActive<?php echo $i?>" <?php echo $checked?> /><label for="iSearchActive<?php echo $i?>"><?php echo $layout['name']; ?></label>
                    <input type="hidden" name="isearch_module[<?php echo $i?>][position]" value="content_bottom" />
                    <input type="hidden" class="iSearchItemLayoutIDField" name="isearch_module[<?php echo $i?>][layout_id]" value="<?php echo $layout['layout_id']; ?>" />
                    <input type="hidden" class="iSearchItemStatusField" name="isearch_module[<?php echo $i?>][status]" value="<?php echo $status ?>" />
                    <input type="hidden" name="isearch_module[<?php echo $i?>][sort_order]" value="<?php echo $i+10?>" />
                </div>
                <?php $i++;} ?>
                 </td>
              </tr>
            </table>
            <script>
            $('.iSearchLayout input[type=checkbox]').change(function() {
                if ($(this).is(':checked')) { 
                    $('.iSearchItemStatusField', $(this).parent()).val(1);
                } else {
                    $('.iSearchItemStatusField', $(this).parent()).val(0);
                }
            });
            
            $('.iSearchEnabled').change(function() {
                toggleiSearchActive(true);
            });
            
            var toggleiSearchActive = function(animated) {
                if ($('.iSearchEnabled').val() == 'yes') {
                    if (animated) 
                        $('.iSearchActiveTR').fadeIn();
                    else 
                        $('.iSearchActiveTR').show();
                } else {
                    if (animated) 
                        $('.iSearchActiveTR').fadeOut();
                    else 
                        $('.iSearchActiveTR').hide();
                }
            }
            
            toggleiSearchActive(false);
            </script>
      	</li>
      	<li>
            <table class="form">
              <tr>
                <td><span class="required">*</span> <?php echo $entry_highlightcolor; ?></td>
                <td>
                    <input type="text" name="iSearch[HighlightColor]" value="<?php echo (empty($data['iSearch']['HighlightColor'])) ? '#F7FF8C' : $data['iSearch']['HighlightColor']?>" />
                </td>
              </tr>
              <tr>
                <td><span class="required">*</span> Limit Results to <span class="help">Denotes on which result to cut off the instant box when iSearch-ing</span></td>
                <td>
                    <input type="text" name="iSearch[ResultsLimit]" value="<?php echo (empty($data['iSearch']['ResultsLimit'])) ? '5' : $data['iSearch']['ResultsLimit']?>" /></td>
              </tr>
              <tr>
                <td>Results Box Width (px)<span class="help">Width of the box in pixels. Default is &quot;278px&quot;.</span></td>
                <td>
                    <input type="text" name="iSearch[ResultsBoxWidth]" value="<?php echo (empty($data['iSearch']['ResultsBoxWidth'])) ? '278px' : $data['iSearch']['ResultsBoxWidth']?>" /></td>
              </tr>
              <tr>
                <td>Results Box Height (px)<span class="help">Height of the box in pixels. Leave empty for &quot;auto&quot;.</span></td>
                <td>
                    <input type="text" name="iSearch[ResultsBoxHeight]" value="<?php echo (empty($data['iSearch']['ResultsBoxHeight'])) ? '' : $data['iSearch']['ResultsBoxHeight']?>" /></td>
              </tr>
              <tr>
                <td>Instant Results Image Width (px)<span class="help">Width of the instant result images in pixels. Default is 80.</span></td>
                <td>
                    <input type="number" name="iSearch[InstantResultsImageWidth]" value="<?php echo (empty($data['iSearch']['InstantResultsImageWidth'])) ? '80' : $data['iSearch']['InstantResultsImageWidth']?>" /></td>
              </tr>
              <tr>
                <td>Instant Results Image Height (px)<span class="help">Height of the instant result images in pixels. Default is 80.</span></td>
                <td>
                    <input type="number" name="iSearch[InstantResultsImageHeight]" value="<?php echo (empty($data['iSearch']['InstantResultsImageHeight'])) ? '80' : $data['iSearch']['InstantResultsImageHeight']?>" /></td>
              </tr>
              <tr>
                <td>Results Title Width<span class="help">Width of the title, typically in %.</span></td>
                <td>
                    <input type="text" name="iSearch[ResultsBoxTitleWidth]" value="<?php echo (empty($data['iSearch']['ResultsBoxTitleWidth'])) ? '42%' : $data['iSearch']['ResultsBoxTitleWidth']?>" /></td>
              </tr>
              <tr>
                <td>Result Title Font Size (px)<span class="help">Leave empty for your site default font size.</span></td>
                <td>
                    <input type="text" name="iSearch[ResultsTitleFontSize]" value="<?php echo (empty($data['iSearch']['ResultsTitleFontSize'])) ? '' : $data['iSearch']['ResultsTitleFontSize']?>" /></td>
              </tr>
              <tr>
                <td>Result Title Font Weight<span class="help">Choose one</span></td>
                <td>
                    <select name="iSearch[ResultsTitleFontWeight]" class="ResultsTitleFontWeight">
                        <option value="bold" <?php echo ($data['iSearch']['ResultsTitleFontWeight'] == 'bold') ? 'selected=selected' : ''?>>Bold</option>
                        <option value="normal" <?php echo ($data['iSearch']['ResultsTitleFontWeight'] == 'normal') ? 'selected=selected' : ''?>>Normal</option>
                    </select>
				</td>
              </tr>
              <tr>
                <td>Show Images<span class="help">Show product images in the results box</span></td>
                <td>
                    <select name="iSearch[ResultsShowImages]" class="ResultsShowImages">
                        <option value="yes" <?php echo ($data['iSearch']['ResultsShowImages'] == 'yes') ? 'selected=selected' : ''?>>Yes</option>
                        <option value="no" <?php echo ($data['iSearch']['ResultsShowImages'] == 'no') ? 'selected=selected' : ''?>>No</option>
                    </select>
                </td>
              </tr>
              <tr>
                <td>Show Models<span class="help">Show product models in the results box</span></td>
                <td>
                    <select name="iSearch[ResultsShowModels]" class="ResultsShowModels">
                        <option value="no" <?php echo ($data['iSearch']['ResultsShowModels'] == 'no') ? 'selected=selected' : ''?>>No</option>
                        <option value="yes" <?php echo ($data['iSearch']['ResultsShowModels'] == 'yes') ? 'selected=selected' : ''?>>Yes</option>
                    </select>
                </td>
              </tr>
              <tr>
                <td>More Results Title<span class="help">This is the label that shows up if more results than the results limit are found</span></td>
                <td>
                	<?php foreach ($languages as $language) : ?>
                    <img src="view/image/flags/<?php echo $language['image']; ?>" title="<?php echo $language['name']; ?>" />
                    <input type="text" name="iSearch[<?php echo $language['language_id']; ?>][ResultsMoreResultsLabel]" value="<?php echo (empty($data['iSearch'][$language['language_id']]['ResultsMoreResultsLabel'])) ? 'View All Results' : $data['iSearch'][$language['language_id']]['ResultsMoreResultsLabel']; ?>" /><br />
                    <?php endforeach; ?>
                </td>
              </tr>
              <tr>
                <td valign="top">Custom CSS<span class="help">Put your custom CSS here</span></td>
                <td>
                    <textarea name="iSearch[CustomCSS]" style="width:320px;height:200px;"><?php echo (empty($data['iSearch']['CustomCSS'])) ? '' : $data['iSearch']['CustomCSS']?></textarea>
                    
                    
                </td>
              </tr>
            </table>
      	</li>
        
      	<li>
            <table class="form">
              <tr>
                <td><span class="required">*</span> Use Singularisation <span class="help">If you use singulatisation, the words will be searched for both their singular and plural form. This method takes into account the words that end in 's' and 'es'. Note that if Strict Search is enabled, the singularisation will not be applied.</span></td>
                <td valign="top">
                    <select name="iSearch[UseSingularize]" class="UseSingularize">
                   		<option value="no" <?php echo (!empty($data['iSearch']['UseSingularize']) && $data['iSearch']['UseSingularize'] == 'no') ? 'selected=selected' : ''?>>No</option>
                    	<option value="yes" <?php echo (!empty($data['iSearch']['UseSingularize']) && $data['iSearch']['UseSingularize'] == 'yes') ? 'selected=selected' : ''?>>Yes</option>
                    </select>
                </td>
              </tr>
              <tr>
                <td>Exclude Search Terms<span class="help">Enter the search terms you wish to exclude line by line. Example:<br /><strong>and<br />or</strong></span></td>
                <td valign="top">
                    <textarea name="iSearch[ExcludeTerms]" style="width:320px;height:100px;"><?php echo (empty($data['iSearch']['ExcludeTerms'])) ? "" : $data['iSearch']['ExcludeTerms']?></textarea>
                </td>
              </tr>
              <tr>
                <td>
                    Exclude products that meet the following criteria:
                </td>
                <td>
                    <table>
                        <tbody id="excludeProduct">
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" class="right">
                                    <a class="button" id="excludeProductAdd">+ Add Rule</a>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                    <script type="text/javascript">
                        var exclude_entries = <?php echo !empty($data['iSearch']['ExcludeProducts']) ? json_encode($data['iSearch']['ExcludeProducts']) : '[]' ?>;
                        var exclude_entries_index = 0;
                        
                        var addExclude = function(entry) {
                            var html = '<tr>';
                            html += '<td>';
                            html += '<select name="iSearch[ExcludeProducts][' + exclude_entries_index + '][type]">';
                            html += '<option value="quantity"' + (typeof entry.type != 'undefined' && entry.type == 'quantity' ? ' selected="selected"' : '') + '>Quantity</option>';
                            html += '<option value="status"' + (typeof entry.type != 'undefined' && entry.type == 'status' ? ' selected="selected"' : '') + '>Status</option>';
							html += '<option value="category_status"' + (typeof entry.type != 'undefined' && entry.type == 'category_status' ? ' selected="selected"' : '') + '>Category Status</option>';
                            html += '</select>';
                            html += '<select name="iSearch[ExcludeProducts][' + exclude_entries_index + '][operator]">';
                            html += '<option value="lt"' + (typeof entry.operator != 'undefined' && entry.operator == 'lt' ? ' selected="selected"' : '') + '>&lt;</option>';
                            html += '<option value="gt"' + (typeof entry.operator != 'undefined' && entry.operator == 'gt' ? ' selected="selected"' : '') + '>&gt;</option>';
                            html += '<option value="eq"' + (typeof entry.operator != 'undefined' && entry.operator == 'eq' ? ' selected="selected"' : '') + '>=</option>';
                            html += '</select>';
                            html += '</td>';
                            html += '<td>';
                            html += '<input type="number" name="iSearch[ExcludeProducts][' + exclude_entries_index + '][value]" value="' + (typeof entry.value != 'undefined' && entry.value != '' ? entry.value : '') + '" />';
                            html += '</td>';
                            html += '<td>';
                            html += '<a class="button ExcludeProductsRemove">- Remove</a>';
                            html += '</td>';
                            html += '</tr>';
                            $('#excludeProduct').append(html);
                            
                            $('.ExcludeProductsRemove').unbind().click(function() {
                                $(this).closest('tr').remove();
                            });
                            
                            exclude_entries_index++;
                        }
                        
                        for (var i in exclude_entries) {
                            var custom_entry = exclude_entries[i];
                            addExclude(exclude_entries[i]);
                        }
                        
                        $('#excludeProductAdd').click(function() {
                            addExclude({});
                        });
                    </script>
                </td>
            </tr>
              <tr>
                <td>Custom Spell Check System</td>
                <td>
                    <select name="iSearch[ResultsSpellCheckSystem]" class="ResultsSpellCheckSystem">
                        <option value="no" <?php echo ($data['iSearch']['ResultsSpellCheckSystem'] == 'no') ? 'selected=selected' : ''?>>No</option>                       <option value="yes" <?php echo ($data['iSearch']['ResultsSpellCheckSystem'] == 'yes') ? 'selected=selected' : ''?>>Yes</option>
                    </select>
                </td>
              </tr>
              <tr>
                <td valign="top">Custom Spell Check Rules<span class="help">E.g. cnema => cinema. Enter as many alternatives as you need. The left side of the rule can also contain a regular expression. For example <strong>/cnem.*/i</strong> => <strong>cinema</strong> will match all search terms containing "cnem" and replace them with "cinema". You can also use the regular expressions to match more than 1 word: <strong>/(cnema)|(cinma)|(cinama)/i</strong> => <strong>cinema</strong></span></td>
                <td>
                <?php //echo '<pre>'; var_dump($data['iSearch']['SCWords']); ?>
                	<div class="wordsWrapper">
                        <div class="wordWrapper">
                        <input type="text" name="iSearch[SCWords][0][incorrect]" class="incorrect" value="<?php echo (empty($data['iSearch']['SCWords'][0]['incorrect'])) ? 'cnema' : $data['iSearch']['SCWords'][0]['incorrect']?>" /> &raquo; <input type="text" name="iSearch[SCWords][0][correct]" class="correct" value="<?php echo (empty($data['iSearch']['SCWords'][0]['correct'])) ? 'cinema' : $data['iSearch']['SCWords'][0]['correct']?>" />
                        </div>
                        <?php if (!empty($data['iSearch']['SCWords'])): $i=0; ?>
                            <?php foreach ($data['iSearch']['SCWords'] as $_word): ?>
                            <?php if ($i==0) {$i++; continue; }  ?>
                                <div class="wordWrapper wordIsDeletable">
                                <input type="text" class="incorrect" name="iSearch[SCWords][<?php echo $i?>][incorrect]" value="<?php echo $_word['incorrect']?>" /> &raquo; <input type="text" class="correct" name="iSearch[SCWords][<?php echo $i?>][correct]" value="<?php echo $_word['correct']?>" /><a href="javascript:void(0)" class="removeWordButton" style="margin-left:10px">- Remove</a>
                                </div>
                            <?php $i++; endforeach; ?>
                        <?php endif; ?>
                    </div>
                    <div><a href="javascript:void(0)" class="addWordButton">+ Add Word</a></div>
                    
                    <script>
						var autonameWords = function() {
							$('.wordsWrapper .wordWrapper').each(function(i, e) {
								$(this).find('.incorrect').attr('name', 'iSearch[SCWords]['+i+'][incorrect]');
								$(this).find('.correct').attr('name', 'iSearch[SCWords]['+i+'][correct]');
							});
						}
					
                    	$('.removeWordButton').live('click',function() {
							$(this).parent().remove();
							autonameWords();
						});
						
						$('.addWordButton').click(function() {
							var l = $('.wordsWrapper .wordWrapper').length;
							var wordWrapper = '<div class="wordWrapper wordIsDeletable"><input type="text" class="incorrect" name="iSearch[SCWords]['+l+'][incorrect]" value="" /> &raquo; <input type="text" class="correct" name="iSearch[SCWords]['+l+'][correct]" value="" /><a href="javascript:void(0)" class="removeWordButton" style="margin-left:10px">- Remove</a></div>';
							$('.wordsWrapper').append(wordWrapper);
						});
                    </script>
                </td>
              </tr>
              <!--{HOOK_ADD_CACHING_OPTION}-->
            </table>
      	</li>

        <li>
        	<style type="text/css">
			.supportPageFrame {
				width:99%;
				height:600px;	
				padding:0;
				margin:0;
				border:0;
			}
			</style>
			<iframe src="//isenselabs.com/external/supportpages/isearch/index.php" class="supportPageFrame"></iframe>
        </li>
      </ul>
	  <input type="hidden" class="selectedTab" name="selectedTab" value="<?php echo (empty($_GET['tab'])) ? 0 : $_GET['tab'] ?>" />
      </form>
    </div>
  </div>
</div>
<script>
var selectedTab = $('.selectedTab').val();
$('.iModuleAdminSuperMenu li').removeClass('selected').eq(selectedTab).addClass('selected');
$('.iModuleAdminSuperWrappers > li').hide().eq(selectedTab).show();

$('.iModuleAdminMenu li').click(function() {
	$('.iModuleAdminMenu li',$(this).parents('li')).removeClass('selected');
	$(this).addClass('selected');
	$($('.iModuleAdminWrappers li',$(this).parents('li')).hide().get($(this).index())).fadeIn(200);
});

$('.iModuleAdminSuperMenu li').click(function() {
	$('.iModuleAdminSuperMenu > li',$(this).parents('h1')).removeClass('selected');
	$(this).addClass('selected');
	$($('.iModuleAdminSuperWrappers > li',$(this).parents('#content')).hide().get($(this).index())).fadeIn(200);
	$('.selectedTab').val($(this).index());
});
</script>
<?php echo $footer; ?>