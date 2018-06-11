<?php

namespace Polyloans\Rest;

use Throwable;

class RestException extends \Exception
{
    protected $contentBody;

    public function __construct(string $message = "", int $code = 0, string $content = '', Throwable $previous = null)
    {
        $this->contentBody = $content;
        parent::__construct($message, $code, $previous);
    }

    public function getContentBody()
    {
        return $this->contentBody;
    }
}
