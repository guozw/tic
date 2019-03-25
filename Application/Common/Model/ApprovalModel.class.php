<?php

namespace Common\Model;
use Think\Model;

class ApprovalModel extends Model{
  private $_db = '';
  
  public function __construct(){
    $this -> $_db = M('approval');     
  }
  public function get_list($userid){
    $sql = "select 
            u.id as userid,account,email,nickname,sex,portrait,
            score,province,city,phone,birthday,constellation,
            a.id as apprvoalid
            from approval a
            left join user u on (a.approvalid = u.id)
            where a.userid = '".$userid."'";
    $Model = M();
    $result = $Model->query($sql);
    return $result;
    // $where['userid'] = $userid;
    // return $this -> $_db -> join('left join user u on ') -> where($where) -> select();
  }
  public function get_noread_list($userid){
    $sql = "select 
            u.id as userid,account,email,nickname,sex,portrait,
            score,province,city,phone,birthday,constellation,
            a.id as apprvoalid
            from approval a
            left join user u on (a.approvalid = u.id)
            where a.userid = '".$userid."' AND a.isread <> 3";
    $Model = M();
    $result = $Model->query($sql);
    return $result;
    // $where['userid'] = $userid;
    // return $this -> $_db -> join('left join user u on ') -> where($where) -> select();
  }
  public function add($data){
    // $data['id'] = uuid();
    $data['createtime'] = time();
    $data['createtimes'] = date('Y-m-d H:i:s',time());
    $data['isread'] = 1;
    $data['status'] = 1;
    return $this -> $_db -> add($data);
  }
  public function get_one($userid,$approvalid){
    $where['userid'] = $userid;
    $where['approvalid'] = $approvalid;
    return $this -> $_db -> where($where) -> find();
  }
  public function isRead($id){
    $where['id'] = $id;
    $data['isread'] = 2;
    return $this -> $_db -> where($where) -> save($data);
  }
  public function update_approval($id){
    $where['id'] = $id;
    $data['isread'] = 3;
    return $this -> $_db -> where($where) -> save($data);
  }
}