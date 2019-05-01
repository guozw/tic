<?php
namespace Information\Controller;
use Think\Controller;

class InformationController extends Controller{
  function __construct() {
    if(!session('login')) return show(-999,'未登录');
  }
  
  public function get_information(){
    $userid = session('login');
    if(!$userid || $userid == '' )
      missing_login();
    // for($i = 0 ; $i < 10 ; $i ++){
    //   $list[] = array(
    //     'content'  => '暂无资讯',
    //     'istop' => 0,
    //     'createtimes' => date('Y-m-d H:i:s',time())
    //   );
    // }
    $list = D('Information') -> get_list();
    if($list){
      // $i = 0;
      // foreach($real_list as $one){
      //   $list[$i] = $one;
      //   $i ++;
      // }
      return show(0,'成功',$list);
    }else{
      return show(0,'暂无资讯');
    }
  }

  //
  // public function add_report(){
  //   $userid = session('login');
  //   if(!$userid || $userid == '' )
  //     missing_login();
  //   $reportuser = I('post.reportuser');
  //   $content = I('post.content');
  //   if( !$reportuser || $reportuser == '' || !$content || $content == '') missing_parameter();
  //   $data['userid'] = $userid;
  //   $data['reportuserid'] = $reportuser;
  //   $data['content'] = $content;
  //   $res = D('Reportuser') -> add($data);
  //   if($res){
  //     return show(0,'成功',$res);
  //   }else{
  //     return show(-1,'失败');
  //   }
  // }
}