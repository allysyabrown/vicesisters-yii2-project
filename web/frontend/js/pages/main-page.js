MainPage = {
    isGuest: 1,
    hasMoney: 0,
    isGuestRedirectUrl: '',
    noHasMoneyRedirectUrl: '',
    vipsUrl: '',
    premiumsUrl: '',
    topprofilesUrl: '',
    verifiedUrl: '',
    feedbackUrl: '',
    getNewFeedbackUrl: '',
    lastMemberUrl: '',
    vipPersonsHide: 6500,
    vipPersonsShow: 7500,
    lastVerifiedTime: 5000,
    lastHotMessageTime: 3000,
    lastFeedBackTime: 10000,
    topVipsContainerId: '#top-vips',
    vipPersonsID: '#vip-persons',
    topprofilesID: '#escorts-list ul',
    verifiedId: '#last-verified',
    feedbackId: '#escort-feedback',
    addHotMessageId: '.add-top-members-post-btn',
    hotMessageNextId: '.popups-wrap .next-btn',
    hotMessageBackId: '.popups-wrap .back-btn',
    hotMessageFormId: '#HotMessageForm-form',
    hotMessageFileInput: '#crop-image-name',
    bubbleManager: null,
    cropperManager: null,
    isCropped: false,
    allDone: false,
    sendMsgButton: '.message-btn',
    addPrivateMessageId: '#add-private-message',
    privateMessageId: '#first-private-message',
    hotMessagesListId: '#escort-messages',

    init: function (data) {
        if (typeof data != 'undefined') {
            var attributes = [
                'isGuest',
                'hasMoney',
                'isGuestRedirectUrl',
                'noHasMoneyRedirectUrl',
                'vipsUrl',
                'topMemebersUrl',
                'premiumsUrl',
                'topprofilesUrl',
                'verifiedUrl',
                'feedbackUrl',
                'getNewFeedbackUrl',
                'lastMemberUrl',
                'vipPersonsHide',
                'vipPersonsShow',
                'lastVerifiedTime',
                'lastHotMessageTime',
                'lastFeedBackTime',
                'hotMessagesListId'
            ];

            $.each(attributes, function (index, element) {
                if (typeof data[element] != 'undefined')
                    MainPage[element] = data[element];
            });
        }

        MainPage.setHandlers();
    },

    setHandlers: function () {
        MainPage.startBubbleManager();
        MainPage.shadowPopupsStandard();

        AllAjax.getTopVips();
        AllAjax.getHotMessages();
        AllAjax.getVipPersons();
        AllAjax.getTopprofiles();
        //AllAjax.getLastVerified();
        AllAjax.getFeedbacks();

        MainPage.topVipsTimeOut();
        //AllAjax.lastVerifiedTimeOut();
        AllAjax.lastHotMessageTimeOut();
        AllAjax.lastFeedBackTimeOut();

        MainPage.addHotMessage();
        MainPage.initHotMessages();
        MainPage.initCropper();
        MainPage.addPrivateMessage();
    },
    // Handlers

    startBubbleManager: function () {
        MainPage.bubbleManager = new BubbleManager();
        MainPage.bubbleManager.init();

        $(MainPage.sendMsgButton).on('click', function(){
            $(this).ajx({
                updateId: '.popup-step-1',
                success: function(json){
                    if(json.html){
                        $('.shadow.popups').fadeIn('slow');
                        $('.shadow .popups-wrap').fadeIn('slow');
                        $('.popup-step-1').fadeIn('slow');
                        $('.main-wrapper').addClass('blur');
                        MainPage.hotMessageNext();
                        MainPage.hotMassageBack();
                        Main.unsetPopupButtons();
                    }
                }
            });
        });
//        $(MainPage.sendMsgButton).on('click', function(){
//            Main.openPopup();
//            Main.unsetPopupButtons();
//            $(Frontend.regionPopup).addClass('region');
//            $(Frontend.headWrap).addClass('blur-add');
//            $(Frontend.contentWrap).addClass('blur-add');
//            $("html").css("overflow-y","hidden");
//        });
//        MainPage.bubbleManager = new BubbleManager();
//        MainPage.bubbleManager.init();
    },

    topVipsTimeOut: function () {
        setInterval(function () {
            var content = $('.main-wrapper .content-wrap .vip-persons-area .content');
            content.find('li').first().addClass('hide-me');
            setTimeout(function () {
                content.find('li').first().hide();
                content.find('li').removeClass('hide-me');
                content.find('li').first().appendTo(content).show();
            }, MainPage.vipPersonsHide);
        }, MainPage.vipPersonsShow);
    },

    initHotMessages: function(){
        (function loop() {
            var rand = Math.round(Math.random() * 20000) + 5000;

            setTimeout(function() {
                AllAjax.updateNewMassege();
                loop();
            }, rand);

        }());
    },

    addHotMessage: function(){
        $(MainPage.addHotMessageId).on('click', function(){
            $(this).ajx({
                updateId: '.popup-step-1',
                success: function(json){
                    if(json.html){
                        $('.shadow.popups').fadeIn('slow');
                        $('.shadow .popups-wrap').fadeIn('slow');
                        $('.popup-step-1').fadeIn('slow');
                        $('.main-wrapper').addClass('blur');
                        MainPage.hotMessageNext();
                        MainPage.hotMassageBack();
                    }
                }
            });
        });
    },

    hotMessageNext: function(){
        $(MainPage.hotMessageNextId).on('click', function(){
            MainPage.hotMessageNextStep();
            return false;
        });
    },

    hotMassageBack: function(){
        $(MainPage.hotMessageBackId).on('click', function () {
            MainPage.closeAllPopups();
            $('.main-wrapper').removeClass('blur');
            $(Frontend.regionPopup).removeClass('region');
            $(Frontend.headWrap).removeClass('blur-add');
            $(Frontend.contentWrap).removeClass('blur-add');
            $("html").css("overflow-y","scroll");
        });
    },
    addPrivateMessage: function(){
        $(document).on('click', '.submit-button', function(){
            Main.setPopUpButtons();
            Main.closePopup();
            MainPage.closeAllPopups();
            $('.main-wrapper').removeClass('blur');
            $(Frontend.regionPopup).removeClass('region');
            $(Frontend.headWrap).removeClass('blur-add');
            $(Frontend.contentWrap).removeClass('blur-add');
            $("html").css("overflow-y","scroll");
        });
    },

    initCropper: function () {
        Crop.init({
            cropDoneFunction: function (imageSource) {
                $('.shadow.crop-ava, .shadow').fadeOut();
                $('.popup-step-1').fadeOut();
                $('.popup-step-2').fadeOut();
                $('.popup-step-3').fadeIn();
                $('.shadow.popups').fadeIn();

                $(MainPage.hotMessageFileInput).val(imageSource);
                MainPage.isCropped = true;
            }
        });
    },

    // END Handlers


    // Private functions

    shadowPopupsStandard: function(){
        $('.shadow.popups').click(function (e) {
            if (e.target == e.currentTarget) {
                $(e.currentTarget).fadeOut();
                $('.main-wrapper').removeClass('blur');
                $(Frontend.regionPopup).removeClass('region');
                $(Frontend.headWrap).removeClass('blur-add');
                $(Frontend.contentWrap).removeClass('blur-add');
                $("html").css("overflow-y","scroll");
                Main.setPopUpButtons();
                $('.back-btn').click();
            }
        });
    },

    hotMessageNextStep: function(){
        if(MainPage.allDone){
            MainPage.closeAllPopups();
            window.location.reload();
        }else if(!MainPage.isCropped){
            if(MainPage.isGuest == 1){
                window.location = MainPage.isGuestRedirectUrl;
            }else if(MainPage.hasMoney == 0){
                window.location = MainPage.noHasMoneyRedirectUrl;
            }else{
                $('.popup-step-1').fadeOut();
                $('.popup-step-2').fadeIn();
            }
        }else{
            var form = $(MainPage.hotMessageFormId);
            var isValid = true;

            $('input', form).each(function(index, val){
                if(!$(val).val()){
                    isValid = false;
                }
            });

            if(isValid){
                MainPage.isCropped = false;
                MainPage.allDone = true;

                Main.ajx({
                    url: form.attr('action'),
                    data: form.serialize(),
                    success: function(json){
                        if(!json.error){
                            $('.shadow.crop-ava, .shadow').fadeOut();
                            $('.popup-step-1').fadeOut();
                            $('.popup-step-2').fadeOut();
                            $('.popup-step-3').fadeOut();
                            $('.popup-step-4').fadeIn();
                            $('.shadow.popups').fadeIn();
                            MainPage.isCropped = false;
                            MainPage.allDone = true;

                            $('.popup-step-4 .count').text(json.count);
                            $('.popup-step-4 .coint-count').text(json.balance);

                            $(document).on('click', MainPage.hotMessageNextId, function(){
                                MainPage.closeAllPopups();

                                window.location.reload();
                                return false;
                            });

                            $(document).on('click', MainPage.hotMessageBackId, function(){
                                MainPage.closeAllPopups();
                                $(Frontend.regionPopup).removeClass('region');
                                $(Frontend.headWrap).removeClass('blur-add');
                                $(Frontend.contentWrap).removeClass('blur-add');
                                $("html").css("overflow-y","scroll");

                                window.location.reload();
                                return false;
                            });
                        }
                    }
                });
            }
        }
    },

    disableOption: function(name){
        $('select[name='+name+']').children().first().attr('disabled', 'disabled');
    },

    closeAllPopups: function(){
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

        return false;
    }


    // END Private functions

}