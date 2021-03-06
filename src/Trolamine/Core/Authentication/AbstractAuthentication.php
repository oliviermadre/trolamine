<?php
namespace Trolamine\Core\Authentication;

/**
 * An abstract authentication object
 *
 * @author Remi
 *
 */
abstract class AbstractAuthentication implements Authentication
{
    
    protected $user;
    
    protected $mode;
    protected $authorities = null;
    
    /**
     * The username
     *
     * @var string
     */
    protected $principal;
    
    /**
     * The password
     *
     * @var string
     */
    protected $credentials;
    
    /**
     *
     * @param UserDetails   $user
     * @param string        $mode
     * @param array<string> $authorities
     */
    public function __construct(UserDetails $user=null, $mode, array $authorities=array())
    {
        $this->user = $user;
        $this->mode = $mode;
        $this->authorities= $authorities;
    }
    
    /**
     * (non-PHPdoc)
     * @see \Trolamine\Core\Authentication::getAuthenticatedUser()
     */
    public function getAuthenticatedUser()
    {
        return $this->user;
    }
    
    /**
     * (non-PHPdoc)
     * @see \Trolamine\Core\Authentication::getAuthenticationMode()
     */
    public function getAuthenticationMode()
    {
        return $this->mode;
    }
    
    /**
     * (non-PHPdoc)
     * @see \Trolamine\Core\Authentication::getAuthorities()
     */
    public function getAuthorities()
    {
        return $this->authorities;
    }
    
    /**
     * (non-PHPdoc)
     * @see \Trolamine\Core\Authentication\Authentication::getCredentials()
     */
    abstract public function getCredentials();
    
    /**
     * (non-PHPdoc)
     * @see \Trolamine\Core\Authentication\Authentication::getPrincipal()
     */
    public function getPrincipal()
    {
        return $this->principal;
    }
}
