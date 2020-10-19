<?php

namespace App\Http\Controllers;

use App\DataTables\UsersDataTable;
use CsCannon\AssetCollectionFactory;
use CsCannon\SandraManager;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use SandraCore\System;
use Yajra\DataTables\Facades\DataTables;
use CsCannon\AssetFactory;
use Exception;
use ReflectionClass;
use SandraCore\EntityFactory;

class CollectionController extends Controller
{

    public function index($db,$env,$table,SandraController $sandraController)
    {


        $sandra = $sandraController->routeSandra($db,$env);

        $columns = TableViewController::getColumns($sandra,$table);


        return view('test.index', [
            'refMap'    => $columns,
            'table'     => $table,
             'urlToCall'     => "/api/collection/$db/$env/get/$table",
             'db'     => "$db",
             'env'     => "$env"
        ]);



    }

    public function get($db,$env,$table,SandraController $sandraController)
    {

        $sandra = $sandraController->routeSandra($db,$env);
        $datas = TableViewController::get($sandra,$table);

        return DataTables::of($datas)
            ->addIndexColumn()
            ->make(true);

    }




    public function testView()
    {

        $assetFactory = new AssetFactory();
        $assetFactory->populateLocal();

        return $this->viewFromObject($assetFactory);

    }




    public function htmlTableView(Request $request)
    {
        $datas = $request->all();

        // Check the form
        $validator = Validator::make($datas, [
            'address'       => 'required|max:255',
            'blockchain'    => 'required',
            'function'      => 'required'
        ]);

        $errors = $validator->messages();

        if($validator->fails()){

            return view('blockchain/index', [
                'howToTest'     => $request->input('howToTest'),
                'blockchains'   => BlockchainController::getBlockchains()
            ])
            ->withErrors($errors);
        }

        $function = $request->input('function');
        $address = $request->input('address');
        $net = str_replace(' ', '_', $request->input('net'));
        $howToTest = $request->input('howToTest');

        return view('collection/collection_display', [
            'function'      => $function,
            'address'       => $address,
            'net'           => $net,
            'howToTest'     => $howToTest
        ]);
    }


    /**
     * count number of rows on db table
     *
     * @param String $table
     * @return Int|false
     */
    public function countDatas(string $table)
    {
        return TableViewController::countTable($table);
    }


    /**
     * Ajax from DataTables with db view
     *
     * @param String $table
     * @return \Yajra\DataTables\Facades\DataTables
     */
    public function tableAjax(string $table)
    {

        $datas = TableViewController::get($table);

        return DataTables::of($datas)
            ->addIndexColumn()
            ->make(true);
    }


    /**
     * From request, call Entity creation and return error if class doesn't exists
     *
     * @param Request $request
     */
    public function factoryToTableView(Request $request)
    {

        $searchedEntity = $request->input('factory');

        $entityFactory = $this->createEntityAndViewTable($searchedEntity);

        if(!$entityFactory){
            return view('collection/collection_display', [
                'error' => 'Class "'.$searchedEntity.'" not found'
            ]);
        }

        return $this->viewFromObject($entityFactory);
    }



    /**
     * create an Entity from a string
     *
     * @param String $entity
     * @return EntityFactory|false
     */
    public function createEntityAndViewTable(string $entity)
    {

        $sandra = new System('', true, env('DB_HOST').':'.env('DB_PORT'), env('DB_SANDRA'), env('DB_USERNAME'), env('DB_PASSWORD'));
        SandraManager::setSandra($sandra);

        $namespace = "CsCannon'";
        $string = addslashes($namespace) . $entity;
        $factory = str_replace("'", "", $string);


        if(is_subclass_of($factory, 'SandraCore\EntityFactory')){

            if(strtolower($factory) == strtolower('CsCannon\AssetCollectionFactory')){
                $entityFactory = new AssetCollectionFactory(SandraManager::getSandra());
            }else{
                $entityFactory = new $factory;
            }

        }else{

            $factory = str_replace('CsCannon', 'CsCannon\Blockchains', $factory);

            if(is_subclass_of($factory, 'SandraCore\EntityFactory')){
                $entityFactory = new $factory;
            }else{
                return false;
            }
        }


        /** @var \SandraCore\EntityFactory $entityFactory */

        $entityFactory->populateLocal();
        $entityFactory->createViewTable($entity . '_cscview');

        return $entityFactory;
    }




    /**
     * create Table from object EntityFactory
     *
     * @param EntityFactory $entity
     * @return View
     */
    public function viewFromObject(EntityFactory $entity){

        $class = new ReflectionClass($entity);
        $className = $class->getShortName();

        foreach($entity->sandraReferenceMap as $concept){

            /** @var \SandraCore\Concept $concept  */
            $columnArray[] = $concept->getShortname();
        }

        return view('collection/collection_display', [
            'refMap'    => $columnArray,
            'table'     => $className
        ]);
    }



    /**
     * return Json for client side DataTables
     *
     * @param String $tableName
     * @return array
     */
    public function dbToJson(string $tableName)
    {

        $myDatas = TableViewController::get($tableName)->toArray();

        if(!$myDatas){
            return [];
        }

        $response['recordsTotal'] = count($myDatas);
        $response['data'] = $myDatas;

        return $response;
    }



}
