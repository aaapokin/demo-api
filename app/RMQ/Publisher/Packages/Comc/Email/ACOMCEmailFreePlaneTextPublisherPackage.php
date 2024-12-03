<?php

namespace app\rmq\Publisher\Packages\Comc\Email;

use app\rmq\Publisher\Packages\Comc\ACOMCPublisherPackage;
use Ramsey\Uuid\Uuid;


abstract class ACOMCEmailFreePlaneTextPublisherPackage extends ACOMCPublisherPackage
{
    protected string $routingKey = "email";

    protected string $html = '';
    protected string $htmlTemplate = '';

    /** В кострукторе производим вырезание недопустимых символов quoteValue м вызываем метод setHtml */
    public function __construct(
        readonly private ?string $email,
        readonly private ?string $subject
    )
    {
        parent::__construct();
    }


    protected function setHtml(array $arr): void
    {
        $this->html = \Yii::$app->mailer->render($this->htmlTemplate, $arr);
    }

    protected function quoteValue(string $value, ?int $length = null): string
    {
        $value = strip_tags($value);
//        $value = \Yii::$app->db->quoteValue($value);
//        $value = trim($value, "'");
        if ($length && mb_strlen($value) > $length)
            $value = mb_substr($value, 0, $length);
        return $value;
    }

    public function setPayload(): void
    {
        parent::setPayload();

        $this->addPayloadValue('data.template', 'FREE_PLANE_TEXT')
            ->addPayloadValue('data.params.0.communicationId', Uuid::uuid4()->toString())
            ->addPayloadValue('data.params.0.name', $this->email)
            ->addPayloadValue('data.params.0.key', $this->email)
            ->addPayloadValue('data.params.0.freePlaneText', $this->html)
            ->addPayloadValue('data.params.0.freePlaneSubject', $this->subject);
    }
}