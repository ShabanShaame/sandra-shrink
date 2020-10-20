<?php

namespace App\Http\Controllers;

use CsCannon\AssetCollectionFactory;
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
            'refMap'    => $columns,
            'table'     => $table,
             'db'       => $db,
             'env'      => $env
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


    /**
     * create an Entity from a string
     *
     * @param String $entity
     * @return EntityFactory|false
     */
    public function createEntityAndViewTable(string $entity)
    {

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
