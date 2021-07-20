/**
 * Brainy Filter Ultimate 4.7.2, December 3, 2015 / brainyfilter.com
 * Copyright 2014-2015 Giant Leap Lab / www.giantleaplab.com
 * License: Commercial. Reselling of this software or its derivatives is not allowed. You may use this software for one website ONLY including all its subdomains if the top level domain belongs to you and all subdomains are parts of the same OpenCart store.
 * Support: support@giantleaplab.com
 */
var BrainyFilterAdm = {
    settings : null,
    layoutsCnt : 0,
    layouts : null,
    defaultLayout : null,
    refreshActionUrl : '',
    lang : {},
    init : function() {
        this.initTabs(jQuery('#bf-adm-main-menu'));
        this.initTabs(jQuery('#bf-adm-faq .tabs'));
        this.attrValues.init();
        // render form for basic settings
        this.buildForm(this.settings['basic'], 'basic');
        // render form for the last instance
        if (this.layoutsCnt) {
            var id = this.layoutsCnt - 1;
            this.buildForm(this.settings[id], id);
        } else {
            // if there are no layouts exist yet, render for for new layout
            this.layout.addDefault();
        }
        this.fillLayoutSelectbox();
        
        if (this.layoutsCnt === 1) {
            jQuery('#bf-create-instance-alert').show();
        }
        
        jQuery('#add-instance-btn').click(this.layout.add);
        jQuery('#bf-layout-selector').change(this.layout.select);
        jQuery('.bf-filter-title').each(function(){
            jQuery(this).find('.ltabs a').first().addClass('selected');
            jQuery(this).find('.ltabs a').first().click();
        });
    },
    fillLayoutSelectbox : function() {
        var i = 0;
        for (var m in this.settings) {
            var id = m === 'basic' ? m : i;
            if (m !== 'basic') {
                var layoutName = this.lang.filter_name.replace('%s', this.layouts[this.settings[m].layout_id])
                    .replace('%s', this.lang[this.settings[m].layout_position]);
                layoutName = !id ? this.lang.default_layout : layoutName;
                var layoutOpt = jQuery('<option value="'+id+'">' + layoutName + '</option>');
                jQuery('#bf-layout-selector').append(layoutOpt).val(id);
            }
            i ++;
        }

    },
    buildForm : function(settings, id) {
        var t = jQuery('#bf-adm-template').clone();
        t.attr('id', 'bf-admin-template-' + id);
        t.attr('data-layout-id', id);
        
        t.html(t.html().split('{m}').join(id));
        // leave global settings only on the Basic settings tab
        if (id !== 'basic') {
            if (this.settings[id] && this.categoryLayouts.indexOf(this.settings[id].layout_id) !== -1) {
                this._insertCategoryBlock(t);
            }
            t.find('.bf-global-settings').remove();
            this.addDefaultButton(t);
        } else {
            t.find('.bf-local-settings').remove();
        }
        // fill out the clonned form section
        this.fillForm(t, settings, id);
        // if the switcher is not set to any value, this means that the 
        // default option should be selected
        t.find('.bf-switcher').each(function(){
            if (!jQuery(this).find(':checked').size()) {
                BrainyFilterAdm.changeSwitcher(jQuery(this), 'default');
            }
        });
        // add form section into the form
        if (id === 'basic') {
            jQuery('#bf-adm-basic-settings .tab-content-inner').append(t);
            t.show();
        } else {
            jQuery('#bf-adm-instances .tab-content-inner').append(t);
        }
        // init switchers, accordins, etc.
        this.initSwitchers(t, id);
        this.initTabs(t);
        this.order.init(t);
        this.initAdvacedSettings(t);
        this.initSelectAll(t);
        this.initSliders(t);
        this.initGridOption(t);
        this.initIntersection();
        if (id !== 'basic') {
            this.layout.init(t);
            this.layout.preventDisablingOfDefault();
        }
        t.show();
        this.initColorPicker();
        jQuery('.bf-filter-title').each(function(){
            jQuery(this).find('.ltabs a').first().addClass('selected');
            jQuery(this).find('.ltabs a').first().click();
        });
    },
    fillForm : function(form, settingsObj, id) {
        var settings = this._convertToFormNames(settingsObj);
        for (var name in settings) {
            var field = form.find('[name="bf['+id+']'+name+'"]');
            if (field.size()) {
                var type = field.eq(0).attr('type') ? field.eq(0).attr('type').toLowerCase() : '';
                var tag  = field.eq(0).prop('tagName').toLowerCase();
                if (type === 'text' || tag === 'select') {
                    field.val(settings[name]);
                } else if (type === 'radio' || type === 'checkbox') {
                    field.filter('[value="'+settings[name]+'"]').attr('checked', 'checked');
                }
            } else {
//                console.log(name + ' - not found');
            }
        }
    },
    _insertCategoryBlock : function(t) {
        var id = t.data('layout-id');
        var b = jQuery('#bf-category-list-tpl').clone();
            b.removeAttr('id').css({'display' : 'block'});
        b.html(b.html().split('{m}').join(id));
        t.find('.bf-panel-row').append(b);
    },
    _convertToFormNames : function(obj) {
        var out = {};
        if (typeof obj === 'object' || typeof obj === 'array') {
            for (var k in obj) {
                var arr = this._convertToFormNames(obj[k]);
                for (var k2 in arr) {
                    out['['+k+']'+k2] = arr[k2];
                }
            }
        } else {
            out[''] = obj;
        }
        return out;
    },
    initTabs : function(parent) {
        BrainyFilterAdm.selectTab(parent.find('.tab.selected'));
        parent.find('.tab').click(function(){ BrainyFilterAdm.selectTab(jQuery(this)); });
    },
    selectTab : function(tab) {
        tab.parent().find('.tab').removeClass('selected');
        tab.addClass('selected');
        var target = tab.attr('data-target');
        var group  = jQuery(target).attr('data-group');
        var container = jQuery('#bf-adm-main-container');
        container.height(container.height());
        jQuery('.tab-content[data-group=' + group + ']').hide();
        jQuery(target).css({display : 'block', opacity : 0});
        
        jQuery(target).animate({opacity : 1}, 200, function(){
            container.css('height', 'auto');
        });
    },
    initSwitchers : function(parent) {
        parent.find('.bf-switcher').each(function() {
            jQuery(this).find('label[for$="on"]').addClass('bf-switcher-on');
            jQuery(this).find('label[for$="off"]').addClass('bf-switcher-off');
            var sel = jQuery(this).find(':checked');
            jQuery(this).find('label[for="'+sel.attr('id')+'"]').addClass('selected');
            jQuery(this).find('input').css('display', 'none');
            var ch = jQuery(this).children();
            var wrap = jQuery('<div></div>');
            jQuery(this).append(wrap);
            wrap.append(ch);
        });
        parent.find('.bf-switcher input').change(function(){
            jQuery(this).parent().find('.selected').removeClass('selected');
            jQuery(this).parent().find('label[for="'+jQuery(this).attr('id')+'"]').addClass('selected');
        });
    },
    initGridOption : function(parent) {
        parent.find('.bf-opt-control').each(function(){
            var f = function(select) {
                if (select.val() === 'grid' || select.val() === 'slider') {
                    select.closest('tr').find('.bf-opt-mode')
                        .addClass('disabled')
                        .attr('disabled', 'disabled');
                } else {
                    select.closest('tr').find('.bf-opt-mode')
                        .removeClass('disabled')
                        .removeAttr('disabled');
                }
            };
            if (!jQuery(this).is(':disabled')) {
                f(jQuery(this));
            }
            jQuery(this).change(function(){ f(jQuery(this)); });
        });
    },
    addDefaultButton : function(parent) {
        parent.find('.bf-switcher').each(function() {
            if (!jQuery(this).hasClass('yesno')) {
                var radio = jQuery(this).find('input[id$="off"]');
                var id = radio.attr('id').replace(/off$/, '');
                var name = radio.attr('name');
                var input = jQuery('<input type="radio" name="'+name+'" id="'+id+'def" value="default" />');
                if (radio.is('[data-disable-adv]')) {
                    input.attr('data-disable-adv', radio.attr('data-disable-adv'));
                }
                if (radio.is('[disabled]')) {
                    input.attr('disabled', 'disabled');
                }
                if (radio.is('[disabled]')) {
                    input.attr('data-adv-group', radio.attr('data-adv-group'));
                }
                jQuery(this).prepend('<label for="'+id+'def" class="bf-switcher-def">' + BrainyFilterAdm.lang.default + '</label>');
                jQuery(this).prepend(input);
                jQuery(this).find('label[for$="off"]').addClass('middle');
            }
        });
    },
    initSliders : function(parent) {
        parent.find('.bf-th-header').each(function(){
            if(!jQuery(this).hasClass('expanded')){
                jQuery(this).next().hide();
            }
        });
        parent.find('.bf-th-header').click(function(){
            var tbl = jQuery(this).next();
            if (jQuery(this).hasClass('expanded')) {
                jQuery(this).removeClass('expanded');
                tbl.stop().slideUp(200);
            } else {
                jQuery(this).addClass('expanded');
                tbl.stop().slideDown(200);
            }
            
        });
    },
    order : {
        init : function(parent) {
            this.enabled = parent.find('.bf-override-default').size() 
                ? parent.find('.bf-override-default').is(':checked')
                : true;
            this.sort(parent);
            var isEmpty = true;
            parent.find('.bf-sort').each(function(){
                BrainyFilterAdm.order.addArrows(jQuery(this).find('.bf-sort-input'));
                if (parseInt(jQuery(this).find('.bf-sort-input').val()) > 0) {
                    isEmpty = false;
                }
            });
            if (isEmpty) {
                parent.find('.bf-sort-input').each(function(i){
                    jQuery(this).val(i);
                });
            }
            parent.find('.bf-override-default').change(this.enable);
        },
        swap : function(direction, item) {
            var val = parseInt(item.find('.bf-sort-input').val());
            if (direction === 'up') {
                item.prev().find('.bf-sort-input').val(val);
                val --;
                item.prev().insertAfter(item);
            } else {
                item.next().find('.bf-sort-input').val(val);
                val ++;
                item.next().insertBefore(item);
            }
            item.find('.bf-sort-input').val(val);
            // animated hightlight
            item.addClass('bf-sort-highlight');
            setTimeout(function(){ item.removeClass('bf-sort-highlight'); }, 300);
        },
        sort : function(parent) {
            var cont = parent.find('.bf-sort').parent();
            var arr  = parent.find('.bf-sort').toArray();
            var func = function(a, b) {
                var v1 = parseInt(jQuery(a).find('.bf-sort-input').val());
                var v2 = parseInt(jQuery(b).find('.bf-sort-input').val());
                return v2 - v1;
            };
            arr.sort(func);
            jQuery(arr).each(function(){
                cont.find('tr').first().after(jQuery(this));
            });
        },
        addArrows : function(input) {
            input.hide();
            var upBtn = jQuery('<span class="up icon bf-arrow"></span>');
            var dnBtn = jQuery('<span class="down icon bf-arrow"></span>');
            var overrideChk = input.closest('[data-layout-id]').find('.bf-override-default');
            var enabled = overrideChk.size() ? overrideChk.is(':checked') : true;
            if (!enabled) {
                upBtn.attr('data-disabled', '');
                dnBtn.attr('data-disabled', '');
            }
            input.closest('td').append(upBtn, dnBtn);
            upBtn.on('click', this.onArrowClick);
            dnBtn.on('click', this.onArrowClick);
        },
        onArrowClick : function () {
            if (!jQuery(this).is('[data-disabled]')) {
                var direction = jQuery(this).hasClass('up') ? 'up' : 'down';
                BrainyFilterAdm.order.swap(direction, jQuery(this).closest('tr'));
            }
        },
        enable : function() {
            var parent = jQuery(this).closest('table');
            if (jQuery(this).is(':checked')) {
                parent.find('.bf-arrow').removeAttr('data-disabled');
            } else {
                parent.find('.bf-arrow').attr('data-disabled', '');
            }
        }
    },
    initAdvacedSettings : function(parent) {
        var func = function(radio) {
                var adv, group = radio.attr('data-enable-adv');
                if (radio.is(':checked')) {
                    jQuery('[data-adv-group="'+group+'"]').each(function(){
                        if (jQuery(this).attr('type') === 'text') {
                            jQuery(this).attr('readonly', 'readonly');
                        } else if (jQuery(this).prop('tagName') === 'SPAN') {
                            jQuery(this).attr('data-disabled','true');
                        } else {
                            jQuery(this).attr('disabled', 'disabled');
                        }
                    });
                    var $cont = radio.closest('.bf-adv-group-cont');
                    if ($cont.size()) {
                        adv = $cont.find('[data-adv-group]');
                    } else {
                        adv = radio.parents('tr').eq(0).find('[data-adv-group]');
                    }
                    adv.each(function(){
                        if (!jQuery(this).is('.disabled')) {
                            if (jQuery(this).attr('type') === 'text') {
                                jQuery(this).removeAttr('readonly');
                            } else if (jQuery(this).prop('tagName') === 'SPAN') {
                                jQuery(this).removeAttr('data-disabled');
                            } else {
                                jQuery(this).removeAttr('disabled');
                            }
                        }
                    });
                }
        };
        parent.find('[data-enable-adv]').each(function(){
            func(jQuery(this));
            jQuery(this).change(function(){
                func(jQuery(this));
            });
        });
        parent.find('[data-disable-adv]').change(function(){
            var group = jQuery(this).attr('data-disable-adv');
            var adv = jQuery('[data-adv-group="'+group+'"]');
            adv.each(function(){
                if (jQuery(this).attr('type') === 'text') {
                    jQuery(this).attr('readonly', 'readonly');
                } else if (jQuery(this).prop('tagName') === 'SPAN') {
                    jQuery(this).attr('data-disabled', 'true');
                } else {
                    jQuery(this).attr('disabled', 'disabled');
                }
            });
        });
    },
    initIntersection : function() {
        jQuery('.bf-intersect input').on('change', function(){
            var p = jQuery(this).closest('.bf-intersect');
            var val = jQuery(this).val();
            jQuery(this).closest('.bf-intersect-cont').find('.bf-intersect').each(function(){
                if (jQuery(this)[0] !== p[0]) {
                    if (val === '1') {
                        BrainyFilterAdm.changeSwitcher(jQuery(this), '0');
                    }
                }
            });
        });
    },
    initColorPicker : function() {
        jQuery('.color-pick').each(function() {
           jQuery(this).parents("tr").find('.bf-color-pick').css('background-color', '#'+jQuery(this).val());
        });
        jQuery( ".bf-color-pick" ).click(function() {
               
                 jQuery(this).parents("tr").find('.color-pick').click();
               
        });
        jQuery('.color-pick').ColorPicker({
            onSubmit: function(hsb, hex, rgb, el) {
                jQuery(el).val(hex);
                jQuery(el).parents("tr").find('.bf-color-pick').css('background-color', '#'+hex);
                jQuery(el).ColorPickerHide();
                if (jQuery(el).parents("tr").find('.bf-chkbox-def').is(':checked') ) {
                    jQuery(el).parents("tr").find('.bf-chkbox-def').prop( "checked", false );
                    jQuery(el).parents("tr").find('input[type="text"]').removeAttr('readonly');
                };
            },
            onBeforeShow: function () {
                jQuery(this).ColorPickerSetColor(this.value);
            }
            })
            .bind('keyup', function(){
                jQuery(this).ColorPickerSetColor(this.value);
        });
        var func = function() {
            if (jQuery(this).is(':checked')) {
                jQuery(this).closest('tr').find('input[type="text"]').attr('readonly', 'true');
            } else {
                jQuery(this).closest('tr').find('input[type="text"]').removeAttr('readonly');
            }
        };
        jQuery('.bf-chkbox-def').each(func);
        jQuery('.bf-chkbox-def').change(func);
    },
    initSelectAll : function(parent) {
        parent.find('[data-select-all]').click(function(){
            var group = jQuery(this).attr('data-select-all');
            var val   = jQuery(this).attr('data-select-all-val');
            jQuery(this).closest('[data-select-all-group="' + group + '"]').find('.bf-switcher').each(function(){
                BrainyFilterAdm.changeSwitcher(jQuery(this), val);
            });
            return false;
        });
    },
    initFaqFrame : function() {
        var h = jQuery('#bf-adm-faq iframe')[0].contentWindow.document.body.scrollHeight;
        jQuery('#bf-adm-faq iframe').css('height', h + 'px');
    },
    changeDefault: function(val, that) {  
            var color = jQuery('input[name="bf[basic][style]['+val+'][val]"]').val(); 
            jQuery(that).closest('tr').find('input[type="text"]').val(color);
            jQuery(that).parents("tr").find('.bf-color-pick').css('background-color', '#'+color);
    },
    changeSwitcher : function(swtch, val) {
        var inp = swtch.find('input[value=' + val + ']');
        if (!inp.size()) return;
        swtch.find('.selected').removeClass('selected');
        var lbl = swtch.find('label[for="' + inp.attr('id') + '"]');
        lbl.addClass('selected');
        swtch.find('input').removeAttr('checked');
        inp.attr('checked', 'checked').prop('checked', true);
        if (inp.is('[data-disable-adv]')) {
            var adv = swtch.closest('tr').find('input, select')
                .filter('[data-adv-group='+inp.attr('data-disable-adv')+']');
            adv.each(function(){
                if (jQuery(this).attr('type') === 'text') {
                    jQuery(this).attr('readonly', 'readonly');
                } else if (jQuery(this).prop('tagName') === 'SPAN') {
                    jQuery(this).attr('data-disabled', 'true');
                } else {
                    jQuery(this).attr('disabled', 'disabled');
                }
            });    
        }
        if (inp.is('[data-enable-adv]')) {
            var adv = swtch.closest('tr').find('input, select')
                .filter('[data-adv-group='+inp.attr('data-enable-adv')+']');
            adv.each(function(){
                if (!jQuery(this).is('.disabled')) {
                    if (jQuery(this).attr('type') === 'text') {
                        jQuery(this).removeAttr('readonly');
                    } else if (jQuery(this).prop('tagName') === 'SPAN') {
                        jQuery(this).removeAttr('data-disabled');
                    } else {
                        jQuery(this).removeAttr('disabled');
                    }
                }
            });
        }
    },
    layout : {
        init : function(parent) {
            parent.find('.bf-layout-select').change(function(){
                BrainyFilterAdm.layout.updateName();
                var lid = jQuery(this).val();
                if (BrainyFilterAdm.categoryLayouts.indexOf(lid) !== -1) {
                    BrainyFilterAdm._insertCategoryBlock(parent);
                } else {
                    parent.find('.bf-category-list').remove();
                }
            });
            parent.find('.bf-layout-position').change(this.updateName);
            parent.find('.bf-remove-layout').click(this.remove);
        },
        add : function () {
            var exists = false;
            jQuery('#bf-adm-instances').find('.bf-layout-select').each(function(){
                if (!parseInt(jQuery(this).val())) {
                    var id = jQuery(this).closest('[data-layout-id]').attr('data-layout-id');
                    if (jQuery('#bf-layout-selector').val() !== id) {
                        jQuery('#bf-layout-selector').val(id);
                        BrainyFilterAdm.layout.select();
                    }
                    exists = true;
                }
            });
            if (exists) {
                return false;
            }
            
            // add item into Layout selectbox
            var id = BrainyFilterAdm.layoutsCnt;
            var layoutName = BrainyFilterAdm.lang.new_instance;
            var layoutOpt = jQuery('<option value="'+id+'">' + layoutName + '</option>');
            jQuery('#bf-layout-selector').append(layoutOpt).val(id);
            
            BrainyFilterAdm.layout.hideNotice();
            BrainyFilterAdm.layout.showNotice(BrainyFilterAdm.lang.notice_new_layout);
            BrainyFilterAdm.layoutsCnt ++;
            // remove current form
            BrainyFilterAdm.layout.destroyForm();
            BrainyFilterAdm.buildForm(BrainyFilterAdm.settings['basic'], id);
            var $layout = jQuery('#bf-admin-template-' + id);
            $layout.find('.bf-filter-title-inputs').find('input').val('');
            // reset all the settings for the new layout to default
            $layout.find('.bf-switcher').each(function(){
                BrainyFilterAdm.changeSwitcher(jQuery(this), 'default');
            });
            $layout.find('.bf-chkbox-def').attr('checked', 'checked');
            $layout.find('.bf-default').trigger('click');
            jQuery('#bf-create-instance-alert').hide();
            return false;
        },
        addDefault : function() {
            this.add();
            var id = BrainyFilterAdm.layoutsCnt - 1;
            var form = jQuery('[data-layout-id='+id+']');
            form.find('.bf-layout-select').val(BrainyFilterAdm.defaultLayout.layout_id);
            form.find('.bf-layout-position').val(BrainyFilterAdm.defaultLayout.position);
            form.find('.bf-layout-sort').val(BrainyFilterAdm.defaultLayout.sort_order);
            BrainyFilterAdm.changeSwitcher(form.find('.bf-layout-enable'), '1');
            var str = BrainyFilterAdm.lang.default_layout;
            jQuery('#bf-layout-selector [value='+id+']').text(str);
        },
        select : function() {
            var container = jQuery('#bf-adm-main-container');
            container.height(container.height());
            var id = jQuery('#bf-layout-selector').val();
            // render form if it is not exists
            if (!jQuery('#bf-admin-template-' + id).size()) {
                // remove current form
                BrainyFilterAdm.layout.destroyForm();
                BrainyFilterAdm.buildForm(BrainyFilterAdm.settings[id], id);
            }
            jQuery('#bf-adm-instances .tab-content-inner').children().hide();
            jQuery('#bf-admin-template-' + id).css({display : 'block', opacity : 0});
            container.css('height', 'auto');
            jQuery('#bf-admin-template-' + id).animate({opacity : 1}, 200);
            BrainyFilterAdm.layout.hideNotice();
            if (id === '0') {
                BrainyFilterAdm.layout.showNotice(BrainyFilterAdm.lang.error_cant_remove_default);
            }
            jQuery('.bf-filter-title').each(function(){
                jQuery(this).find('.ltabs a').first().addClass('selected');
                jQuery(this).find('.ltabs a').first().click();
            });
        },
        destroyForm : function() {
            jQuery('[data-layout-id]').each(function(){
                var id = jQuery(this).attr('data-layout-id');
                if (id.match(/\d+/)) {
                    jQuery(this).find('input[disabled], select[disabled]').removeAttr('disabled');
                    jQuery(this).find('[name=bf_layout]').attr('disabled','disabled');
                    var obj = BrainyFilterAdm._convertFormToObj(jQuery(this).wrap('<form>').parent());
                    for (var l in obj) {
                        BrainyFilterAdm.settings[l] = obj[l];
                    }
                    jQuery(this).parent().remove();
                }
            });
        },
        updateName : function() {
            var id = jQuery('#bf-layout-selector').val();
            var form = jQuery('[data-layout-id='+id+']');
            var layoutField = form.find('.bf-layout-select').not(':disabled');
            if (!layoutField.size() || !parseInt(layoutField.val())) {
                return;
            }
            var name = form.find('.bf-layout-select option:selected').text();
            var pos  = form.find('.bf-layout-position option:selected').text();
            var str = BrainyFilterAdm.lang.filter_name.replace('%s', name)
                    .replace('%s', pos);
            jQuery('#bf-layout-selector [value='+id+']').text(str);
        },
        remove : function() {
            if (window.confirm(BrainyFilterAdm.lang.confirm_remove_layout)) {
                var form = jQuery('[data-layout-id]:visible');
                var id = form.attr('data-layout-id');
                jQuery('#bf-layout-selector [value='+id+']').remove();
                BrainyFilterAdm.layout.select();
                form.remove();
                delete BrainyFilterAdm.settings[id];
            }
            return false;
        },
        preventDisablingOfDefault : function() {
            var defLayout = jQuery('[data-layout-id=0]');
            defLayout.find('.bf-remove-layout')
                    .unbind('click')
                    .addClass('disabled');
            defLayout.find('.bf-layout-enable label')
                    .addClass('disabled')
                    .click(function(e){
                        e.preventDefault();
                    });
            defLayout.find('.bf-layout-select').attr('disabled', 'disabled');
        },
        showNotice : function(msg) {
            jQuery('#bf-layout-selector')
                    .closest('.bf-panel-row')
                    .find('.bf-notice')
                    .text(msg)
                    .show();
        },
        hideNotice : function() {
            jQuery('#bf-layout-selector')
                    .closest('.bf-panel-row')
                    .find('.bf-notice').hide();
        }
    },
    submitForm : function() {
        if (BrainyFilterAdm.validateForm()) {
            jQuery('#bf-form input[disabled], #bf-form select[disabled]').removeAttr('disabled');
            jQuery('[name=bf_layout]').attr('disabled','disabled');
            var obj = BrainyFilterAdm._convertFormToObj(jQuery('#bf-form'));
            for (var i in obj) {
                BrainyFilterAdm.settings[i] = obj[i];
            }
            for (var i in BrainyFilterAdm.settings) {
                if (i !== 'basic') {
                    var set = BrainyFilterAdm.settings[i];
                    set.behaviour.sort_order.enabled = set.behaviour.sort_order.enabled === '0' ? 'default' : '1';
                    if (set.submission.submit_type === 'default') {
                        delete(set.submission.submit_button_type);
                        delete(set.submission.submit_delay_time);
                    }
                }
            }
            jQuery('#form [name=bf]').val(JSON.stringify(BrainyFilterAdm.settings));
            jQuery('#form').submit();
        }
    },
    validateForm : function() {
        var success = true;
        jQuery('.warning, .success').remove();
        jQuery('#bf-adm-main-container .bf-layout-select').each(function(){
            if (!parseInt(jQuery(this).val())) {
                success = false;
                jQuery('.breadcrumb').after('<div class="warning bf-validation-msg">'+BrainyFilterAdm.lang.error_layout_not_set+'</div>');
                if (jQuery(this).closest('[data-layout-id]').not(':visible')) {
                    BrainyFilterAdm.selectTab(jQuery('[data-target="#bf-adm-instances"]'));
                    var id = jQuery(this).closest('[data-layout-id]').attr('data-layout-id');
                    jQuery('#bf-layout-selector').val(id);
                }
            }
        });
        
        return success;
    },
    _convertFormToObj : function(cont) {
        var obj = {};
        var func = function(obj, arr, val) {
            var f = arr.shift();
            if (typeof obj[f] === 'undefined') {
                obj[f] = {};
            }
            if (arr.length) {
                return func(obj[f], arr, val);
            } else {
                obj[f] = val;
            }
        };
        // replace each checkbox with hidden field in order to don't lose unticked items
        cont.find('input[type=checkbox]').each(function(){
            var hidden = jQuery('<input type="hidden" />');
            hidden.attr('name', jQuery(this).attr('name'));
            hidden.val(jQuery(this).is(':checked') ? '1' : '0');
            jQuery(this).replaceWith(hidden);
        });
        jQuery(cont.serializeArray()).each(function(i, v){
            var arr = this.name.replace(/(^[^\[]+\[)|(\]$)/g, '').split('][');
            func(obj, arr, this.value);
        });
        return obj;
    },
    
    refreshDB : function() {
        var btn = jQuery('#bf-refresh-db');
        if (!btn.hasClass('wait')) {
            btn.addClass('wait');
            var url = BrainyFilterAdm.refreshActionUrl.replace('&amp;', '&');
            var lbl = btn.find('.lbl').text();
            btn.find('.lbl').text(BrainyFilterAdm.lang.updating);
            jQuery.ajax({
                url : url,
                success : function() {
                    btn.removeClass('wait');
                    btn.find('.lbl').text(lbl);
                }
            });
        }
    },
    
    attrValues : {
        init : function() {
            var popup = jQuery('#bf-attr-value-popup');
            popup.find('.htabs a').first().addClass('selected');
            BrainyFilterAdm.initTabs(popup);
            popup.find('.bf-auto-sort').on('click', function(){
                var $btn = jQuery(this), type = $btn.data('type'), 
                    direction, lbl;
                if ($btn.hasClass('bf-desc')) {
                    direction = 'desc';
                    $btn.text(type === 'number' ? '0..9' : 'A..Z');
                } else {
                    direction = 'asc';
                    $btn.text(type === 'number' ? '9..0' : 'Z..A');
                }
                $btn.toggleClass('bf-desc');
                popup.find('.bf-auto-sort').removeClass('bf-active');
                $btn.addClass('bf-active');
                BrainyFilterAdm.attrValues.sort(type, direction);
            });
        },
        openPopup : function(attrId) {
            jQuery.ajax({
                url : BrainyFilterAdm.attrValActionUrl.replace('&amp;', '&'),
                data : {attr_id : attrId},
                dataType : 'json',
                success : function(json) {
                    var popup = jQuery('#bf-attr-value-popup');
                    if (json && !json.error) {
                        for (var lang in json) {
                            var tbl = popup.find('#tab-language-' + lang).find('table tbody');
                            tbl.children().each(function(i){
                                if (i) {
                                    jQuery(this).remove();
                                }
                            });
                            for (var i in json[lang]) {
                                var tr = '<tr class="bf-sort">'
                                       + '<td class="bf-adm-label-td"><span class="bf-wrapper">' + json[lang][i].value + '</span></td>'
                                       + '<td class="center"><input type="text" class="bf-sort-input" name="sort_order[' 
                                       + json[lang][i].attribute_value_id + ']" value="' + json[lang][i].sort_order + '" /></td>'
                                       + '</tr>';
                                tbl.append(tr);
                            }
                            BrainyFilterAdm.order.init(tbl);
                        }
                    }
                    popup.wrap('<div class="bf-adm-shadow"></div>');
                    popup.show();
                    jQuery('body').css('overflow', 'hidden');
                    
                }
            });
        },
        save : function() {
            var form = jQuery('#bf-attr-value-popup').find('form');
            jQuery.ajax({
                type : 'post',
                url : BrainyFilterAdm.attrValActionUrl.replace('&amp;', '&'),
                data : form.serialize(),
                success : function() {
                    
                }
            });
            this.closePopup();
        },
        closePopup : function() {
            jQuery('#bf-attr-value-popup').unwrap().hide();
            jQuery('body').css('overflow', 'auto');
        },
        sort : function(type, direction) {
            var $popup = jQuery('#bf-attr-value-popup'),
                $table = $popup.find('.tab-content:visible .bf-adm-table'),
                values = [],
                sortAsc = function(a, b){
                    return a.val < b.val ? -1 : 1;
                },
                sortDesc = function(a, b){
                    return a.val > b.val ? -1 : 1;
                };
            
            $table.find('.bf-sort').each(function(){
                var $row = jQuery(this), 
                    $lbl = $row.find('.bf-adm-label-td .bf-wrapper'),
                    val = (type === 'number') 
                        ? parseFloat($lbl.text().replace(/(^[\d\.]+)(.*)/, '$1'))
                        : $lbl.text() + ' ';
                    if (type === 'number' && isNaN(val)) val = 1e10;
                values.push({val: val, $: $row});
            });
            
            values.sort(direction === 'desc' ? sortDesc : sortAsc);
            
            jQuery.each(values, function(i, v){
                var $tbody = $table.find('tbody');
                ($tbody.size()) ? $tbody.append(v.$) : $table.append(v.$);
                v.$.find('input').attr('value', i + 1);
            });
        }
    }

};
