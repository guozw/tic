<?php

namespace Common\Model;
use Think\Model;

class ChatlistModel extends Model{
  private $_db = '';
  
  public function __construct(){
    $this -> $_db = M('chatlist');
  }
  public function get_list($userid){
    $where['userid'] = $userid;
    $list = $this -> $_db -> where($where) -> find();
    return $list;
  }
  public function add($data){
    $data['createtime'] = time();
    $data['createtimes'] = date('Y-m-d H:i:s',time());
    $data['status'] = 1;
    return $this -> $_db -> add($data);
  }
  public function update_list($userid,$data){
    $where['userid'] = $userid;
    return $this -> $_db -> where($where) -> save($data);
  }
}