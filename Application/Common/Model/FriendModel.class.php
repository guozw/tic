<?php

namespace Common\Model;
use Think\Model;

class FriendModel extends Model{
  private $_db = '';
  
  public function __construct(){
    $this -> $_db = M('friend');     
  }
  public function addFriend($minid,$maxid){
    // $data['id'] = uuid();
    $data['userid'] = $minid;
    $data['friendid'] = $maxid;
    $data['createtime'] = time();
    $data['createtimes'] = date('Y-m-d H:i:s',time());
    $data['status'] = 1;
    return $this -> $_db -> add($data);
  }
  
  public function get_one($userid,$friendid){
    $where['userid'] = $userid;
    $where['friendid'] = $friendid;
    return $this -> $_db -> where($where) -> find();
  }
  public function get_friendList($userid){
    // $sql = "select id,account,email,nickname,sex,portrait,score,province,city,phone,birthday,constellation,describe,createtime,createtimes from ";
    $sql = "select u.id as id ,account,email,nickname,sex,portrait,score,province,city,phone,birthday,constellation,`describe`,u.createtimes as createtimes
    from friend f
    LEFT JOIN `user` u on f.userid = u.id OR f.friendid = u.id
    where (f.userid = '".$userid."' OR f.friendid = '".$userid."')
    AND u.id <> '".$userid."'";
    $result = $this -> $_db -> query($sql);
    return $result;
  }
 

}