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

  

}