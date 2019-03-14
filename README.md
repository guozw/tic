# tic

因为需要在不同环境下coding 所以一些配置文件没有上传
需要自行新建

路径 Application/Common/Conf/

文件1:

config.php

<?php
return array(
  //'配置项'=>'配置值'
  'LOAD_EXT_CONFIG' => 'db',
    'PortraitsUpload' => array(
        'maxSize'    =>    2097152,
        'exts'       =>    array('jpg', 'gif', 'png', 'jpeg'),
        'replace'   =>     true,
        'rootPath'   =>    'public/',
        'savePath'   =>    'img/',
        // 'saveName'   =>    array('uniqid',''),
        'autoSub'    =>    true,
        'subName'    =>    'portraits',
    ),
    'PicturesUpload' => array(
        'maxSize'    =>    2097152,
        'exts'       =>    array('jpg', 'gif', 'png', 'jpeg'),
        'replace'   =>     true,
        'rootPath'   =>    'public/',
        'savePath'   =>    'img/',
        'saveName'   =>    array('uniqid',''),
        'autoSub'    =>    true,
        'subName'    =>    'pictures',
    ),
);

文件2:

db.php

<?php
return array(
    'DB_TYPE' => 'mysql',
    'DB_HOST' => 'xxx.xxx.xxx.xxx',
    'DB_USER' => 'xxx',
    'DB_PWD' => 'xxxx',
    'DB_PORT' => 3306,
    'DB_NAME' => 'xxxx',
    'DB_CHARSET' => 'utf8',
)
?>

此文件需要根据自己数据库配置自行修改

为了安全起见 需要创建文件3 index.html
只需要创建空文件就可以 否则发布出来 路由访问有可能直接拉出文件目录结构
