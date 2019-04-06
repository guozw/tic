<?php

namespace Common\Model;
use Think\Model;

class ChathistoryModel extends Model{
  private $_db = '';
  
  public function __construct(){
    $this -> $_db = M('chathistory');
  }
  public function get_grouplastone($groupid){
    $where['to_user'] = $groupid;
    $where['type'] = 2;
    $list = $this -> $_db -> where($where) -> order('createtime desc') -> find();
    return $list;
  }
  public function get_userlastone($userid,$touserid){
    $sql = 'SELECT * 
          FROM chathistory 
          WHERE `type` = 1 
          AND ((send_user = "'.$userid.'" AND to_user = "'.$touserid.'") OR (to_user = "'.$userid.'" AND send_user = "'.$touserid.'"))
          ORDER BY createtime DESC';
    $Model = M();
    $result = $Model->query($sql);
    return $result;
  }
  

}