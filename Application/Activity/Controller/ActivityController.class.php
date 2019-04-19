<?php
namespace Activity\Controller;
use Think\Controller;

class ActivityController extends Controller{
  function __construct() {
    if(!session('login')) return show(-999,'未登录');
  }

  //发布活动
  public function add_activity(){
    $userid = session('login');
    if(!$userid || $userid == '' )
      missing_login();
    $title = I('post.title');
    $type = I('post.type');
    $content = I('post.content');
    $starttimes = I('post.starttimes');
    $score = I('post.score');
    $maxuser = I('post.maxuser');
    if(!$title || $title == '' || !$type || $type == '' || !$content || $content == '' || !$maxuser || $maxuser == '' || !$starttimes || $starttimes == '' ) missing_parameter();
    if(!$score || $score == '') $score = 0;
    $userinfo = D('User') -> get_by_id($userid);
    if($userinfo){
      if($score <= $userinfo['score']){
        $userdata['score'] = $userinfo['score'] - $score;
        $resuser = D('User') -> updateUserinfo($userid,$userdata);
        if($resuser){
          //nothing
        }else{
          return show(-1,'扣除积分失败');
        }
      }else{
        return show(-1,'积分不足');
      }
    }else{
      return show(-1,'用户不存在');
    }
    
    $data['title'] = $title;
    $data['type'] = $type;
    $data['content'] = $content;
    $data['starttimes'] = $starttimes;
    $data['userid'] = $userid;
    $data['score'] = $score;
    $data['maxuser'] = $maxuser;
    switch($maxuser){
      case $maxuser >= 1 && $maxuser <= 5:
        $systemscore = 10;
        break;
      case $maxuser >= 6 && $maxuser <= 10:
        $systemscore = 20;
        break;
      case $maxuser >= 11 && $maxuser <= 15:
        $systemscore = 30;
        break;
      default :
        $systemscore = 50;
        break;
    }
    $data['systemscore'] = $systemscore;
    $data['joinuser'] = $userid;
    $res = D('Activity') -> add($data);
    if($res){
      return show(0,'成功',$res);
    }else{
      return show(-1,'失败,错误代码1_1');
    }
    
  }
  //参加活动
  public function join_activity(){
    $userid = session('login');
    if(!$userid || $userid == '' )
      missing_login();
    $activityid = I('post.activityid');
    if(!$activityid || $activityid == '' ) missing_parameter();
    $isend = D('Activity') -> get_by_id($activityid);
    // print_r($isend);exit;
    if($isend){
      if($isend['stage'] == 1){
        $joinuser = $isend['joinuser'];
        $joinarr = explode('|',$joinuser);
        if($isend['maxuser'] <= count($joinarr)){
          return show(-1,'该活动人数已满');
        }
        foreach($joinarr as $one){
          if($one == $userid){
            return show(-1,'您已参加该活动');
          }
        }
        $joinarr[] = $userid;
        $newjoinuser = implode('|',$joinarr);
        $data['joinuser'] = $newjoinuser;
        $res = D('Activity') -> join_activity($activityid,$data);
        if($res){
          return show(0,'成功');
        }else{
          return show(-1,'失败,错误代码2_1');
        }
      }else{
        return show(-1,'活动已开始或已结束');
      }
    }else{
      return show(-1,'活动不存在');
    }
  }
  //退出活动
  public function exit_activity(){
    $userid = session('login');
    if(!$userid || $userid == '' )
      missing_login();
    $activityid = I('post.activityid');
    if(!$activityid || $activityid == '' ) missing_parameter();
    $isend = D('Activity') -> get_by_id($activityid);
    if($isend){
      if($isend['userid'] == $userid){
        return show(-1,'您不能退出您自己的活动');
      }
      if($isend['stage'] == 1){
        $joinuser = $isend['joinuser'];
        $joinarr = explode('|',$joinuser);
        $flag = true;
        $count = count($joinarr);
        for($i = 0 ; $i < $count ; $i ++){
          if($joinarr[$i] == $userid){
            unset($joinarr[$i]);
            $flag = false;
          }
        }
        if($flag){
          return show(-1,'您不在该活动中');
        }
        $joinarr = array_values($joinarr);
        $newjoinuser = implode('|',$joinarr);
        $data['joinuser'] = $newjoinuser;
        $res = D('Activity') -> join_activity($activityid,$data);
        if($res){
          return show(0,'成功');
        }else{
          return show(-1,'失败,错误代码2_1');
        }
      }else{
        return show(-1,'活动已开始或已结束');
      }
    }else{
      return show(-1,'活动不存在');
    }
  }
  //结束活动
  public function finish_activity(){
    $userid = session('login');
    if(!$userid || $userid == '' )
      missing_login();
    $activityid = I('post.activityid');
    if(!$activityid || $activityid == '' ) missing_parameter();
    $isend = D('Activity') -> get_by_id($activityid);
    if($isend){
      if($isend['userid'] == $userid){
        if($isend['stage'] == 4){
          return show(-1,'活动已弃置');
        }
        $joinuser = $isend['joinuser'];
        $joinarr = explode('|',$joinuser);
        $count = count($joinarr);
        $userscore = floor($isend['score'] / $count);
        $addscore = $isend['systemscore'] + $userscore;
        foreach($joinarr as $one){
          $datascore = array('score'=>array('exp','score+'.$addscore));
          $userres = D('User') -> updateUserinfo($one,$datascore);
          if($userres){

          }else{
            return show(-1,'失败,错误代码3_1');
          }
        }
        $data['stage'] = 3;
        $res = D('Activity') -> join_activity($activityid,$data);
        if($res){
          return show(0,'成功');
        }else{
          return show(-1,'失败,错误代码3_2');
        }
      }else{
        return show(-1,'您不是活动创建者');
      }
    }else{
      return show(-1,'活动不存在');
    }
  }
  //废弃活动
  public function throw_activity(){
    $userid = session('login');
    if(!$userid || $userid == '' )
      missing_login();
    $activityid = I('post.activityid');
    if(!$activityid || $activityid == '' ) missing_parameter();
    $isend = D('Activity') -> get_by_id($activityid);
    if($isend){
      if($isend['userid'] == $userid){
        if($isend['stage'] == 3){
          return show(-1,'活动已结束');
        }
        $addscore = $isend['score'];
        $datascore = array('score'=>array('exp','score+'.$addscore));
        $userres = D('User') -> updateUserinfo($userid,$datascore);
        if($userres){

        }else{
          return show(-1,'失败,错误代码4_1');
        }
        $data['stage'] = 4;
        $res = D('Activity') -> join_activity($activityid,$data);
        if($res){
          return show(0,'成功');
        }else{
          return show(-1,'失败,错误代码4_2');
        }
      }else{
        return show(-1,'您不是活动创建者');
      }
    }else{
      return show(-1,'活动不存在');
    }
  }

