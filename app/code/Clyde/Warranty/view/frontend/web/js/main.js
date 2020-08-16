function addWarranty(obj,url)
{
  require(
      ['jquery', 'Clyde_Warranty/js/warrantyall'], function ($, script) {
      var data_params = $('#'+obj).attr('data-params');
      script.warrantyAdd(url, data_params)
      }
  );
}

function removeWarranty(obj,url)
{
  require(
      ['jquery', 'Clyde_Warranty/js/warrantyall'], function ($, script) {
      var data_params = $('#'+obj).attr('data-params');
      script.warrantyRemove(url, data_params)
      }
  );
}

function selecteCoverage(obj,url)
{
  require(
      ['jquery'], function ($) {
      var data_params = '{"id":"'+$(obj).attr('id')+'", "url":"'+url+'", "type":"add"}';
      var hasClass = $(obj).hasClass("selected");
      $(".warranty-popup-plans .rounded-list.clyde-popup .items").each(
          function( index ) {
          var eatchasClass = $(this).hasClass("selected")
          if($(this).attr('data-plan') !== $(obj).attr('data-plan') && eatchasClass !== false){
          $(this).removeClass('selected');
          }
          }
      );
      if(hasClass !== true){
        $(obj).addClass('selected');
        $('#popup-add-coverage-button').attr('data-params',data_params);
      }else{
        $(obj).removeClass('selected');
        $('#popup-add-coverage-button').attr('data-params','');
      }
      }
  );
}

function coverageMessage(type)
{
  require(
      ['jquery'], function ($) {
      if(type == 'add'){
        var message = $('#coverage-message-element');
        if(message.length > 0){
          $('#coverage-message-element').html('Please select coverage plan');
        }else{
          $('#popup-add-coverage-button').parent().append('<div class="coverage-message" id="coverage-message-element">Please select coverage plan</div>');
        }
      }else if(type == 'remove'){
        $('#coverage-message-element').remove();
      }
      }
  );
}

function addCoverage(obj)
{
  coverageMessage('remove');
  var data_params = document.getElementById(obj).getAttribute('data-params');
  if(data_params != null){
    if(data_params.length > 0){
      var data_params = JSON.parse(data_params);
      if(data_params.type == 'add'){
        addWarranty(data_params.id, data_params.url)
        // closeMoalPopupCart();
      }else if(data_params.type == 'remove'){
        removeWarranty(data_params.id, data_params.url)
      }
    }else{
      coverageMessage('add');
    }
  }else{
    coverageMessage('add');
  }
  
}

function removeCheckoutWarranty(obj,url)
{
  require(
      ['jquery', 'Clyde_Warranty/js/warrantyall'], function ($, script) {
      var element = $(obj);
      var data_params = element.attr('data-params');
      element.html('10');
      element.removeAttr('onclick');
      script.warrantyRemove(url, data_params)
      var timerId = setInterval(
          function () {
          var count = element.html();
          if (parseInt(count) > 0) {
          var count_add = count;
          count_add = parseInt(count_add) - 1;
          element.html(count_add);
          } else {
          clearInterval(timerId);
          var params_data = JSON.parse(data_params);
          $('#warranty-warning-'+params_data.itemid).remove();

          if($('.warranty-details').length == 1){
          $('.warranty-list-title').remove();
          }

          $(obj).parent().parent().remove();
          }
          }, 1000
      );
      }
  );
}
function closeMoalPopupCart()
{
  require(
      ['jquery','Magento_Ui/js/modal/modal'], 
      function ($, modal) {
              $("#popup-mpdal").modal("closeModal");  
      }
  );
}
function setRediectUrl(url)
{
  window.location.href = url;
}

function addWarrantyCart(obj)
{
        require(
            ['jquery', 'Clyde_Warranty/js/warranty'], function ($, script) {
            $(obj).addClass('selected');
            var data_params = $(obj).attr('data-params');
            var urlData = JSON.parse(data_params);
            var url = urlData.url;
            script.warrantyAdd(url, data_params)
            }
        );
}
function removeWarrantycart(obj)
{
  require(
      ['jquery', 'Clyde_Warranty/js/warranty'], function ($, script) {
      var data_params = $(obj).attr('data-params');
      var urlData = JSON.parse(data_params);
      var url = urlData.url;
      script.warrantyRemove(url, data_params)
      }
  );
}

function openContactDetailCart(id)
{
        require(
            ['jquery','Magento_Ui/js/modal/modal'], 
            function ($, modal) {
            var options = {
                    type: 'popup',
                    responsive: true,
                    innerScroll: true,
                    footer: false,
                    innerScrollClass: '_inner-scroll plan-detail-container',
                    buttons: [{
                        text: $.mage.__('Close'),
                        class: 'warranty_plan_modal',
                        click: function () {
                            this.closeModal();
                        }
                    }],
                  };
                  var html = $('#warranty-plan-detail-rule-'+id).html();
                  $('#popup-plan-detail-popup-cart').remove();
                  $('body').append('<div id="popup-plan-detail-popup-cart" class="warranty-plan-detail-rule" style="display:none;">'+html+'</div>');
                  var popup = modal(options, $('#popup-plan-detail-popup-cart'));
                  $("#popup-plan-detail-popup-cart").modal("openModal");
            
            }
        );
      }