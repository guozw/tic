<?php
namespace Chat\Controller;
use Think\Controller;

class ChatController extends Controller{
  function __construct() {
    if(!session('login')) return show(-999,'未登录');
  }
  //更新、创建、维护聊天列表
  public function update_chatList(){
    $userid = session('login');
    if(!$userid || $userid == '' )  missing_login();
    $action = I('post.action'); //1:发送消息  2:点击按钮创建  3:置顶  4:取消置顶  5:删除
    $touserid = I('post.touserid');
    $type = I('post.type'); //1:私聊 2:群聊
    if(!$action || $action == '' || !$type || $type == '' || !$touserid || $touserid == '' ) missing_parameter();
    $otherlisttouserid = $touserid;
    if($action == 1){
      $top = I('post.top');
      // if(!$top || $top == '' ) missing_parameter();
      //发送消息置顶
      if($type == 1){
        //私聊逻辑处理
        //另一个人列表 以及 另一个人id 先保存起来 防止被改写 
        $otherlist = D('Chatlist') -> get_list($touserid);
        
        if($top){
          //如果是 置顶
          $flag = $touserid .'.1';
          $touserid .= '.1';
          $newlist = $touserid . ',';
          $list = D('Chatlist') -> get_list($userid);
          $listarr = explode(",",$list['list']);
          foreach($listarr as $room){
            if($room != $flag){
              $newlist .= $room.',';
            }
          }
          $data['list'] = substr($newlist,0,strlen($newlist)-1);
          $res = D('Chatlist') -> update_list($userid,$data);
          if($res){
            // return show(0,'成功',$res);
          }else{
            // return show(-1,'失败');
          }
        }else{
          //如果不是置顶
          $touserid = $touserid.'.0';
          $flag = 1;
          $newlist;
          $list = D('Chatlist') -> get_list($userid);
          $listarr = explode(",",$list['list']);
          foreach($listarr as $room){
            if($room != $touserid){
              if(strpos($room,'.1') == false){
                if($flag == 1){
                  $flag = 0;
                  $newlist .= $touserid .',' . $room .',';
                }else{
                  $newlist .= $room . ',';
                }
              }else{
                $newlist .= $room . ',';
              }
            }
          }
          $data['list'] = substr($newlist,0,strlen($newlist)-1);
          $res = D('Chatlist') -> update_list($userid,$data);
          if($res){
            // return show(0,'成功',$res);
          }else{
            // return show(-1,'失败');
          }
        }
        //更新对方列表
        if($otherlist){
          $otop = $userid . '.1'; 
          $otheristop = false;
          $otherlistarr = explode(",",$otherlist['list']);
          foreach($otherlistarr as $room){
            if($room == $otop){
              $otheristop = true;
            }
          }
          if($otheristop){
            //本人在对方列表是置顶
            $flag = $userid .'.1';
            $userid .= '.1';
            $newlist = $userid . ',';
            foreach($otherlistarr as $room){
              if($room != $flag){
                $newlist .= $room.',';
              }
            }
            $data['list'] = substr($newlist,0,strlen($newlist)-1);
            $res = D('Chatlist') -> update_list($otherlisttouserid,$data);
            if($res){
              // return show(0,'成功',$res);
            }else{
              // return show(-1,'失败');
            }
          }else{
            //本人在对方列表不是置顶
            $flag = true;
            $userid .= '.0';
            $newlist = '';

            foreach($otherlistarr as $room){
              if(strpos($room,'.1') != false){
                $newlist .= $room . ',';
              }else{
                if($room != $userid){
                  if($flag){
                    $newlist .= $userid . ',' . $room.',';
                    $flag = false;
                  }else{
                    $newlist .= $room.',';
                  }
                }
              }
            }
            $data['list'] = substr($newlist,0,strlen($newlist)-1);
            $res = D('Chatlist') -> update_list($otherlisttouserid,$data);
            if($res){
              // return show(0,'成功',$res);
            }else{
              // return show(-1,'失败');
            }
          }

        }else{
          $data['list'] = $userid.'.0';
          $data['userid'] = $otherlisttouserid;
          $res = D('Chatlist') -> add($data);
          if($res){
            // return show(0,'成功',$res);
          }else{
            // return show(-1,'失败');
          }
        }
      }else{
        //群聊逻辑处理
        $others = D('Groups') -> get_by_id($touserid);
        $tousersarr = explode(",",$others['group_user']);
        if($top){
          //如果是置顶的操作
          $flag = 'q'.$touserid .'.1';
          $touserid ='q'. $touserid .'.1';
          $newlist = $touserid . ',';
          $list = D('Chatlist') -> get_list($userid);
          $listarr = explode(",",$list['list']);
          foreach($listarr as $room){
            if($room != $flag){
              $newlist .= $room.',';
            }
          }
          $data['list'] = substr($newlist,0,strlen($newlist)-1);
          $res = D('Chatlist') -> update_list($userid,$data);
          if($res){
            // return show(0,'成功',$res);
          }else{
            // return show(-1,'失败');
          }
        }else{
          //如果不是置顶的操作
          $touserid = 'q'.$touserid.'.0';
          $flag = 1;
          $newlist;
          $list = D('Chatlist') -> get_list($userid);
          $listarr = explode(",",$list['list']);
          foreach($listarr as $room){
            if($room != $touserid){
              if(strpos($room,'.1') == false){
                if($flag == 1){
                  $flag = 0;
                  $newlist .= $touserid .',' . $room .',';
                }else{
                  $newlist .= $room . ',';
                }
              }else{
                $newlist .= $room . ',';
              }
            }
          }
          $data['list'] = substr($newlist,0,strlen($newlist)-1);
          $res = D('Chatlist') -> update_list($userid,$data);
          if($res){
            // return show(0,'成功',$res);
          }else{
            // return show(-1,'失败');
          }
        }
        //别人列表处理
        foreach($tousersarr as $touser){
          if($touser != $userid){
            $otherlist = D('Chatlist') -> get_list($touser);

            if($otherlist){
              $onotop = 'q' . $otherlisttouserid . '.0'; 
              $otop = 'q' . $otherlisttouserid . '.1'; 
              $otheristop = 0;
              $otherlistarr = explode(",",$otherlist['list']);
              foreach($otherlistarr as $room){
                if($room == $otop){
                  $otheristop = 1;
                }
                if($room == $onotop){
                  $otheristop = 2;
                }
              }
              if($otheristop == 1){
                //群聊在对方列表是置顶
                $flag = $otop;
                $newlist = $otop . ',';
                foreach($otherlistarr as $room){
                  if($room != $flag){
                    $newlist .= $room.',';
                  }
                }
                $data['list'] = substr($newlist,0,strlen($newlist)-1);
                $res = D('Chatlist') -> update_list($touser,$data);
                if($res){
                  // return show(0,'成功',$res);
                }else{
                  // return show(-1,'失败');
                }
              }else{
                //群聊在对方列表不是置顶
                $flag = 1;
                $newlist = '';
                foreach($otherlistarr as $room){
                  if($room != $onotop){
                    if(strpos($room,'.1') == false){
                      if($flag == 1){
                        $flag = 0;
                        $newlist .= $onotop .',' . $room .',';
                      }else{
                        $newlist .= $room . ',';
                      }
                    }else{
                      $newlist .= $room . ',';
                    }
                  }
                }
                $data['list'] = substr($newlist,0,strlen($newlist)-1);
                $res = D('Chatlist') -> update_list($touser,$data);
                if($res){
                  // return show(0,'成功',$res);
                }else{
                  // return show(-1,'失败');
                }
              }
      
            }else{
              $data['list'] = 'q' . $otherlisttouserid . '.0';
              $data['userid'] = $touser;
              $res = D('Chatlist') -> add($data);
              if($res){
                // return show(0,'成功',$res);
              }else{
                // return show(-1,'失败');
              }
            }
            //----
          }
        }
        return show(0,'成功',$res);
      }
    }else if($action == 2){
      //点击聊天按钮 创建列表
      $list = D('Chatlist') -> get_list($userid);
      if($list){
        $touserid .= '.0'; 
        $ishave = false;
        if($type == 1){
          $listarr = explode(",",$list['list']);
          foreach($listarr as $room){
            if(substr_count($room,'q') == false){
              if($touserid == $room){
                $ishave = true;
              }
            }
          }
        }else{
          $touserid = 'q'.$touserid;
          $listarr = explode(",",$list['list']);
          foreach($listarr as $room){
            if(substr_count($room,'q') != false){
              if($touserid == $room){
                $ishave = true;
              }
            }
          }
        }

        if($ishave){
          $data['type'] = 1;
          $data['userid'] = substr($touserid,0,strpos($touserid, '.'));
          return show(0,'已在列表中',$data);
        }else{
          $newlist;
          $flag = 1;
          foreach($listarr as $room){
            if(strpos($room,'.1') == false){
              if($flag == 1){
                $newlist .= $touserid.','.$room.',';
                $flag = 0;
              }else{
                $newlist .= $room.',';
              }
            }else{
              $newlist .= $room.',';
            }
            
          }
          $data['list'] = substr($newlist,0,strlen($newlist)-1);
          $res = D('Chatlist') -> update_list($userid,$data);
          if($res){
            return show(0,'成功',$res);
          }else{
            return show(-1,'失败');
          }
        }
      }else{
        if($type == 1){
          $data['list'] = $touserid.'.0';
        }else{
          $data['list'] = 'q' . $touserid . '.0';
        }
        $data['userid'] = $userid;
        $res = D('Chatlist') -> add($data);
        if($res){
          return show(0,'成功',$res);
        }else{
          return show(-1,'失败');
        }
        
      }
    }else if($action == 3){
      //置顶
      if($type == 1){
        $flag = $touserid .'.0';
        $touserid .= '.1';
        $newlist = $touserid . ',';
      }else{
        $flag = 'q'.$touserid .'.0';
        $touserid ='q'. $touserid .'.1';
        $newlist = $touserid . ',';
      }
      $list = D('Chatlist') -> get_list($userid);
      $listarr = explode(",",$list['list']);
      foreach($listarr as $room){
        if($room != $flag){
          $newlist .= $room.',';
        }
      }
      $data['list'] = substr($newlist,0,strlen($newlist)-1);
      $res = D('Chatlist') -> update_list($userid,$data);
      if($res){
        return show(0,'成功',$res);
      }else{
        return show(-1,'失败');
      }
    }else if($action == 4){
      //取消置顶
      if($type == 1){
        $touserid = $touserid.'.1';
      }else{
        $touserid = 'q'.$touserid.'.1';
      }
      $flag = 1;
      $newlist;
      $list = D('Chatlist') -> get_list($userid);
      $listarr = explode(",",$list['list']);
      foreach($listarr as $room){
        if($room != $touserid){
          if(strpos($room,'.1') == false){
            if($flag == 1){
              $flag = 0;
              $newlist .= str_replace(".1",".0",$touserid).','.$room.',';
            }else{
              $newlist .= $room.',';
            }
          }else{
            $newlist .= $room.',';
          }
        }
      }
      $data['list'] = substr($newlist,0,strlen($newlist)-1);
      $res = D('Chatlist') -> update_list($userid,$data);
      if($res){
        return show(0,'成功',$res);
      }else{
        return show(-1,'失败');
      } 
    }else{
      //删除
      if($type == 1){
        $touserid = $touserid;
      }else{
        $touserid = 'q'.$touserid;
      }
      $newlist;
      $list = D('Chatlist') -> get_list($userid);
      $listarr = explode(",",$list['list']);
      foreach($listarr as $room){
        if($room != $touserid.'.1' && $room != $touserid.'.0' ){
          $newlist .= $room.',';
        }
      }
      $data['list'] = substr($newlist,0,strlen($newlist)-1);
      $res = D('Chatlist') -> update_list($userid,$data);
      if($res){
        return show(0,'成功',$res);
      }else{
        return show(-1,'失败');
      }
    }
  }

