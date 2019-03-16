<?php
namespace Friend\Controller;
use Think\Controller;

class FriendController extends Controller{
  function __construct() {
    if(!session()) return show(-999,'未登录');
  }
  //添加好友
  public function addFriend(){
    $approvalid = I('post.approvalid');
    $userid = I('post.userid');
    $friendid = I('post.friendid');
    if(!$userid || !$friendid || $userid == '' || $friendid == '' || !$approvalid || $approvalid == '') missing_parameter();
    $minid = $userid < $friendid ? $userid : $friendid;
    $maxid = $userid < $friendid ? $friendid : $userid;
    $friendrow = D('Friend') -> get_one($minid,$maxid);
    if($friendrow){
      return show(-1,'对方已经是你的好友，请勿重复申请');
    }
    $res = D('Friend')->addFriend($minid,$maxid);
    if($res){
      $res = D('Approval') -> update_approval($approvalid);
      if($res){
        $arow = D('Approval') -> get_one($friendid,$userid);
        if($arow){
          D('Approval') -> update_approval($arow['id']);
        }
        return show(0,'添加成功',$res);
      }else{
        return show(-1,'添加成功但是修改状态失败');
      }
    }else{
      return show(-1,'添加失败');
    }
  }
  //获取好友列表
  public function get_friendList(){
    $userid = I('post.userid');
    if(!$userid || $userid == '') missing_parameter();
    $list = D('Friend') -> get_friendList($userid);
    if($list){
      return show(0,'获取成功',$list);
    }else{
      return show(-1,'无好友好友');
    }
  }
  //所有用户
  public function search_user(){
    $search = I('post.search');
    if(!$search || $search == '') missing_parameter();
    $list = D('User') -> find_user($search);
    if($list){
      return show(0,'搜索成功',$list);
    }else{
      return show(-1,'没有符合条件的找到用户');
    }
  }
  //申请添加好友
  public function add_approval(){
    $approvalid = I('post.myid');
    $userid = I('post.touserid');
    if(!$approvalid || $approvalid == '' || !$userid || $userid == '') missing_parameter();
    $minid = $approvalid < $userid ? $approvalid : $userid;
    $maxid = $approvalid < $userid ? $userid : $approvalid;
    $friendrow = D('Friend') -> get_one($minid,$maxid);
    if($friendrow){
      return show(-1,'对方已经是你的好友，请勿重复申请');
    }
    $row = D('Approval') -> get_one($userid,$approvalid);
    if($row){
      return show(-1,'您已申请，请勿重复申请');
    }
    $data['userid'] = $userid;
    $data['approvalid'] = $approvalid;
    $res = D('Approval') -> add($data);
    if($res){
      return show(0,'申请成功',$res);
    }else{
      return show(-1,'申请失败');
    }
  }
  //获取申请列表
  public function get_approval_list(){
    $userid = I('post.userid');
    if(!$userid || $userid == '') missing_parameter();
    $list = D('Approval') -> get_list($userid);
    if($list){
      return show(0,'获取成功',$list);
    }else{
      return show(-1,'无申请信息');
    }
  }
  //申请列表已读
  public function isRead(){
    $id = I('post.approvalid');
    if(!$id || $id == '') missing_parameter();
    $res = D('Approval') -> isRead($id);
    if($res){
      return show(0,'修改成功',$res);
    }else{
      return show(-1,'修改失败');
    }
  }



}
