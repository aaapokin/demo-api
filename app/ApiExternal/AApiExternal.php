<?php

namespace app\ApiExternal;

use app\ApiExternal\enums\Formats;
use app\ApiExternal\enums\Methods;
use app\ApiExternal\Requests\IRequest;
use app\ApiExternal\Responses\ApiExternalResponse;
use app\ApiExternal\Responses\IResponse;
use app\Env;
use app\helpers\Log\Log;
use app\jobs\SendApiExternalPackageJob;
use app\jobs\SendRmqPackageJob;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\RequestOptions;
use yii\web\BadRequestHttpException;


abstract class AApiExternal implements IApiExternal
{
    public function request(IRequest $request): IResponse
    {
        $client = new Client([
            'base_uri' => $request->getHost(),
            'verify' => Env::apiExternalSsl(),
        ]);
        $RequestOptions = [
            RequestOptions::TIMEOUT => $request->getTimeout(),
            RequestOptions::CONNECT_TIMEOUT => $request->getReadTimeout(),
            RequestOptions::READ_TIMEOUT => $request->getConnectTimeout(),
            RequestOptions::HEADERS => $request->getHeaders(),
        ];

        if ($request->getMethod() == Methods::GET) $RequestOptions[RequestOptions::QUERY] = $request->getPayload();
        else if ($request->getFormat() == Formats::form) $RequestOptions[RequestOptions::FORM_PARAMS] = $request->getPayload();
        else if ($request->getFormat() == Formats::json) $RequestOptions[RequestOptions::JSON] = $request->getPayload();

        try {
            $response = $client->request($request->getMethod()->name, $request->getUrl(), $RequestOptions);
        } catch (BadResponseException $e){
            Log::error($err='GuzzleHttp BadResponseException error '.$e->getResponse()->getBody()->getContents(), $request, $e, $request->getPayload());
            return $request->getResponse(new ApiExternalResponse(
                $e->getResponse()->getStatusCode(),
                $e->getResponse()->getHeaders(),
                [],
                $request->getHeaders(),
                $request->getHost() . $request->getUrl(),
                $request->getPayload(),
                $err
            ));
        }catch (\Throwable $e) {
            Log::error($err='request error', $request, $e, $request->getPayload());
            return $request->getResponse(new ApiExternalResponse(
                $e->getResponse()->getStatusCode(),
                $e->getResponse()->getHeaders(),
                [],
                $request->getHeaders(),
                $request->getHost() . $request->getUrl(),
                $request->getPayload(),
                $err
            ));
        }

        try {
            $payload = json_decode((string)$response->getBody(), 1);
        } catch (\Throwable $e) {
            Log::error($err='json_decode error', $request, $e, (string)$response->getBody());
            return $request->getResponse(new ApiExternalResponse(
                $response->getResponse()->getStatusCode(),
                $response->getResponse()->getHeaders(),
                [],
                $request->getHeaders(),
                $request->getHost() . $request->getUrl(),
                $request->getPayload(),
                $err
            ));
        }

        return $request->getResponse(new ApiExternalResponse(
            $response->getStatusCode(),
            $response->getHeaders(),
            $payload,
            $request->getHeaders(),
            $request->getHost() . $request->getUrl(),
            $request->getPayload()
        ));


    }


    public function requestViaQUEUE(IRequest $request): bool
    {
        return \Yii::$app->queue->push(new SendApiExternalPackageJob(['package' => $request]));

    }
}
