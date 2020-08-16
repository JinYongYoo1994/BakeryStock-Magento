define(
    [
    'jquery',
    window.widgetUrl
    ],
    function ($,widgetObj) {
    'use strict';
    var clydeObj = null;
    if(typeof Clyde !== 'undefined'){
      clydeObj = Clyde;
    }
    var activeClydeProduct = '';
    var widgetData = window.clydeWidget;
    return  {
        clydeObj: clydeObj,
        clydeOptions: {},
        activeSku: '',
        widgetData: widgetData,
        warranty:'',
        firstItem:'',
        initWidget: function (options, sku) {
          this.clydeOptions = options;
          this.clydeOptions._onShowModal = this._onShowModal;
          this.clydeOptions._onCloseModal = this._onCloseModal;
          this.clydeOptions._onSelectedContract = this._onSelectedContract;
          activeClydeProduct = this.activeSku = sku;
          window.widgetObj = this;
          this.connectClydeWidget();
        },
        initWidgetCart: function (callbck) {
          this.clydeOptions = this.widgetData;
          this.clydeOptions._onShowModal = this._onShowModalCart;
          this.clydeOptions._onCloseModal = this._onCloseModalCart;
          this.clydeOptions._onSelectedContract = this._onSelectedContractCart;
          //activeClydeProduct = sku;
          window.widgetObj = this;
          this.connectClydeWidgetCart(callbck);
        },

        contractListProduct: function (skus) {
            this.clydeObj.loadContractsForProductList(skus);
        },

        appendToSelector: function (selector, type, shouldHide) {
            this.clydeObj.appendToSelector(selector, type, shouldHide);
        },

        appendCartPrompt: function (productSku, selector, modalCloseCb) {
            this.clydeObj.appendCartPrompt(productSku, selector, modalCloseCb);
        },

        appendToSelectorWithSku: function (productSku, selector, modalCloseCb) {
            this.clydeObj.appendToSelectorWithSku(productSku, selector, modalCloseCb);
        },

        getSettings: function () {
            return this.clydeObj.getSettings();
        },

        checkReady: function () {
            if(this.clydeObj !== null){
              return this.clydeObj.checkReady();
            }else{
              return false;
            }
        },

        getPage: function () {
          var self = window.widgetObj;
          if(typeof self.clydeOptions.page != 'undefined' && self.clydeOptions.page !== null){
            return self.clydeOptions.page;
          }else{
            return false;
          }
        },

        setDymanicFunction: function (callbck) {
          window.dynamicFunction = {'callbck':callbck,'addurl':this.clydeOptions.addurl};
        },


        showModalCallback: function (openCallback, closeCallback) {
          window.dynamicFunction = {'callbck':closeCallback,'addurl':this.clydeOptions.addurl};
          var settings = Clyde.getSettings();
          if(settings.modal === true){
            this.clydeObj.showModal(openCallback, closeCallback);
          }

        },
        getSelectedContract: function(){
          return this.clydeObj.getSelectedContract();
        },
        setClydeProduct: function () {
          if(activeClydeProduct != null){
            Clyde.setActiveProduct(encodeURIComponent(activeClydeProduct));
          }
        },
        setClydeProductCart: function () {
          Clyde.setActiveProduct();
        },

        setClydeActiveProduct: function (activeClydeProduct, callBack) {
          this.clydeObj.setActiveProduct(encodeURIComponent(activeClydeProduct, callBack));
        },

        getActiveProduct: function () {
          return Clyde.getActiveProduct();
        },

        connectClydeWidget: function () {
          var options = this.getClydeOptionData();
          var callBack = this.getClydeCallback();
          this.clydeObj.init(options,this.setClydeProduct);
        },


        connectClydeWidgetCart: function (callbck) {
          var options = this.getClydeOptionData();
          var callBack = this.getClydeCallback();
          this.clydeObj.init(options, callbck);
        },

        _skuList: function (obj) {

        },

        loadContractsForProduct: function (product) {
          this.clydeObj.loadContractsForProduct(product);

        },
        loadContractsForProductList: function (product) {
          this.clydeObj.loadContractsForProductList(product);

        },

        changeProductShowmodal: function (product,callbck) {
          this.clydeObj.loadContractsForProduct(product);
          this.clydeObj.setActiveProduct(product);
          this.showModalCallback(callbck);
        },

        checkShowModel: function (obj) {
        },

        _onShowModal: function (obj) {
          var self = window.widgetObj;
          if(self.getPage() !== false){
                self._onShowModalList(obj);
                return false;
            }

          //console.log('onShowModal',obj);
        },

        getSelectedContractbySKU: function (contact, activeProduct) {
          var selectedTurm = '';
          //console.log(contact);
          if (typeof contact !== 'undefined' && contact !== null ) {
            $.each(
                activeProduct.contracts, function( key, value ) {
                if(contact.sku == value.attributes.sku){
                selectedTurm = value.attributes.term;
                }
                }
            );
          }

          return selectedTurm;
        },

        _onCloseModal: function () {
          var self = window.widgetObj;
          //console.log('_onCloseModal');
          var contract = Clyde.getSelectedContract();
          var activeProduct = Clyde.getActiveProduct();
          var selectedContract = self.getSelectedContractbySKU(contract, activeProduct);
          if(self.getPage() !== false){
                self._onCloseModalList();
                return true;
            }

          if(contract === null){
            $("#selected_rule_id").val('');
            $("#warranty").val('');
            $("#contract_detail").val('');
          }else{
            contract.term = selectedContract;
            $("#selected_rule_id").val(1);
            $("#warranty").val(contract.sku);
            $("#contract_detail").val(JSON.stringify(contract));
          }

          return true;
        },

        _onSelectedContract: function (obj) {
           var self = window.widgetObj;
           if(self.getPage() !== false){
                self._onSelectedContractList(obj);
                return false;
            }

          var contact = Clyde.getSelectedContract();
          var activeProduct = Clyde.getActiveProduct();
          var selectedContact = self.getSelectedContractbySKU(contact, activeProduct);
          var contact = obj;
          contact.term = selectedContact;
          $("#selected_rule_id").val(1);
          $("#warranty").val(contact.sku);
          $("#contract_detail").val(JSON.stringify(contact));
        },

        _onShowModalCart: function (obj) {
          /*var activeProduct = Clyde.getActiveProduct();
          var element = $('.xulumus-product-warranty-dropdown-'+activeProduct.sku).attr('data-params');
          var data = JSON.parse(element);
          window['openModal'+data.itemid]();*/

        },

        _onCloseModalCart: function (obj) {
          //console.log(obj);
          var contact = Clyde.getSelectedContract();
          var obj = window.widgetObj;
          var element = window.clydeWidget.selectedElement.attr('data-params');
          var data = JSON.parse(element);
          data.contract_detail = '';
          if(contact !== null){
            data.warranty = contact.sku;
            data.contract_detail = contact;
            data.update_type = 'add';
              obj.addWarranty(data.addurl, JSON.stringify(data));
          }else{
              data.contract_detail = {sku:"",recommendedPrice:""};
              data.update_type = 'remove';
            obj.removeWarranty(data.removeurl, JSON.stringify(data));
          }

        },

        addWarranty: function (url , param) {
          var warranty = window.clydeWidget.warranty;
          warranty.warrantyAdd(url , param);
        },
        removeWarranty: function (url , param) {
          var warranty = window.clydeWidget.warranty;
          warranty.warrantyRemove(url , param);
        },
        _onSelectedContractCart: function (obj) {

        },

        _onShowModalList: function (obj) {
          //console.log('_onShowModalList',obj);
        },

        _onCloseModalList: function () {
          var self = window.widgetObj;
          //console.log('_onCloseModalList');
          var contact = Clyde.getSelectedContract();
          var activeSku = Clyde.getActiveProduct();
            $('[data-warranty-contract="warranty"]').val('');
            $('[data-ruleid-contract="selected_rule_id"]').val('');
            $('[data-contractdetail-contract="contract_detail"]').val('');
          var selectedContact = self.getSelectedContractbySKU(contact, activeSku);

          if(contact !== null){
            contact.term = selectedContact;
            $("#selected_rule_id_"+activeSku.sku).val(1);
            $("#warranty_"+activeSku.sku).val(contact.sku);
            $("#contract_detail_"+activeSku.sku).val(JSON.stringify(contact));
          }

          return true;
        },

        _onSelectedContractList: function (obj) {
          var contact = obj;
          var activeSku = Clyde.getActiveProduct();
          //console.log('_onSelectedContractList',activeSku.sku);
          //console.log('elementCheck',$("#selected_rule_id_"+activeSku));
          $("#selected_rule_id_"+activeSku.sku).val(1);
          $("#warranty_"+activeSku.sku).val(contact.sku);
          $("#contract_detail_"+activeSku.sku).val(JSON.stringify(contact));
        },

        getFormElement: function () {
           var elementSku = [];
           $("form").each(
               function() {
                var self = this;
                var productId = $(self).find('input[name="product"]').val();
                if (typeof productId !== 'undefined') {
                  var productOptions =  $("[data-id='clide-widget-" + productId + "']").attr('widget-data');
                  var arrayValue = JSON.parse(productOptions);
                  if(typeof arrayValue.index !== 'undefined'){
                    $.each(
                        arrayValue.index, function( key, value ) {
                        var warranty = '<input type="hidden" data-warranty-contract="warranty" name="warranty['+value.sku+']" value="" id="warranty_'+value.sku+'" />';
                        var rule = '<input type="hidden" data-ruleid-contract="selected_rule_id" name="selected_rule_id['+value.sku+']" value="0" id="selected_rule_id_'+value.sku+'" />';
                        var detail = '<input type="hidden" data-contractdetail-contract="contract_detail" name="contract_detail['+value.sku+']" value="" id="contract_detail_'+value.sku+'" />';
                        $(self).append(warranty+rule+detail);
                        elementSku.push(value.sku);
                        }
                    );
                  }else if(typeof arrayValue.sku !== 'undefined'){
                    var warranty = '<input type="hidden" data-warranty-contract="warranty" name="warranty['+arrayValue.sku+']" value="" id="warranty_'+arrayValue.sku+'" />';
                    var rule = '<input type="hidden" data-ruleid-contract="selected_rule_id" name="selected_rule_id['+arrayValue.sku+']" value="0" id="selected_rule_id_'+arrayValue.sku+'" />';
                    var detail = '<input type="hidden" data-contractdetail-contract="contract_detail" name="contract_detail['+arrayValue.sku+']" value="" id="contract_detail_'+arrayValue.sku+'" />';
                        $(self).append(warranty+rule+detail);
                   // self.firstItem = sku;
                   elementSku.push(arrayValue.sku);
                  }
                }
               }
           );
             return elementSku;
        },

        getClydeOptionData: function () {

          var options = {'key':this.clydeOptions.key,
                        'environment':this.clydeOptions.environment,
                        'defaultSelector':this.clydeOptions.defaultSelector,
                        'skuList': this.clydeOptions.skuList,
                        'pageKey': this.clydeOptions.pageKey,
                        'onShowModal':this.clydeOptions._onShowModal,
                        'onCloseModal':this.clydeOptions._onCloseModal,
                        'onSelectedContract': this.clydeOptions._onSelectedContract,
          };
          return options;
        },

        getClydeCallback: function () {
          var options = {'onShowModal':this._onShowModal,'onCloseModal':this._onCloseModal,'onSelectedContract': this._onSelectedContract};
          return options;
        }
    }
    }
);
