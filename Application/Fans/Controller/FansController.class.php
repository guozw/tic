<?php
namespace Fans\Controller;
use Think\Controller;

class FansController extends Controller{
  function __construct() {
    if(!session('login')) return show(-999,'未登录');
  }
  //成为粉丝
  public function become_fans(){
    $userid = session('login');
    if(!$userid || $userid == '' )
      missing_login();
    $touserid = I('post.touserid');
    if(!$touserid || $touserid == '' ) missing_parameter();
    if($userid == $touserid){
      return show(-1,'不能成为自己的粉丝');
    }else{
      $ishave = D('Fans') -> is_fans($touserid,$userid);
      if($ishave){
        return show(-1,'已经是粉丝,无需添加');
      }else{
        $res = D('Fans') -> become_fans($touserid,$userid);
        if($res){
          return show(0,'成功',$res);
        }else{
          return show(-1,'失败');
        }
      }
    }
  }
  //获取粉丝列表
  public function get_fans_list(){
    $userid = session('login');
    if(!$userid || $userid == '' )
      missing_login();
    $list = D('Fans') -> get_fans_list($userid);
    // print_r($list);exit;
    foreach($list as &$one){
      if($one['id'] == $userid){
        $one['isfans'] = true;
      }else{
        if(D('Fans') -> is_fans($one['id'],$userid)){
          $one['isfans'] = true;
        }else{
          $one['isfans'] = false;
        }
      }
    }
    if($list){
      return show(0,'成功',$list);
    }else{
      return show(0,'暂无粉丝');
    }
  }
  //获取关注人列表
  public function get_follow_list(){
    $userid = session('login');
    if(!$userid || $userid == '' )
      missing_login();
    $list = D('Fans') -> get_follow_list($userid);
    if($list){
      return show(0,'成功',$list);
    }else{
      return show(0,'暂无关注的人');
    }
  }




}
