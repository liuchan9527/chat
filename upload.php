<?php
$file = $_FILES['file'];
if(!is_dir('./upload/')){
 mkdir('./upload/',755);
}
error_reporting(E_ALL);
ini_set('display_errors',true);
$ret = new stdclass();
$ret -> pic = 0;
if($file['error'] == 0){
if(strpos($file['name'],'.php') !== false){
	echo 'not support';exit;
}
$ext = explode('.',$file['name']);
$ext = end($ext);
$fileName = $file['name'];
if(in_array($ext,array('jpg','gif','jpeg','bmp','png'))){
$ret -> pic = 1;
$fileName = time() .'.'.$ext;
}
move_uploaded_file($file['tmp_name'],'./upload/'.$fileName);
}
$ret -> msg = '.\\upload\\'.$fileName;
$ret -> file = 1;
$ret -> status = 1;
$ret -> from = $_POST['from'];
$ret -> key = $_POST['key'];
$to = $_POST['to'];
$ret -> to = $to;
$roomId = $_POST['roomId'];

$records = 'MSG:'.date('Ymd').':'.$roomId;
$redis = new Redis();
$redis -> connect('127.0.0.1');
if($redis -> ttl($records) == -1){
    $redis -> expire($records,86400);
}
$tmpKey = 'MSG:'.date('Ymd').':'.$roomId.':'.$to;
$redis -> rPush($tmpKey,json_encode($ret));
if($redis -> ttl($tmpKey) == -1){
    $redis -> expire($tmpKey,86400);
}

$redis -> rpush($records,json_encode($ret));
echo json_encode($ret,JSON_UNESCAPED_UNICODE);
