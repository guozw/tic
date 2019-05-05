<?php
namespace Circle\Controller;
use Think\Controller;

class CircleController extends Controller{
  function __construct() {
    if(!session('login')) return show(-999,'未登录');
  }

  //获取所有圈子
  public function get_allCircle(){
    $list = D('Circle_info') -> get_all();
    if($list){
      // print_r($list);exit;
      foreach($list as &$circle){
        
        $circlecount = count(D('Circle') -> get_by_circleid($circle['id']));
        $circle['circlecount'] = $circlecount;
        $usercount = count(D('Circle_user') -> get_by_circleid($circle['id']));
        $circle['circleusercount'] = $usercount;
      }
      return show(0,'成功',$list);
    }else{
      return show(0,'暂无更多圈子');
    }
  }
  //加入圈子
  public function join_circle(){
    $userid = session('login');
    if(!$userid || $userid == '' )
      missing_login();
    $circleid = I('post.circleid');
    if( !$circleid || $circleid == '' ) missing_parameter();
    $ishave = D('Circle_info') -> get_by_id($circleid);
    if($ishave){
      $isjoin = D('Circle_user') -> get_by_userid($userid,$circleid);
      if($isjoin){
        return show(-1,'您已加入该圈子,请勿重复加入');
      }else{
        $data['userid'] = $userid;
        $data['circleid'] = $circleid;
        $res = D('Circle_user') -> add($data);
        if($res){
          return show(0,'成功',$res);
        }else{
          return show(-1,'失败');
        }
      }
    }else{
      return show(-1,'圈子不存在');
    }
  }
  //发布帖子
  public function add_circle(){
    $userid = session('login');
    if(!$userid || $userid == '' )
      missing_login();
    $circleid = I('post.circleid');
    $content = I('post.content');
    $pictures = I('post.pritures');
    if(!$circleid || $circleid == '' || !$content || $content == '') missing_parameter();
    if(!$pictures || $pictures == ''){
      $pictures = 0;
    }
    $data['userid'] = $userid;
    $data['circleid'] = $circleid;
    $data['content'] = $content;
    $data['pictures'] = $pictures;
    $res = D('Circle') -> add($data);
    if($res){
      return show(0,'发布成功',$res);
    }else{
      return show(-1,'发布失败');
    }
    
  }
  //查看圈子帖子
  public function get_circle(){
    $userid = session('login');
    if(!$userid || $userid == '' )
      missing_login();
    $circleid = I('post.circleid');
    if(!$circleid || $circleid == '') missing_parameter();
    $list = D('Circle') -> get_circle($circleid);
    $i = 0;
    // print_r($list);exit;
    foreach($list as &$circle){
      if($circle['userid'] == $userid){
        $circle['isfans'] = true;
      }else{
        if(D('Fans') -> is_fans($circle['userid'],$userid)){
          $circle['isfans'] = true;
        }else{
          $circle['isfans'] = false;
        }
      }
      if($circle['like'] != 0){
        $likearr = explode("|", $circle['like']);
        
        foreach($likearr as $likeuser){
          $usersortinfo = D('User') -> get_user_sortinfo($likeuser);
          // print_r($usersortinfo);
          $list[$i]['likes'][] = $usersortinfo;
          if($usersortinfo['id'] == $userid)
            $list[$i]['islike'] = true;
          else
            $list[$i]['islike'] = false;
        }
        
      }
      $remarklist = D('Circle_remark') -> get_by_circle($circle['id']);
      if($remarklist){
        foreach($remarklist as $remark){
          if($remark['userid'] == $userid)
            $remark['isdelete'] = 1;
          else
            $remark['isdelete'] = 0;
          $list[$i]['remarks'][] = $remark;
        }
      }
      $i ++;
    }
    $list = array_values($list);
    usort($list,'sortbytime');
    if($list){
      return show(0,'获取成功',$list);
    }else{
      return show(0,'暂无帖子');
    }
  }
  //点赞圈子帖子
  public function like_circle(){
    $userid = session('login');
    if(!$userid || $userid == '' )
      missing_login();
    $circiesid = I('post.tieziid');
    if(!$circiesid || $circiesid == '') missing_parameter();
    $Circle = D('Circle') -> get_one($circiesid);
    if($Circle){
      $data['like'] = $Circle['like'];
      if(strstr($data['like'],'|') || $data['like'] != 0){
        $data['like'] .= '|'.$userid;
      }else{
        $data['like'] = $userid;
      }
      $res = D('Circle') -> like_circles($circiesid,$data);
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
  public function unlike_circle(){
    $userid = session('login');
    if(!$userid || $userid == '' )
      missing_login();
    $circiesid = I('post.tieziid');
    if(!$circiesid || $circiesid == '') missing_parameter();
    $cirlces = D('Circle') -> get_one($circiesid);
    if($cirlces){
      $likearr = explode("|", $cirlces['like']);
      $count = count($likearr);
      for($i = 0 ; $i < $count ; $i ++){
        if($userid == $likearr[$i]){
          unset($likearr[$i]);
        }
      }
      $likearr = array_values($likearr);
      $data['like'] = implode("|", $likearr);
      $res = D('Circle') -> like_circles($circiesid,$data);
      if($res){
        return show(0,'取消点赞成功',$res);
      }else{
        return show(-1,'取消点赞失败');
      }
    }else{
      return show(-1,'此条朋友圈不存在');
    }
  }
  //评论圈子帖子
  public function remark_circle(){
    $userid = session('login');
    if(!$userid || $userid == '' )
      missing_login();
    $circiesid = I('post.tieziid');
    $content = I('post.content');
    if(!$circiesid || $circiesid == '' || !$content || $content == '') missing_parameter();
    $data['userid'] = $userid;
    $data['circleid'] = $circiesid;
    $data['content'] = $content;
    $res = D('Circle_remark') -> add($data);
    if($res){
      return show(0,'评论成功',$res);
    }else{
      return show(-1,'评论失败');
    }
  }
  //删除评论
  public function delremark(){
    $remarkid = I('post.remarkid');
    if(!$remarkid || $remarkid == '') missing_parameter();
    $res = D('Circle_remark') -> del($remarkid);
    if($res){
      return show(0,'删除成功',$res);
    }else{
      return show(-1,'删除失败');
    }
  }
  //管理员可删除帖子
  public function delcircle(){
    $userid = session('login');
    if(!$userid || $userid == '' )
      missing_login();
    $circiesid = I('post.tieziid');
    $circleid = I('post.circleid');
    if(!$circiesid || $circiesid == '' || !$circleid || $circleid == '') missing_parameter();
    $res = D('Circle_info') -> get_by_id($circleid);
    if($res){
      if($res['circle_admin'] == $userid){
        $resdel = D('Circle') -> del_circles($circiesid);
        if($resdel){
          return show(0,'成功',$resdel);
        }else{
          return show(-1,'失败,错误代码1_2');
        }
      }else{
        return show(-1,'您不是管理员无法删除帖子');
      }
    }else{
      return show(-1,'失败,错误代码1_1');
    }
  }
  //推荐圈子
  public function get_recommend_circle(){
    $list = D('Circle_info') -> get_all();
    if($list){
      // print_r($list);exit;
      foreach($list as &$circle){
        $circlecount = count(D('Circle') -> get_by_circleid($circle['id']));
        $circle['circlecount'] = $circlecount;
        $usercount = count(D('Circle_user') -> get_by_circleid($circle['id']));
        $circle['circleusercount'] = $usercount;
      }
      $count = count($list);
      if($count > 1){
        $seed = time();
        mt_srand($seed);
        $count = $count - 1;
        $index = rand(0,$count);
        $newlist = $list[$index];
        return show(0,'成功',$newlist);
      }else{
        return show(0,'成功',$list);
      }
    }else{
      return show(0,'暂无更多圈子');
    }
  }
  //获取自己加入的的圈子
  public function get_my_circle(){
    $userid = session('login');
    if(!$userid || $userid == '' )
      missing_login();
    $list = D('Circle_user') -> get_my_circle($userid);
    // print_r($list);exit;
    if($list){
      // print_r($list);exit;
      foreach($list as &$circle){
        $circlecount = count(D('Circle') -> get_by_circleid($circle['id']));
        $circle['circlecount'] = $circlecount;
        $usercount = count(D('Circle_user') -> get_by_circleid($circle['id']));
        $circle['circleusercount'] = $usercount;
      }
      return show(0,'成功',$list);
    }else{
      return show(0,'您未加入圈子');
    }
  }
  //退出圈子
  public function out_circle(){
    $userid = session('login');
    if(!$userid || $userid == '' )
      missing_login();
    $circleid = I('post.circleid');
    if( !$circleid || $circleid == '' ) missing_parameter();
    $ishave = D('Circle_info') -> get_by_id($circleid);
    if($ishave){
      $isjoin = D('Circle_user') -> get_by_userid($userid,$circleid);
      if($isjoin){
        $where['userid'] = $userid;
        $where['circleid'] = $circleid;
        $res = D('Circle_user') -> out_circle($where);
        if($res){
          return show(0,'成功',$res);
        }else{
          return show(-1,'失败');
        }
      }else{
        return show(-1,'您未加入该圈子');
        
      }
    }else{
      return show(-1,'圈子不存在');
    }
  }
  
  public function test(){
    print_r(session('login'));
  }
}