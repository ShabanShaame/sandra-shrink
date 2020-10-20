jQuery(document).ready(function($){

    if(refMap != 'null' && table != 'null'){

        $('.spinner-grow').show()

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

        ajaxServerSide()
        changeView()

        function ajaxServerSide(){

            $table = $('#factoryTable');

            $('.spinner-grow').hide()

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
