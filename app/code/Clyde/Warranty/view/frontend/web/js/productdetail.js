define(
    [
    'jquery',
    'Magento_Customer/js/customer-data',
    'Clyde_Warranty/js/widget'
    ],
    function ($, customerData, clydeWidget) {
    'use strict';
    return  {
        selectPlanInProduct: function (param) {

          if(param.product_id){
            var self = this,
            cartData = customerData.get('cart');
                 cartData.subscribe(
                     function (updatedCart) {
                     self.getCartItem(updatedCart.items, param);
                     }, this
                 );
          }
        },
        getCartItem: function (cartItem, param) {
          
          var self = this;
            _.each(
                cartItem, function (value, key) {
                if(value.warranty_info != '' && param.id == value.item_id){
                  self.updateItemValue(value.warranty_info, param);
                }
                }, this
            );
        },
        updateItemValue: function (item, param) {
            item = JSON.parse(item);
            $('#selected_rule_id').val(item.rule_id);
            $('#warranty').val(item.plan_id);
            $("#pdp-simple").addClass('added');
            $("#pdp-simple").find('.addtext').html('Add product protection');
            $("#pdp-simple").find('.pricewrapper').html(item.customer_cost);
            
        },

        getProductWarranty: function (added) {
            var obj = this;
            var checkReady = clydeWidget.checkReady();
            if(checkReady === true){
              this.clearSelectedValue();
              var form = $('#product_addtocart_form');
              obj.isLodingWarranty(true);
              var sku = this.getProductSku(form); 
              if(sku != false){
                  clydeWidget.setClydeActiveProduct(sku);
              }

             obj.isLodingWarranty(false);
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
                element = $(form).find("[data-id='clide-widget-" + value.value + "']").attr('widget-data'); 
                }
                }
            );
            return JSON.parse(element);
        },

        clearSelectedValue: function () {
            $("#selected_rule_id").val('');
            $("#warranty").val('');
            $("#contract_detail").val('');
        },

        isLodingWarranty: function (type) {
            if(type){
              $('#loading_warranty_product_view').css('display','block');
              $('#xulumus-product-warranty-dropdown').css('display','none');
            }else{
              $('#loading_warranty_product_view').css('display','none');
              $('#xulumus-product-warranty-dropdown').css('display','block');
            }
        }
    }
    }
);