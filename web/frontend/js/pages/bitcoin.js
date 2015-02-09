Bitcoin = {

    getInfoImgId: '.blockchain.stage-begin img',
    formId: '#BitcoinForm-form',
    balanceId: '.user-balance',
    addToBalanceId: '.add-to-balance',
    cancelId: '.go-to-step-1-btn',
    okId: '.bit-sub-btn',
    serviceScriptInputId: '#service-script-input',
    moneyInputId: '#bitcoinform-amount',
    cancelButtonId: '.back-btn',
    okButtonId: '.ok-btn',
    payWithBitcoinsId: '.bitcoin-service-btn',

    getInfoUrl: '',
    submitCount: 0,

    init: function(data){
        if(typeof data != 'undefined'){
            var attributes = [
                'getInfoImgId',
                'getInfoUrl'
            ];

            $.each(attributes, function(index, element){
                if(typeof data[element] != 'undefined')
                    Bitcoin[element] = data[element];
            });
        }

        Bitcoin.setHandlers();
    },

    setHandlers: function(){
        Bitcoin.popupButtons();
        Bitcoin.addToBalance();
        Bitcoin.cancel();
        Bitcoin.formSubmit();
        Bitcoin.payWithBitcoins();
    },

    // Handlers

    payWithBitcoins: function(){
        $(Bitcoin.payWithBitcoinsId).on('click', function(){
            $('.content-step-2').fadeIn();
            $('.content-step-services').addClass(Main.hiddenClass);
        });
    },

    popupButtons: function(){
        $(document).on('click', Bitcoin.cancelButtonId, function(){
            location.reload();
        });
        $(document).on('click', Bitcoin.okButtonId, function(){
            location.reload();
        });
    },

    addToBalance: function(){
        $(Bitcoin.addToBalanceId).on('click', function(){
            $(Bitcoin.balanceId).addClass(Main.hiddenClass);
            $('.content-step-services').removeClass(Main.hiddenClass);
        });
    },

    cancel: function(){
        $(Bitcoin.cancelId).on('click', function(){
            $(Bitcoin.balanceId).removeClass(Main.hiddenClass);
            $('.content-step-2').fadeOut();
        });
    },

    formSubmit: function(){
        $(Bitcoin.formId).on('submit', function(){
            Bitcoin.submitCount++;
            if(Bitcoin.submitCount == 1){
                var form = $(this);

                Main.ajx({
                    url: form.attr('action'),
                    data: form.serialize(),
                    success: function(json){
                        if(json.html){
                            var scriptInput = $(Bitcoin.serviceScriptInputId, json.html);
                            if(scriptInput.length){
                                $('.content-step-2').html(json.html);

                                var script = document.createElement('script');
                                script.src = scriptInput.val();
                                document.body.appendChild(script);

                                Bitcoin.getInfo();
                                return false;
                            }
                        }
                    }
                });
            }else{
                Bitcoin.submitCount = 0;
            }

            return false;
        });
    },

    // END Handlers

    // Functions

    getInfo: function(){
        setTimeout(function(){
            $(Bitcoin.getInfoImgId).click();
            $('.content-step-3').fadeIn();
        }, 300);

        $('.ok-btn', '.back-btn').on('click', function(){
            $('.content-step-2').fadeOut();
            $('.content-step-3').fadeOut();
            $(Bitcoin.balanceId).removeClass(Main.hiddenClass);
            return false;
        });
    }

    // END Functions
}