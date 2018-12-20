<?php
date_default_timezone_set('PRC');
$from = $_POST['from'];
$to = $_POST['to'];
$roomId = $_POST['roomId'];
$redis = new Redis();
$redis -> connect('127.0.0.1');
$tmpKey = 'MSG:'.date('Ymd').':'.$roomId.':'.$from;
$nums = $redis -> lLen($tmpKey);
$msgs = array();
for($i = 0; $i <$nums;$i++)
{
    $msg = $redis -> rPop($tmpKey);
    $msgs[] = json_decode($msg,true);
}
$ret = new stdClass();
$ret -> status = 0;
$ret -> msg = $msgs;
echo json_encode($ret);