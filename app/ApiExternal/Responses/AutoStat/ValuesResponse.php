<?php

namespace app\ApiExternal\Responses\AutoStat;

use app\ApiExternal\Responses\AResponse;
use app\ApiExternal\Responses\EResponseValidate;
use app\dto\AutoStat\ValueResponseDto;

class ValuesResponse extends AResponse
{
    /**
     * @return \app\core\dto\AutoStat\ValueResponseDto[]
     */
    public function getValues(): array
    {
        $return=[];
        foreach ($this->getByPath('values') as $item)if($item['value']??null){
            $return[]=new ValueResponseDto($item['value']);
        }
        return $return;
    }
}