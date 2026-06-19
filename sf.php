<?php
error_reporting(0);
header('Content-Type:text/html;charset=utf-8');
date_default_timezone_set('PRC');
require ('../include/db2.php');
require ('../include/function.php');
if (!isset($_POST['action'])) {
    exit('error!');
} 

$uid = isset($_POST['uid']) ? $_POST['uid'] : '';
$money = isset($_POST['money']) ? $_POST['money'] : '';
$token = isset($_POST['token']) ? $_POST['token'] : '';

$uid = isset($_POST['uid']) ? trim($_POST['uid']) : '';
$money = isset($_POST['money']) ? trim($_POST['money']) : '';
$token = isset($_POST['token']) ? trim($_POST['token']) : '';

$key='!#$%7482djdj372';

sf($uid,$key,$token,$money);

function sf($uid,$key,$token,$money) {
   global $db;
   $ip=getip();
   $stoken=md5($uid.$key);
   if ($token!=$stoken){
	json_msg('code', 0);
	exit();
   }	
   $res = $db->query("select id,money from `userb` where id='$uid'");
   if ($db->num_row($res) == 0) {
      	json_msg('code', 0);
      	exit();
   }else{
	   $row=$db->fetch($res);
       $uid=$row['id'];
	   $money=$row['money'];
	   $newmoney=0;
	   $newmoney=$money+$price;
	   $db->query("update `userb` set money='$newmoney' where id='$uid'");
	   $ctime=time();
       $dno=$ctime.mt_rand(100,999);
       $sql="insert into `payb` (`id`,`dno`,`uid`,`payid`,`money`,`ctime`,`paytime`,`flag`,`ip`) VALUES (NULL,'$dno', '$uid','9','$price','$ctime','$ctime','1','$ip')";
	   $db->query($sql);
       	json_msg('code', 1);
   }
}

?>

