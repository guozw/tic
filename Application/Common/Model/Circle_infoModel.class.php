<?php

namespace Common\Model;
use Think\Model;

class Circle_infoModel extends Model{
  private $_db = '';
  
  public function __construct(){
    $this -> $_db = M('circle_info');     
  }

  public function add($data){
    $data['createtime'] = time();
    $data['createtimes'] = date('Y-m-d H:i:s',time());
    $data['status'] = 1;
    return $this -> $_db -> add($data);
  }
  public function get_all(){
    $sql = 'SELECT ci.id as id,ci.circle_name,ci.circle_picture,u.id as userid,u.nickname,u.account,u.portrait
            FROM circle_info ci
            LEFT JOIN user u ON u.id = ci.circle_admin
            WHERE  ci.`status` = 1';
    $result = $this -> $_db -> query($sql);
    return $result;
    // $where['status'] = 1;
    // return $this -> $_db -> where($where) -> select();
  }
  public function get_by_id($circleid){
    $where['id'] = $circleid;
    return $this -> $_db -> where($where) -> find();
  }



}