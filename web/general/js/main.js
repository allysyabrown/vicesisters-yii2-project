Main = {

    autoUrlId: '.auto-url',
    autoAjaxId: '.auto-ajax',
    autoSelectId: '.auto-select',
    autoSelectAjaxId: '.auto-ajax-select',
    autoAjaxFormId: 'form.auto-ajax-form',
    hiddenClass: 'hide-container',
    activeClass: 'active',
    actionAskClass: '.ask-before',
    defaultConfirmationText: 'You sure?',

    modalCancelId: '.popup-button .back-btn',
    modalOkId: '.popup-button .next-btn',
    modalId: ['.popups-wrap .popup-content', '.popups-wrap', '.shadow.popups'],
    modalButtonsId: '#popup-buttons',
    popupContainer: '.popusp-bg',
    proplansUrl: '',

    ajaxLoader: 'ajaxLoader',

    init: function(data){
        if(typeof data != 'undefined'){
            var attributes = [
                'autoUrlId',
                'autoAjaxId',
                'autoSelectId',
                'autoSelectAjaxId',
                'autoAjaxFormId',
                'activeClass',
                'proplansUrl',
                'actionAskClass'
            ];

            $.each(attributes, function(index, element){
                if(typeof data[element] != 'undefined')
                    Main[element] = data[element];
            });
        }

        Ext.init();
        Main.setHandlers();
    },

    setHandlers: function(){
        Main.setAskBeforeAction();
        Main.setAutoUrl();
        Main.setAutoAjax();
        Main.setAutoAjaxForm();
        Main.setAutoSelect();
        Main.setAutoSelectAjax();
    },

    /* Handlers */

    setAutoUrl: function(){
        $(document).on('click', Main.autoUrlId, function(){
            window.location = $(this).data('url');
            return false;
        });
    },

    setAutoAjax: function(){
        $(document).on('click', Main.autoAjaxId, function(){
            $(this).ajx();
            return false;
        });
    },

    setAutoAjaxForm: function(){
        $(document).on('submit', Main.autoAjaxFormId, function(){
            var form = $(this);

            form.ajx({
                type: form.attr('method'),
                url: form.attr('action'),
                data: form.serialize(),
                scrollerId: form,
                success: function(json){
                    if(!json.error){
                        form.find('textarea').val('');
                        //form.addClass(Main.hiddenClass);
                    }
                }
            });

            return false;
        });
    },

    setAutoSelect: function(){
        $(document).on('change', Main.autoSelectId, function(){
            Main.select($(this));
        });
    },

    setAutoSelectAjax: function(){
        $(document).on('change', Main.autoSelectAjaxId, function(){
            var selector = $(this);
            var option = $('option:selected', selector);
            var data = option.data('params');

            if(typeof data == 'undefined' || !data){
                var name = option.parent('select').attr('name');
                if(name.indexOf('[') != -1){
                    name = name.split('[');
                    if(typeof name[1] != 'undefined')
                        name = name[1];
                    else
                        name = name[0];

                    name = name.replace(']', '');
                }

                data = name+'='+option.val();
            }

            if(option.length){
                selector.ajx({
                    type: 'GET',
                    data: data
                });
            }
        });
    },

    setAskBeforeAction: function(){
        $(document).on('click', Main.actionAskClass, function(){
            var text = $(this).data('ask');
            if(typeof text == 'undefined')
                text = Main.defaultConfirmationText;

            return confirm(text);
        });
    },

    /* END Handlers */


    /* Public functions */
    
    ajx: function(data){
        $(document).ajx(data);
    },

    redirectToProplans: function(){
        window.location = Main.proplansUrl;
    },
    
    addToUrl: function(attributes, url){
        if(typeof url == 'undefined')
            url = document.URL;

        if(typeof attributes == 'undefined')
            return url;

        url = url.split('?');

        if(typeof url[1] != 'undefined'){
            var attrs = Main.sepUrlParams(url[1]);
            url = url[0];
        }else{
            var attrs = {};
            url = url[0];
        }

        var oldAttrs = '';
        var newAttrs = '';

        if(typeof attributes == 'string')
            attributes = Main.sepUrlParams(attributes)

        $.each(attributes, function(n, val){
            if(typeof val !== 'undefined')
                newAttrs += n+'='+val+'&';

            if(typeof attrs[n] != 'undefined')
                attrs[n] = null;
        });

        $.each(attrs, function(n, val){
            if(val !== null)
                oldAttrs += n+'='+val+'&';
        });

        if(newAttrs.length > 0)
            newAttrs = newAttrs.substr(0, newAttrs.length-1);

        if(oldAttrs.length > 0)
            oldAttrs = oldAttrs.substr(0, oldAttrs.length-1);

        if(oldAttrs.length > 0)
            url += '?'+oldAttrs;

        if(newAttrs.length > 0){
            if(url.indexOf('?') == -1)
                url += '?'+newAttrs;
            else
                url += '&'+newAttrs;
        }

        return url;
    },

    removeFromUrl: function(attributes, url){
        if(typeof url == 'undefined')
            url = document.URL;

        if(typeof attributes == 'undefined')
            return url;

        var attrs;
        url = url.split('?');

        if(typeof url[1] != 'undefined'){
            attrs = Main.sepUrlParams(url[1]);
            url = url[0];
        }else{
            attrs = {};
            url = url[0];
        }

        if(typeof attributes == 'string')
            attributes = Main.sepUrlParams(attributes);

        var newAttrs = '';

        if(attrs && attributes){
            $.each(attributes, function(index, val){
                attrs[val] = null;
            });

            $.each(attrs, function(index, val){
                if(val != null)
                    newAttrs += index+'='+val+'&';
            });

            if(newAttrs.length > 0){
                newAttrs = newAttrs.substr(0, newAttrs.length-1);
                url += '?'+newAttrs;
            }
        }

        return url;
    },

    addNotify: function(message){
        console.log(message);
    },

    select: function(el){
        var url = $('option:selected', el).data('url');
        if(url != undefined && url !== '')
            document.location = url;
    },

        /* Ajax */

        blockAjaxScroller: function(params){
            return $('<div/>',{
                class: Main.ajaxLoader,
                css: {
                    height: params.height
                }
            });
        },

        beforeAjax: function(ajaxSettings){
            Main.addScrollers(ajaxSettings);
        },

        successAjax: function(json, settings){

            if(json.error){
                Main.errorAjax(json.error);
            }else{
                if(settings.modal){
                    if(settings.modal === '1' || settings.modal === 1){
                        settings.modal = Main.modalId;
                    }else{
                        settings.modal = settings.modal.split(',');
                    }

                    $.each(settings.modal, function(index, val){
                        var modal = $(val);
                        if(modal.length){
                            if(index == 0){
                                if(json.html)
                                    html = json.html;
                                else
                                    html = json.toString();

                                modal.html(html);
                            }

                            modal.fadeIn();
                        }
                    });
                }

                if(settings.updateId){
                    var updateContainer = $(settings.updateId);

                    if(updateContainer.length){
                        if(json.html){
                            updateContainer.html(json.html);
                        }
                    }
                }else if(settings.appendId){
                    var addContainer = $(settings.appendId);

                    if(addContainer.length){
                        if(json.html){
                            addContainer.append(json.html);
                        }
                    }
                }else if(settings.prependId){
                    var addContainer = $(settings.prependId);
                    if(addContainer.length){
                        if(json.html){
                            addContainer.prepend(json.html);
                        }
                    }
                }

                if(settings.run){
                    eval(settings.run);
                }

                if(typeof json.redirect != 'undefined' && json.redirect)
                    window.location.href = json.redirect;
            }

            Main.removeScrollers(settings);
        },

        afterAjax: function(ajaxSettings){
        },

        errorAjax: function(errorThrown){
            console.log(errorThrown);
        },

        addScrollers: function(params){
            if(params.scrollerId !== undefined){
                $(params.scrollerId).append(Main.blockAjaxScroller({height: $(params.scrollerId).height()}));
            }else if(params.updateId !== undefined){
                $(params.updateId).html(Main.blockAjaxScroller({height: $(params.updateId).height()}));
            } else if(params.appendId !== undefined){
                $(params.appendId).append(Main.blockAjaxScroller({height: $(params.appendId).height()}));
            } else if(params.prependId !== undefined){
                $(params.prependId).append(Main.blockAjaxScroller({height: $(params.prependId).height()}));
            } else {
            }
        },

        removeScrollers: function(params){
            if(params.scrollerId !== undefined){
                $(params.scrollerId).find('.'+Main.ajaxLoader).remove()
            }else if(params.appendId !== undefined){
                $(params.appendId).find('.'+Main.ajaxLoader).remove()
            } else if(params.prependId !== undefined){
                $(params.prependId).find('.'+Main.ajaxLoader).remove()
            } else {
            }
        },

        /* END Ajax */

    /* END Public functions */


    /* Private Functions */

    sepUrlParams: function(string){
        var params = Main.sep(string, '&');

        var result = {};
        var res = [];

        $.each(params, function(n, val){
            res = Main.sep(val, '=');
            result[res[0]] = res[1];
        });

        return result;
    },

    sep: function(string, sep){
        var result = {};

        if(string.length == 0)
            return result;

        var params = string.split(sep);

        $.each(params, function(n, val){
            result[n] = val;
        });

        return result;
    },

    setPopUpContent: function(content, handler)
    {
        Main.getPopUpContent().html(content);

        if(typeof handler === 'undefined'){
            handler = function(){
                $(Main.modalCancelId).on('click', function(){
                    $(Frontend.regionPopup).removeClass('region');
                    $(Frontend.headWrap).removeClass('blur-add');
                    $(Frontend.contentWrap).removeClass('blur-add');
                    $("html").css("overflow-y","scroll");
                    Main.closePopup();
                });
            };
        }

        handler();
    },

    setPopUpButtons: function(){
        $(Main.modalButtonsId).show();
    },

    unsetPopupButtons: function(){
        $(Main.modalButtonsId).hide();
    },

    openPopup: function(){
        $.each(Main.modalId, function(index, val){
            var block = $(val);

            if(block.length){
                if(index > 0){
                    block.fadeIn();
                }
            }
        });
    },

    closePopup: function(){
        $.each(Main.modalId, function(index, val){
            var block = $(val);
            if(block.length){
                if(index > 0){
                    block.fadeOut();
                }
            }
        });

        Main.getPopUpContent().html(' ');
    },

    getPopUpContent: function(isHtml){
        if(typeof isHtml === 'undefined' || isHtml === false)
            return $(Main.modalId[0]);
        else
            return $(Main.modalId[0]).html();
    }


    /* END Private Functions */
}