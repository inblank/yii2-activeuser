<?php
namespace app\components;

use yii\swiftmailer\Mailer;
use yii\swiftmailer\Message;

class MailMock extends Mailer{

    /**
     * List of sent emails
     * @var array
     */
    public static $mails = [];

    /**
     * @inheritdoc
     */
    protected function sendMessage($message)
    {
        /** @var Message $message */
        self::$mails[] = [
            'to'=> key($message->getTo()),
            'from' => key($message->getFrom()),
            'subject' => $message->getSubject(),
            'body' => $message->toString(),
        ];
        return true;
    }
}
