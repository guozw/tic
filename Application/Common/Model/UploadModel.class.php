<?php
/**
 * Created by PhpStorm.
 * User: guozw
 * Date: 2017/6/7
 * Time: 下午3:15
 */
namespace Common\Model;
use Think\Model;

Class BannerModel extends Model{
    private $_db = '';

    public function __construct()
    {
        $this -> $_db = M('Banner');
    }

    public function addBanner($img,$title,$link){
        $condition['img'] = $img;
        $condition['title'] = $title;
        $condition['link'] = $link;

        $res = $this -> $_db -> add($condition);
        return $res;
    }
    public function delBanner($id){
        $condition['id'] = $id;
        $res = $this -> $_db -> where($condition) -> delete();
        return $res;
    }
    public function getOneBanner($id){
        $res = $this -> $_db -> where("id=".$id) -> find();
        return $res;
    }

}
