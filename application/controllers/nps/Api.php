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
    function nps_get(){
       if($this->get('pid')){
         $result = $this->DAO->selectEntity('np',  array('np_id' =>$this->get('pid')), TRUE);
       }else{
          $result = $this->DAO->selectEntity('np');
       }
       $response = array(
         "status" => 200,
         "status_text" => "success",
         "api" => "/nps/api/nps",
         "method" => "GET",
         "message" => "Lista de Números de parte",
         "data" => $result
       );
       $this->response($response,200);
    }
    function valid_unidad($value){
      if($value){
        $unidad_exists = $this->DAO->selectEntity('unidad', array('unidad_id' => $value), true);
        if($unidad_exists){
          return TRUE;
        }else{
          $this->form_validation->set_message('valid_undidad','La clave del campo {field} no es correcto');
          return FALSE;
        }
 
      }else{
        $this->form_validation->set_message('valid_unidad','El campo {field} es requerido');
        return FALSE;
      }
    }
    function nps_post(){
      $this->form_validation->set_data($this->post());

      $this->form_validation->set_rules('pNp','Número de parte','required');
      $this->form_validation->set_rules('pUnidad','Clave de la unidad','required|callback_valid_unidad');
      if($this->form_validation->run()){
        $data = array(
          "np" => $this->post('pNp'),
          "id_unidad" => $this->post('pUnidad')
        );
        $this->DAO->saveOrUpdateEntity('np', $data);

        $response = array(
          "status" => 200,
          "status_text" => "Success",
          "api" => "/nps/api/nps",
          "method" => "POST",
          "message" => "Número de parte registrado correctamente",
          "data" => null
        );
      }else{

        $response = array(
          "status" => 500,
          "status_text" => "error",
          "api" => "/nps/api/nps",
          "method" => "POST",
          "message" => "error al registar el Número de parte",
          "errors" => $this->form_validation->error_array(),
          "data" => null
        );

      }
      $this->response($response,200);
    }

    function nps_put(){
      if($this->get('pid')){
        $np_exists = $this->DAO->selectEntity('np', array('np_id'=> $this->get('pid')),TRUE);
        if($np_exists){
          $this->form_validation->set_data($this->put());
          $this->form_validation->set_rules('pNp','Nombre','required');
          $this->form_validation->set_rules('pUnidad','Clave de la unidad','required|callback_valid_unidad');
    
          if($this->form_validation->run()){
  
            $data = array(
              "np" => $this->put('pNp'),
              "id_unidad" => $this->put('pUnidad')
            );
            $this->DAO->saveOrUpdateEntity('np', $data, array("np_id"=>$this->get('pid')));
  
            $response = array(
              "status" => 200,
              "status_text" => "Success",
              "api" => "/nps/api/nps",
              "method" => "PUT",
              "message" => "Número de parte actualizado correctamente",
              "data" => null
            );
          }else{
            $response = array(
              "status"=> 500,
              "status_text" => "error",
              "api"=> "/nps/api/nps",
              "method" => "PUT",
              "message" => "Error al actualizar el Número de parte",
              "errors"=> $this->form_validation->error_array(),
              "data" => null
            );
          }
        }else{
          $response = array(
            "status"=> 500,
            "status_text" => "error",
            "api"=> "/nps/api/nps",
            "method" => "PUT",
            "message" => "Número de parte no localizado",
            "errors"=> $this->form_validation->error_array(),
            "data" => null
            );
        }
      }else{
        $response = array(
          "status"=> 404,
          "status_text" => "error",
          "api"=> "/nps/api/nps",
          "method" => "PUT",
          "message" => "Identificador del Número de parte no localizado, la clave de el Número de parte no fue enviada",
          "errors"=> $this->form_validation->error_array(),
          "data" => null
        );
      }
      $this->response($response, 200);
    }

        function nps_delete(){
            if($this->get('pid')){
              $np_exists = $this->DAO->selectEntity('np', array('np_id'=> $this->get('pid')),TRUE);
              if($np_exists){
                 $this->DAO->deleteItemEntity('np',array('np_id'=>$this->get('pid')));

                 $response = array(
                   "status" => 200,
                   "status_text" => "Success",
                   "api" => "/nps/api/nps",
                   "method" => "DELETE",
                   "message" => "Número de parte eliminado correctamente",
                   "data" => null
                 );
              }else{
                $response = array(
                  "status"=> 404,
                  "status_text" => "error",
                  "api"=> "/nps/api/nps",
                  "method" => "DELETE",
                  "message" => "Identificador de Número de parte no localizado",
                  "errors"=> $this->form_validation->error_array(),
                  "data" => null
                );
              }
        }else{
          $response = array(
            "status"=> 404,
            "status_text" => "error",
            "api"=> "/nps/api/nps",
            "method" => "DELETE",
            "message" => "Identificador de Número de parte no localizado, la clave de Número de parte no fue enviada",
            "errors"=> $this->form_validation->error_array(),
            "data" => null
          );
        }
        $this->response($response, 200);
      }
}
