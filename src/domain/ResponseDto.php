<?php

namespace NipFinder\domain;

class ResponseDto
{
    /**
     * @var object
     */
    private object $content;

    /**
     * @var int
     */
    private int $statusCode;

    /**
     * @param object $content
     * @param int $statusCode
     */
    public function __construct(object $content, int $statusCode){
        $this->content = $content;
        $this->statusCode = $statusCode;
    }

    /**
     * @return object
     */
    public function getContent(): object
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
