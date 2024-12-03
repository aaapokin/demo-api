<?php

use app\ApiExternal\Requests\AutoStat\GetMarksRequest;

require __DIR__.'/vendor/autoload.php';

//
$res=(new \app\ApiExternal\ApiAutoStat())->getMarks(new GetMarksRequest());
if($res->isOk()){
    // ......
}

$bool=(new \app\ApiExternal\ApiAutoStat())->getMarksViaQUEUE(new GetMarksRequest());

if(!$bool){
    // ошибка отправки пакета в очередь
}