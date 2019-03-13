<?php
namespace Home\Controller;
use Think\Controller;
use Workerman\Worker;
class WorkerManController{
    public function index(){
        if(!IS_CLI){
            die("无法直接访问，请通过命令行启动");
        }
        $worker = new \Workerman\Worker('websocket://127.0.0.1:2346');
        // 当收到客户端发来的数据后返回hello $data给客户端
        $worker->onWorkerStart = function($worker){
            echo "Worker starting...\n";
        };
        $worker->onMessage = function($connection, $data)
        {
            // 向客户端发送hello $data
            $connection->send('hello '.$data);
        };
        // 运行worker
        Worker::runAll();
        
    }
    public function aa(){
      echo 'a';
    }
}