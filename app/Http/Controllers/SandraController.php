<?php

namespace App\Http\Controllers;

use SandraCore\System;

class SandraController extends Controller
{
   public function routeSandra($db,$env){

       return new System($env,false,env('DB_HOST'),$db,env('DB_USERNAME'),env("DB_PASSWORD"));

   }
}
