<?php

namespace Common\Model;
use Think\Model;

class ReportuserModel extends Model{
  private $_db = '';
  
  public function __construct(){
    $this -> $_db = M('reportuser');     
  }

  public function add($data){
    $data['createtime'] = time();
    $data['createtimes'] = date('Y-m-d H:i:s',time());
    $data['status'] = 1;
    return $this -> $_db -> add($data);
  }

  

}