

@extends('layouts.app')

@section('content')

    <div class="spinner-grow" role="status">
        <span class="sr-only"></span>
    </div>

    <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
    </ul>

    <div class="tab-content" id="pills-tabContent">
    </div>

    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.21/js/dataTables.bootstrap.min.js"></script>
    <script>
        const urlToCall = "<?php  print_r($urlToCall) ?>";
    </script>
    <script type="text/javascript" src="{{ asset('js/collection/collection_display.js') }}"></script>


    <div class="spinner-grow" role="status">
        <span class="sr-only"></span>
    </div>

    <button id="serverSide" class="btn btn-outline-success switchTables">Table Server Side</button>
    <button id="clientSide" class="btn btn-outline-primary switchTables">Table Client Side</button>

    <div class="" id="tableContainer">
        <table class="text-light dataTable table-dark" id="factoryTable">
            <thead></thead>
            <tbody></tbody>
        </table>
    </div>

    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.21/js/dataTables.bootstrap.min.js"></script>
    <script>
        const refMap = '<?php print_r(json_encode($refMap)) ?>';
        const table = '<?php  print_r($table) ?>';
        const db = '<?php  print_r($db) ?>';
        const env = '<?php  print_r($env) ?>';
    </script>
    <script type="text/javascript" src="{{ asset('js/collection/factory.js') }}"></script>

@endsection

