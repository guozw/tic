<?php
namespace Admin\Controller;
use Think\Controller;

class AdminController extends Controller{
  public function test(){
    $userarr = D('User')->admin_count();
    return show(0,'成功',$userarr);
  }
  public function jubao(){
    $userarr = D('Reportuser')->admin_count();
    return show(0,'成功',$userarr);
  }
  public function fengjin(){
    $userid = I('post.userid');
    $data['status'] = -1;
    $res = D('User') -> updateUserinfo($userid,$data);
    return show(0,'成功',$res);
  }
  public function jiefeng(){
    $userid = I('post.userid');
    $data['status'] = 1;
    $res = D('User') -> updateUserinfo($userid,$data);
    return show(0,'成功',$res);
  }
  public function feng(){
    $id = I('post.id');
    $userid = I('post.userid');
    $data['status'] = 2;
    $res = D('Reportuser') -> admin_update($id,$data);
    $data['status'] = -1;
    $res = D('User') -> updateUserinfo($userid,$data);
    return show(0,'成功',$res);
  }
  public function unfeng(){
    $id = I('post.id');
    $data['status'] = 3;
    $res = D('Reportuser') -> admin_update($id,$data);
    return show(0,'成功',$res);
  }
  public function circle_all(){
    $list = D('Circle') -> admin_circle();
    return show(0,'成功',$list);
  }
  public function delcircle(){
    $id = I('post.id');
    $list = D('Circle') -> del_circles($id);
    return show(0,'成功',$list);
  }
  public function get_information(){
    $list = D('Information') -> get_admin_list();
    return show(0,'成功',$list);
  }
  public function update_information(){
    $id = I('post.id');
    $field = I('post.field');
    $value = I('post.value');
    if($field == 'content'){
      $data['content'] = $value;
    }
    if($field == 'istop'){
      $data['istop'] = $value;
    }
    if($data){
      $res = D('Information') -> admin_update($id,$data);
      return show(0,'成功',$res);
    }else{
      return show(0,'成功');
    }
  }
  public function add_information(){
    $content = I('post.content');
    $data['content'] = $content;
    $data['istop'] = 99;
    $res = D('Information') -> add($data);
    return show(0,'成功',$res);
  }
}