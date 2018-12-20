<?php
date_default_timezone_set('PRC');
$from = $_POST['from'];
$to = $_POST['to'];
$roomId = $_POST['roomId'];
$redis = new Redis();
$redis -> connect('127.0.0.1');
$tmpKey = 'MSG:'.date('Ymd').':'.$roomId;
$records = $redis -> lRange($tmpKey,0,-1);
$msgs = array();
foreach ($records as $r)
{
    $msgs[] = json_decode($r,true);
}
$ret = new stdClass();
$ret -> status = 0;
$ret -> msg = $msgs;
echo json_encode($ret);