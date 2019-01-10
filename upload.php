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
if(in_array($ext,array('jpg','gif','jpeg','bmp','png'))){
$ret -> pic = 1;
}
move_uploaded_file($file['tmp_name'],'./upload/'.$file['name']);
}
$ret -> url = '.\\upload\\'.$file['name'];
$ret -> status = 1;
echo json_encode($ret,JSON_UNESCAPED_UNICODE);
