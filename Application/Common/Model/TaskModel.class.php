<?php
/**
 * Created by PhpStorm.
 * User: guozw
 * Date: 2017/10/14
 * Time: 下午1:07
 */
namespace Common\Model;
use Think\Model;

class TaskModel extends Model{
    private $_db = '';

    public function __construct(){
        $this -> $_db = M('task');
    }
    //进行中的任务页 获取所有任务

    public function getAll(){
        $where['validity'] = '1';
        $res = $this -> $_db -> field('task_id,task_title,task_details,reward,createAt,updateAt') -> where($where) ->order('updateAt desc') -> select();
        return $res;
    }



}