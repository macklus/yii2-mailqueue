<?php

/**
 * Message.php
 * @author Saranga Abeykoon http://dantart.com
 */

namespace dantart\mailqueue;

use Yii;
use dantart\mailqueue\models\Queue;

/**
 * Extends `yii\swiftmailer\Message` to enable queuing.
 *
 * @see http://www.yiiframework.com/doc-2.0/yii-swiftmailer-message.html
 */
class Message extends \yii\swiftmailer\Message
{
    /**
     * Enqueue the message storing it in database.
     *
     * @param timestamp $time_to_send
     * @return boolean true on success, false otherwise
     */
    public function queue($time_to_send = 'now')
    {
        if($time_to_send == 'now') {
            $time_to_send = time();
        }

        $item = new Queue();

        $item->from = is_array($this->getFrom()) ? key($this->getFrom()) : $this->getFrom();
        $item->to = is_array($this->getTo()) ? key($this->getTo()) : $this->getTo();
        $item->cc = is_array($this->getCC()) ? key($this->getCC()) : $this->getCC();
        $item->bcc = is_array($this->getBcc()) ? key($this->getBcc()) : $this->getBcc();

        $item->subject = $this->getSubject();
        $item->attempts = 0;
        $item->swift_message = base64_encode(serialize($this));
        $item->time_to_send = date('Y-m-d H:i:s', $time_to_send);

        return $item->save();
    }
}
