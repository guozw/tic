<?php

namespace Common\Model;
use Think\Model;

class FriendModel extends Model{
  private $_db = '';
  
  public function __construct(){
    $this -> $_db = M('friend');     
  }
  public function addFriend($data){
    $data['id'] = uuid();
    $data['createtime'] = time();
    $data['createtimes'] = date('Y-m-d H:i:s',time());
    $data['status'] = 1;
    return $this -> $_db -> add($data);
  }
  public function get_friendList($userid){
    return $this -> $_db -> field('id,account,email,nickname,sex,portrait,score,province,city,phone,birthday,constellation,describe,createtime,createtimes,status') -> where("userid = '".$userid."' OR friendid = '".$userid."'") -> select();
  }

}