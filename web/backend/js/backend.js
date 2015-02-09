Backend = {

    datatableId: '.dataTable',
    simpleTableId: '.simple-table table',
    tableSearchId: '.dataTables_filter input',

    DTsSearch: 'Поиск',
    DTsLengthMenu: 'элементов на страницу',
    DTsZeroRecords: 'Ничего не найдено',
    DTsInfo: 'из',
    DTsInfoEmpty: 'Список пуст',
    DTsInfoFilteredOne: 'отфильтровано из',
    DTsInfoFilteredTo: 'записей',

    init: function(data){
        if(typeof data != 'undefined'){
            var attributes = [
                'datatableId',
                'DTsSearch',
                'DTsLengthMenu',
                'DTsZeroRecords',
                'DTsInfo',
                'DTsInfoEmpty',
                'DTsInfoFilteredOne',
                'DTsInfoFilteredTo'
            ];

            $.each(attributes, function(index, element){
                if(typeof data[element] != 'undefined')
                    Backend[element] = data[element];
            });
        }

        Backend.setHandlers();
    },

    setHandlers: function(){
        Backend.dataTables();
        Backend.simpleTables();
    },


    // Handlers

    dataTables: function(){
        var dataTable = $(Backend.datatableId);

        if(!dataTable.length)
            return false;

        var options = Backend.getTableOptions();

        var ajaxOptions = dataTable.data('ajax');

        if(typeof ajaxOptions != 'undefined' && ajaxOptions){
            var url = dataTable.data('url');
            if(typeof url == 'undefined')
                url = ajaxOptions;

            options.bProcessing = true;
            options.bServerSide = true;
            options.sAjaxSource = url;
        }

        var table = dataTable.dataTable(options);

        Backend.fixDataTable();

        /*var filtering = dataTable.data('filter');

        if(typeof filtering != 'undefined' && filtering){
            var asInitVals = [];

            $(filtering+' input').on('input', function(){
                table.fnFilter(this.value, $(filtering+' input').index(this));
            });

            $(filtering+' input').each(function(i){
                asInitVals[i] = this.value;
            });

            $(filtering+' input').focus(function(){
                if(this.className == 'search_init'){
                    this.className = '';
                    this.value = '';
                }
            });

            $(filtering+' input').blur(function(i){
                if(this.value == ''){
                    this.className = 'search_init';
                    this.value = asInitVals[$(filtering+' input').index(this)];
                }
            });
        }*/
    },

    simpleTables: function(){
        var table = $(Backend.simpleTableId);

        if(!table.length)
            return false;

        var options = Backend.getTableOptions();

        table.dataTable(options);

        Backend.fixDataTable();
    },

    // END Handlers


    // Functions

    fixDataTable: function(){
        var tableContainer = $('.dataTables_wrapper');

        $('.span6', tableContainer).removeClass('span6').addClass('col-lg-6');
        $('select', tableContainer).addClass('form-control').addClass('xsmall');
        $('input:text', tableContainer).addClass('form-control').addClass('medium');
    },

    getTableOptions: function(){
        return {
            oLanguage: {
                sSearch: Backend.DTsSearch,
                sLengthMenu: '_MENU_ '+Backend.DTsLengthMenu,
                sZeroRecords: Backend.DTsZeroRecords,
                sInfo: '_START_ - _END_ '+Backend.DTsInfo+' _TOTAL_',
                sInfoEmpty: Backend.DTsInfoEmpty,
                sInfoFiltered: '('+Backend.DTsInfoFilteredOne+' _MAX_ '+Backend.DTsInfoFilteredTo+')'
            }
        };
    }

    // END Functions

}