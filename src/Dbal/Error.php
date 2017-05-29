<?php

namespace ORM\Dbal;

use ORM\Dbal\Column;
use ORM\Namer;

abstract class Error
{
    const ERROR_CODE = 'UNKNOWN';

    /** @var string */
    protected $code = self::ERROR_CODE;

    /** @var string */
    protected $message = '%value% is not valid for column %column% from type %type%';

    /** @var mixed */
    protected $value;

    /** @var Column */
    public $column;

    /**
     * Error constructor.
     *
     * @param Column $column
     */
    public function __construct(Column $column, $value = null, $code = null, $message = null)
    {
        $this->column = $column;
        $this->value = $value;
        $this->code = static::ERROR_CODE;

        $template = new Namer();
        $this->message = $template->substitute(
            $message ? $message : $this->message,
            [
                'value' => (string)$value,
                'column' => $column->name,
                'type' => get_class($column->type)
            ]
        );

        if ($code) {
            $this->code = $code;
        }
    }
}
