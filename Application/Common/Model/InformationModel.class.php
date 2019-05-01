<?php

namespace Common\Model;
use Think\Model;

class InformationModel extends Model{
  private $_db = '';
  
  public function __construct(){
    $this -> $_db = M('information');     
  }

  public function get_list(){
    
    return $this -> $_db -> field('content,istop,createtimes') -> order('istop desc,createtime desc') -> limit(3) -> select();
  }
  public function get_admin_list(){
    
    return $this -> $_db -> field('id,content,istop,createtimes') -> order('istop desc,createtime desc') -> select();
  }
  public function admin_update($id,$data){
    $where['id'] = $id;
    return $this -> $_db -> where($where) -> save($data);
  }
  public function add($data){
    $data['createtime'] = time();
    $data['createtimes'] = date('Y-m-d H:i:s',time());
    $data['status'] = 1;
    return $this -> $_db -> add($data);
  }
  

}