<?php
namespace Reportuser\Controller;
use Think\Controller;

class ReportuserController extends Controller{
  function __construct() {
    if(!session('login')) return show(-999,'未登录');
  }
  
  public function add_report(){
    $userid = session('login');
    if(!$userid || $userid == '' )
      missing_login();
    $reportuser = I('post.reportuser');
    $content = I('post.content');
    if( !$reportuser || $reportuser == '' || !$content || $content == '') missing_parameter();
    $data['userid'] = $userid;
    $data['reportuserid'] = $reportuser;
    $data['content'] = $content;
    $res = D('Reportuser') -> add($data);
    if($res){
      return show(0,'成功',$res);
    }else{
      return show(-1,'失败');
    }
  }
}