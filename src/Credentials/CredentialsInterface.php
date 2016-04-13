<?php
namespace Kr\OAuthClient\Credentials;

interface CredentialsInterface
{
    /**
     * @return string
     */
    public static function getType();

    /**
     * @return array
     */
    public function getCredentials();

    /**
     * @return boolean
     */
    public function areExpired();
}