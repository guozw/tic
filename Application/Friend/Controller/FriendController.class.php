<?php
namespace Friend\Controller;
use Think\Controller;

class FriendController extends Controller{
  function __construct() {
    if(!session()) return show(-999,'未登录');
  }
  //添加好友
  public function addFriend(){
    $userid = I('post.userid');
    $friendid = I('post.friendid');
    if(!$userid || !$friendid || $userid == '' || $friendid == '') missing_parameter();
    
    if($userid < $friendid){
      $data['userid'] = $userid;
      $data['friendid'] = $friendid;
    }else{
      $data['userid'] = $friendid;
      $data['friendid'] = $userid;
    }
    $res = D('Friend')->addFriend($data);
    if($res){
      show(0,'添加成功',$res);
    }else{
      show(-1,'添加失败');
    }
  }
  //获取好友列表
  


}
