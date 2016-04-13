<?php
namespace Kr\OAuthClient\Token;

abstract class AbstractToken implements TokenInterface
{
    protected $token, $expiresAt;

    /**
     * AbstractToken constructor.
     * @param string $token
     * @param \DateTime|null $expiresAt
     */
    public function __construct($token, \DateTime $expiresAt = null)
    {
        $this->token        = $token;
        $this->expiresAt    = $expiresAt;
    }

    /**
     * @inheritdoc
     */
    public function isExpired()
    {
        if($this->expiresAt === null) {
            return false;
        }
        return $this->expiresAt < new \DateTime();
    }

    /**
     * @inheritdoc
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @inheritdoc
     */
    public function getExpiresAt()
    {
        return $this->expiresAt;
    }
}