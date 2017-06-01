<?php

namespace ORM\Dbal;

use ORM\Dbal\Column;
use ORM\EntityManager;
use ORM\Namer;

class Error
{
    const ERROR_CODE = 'UNKNOWN';

    /** @var string[] */
    protected $params = [
        'code' => self::ERROR_CODE
    ];

    /** @var string */
    protected $message = 'ERROR(%code%) occurred';

    /**
     * Error constructor.
     * @param array $params
     * @param null $code
     * @param null $message
     */
    public function __construct(array $params = [], $code = null, $message = null)
    {
        // set code from concrete class
        $this->params['code'] = static::ERROR_CODE;

        // overwrite message from params
        if ($message) {
            $this->message = $message;
        }

        // overwrite code from params
        if ($code) {
            $this->params['code'] = $code;
        }

        $this->addParams($params);
    }

    public function addParams($params)
    {
        $this->params = array_merge($this->params, $params);
    }

    public function getMessage()
    {
        return EntityManager::getInstance()->getNamer()->substitute($this->message, $this->params);
    }

    public function getCode()
    {
        return $this->params['code'];
    }
}
