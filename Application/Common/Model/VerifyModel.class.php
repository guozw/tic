<?php

namespace Common\Model;
use Think\Model;

class VerifyModel extends Model{
  private $_db = '';
  //private $_db1 = '';

  public function __construct(){
      $this -> $_db = M('verify');
  }
  public function add($user,$code){
    $data['user'] = $user;
    $data['code'] = $code;
    $data['createtime'] = time();
    return $this -> $_db -> add($data);
  }
  public function get_code($user){
    $where['user'] = $user;
    return $this -> $_db -> where($where) -> find();
  }
  public function del($id){
    $condition['id'] = $id;
    $res = $this -> $_db -> where($condition) -> delete();
    return $res;
  }
}
