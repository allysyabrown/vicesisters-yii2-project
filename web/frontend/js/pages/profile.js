Profile = {

    navButton: '.escort-nav',
    contentBlocks: '.escort-content',
    addPrivateMessageId: '#add-private-message',
    privateMessageId: '#first-private-message',
    privateMessageButtonsId: '.message-content .message-btns',
    privateMessageFormId: '#MessageForm-form',
    galleryPhoto: '.gallery-photo > img, .top-photo > .mini-shadow',
    galleryPopup: '#gallery-popup',
    feedMessageForm: '.message-form',
    feedbackForm: '.feedback-form',
    feedCommentForm: '.comment-form',
    commentButton: '.coment-btn',
    feedContainer: '.feeds-top-content',
    feedCommentPrefix: '#comment-form-',
    galleryPopupWrapper: '.gal-popup-wrap',
    sendMsgButton: '.send-msg',
    removePhotoButton: '.remove-photo',
    showMoreId: 'a.show-more-btn',
    feedsListId: '#escort-feed-messages',

    show: '',

    uploadPhotoUrl: '',

    init: function(data){
        if (typeof data != 'undefined') {
            var attributes = [
                'show',
                'uploadPhotoUrl'
            ];

            $.each(attributes, function(index, element){
                if (typeof data[element] != 'undefined')
                    Profile[element] = data[element];
            });
        }

        Profile.setHandlers();
    },

    setHandlers: function(){
        Profile.setNavigation();
        Profile.setPhotoGallery();
        Profile.setFeeds();
        Profile.setFeedbacks();
        Profile.setMessage();
        Profile.addPrivateMessage();
        Profile.setPopups();

        Profile.initCropper();
        Profile.fixedTabs();
        Profile.getKeyPress();

        Profile.setShowMoreFeeds();
    },

    // Handlers

    setNavigation: function(){
        $(Profile.navButton).on('click', function(){
            $(Profile.navButton).removeClass(Main.activeClass);
            $(this).addClass(Main.activeClass);

            $(Profile.contentBlocks).hide();
            $($(this).data('id')).show(300);
        });

        if(Profile.show){
            var show = Profile.show;
            $(Profile.navButton).removeClass(Main.activeClass);
            $(Profile.navButton+'[data-id="'+show+'"]').addClass(Main.activeClass);
            $(Profile.contentBlocks).hide();
            $(show).show(300);
        }
    },

    getKeyPress: function(){
        window.addEventListener("keydown", checkKeyPressed, false);

        function checkKeyPressed(e) {
            if (e.keyCode == "37"){
                $('.roll-left').click();
            }
            if(e.keyCode == "39"){
                $('.roll-right').click();
            }
        }
    },

    setPhotoGallery: function(){
        $('.fixed-buttons').hide();
        $(Profile.galleryPhoto).on('click', function(){
            var id = $(this).data('id');

            $(Profile.galleryPopup).show();
        });

        $(Profile.removePhotoButton).on('click', function(){
            var obj = $(this);

            if(confirm("Delete this photo?")) {
                Main.ajx({
                    url: obj.data('url'),
                    data: {delete: true},
                    success: function(json){
                        if(!json.error){
                            obj.parent().remove();
                        } else {
                            Main.addNotify(json.error);
                        }
                    }
                })
            }
        });

        $(document).on('mouseup', function(e){
            var container = $(Profile.galleryPopupWrapper);

            if (!container.is(e.target)
                && container.has(e.target).length === 0)
            {
                $(Profile.galleryPopup).hide();
            }
        });
        $(document).on('mouseup', function(e){
            var container = $(Profile.galleryPopupWrapper);

            if (!container.is(e.target)
                && container.has(e.target).length === 0)
            {
                $(Profile.galleryPopup).hide();
            }
        });

    },

    setFeeds: function(){
        var textarea = $(Profile.feedMessageForm).find('textarea');

        textarea.on('click', function(){
            $(this).height('100px');
            $(Profile.feedMessageForm).find('button').removeClass(Main.hiddenClass);
        });

        $(document).on('click', Profile.commentButton, function(){
            var id = $(this).parent().parent().data('id');
            $(Profile.feedCommentPrefix + id).toggleClass(Main.hiddenClass);
        });
    },

    fixedTabs: function(){
        var title = $('.lower-title');
        var distance = title.offset().top;
        var $window = $(window);
        var menu = $('.main-header-bg');
        var distanceHead = menu.offset().top;
        var header = $('.header-js');
        var head = $(Frontend.headWrap);

        $window.scroll(function() {
            if( ($window.scrollTop()) + 50  >= distance) {
                title.addClass('fixed-lower-title');
            }
            else{
                title.removeClass('fixed-lower-title');
            }
            if ( ($window.scrollTop()) - 400  >= distanceHead ) {
                menu.css('margin-top','-38px');
                header.css('margin-top','-38px');
                head.css('height','49');
            }
            else{
                menu.css('margin-top','0');
                header.css('margin-top','0');
                head.css('height','89');
            }
        });
    },

    setFeedbacks: function(){
        var textarea = $(Profile.feedbackForm).find('textarea');

        textarea.on('click', function(){
            $(this).height('100px');
            $(Profile.feedbackForm).find('button').removeClass(Main.hiddenClass);
        });
    },

    setMessage: function(){
        $(Profile.sendMsgButton).on('click', function(){
            Main.openPopup();
            Main.unsetPopupButtons();
        });
    },

    addPrivateMessage: function(){
        $(Profile.addPrivateMessageId).on('click', function(){
            Main.setPopUpContent($(Profile.privateMessageId).html());
            Main.unsetPopupButtons();
            Main.openPopup();
        });

        Main.getPopUpContent().on('click', '.cencel-btn', function(){
            Main.closePopup();
        });

        Main.getPopUpContent().on('submit', Profile.privateMessageFormId, function(){
            var form = $(this);

            if($('textarea', form).val()){
                Main.ajx({
                    url: form.attr('action'),
                    data: form.serialize(),
                    success: function(json){
                        if(!json.error){
                            Main.closePopup();
                        }
                    }
                });
            }

            return false;
        });
    },

    setPopups: function(){
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

        $(document).on('click', '.back-btn', function(){
            $(Frontend.regionPopup).removeClass('region');
            $(Frontend.headWrap).removeClass('blur-add');
            $(Frontend.contentWrap).removeClass('blur-add');
            $("html").css("overflow-y","scroll");
            $('.shadow').hide();
        });

        $(document).on('submit', Profile.privateMessageFormId, function(){
            $(Frontend.regionPopup).removeClass('region');
            $(Frontend.headWrap).removeClass('blur-add');
            $(Frontend.contentWrap).removeClass('blur-add');
            $("html").css("overflow-y","scroll");
            Main.closePopup();
        });
    },
    
    toggleLike: function(id){
        $(id).toggleClass('active');
    },

    initCropper: function(){
        Crop.init({
            popupId: '.upload-more-btn, .addphoto',
            width: 600,
            height: 600,
            cropDoneFunction: function(imageSource){
                if(imageSource){
                    $('.shadow.crop-ava, .shadow').fadeOut();
                    $('.popup-step-1').fadeOut();
                    $('.popup-step-2').fadeOut();
                    $(Frontend.regionPopup).removeClass('region');
                    $(Frontend.headWrap).removeClass('blur-add');
                    $(Frontend.contentWrap).removeClass('blur-add');
                    $("html").css("overflow-y","scroll");

                    Main.ajx({
                        url: Profile.uploadPhotoUrl,
                        data: {image: imageSource},
                        success: function(json){
                            if(!json.error){
                                location.reload();
                            }else{
                                if(json.error == 'Лимит фотографий исчерпан'){
                                    Main.redirectToProplans();
                                }
                                Main.addNotify(json.error);
                            }
                        }
                    });
                }
            }
        });
    },

    setShowMoreFeeds: function(){
        $(Profile.showMoreId).on('click', function(){
            var feedsCount = $(Profile.feedsListId).children('ul').length;

            $(this).ajx({
                data: {feedsCont: feedsCount},
                after: Profile.feedsListId
            });

            return false;
        });
    }

    // END Handlers

};