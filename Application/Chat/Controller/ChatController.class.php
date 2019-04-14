<?php
namespace Chat\Controller;
use Think\Controller;

class ChatController extends Controller{
  function __construct() {
    if(!session('login')) return show(-999,'未登录');
  }
  
  
  //------------------------------------
  //获取列表
  public function get_chatList(){
    $userid = session('login');
    if(!$userid || $userid == '' )  missing_login();
    $listres = D('Chatlist') -> get_list($userid);
    $chatlist = array();
    if($listres){
      $list = json_decode($listres['list'],true);
      foreach($list['top'] as $top){
        if($top['type'] == 1){
          $userinfo = D('User') -> get_by_id($top['id']);
          $lastmessage = false;
          $lasttime = false;
          $isread = true;
          $history = D('Chathistory') -> get_userlastone($userid,$top['id']);
          if($history) {
            $lastmessage = $history[0]['message'];
            $lasttime = $history[0]['createtimes'];
            if($userid != $history[0]['send_user'])
              $isread = $history[0]['isread'] == 2 ? false : true;
          }
          $chatlist[] = array(
            'type'        => 1,
            'userid'      => $userinfo['id'],
            'username'    => $userinfo['nickname'],
            'useraccount' => $userinfo['account'],
            'lastmessage' => $lastmessage,
            'lasttime'    => $lasttime,
            'isread'      => $isread,
            'top'         => true,
            'picture'     => $userinfo['portrait']
          );
        }else{
          $groupinfo = D('Groups') -> get_by_id($top['id']);
          $lastmessage = false;
          $lasttime = false;
          $isread = true;
          $history = D('Chathistory') -> get_grouplastone($groupinfo['id']);
          if($history) {
            $lastmessage = $history['message'];
            $lasttime = $history['createtimes'];
            $isread = $history['isread'] == 2 ? false : true;
          }
          $chatlist[] = array(
            'type'        => 2,
            'roomid'      => $groupinfo['id'],
            'roomname'    => $groupinfo['group_name'],
            'lastmessage' => $lastmessage,
            'lasttime'    => $lasttime,
            'isread'      => $isread,
            'top'         => true,
            'picture'     => 'http://tic.codergzw.com/Public/img/portraits/group.png' 
          );
        }
      }
      foreach($list['normal'] as $normal){
        if($normal['type'] == 1){
          $userinfo = D('User') -> get_by_id($normal['id']);
          $lastmessage = false;
          $lasttime = false;
          $isread = true;
          $history = D('Chathistory') -> get_userlastone($userid,$normal['id']);
          if($history) {
            $lastmessage = $history[0]['message'];
            $lasttime = $history[0]['createtimes'];
            if($userid != $history[0]['send_user'])
            $isread = $history[0]['isread'] == 2 ? false : true;
          }
          $chatlist[] = array(
            'type'        => 1,
            'userid'      => $userinfo['id'],
            'username'    => $userinfo['nickname'],
            'useraccount' => $userinfo['account'],
            'lastmessage' => $lastmessage,
            'lasttime'    => $lasttime,
            'isread'      => $isread,
            'top'         => false,
            'picture'     => $userinfo['portrait']
          );
        }else{
          $groupinfo = D('Groups') -> get_by_id($normal['id']);
          $lastmessage = false;
          $lasttime = false;
          $isread = true;
          $history = D('Chathistory') -> get_grouplastone($groupinfo['id']);
          if($history) {
            $lastmessage = $history['message'];
            $lasttime = $history['createtimes'];
            $isread = $history['isread'] == 2 ? false : true;
          }
          $chatlist[] = array(
            'type'        => 2,
            'roomid'      => $groupinfo['id'],
            'roomname'    => $groupinfo['group_name'],
            'lastmessage' => $lastmessage,
            'lasttime'    => $lasttime,
            'isread'      => $isread,
            'top'         => false,
            'picture'     => 'http://tic.codergzw.com/Public/img/portraits/group.png' 
          );
        }
      }
      return show(0,'获取成功',$chatlist);
    }else{
      return show(0,'暂无列表信息');
    }
  }
  //------------------------------------
  //更新、创建、维护聊天列表
  public function update_chatList(){
    $userid = session('login');
    // $userid = 5;
    if(!$userid || $userid == '' )  missing_login();
    $action = I('post.action'); //1:发送消息  2:点击按钮创建  3:置顶  4:取消置顶  5:删除
    $touserid = I('post.touserid');
    $type = I('post.type'); //1:私聊 2:群聊
    if(!$action || $action == '' || !$type || $type == '' || !$touserid || $touserid == '' ) missing_parameter();
    if($userid == $touserid){
      return show(-1,'不能和自己聊天');
    }
    if($action == 1){
      //发送消息
      $listres = D('Chatlist') -> get_list($userid);
      if($listres){
        //数据库插入过此人记录
        $flag = 0;
        $list = json_decode($listres['list'],true);
        $newlist = array(
          'top'    => array(),
          'normal' => array()
        );
        $i = 0;
        $j = 0;
        foreach($list['top'] as $top){
          if($top['type'] == $type && $top['id'] == $touserid){
            $flag = 'top';
            $j = $i;
          }else{
            $newlist['top'][] = array(
              'type' => $top['type'],
              'id' => $top['id']
            );
          }
          $i ++;
        }
        $m = 0;
        $n = 0;
        foreach($list['normal'] as $normal){
          if($normal['type'] == $type && $normal['id'] == $touserid){
            $flag = 'normal';
            $n = $m;
          }else{
            $newlist['normal'][] = array(
              'type' => $normal['type'],
              'id' => $normal['id']
            );
          }
          $m ++;
        }
        $needupdate = true;
        if(!$flag){
          return show(-1,'错误对话信息 错误代码:1_3');
        }else if($j == 0 && $n == 0){
          // return show(0,'该对话已在正确位置');
          $needupdate = false;
        }
        $res = true;
        if($needupdate){
          $my = array(
            'type' => $type,
            'id' => $touserid
          );
          array_unshift($newlist[$flag], $my);
          $data['list'] = json_encode($newlist);
          $res = D('Chatlist') -> update_list($userid,$data);
          if(!$res){
            return show(-1,'失败 错误代码:1_4');
          }
        }
        if($type == 1){
          //私聊更新对方列表
          $listres = D('Chatlist') -> get_list($touserid);
          if($listres){
            //数据库插入过此人记录
            $list = json_decode($listres['list'],true);
            $newlist = array(
              'top'    => array(),
              'normal' => array()
            );
            $flag = true;
            $i = 0;
            $j = 0;
            $who;
            foreach($list['top'] as $top){
              if($top['type'] == $type && $top['id'] == $userid){
                $flag = false;
                $j = $i;
                $who = 'top';
              }else{
                $newlist['top'][] = array(
                  'type' => $top['type'],
                  'id' => $top['id']
                );
              }
              $i ++;
            }
            $m = 0;
            $n = 0;
            foreach($list['normal'] as $normal){
              if($normal['type'] == $type && $normal['id'] == $userid){
                $flag = false;
                $n = $m;
                $who = 'normal';
              }else{
                $newlist['normal'][] = array(
                  'type' => $normal['type'],
                  'id' => $normal['id']
                );
              }
              $m ++;
            }
            if($flag){
              $my = array(
                'type' => $type,
                'id' => $userid
              );
              array_unshift($newlist['normal'], $my);
              $data['list'] = json_encode($newlist);
              $res = D('Chatlist') -> update_list($touserid,$data);
              if($res){
                return show(0,'成功1_2',$res);
              }else{
                return show(-1,'失败 错误代码:1_6');
              }
            }else{
              if($n == 0 && $j == 0){
                return show(0,'成功 对方已在正确位置');
              }else{
                $my = array(
                  'type' => $type,
                  'id' => $userid
                );
                if($who == 'top'){
                  array_unshift($newlist['top'], $my);
                }else{
                  array_unshift($newlist['normal'], $my);
                }
                
                $data['list'] = json_encode($newlist);
                $res = D('Chatlist') -> update_list($touserid,$data);
                if($res){
                  return show(0,'成功1_3',$res);
                }else{
                  return show(-1,'失败 错误代码:1_5');
                }
              }
              // return show(0,'成功 对方已在列表中');
            }
          }else{
            //数据库从未插入过此人记录 可理解为列表为空
            $data['userid'] = $touserid;
            $data['list'] = '{"top":[],"normal":[{"type":'.$type.',"id":'.$userid.'}]}';
            $res = D('Chatlist') -> add($data);
            if($res){
              return show(0,'成功1_1',$res);
            }else{
              return show(-1,'失败 错误代码:1_2');
            }
          }
        }else{
          //群聊更新一群人列表
          $groupinfo = D('Groups') -> get_by_id($touserid);
          if($groupinfo){
            // print_r($groupinfo);exit;
            $users = explode(",",$groupinfo['group_user']);
            foreach($users as $guserid){
              if($guserid != $userid){
                $listres = D('Chatlist') -> get_list($guserid);
                if($listres){
                  //数据库插入过此人记录
                  $list = json_decode($listres['list'],true);
                  $newlist = array(
                    'top'    => array(),
                    'normal' => array()
                  );
                  $flag = true;
                  $i = 0;
                  $j = 0;
                  $who;
                  foreach($list['top'] as $top){
                    if($top['type'] == $type && $top['id'] == $touserid){
                      $flag = false;
                      $j = $i;
                      $who = 'top';
                    }else{
                      $newlist['top'][] = array(
                        'type' => $top['type'],
                        'id' => $top['id']
                      );
                    }
                    $i ++;
                  }
                  $m = 0;
                  $n = 0;
                  foreach($list['normal'] as $normal){
                    if($normal['type'] == $type && $normal['id'] == $touserid){
                      $flag = false;
                      $n = $m;
                      $who = 'normal';
                    }else{
                      $newlist['normal'][] = array(
                        'type' => $normal['type'],
                        'id' => $normal['id']
                      );
                    }
                    $m ++;
                  }
                  if($flag){
                    $my = array(
                      'type' => $type,
                      'id' => $touserid
                    );
                    array_unshift($newlist['normal'], $my);
                    $data['list'] = json_encode($newlist);
                    $res = D('Chatlist') -> update_list($guserid,$data);
                    if($res){
                      // return show(0,'成功1_3',$res);
                    }else{
                      return show(-1,'失败 错误代码:1_7');
                    }
                  }else{
                    if($n == 0 && $j == 0){
                      // return show(0,'成功 对方已在正确位置');
                    }else{
                      $my = array(
                        'type' => $type,
                        'id' => $touserid
                      );
                      if($who == 'top'){
                        array_unshift($newlist['top'], $my);
                      }else{
                        array_unshift($newlist['normal'], $my);
                      }
                      
                      $data['list'] = json_encode($newlist);
                      $res = D('Chatlist') -> update_list($guserid,$data);
                      if($res){
                        // return show(0,'成功1_3',$res);
                      }else{
                        return show(-1,'失败 错误代码:1_8');
                      }
                    }
                    // return show(0,'成功 对方已在列表中');
                  }
                }else{
                  //数据库从未插入过此人记录 可理解为列表为空
                  $data['userid'] = $guserid;
                  $data['list'] = '{"top":[],"normal":[{"type":'.$type.',"id":'.$touserid.'}]}';
                  $res = D('Chatlist') -> add($data);
                  if($res){
                    // return show(0,'成功1_1',$res);
                  }else{
                    return show(-1,'失败 错误代码:1_9');
                  }
                }
              }
            }
            return show(0,'成功1_4',$res);
          }else{
            return show(-1,'群组不存在');
          }
          
        }
        // return show(0,'成功1_1',$res);

          
 
      }else{
        return show(-1,'无列表信息 错误代码:1_1');
      }
    }else if($action == 2){
      //点击按钮创建
      $listres = D('Chatlist') -> get_list($userid);
      if($listres){
        //数据库插入过此人记录
        $list = json_decode($listres['list'],true);
        $flag = true;
        $i = 0;
        $j = 0;
        $who;
        $newlist = array(
          'top'    => array(),
          'normal' => array()
        );
        foreach($list['top'] as $top){
          if($top['type'] == $type && $top['id'] == $touserid){
            $flag = false;
            $j = $i;
            $who = 'top';
          }else{
            $newlist['top'][] = array(
              'type' => $top['type'],
              'id' => $top['id']
            );
          }
          $i ++;
        }
        $m = 0;
        $n = 0;
        foreach($list['normal'] as $normal){
          if($normal['type'] == $type && $normal['id'] == $touserid){
            $flag = false;
            $n = $m;
            $who = 'normal';
          }else{
            $newlist['normal'][] = array(
              'type' => $normal['type'],
              'id' => $normal['id']
            );
          }
          $m ++;
        }
        if($flag){
          $my = array(
            'type' => $type,
            'id' => $touserid
          );
          array_unshift($newlist['normal'], $my);
          $data['list'] = json_encode($newlist);
          $res = D('Chatlist') -> update_list($userid,$data);
          if($res){
            return show(0,'成功2_1',$res);
          }else{
            return show(-1,'失败 错误代码:2_2');
          }
        }else{
          if($n == 0 && $j == 0){
            return show(0,'成功 对方已在正确位置');
          }else{
            $my = array(
              'type' => $type,
              'id' => $touserid
            );
            if($who == 'top'){
              array_unshift($newlist['top'], $my);
            }else{
              array_unshift($newlist['normal'], $my);
            }
            
            $data['list'] = json_encode($newlist);
            $res = D('Chatlist') -> update_list($userid,$data);
            if($res){
              return show(0,'成功2_2',$res);
            }else{
              return show(-1,'失败 错误代码:2_3');
            }
          }
          // return show(0,'成功 对方已在列表中');
        }
      }else{
        //数据库从未插入过此人记录 可理解为列表为空
        $data['userid'] = $userid;
        $data['list'] = '{"top":[],"normal":[{"type":'.$type.',"id":'.$touserid.'}]}';
        $res = D('Chatlist') -> add($data);
        if($res){
          return show(0,'成功2_1',$res);
        }else{
          return show(-1,'失败 错误代码:2_1');
        }
      }
    }else if($action == 3){
      //置顶
      $listres = D('Chatlist') -> get_list($userid);
      if($listres){
        //数据库插入过此人记录
        $flag = true;
        $list = json_decode($listres['list'],true);
        $newlist = array(
          'top'    => array(),
          'normal' => array()
        );
        $newlist['top'][] = array(
          'type' => $type,
          'id' => $touserid
        );
        foreach($list['top'] as $top){
          if($top['type'] == $type && $top['id'] == $touserid){
            return show(-1,'已是置顶 错误代码:3_2');
          }else{
            $newlist['top'][] = array(
              'type' => $top['type'],
              'id' => $top['id']
            );
          }
        }
        foreach($list['normal'] as $normal){
          if($normal['type'] == $type && $normal['id'] == $touserid){
            $flag = false;
          }else{
            $newlist['normal'][] = array(
              'type' => $normal['type'],
              'id' => $normal['id']
            );
          }
        }
        if($flag){
          return show(-1,'错误对话信息 错误代码:3_3');
        }
        // print_r($newlist);exit;
        $data['list'] = json_encode($newlist);
        $res = D('Chatlist') -> update_list($userid,$data);
        if($res){
          return show(0,'成功3_1',$res);
        }else{
          return show(-1,'失败 错误代码:3_4');
        }
      }else{
        return show(-1,'无列表信息 错误代码:3_1');
      }
    }else if($action == 4){
      //取消置顶
      $listres = D('Chatlist') -> get_list($userid);
      if($listres){
        //数据库插入过此人记录
        $flag = true;
        $list = json_decode($listres['list'],true);
        $newlist = array(
          'top'    => array(),
          'normal' => array()
        );
        $newlist['normal'][] = array(
          'type' => $type,
          'id' => $touserid
        );
        foreach($list['top'] as $top){
          if($top['type'] == $type && $top['id'] == $touserid){
            $flag = false;
          }else{
            $newlist['top'][] = array(
              'type' => $top['type'],
              'id' => $top['id']
            );
          }
        }
        foreach($list['normal'] as $normal){
          if($normal['type'] == $type && $normal['id'] == $touserid){
            return show(-1,'已是未置顶 错误代码:4_2');
          }else{
            $newlist['normal'][] = array(
              'type' => $normal['type'],
              'id' => $normal['id']
            );
          }
        }
        if($flag){
          return show(-1,'错误对话信息 错误代码:4_3');
        }
        // print_r($newlist);exit;
        $data['list'] = json_encode($newlist);
        $res = D('Chatlist') -> update_list($userid,$data);
        if($res){
          return show(0,'成功4_1',$res);
        }else{
          return show(-1,'失败 错误代码:4_4');
        }
      }else{
        return show(-1,'无列表信息 错误代码:4_1');
      }
    }else{
      //删除
      $listres = D('Chatlist') -> get_list($userid);
      if($listres){
        //数据库插入过此人记录
        $flag = true;
        $list = json_decode($listres['list'],true);
        $newlist = array(
          'top'    => array(),
          'normal' => array()
        );
        foreach($list['top'] as $top){
          if($top['type'] == $type && $top['id'] == $touserid){
            $flag = false;
          }else{
            $newlist['top'][] = array(
              'type' => $top['type'],
              'id' => $top['id']
            );
          }
        }
        foreach($list['normal'] as $normal){
          if($normal['type'] == $type && $normal['id'] == $touserid){
            $flag = false;
          }else{
            $newlist['normal'][] = array(
              'type' => $normal['type'],
              'id' => $normal['id']
            );
          }
        }
        if($flag){
          return show(-1,'错误对话信息 错误代码:5_2');
        }
        // print_r($newlist);exit;
        $data['list'] = json_encode($newlist);
        $res = D('Chatlist') -> update_list($userid,$data);
        if($res){
          return show(0,'成功5_1',$res);
        }else{
          return show(-1,'失败 错误代码:5_3');
        }
      }else{
        return show(-1,'无列表信息 错误代码:5_1');
      }
    }
  }



