<?php

use app\ApiExternal\Requests\AutoStat\GetMarksRequest;

require __DIR__.'/vendor/autoload.php';

//
(new \app\ApiExternal\ApiAutoStat())->getMarks(new GetMarksRequest());