jQuery(document).ready(function($){

    if(urlToCall != 'null'){
        document.addEventListener('DOMContentLoaded', getDatas(urlToCall));
    }



    function getDatas(myUrl){

        $.ajax({

            url: myUrl,
            type : 'get',
            beforeSend: function(){
                $('.spinner-grow').show();
            },
            success: function(response){

                $('.spinner-grow').hide();

                // let countDatasources = 0;

                $.each(response, function(index, content){

                    // let active = '';
                    // let selected = '';
                    // let show = '';
                    //
                    // if(countDatasources == 0){
                    //     active = 'active';
                    //     selected = 'true';
                    //     show = 'show';
                    // }else{
                    //     active = '';
                    //     selected = 'false';
                    //     show = '';
                    // }
                    //
                    // let datasources = '<li class="nav-item" id='+ datasource
                    //     +' role="presentation"><a class="nav-link datasources '+ active
                    //     +' id="pills-'+ datasource +'-tab" data-toggle="pill" href="#pills-'+datasource
                    //     +'" role="tab" aria-controls="pills-'+datasource
                    //     +'_table'+ countDatasources +'" aria-selected='+ selected +'>'+
                    //     datasource
                    //     +'</a></li>';
                    //
                    // $('#pills-tab').append(datasources);
                    //
                    // const tableId = datasource + '_table' + countDatasources;
                    //
                    // const tableCreate = '<table id='+ tableId +' class="datasourceTable table"></table>';



                    // $('#pills-'+ datasource).prepend('<div class="col-10 offset-1" id="'+ datasource +'-content"></div>');
                    //
                    // $('#pills-'+ datasource).append(tableCreate);
                    //
                    // countDatasources ++;

                    if(typeof content == 'object' && index === 'data'){
                        dataTableInit(content);
                    }

                })

            },

            error: function (jqXHR, exception){

                $msg = '';

                if (jqXHR.status === 0) {
                    msg = 'Not connect.\n Verify Network.';
                } else if (jqXHR.status == 404) {
                    msg = 'Requested page not found. [404]';
                } else if (jqXHR.status == 500) {
                    msg = 'Internal Server Error [500].';
                } else if (exception === 'timeout') {
                    msg = 'Time out error.';
                } else if (exception === 'abort') {
                    msg = 'Ajax request aborted.';
                } else {
                    msg = 'Uncaught Error.\n' + jqXHR.responseText;
                }
                $('#jsonAlert').slideDown(500).html($msg);
            }
        })
    }




    function dataTableInit(datasArray){

        const content = datasArray.shift();

        const columnsToDisplay = [];

        $.each(datasArray.shift(), function (name, datas){
            columnsToDisplay.unshift({ title: name, data: name })
        })

        // $.each(datasArray.shift(), function(name, datas){
        //     columnsToDisplay.unshift({ title: name, data: name });
        // })
        //
        console.log(columnsToDisplay);

        $('#tableContent').DataTable({
            data: content,
            columns: columnsToDisplay
        })

        // $('.row').addClass('col-6 offset-3 text-warning');
        // $('.dataTables_paginate').parent().removeClass('col-sm-7').addClass('col8 offset-2');
    }




    function getContent(array){

        const datasToDisplay = [];

        $.each(array, function(name, datas){

            if($.type(datas) == 'object'){
                $.each(datas, function(info, data){

                    if($.type(data) == 'object'){
                        $.each(data, function(string, metaDatas){

                            metaDatas.name = info;
                            datasToDisplay.push(metaDatas);
                        })
                    }
                })
            }
        })

        return datasToDisplay;
    }


})
