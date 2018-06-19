<?php
/**
 * Created by PhpStorm.
 * User: Yarmaliuk Mikhail
 * Date: 26.06.2017
 * Time: 18:21
 */

namespace Kakadu\Yii2BaseHelpers;

use yii\swiftmailer\Mailer;

/**
 * Class    MPMailer
 * @package Kakadu\Yii2BaseHelpers
 * @author  Yarmaliuk Mikhail
 * @version 1.0
 */
class MPMailer extends Mailer
{
    /**
     * @var NULL
     */
    private $_toUser = null;

    /**
     * @inheritdoc
     */
    public function compose($view = null, array $params = [])
    {
        if (!empty($params['toUser'])) {
            $this->_toUser = $params['toUser'];
        }

        return parent::compose($view, $params);
    }

    /**
     * @inheritdoc
     */
    public function send($message)
    {
        $bccList = $message->getBcc();

        $message->setBcc([]);

        $result = parent::send($message);

        // Send bcc
        if ($result && !empty($bccList)) {
            foreach ($bccList as $key => $bcc) {
                $message
                    ->setTo([$key => $bcc])
                    ->setBcc([])
                    ->send();
            }
        }

        return $result;
    }

    /**
     * Get message recipient
     *
     * @return NULL
     */
    public function getToUser()
    {
        return $this->_toUser;
    }
}
