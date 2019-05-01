<?php

namespace Common\Model;
use Think\Model;

class ReportuserModel extends Model{
  private $_db = '';
  
  public function __construct(){
    $this -> $_db = M('reportuser');     
  }

  public function add($data){
    $data['createtime'] = time();
    $data['createtimes'] = date('Y-m-d H:i:s',time());
    $data['status'] = 1;
    return $this -> $_db -> add($data);
  }
  public function admin_count(){
    $sql = "select 
            u.id as userid,account,nickname,r.content as content,
            r.createtime as createtime,r.createtimes as createtimes,r.status as status,r.id as id
            from reportuser r
            left join user u on (r.reportuserid = u.id)
            order by createtime";
    $Model = M();
    $result = $Model->query($sql);
    return $result;
  }
  public function admin_update($id,$data){
    $where['id'] = $id;
    return $this -> $_db -> where($where) -> save($data);
  }
  

}