<?php
namespace Kr\OAuthClient\Token;

abstract class AbstractToken implements TokenInterface
{
    /** @var string */
    protected $token;

    /** @var \DateTime */
    protected $expiresAt;

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
    public function setToken($token)
    {
        $this->token = $token;
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
    public function getExpiresAt()
    {
        return $this->expiresAt;
    }

    /**
     * @inheritdoc
     */
    public function setExpiresAt(\DateTime $expiresAt = null)
    {
        $this->expiresAt = $expiresAt;
    }

    /**
     * @inheritdoc
     */
    public function setExpiresIn($seconds)
    {
        if($seconds === null) {
            return;
        } else {
        }
        $expiresAt = (new \DateTime())->modify("+$seconds seconds");
        $this->setExpiresAt($expiresAt);
    }
}