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

class ShrinkConsultation extends Controller
{
    public $sResponse = null ;
    private $caller = null ;
    private $keepCaptive = false ;
    private $shinkDataGraph = null ;
    private $currentDatagraph = null ;

    public function __construct(ResponseHandler $caller)
    {
       $this->sResponse = new ShrinkResponse($this);
       $this->caller = $caller ;

       //Shrink datagraph
        $this->shinkDataGraph = new System('shrink',true);

        //Datagraph

       //$dataGFactory = $this->shinkDataGraph->factoryManager->create('datagaphFactory','datagraph','datagraphFile');
        //$dataGFactory->getOrCreateFromRef()

        $this->currentDatagraph = new System('klay');


    }

    public function sendResponse($group,$message,$closure=false)
    {
        $this->sResponse->responseRegister($group,$message,$closure);


    }

    public function start()
    {
        $this->keepCaptive = true ;
        $this->caller->prompt("Welcome",'Welcome');


    }

    public function dispatchToCaller($group,$message,$closure=false){

    $this->caller->handleResponse($group,$message,$closure=false);


    }

    public function backMain(){

        $this->keepCaptive = true ;
        $this->caller->prompt("Welcome",'And Now');


    }

    public function examineCommand($command,$type=null){

        $sandra = $this->currentDatagraph ;

        $conceptExplorer = new ConceptExplorer();
       $data =  $conceptExplorer->loadConcept($command,$sandra,$this);

       if ($this->keepCaptive){
           $this->backMain();

       }




    }

    public function getContext(){








    }

}
