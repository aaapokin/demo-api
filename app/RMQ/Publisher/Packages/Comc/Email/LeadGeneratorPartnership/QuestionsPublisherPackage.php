<?php

namespace app\rmq\Publisher\Packages\Comc\Email\LeadGeneratorPartnership;

use app\dto\LeadGeneratorPartnership\Email\QuestionsDto;
use app\rmq\Publisher\Packages\Comc\Email\ACOMCEmailFreePlaneTextPublisherPackage;

class QuestionsPublisherPackage extends ACOMCEmailFreePlaneTextPublisherPackage
{
    protected string $htmlTemplate = 'lead_generator_partnership/leadgen_request_to_email';
    protected string $subject = "";

    public function __construct(
        readonly protected string $email,
        readonly private QuestionsDto $questionsDto
    ) {
        $this->subject = "🤟 Запрос подк. лидгена: ".$this->quoteValue($questionsDto->title, 255);

        $this->setHtml([
            'type' => $this->questionsDto->type->name,
            'contact' => \implode(',', [$this->questionsDto->phone->toCommonFormat(), $this->questionsDto->email]),
            'questions' => $this->quoteValue($this->questionsDto->questions, 10000),
        ]);
        parent::__construct(
            $this->email,
            $this->subject
        );
    }
}