<?php
/**
 * Created by PhpStorm.
 * User: guozw
 * Date: 2017/10/17
 * Time: 下坈2:11
 */
namespace Common\Model;
use Think\Model;

class UserModel extends Model{
  private $_db = '';

  public function __construct(){
      $this -> $_db = M('user');
  }
  
  public function get_by_email($email){
    $where['email'] = $email;
    return $this -> $_db -> where($where) -> find();
  }
  public function add($data){
    $data['status'] = 1;
    return $this -> $_db -> add($data);
  }
  public function login($username,$password,$code){
    $password = SafePassword($password.$code);
    return $this -> $_db -> field('id,account,email,nickname,phone,birthday,constellation,describe,createtime,createtimes,status') -> where("(account = '".$username."' OR email = '".$username."') AND password = '".$password."' ") -> find();
  }
  public function get_by_username($username,$password){
    return $this -> $_db -> where("(account = '".$username."' OR email = '".$username."') ") -> find();
  }
  public function get_by_id($userid){
    $where['id'] = $userid;
    return $this -> $_db -> field('id,account,email,nickname,phone,birthday,constellation,describe,createtime,createtimes,status') -> where($where) -> find();
  }
  public function change_password($email,$password,$code){
    $where['email'] = $email;
    $data['password'] = $password;
    $data['code'] = $code;
    return $this -> $_db -> where($where) -> save($data);
  }
  public function uploadPicture($account,$img){
    $where['account'] = $account;
    $data['portrait'] = $img;
    return $this -> $_db -> where($where) -> save($data);
  }

    public function getAllUser(){
            $where['level'] = 1;
            $res = $this -> $_db -> field('admin_id,user_name,usable') -> where($where) -> select();
            return $res;
        }
        public function editUserUsable($admin_id,$usable){
            $where['admin_id'] = $admin_id;
            $res = $this -> $_db -> where($where) ->setField('usable',$usable);
            return $res;
        }


}