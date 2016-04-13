<?php
namespace Kr\OAuthClient\Exception;

class InvalidAliasException extends Exception
{
    public function __construct($alias)
    {
        $message = "Invalid alias: '$alias'";
        $code = 500;
        $previous = null;
        parent::__construct($message, $code, $previous);
    }
}