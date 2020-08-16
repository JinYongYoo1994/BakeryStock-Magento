define(
    [
        'jquery',
        'mage/translate',
        'jquery/ui',
        'Magento_Catalog/js/catalog-add-to-cart', // Important, require the original!
        'Magento_Ui/js/modal/modal',
        'mage/url',
        'Clyde_Warranty/js/widget'
    ], function ($, $t, jUi, addToCart, modal, mageurl, clydeWarranty) {
        'use strict';
        $.widget(
           'Clyde_Warranty.catalogAddToCart', $.mage.catalogAddToCart, {
                clydeWarranty: clydeWarranty,
                submitForm: function (form) {
                    var checkReady = this.clydeWarranty.checkReady();
                    //todo fix issuw with modal set to true, and product off -- does not show modal
                    if(checkReady === true ){
                        window.ajaxCartForm = form;
                        window.clydeCustomClass = this;
                        var settings = Clyde.getSettings();
                        var pageType = clydeWarranty.clydeOptions.pageKey;

                        if(pageType == 'catalog' && settings.catalog) {
                            var sku = this.getProductSku(form);
                            if (sku != false) {
                                window.Clyde.setActiveProduct(sku, this.activeProductCallback);
                            } else {
                                window.Clyde.setActiveProduct(form.attr('data-product-sku'), this.activeProductCallback);
                            }
                        }else if( settings.modal === true && pageType == 'productPage'){
                            var selectedContract = Clyde.getSelectedContract();
                            var activeProduct = window.Clyde.getActiveProduct();
                            if(selectedContract == null && activeProduct.contracts.length > 0){
                                window.Clyde.showModal(null, this.ajaxSubmitOriginal);
                            }else{
                                this.ajaxSubmit(form);
                            }
                        }else{
                            this.ajaxSubmit(form);
                        }
                    }else{
                        this.ajaxSubmit(form);
                    }


                },
                activeProductCallback : function () {
                    var self = window.clydeCustomClass;
                    var activeProduct = window.Clyde.getActiveProduct();
                    var form = window.ajaxCartForm;
                    if(activeProduct.contracts.length > 0){
                        window.Clyde.showModal(null, self.ajaxSubmitOriginal);
                    }else{
                        self.ajaxSubmit(form);
                    }


                },
                ajaxSubmitOriginal: function () {
                    var form = window.ajaxCartForm;
                    var self = window.clydeCustomClass;
                    var value = self.clydeWarranty._onCloseModal();
                    if(value === true){
                        self.ajaxSubmit(form);
                    }

                },

                getFormSelectedValue: function (form) {

                    var fromData = form.serializeArray();
                    var superAttribute = {};
                    $.each(
                        fromData, function( key, value ) {
                            if(typeof value.name != 'undefined'){
                                var name = value.name;
                                if(name.indexOf("super_attribute") != -1){
                                    superAttribute[value.name] = value.value;
                                }
                            }

                        }
                    );
                    return superAttribute;
                },

                compareOptionsa: function (optionsValue, options) {
                    var data = {};
                    $.each(
                        options, function( key, value ) {
                            if(key != 'sku'){
                                data['super_attribute['+key+']'] = value;
                            }
                        }
                    );
                    if(this.objectsAreSame(optionsValue, data) === true){
                        return options.sku;
                    }

                    return false;
                },

                objectsAreSame: function (x, y) {
                    var objectsAreSame = true;
                    for(var propertyName in x) {
                        if(x[propertyName] !== y[propertyName]) {
                            objectsAreSame = false;
                            break;
                        }
                    }

                    return objectsAreSame;
                },

                getProductSku: function (form) {
                    var options = this.getProductOptions(form);
                    var fromData = form.serializeArray();
                    var selectedOptions = this.getFormSelectedValue(form);
                    var self = this;
                    //console.log('selectedOptions',selectedOptions);
                    var result = false;
                    if(typeof options.index != 'undefined'){
                        $.each(
                            options.index, function( key, value ) {
                                var sku_value = self.compareOptionsa(selectedOptions,value);
                                if(sku_value != false){
                                    result = sku_value;
                                }
                            }
                        );
                    }

                    return result;
                },

                getProductOptions: function (form) {

                    var fromData = form.serializeArray();
                    var element = '';
                    $.each(
                        fromData, function( key, value ) {
                            if(typeof value.name != 'undefined' && value.name == 'product'){
                                element = $("[data-id='clide-widget-" + value.value + "']").attr('widget-data');
                            }
                        }
                    );
                    return JSON.parse(element);
                }
            }
        )
    }

);
