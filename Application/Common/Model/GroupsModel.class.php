<?php

namespace Common\Model;
use Think\Model;

class GroupsModel extends Model{
  private $_db = '';
  
  public function __construct(){
    $this -> $_db = M('groups');
  }
  public function add($data){
    $data['createtime'] = time();
    $data['createtimes'] = date('Y-m-d H:i:s',time());
    $data['status'] = 1;
    return $this -> $_db -> add($data);
  }
  public function get_list($userid){
    $sql = 'SELECT
              * 
            FROM
              groups 
            WHERE
              group_user REGEXP "^'.$userid.'," 
              OR group_user REGEXP ",'.$userid.'$" 
              OR group_user REGEXP ",'.$userid.',"';
    $Model = M();
    $result = $Model->query($sql);
    return $result;
  }
  public function get_by_id($groupid){
    $where['id'] = $groupid;
    $groupinfo = $this -> $_db -> where($where) -> find();
    return $groupinfo;
  }
  public function del_group($groupid){
    $where['id'] = $groupid;
    $res = $this -> $_db -> where($where) -> delete();
    return $res;
  }
  public function update_users($groupid,$users){
    $where['id'] = $groupid;
    $data['group_user'] = $users;
    $res = $this -> $_db -> where($where) -> save($data);
    return $res;
  }

}