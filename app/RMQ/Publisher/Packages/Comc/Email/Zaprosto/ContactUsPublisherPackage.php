<?php

namespace app\rmq\Publisher\Packages\Comc\Email\Zaprosto;

use app\dto\Techmoney\Email\ContactUsDto;
use app\rmq\Publisher\Packages\Comc\Email\ACOMCEmailFreePlaneTextPublisherPackage;
use app\Env;


class ContactUsPublisherPackage extends ACOMCEmailFreePlaneTextPublisherPackage
{
    protected string $htmlTemplate = 'zaprosto/contact_us';
    protected string $subject = "Новая заявка на смартфон";

    public function __construct(
        readonly protected string     $email,
        readonly private ContactUsDto $contactUsDto
    )
    {
        $this->setHtml([
            'client_full_name' => $this->quoteValue($this->contactUsDto->fullName, 255),
            'client_phone_e164' => $this->contactUsDto->phone->toE164Format(),
            'client_phone' => $this->contactUsDto->phone->toDataBaseFormat(),
            'environment_code' => Env::getBranchName()->name,
            'host' => Env::getHttpBaseUri(),
        ]);
        parent::__construct(
            $this->email,
            $this->subject);
    }

}