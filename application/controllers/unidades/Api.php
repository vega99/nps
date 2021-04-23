<?php
defined('BASEPATH') OR exit('no...');
use Restserver\Libraries\REST_Controller;
require APPPATH.'libraries/REST_Controller.php';
require APPPATH.'libraries/Format.php';

class Api extends REST_Controller{
    function __construct(){
        parent::__construct();
        $this->load->model('DAO');
    }
  
    function unidades_get(){
       if($this->get('pid')){
         $result = $this->DAO->selectEntity('unidad',  array('unidad_id' =>$this->get('pid')), TRUE);


         $misUnidades = array();

         foreach($result as $some){
           $ok = array(
             "unidad_id" => $some->unidad_id,
             "nombre_unidad" => $some->nombre_unidad,
             "nps" => array()
           );
 
           $misNpPorUnidad = $this->DAO->selectWhereNp('np', array('id_unidad' => $some->unidad_id));
 
           foreach($misNpPorUnidad as $np){
             //todo el $np o solo np
             array_push($ok["nps"], $np);
           }
           array_push($misUnidades, $ok);
         }
       }else{
          $result = $this->DAO->selectEntity('unidad');
          
          $misUnidades = array();

          foreach($result as $some){
            $ok = array(
              "unidad_id" => $some->unidad_id,
              "nombre_unidad" => $some->nombre_unidad,
              "nps" => array()
            );
  
            $misNpPorUnidad = $this->DAO->selectWhereNp('np', array('id_unidad' => $some->unidad_id));
  
            foreach($misNpPorUnidad as $np){
              //todo el $np o solo np
              array_push($ok["nps"], $np);
            }
            array_push($misUnidades, $ok);
          }

       }
       $response = array(
         "status" => 200,
         "status_text" => "success",
         "api" => "/albums/api/albums",
         "method" => "GET",
         "message" => "Lista de Albums",
         "data" => $misUnidades
       );
       $this->response($response,200);
    }
    function unidades_post(){
      $this->form_validation->set_data($this->post());

      $this->form_validation->set_rules('pName','Nombre','required');
      $this->form_validation->set_rules('nTote','Clave del   tote','required|callback_valid_tote');
      if($this->form_validation->run()){
        $data = array(
          "nombre_unidad" => $this->post('pName'),
          "id_totes" => $this->post('nTote')
        );
        $this->DAO->saveOrUpdateEntity('unidad', $data);

        $response = array(
          "status" => 200,
          "status_text" => "Success",
          "api" => "/unidad/api/unidad",
          "method" => "POST",
          "message" => "Unidad registrada correctamente",
          "data" => null
        );
      }else{

        $response = array(
          "status" => 500,
          "status_text" => "error",
          "api" => "/unidad/api/unidad",
          "method" => "POST",
          "message" => "error al registar el Unidad",
          "errors" => $this->form_validation->error_array(),
          "data" => null
        );

      }
      $this->response($response,200);
    }

    function valid_tote($value){
     if($value){
       $tote_exists = $this->DAO->selectEntity('totes', array('tote_id' => $value), true);
       if($tote_exists){
         return TRUE;
       }else{
         $this->form_validation->set_message('valid_tote','La clave del campo {field} no es correcto');
         return FALSE;
       }

     }else{
       $this->form_validation->set_message('valid_tote','El campo {field} es requerido');
       return FALSE;
     }
   }

   function unidades_put(){
    if($this->get('pid')){
      $unidad_exists = $this->DAO->selectEntity('unidad', array('unidad_id'=> $this->get('pid')),TRUE);
      if($unidad_exists){
        $this->form_validation->set_data($this->put());
        $this->form_validation->set_rules('pName','Nombre','required');
        $this->form_validation->set_rules('nTote','Clave del tote','required|callback_valid_tote');
  
        if($this->form_validation->run()){

          $data = array(
            "nombre_unidad" => $this->put('pName'),
            "id_totes" => $this->put('nTote')
          );
          $this->DAO->saveOrUpdateEntity('unidad', $data, array("unidad_id"=>$this->get('pid')));

          $response = array(
            "status" => 200,
            "status_text" => "Success",
            "api" => "/unidades/api/unidades",
            "method" => "PUT",
            "message" => "Unidad actualizado correctamente",
            "data" => null
          );
        }else{
          $response = array(
            "status"=> 500,
            "status_text" => "error",
            "api"=> "/unidad/api/unidad",
            "method" => "PUT",
            "message" => "Error al actualizar unidad",
            "errors"=> $this->form_validation->error_array(),
            "data" => null
          );
        }
      }else{
        $response = array(
          "status"=> 500,
          "status_text" => "error",
          "api"=> "/albums/api/albums",
          "method" => "PUT",
          "message" => "Album no localizado",
          "errors"=> $this->form_validation->error_array(),
          "data" => null
          );
      }
    }else{
      $response = array(
        "status"=> 404,
        "status_text" => "error",
        "api"=> "/unidades/api/unidades",
        "method" => "PUT",
        "message" => "Identificador de unidad no localizado, la clave de unidad no fue enviada",
        "errors"=> $this->form_validation->error_array(),
        "data" => null
      );
    }
    $this->response($response, 200);
  }
  function unidades_delete(){
    if($this->get('pid')){
      $unidad_exists = $this->DAO->selectEntity('unidad', array('unidad_id'=> $this->get('pid')),TRUE);
      if($unidad_exists){
         $this->DAO->deleteItemEntity('unidad',array('unidad_id'=>$this->get('pid')));

         $response = array(
           "status" => 200,
           "status_text" => "Success",
           "api" => "/unidades/api/unidades",
           "method" => "DELETE",
           "message" => "Unidad eliminado correctamente",
           "data" => null
         );
      }else{
        $response = array(
          "status"=> 404,
          "status_text" => "error",
          "api"=> "/unidades/api/unidades",
          "method" => "DELETE",
          "message" => "Identificador de Unidad no localizado",
          "errors"=> $this->form_validation->error_array(),
          "data" => null
        );
      }
}else{
  $response = array(
    "status"=> 404,
    "status_text" => "error",
    "api"=> "/unidades/api/unidades",
    "method" => "DELETE",
    "message" => "Identificador de Unidad no localizado, la clave de unidad no fue enviada",
    "errors"=> $this->form_validation->error_array(),
    "data" => null
  );
}
$this->response($response, 200);
 }
}
