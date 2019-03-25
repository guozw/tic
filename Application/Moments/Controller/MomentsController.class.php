<?php
namespace Moments\Controller;
use Think\Controller;

class MomentsController extends Controller{
  function __construct() {
    // if(!session()) return show(-999,'未登录');
  }
  //发布朋友圈
  public function add_moments(){
    $userid = I('post.userid');
    $content = I('post.content');
    $pictures = I('post.pritures');
    $blacklist = I('post.blacklist');
    if(!$userid || $userid == '' || !$content || $content == '') missing_parameter();
    if(!$pictures || $pictures == ''){
      $pictures = 0;
    }
    if(!$blacklist || $blacklist == ''){
      $blacklist = 0;
    }
    $data['userid'] = $userid;
    $data['content'] = $content;
    $data['pictures'] = $pictures;
    $data['blacklist'] = $blacklist;
    $res = D('Moments') -> add($data);
    if($res){
      return show(0,'发布成功',$res);
    }else{
      return show(-1,'发布失败');
    }
    
  }
  //查看朋友圈
  public function get_moments(){
    $userid = I('post.userid');
    if(!$userid || $userid == '') missing_parameter();
    $list = D('Moments') -> get_moments($userid);
    $i = 0;
    foreach($list as &$moments){
      
      if($moments['like'] != 0){
        $likearr = explode("|", $moments['like']);
        foreach($likearr as $likeuser){
          $usersortinfo = D('User') -> get_user_sortinfo($likeuser);
          // print_r($usersortinfo);
          $list[$i]['likes'][] = $usersortinfo;
          if($usersortinfo['id'] == $userid)
            $list[$i]['islike'] = 1;
        }
        
      }
      $remarklist = D('Moments_remark') -> get_by_moments($moments['id']);
      if($remarklist){
        foreach($remarklist as $remark){
          if($remark['userid'] == $userid)
            $remark['isdelete'] = 1;
          else
            $remark['isdelete'] = 0;
          $list[$i]['remarks'][] = $remark;
        }
      }
      $blacklist = $moments['blacklist'];
      $blacklist = explode("|", $blacklist);
      foreach($blacklist as $user){
        if($user == $userid)
          unset($list[$i]);
      }
      
      $i ++;
    }
    $list = array_values($list);
    usort($list,'sortbytime');
    if($list){
      return show(0,'获取成功',$list);
    }else{
      return show(-1,'暂无朋友圈');
    }
  }
  //点赞朋友圈
  public function like_moments(){
    $userid = I('post.userid');
    $momentsid = I('post.momentsid');
    if(!$userid || $userid == '' || !$momentsid || $momentsid == '') missing_parameter();
    $moments = D('Moments') -> get_one($momentsid);
    if($moments){
      $data['like'] = $moments['like'];
      if(strstr($data['like'],'|') || $data['like'] != 0){
        $data['like'] .= '|'.$userid;
      }else{
        $data['like'] = $userid;
      }
      $res = D('Moments') -> like_moments($momentsid,$data);
      if($res){
        return show(0,'点赞成功',$res);
      }else{
        return show(-1,'点赞失败');
      }
    }else{
      return show(-1,'此条朋友圈不存在');
    }
  }
  //取消点赞
  public function unlike_moments(){
    $userid = I('post.userid');
    $momentsid = I('post.momentsid');
    if(!$userid || $userid == '' || !$momentsid || $momentsid == '') missing_parameter();
    $moments = D('Moments') -> get_one($momentsid);
    if($moments){
      $likearr = explode("|", $moments['like']);
      $count = count($likearr);
      for($i = 0 ; $i < $count ; $i ++){
        if($userid == $likearr[$i]){
          unset($likearr[$i]);
        }
      }
      $likearr = array_values($likearr);
      $data['like'] = implode("|", $likearr);
      $res = D('Moments') -> like_moments($momentsid,$data);
      if($res){
        return show(0,'取消点赞成功',$res);
      }else{
        return show(-1,'取消点赞失败');
      }
    }else{
      return show(-1,'此条朋友圈不存在');
    }
  }
  //评论朋友圈
  public function remark_moments(){
    $userid = I('post.userid');
    $momentsid = I('post.momentsid');
    $content = I('post.content');
    if(!$userid || $userid == '' || !$momentsid || $momentsid == '') missing_parameter();
    $data['userid'] = $userid;
    $data['momentsid'] = $momentsid;
    $data['content'] = $content;
    $res = D('Moments_remark') -> add($data);
    if($res){
      return show(0,'评论成功',$res);
    }else{
      return show(-1,'评论失败');
    }
  }
  public function test(){
    $remarklist = D('Moments_remark') -> get_by_moments(3);
    print_r($remarklist);
  }
  //删除评论
  public function delremark(){
    $remarkid = I('post.remarkid');
    if(!$remarkid || $remarkid == '') missing_parameter();
    $res = D('Moments_remark') -> del($remarkid);
    if($res){
      return show(0,'删除成功',$res);
    }else{
      return show(-1,'删除失败');
    }
  }


}