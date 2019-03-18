<?php

namespace Common\Model;
use Think\Model;

class Moments_remarkModel extends Model{
  private $_db = '';
  
  public function __construct(){
    $this -> $_db = M('moments_remark');     
  }

  public function add($data){
    $data['createtime'] = time();
    $data['createtimes'] = date('Y-m-d H:i:s',time());
    $data['status'] = 1;
    return $this -> $_db -> add($data);
  }
  public function get_by_moments($momentsid){
    $sql = "select mr.id as remarkid , mr.userid as userid , nickname , content , mr.createtime as createtime ,  mr.createtimes as createtimes
    FROM moments_remark mr
    LEFT JOIN `user` u on mr.userid = u.id
    WHERE mr.momentsid = '".$momentsid."' AND mr.status = 1";
    $result = $this -> $_db -> query($sql);
    return $result;
  }
  public function del($remarkid){
    $where['id'] = $remarkid;
    $data['status'] = -1;
    return $this -> $_db -> where($where) -> save($data);
  }
}