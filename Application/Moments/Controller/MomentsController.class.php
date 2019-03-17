<?php
namespace Monments\Controller;
use Think\Controller;

class MonmentsController extends Controller{
  function __construct() {
    if(!session()) return show(-999,'未登录');
  }

  

}