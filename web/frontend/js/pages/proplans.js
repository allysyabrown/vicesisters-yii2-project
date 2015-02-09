Proplans = {

    proplanCostId: '.proplan-get-cost',
    proplanFormId: '#ProplanForm-form',
    proplanPriceInputId: '#proplanform-price',
    proplanPriceId: '#proplan-price',
    proplanDurationId: '#proplan-duration',

    isCliked: false,

    init: function(data){
        if(typeof data != 'undefined'){
            var attributes = [
            ];

            $.each(attributes, function(index, element){
                if(typeof data[element] != 'undefined')
                    Proplans[element] = data[element];
            });
        }

        Proplans.setHandlers();
    },

    setHandlers: function(){
        Proplans.proplanCost();
    },

    // Handlers

    proplanCost: function(){
        $(Proplans.proplanCostId).on('click', function(){
            var button = $(this);

            Main.ajx({
                url: button.data('url'),
                success: function(json){
                    if(json.html){
                        $(Frontend.regionPopup).addClass('region');
                        $(Frontend.headWrap).addClass('blur-add');
                        $(Frontend.contentWrap).addClass('blur-add');
                        $("html").css("overflow-y","hidden");
                        Main.setPopUpContent(json.html, Proplans.proplanSubmit());
                        Main.openPopup();
                    }
                }
            });

            return false;
        });
        $(document).on('mouseup', function(e){
            var container = $(Main.popupContainer);

            if (!container.is(e.target)
                && container.has(e.target).length === 0)
            {
                Main.closePopup();
                $(Frontend.regionPopup).removeClass('region');
                $(Frontend.headWrap).removeClass('blur-add');
                $(Frontend.contentWrap).removeClass('blur-add');
                $("html").css("overflow-y","scroll");
            }
        });
    },

    proplanSubmit: function(){
        setTimeout(function(){
            $(Proplans.proplanFormId+' select').on('change', function(){
                var price = $(Proplans.proplanPriceInputId).val();
                var duration = $(this).val();
                var baseDuration = $(Proplans.proplanDurationId).val();

                $(Proplans.proplanPriceId).text(price*(duration/baseDuration)+'$');
            });
        }, 10);

        $(Main.modalOkId).on('click', function(){
            var form = $(Proplans.proplanFormId);
            if(form.length && Proplans.isCliked === false){
                Proplans.isCliked = true;

                Main.ajx({
                    url: form.attr('action'),
                    data: form.serialize(),
                    success: function(json){
                        Proplans.proplanEnd(json);
                    }
                });
            }else{
                Main.closePopup();
            }

            return false;
        });
    },

    proplanEnd: function(json){
        if(typeof json.money !== 'undefined' && json.money){
            Main.setPopUpContent(json.money, function(){
                $(Main.modalOkId).on('click', function(){
                    window.location.href = json.url;
                    return false;
                });
            });
        }else if(json.html){
            Main.setPopUpContent(json.html, function(){
                $(Main.modalOkId).on('click', function(){
                    Main.setPopUpContent(json.html, function(){
                        Main.closePopup();
                    });
                    return false;
                });
            });
        }
    }

    // END Handlers


    // Functions


    // END Functioons

}