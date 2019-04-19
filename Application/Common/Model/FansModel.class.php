<?php

namespace Common\Model;
use Think\Model;

class FansModel extends Model{
  private $_db = '';
  
  public function __construct(){
    $this -> $_db = M('fans');     
  }

  public function is_fans($userid,$fansid){
    $where['userid'] = $userid;
    $where['fansid'] = $fansid;
    $where['status'] = 1;
    return $this -> $_db -> where($where) -> find();
  }

  public function become_fans($userid,$fansid){
    $data['userid'] = $userid;
    $data['fansid'] = $fansid;
    $data['createtime'] = time();
    $data['createtimes'] = date('Y-m-d H:i:s',time());
    $data['status'] = 1;
    return $this -> $_db -> add($data);
  }
  public function get_fans_list($userid){
    $sql = "select u.id as id ,account,email,nickname,sex,portrait,score,province,city,phone,birthday,constellation,`describe`,u.createtimes as createtimes
    from fans f
    LEFT JOIN `user` u on f.fansid = u.id 
    where f.userid = '".$userid."'";
    $result = $this -> $_db -> query($sql);
    return $result;
  }
  public function get_follow_list($userid){
    $sql = "select u.id as id ,account,email,nickname,sex,portrait,score,province,city,phone,birthday,constellation,`describe`,u.createtimes as createtimes
    from fans f
    LEFT JOIN `user` u on f.fansid = u.id 
    where f.fansid = '".$userid."'";
    $result = $this -> $_db -> query($sql);
    return $result;
  }

  
  public function get_one($userid,$friendid){
    $where['userid'] = $userid;
    $where['friendid'] = $friendid;
    return $this -> $_db -> where($where) -> find();
  }
  public function get_friendList($userid){
    $sql = "select u.id as id ,account,email,nickname,sex,portrait,score,province,city,phone,birthday,constellation,`describe`,u.createtimes as createtimes
    from friend f
    LEFT JOIN `user` u on f.userid = u.id OR f.friendid = u.id
    where (f.userid = '".$userid."' OR f.friendid = '".$userid."')
    AND u.id <> '".$userid."'";
    $result = $this -> $_db -> query($sql);
    return $result;
  }
  public function delOne($userid,$friendid){
    $where['userid'] = $userid;
    $where['friendid'] = $friendid;
    return $this -> $_db -> where($where) -> delete();
  }

}