  //获取列表
  public function get_chatList(){
    $userid = session('login');
    if(!$userid || $userid == '' )  missing_login();
    $list = D('Chatlist') -> get_list($userid);
    $chatlist = array();
    if($list){
      $rooms = explode(",", $list['list']);
      // print_r($rooms);exit;
      foreach($rooms as $room){
        if(substr_count($room,'q') != false){
          // echo 1;
          //群聊
          if(strpos($room,'.0') != false){
            $flag = false;
          }else{
            $flag = true;
          }
          $groupid = substr($room,1,strpos($room, '.'));
          $groupinfo = D('Groups') -> get_by_id($groupid);
          $lastmessage = false;
          $lasttime = false;
          $history = D('Chathistory') -> get_grouplastone($groupinfo['id']);
          if($history) {
            $lastmessage = $history['message'];
            $lasttime = $history['createtimes'];
          }
          $chatlist[] = array(
            'type'        => 2,
            'roomid'      => $groupinfo['id'],
            'roomname'    => $groupinfo['group_name'],
            'lastmessage' => $lastmessage,
            'lasttime'    => $lasttime,
            'top'         => $flag,
            'picture'     => 'http://tic.codergzw.com/Public/img/portraits/group.png' 
          );
        }else{
          //私聊
          if(strpos($room,'.0') != false){
            $flag = false;
          }else{
            $flag = true;
          }
          $touserid = substr($room,0,strpos($room, '.'));
          $userinfo = D('User') -> get_by_id($touserid);
          $lastmessage = false;
          $lasttime = false;
          $history = D('Chathistory') -> get_userlastone($userid,$userinfo['id']);
          if($history) {
            $lastmessage = $history['message'];
            $lasttime = $history['createtimes'];
          }
          $chatlist[] = array(
            'type'        => 1,
            'userid'      => $userinfo['id'],
            'username'    => $userinfo['nickname'],
            'useraccount' => $userinfo['account'],
            'lastmessage' => $lastmessage,
            'lasttime'    => $lasttime,
            'top'         => $flag,
            'picture'     => $userinfo['portrait']
          );
        }  
      }
      
      return show(0,'获取成功',$chatlist);
    }else{
      return show(-1,'获取失败');
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
  public function test(){
    $str = 'q1';
    // print_r(explode(",", $str));
    echo substr_count($str,'q');
  }
}