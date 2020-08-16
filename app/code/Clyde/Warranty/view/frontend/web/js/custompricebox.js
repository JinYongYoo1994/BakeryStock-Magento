define(
    [
        'jquery',
        'Magento_Catalog/js/price-utils',
        'underscore',
        'mage/template',
        'mage/url',
        /*'mage/priceBox',*/
        'Clyde_Warranty/js/productdetail',
        'Magento_Catalog/js/price-box',
        'jquery/ui',

    ],
    function ($, utils, _, mageTemplate,mageurl, productdetail) {

        'use strict';

        $.widget(
            'mage.priceBox', $.mage.priceBox, {
            /**
             * Render price unit block.
             */
            reloadPrice: function reDrawPrices() 
            {

                var priceFormat = (this.options.priceConfig && this.options.priceConfig.priceFormat) || {},
                    priceTemplate = mageTemplate(this.options.priceTemplate);
                var final_price = 0;
                _.each(
                    this.cache.displayPrices, function (price, priceCode) {
                    price.final = _.reduce(
                        price.adjustments, function(memo, amount) {
                        return memo + amount;
                        }, price.amount
                    );

                    // you can put your custom code here. 
                    //console.log(price);
                    
                    price.formatted = utils.formatPrice(price.final, priceFormat);
                    final_price = price.final;
                    $('[data-price-type="' + priceCode + '"]', this.element).html(priceTemplate({data: price}));
                    }, this
                );

                
                    var count = $('#clyde-warranty-event-count').val();
                    if(parseInt(count) % 2 == 1){
                        productdetail.getProductWarranty(final_price);
                    }

                    $('#clyde-warranty-event-count').val(parseInt(count)+1);
                    

            }

            }
        );

        return $.mage.priceBox;
    }
);