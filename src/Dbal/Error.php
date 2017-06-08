<?php

namespace ORM\Dbal;

use ORM\Dbal\Column;
use ORM\EntityManager;
use ORM\Exception;
use ORM\Namer;

class Error extends Exception
{
    const ERROR_CODE = 'UNKNOWN';

    /** @var string */
    protected $message = 'ERROR(%code%) occurred';

    /** @var string */
    protected $errorCode;

    /**
     * Error constructor
     *
     * @param array $params
     * @param null  $code
     * @param null  $message
     * @param Error $previous
     */
    public function __construct(array $params = [], $code = null, $message = null, Error $previous = null)
    {
        $this->message = $message ?: $this->message;
        $this->code = $code ?: static::ERROR_CODE;

        $params['code'] = $this->code;

        $namer = EntityManager::getInstance()->getNamer();

        parent::__construct($namer->substitute($this->message, $params), 0, $previous);
    }
}
