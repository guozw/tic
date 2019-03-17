<?php

function show($s, $m, $d=array()) {
    $result = array(
        'code' => $s,
        'message' => $m,
        'data' => $d
    );
    header('Access-Control-Allow-Origin:*');
  // // 响应类型  
  header('Access-Control-Allow-Methods:*');
  // 响应头设置  
  
  header('Access-Control-Allow-Origin:*'); 
  header('Access-Control-Allow-Headers:content-type'); 
  header('Access-Control-Request-Method:GET,POST,OPTIONS'); 
  if(strtoupper($_SERVER['REQUEST_METHOD'])== 'OPTIONS'){ 
    exit;
  }
    exit(json_encode($result, JSON_UNESCAPED_UNICODE));
}

function missing_parameter(){
  return show(-1,'缺少参数');
}

function pageShow($s,$m,$c,$p,$d=array()){
    $result = array(
        'status' => $s,
        'len'=>count($d),
        'count' => $c,
        'page' => $p,
        'message' =>$m,
        'data' =>$d
    );
    exit(json_encode($result,JSON_UNESCAPED_UNICODE));
}

function SafePassword($p) {
    return MD5($p);
}
function uuid($prefix = '')
{
    $chars = md5(uniqid(mt_rand(), true));
    $uuid  = substr($chars,0,8) . '-';
    $uuid .= substr($chars,8,4) . '-';
    $uuid .= substr($chars,12,4) . '-';
    $uuid .= substr($chars,16,4) . '-';
    $uuid .= substr($chars,20,12);
    return $prefix . $uuid;
}
function check_verify($code, $id = ''){
    $verify = new \Think\Verify;
    return $verify->check($code, $id);
}
function extend($file_name){
    $extend = pathinfo($file_name);
    $extend = strtolower($extend["extension"]);
    return $extend;
}
function accessToken(){
    $app_id = C('app_id');
    $app_secret = C('app_secret');
    $data = array('app_id' => $app_id,'app_secret' => $app_secret);  //定义参数

    $data = http_build_query($data);  //把参数转换成URL数据

    $aContext = array('http' => array('method' => 'POST',

        'header'  => 'Content-type: application/x-www-form-urlencoded',

        'content' => $data ));

    $cxContext  = stream_context_create($aContext);

    $sUrl = C('getTokenUrl'); //此处必须为完整路径4
    $d = file_get_contents($sUrl,false,$cxContext);

    return $d;
}
function sendMail($emailsubject,$emailbody,$smtpemailto,$type) {
  vendor('phpmailer.class#phpmailer');
  $mail = new PHPMailer(); //实例化
  $mail->CharSet = "utf-8";                // 编码格式
  $mail->IsSMTP(); // 启用SMTP

  $mail->SMTPAuth   = true;                   // 必填，SMTP服务器是否需要验证，true为需要，false为不需要
  $mail->Host       = "smtp.qq.com";         // 必填，设置SMTP服务器
  if($type == 1){
    $mail->Username   = "1239236430@qq.com";           // 必填，开通SMTP服务的邮箱；
    $mail->Password   = "qhnrbjggesisiaaj";         // 必填， 以上邮箱对应的密码
    $mail->From       = "1239236430@qq.com";       // 必填，发件人Email
    
  }else{
    $mail->Username   = "511287680@qq.com";           // 必填，开通SMTP服务的邮箱；
    $mail->Password   = "ftjpspnoirxbbggb";         // 必填， 以上邮箱对应的密码
    $mail->From       = "511287680@qq.com";       // 必填，发件人Email
  }
  $mail->FromName   = "Talk Is Cheap Team";             // 必填，发件人昵称或姓名
  $mail->Subject    = $emailsubject;          // 必填，邮件标题（主题）
  $mail->MsgHTML($emailbody);             //邮件内容
  $mail->AddReplyTo($smtpemailto);       // 收件人回复的邮箱地址
  $mail->AddAddress($smtpemailto);      // 收件人邮箱
  //发送
  if(!$mail->Send()) {
    // $mail->ErrorInfo;
      return false;
  } else {
      return true;
  }
}

function generate_code($length = 6) {
    return rand(pow(10,($length-1)), pow(10,$length)-1);
}

function verifyEmail($str){
  //@前面的字符可以是英文字母和._- ，._-不能放在开头和结尾，且不能连续出现
  $pattern = '/^[a-z0-9]([a-z0-9]*[-_\.]?[a-z0-9]+)*@[a-z0-9]*([-_\.]?[a-z0-9]+)+[\.][a-z0-9]{2,3}([\.][a-z0-9]{2})?$/i';
  if(preg_match($pattern,$str)){
    return true;
  }else{
    return false;
  }
}

function get_constellation($month,$day){
  //检查参数有效性
  if($month<1||$month>12||$day<1||$day>31) return false;	
  //星座名称以及开始日期
  $constellations=array(
    array("20"=>"宝瓶座"),
    array("19"=>"双鱼座"),
    array("21"=>"白羊座"),
    array("20"=>"金牛座"),
    array("21"=>"双子座"),
    array("22"=>"巨蟹座"),
    array("23"=>"狮子座"),
    array("23"=>"处女座"),
    array("23"=>"天秤座"),
    array("24"=>"天蝎座"),
    array("22"=>"射手座"),
    array("22"=>"摩羯座")
  );
  list($constellation_start,$constellation_name) = each($constellations[(int)$month-1]);
  if($day<$constellation_start){
    list($constellation_start,$constellation_name) = each($constellations[($month-2<0)?$month=11:$month-=2]);
  }
  return $constellation_name;
}
function get_account(){
  $seed = time();
  mt_srand($seed);
  return rand(100000000, 999999999);
}
function session_login($userid){
  session_start();
  $_SESSION[$userid] = '1';
}
function session_logout($userid){
  session_start();
  unset($_SESSION[$userid]);
}
function islogin($userid){
  session_start();
  // if (isset($_SESSION[$userid]) && $_SESSION[$userid] == '1') {
  if ($_SESSION[$userid]) {
    return '1';
  } else {
    // $_SESSION[$userid] = false;
    return '2';
  }
}


function sortbytime($a,$b){
  if($a['createtime'] < $b['createtime']){
    return 1;
  }else if($a['createtime'] == $b['createtime']){
    return $a['id'] < $b['id'] ? 1 : -1;
  }else{
    return -1;
  }

}


