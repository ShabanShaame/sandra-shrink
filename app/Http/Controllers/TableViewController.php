<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use PDOException;
use SandraCore\System;

class TableViewController extends Controller
{

    private static $tableName;

    /**
     * @param System $sandra
     * @param string $table
     * @return string
     */
    private static function getTableName(System $sandra,string $table)
    {

        self::$tableName = $sandra->env.'_view_' . $table ;

        return self::$tableName;
    }



    /**
     * @param System $sandra
     * @param string $table
     * @return mixed
     */
    public static function getColumns(System $sandra,string $table)
    {

        $tableExists = self::checkExists($sandra,$table);

        return DB::getSchemaBuilder()->getColumnListing(self::getTableName($sandra,$table));

    }


    /**
     * @param System $sandra
     * @param string $table
     * @return bool|\Illuminate\Support\Collection
     */
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


    /**
     * @param string $table
     * @return bool|\Illuminate\Support\Collection
     */
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


    /**
     * @param System $sandra
     * @param string $table
     * @return bool
     */
    private static function checkExists(System $sandra,string $table)
    {
        $tableName = self::getTableName($sandra,$table);

        try{
            $checkExists = DB::table($tableName)->exists();
        }catch(PDOException $e){
            $checkExists = false;
        }

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