  //获取活动列表
  public function get_activity_list(){
    $userid = session('login');
    if(!$userid || $userid == '' )
      missing_login();
    $type = I('post.type');//不传: 全部活动   1:我的  2:我参加的
    if(!$type || $type == '' ){
      //全部列表
      $list = D('Activity') -> get_list();
      if($list){
        foreach($list as &$one){
          if($one['userid'] == $userid){
            $one['isown'] = true;
          }else{
            $one['isown'] = false;
          }
          $joinuser = $one['joinuser'];
          $joinarr = explode('|',$joinuser);
          foreach($joinarr as &$userone){
            $usersortinfo = D('User') -> get_user_sortinfo($userone);
            $one['users'][] = $usersortinfo;
          }
        }
        return show(0,'成功',$list);
      }else{
        return show(0,'暂无活动');
      }
    }else if($type == 1){
      //我创建的
      $list = D('Activity') -> get_list_own($userid);
      if($list){
        foreach($list as &$one){
          $one['isown'] = true;
          $joinuser = $one['joinuser'];
          $joinarr = explode('|',$joinuser);
          foreach($joinarr as &$userone){
            $usersortinfo = D('User') -> get_user_sortinfo($userone);
            $one['users'][] = $usersortinfo;
          }
        }
        return show(0,'成功',$list);
      }else{
        return show(0,'暂无活动');
      }
    }else{
      //我加入的
      $list = D('Activity') -> get_list($userid);
      if($list){
        $count = count($list);
        for($i = 0 ; $i < $count ; $i ++){
          if($list[$i]['userid'] == $userid){
            $list[$i]['isown'] = true;
          }else{
            $list[$i]['isown'] = false;
          }
          $flag = 1;
          $joinuser = $list[$i]['joinuser'];
          $joinarr = explode('|',$joinuser);
          foreach($joinarr as &$userone){
            if($userone == $userid){
              $flag = 0;
            }
            $usersortinfo = D('User') -> get_user_sortinfo($userone);
            $list[$i]['users'][] = $usersortinfo;
          }
          if($flag){
            unset($list[$i]);
            
          }
        }
        $list = array_values($list);
        return show(0,'成功',$list);
      }else{
        return show(0,'暂无活动');
      }
    }
    
    
  }

  public function test(){
    print_r(session('login'));
  }
}