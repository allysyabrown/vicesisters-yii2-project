Frontend = {

    profileBlock: '.user-prof',
    dropdownMenu: '.log-user-drop-menu',
    likeBlock: '.likes-count',
    helpButtonId: '.help-popup-button',
    helpFormUrl: '',
    ticketFormId: '#TicketForm-form',
    chatFrame: '#vice-chat',
    showMoreId: '.show-more-img',
    regionPopup: '.chose-region-popup',
    headWrap: '.head-wrap',
    contentWrap: '.content-wrap',
    extendedId: '#searchform-extended',
    selectors: '.selectize-control.form-control.single',
    buttonExtend: '.field-searchform-extended',
    searchFormId: '#SearchForm-form',
    advancedButtonId: '#SearchForm-form .advanced-search',
    advancesInputId: '.field-searchform-advanced input',
    extendedInputId: '#searchform-extendedtext',
    removeExtendedId: '#SearchForm-form .control-form-appended span',
    resetAllId: '#SearchForm-form .reset-all',
    selectorsRegion: '.selectize-control.form-control.single',
    regionSelector: '.region-selector',
    searchAdvanceForm: '.advanced-search-form',

    actionLikePhotoUrl: 'like/escortphoto',
    actionLikeFeedUrl: 'like/feedmessage',
    isAdvancedFrom: 0,

    init: function(data){
        if(typeof data != 'undefined'){
            var attributes = [
                'profileBlock',
                'dropdownMenu',
                'likeBlock',
                'helpFormUrl',
                'showMoreId',
                'isAdvancedFrom'
            ];

            $.each(attributes, function(index, element){
                if(typeof data[element] != 'undefined')
                    Frontend[element] = data[element];
            });
        }

        Frontend.setHandlers();
    },

    setHandlers: function(){
        Frontend.setDropdownMenu();
        Frontend.setLikeClick();
        Frontend.setTicketForm();
        Frontend.setChat();
        Frontend.escortListAnimate();
        Frontend.choseRegion();
        Frontend.setSelects();
        Frontend.settingList();
        Frontend.setAdvancesSearch();
        Frontend.showMoreOptionSearch();
        //Frontend.setResetAll();
    },

    // Handlers

    settingList: function(){

        $(Frontend.extendedId).on('change', function(){
            var sizeInp = $("div.control-form-appended").length;

            var del = ':';
            var extendedInput = $(Frontend.extendedInputId);
            var currentInputValue = extendedInput.val();
            var val = del+$(this).val()+del;

            if(currentInputValue.indexOf(val) === -1 && val !== del+'-'+del){
                if(sizeInp >= 1){
                    $(Frontend.regionSelector).css('height','350px');
                }
                if(sizeInp > 4){
                    $(Frontend.buttonExtend).hide();
                }else{
                    $(Frontend.buttonExtend).show();
                }

                var div = document.createElement('div');
                var span = document.createElement('span');
                $(div).attr('data-id', val);

                $(span).html('-');
                $(div).html($('option:selected', $(this)).text());
                $(div).append(span).addClass('control-form-appended');
                $('.inputs-search').append(div);

                extendedInput.attr('value', currentInputValue+val);
            }else{
                return false
            }
        });

        $(document).on('click', Frontend.removeExtendedId, function(){
            var sizeInp = $("div.control-form-appended").length;

            var extendedInput = $(Frontend.extendedInputId);
            var currentInputValue = extendedInput.val();
            var container = $(this).parent('div');

            if(sizeInp <= 2){
                $(Frontend.regionSelector).css('height','308px');
            }

            extendedInput.attr('value', currentInputValue.replace(container.data('id'), ''));
            container.remove();
            $(Frontend.buttonExtend).show();
        });
    },

    showMoreOptionSearch: function(){

        var a = $('.advanced-search');
        var b = 'adv-search-sp';

        a.on('click', function(){
            if(a.hasClass('adv-search-sp')){
                $(Frontend.searchAdvanceForm).addClass('fadeOutUp');
                $(Frontend.searchAdvanceForm).css('z-index','-1');
                $(Frontend.regionSelector).css('height','150px');
                $(Frontend.searchAdvanceForm).removeClass('advanced-search-form-on-click bounceInDown');
                $('.drop-img').removeClass('img-rotate-adv');
                a.removeClass(b);
            }
            else{
                $(Frontend.searchAdvanceForm).css('z-index','');
                $(Frontend.searchAdvanceForm).removeClass('fadeOutUp');
                $(Frontend.regionSelector).css('height','308px');
                $(Frontend.searchAdvanceForm).addClass('advanced-search-form-on-click animated bounceInDown');
                $('.drop-img').addClass('img-rotate-adv');
                a.addClass(b);
            }

        })
    },

    setSelects: function(){
        if(Frontend.isAdvancedFrom == 1){
            $(Frontend.advancedButtonId).click();
        }

        $('#SearchForm-form .form-control').each(function(indx, element){
            $(element).selectize();
        });
    },

    setResetAll: function(){
       /* $(Frontend.resetAllId).on('click', function(){
            var form = $(Frontend.searchFormId);

        });*/
    },

    setAdvancesSearch: function(){

        $(Frontend.advancedButtonId).on('click', function(){
            $(Frontend.advancesInputId).click();
        });
    },

    handleRegionSelector: function(selectId){
        $(selectId+' select').selectize();
        var selectState = '#cities-selector';
        if(selectId == selectState) {
            $(Frontend.selectorsRegion).css('width', '132px');
        }
        else{
            $(Frontend.selectorsRegion).css('width', '170px');
        }
    },

    setDropdownMenu: function(){
        $(Frontend.profileBlock).on('click', function(){
            var menu = $(Frontend.dropdownMenu);
            menu.toggle();
            menu.addClass('hide-me-on-click');
        });

        $(document).on('click', function(e){
            var menu = $(e.target).closest(Frontend.dropdownMenu);
            var userIcon = $(e.target).closest(Frontend.profileBlock);

            if(menu.length + userIcon.length == 0){
                menu = $(Frontend.dropdownMenu);
                menu.hide();
                menu.removeClass('hide-me-on-click');
            }
        });
    },

    setChat: function(){
      $(Frontend.chatFrame).bind( 'mousewheel DOMMouseScroll', function ( e ) {
          var e0 = e.originalEvent,
              delta = e0.wheelDelta || -e0.detail;

          this.scrollTop += ( delta < 0 ? 1 : -1 ) * 30;
          e.preventDefault();
      });
    },

    choseRegion: function(){

        $('.add-top-members-post-btn').on('click', function(){
            $("html").css("overflow-y","hidden");
        });
        $('.button-premium').on('click', function(){
            $("html").css("overflow-y","hidden");
        });
        $('.button-vip').on('click', function(){
            $("html").css("overflow-y","hidden");
        });
        var animate = 'front-side-prop';
        var count = 1;
        var frontSide1 = $('.front-side');
        var backSide1 = $('.back-side');
        var frontSide2 = $('.front-side-two');
        var backSide2 = $('.back-side-two');
//        var frontSide3 = $('.front-side-third');
//        var backSide3 = $('.back-side-third');
//        var trust3 = true;
        var trust = true;
        var trust2;

        setInterval(function() {
            if(count == 1){
                if(frontSide1.hasClass(animate)){
                    frontSide1.removeClass(animate);
                    backSide1.removeClass(animate);
                    trust = true;
//                    trust3 = false;
                    trust2 = false;
                }
                else{
                    frontSide1.addClass(animate);
                    backSide1.addClass(animate);
                    trust = true;
//                    trust3 = false;
                    trust2 = false;
                }
            }
            if(count == 2){
                if(frontSide2.hasClass(animate)){
                    frontSide2.removeClass(animate);
                    backSide2.removeClass(animate);
                    trust2 = true;
                    trust = false;
//                    trust3 = false;
                }
                else{
                    frontSide2.addClass(animate);
                    backSide2.addClass(animate);
                    trust2 = true;
                    trust = false;
//                    trust3 = false;
                }
            }
//            if(count == 3){
//                if(frontSide3.hasClass(animate)){
//                    frontSide3.removeClass(animate);
//                    backSide3.removeClass(animate);
//                    trust3 = true;
//                    trust = false;
//                    trust2 = false;
//                }
//                else{
//                    frontSide3.addClass(animate);
//                    backSide3.addClass(animate);
//                    trust3 = true;
//                    trust = false;
//                    trust2 = false;
//                }
//            }
            if(trust){
                count = 2;
            }
            if(trust2){
                count = 1;
            }
//            if(trust3){
//                count = 1;
//            }
        },5000);

        var choseSpan = $('.chose-sp');
        var regionSelector = $('.region-selector');

        $('.region-btn').on('click', function(){
            $(Frontend.regionPopup).addClass('region');
            $(Frontend.headWrap).addClass('blur-add');
            $(Frontend.contentWrap).addClass('blur-add');
            choseSpan.addClass('chose-sp-block');
            regionSelector.addClass('region-block');
            $("html").css("overflow-y","hidden");
        });

        $(Frontend.regionPopup).on('click', function(){
            $(Frontend.regionPopup).removeClass('region');
            $(Frontend.headWrap).removeClass('blur-add');
            $(Frontend.contentWrap).removeClass('blur-add');
            choseSpan.removeClass('chose-sp-block');
            regionSelector.removeClass('region-block');
            $("html").css("overflow-y","scroll");
        });
        $('.chose-btn').on('click', function(){
            $(Frontend.regionPopup).removeClass('region');
            $(Frontend.headWrap).removeClass('blur-add');
            $(Frontend.contentWrap).removeClass('blur-add');
            choseSpan.removeClass('chose-sp-block');
            regionSelector.removeClass('region-block');
            $("html").css("overflow-y","scroll");
        });

        $( document ).ready(function() {
            setTimeout(function(){
                $('.region-btn').addClass('fadeInLeft animated region-btn-left');
                $('.help-btn').addClass('fadeInRight animated help-btn-right');
            },2000);
        });
    },

    escortListAnimate: function(){
        var showMore = $(Frontend.showMoreId);

            //$( window ).load(function() {
            //
            //    var EscortArea = $('.regular-member-footer');
            //    var sizeEscortList = $(".member-area > ul > li").length;
            //    var escortListUl = $(".member-area ul");
            //    if(sizeEscortList < 10){
            //        EscortArea.hide();
            //    }
            //    else{
            //        EscortArea.show();
            //    }
            //    if(sizeEscortList == 0){
            //        escortListUl.addClass('escort-list-none');
            //    }
            //    else{
            //        escortListUl.removeClass('escort-list-none');
            //    }
            //});
            //



        if(!showMore.hasClass('js-appeared')){
            $( window ).scroll(function() {
                if (isScrolledIntoView(showMore)) {
                    setTimeout(function(){
                        showMore.addClass('js-appeared');
                    },300);
                    $('.show-more').fadeIn(2000);
                }
            });
        }

        function isScrolledIntoView(elem) {

            var docViewTop = $(window).scrollTop();
            var docViewBottom = docViewTop + $(window).height();
            if($(elem).length > 0){
                var elemTop = $(elem).offset().top;
                var elemBottom = elemTop + $(elem).height();

                return ((elemBottom <= docViewBottom) && (elemTop >= docViewTop));
            }else{
                return false;
            }
        };
    },

    setLikeClick: function(){
        $(document).on('click', Frontend.likeBlock, function(){
            var url;
            var like = this;
            var type = $(this).data('type');

            switch (type){
                case 'photo':
                    url = Frontend.actionLikePhotoUrl;
                    break;
                case 'feed':
                    url = Frontend.actionLikeFeedUrl;
                    break;
                default :
                    url = null;
                    break;
            }

            Main.ajx({
                url: url,
                type: 'POST',
                data: 'escortPhotoId='+$(this).data('id'),
                success: function (json) {
                    if(json.html){
                        $(like).replaceWith(json.html);
                    }
                }
            });
        });
    },

    setTicketForm: function(){
        var helpButton = $(Frontend.helpButtonId);
        if(helpButton.length){
            helpButton.on('click', function(){
                Main.ajx({
                    url: Frontend.helpFormUrl,
                    success: function(json){
                        if(json.html){
                            $(Frontend.regionPopup).addClass('region');
                            $(Frontend.headWrap).addClass('blur-add');
                            $(Frontend.contentWrap).addClass('blur-add');
                            $("html").css("overflow-y","hidden");
                            Main.setPopUpContent(json.html, Frontend.sendTicket());
                            Main.openPopup();
                        }
                    }
                });

                return false;
            });
        }
    },

    sendTicket: function(){
        $(Main.modalOkId).on('click', function(){
            var form = $(Frontend.ticketFormId);
            if(form.length){
                Main.ajx({
                    url: form.attr('action'),
                    data: form.serialize(),
                    success: function(json){
                        if(json.html){
                            Main.setPopUpContent(json.html, Frontend.afterSendTicket());
                            Main.openPopup();
                        }
                    }
                });
            }

            return false;
        });
    },

    afterSendTicket: function(){
        $(Main.modalOkId).on('click', function(){
            Main.closePopup();
            return false;
        });
    }

    // END Handlers


    // Functions



    // END Functions

}