EscortList = {
    isGuest: 1,
    hasMoney: 0,
    isGuestRedirectUrl: '',
    noHasMoneyRedirectUrl: '',

    dummyAjaxId: '.dummy',

    showMoreId: '.regular-member-footer a',
    listId: '#escorts-list ul',
    listElementId: '#escorts-list li',

    addHotMessageId: '.add-top-members-post-btn',
    hotMessageNextId: '.popups-wrap .next-btn',
    hotMessageBackId: '.popups-wrap .back-btn',
    hotMessageFormId: '#HotMessageForm-form',
    hotMessageFileInput: '#crop-image-name',
    allDone: false,
    isCropped: false,

    showMoreUrl: '',

    init: function(data){
        if(typeof data != 'undefined'){
            var attributes = [
                'isGuest',
                'showMoreUrl',
                'hasMoney',
                'isGuestRedirectUrl',
                'noHasMoneyRedirectUrl'
            ];

            $.each(attributes, function(index, element){
                if(typeof data[element] != 'undefined')
                    EscortList[element] = data[element];
            });
        }

        EscortList.setHandlers();
    },

    setHandlers: function(){
        EscortList.showMore();
        EscortList.hideDummy();
        AllAjax.getHotMessages();
        //AllAjax.getLastVerified();
        AllAjax.getFeedbacks();

        AllAjax.lastHotMessageTimeOut();
        //AllAjax.lastVerifiedTimeOut();
        AllAjax.lastFeedBackTimeOut();
    },


    // Handlers

    showMore: function(){
        $(EscortList.showMoreId).on('click', function(){
            $(this).ajx({
                url: EscortList.showMoreUrl,
                data: {offset: $(EscortList.listElementId).length},
                success: function(json){
                    if(json.html){
                        $(EscortList.listId).append(json.html);
//                        setTimeout(function(){
//                            var n = $('.member-area').height();
//                            $('html, body').animate({scrollTop: n}, 500);
//                        }, 300);
                    }
                }
            });

            return false;
        });
    },

    hideDummy: function(){
        $(EscortList.dummyAjaxId).hide();
    },

    closeAllPopups: function () {
        $('.shadow.crop-ava, .shadow').fadeOut();
        $('.popup-step-1').fadeOut();
        $('.popup-step-2').fadeOut();
        $('.popup-step-3').fadeOut();
        $('.popup-step-4').fadeOut();
        $('.shadow.crop-ava, .shadow').fadeOut();
        $(Frontend.regionPopup).removeClass('region');
        $(Frontend.headWrap).removeClass('blur-add');
        $(Frontend.contentWrap).removeClass('blur-add');
        $("html").css("overflow-y","scroll");


        EscortList.isCropped = false;
        EscortList.allDone = false;

        return false;
    }

    // END Handlers

}