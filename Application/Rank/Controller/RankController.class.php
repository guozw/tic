<?php
namespace Rank\Controller;
use Think\Controller;

class RankController extends Controller{
  function __construct() {
    if(!session('login')) return show(-999,'未登录');
  }
  public function get_rank(){
    $userid = session('login');
    if(!$userid || $userid == '' )
      missing_login();
    $type = I('post.type'); // 1.积分排行榜  2.圈子排行榜  3.动态排行榜  4.活动排行榜
    if(!$type || $type == '' ) missing_parameter();
    
    if($type == 1){
      $list = array();
      for($i = 0 ; $i < 10 ; $i ++){
        $list[] = array(
          'id'       => 0,
          'account'  => 0,
          'nickname' => '暂无用户',
          'portrait' => 'http://tic.codergzw.com/Public/img/portraits/mandefault.png',
          'score'    => 0
        );
      }
      $real_list = D('User') -> get_score_rank();
    }else if($type == 2){
      $list = array();
      for($i = 0 ; $i < 10 ; $i ++){
        $list[] = array(
          'id'        => 0,
          'account'   => 0,
          'nickname'  => '暂无用户',
          'portrait'  => 'http://tic.codergzw.com/Public/img/portraits/mandefault.png',
          'circlenum' => 0
        );
      }
      $real_list = D('Circle') -> get_circle_rank();
    }else if($type == 3){
      $list = array();
      for($i = 0 ; $i < 10 ; $i ++){
        $list[] = array(
          'id'        => 0,
          'account'   => 0,
          'nickname'  => '暂无用户',
          'portrait'  => 'http://tic.codergzw.com/Public/img/portraits/mandefault.png',
          'momentsnum' => 0
        );
      }
      $real_list = D('Moments') -> get_moments_rank();
    }else{
      $list = array();
      for($i = 0 ; $i < 10 ; $i ++){
        $list[] = array(
          'id'        => 0,
          'account'   => 0,
          'nickname'  => '暂无用户',
          'portrait'  => 'http://tic.codergzw.com/Public/img/portraits/mandefault.png',
          'activitynum' => 0
        );
      }
      $real_list = D('Activity') -> get_activity_rank();
    }
    $i = 0;
    foreach($real_list as $one){
      $list[$i] = $one;
      $i ++;
    }
    return show(0,'成功',$list);
  }

}