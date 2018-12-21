<?php
date_default_timezone_set('PRC');
$msg = $_POST['msg'];
$to = $_POST['to'];
$from = $_POST['from'];
$roomId = $_POST['roomId'];
$key = $_POST['key'];
$ret = new stdClass();
$ret -> status = 0;
$ret -> msg = $msg;
$ret -> from = $from;
$ret -> to = $to;
$ret -> key = $key;

$records = 'MSG:'.date('Ymd').':'.$roomId;
$redis = new Redis();
$redis -> connect('127.0.0.1');
if($redis -> ttl($records) == -1){
    $redis -> expire($records,86400);
}
$tmpKey = 'MSG:'.date('Ymd').':'.$roomId.':'.$to;
$redis -> rPush($tmpKey,json_encode($ret));
if($redis -> ttl($tmpKey) == -1){
    $redis -> expire($records,86400);
}

$redis -> rpush($records,json_encode($ret));
echo json_encode($ret);
