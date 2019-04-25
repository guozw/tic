<?php

namespace Common\Model;
use Think\Model;

class CircleModel extends Model{
  private $_db = '';
  
  public function __construct(){
    $this -> $_db = M('circle');     
  }

  public function add($data){
    $data['like'] = 0;
    $data['createtime'] = time();
    $data['createtimes'] = date('Y-m-d H:i:s',time());
    $data['status'] = 1;
    return $this -> $_db -> add($data);
  }
  public function get_by_circleid($circleid){
    $where['circleid'] = $circleid;
    $where['status'] = 1;
    return $this -> $_db -> where($where) -> select();
  }

  public function get_circle($circleid){
    $sql = "select c.*,tt.nickname,tt.account,tt.portrait 
    FROM circle c
    left join `user` tt on c.userid = tt.id
    where  circleid = '".$circleid."' AND c.status = 1";
    $result = $this -> $_db -> query($sql);
    return $result;

  }
  public function get_one($circlesid){
    $where['id'] = $circlesid;
    $data['status'] = 1;
    return $this -> $_db -> where($where) -> find();
  }
  public function like_circles($circlesid,$data){
    $where['id'] = $circlesid;
    return $this -> $_db -> where($where) -> save($data);
  }
  public function del_circles($circlesid){
    $where['id'] = $circlesid;
    $data['status'] = -1;
    return $this -> $_db -> where($where) -> save($data);
  }
  public function get_circle_rank(){
    $sql = "select tt.id,tt.nickname,tt.account,tt.portrait,count(*) as circlenum
    FROM circle c
    left join `user` tt on c.userid = tt.id
    group by tt.id
    ORDER BY circlenum desc
    LIMIT 10";
    $result = $this -> $_db -> query($sql);
    return $result;
  }
}