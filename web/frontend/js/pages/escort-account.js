EscortAccount = {

    messageFormId: '#HotMessageForm-form',
    escortTagsId: '#escortaccountform-extended input:checkbox',
    formSubmitId: '.save-settings-btn',
    formId: '#EscortAccountForm-form',
    settMenuSelectorId: '.side-settings-block .side-menu',
    settingsId: '.escort-account-settings',
    showPage: '.escort-account',
    addTravelButtonId: '.add-to-travel-list-btn',
    travelsId: '.user-travel-list ul',
    travelsSelectorId: '#escorttravelsform-travels option:selected',
    travelsHiddenId: '#escorttravelsform-travelslist',
    removeFormTravelListId: '.remove-city',
    travelsDelimiter: ':',
    nonSwitch: 'non-switch',

    uploadAvaUrl: '',

    init: function (data) {
        if (typeof data != 'undefined') {
            var attributes = [
                'messageFormId',
                'escortTagsId',
                'formSubmitId',
                'formId',
                'settMenuSelectorId',
                'settingsId',
                'showPage',
                'uploadAvaUrl',
                'travelsDelimiter'
            ];

            $.each(attributes, function (index, element) {
                if (typeof data[element] != 'undefined')
                    EscortAccount[element] = data[element];
            });
        }

        EscortAccount.setHandlers();
    },

    setHandlers: function(){
        EscortAccount.menuSelectors();
        EscortAccount.submitMessageForm();
        EscortAccount.setSettings();
        EscortAccount.travelsList();

        EscortAccount.initCropper();
    },


    // Handlers

    submitMessageForm: function(){
        $(EscortAccount.messageFormId).on('submit', function(e){
           /* e.preventDefault();
            var form = $(this);

            if($('textarea', form).val()){
                form.ajaxSubmit();
                $('textarea', form).val('');
            }*/

            return false;
        });
    },

    setSettings: function(){
        $('.fixed-buttons').hide();
        $('.settings-holder').on('click', function () {
            var holder = $(this),
                switcher = holder.find('.onoff-switch');

            var input = $('input[value="'+switcher.data('id')+'"]');

            if(input.length)
                input.click();

            if(switcher.attr('data-state') == 1) {
                switcher.attr('data-state', 0);
                switcher.removeClass('is-on');
                holder.removeClass('is-on');
            }else{
                switcher.attr('data-state', 1);
                switcher.addClass('is-on');
                holder.addClass('is-on');
            }
        });
    },

    menuSelectors: function(){
        $('.content .side-menu').removeClass(Main.activeClass);
        $('.escort-account-settings').fadeOut();
        $('.side-menu[data-id="'+EscortAccount.showPage+'"]').addClass(Main.activeClass);
        $(EscortAccount.showPage).fadeIn();

        $(EscortAccount.settMenuSelectorId).on('click', function(){
            if($(this).hasClass(EscortAccount.nonSwitch))
                return true;

            $(EscortAccount.settMenuSelectorId).removeClass(Main.activeClass);
            $(this).addClass(Main.activeClass);

            $(EscortAccount.settingsId).hide();
            $($(this).data('id')).show();
        });

        $(EscortAccount.formSubmitId).on('click', function(){
            $(EscortAccount.formId).submit();
            return false;
        });
    },

    travelsList: function(){
        var del = EscortAccount.travelsDelimiter;
        var travels = $(EscortAccount.travelsId);

        $(EscortAccount.addTravelButtonId).on('click', function(){
            var country = $(EscortAccount.travelsSelectorId);
            var hidden = $(EscortAccount.travelsHiddenId);
            var list = hidden.val();
            var id = country.val();

            if(list.indexOf(del+id+del) == -1){
                list += del+id+del;
                hidden.val(list);

                var newLi = '';

                newLi += '<li>';
                newLi += '<div class="city">'+country.text()+'</div>';
                newLi += '<div class="'+EscortAccount.removeFormTravelListId.replace('.', '')+'" data-id="'+id+'">X</div>';
                newLi += '</li>';

                travels.append(newLi);
            }
        });

        $(document).on('click', EscortAccount.removeFormTravelListId, function(){
            var hidden = $(EscortAccount.travelsHiddenId);
            var list = hidden.val();
            var id = del+$(this).data('id')+del;

            hidden.val(list.replace(id, ''));

            $(this).closest('li').remove();
        });
    },

    // END Handlers


    // Functions

    afterChangePassword: function(json){
        if(!json.error){
            $('.password-area').fadeOut();
            $('.account-area').fadeIn();
        }
    },

    initCropper: function(){
        Crop.init({
            popupId: '.add-new-ava',
            cropDoneFunction: function(imageSource){
                if(imageSource){
                    $('.shadow.crop-ava, .shadow').fadeOut();
                    $('.popup-step-1').fadeOut();
                    $('.popup-step-2').fadeOut();

                    Main.ajx({
                        url: EscortAccount.uploadAvaUrl,
                        data: {image: imageSource},
                        success: function(json){
                            if(!json.error){
                                location.reload();
                            }else{
                                Main.addNotify(json.error);
                            }
                        }
                    });
                }
            }
        });
    }

    // END Functions

}