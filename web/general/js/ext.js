Ext = {
    init: function(){

        $.fn.ajx = function(attributes){
            var type = $(this).data('type');
            var url = $(this).data('url');
            var data = $(this).data('params');
            var dataType = 'json';
            var cache = true;
            var beforeSend = function(){};
            var success = function(json){};
            var complete = function(){};
            var error = function(){};
            var updateId = $(this).data('update');
            var parent = $(this).data('parent');
            var closest = $(this).data('closest');
            var formId = $(this).data('form');
            var prependId = $(this).data('before');
            var appendId = $(this).data('after');
            var redirect = $(this).data('redirect');
            var close = $(this).data('close');
            var modal = $(this).data('modal');
            var run = $(this).data('run');
            var parentDel = $(this).data('parentdel');
            var closestDel = $(this).data('closestdel');
            var remove = $(this).data('remove');
            var scrollerId = $(this).data('scroll');
            var parentUpd = $(this).data('parentupd');
            var closestUpd = $(this).data('closestupd');
            var updatesiblings = $(this).data('updatesiblings');
            var find = $(this).data('find');

            if(typeof attributes != 'undefined'){
                if(typeof attributes.type != 'undefined')
                    type = attributes.type;
                if(typeof attributes.url != 'undefined')
                    url = attributes.url;
                if(typeof attributes.data != 'undefined')
                    data = attributes.data;
                if(typeof attributes.dataType != 'undefined')
                    dataType = attributes.dataType;
                if(typeof attributes.cache != 'undefined')
                    cache = attributes.cache;
                if(typeof attributes.beforeSend != 'undefined')
                    beforeSend = attributes.beforeSend;
                if(typeof attributes.success != 'undefined')
                    success = attributes.success;
                if(typeof attributes.complete != 'undefined')
                    complete = attributes.complete;
                if(typeof attributes.error != 'undefined')
                    error = attributes.error;
                if(typeof attributes.updateId != 'undefined')
                    updateId = attributes.updateId;
                if(typeof attributes.formId != 'undefined')
                    formId = attributes.formId;
                if(typeof attributes.after != 'undefined')
                    appendId = attributes.after;
                if(typeof attributes.redirect != 'undefined')
                    redirect = attributes.redirect;
                if(typeof attributes.close != 'undefined')
                    close = attributes.close;
                if(typeof attributes.modal != 'undefined')
                    modal = attributes.modal;
                if(typeof attributes.run != 'undefined')
                    run = attributes.run;
                if(typeof attributes.before != 'undefined')
                    prependId = attributes.before;
                if(typeof attributes.parentdel != 'undefined')
                    parentDel = attributes.parentdel;
                if(typeof attributes.closestdel != 'undefined')
                    closestDel = attributes.closestdel;
                if(typeof attributes.remove != 'undefined')
                    remove = attributes.remove;
                if(typeof attributes.parentUpd != 'undefined')
                    parentUpd = attributes.parentUpd;
                if(typeof attributes.closestUpd != 'undefined')
                    closestUpd = attributes.closestUpd;
                if(typeof attributes.scrollerId != 'undefined')
                    scrollerId = attributes.scrollerId;
                if(typeof attributes.updatesiblings != 'undefined')
                    updatesiblings = attributes.updatesiblings;
                if(typeof attributes.find != 'undefined')
                    find = attributes.find;
            }

            var delElem = null;

            if(typeof url == 'undefined' || !url){
                url = $(this).attr('href');
            }

            if(typeof parentUpd != 'undefined'){
                updateId = $(this).parent(parentUpd);
            }

            if(typeof closestUpd != 'undefined'){
                updateId = $(this).closest(closestUpd);
            }

            if(typeof updateId == 'undefined'){
                if(typeof parent != 'undefined')
                    updateId = $(this).parent(parent);
                if(typeof closest != 'undefined')
                    updateId = $(this).closest(closest);
            }

            if(typeof updatesiblings != 'undefined'){
                updateId = $(this).siblings(updatesiblings);
            }

            if(typeof find != 'undefined'){
                updateId = eval(find);
            }

            if(typeof parentDel != 'undefined'){
                delElem = $(this).parent(parentDel);
            }

            if(typeof closestDel != 'undefined'){
                delElem = $(this).closest(closestDel);
            }

            if(typeof remove != 'undefined'){
                delElem = $(remove);
            }

            if(typeof data == 'undefined')
                data = 'name=value';

            if(typeof formId != 'undefined' && formId){
                var form;

                if(formId == 1)
                    form = $(this).closest('form');
                else
                    form = $(formId);

                if(form.length)
                    data += '&'+form.serialize();
            }

            if(typeof type == 'undefined')
                type = 'POST';

            var ajaxSettings = {
                modal: modal,
                updateId: updateId,
                appendId: appendId,
                prependId: prependId,
                run: run,
                scrollerId: scrollerId
            };

            $.ajax({
                type: type,
                url: url,
                data: data,
                dataType: dataType,
                cache: cache,
                beforeSend: function(){
                    Main.beforeAjax(ajaxSettings);
                    beforeSend();
                },
                success: function(json){
                    if(typeof redirect != 'undefined' && redirect)
                        json.redirect = redirect;

                    if(typeof close != 'undefined' && close){
                        if(close == 1 || close == 'true' || close == '1' || close == 'true')
                            close = this;

                        $(close).hide();
                    }

                    if(delElem !== null && !json.error)
                        delElem.remove();

                    Main.successAjax(json, ajaxSettings);
                    success(json);
                },
                complete: function(){
                    Main.afterAjax(ajaxSettings);
                    complete();
                },
                error: function(XMLHttpRequest, textStatus, errorThrown){
                    Main.errorAjax(errorThrown);
                    error();
                }
            });
        };


        $.fn.serializeObject = function() {
            var o = {};
            var a = this.serializeArray();
            $.each(a, function() {
                if (o[this.name]) {
                    if (!o[this.name].push) {
                        o[this.name] = [o[this.name]];
                    }
                    o[this.name].push(this.value || '');
                } else {
                    o[this.name] = this.value || '';
                }
            });
            return o;
        };

    }
};