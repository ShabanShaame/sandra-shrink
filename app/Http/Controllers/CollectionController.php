<?php

namespace App\Http\Controllers;

use CsCannon\AssetCollectionFactory;
use CsCannon\SandraManager;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use SandraCore\System;
use Yajra\DataTables\Facades\DataTables;
use CsCannon\AssetFactory;
use ReflectionClass;
use SandraCore\EntityFactory;

class CollectionController extends Controller
{

    private static $sandra;

    public function index($db, $env,$table,SandraController $sandraController)
    {

        if($env === "null"){
            $myEnv = '';
        }else{
            $myEnv = $env;
        }

        self::$sandra = $sandraController->routeSandra($db,$myEnv);

        $columns = TableViewController::getColumns(self::$sandra,$table);

        return view('test.index', [
            'refMap'        => $columns,
            'table'         => $table,
             'db'           => $db,
             'env'          => $env
        ]);

    }


    public function get($db,$env,$table,SandraController $sandraController)
    {

        if($env === 'null'){
            $env = '';
        }

        self::$sandra = $sandraController->routeSandra($db,$env);
        $datas = TableViewController::get(self::$sandra,$table);

        return DataTables::of($datas)
            ->addIndexColumn()
            ->make(true);

    }

    /**
     * @param string $db
     * @param string $env
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     * @throws \ReflectionException
     */
    public function testView(string $db, string $env)
    {
        $assetFactory = new AssetFactory();
        $assetFactory->populateLocal();

        return $this->viewFromObject($db, $env, $assetFactory);

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
                $entityFactory = new AssetCollectionFactory(self::$sandra);
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
        $entityFactory->createViewTable($entity);

        return $entityFactory;
    }


    /**
     * create Table from object EntityFactory
     *
     * @param string $db
     * @param string $env
     * @param EntityFactory $entity
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     * @throws \ReflectionException
     */
    public function viewFromObject(string $db, string $env,EntityFactory $entity){

        $class = new ReflectionClass($entity);
        $className = $class->getShortName();

        $columnArray = [];

        foreach($entity->sandraReferenceMap as $concept){

            /** @var \SandraCore\Concept $concept  */
            $columnArray[] = $concept->getShortname();
        }

        return view('test.index', [
            'env'       => $env,
            'db'        => $db,
            'refMap'    => $columnArray,
            'table'     => $className
        ]);
    }




}
