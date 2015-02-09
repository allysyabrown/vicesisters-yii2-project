Proplan = {

    allowDurationItemsId: '#editprolanform-allowdurationoptions',
    durationItemsId: '#proplan-duration-items',
    addDurationOptionId: '#add-duration-option',
    durationOptionsListId: '.duration-options-list',
    durationOptionInputId: '#editprolanform-durationoptions',
    removeDurationOptionId: '.remove-duration-option',
    exampleDurationOptionsId: '#duration-options-example',
    durationOptionId: '.duration-option-container',

    allowProplansPricesId: '#editprolanform-allowmembersprices',
    proplansPricesId: '#proplans-prices-container',

    allowRegionsPricesId: '#editprolanform-allowregionsprices',
    regionsPricesId: '#regions-prices-container',
    addRegionPriceId: '#add-region-price',
    removeRegionPriceId: '.remove-region-price',
    regionPriceExampleId: '#region-price-example',
    regionPriceElementId: '.regions-prices-list',
    regionPriceInputsId: '.region-price-inputs',

    formId: '#EditProlanForm-form',

    durationOptionValueLabel: '',
    durationOptionNameLabel: '',
    durationOptionsName: '',
    regionPriceName: '',
    regionPriceLabel: '',

    init: function(data){
        if(typeof data != 'undefined'){
            var attributes = [
                'durationOptionValueLabel',
                'durationOptionNameLabel',
                'durationOptionsName',
                'regionPriceName',
                'regionPriceLabel'
            ];

            $.each(attributes, function(index, element){
                if(typeof data[element] != 'undefined')
                    Proplan[element] = data[element];
            });
        }

        Proplan.setHandlers();
    },

    setHandlers: function(){
        Proplan.allowDurationOptions();
        Proplan.addDurationOption();
        Proplan.removeDurationOption();

        Proplan.allowProplansPrices();

        Proplan.allowRegionsPrices();
        Proplan.addRegionPrice();
        Proplan.removeRegionPrice();

        Proplan.beforeSubmitForm();
    },

    // Handlers

    allowDurationOptions: function(){
        $(Proplan.allowDurationItemsId).on('change', function(){
            $(Proplan.durationItemsId).toggleClass('hide');
        });
    },

    addDurationOption: function(){
        $(Proplan.addDurationOptionId).on('click', function(){
            var newOption = $(Proplan.exampleDurationOptionsId).html();

            $(Proplan.durationOptionsListId).append(newOption);
        });
    },

    removeDurationOption: function(){
        $(document).on('click', Proplan.removeDurationOptionId, function(){
            $(this).closest(Proplan.durationOptionId).remove();
        });
    },

    allowProplansPrices: function(){
        $(Proplan.allowProplansPricesId).on('change', function(){
            $(Proplan.proplansPricesId).toggleClass('hide');
        });
    },

    allowRegionsPrices: function(){
        $(Proplan.allowRegionsPricesId).on('change', function(){
            $(Proplan.regionsPricesId).toggleClass('hide');
        })
    },

    addRegionPrice: function(){
        var newRegion = $(Proplan.regionPriceExampleId).html();
        var regionPricesContainer = $(Proplan.regionsPricesId);
        var i = $(Proplan.regionPriceElementId).length;

        $(Proplan.addRegionPriceId).on('click', function(){
            regionPricesContainer.append(newRegion);

            //var input = '<input class="form-control" type="text" name="'+Proplan.regionPriceName+'['+i+'][price]" value="" placeholder="'+Proplan.regionPriceLabel+'" />';
            var input = '<input data-id="'+i+'" class="form-control" type="text" name="'+Proplan.regionPriceName+'['+i+'][price]" value="" />';

            $(Proplan.regionPriceElementId).last().children(Proplan.regionPriceInputsId).html(input);

            i++;
        });
    },

    removeRegionPrice: function(){
        $(document).on('click', Proplan.removeRegionPriceId, function(){
            $(this).closest('.regions-prices-list').remove();
        });
    },

    beforeSubmitForm: function(){
        var isSubmitted = false;

        $(Proplan.formId).on('submit', function(){
            if(isSubmitted === false){
                // Set region prices names
                var regionPrices = $(Proplan.regionPriceElementId);

                if(regionPrices.length === 0)
                    return true;

                regionPrices.each(function(index, elem){
                    var regions = $('select', $(elem));
                    if(regions.length === 0)
                        return true;

                    var i = $(Proplan.regionPriceInputsId+' input:text', $(elem)).data('id');

                    if(typeof i != 'undefined'){
                        var subname = Proplan.regionPriceName+'['+i+'][';

                        $(regions[0]).attr('name', subname+'region]');
                        if(typeof regions[1] != 'undefined')
                            $(regions[1]).attr('name', subname+'country]');

                        if(typeof regions[2] != 'undefined'){
                            if(typeof regions[3] != 'undefined'){
                                $(regions[2]).attr('name', subname+'state]');
                                $(regions[3]).attr('name', subname+'city]');
                            }else{
                                $(regions[2]).attr('name', subname+'city]');
                            }
                        }
                    }

                    var price = $('input:text', $(elem)).val();
                    if(!(parseInt(price) > 0))
                        $(elem).remove();
                });

                // Set duration options names
                var durationOptions = $(Proplan.durationOptionsListId).children(Proplan.durationOptionId);
                var y = 0;

                durationOptions.each(function(index, option){
                    $('select', $(option)).attr('name', Proplan.durationOptionsName+'['+y+'][duration]');
                    y++;
                });
            }

            isSubmitted = true;
            return true;
        });
    }

    // END Handlers


    // Functions

    // END Functions

}