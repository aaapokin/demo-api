<?php

namespace app\ApiExternal\Responses\AutoStat;

use app\ApiExternal\Responses\AResponse;
use app\ApiExternal\Responses\EResponseValidate;
use app\dto\AutoStat\ValueResponseDto;

class PriceResponse extends AResponse
{

    private int $price=0;
    private string $name="";

    protected function setData():void
    {
        if($values=$this->getByPath('values')){
            $val = \reset($values);
            $this->price=(int)($val['value']??0);
            $this->name=$val['name']??'';
        }
    }


    public function getPrice(): int
    {
        return $this->price;
    }

    public function getName(): string
    {
        return $this->name;
    }
}