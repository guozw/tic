<?php

namespace Common\Model;
use Think\Model;

class MomentsModel extends Model{
  private $_db = '';
  
  public function __construct(){
    $this -> $_db = M('moments');     
  }

  public function add($data){
    $data['like'] = 0;
    $data['createtime'] = time();
    $data['createtimes'] = date('Y-m-d H:i:s',time());
    $data['status'] = 1;
    return $this -> $_db -> add($data);
  }
  public function get_moments($userid){
    $sql = "select * 
    FROM moments
    where userid IN
    (
      select u.id as id
      from friend f
      LEFT JOIN `user` u on f.userid = u.id OR f.friendid = u.id
      where (f.userid = '".$userid."' OR f.friendid = '".$userid."')
      AND u.id <> '".$userid."') AND status = 1";
    $result = $this -> $_db -> query($sql);
    return $result;
  }
  public function get_one($momentid){
    $where['id'] = $momentid;
    $data['status'] = 1;
    return $this -> $_db -> where($where) -> find();
  }
  public function like_moments($momentid,$data){
    $where['id'] = $momentid;
    return $this -> $_db -> where($where) -> save($data);
  }

}