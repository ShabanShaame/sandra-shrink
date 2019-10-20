<?php


namespace App;


interface ResponseHandler
{
    public function handleResponse($group,$message,$closure = false);
    public function prompt($group, $message);



}
