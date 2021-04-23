<?php
defined('BASEPATH') OR exit('no...');
use Restserver\Libraries\REST_Controller;
require APPPATH.'libraries/REST_Controller.php';
require APPPATH.'libraries/Format.php';

class Home extends REST_Controller{
    function __construct(){
        parent::__construct();
    }
    function index_get(){
      $message = array(
        "api_name" => "albums",
        "api_desc" => "Api de comunicacion del sitema de Albums"

      );
      $this->response($message,200);
    }
}
