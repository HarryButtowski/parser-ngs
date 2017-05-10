<?php

namespace app\components\parser;

use yii\base\Component;

/**
 * Class Parser
 * @package app\components\parser
 */
class Parser extends Component
{
    /**
     * @param Provider      $provider
     * @param HandlerOfData $handler
     * @param array         $params
     *
     * @return array
     */
    public function parseData($provider, $handler, $params = [])
    {
        $generator = $provider->getGenerator($params);

        foreach ($generator as $item) {
            if (!$handler->setData($item)) {
                $generator->send($handler->getState());
            }
        }
    }
}