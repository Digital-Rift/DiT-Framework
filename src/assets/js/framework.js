/**
 * @project DIT Framework
 * @link http://digitalrift.org
 * @author Yuriy Seleznev <sendelius@gmail.com>
 * @author Alex Kalantaryan <alex_phant0m@mail.ru>
 * @license MIT https://opensource.org/licenses/MIT
 */

function DITFramework() {
    var self = this;
    this.block_ajax = false;
    this.popup = false;

    this.serializefiles = function(objForm) {
        /* ADD FILE TO PARAM AJAX */
        var formData = new FormData();
        $.each($(objForm).find('input[type="file"]'), function(i, tag) {
            $.each($(tag)[0].files, function(i, file) {
                formData.append(tag.name, file);
            });
        });
        var params = $(objForm).serializeArray();
        $.each(params, function (i, val) {
            formData.append(val.name, val.value);
        });
        return formData;
    };

    this.initAjaxLinks = function () {
        $('a.ditAjaxLink').each(function(){
            $(this).removeClass('ditAjaxLink');
            $(this).on('click',function(){
                var link = $(this).attr('href');
                return false;
            });
        });
    };

    this.initPopupBtn = function () {
        $('a.ditPopupBtn').each(function(){
            $(this).removeClass('ditPopupBtn');
            $(this).on('click',function(){
                var src = $(this).data('src');
                $.ajax({
                    type: "POST",
                    url: src,
                    dataType : 'json',
                    processData: false,
                    contentType: false,
                    success: function(data){
                        if(typeof data.popup != "undefined") self.showPopup(data.popup);
                    }
                });
                return false;
            });
        });
    };

    this.initAjaxForms = function () {
        $('form.ditAjaxForm').each(function(index){
            var form = $(this);
            var container;
            var preloader = $('<div/>').addClass('ditPreloader').appendTo(form);
            if(form.hasClass('ditPopupForm')){
                container = $('.ditPopupContent');
            }else{
                container = form;
            }
            self.validateForm();
            form.removeClass('ditAjaxForm');
            form.on('submit',function(){
                if(form.valid() && self.block_ajax==false){
                    self.block_ajax = true;
                    preloader.show();
                    $.ajax({
                        type: "POST",
                        url: form.attr('action'),
                        data: self.serializefiles(form),
                        dataType : 'json',
                        processData: false,
                        contentType: false,
                        success: function(data){
                            preloader.hide();
                            self.block_ajax = false;
                            if(typeof data.form != "undefined"){
                                if(data.form.error){
                                    $('.ditErrorForm').remove();
                                    $('.ditSuccessForm').remove();
                                    $('<div/>').addClass('ditErrorForm').html(data.form.error).prependTo(container);
                                }
                                if(data.form.success){
                                    $('.ditErrorForm').remove();
                                    $('.ditSuccessForm').remove();
                                    $('<div/>').addClass('ditSuccessForm').html(data.form.success).prependTo(container);
                                    if(data.form.successCallback){
                                        eval(data.form.successCallback)();
                                    }
                                }
                            }
                        }
                    });
                }
                return false;
            });
        });
    };

    this.validateForm = function(){
        $.validator.addMethod("phoneFormat", function (value, element) {
            if($(element).attr('required')) {
                if (value=='+7 (___) ___-__-__'){
                    $(element).val('');
                    return false;
                }else{
                    return value.match(/^\+7 \(\d{3}\) \d{3}-\d{2}-\d{2}$/);
                }
            }else{
                if(value.length>0){
                    if (value=='+7 (___) ___-__-__'){
                        $(element).val('');
                        return true;
                    }else{
                        return value.match(/^\+7 \(\d{3}\) \d{3}-\d{2}-\d{2}$/);
                    }
                }else{
                    return true;
                }
            }
        }, "Введите корректный номер телефона");
        var validateForm = $('.ditAjaxForm:visible').not('.is_validate');
        validateForm.each(function() {
            $(this).addClass('is_validate').validate();
        });
        var phoneMask = $('input.ditPhoneMask:visible').not('.is_masked');
        phoneMask.each(function() {
            $(this).addClass('is_masked').rules('add', {
                phoneFormat: true
            });
        });
        phoneMask.mask("+7 (999) 999-99-99");
    };

    this.showPopup = function(options){
        var body = $('body');
        self.popup = $('<div/>').addClass('ditPopup');
        if(!options.btnOne && !options.btnTwo) self.popup.addClass('ditPopupNoBottom');
        var popupContainer;
        if(options.form) {
            var popupForm = $('<form/>').addClass('ditAjaxForm').addClass('ditPopupForm').appendTo(self.popup);
            if(options.formAction) popupForm.attr('action',options.formAction);
            popupContainer = popupForm;
        }else{
            popupContainer = self.popup;
        }

        var popupCloseBtn = $('<div/>').addClass('ditPopupCloseBtn').appendTo(popupContainer);
        var popupHeader = $('<div/>').addClass('ditPopupHeader').appendTo(popupContainer);
        var popupContentWrap = $('<div/>').addClass('ditPopupContentWrap').appendTo(popupContainer);
        var popupContent = $('<div/>').addClass('ditPopupContent').appendTo(popupContentWrap);
        var popupFooter = $('<div/>').addClass('ditPopupFooter').appendTo(popupContainer);
        var popupOverlay = $('<div/>').addClass('ditPopupOverlay').appendTo(body);
        self.popup.appendTo(body);

        if(options.class) {
            self.popup.addClass(options.class);
        }

        if(options.content) {
            popupContent.html(options.content);
        }else{
            popupContent.hide();
        }
        if(options.title) popupHeader.html('<h3>'+options.title+'</h3>');

        if(options.btnOne) {
            var btn = $('<input class="ditPopupFooterBtn ditPopupFooterBtnOne" value="'+options.btnOne+'" type="submit">');
            btn.appendTo(popupFooter);
        }
        if(options.btnTwo) {
            var btnTwo = $('<input class="ditPopupFooterBtn ditPopupFooterBtnTwo" value="'+options.btnTwo+'" type="button">');
            btnTwo.appendTo(popupFooter);
            btnTwo.off('click').on('click',self.closePopup);
            popupFooter.addClass('ditPopupFooterForTwoBtn');
        }
        if(options.hideClose) {
            popupCloseBtn.hide();
        }else{
            popupCloseBtn.show();
        }
        popupOverlay.show();
        self.popup.show();
        popupCloseBtn.off('click').on('click',self.closePopup);
        popupOverlay.off('click').on('click',self.closePopup);
        self.initAjaxForms();
        self.initAjaxLinks();
        self.initPopupBtn();
    };

    this.closePopup = function () {
        $('.ditPopupOverlay').remove();
        $('.ditPopup').remove();
    };

    this.init = function() {
        self.initAjaxForms();
        self.initAjaxLinks();
        self.initPopupBtn();
    }
}

var TF = new DITFramework();
$( document ).ready(function() {
    TF.init();
});