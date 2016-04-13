<?php
namespace Kr\OAuthClient\Exception;

class CsrfException extends Exception
{
    public function __construct()
    {
        $message = "CSRF token mismatch";
        $code = 403;
        $previous = null;
        parent::__construct($message, $code, $previous);
    }
}