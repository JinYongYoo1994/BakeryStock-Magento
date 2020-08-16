define(
    [
    'jquery'
    ],
    function ($) {
    'use strict';
    return  {
        warrantyAjaxCall: function (url) {
          var watanty = this;
          var params = {'product_limit':$('#product_limit').val(),'product_limit_page':$('#product_limit_page').val()};
          watanty.isLodingWarranty(params,true);  
          $.ajax(
              {
                url: url,
                type: 'POST',
                dataType: 'json',
                data: params,
              complete: function (response) {             
                var responce = response.responseJSON;
                if(responce.error == 0){
                  if(responce.product_limit){
                    $('#product_limit').val(responce.product_limit)
                  }

                  if(responce.product_limit_page){
                      $('#product_limit_page').val(responce.product_limit_page)
                  }

                  $('#sync-progress-report').css({'width':responce.totalCount+'%'});
                  $('#sync-progress-report').html(responce.totalCount+'%');
                  if(responce.stop == 0){
                    watanty.warrantyAjaxCall(url);
                  }
                }else{
                  var message = '<div id="messages"><div class="messages"><div class="message message-error error"><div data-ui-id="messages-message-error">'+responce.message+'</div></div></div></div>';
                  $("#anchor-content").prepend(message);
                }
                
                
              },
                error: function (xhr, status, errorThrown) {
                    console.log('Error happens. Try again.');
                }
              }
          );
        },
        syncProduct: function (url) {
            
          this.warrantyAjaxCall(url);
        },
        stopSync: function (url , params) {
          var data = JSON.parse(params);
             this.warrantyAjaxCall(url , data);
        },
        isLodingWarranty: function (action , type) {
          if (type) {
            $('#loading_sync_product').css('display','block');
          } else {
            $('#loading_sync_product').css('display','none');
          }
        }
    }
    }
);