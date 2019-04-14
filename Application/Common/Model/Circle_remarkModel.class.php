<?php

namespace Common\Model;
use Think\Model;

class Circle_remarkModel extends Model{
  private $_db = '';
  
  public function __construct(){
    $this -> $_db = M('circle_remark');     
  }

  public function add($data){
    $data['createtime'] = time();
    $data['createtimes'] = date('Y-m-d H:i:s',time());
    $data['status'] = 1;
    return $this -> $_db -> add($data);
  }

  public function get_by_circle($cid){
    $sql = "select cr.id as remarkid , cr.userid as userid , nickname , content , cr.createtime as createtime ,  cr.createtimes as createtimes
    FROM circle_remark cr
    LEFT JOIN `user` u on cr.userid = u.id
    WHERE cr.circleid = '".$cid."' AND cr.status = 1";
    $result = $this -> $_db -> query($sql);
    return $result;

  }
  public function del($remarkid){
    $where['id'] = $remarkid;
    $data['status'] = -1;
    return $this -> $_db -> where($where) -> save($data);
  }
}