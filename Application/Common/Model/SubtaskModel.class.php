<?php
/**
 * Created by PhpStorm.
 * User: guozw
 * Date: 2017/10/18
 * Time: 上午8:46
 */
namespace Common\Model;
use Think\Model;

class SubtaskModel extends Model{
    private $_db = '';

    public function __construct(){
        $this -> $_db = M('subtask');
    }

    public function subOneTask($phone,$task_id,$user_name,$wechat_id,$task_url,$name,$address,$remarks,$picture){

        if(empty($picture)){
            $picture = '';
        }
        $data1['task_id'] = $task_id;
        $res = M('task') -> field('task_title,task_id,task_type') -> where($data1) -> find();

        //每人只能提交一次
        if($res['task_type'] == 2){
            $where['phone'] = $phone;
            $where['task_id'] = $task_id;
            $re = $this -> $_db -> where($where) -> select();
            $c = count($re);
            if($c == 0){
                $data['id'] = uuid();
                $data['phone'] = $phone;
                $data['task_id'] = $task_id;
                $data['user_name'] = $user_name;
                $data['wechat_id'] = $wechat_id;
                $data['task_url'] = $task_url;
                $data['name'] = $name;
                $data['address'] = $address;
                $data['remarks'] = $remarks;
                $data['picture'] = $picture;
                $where1['task_id'] = $task_id;
                $res = $this -> $_db -> add($data);
                //增加参与人数
                M('task') -> where($where1) -> setInc('num');
                return $res;
            }else{
                $res = 2;
                return $res;
            }
        }else{
            //每人每天只能提交一次
            if($res['task_type'] == 3){
                $starttime = date('Ymd000000');
                $endtime = date('Ymd235959');
                $st = (int)$starttime;
                $en = (int)$endtime;
                $map['phone'] = $phone;
                $map['task_id'] = $res['task_id'];
                $map['subAt'] = array('between',array($st,$en));
                $re = $this -> $_db -> where($map) -> select();
                $c = count($re);
                if($c == 0) {
                    $data['id'] = uuid();
                    $data['phone'] = $phone;
                    $data['task_id'] = $task_id;
                    $data['user_name'] = $user_name;
                    $data['wechat_id'] = $wechat_id;
                    $data['task_url'] = $task_url;
                    $data['name'] = $name;
                    $data['address'] = $address;
                    $data['remarks'] = $remarks;
                    $data['picture'] = $picture;
                    $where1['task_id'] = $task_id;
                    $res = $this -> $_db -> add($data);
                    //增加参与人数
                    M('task') -> where($where1) -> setInc('num');
                    return $res;
                }else{
                    $res = 3;
                    return $res;
                }
            }else{
                //每人随便提交
                $data['id'] = uuid();
                $data['phone'] = $phone;
                $data['task_id'] = $task_id;
                $data['user_name'] = $user_name;
                $data['wechat_id'] = $wechat_id;
                $data['task_url'] = $task_url;
                $data['name'] = $name;
                $data['address'] = $address;
                $data['remarks'] = $remarks;
                $data['picture'] = $picture;
                $where1['task_id'] = $task_id;
                $res = $this -> $_db -> add($data);
                //增加参与人数
                M('task') -> where($where1) -> setInc('num');
                return $res;
            }
        }
    }
    public function getMyAllTask($phone){
        $where['phone'] = $phone;
        $res = M('task') -> join('subtask on task.task_id=subtask.task_id') -> field('subtask.id,task.task_id,task.task_title,subtask.subAt,subtask.status') -> order('subtask.subAt desc') ->  where($where) -> select();
        return $res;
    }
    public function getMyOneTask($id){
        $where['id'] = $id;
        $res = M('task') -> join('subtask on task.task_id=subtask.task_id') -> field('subtask.id,subtask.user_name,subtask.name,subtask.phone,subtask.wechat_id,subtask.address,subtask.task_url,task.task_id,task.task_title,task.reward,subtask.remarks,subtask.subAt,subtask.picture,subtask.status,subtask.reason') -> where($where) -> select();
        return $res;
    }
    public function editMyOneTask($id,$phone,$user_name,$wechat_id,$task_url,$name,$address,$remarks,$old_imgs,$picture){
        $where['id'] = $id;
        $data['phone'] = $phone;
        $data['user_name'] = $user_name;
        $data['wechat_id'] = $wechat_id;
        $data['task_url'] = $task_url;
        $data['name'] = $name;
        $data['address'] = $address;
        $data['remarks'] = $remarks;
        if(empty($picture)){
            $picture = '';
        }
        if(!empty($old_imgs)) {
            $res = M('subtask')->field('picture')->where($where)->select();
            $res = explode('|', $res[0]['picture']);

            $imgs = explode('|', $old_imgs);
            $imgarr = array();
            foreach ($imgs as $a) {
                switch ($a) {
                    case '1':
                        array_push($imgarr, $res[0]);
                        break;
                    case '2':
                        array_push($imgarr, $res[1]);
                        break;
                    case '3':
                        array_push($imgarr, $res[2]);
                        break;
                    case '4':
                        array_push($imgarr, $res[3]);
                        break;
                    case '5':
                        array_push($imgarr, $res[4]);
                        break;
                }

            }
//        implode('|',$imgarr);
            $data['picture'] = implode('|', $imgarr);
            if(empty($picture)){
                $data['picture'] = $data['picture'];
            }else{
            $data['picture'] = $data['picture'] . '|' .$picture;
            }
        }else{
            $data['picture'] = $picture;
        }

        $res = $this -> $_db -> where($where) -> save($data);
        return $res;
    }
    public function showinp($task_id){
        $where['task_id'] = $task_id;
        $res = M('showinput') ->  field('showinput.user,showinput.wechat,showinput.name,showinput.address,showinput.url') -> where($where) ->select();
        return $res;
    }
    public function getreward($task_id){
        $where['task_id'] = $task_id;
        $res = M('task') -> field('reward') -> where($where) -> select();
        return $res;
    }
    public function addFile($file,$id){
        $where['id'] = $id;
        $res = M('subtask') -> field('picture',$file);
        return $res;
    }



}