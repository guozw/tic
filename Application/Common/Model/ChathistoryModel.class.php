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
          ORDER BY createtime DESC limit 1';
    $Model = M();
    $result = $Model->query($sql);
    return $result;
  }
  public function get_grouphistory($groupid){
    $sql = 'SELECT c.id as historyid,c.type as type ,c.send_user as send_user,u.account as send_account,u.nickname as send_nickname,u.portrait as portrait,
                    c.to_user as touser,c.message as message,c.isread as isread,c.createtimes as createtimes
          FROM chathistory c
          LEFT JOIN `user` u ON u.id = c.send_user
          WHERE c.type = 2 AND c.to_user = "'.$groupid.'"
          ORDER BY c.createtime ASC';
    $Model = M();
    $result = $Model->query($sql);
    return $result;

  }
  public function get_userhistory($userid,$touserid){
    $sql = 'SELECT c.id as historyid,c.type as type ,c.send_user as send_user,u.account as send_account,u.nickname as send_nickname,u.portrait as portrait,
                    c.to_user as touser,c.message as message,c.isread as isread,c.createtimes as createtimes
          FROM chathistory c
          LEFT JOIN `user` u ON u.id = c.send_user
          WHERE (c.to_user = "'.$userid.'" AND c.send_user = "'.$touserid.'" ) OR (c.to_user = "'.$touserid.'" AND c.send_user = "'.$userid.'" )
          ORDER BY c.createtime ASC';
    $Model = M();
    $result = $Model->query($sql);
    return $result;
  }
  public function isread_history($senduser,$touser){
    $where['send_user'] = $senduser;
    $where['to_user'] = $touser;
    $where['tpye'] = 1;
    $data['isread'] = 1;
    $res = $this -> $_db -> where($where) -> save($data);
    return $res;
  }
  

}