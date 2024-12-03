<?php

namespace app\ApiExternal;

use app\ApiExternal\enums\Methods;
use app\ApiExternal\Requests\ARequest;
use app\ApiExternal\Requests\Edo\GetDocumentsRequest;
use app\ApiExternal\Requests\IRequest;
use app\ApiExternal\Requests\LK\CalcOptionsRequest;
use app\ApiExternal\Responses\Edo\DocumentsResponse;
use app\ApiExternal\Responses\IResponse;
use app\ApiExternal\Responses\ProxyResponse;
use app\helpers\Log\Log;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;

class ApiStandard extends AApiExternal
{

}
