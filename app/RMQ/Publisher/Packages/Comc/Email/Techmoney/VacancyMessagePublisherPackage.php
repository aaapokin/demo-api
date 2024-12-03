<?php

namespace app\rmq\Publisher\Packages\Comc\Email\Techmoney;

use app\dto\Techmoney\Email\ContactUsDto;
use app\dto\Techmoney\Email\VacancyMessageDto;
use app\rmq\Publisher\Packages\Comc\Email\ACOMCEmailFreePlaneTextPublisherPackage;
use app\Env;


class VacancyMessagePublisherPackage extends ACOMCEmailFreePlaneTextPublisherPackage
{
    protected string $htmlTemplate = 'techmoney/vacancy_message';
    protected string $subject = "Отзыв на вакансию с сайта techmoney.ru";

    public function __construct(
        readonly protected string     $email,
        readonly private VacancyMessageDto $dto
    )
    {
        $this->setHtml([
            'client_phone' => $this->dto->phone->toHumanFormat(),
            'client_phone_e164' => $this->dto->phone->toE164Format(),
            'client_full_name' => $this->quoteValue($this->dto->fullName,255),
            'vacancy_url' => $this->quoteValue($this->dto->url,255),
            'message' =>  $this->quoteValue($this->dto->message,500),
            'host' => Env::getHttpBaseUri(),
            'environment_code' => Env::getBranchName()->name,
        ]);
        parent::__construct(
            $this->email,
            $this->subject);
    }

}