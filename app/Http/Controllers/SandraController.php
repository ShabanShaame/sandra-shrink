<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use SandraCore\System;

class SandraController extends Controller
{
   public function routeSandra($db,$env){

       return new System($env,false,env('DB_HOST'),$db,env('DB_USERNAME'),env("DB_PASSWORD"));



   }
}
