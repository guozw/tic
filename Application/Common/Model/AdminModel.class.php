<?php
/**
 * Created by PhpStorm.
 * User: guozw
 * Date: 2017/10/15
 * Time: 上午10:40
 */
namespace Common\Model;
use Think\Model;

class AdminModel extends Model{
    private $_db = '';
    //private $_db1 = '';

    public function __construct(){
      $this -> $_db = M('user');
    }


    public function getbyId($id){
      $where['id'] = '1';
      $result = $this -> $_db -> where($where) -> select();
      return $result;
    }

    
    //任务统计 条件筛选 获取对应任务
    public function getSomeSubTask($arr,$page,$count){
        foreach ($arr as $key => $value){
            switch($key){
                case 'task_id':
                    $map['task.task_id'] = $value;
                    break;
                case 'task_type':
                    $map['task_type'] = $value;
                    break;
                case 'user_name':
                    $map['user_name'] = $value;
                    break;
                case 'phone':
                    $map['phone'] = $value;
                    break;
                case 'wechat_id':
                    $map['wechat_id'] = $value;
                    break;
                case 'validity':
                    $map['task.validity'] = $value;
                    break;
                case 'starttimea':
                    $time = array($value);
                    break;
                case 'endtime':
                    array_push($time,$value);
            }

        }
        if(!empty($time)){
            $map['subAt'] = array('between',$time);
        }
        $resAll = M('subtask') -> join('task on subtask.task_id=task.task_id') -> field('subtask.id,subtask.user_name,subtask.phone,subtask.wechat_id,task.task_id,task.task_title,task.task_type,task.reward,task.reward_type,subtask.subAt,task.validity,subtask.status') -> order('subtask.subAt desc') ->  where($map) -> select();

        $c = count($resAll);
        if($page == 0){
            $result['page'] = 1;
            $result['count'] = $c;
//            $result['data'] = $resAll;
            $page = 1;
            $count = 5;
        }else {
            $result['page'] = $page;
            $result['count'] = $c;
        }
        $result['data'] = M('subtask')->join('task on subtask.task_id=task.task_id')->field('subtask.id,subtask.user_name,subtask.phone,subtask.wechat_id,task.task_id,task.task_title,task.task_type,task.reward,task.reward_type,subtask.subAt,task.validity,subtask.status')->order('subtask.subAt desc')->limit(($page - 1) * $count, $count)-> where($map) -> select();

        return $result;

    }
    //导出搜索
    public function getDaoChuSubTask($arr){
        foreach ($arr as $key => $value){
            switch($key){
                case 'task_id':
                    $map['task.task_id'] = $value;
                    break;
                case 'task_type':
                    $map['task_type'] = $value;
                    break;
                case 'user_name':
                    $map['user_name'] = $value;
                    break;
                case 'phone':
                    $map['phone'] = $value;
                    break;
                case 'wechat_id':
                    $map['wechat_id'] = $value;
                    break;
                case 'status':
                    $map['status'] = $value;
                    break;
                case 'starttime':
                    $time = array($value);
                    break;
                case 'endtime':
                    array_push($time,$value);
            }

        }
        if(!empty($time)){
            $map['subAt'] = array('between',$time);
        }

        $result['data'] = M('subtask')->join('task on subtask.task_id=task.task_id')->field('subtask.id,subtask.user_name,subtask.phone,subtask.wechat_id,task.task_id,task.task_title,task.task_type,task.reward,task.reward_type,subtask.subAt,subtask.status')->order('subtask.subAt desc')-> where($map) -> select();

        return $result;

    }






    public function getTime($starttime,$endtime){
        $map['createAt'] = array('between',array($starttime,$endtime));
        $map['endAt'] = array('between',array($starttime,$endtime));
        $res = M('task') -> where($map) -> select();
        return $res;
    }
//    public function test($arr){
//
//        foreach ($arr as $key => $value){
//            switch($key){
//                case 'phone':
//                    $map['phone'] = $value;
//                    break;
//                case 'wechat_id':
//                    $map['wechat_id'] = $value;
//                    break;
//            }
//        }
//        $res = M('subtask') -> where($map) -> select();
//        return $res;
//    }
    public function getTaskDetail($id){
        $condition['id'] = $id;
        $res = M('subtask')->join('task on subtask.task_id=task.task_id')->field('subtask.user_name,subtask.phone,subtask.wechat_id,subtask.task_url,subtask.name,subtask.address,task.task_title,task.reward_type,task.jiujinbi_num,subtask.remarks,subtask.picture,subtask.status,subtask.reason') -> where($condition) -> select();
        if($res[0]['reward_type'] == '久金币'){
                    $res[0]['isIcon'] = true;
                    $res[0]['iconNum'] = $res[0]['jiujinbi_num'];
                }else{
                    $res[0]['isIcon'] = false;
                }

        return $res;
    }
    public function editStatus($id,$status,$reason){
        $condition['id'] = $id;
        $data['status'] = $status;
        $data['reason'] = $reason;
        $res = M('subtask') -> where($condition) -> save($data);
        return $res;
    }



}