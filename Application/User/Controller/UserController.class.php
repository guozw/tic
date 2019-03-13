<?php
namespace User\Controller;
use Think\Controller;

class UserController extends Controller{
  // function __construct() {
  //   if(!session()) return show(-999,'未登录');
  // }
  
  public function register(){
    $email = I('post.email');
    $nickname = I('post.nickname');
    $password = I('post.password');
    $code = I('post.code');
    if(!$email || !$nickname || !$password || !$code || $email == '' || $nickname == '' || $password == '' || $code == '' ){
      missing_parameter();
    }
    $verifyinfo = D('Verify') -> get_code($email);
    if(!$verifyinfo){
      return show(-1,'非法请求,请获取验证码后再注册');
    }
    if($code != $verifyinfo['code']){
      return show(-1,'验证码不正确');
    }
    $user = D('User') -> get_by_email($email);
    if($user){
      return show(-1,'邮箱已注册');
    }
    
    $password = SafePassword($password.$code);
    $data['id'] = uuid();
    $data['email'] = $email;
    $data['account'] = get_account();
    $data['nickname'] = $nickname;
    $data['password'] = $password;
    $data['code'] = $code;
    $data['score'] = 0;
    $data['phone'] = '空';
    $data['birthday'] = '1997-01-01';
    $data['constellation'] = '摩羯座';
    $data['describe'] = '这个人很懒什么都没留下';
    $data['province'] = '天津';
    $data['city'] = '天津市';
    $data['createtime'] = time();
    $data['createtimes'] = date('Y-m-d H:i:s',time());
    $newuser = D('User') -> add($data);
    if($newuser){
      return show(0,'注册成功',$newuser);
    }else{
      return show(-1,'系统错误');
    }
  }

  public function login(){
    $username = I('post.username');
    $password = I('post.password');
    if(!$username || !$password || $username == '' || $password == '' ){
      missing_parameter();
    }
    $userinfo = D('User')->get_by_username($username);
    if($userinfo){
      $user = D('User')->login($username,$password,$userinfo['code']);
      if($user){
        session('login',$user['id']);
        show(0,'登录成功',$user);
        
      }else{
        show(-1,'密码不正确');
      }
    }else{
      show(-1,'用户不存在');
    }
    
  }

  public function verify(){
    $mailto = I('post.mailto');
    $type = I('post.type');
    if(!$mailto || !$type){
      missing_parameter();
    }else{
      if(verifyEmail($mailto)){
        $verifyinfo = D('Verify') -> get_code($mailto);
        if($verifyinfo){
          if(time() - $verifyinfo['createtime'] < 60)
            return show(-1,'验证码请求过于频繁,请过一分钟后再试');
          else{
            D('Verify') -> del($verifyinfo['id']);
          }
        }
        $verifycode = generate_code();
        if($type == 1){
          $send = sendMail("Talk Is Cheap 注册验证码", "感谢您注册Talk Is Cheap!<br>您的验证码是:".$verifycode."<br>制作团队：徐弥阳 郭志伟 文亚兰",$mailto,1);
          if($send){
            $data = D('Verify') -> add($mailto,$verifycode);
            return show(0,'success',$data);
          }else{
            return show(-1,'mail server is error');
          }
        }else{
          $send = sendMail("Talk Is Cheap 找回密码验证码", "Talk Is Cheap!<br>您的验证码是:".$verifycode."<br>:( 您不要忘记新密码了哦<br>制作团队：徐弥阳 郭志伟 文亚兰",$mailto,2);
          if($send){
            $data = D('Verify') -> add($mailto,$verifycode);
            return show(0,'success',$data);
          }else{
            return show(-1,'mail server is error');
          }
          echo '找回密码';
        }
      }else{
        return show(-1,'邮箱格式错误');
      }
    }
  }

  public function logout(){
    session('login',null);
    return show(-1,'退出成功');
  }

  public function verify_email(){
    $email = I('post.email');
    if(!$email || $email == '') missing_parameter();
    if(verifyEmail($email)){
      $user = D('User') -> get_by_email($email);
      if($user){
        return show(0,'获取成功',$user['id']);
      }else{
        return show(-1,'邮箱不存在');
      }
    }else{
      return show(-1,'邮箱格式错误');
    }
  }
  public function change_password(){
    $email = I('post.email');
    $password = I('post.password');
    if(!$email || !$password || $email == '' || $password == '') missing_parameter();
    $user = D('User') -> get_by_email($email);
    $verifyinfo = D('Verify') -> get_code($email);
    $code = $verifyinfo['code'];
    $password = SafePassword($password.$code);
    $res = D('User') -> change_password($email,$password,$code);
    if($res){
      return show(0,'修改成功',$res);
    }else{
      return show(-1,'修改失败');
    }
  }

  public function get_userinfo(){
    if(!session()) return show(-999,'未登录');
    $userid = I('post.userid');
    if(!$userid || $userid == ''){
      missing_parameter();
    }
    $user = D('User') -> get_by_id($userid);
    if($user){
      return show(0,'获取成功',$user);
    }else{
      return show(-1,'获取失败');
    }
  }
  public function update_userinfo(){
    
  }

  public function test(){
    $birthday = I('post.birthday');
    $month = substr($birthday,5,2);
    $day = substr($birthday,8,2);
    echo get_constellation($month,$day);
    
    // print_r(session());
  }  
}

