<?php

namespace Common\Model;
use Think\Model;

class Circle_userModel extends Model{
  private $_db = '';
  
  public function __construct(){
    $this -> $_db = M('circle_user');     
  }

  public function add($data){
    $data['createtime'] = time();
    $data['createtimes'] = date('Y-m-d H:i:s',time());
    $data['status'] = 1;
    return $this -> $_db -> add($data);
  }
  public function get_by_userid($userid,$circleid){
    $where['userid'] = $userid;
    $where['circleid'] = $circleid;
    $where['status'] = 1;
    return $this -> $_db -> where($where) -> find();
  }
  public function get_by_circleid($circleid){
    $where['circleid'] = $circleid;
    $where['status'] = 1;
    return $this -> $_db -> where($where) -> select();
  }


}