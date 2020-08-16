define(
    ['jquery',
        'Magento_Customer/js/customer-data',
        'Magento_Ui/js/modal/modal'],
    function($ , customerData, modal) {
    'use strict';
    return  {
        warrantyAjaxCall: function (url , params, type) {
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
                //    if (responce.success) {
                        customerData.invalidate(sections);
                        customerData.reload(sections, true);
                 //   }
                },
                error: function (xhr, status, errorThrown) {
                    console.log('Error happens. Try again.');
                }
                }
            );
        },
        warrantyAdd: function (url , params) {
            var data = JSON.parse(params);
             this.warrantyAjaxCall(url , data , 'add');
        },
        warrantyRemove: function (url , params) {
            var data = JSON.parse(params);
             this.warrantyAjaxCall(url , data , 'remove');
        },
        addCoverage: function (params) {
            var data_params = '{"id":"remove_warranty_'+params.itemid+'_'+params.warranty+'", "url":"'+params.remove_url+'", "type":"remove"}';
            $('#popup-add-coverage-button').attr('data-params',data_params);
            $('#popup-add-coverage-button').find('span').html('Remove Coverage');
            this.closeMoalPopupCart();
        },
        removeCoverage: function () {
            var data_params = '';
            $('#popup-add-coverage-button').attr('data-params',data_params);
            $('#popup-add-coverage-button').find('span').html('Add Coverage');
            this.closeMoalPopupCart();
        },
        closeMoalPopupCart: function () {
            $("#popup-mpdal").modal("closeModal");  
        },
        isLodingWarranty: function (action , type) {
          if (type) {
            $('#loading_warranty_'+action.itemid+'_'+action.warranty).css('display','block');
          } else {
            $('#loading_warranty_'+action.itemid+'_'+action.warranty).css('display','none');
          }
        }
    }
    }
);