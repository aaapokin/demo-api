<?php

namespace app\ApiExternal;

use app\ApiExternal\enums\Methods;
use app\ApiExternal\Requests\ARequest;
use app\ApiExternal\Requests\AutoStat\AutoStatAuthRequest;
use app\ApiExternal\Requests\AutoStat\GetCarBodyTypesRequest;
use app\ApiExternal\Requests\AutoStat\GetEquippingLevelRequest;
use app\ApiExternal\Requests\AutoStat\GetGenerationsRequest;
use app\ApiExternal\Requests\AutoStat\GetMarksRequest;
use app\ApiExternal\Requests\AutoStat\GetModelsRequest;
use app\ApiExternal\Requests\AutoStat\GetModificationsRequest;
use app\ApiExternal\Requests\AutoStat\GetPriceRequest;
use app\ApiExternal\Requests\AutoStat\GetYearsRequest;
use app\ApiExternal\Requests\DaData\CleanAddressRequest;
use app\ApiExternal\Requests\Edo\GetDocumentsRequest;
use app\ApiExternal\Requests\IRequest;
use app\ApiExternal\Requests\LK\CalcOptionsRequest;
use app\ApiExternal\Responses\AutoStat\AuthResponse;
use app\ApiExternal\Responses\AutoStat\PriceResponse;
use app\ApiExternal\Responses\AutoStat\ValuesResponse;
use app\ApiExternal\Responses\DaData\CleanAddressResponse;
use app\ApiExternal\Responses\Edo\DocumentsResponse;
use app\ApiExternal\Responses\IResponse;
use app\ApiExternal\Responses\ProxyResponse;
use app\enums\SettingKeys;
use app\helpers\Log\Log;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;

class ApiAutoStat extends AApiExternal
{
    private const TOKEN_LIFE_TIME_SEC = 60 * 60 * 24;

    private function cache(string $key, callable $func)
    {
        $response = \Yii::$app->cache->getOrSet($key, $func, self::TOKEN_LIFE_TIME_SEC - 5);
        if (!$response) {
            \Yii::$app->cache->delete($key);
            return null;
        }
        return $response;
    }

    public function getMarks(GetMarksRequest $getMarksRequest): ValuesResponse
    {
        return $this->cache('ApiAutoStat::'.__FUNCTION__.$getMarksRequest->getSHA1(), function () use ($getMarksRequest) {
            if (!$response = $this->request($getMarksRequest)) {
                $this->resetToken();
                sleep(3);
                $response = $this->request($getMarksRequest);
            }
            return $response;
        });
    }

    public function getMarksViaQUEUE(GetMarksRequest $getMarksRequest): bool
    {
        return $this->requestViaQUEUE($getMarksRequest);
    }

    public function getTokenResponse(): ?AuthResponse
    {
        if (!$response = \Yii::$app->cache->getOrSet(SettingKeys::ApiAutoStatAuth, function () {
            if ($response = $this->request(new AutoStatAuthRequest())) {
                return $response;
            }
            return false;
        }, self::TOKEN_LIFE_TIME_SEC)) {
            $this->resetToken();
            return null;
        }
        return $response;
    }

    public function resetToken(): void
    {
        \Yii::$app->cache->delete(SettingKeys::ApiAutoStatAuth);
    }
}
