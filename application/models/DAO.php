<?php
defined('BASEPATH') OR exit('no...');

class DAO extends CI_model{
    function __construct(){
        parent::__construct();
    }
    //CONSUOTAR CUALQUIER ENTIDAD ($ENTITY_NAME)
    //whereCaluse ejemplo si mando  where id= ? and name = ? array("id"=>"?", "name"=> "?")
    //isUnique = true => regresa un row y si no un list
    function selectEntity($entityName, $whereClause = array(), $isUnique = FALSE){
      if($whereClause){
        $this->db->where($whereClause);
      }
      $query = $this->db->get($entityName);
      if($isUnique){
        return $query->result();
      }else{
        return $query->result();
      }
    }
    function saveOrUpdateEntity($entityName, $data, $whereClause = array()){
      if($whereClause){
        $this->db->where($whereClause);
        $this->db->update($entityName, $data);
      }else{
        $this->db->insert($entityName, $data);
      }
    }
    function deleteItemEntity($entityName, $whereCaluse){
      
      $this->db->where($whereCaluse);
      $this->db->delete($entityName);
    }


    function selectWhere($entityName, $whereClause){
      $this->db->where($whereClause);
      $query = $this->db->get($entityName);
      return $query->result();
    }
    function selectWhereNp($entityName, $whereClause){
      $this->db->select('np_id, np');
      $this->db->where($whereClause);
      $query = $this->db->get($entityName);
      return $query->result();
    }
}
