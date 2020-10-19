<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use PDOException;
use SandraCore\System;

class TableViewController extends Controller
{

    private static $tableName;


    private static function getTableName(System $sandra,string $table)
    {

        self::$tableName = $sandra->env.'_view_' . $table ;
        return self::$tableName;
    }




    public static function countTable(string $table)
    {

        $tableExists = self::checkExists($table);

        if($tableExists){

            return DB::table(self::getTableName($table))
                ->count();
        }

        return $tableExists;

    }

    public static function getColumns(System $sandra,string $table)
    {

        $tableExists = self::checkExists($sandra,$table);



        return DB::getSchemaBuilder()->getColumnListing(self::getTableName($sandra,$table));



    }


    public static function get(System $sandra,string $table)
    {

        $tableExists = self::checkExists($sandra,$table);

        if($tableExists){

            return DB::table(self::getTableName($sandra,$table))
                ->select('*')
                ->get();
        }

        return $tableExists;
    }

    public static function rawGet(string $table)
    {

        $tableExists = self::checkExists($table);

        if($tableExists){

            return DB::table(self::getTableName($table))
                ->select('*')
                ->get();
        }

        return $tableExists;
    }


    private static function checkExists(System $sandra,string $table)
    {
        $checkExists = DB::table(self::getTableName($sandra,$table))->exists();

        if(!$checkExists){
            try{
                $collectionController = new CollectionController;
                $collectionController->createEntityAndViewTable($table);

            }catch(PDOException $e){

                return false;
            }
            return true;
        }
        return $checkExists;
    }



}