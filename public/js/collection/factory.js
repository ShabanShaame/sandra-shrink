jQuery(document).ready(function($){

    if(refMap != 'null' && table != 'null'){

        console.log(refMap);

        const references = refMap.replace('[', '').replace(']', '');
        const refColumns = references.split(',');

        let columnsArray = [];

        // create array with all columns, from refMap
        refColumns.forEach(element => {
            str = element.replace(/^"(.*)"$/, '$1');
            if(str != 'creationTimestamp'){
                columnsArray.push({ title: str, data: str })
            }
        });

        $.ajax({
            url: '/api/collection/'+db+'/'+env+'/get/'+table,
            type: 'get',
            beforeSend:function(){
                $('.spinner-grow').show();
            },
            success: function(response){
                $('.spinner-grow').hide()
                ajaxServerSide()
                changeView()
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


        function ajaxServerSide(){

            $table = $('#factoryTable');

            $table.DataTable({
                processing: true,
                serverSide: true,
                paging: true,
                ajax: '/api/collection/'+db+'/'+env+'/get/'+table,
                columns: columnsArray,
            })

        }


        function changeView(){

            $table = $('#factoryTable');
            $filter = $('#factoryTable_filter');
            $paginate = $('#factoryTable_paginate');

            $array = [
                $table,
                $filter,
                $paginate,
            ];

            $.each($array, function(index, id){
                id.addClass('container')
                id.parent().parent().addClass('container-fluid')
            })
        }


    }




})
