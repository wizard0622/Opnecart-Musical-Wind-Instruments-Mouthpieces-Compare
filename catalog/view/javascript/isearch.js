$(document).ready(function() {
    $(window).load(function() {
        String.prototype.capitalize = function() {
            return this.charAt(0).toUpperCase() + this.slice(1);
        }

        var searchBoxSelector = 'input[name=filter_name]';
        var searchBoxCategory = 'input[name=category_id]';
        var searchParam = 'filter_name';
        var searchCompatibility = '/onefivefour';
        var searchButtonSelector = '.button-search';
        var descriptionVariable = 'filter_description';

        if (ocVersion >= '1.5.5.1') {
            searchBoxSelector = 'input[name=search]';
            searchBoxCategory = 'input[name=category_id]';
            searchParam = 'search';
            descriptionVariable = 'description';
            searchCompatibility = ''
        }

        var searchBox = $(searchBoxSelector).first();
        var originalSearchBox = searchBox;

        var originalSearchBoxContainer = searchBox.parent().html();
        searchBox.parent().html('<div class="iSearchBoxWrapper">' + originalSearchBoxContainer + '<div class="clearfix"></div><div class="iSearchBox"><ul></ul></div><div id="iSearchBoxLoadingImage"></div></div>');

        var originalSearchBoxOffset = $(originalSearchBox).offset();
        var originalSearchBoxWidth = $(originalSearchBox).innerWidth();
        var originalSearchBoxHeight = $(originalSearchBox).innerHeight();

        $('#iSearchBoxLoadingImage').offset({
            left: (originalSearchBoxOffset.left + originalSearchBoxWidth - 21),
            top: (originalSearchBoxOffset.top + (originalSearchBoxHeight / 2) - ($('#iSearchBoxLoadingImage').innerHeight() / 2))
        });
        $('#iSearchBoxLoadingImage').hide();
        $('#iSearchBoxLoadingImage').css('visibility', 'visible');

        /** Responsive Design logic */
        var respondOnWidthChange = function() {
            var searchFieldWidth = searchBox.width() + 16;
            $('.iSearchBoxWrapper .iSearchBox').width(searchFieldWidth);
        }

        var iSearchAjax = null;

        if (responsiveDesign == 'yes') {
            $(window).resize(function() {
                respondOnWidthChange();
            });
            respondOnWidthChange();
        }
        /** END */

        /** Close iSearch Box on Body Click */
        $(document).click(function() {
            $('.iSearchBox').hide();
        });
        $('.iSearchBoxWrapper').click(function(event) {
            event.stopPropagation();
        });

        /** END */

        /** After Hitting Enter */
        var goIsearching = function() {
            
            url = $('base').attr('href') + 'index.php?route=product/isearch' + searchCompatibility;
            var filter_name = $(searchBoxSelector).first().val();
            
            
            if (filter_name) {
                url += '&' + searchParam + '=' + encodeURIComponent(filter_name);
            }
           
            if (searchInDescription) {
                url += '&' + descriptionVariable + '=true';
            }
            location = url;
        }

        var useIsearchAfterHittingEnter = function() {
            $(searchButtonSelector).unbind('click');
            $(searchButtonSelector).bind('click', function() {
                goIsearching();
            });

            $(searchBoxSelector).first().unbind('keydown');
            $(searchBoxSelector).first().bind('keydown', function(e) {
                if (e.keyCode == 13) {
                    goIsearching();
                }
            });

        }
        if (afterHittingEnter == 'isearchengine1541' || afterHittingEnter == 'isearchengine1551') {
            setTimeout(function() {
                useIsearchAfterHittingEnter();
            }, 1000);
        } else {
            $(searchButtonSelector).bind('click', function() {
               
                var url = $('base').attr('href') + 'index.php?route=product/search';
                var filter_name = $(searchBoxSelector).first().val();
                var cat_name = $(searchBoxCategory).first().val();
                if (filter_name) {
                    url += '&' + searchParam + '=' + encodeURIComponent(filter_name);
                }
                 if (cat_name) {
                url += '&category_id=' + encodeURIComponent(cat_name);
            }
                location = url;
            });

            $(searchBoxSelector).first().bind('keydown', function(e) {
                if (e.keyCode == 13) {
                    url = $('base').attr('href') + 'index.php?route=product/search';

                    var filter_name = $(searchBoxSelector).first().val();

                    if (filter_name) {
                        url += '&' + searchParam + '=' + encodeURIComponent(filter_name);
                    }

                    location = url;
                }
            });
        }

        /** END */

        /** Non-AJAX Loading */
        if (useAJAX == 'no') {
            $.ajax({
                type: 'get',
                url: 'index.php?route=module/isearch/ajaxget',
                contentType: "application/json; charset=utf-8",
                success: function(o) {
                    productsData = generateProductsDataFromJSONResponse(o);
                }
            });
        }

        var sortProductsByKeyword = function(keywords, products) {
            var words = keywords.split(' ');
            var sortedProducts = [];
            $(products).each(function(i, e) {
                productName = (e.name) ? e.name.toString().toLowerCase() : '';
                if (productName.indexOf(words[0].toLowerCase()) != -1) {
                    sortedProducts.unshift(e);
                } else {
                    sortedProducts.push(e);
                }
            });
            return sortedProducts;
        }

        var filterProductsByKeyword = function(keywords, products, strictMode) {
            var words = keywords.split(' ');
            var filteredProducts = [];
            $(products).each(function(i, e) {
                productName = (e.name) ? e.name.toString().toLowerCase() : '';
                productModel = (e.model) ? e.model.toString().toLowerCase() : '';

                var allWordsExist = true;
                if (strictMode == 'yes') {
                    words[0] = keywords;
                }
                $(words).each(function(j, w) {
                    if (productName.indexOf(w.toLowerCase()) == -1) {
                        if (searchInModel == 'no') {
                            allWordsExist = false;
                        } else {
                            if (productModel.indexOf(w.toLowerCase()) == -1) {
                                allWordsExist = false;
                            }
                        }
                    }
                });

                if (allWordsExist == true) {
                    filteredProducts.push(e);
                }


            });
            return filteredProducts;
        }

        /** END */

        var runSpellCheck = function($searchVal) {
            /*if (SCWords) {
             $(SCWords).each(function(i, e) {
             if (e.incorrect == $searchVal) {
             $searchVal = e.correct;
             }
             });
             }*/
            return $searchVal;
        }

        var searchInProducts = function($name, $searchVal) {
            var iname = $name;
            var searchSplit = $searchVal.split(' ');
            var ind = null;
            $(searchSplit).each(function(i, searchWord) {
                ind = i;
                if (iname.toLowerCase().indexOf(searchWord.toLowerCase()) != -1) {
                    var startPos = iname.toLowerCase().indexOf(searchWord.toLowerCase());
                    var extractStr = iname.substr(startPos, searchWord.length);
                    iname = iname.replace(extractStr, '{' + extractStr + '}');
                }
            });
            if (ind != null) {
                iname = iname.replace(/{/g, '<span class="iMarq">');
                iname = iname.replace(/}/g, '</span>');
            }
            return iname;

            var iname = $name;
            if ($name.toLowerCase().indexOf($searchVal.toLowerCase()) != -1) {
                var startPos = $name.toLowerCase().indexOf($searchVal.toLowerCase());
                var extractStr = $name.substr(startPos, $searchVal.length);
                iname = $name.replace(extractStr, '<span class="iMarq">' + extractStr + '</span>');
            }
            return iname;
        }

        var generateLIs = function(searchVal, fromProducts) {
            var LIs = '';
            searchVal = $.trim(searchVal);
            var f = 0;
            var maxFound = iSearchResultsLimit;
            fromProducts = (fromProducts) ? fromProducts : productsData;

            var sortedProducts = fromProducts;
            if (useAJAX == 'no') {
                sortedProducts = sortProductsByKeyword(searchVal, fromProducts);
                sortedProducts = filterProductsByKeyword(searchVal, sortedProducts, useStrictSearch);
            }
            $(sortedProducts).each(function(i, e) {
                e.name = (e.name) ? e.name.toString() : '';
                e.model = (e.model) ? e.model.toString() : '';
                e.special = (e.special) ? e.special : '';
                f++;
                if (f <= maxFound) {
                    var iname = searchInProducts(e.name, searchVal);
                    var imodel = searchInProducts(e.model, searchVal);
                    var specialClass = (e.special) ? 'specialPrice' : '';
                    var imageTag = (loadImagesOnInstantSearch == 'no') ? '' : '<img src="' + e.image + '" />';
                    LIs += '<li onclick="document.location.href=\'' + e.href.replace('\'', '\\\'') + '\'">' + imageTag + '<div class="iSearchItem"><h3>' + iname + '</h3><div class="iSearchPrice"><span class="' + specialClass + '">' + e.price + '</span><div class="iSearchSpecial">' + e.special + '</div></div><div class="iSearchModel">' + imodel + '</div><div class="clearfix"></div></div></li>';
                }

            });

            if (f > maxFound) {
                var viewAllLink = moreResultsText.replace('(N)', f);
                var routeToController = (afterHittingEnter == 'isearchengine1541' || afterHittingEnter == 'isearchengine1551') ? 'product/isearch' + searchCompatibility : 'product/search';

                LIs += '<li class="iSearchViewAllResults" onclick="document.location = \'index.php?route=' + routeToController + '&' + searchParam + '=' + searchVal + '\'">' + viewAllLink + '</li>';
            }

            if (LIs != '') {
                $('.iSearchBox ul').html(LIs);
                $('.iSearchBox').slideDown(70);
            } else {
                $('.iSearchBox').slideUp(50);
            }


        }

        var generateProductsDataFromJSONResponse = function(jsonObject) {
            var prodData = [];
            $(jsonObject).each(function(i, e) {
                var productObj = {
                    'name': e.name,
                    'image': e.image,
                    'href': e.href,
                    'model': e.model,
                    'price': e.price,
                    'special': (e.special) ? e.special : ''
                };
                $.merge(prodData, [productObj]);
            });
            return prodData;
        }

        var showSearchResults = function(val, secondTry) {

            if (val.length > 1) {
                if (useAJAX == 'no') {
                    generateLIs(val);
                } else {
                    var cat_id = '';
                    var name = "cat=";
                    var ca = document.cookie.split(';');
                    for (var i = 0; i < ca.length; i++) {
                        var c = ca[i];
                        while (c.charAt(0) == ' ')
                            c = c.substring(1);
                        if (c.indexOf(name) == 0)
                            var cat_id = c.substring(name.length, c.length);
                    }
                    if (cat_id == '') {
                        var url = 'index.php?route=module/isearch/ajaxget&k=' + val;
                    } else {
                        var url = 'index.php?route=module/isearch/ajaxget&k=' + val +'&cat_id='+cat_id;
                    }
                    iSearchAjax = $.ajax({
                        type: 'get',
                        url: url,
                        contentType: "application/json; charset=utf-8",
                        success: function(o) {
                            var prodData = generateProductsDataFromJSONResponse(o);
                            if (prodData == '' && secondTry != true && runSpellCheck(val) != val) {
                                showSearchResults(runSpellCheck(val), true);
                            }
                            generateLIs(val, prodData);

                        },
                        beforeSend: function(jqXHR, settings) {
                            //$('#iSearchBoxLoadingImage').show();
                        },
                        complete: function(jqXHR, textStatus) {
                            //$('#iSearchBoxLoadingImage').hide();	
                        }

                    });
                }

            } else {
                $('.iSearchBox').slideUp(50);
            }
        }

        var typewatch = (function() {
            var timer = 0;
            return function(callback, ms) {
                clearTimeout(timer);
                timer = setTimeout(callback, ms);
            }
        })();

        $(searchBoxSelector).first().bind('keyup', function(e) {
            if ($(this).is(':focus')) {
                if (iSearchAjax != null)
                    iSearchAjax.abort();
                var baseVal = $(this).val();
                if (useAJAX == 'no') {
                    showSearchResults(baseVal);
                } else {
                    typewatch(function() {
                        showSearchResults(baseVal);
                    }, 300);
                }
            }
        });
    });
});