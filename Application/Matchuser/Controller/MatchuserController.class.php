<?php
namespace Matchuser\Controller;
use Think\Controller;

class MatchuserController extends Controller{
  function __construct() {
    if(!session('login')) return show(-999,'未登录');
  }
  //匹配规则：
  /*
   * 省市       + 10 
   * 地区       + 10
   * 星座       + 20
   * 生日 year  + 10
   * 生日 month + 10
   * 生日 day   + 20
   * 圈子       + 20
  */
  public function system_match(){
    $userid = session('login');
    if(!$userid || $userid == '' )
      missing_login();
    $mineinfo = D('User') -> get_by_id($userid);
    $minecircleres = D('Circle_user') -> get_my_circle($userid);
    $minecircle = array();
    if($minecircleres){
      foreach($minecircleres as $c){
        $minecircle[] = $c['id'];
      }
    }
    $userarr = array();
    $list = D('User') -> get_all_user($userid);
    foreach($list as $one){
      $matchscore = 0;
      if($one['province'] == $mineinfo['province']) $matchscore += 10;
      if($one['city'] == $mineinfo['city']) $matchscore += 10;
      if($one['constellation'] == $mineinfo['constellation']) $matchscore += 20;
      if(substr($one['birthday'],0,4) == substr($mineinfo['birthday'],0,4)) $matchscore += 10;
      if(substr($one['birthday'],5,2) == substr($mineinfo['birthday'],5,2)) $matchscore += 10;
      if(substr($one['birthday'],8,2) == substr($mineinfo['birthday'],8,2)) $matchscore += 20;
      $flag = 1;
      $cres = D('Circle_user') -> get_my_circle($one['id']);
      if($cres){
        foreach($cres as $c){
          if($flag){
            if(in_array($c['id'],$minecircle)){
              $flag = 0;
              $matchscore += 20;
            }
          }
        }
      }
      $one['matchscore'] = $matchscore .'%';
      $userarr[] = $one;
    }
    usort($userarr,'sortbymatch');
    if($userarr){
      return show(0,'成功',$userarr[0]);
    }else{
      return show(0,'暂无用户');
    }
  }
  
  public function hobby_match(){
    $userid = session('login');
    if(!$userid || $userid == '' )
      missing_login();
    $birthday = I('post.birthday');
    $province = I('post.province');
    $city = I('post.city');
    $constellation = I('post.constellation');
    $circle = I('post.circle');
    $h = $birthday + $province + $city + $constellation + $circle;
    if($h == 0){
      $this -> system_match();
    }
    $mineinfo = D('User') -> get_by_id($userid);
    $minecircleres = D('Circle_user') -> get_my_circle($userid);
    $minecircle = array();
    if($minecircleres){
      foreach($minecircleres as $c){
        $minecircle[] = $c['id'];
      }
    }
    $userarr = array();
    $list = D('User') -> get_all_user($userid);
    foreach($list as $one){
      $matchscore = 0;
      if($one['province'] == $mineinfo['province'] && $province) $matchscore += 10;
      if($one['city'] == $mineinfo['city']  && $city) $matchscore += 10;
      if($one['constellation'] == $mineinfo['constellation']  && $constellation) $matchscore += 20;
      if(substr($one['birthday'],0,4) == substr($mineinfo['birthday'],0,4)  && $birthday) $matchscore += 10;
      if(substr($one['birthday'],5,2) == substr($mineinfo['birthday'],5,2)  && $birthday) $matchscore += 10;
      if(substr($one['birthday'],8,2) == substr($mineinfo['birthday'],8,2)  && $birthday) $matchscore += 20;
      if($circle){
        $flag = 1;
        $cres = D('Circle_user') -> get_my_circle($one['id']);
        if($cres){
          foreach($cres as $c){
            if($flag){
              if(in_array($c['id'],$minecircle)){
                $flag = 0;
                $matchscore += 20;
              }
            }
          }
        }
      }
      
      $one['matchscore'] = $matchscore .'%';
      $userarr[] = $one;
    }
    usort($userarr,'sortbymatch');
    if($userarr){
      return show(0,'成功',$userarr[0]);
    }else{
      return show(0,'暂无用户');
    }
  }
 

}