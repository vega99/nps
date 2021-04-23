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

    function totes_get(){
      if($this->get('pid')){
        $result = $this->DAO->selectEntity('totes',  array('tote_id' =>$this->get('pid')), TRUE);

        $misTotes = array();
        
        foreach($result as $som){
          $ok = array(
            "tote_id" => $som->tote_id,
            "nombre_tote" => $som->nombre_tote,
            "unidades" => array()
          );

          $misUnidadesPorTote = $this->DAO->selectWhere('unidad', array('id_totes' => $som->tote_id));

          foreach($misUnidadesPorTote as $unidad){
            $oka = array(
              "unidad_id" => $unidad->unidad_id,
              "nombre_unidad" => $unidad->nombre_unidad,
              "nps" => array()
            );
            $misNps = $this->DAO->selectWhereNp('np', array('id_unidad' => $unidad->unidad_id));

            foreach($misNps as $np){
              //todo el $np o solo np
              array_push($oka["nps"], $np);
            }
            array_push($ok["unidades"], $oka);
          }
          array_push($misTotes, $ok);
        }
      }else{
        $result = $this->DAO->selectEntity('totes');
        $misTotes = array();
        
        foreach($result as $som){
          $ok = array(
            "tote_id" => $som->tote_id,
            "nombre_tote" => $som->nombre_tote,
            "unidades" => array()
          );

          $misUnidadesPorTote = $this->DAO->selectWhere('unidad', array('id_totes' => $som->tote_id));

          foreach($misUnidadesPorTote as $unidad){
            $oka = array(
              "unidad_id" => $unidad->unidad_id,
              "nombre_unidad" => $unidad->nombre_unidad,
              "nps" => array()
            );
            $misNps = $this->DAO->selectWhereNp('np', array('id_unidad' => $unidad->unidad_id));

            foreach($misNps as $np){
              //todo el $np o solo np
              array_push($oka["nps"], $np);
            }
            array_push($ok["unidades"], $oka);
          }
          array_push($misTotes, $ok);
        }

      }
       $response = array(
         "status" => 200,
         "status_text" => "success",
         "api" => "/totes/api/totes",
         "method" => "GET",
         "message" => "Lista de totes",
         "data" => $misTotes
       );
       $this->response($response,200);
    }
    function totes_post(){
      $this->form_validation->set_data($this->post());
      $this->form_validation->set_rules('pName','Nombre Tote','required');
      if ($this->form_validation->run()){
        //mandamos guardar
        $data = array(
          "nombre_tote" => $this->post('pName')
        );
        $this->DAO->saveOrUpdateEntity('totes', $data);

        $response = array(
          "status" => 200,
          "status_text" => "Success",
          "api" => "/totes/api/totes",
          "method" => "POST",
          "message" => "Tote registrado correctamente",
          "data" => null
        );

      }else{
        //mendaje de error
        $response = array(
          "status" => 500,
          "status_text" => "error",
          "api" => "/totes/api/totes",
          "method" => "POST",
          "message" => "error al registar el tote",
          "errors" => $this->form_validation->error_array(),
          "data" => null
        );

      }
      $this->response($response,200);
    }


    //en point :: ${localhost/6to_cuatrimestre/albums_api/index.php/genres/api/genres/pid/1 : put}
    //data : {pGenre : ""}
    function totes_put(){
      if($this->get('pid')){
        $totes_exists = $this->DAO->selectEntity('totes', array('tote_id'=> $this->get('pid')),TRUE);
        if($totes_exists){
          $this->form_validation->set_data($this->put());
          $this->form_validation->set_rules('pName','Nombre Tote','required');
          if($this->form_validation->run()){

            $data = array(
              "nombre_tote" => $this->put('pName')
            );
            $this->DAO->saveOrUpdateEntity('totes', $data, array("tote_id"=>$this->get('pid')));

            $response = array(
              "status" => 200,
              "status_text" => "Success",
              "api" => "/totes/api/totes",
              "method" => "PUT",
              "message" => "Tote actualizado correctamente",
              "data" => null
            );
          }else{
            $response = array(
              "status"=> 500,
              "status_text" => "error",
              "api"=> "/totes/api/totes",
              "method" => "PUT",
              "message" => "Error al actualizar Tote",
              "errors"=> $this->form_validation->error_array(),
              "data" => null
            );
          }
        }else{
          $response = array(
            "status"=> 500,
            "status_text" => "error",
            "api"=> "/totes/api/totes",
            "method" => "PUT",
            "message" => "Tote no localizado",
            "errors"=> $this->form_validation->error_array(),
            "data" => null
            );
        }
      }else{
        $response = array(
          "status"=> 404,
          "status_text" => "error",
          "api"=> "/totes/api/totes",
          "method" => "PUT",
          "message" => "Identificador de Tote no localizado, la clave de tote no fue enviada",
          "errors"=> $this->form_validation->error_array(),
          "data" => null
        );
      }
      $this->response($response, 200);
    }

    function totes_delete(){
        if($this->get('pid')){
          $tote_exists = $this->DAO->selectEntity('totes', array('tote_id'=> $this->get('pid')),TRUE);
          if($tote_exists){
            $whereCluse= array('tote_id'=>$this->get('pid'));
            
            $this->DAO->deleteItemEntity('totes',$whereCluse);

             $response = array(
               "status" => 200,
               "status_text" => "Success",
               "api" => "/totes/api/totes",
               "method" => "DELETE",
               "message" => "Tote eliminado correctamente",
               "data" => null
             );
          }else{
            $response = array(
              "status"=> 404,
              "status_text" => "error",
              "api"=> "/totes/api/totes",
              "method" => "DELETE",
              "message" => "Identificador de tote no localizado",
              "errors"=> $this->form_validation->error_array(),
              "data" => null
            );
          }
    }else{
      $response = array(
        "status"=> 404,
        "status_text" => "error",
        "api"=> "/totes/api/totes",
        "method" => "DELETE",
        "message" => "Identificador de tote no localizado, la clave de tote no fue enviada",
        "errors"=> $this->form_validation->error_array(),
        "data" => null
      );
    }
    $this->response($response, 200);
  }
}
