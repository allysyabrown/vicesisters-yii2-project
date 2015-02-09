Wu = {

    openFormId: '.wu-service-btn',
    formContainerId: '#wu-form-container',
    formId: '#WuForm-form',
    buttonOkid: '#wu-payment-ok',
    openFormButtonId: '#open-form-button',
    contentId: '.wu-description',
    formCOntId: '.wu-form',

    init: function(data){
        if(typeof data != 'undefined'){
            var attributes = [
            ];

            $.each(attributes, function(index, element){
                if(typeof data[element] != 'undefined')
                    Bitcoin[element] = data[element];
            });
        }

        Wu.setHandlers();
    },

    setHandlers: function(){
        Wu.openForm();
        Wu.getForm();
        Wu.sendPayment();
    },

    // Handlers

    openForm: function(){
        $(Wu.openFormButtonId).on('click', function(){
            $(Wu.contentId).addClass(Main.hiddenClass);
            $(Wu.formCOntId).removeClass(Main.hiddenClass);
        });
    },

    getForm: function(){
        $(Wu.openFormId).on('click', function(){
            $('.content-step-services').addClass(Main.hiddenClass);
            $(Wu.formContainerId).removeClass(Main.hiddenClass);
            return false;
        });
    },

    sendPayment: function(){
        var form = $(Wu.formId);
        var isSubmited = false;

        form.on('submit', function(){
            var isValid = true;

            $('input', form).each(function(index, value){
                if(!$(value).val())
                    isValid = false;
            });

            if(isValid && !isSubmited){
                isSubmited = true;

                $(this).ajx({
                    url: form.attr('action'),
                    data: form.serialize(),
                    success: function(json){
                        if(json.html){
                            $(Wu.formContainerId).html(json.html);
                            Wu.buttonOk();
                        }
                    }
                });
            }

            return false;
        });
    },

    buttonOk: function(){
        $(Wu.buttonOkid).on('click', function(){
            location.reload();
        });
    }

    // END Handlers


    // Functions


    // END Functions
};