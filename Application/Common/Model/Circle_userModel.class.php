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
  public function get_my_circle($userid){
    $sql = "SELECT ci.id as id,ci.circle_name,ci.circle_picture,u.id as userid,u.nickname,u.account,u.portrait
      FROM circle_info ci
      LEFT JOIN user u ON u.id = ci.circle_admin
      LEFT JOIN circle_user cu ON cu.circleid = ci.id
      WHERE  cu.`status` = 1 AND ci.`status` = 1 AND cu.userid = '".$userid."'";
    $result = $this -> $_db -> query($sql);
    return $result;
  }
  public function out_circle($where){
    $data['status'] = -1;
    return $this -> $_db -> where($where) -> save($data);
  }
}