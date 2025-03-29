<?php

namespace NipFinder\domain;

class ResponseDto
{
    /**
     * @var object|array
     */
    private $content;

    /**
     * @var int
     */
    private int $statusCode;

    /**
     * @param object|array $content
     * @param int $statusCode
     */
    public function __construct($content, int $statusCode){
        $this->content = $content;
        $this->statusCode = $statusCode;
    }

    /**
     * @return object
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
}