  //创建群聊
  public function create_group(){
    $userid = session('login');
    if(!$userid || $userid == '' )  missing_login();
    $users = I('users');
    $group_name = I('group_name');
    if(!$users || $users == '' || !$group_name || $group_name == '' ) missing_parameter();
    $data['group_name'] = $group_name;
    $users = $userid.','.$users;
    $data['group_user'] = $users;
    $res = D('Groups') -> add($data);
    if($res){
      return show(0,'创建成功',$res);
    }else{
      return show(-1,'创建失败');
    }
  }
  //获取群组列表
  public function get_grouplist(){
    $userid = session('login');
    if(!$userid || $userid == '' )  missing_login();
    $list = D('Groups') -> get_list($userid);
    if($list){
      return show(0,'获取成功',$list);
    }else{
      return show(-1,'获取失败');
    }
  }
  //获取群组详细信息
  public function get_groupinfo(){
    $userid = session('login');
    if(!$userid || $userid == '' )  missing_login();
    $groupid = I('groupid');
    if(!$groupid || $groupid == '' ) missing_parameter();
    $groupinfo = D('Groups') -> get_by_id($groupid);
    
    if($groupinfo){
      $groupinfo['users'] = array();
      $users = explode(",", $groupinfo['group_user']);
      foreach($users as $user){
        $userinfo = D('User')->get_by_id($user);
        if($userinfo){
          $groupinfo['users'][] = $userinfo;
        }
      }
      return show(0,'获取成功',$groupinfo);
    }else{
      return show(-1,'群组不存在');
    }

  }
  //退出群组
  public function out_group(){
    $userid = session('login');
    if(!$userid || $userid == '' )  missing_login();
    $groupid = I('groupid');
    if(!$groupid || $groupid == '' ) missing_parameter();
    $groupinfo = D('Groups') -> get_by_id($groupid);
    if($groupinfo){
      $users = explode(",", $groupinfo['group_user']);
      if(count($users) == 1){
        $res = D('Groups') -> del_group($groupid);
        if($res){
          return show(0,'退出成功',$res);
        }else{
          return show(-1,'退出失败');
        }
      }else{
        $newusers;
        foreach($users as $user){
          if($user != $userid)
            $newusers .= $user . ','; 
        }
        $newusers = substr($newusers,0,strlen($newusers)-1); 
        $res = D('Groups') -> update_users($groupinfo['id'],$newusers);
        if($res){
          return show(0,'退出成功',$res);
        }else{
          return show(-1,'退出失败');
        }
      }
    }else{
      return show(-1,'群组不存在');
    }
  }
  //获取聊天记录
  public function get_chathistory(){
    $userid = session('login');
    if(!$userid || $userid == '' )  missing_login();
    $otheruserid = I('post.otheruserid');
    if(!$otheruserid || $otheruserid == '' ) missing_parameter();
    
    if($otheruserid > 50000){
      //这里是群聊
      $list = D('Chathistory') -> get_grouphistory($otheruserid);
    }else{
      //这里是私聊
      $list = D('Chathistory') -> get_userhistory($otheruserid,$userid);
    }
    if($list){
      return show(0,'成功',$list);
    }else{
      return show(0,'暂无历史记录');
    }
  }
  public function isread_history(){
    $userid = session('login');
    if(!$userid || $userid == '' )  missing_login();
    $otheruserid = I('post.otheruserid');
    if(!$otheruserid || $otheruserid == '' ) missing_parameter();
    $res = D('Chathistory') -> isread_history($otheruserid,$userid);
    if($res){
      return show(0,'成功',$res);
    }else{
      return show(-1,'失败');
    }
  }
  public function test(){
    $str = 'q1';
    // print_r(explode(",", $str));
    echo substr_count($str,'q');
  }
}