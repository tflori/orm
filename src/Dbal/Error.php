<?php

namespace ORM\Dbal;

use ORM\EntityManager;
use ORM\Exception;

/**
 * Validation Error
 *
 * @package ORM\Dbal
 * @author  Thomas Flori <thflori@gmail.com>
 */
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
     * @param array   $params
     * @param ?string    $code
     * @param ?string $message
     * @param ?Error  $previous
     */
    public function __construct(
        array $params = [],
        ?string $code = null,
        ?string $message = null,
        ?Error $previous = null
    ) {
        $this->message   = $message ?: $this->message;
        $this->errorCode = $code ?: static::ERROR_CODE;

        $params['code'] = $this->errorCode;

        $namer = EntityManager::getInstance()->getNamer();

        parent::__construct($namer->substitute($this->message, $params), 0, $previous);
    }
}
