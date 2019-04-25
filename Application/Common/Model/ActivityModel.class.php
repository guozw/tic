<?php

namespace Common\Model;
use Think\Model;

class ActivityModel extends Model{
  private $_db = '';
  
  public function __construct(){
    $this -> $_db = M('activity');
  }
  public function add($data){
    $data['createtime'] = time();
    $data['createtimes'] = date('Y-m-d H:i:s',time());
    $data['status'] = 1;
    return $this -> $_db -> add($data);
  }
  public function get_by_id($id){
    $where['id'] = $id;
    $list = $this -> $_db -> where($where) -> find();
    return $list;
  }
  public function join_activity($id,$data){
    $where['id'] = $id;
    return $this -> $_db -> where($where) -> save($data);
  }
  public function get_list(){
    $where['status'] = 1;
    $list = $this -> $_db -> where($where) -> select();
    return $list;
  }
  public function get_list_own($userid){
    $where['userid'] = $userid;
    $where['status'] = 1;
    $list = $this -> $_db -> where($where) -> select();
    return $list;
  }
  public function get_list_join($userid){
    $sql = 'SELECT
              * 
            FROM
            activity 
            WHERE
              joinuser REGEXP "^'.$userid.'|" 
              OR joinuser REGEXP "|'.$userid.'$" 
              OR joinuser = "'.$userid.'" 
              OR joinuser REGEXP "|'.$userid.'|"';
    $Model = M();
    $result = $Model->query($sql);
    return $result;
  }
  public function get_activity_rank(){
    $sql = "select tt.id,tt.nickname,tt.account,tt.portrait,count(*) as activitynum
    FROM activity c
    left join `user` tt on c.userid = tt.id
    group by tt.id
    ORDER BY activitynum desc
    LIMIT 10";
    $result = $this -> $_db -> query($sql);
    return $result;
  }
  
 
}