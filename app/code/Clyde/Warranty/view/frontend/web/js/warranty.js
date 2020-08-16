define(
    [
    'jquery',
    'Magento_Checkout/js/model/resource-url-manager',
    'Magento_Checkout/js/model/quote',
    'mage/storage',
    'Magento_Checkout/js/model/totals',
    'Magento_Customer/js/customer-data'
    ],
    function ($ , resourceUrlManager, quote, storage ,totals, customerData) {
    'use strict';
    return  {
        warrantyAjaxCall: function (url , params) {
          totals.isLoading(true);  
          var watanty = this;
          watanty.isLodingWarranty(params,true);  
          $.ajax(
              {
                url: url,
                type: 'POST',
                dataType: 'json',
                data: params,
              complete: function (response) {             
                var responce = response.responseJSON;
                var sections = ['cart'];
                if(responce.cart){
                    //$('#warranty_action_'+params.itemid).html(responce.html);
                    var formChange = $('#form-validate');
                    formChange.replaceWith(responce.cart);
                    storage.get(resourceUrlManager.getUrlForCartTotals(quote), false)
                   .done(
                        function (response) {
                           quote.setTotals(response);    
                           totals.isLoading(false);
                        customerData.invalidate(sections);
                        customerData.reload(sections, true);
                        }
                    )
                   .fail(
                        function (response) {
                          totals.isLoading(false);
                          customerData.invalidate(sections);
                            customerData.reload(sections, true);
                        watanty.isLodingWarranty(params,false);  
                        }
                    );
                }

                 watanty.closeMoalPopupCart();
              },
                error: function (xhr, status, errorThrown) {
                    console.log('Error happens. Try again.');
                    watanty.closeMoalPopupCart();
                }
              }
          );
        },
        warrantyAdd: function (url , params) {
            var data = JSON.parse(params);
             this.warrantyAjaxCall(url , data);
        },
        warrantyRemove: function (url , params) {
          var data = JSON.parse(params);
             this.warrantyAjaxCall(url , data);
        },
        closeMoalPopupCart: function () {
          $("#popup-plan-detail-popup-cart").modal("closeModal");
          $('#popup-plan-detail-popup-cart').remove();  
        },
        isLodingWarranty: function (action , type) {
          if (type) {
            $('#popup-plan-detail-popup-cart').find('#loading_warranty_'+action.itemid+'_'+action.warranty).css('display','block');
          } else {
            $('#popup-plan-detail-popup-cart').find('#loading_warranty_'+action.itemid+'_'+action.warranty).css('display','none');
          }
        }
    }
    }
);