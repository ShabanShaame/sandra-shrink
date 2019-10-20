<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 19.10.2019
 * Time: 10:27
 */

namespace App;


use App\Http\Controllers\Controller;
use Carbon\Laravel\ServiceProvider;
use SandraCore\System;

class ShrinkResponse extends Controller
{
    private $response ;
    private $consultation ;

    public function __construct(ShrinkConsultation $consultation)
    {
        $this->consultation = $consultation ;

    }

    public function responseRegister($group,$message,$closure=false){

        $this->response[$group] = $message ;

        $this->consultation->dispatchToCaller($group,$message,$closure);




    }



}
