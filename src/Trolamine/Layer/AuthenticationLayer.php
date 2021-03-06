<?php
namespace Trolamine\Layer;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Pyrite\Layer\AbstractLayer;
use Pyrite\Response\ResponseBag;
use Pyrite\Container\Container;
use Symfony\Component\HttpFoundation\Request;
use Trolamine\Core\Authentication\Authentication;
use Trolamine\Core\Authentication\AnonymousAuthenticationToken;
use Trolamine\Core\SecurityContext;

class AuthenticationLayer extends AbstractLayer implements LoggerAwareInterface
{
    /**
     * The authentication var name in the ResponseBag
     *
     * @var string
     */
    const VAR_NAME = 'authentication';
    
    /**
     * The authentication var name in the session
     *
     * @var string
     */
    private static $sessionVarName = 'authentication';
    
    /**
     * The security context
     *
     * @var SecurityContext
     */
    private $securityContext;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Constructor
     *
     * @param SecurityContext $securityContext
     * @param string          $sessionVarName
     */
    public function __construct(SecurityContext $securityContext, $sessionVarName = null)
    {
        $this->securityContext = $securityContext;
        if ($sessionVarName != null) {
            self::$sessionVarName= $sessionVarName;
        }
    }
    
    /**
     *
     * @return string
     */
    public static function getSessionVarName()
    {
        return self::$sessionVarName;
    }
    
    /**
     *
     * @param string $sessionVarName
     */
    public static function setSessionVarName($sessionVarName)
    {
        self::$sessionVarName= $sessionVarName;
    }
    
    /**
     * (non-PHPdoc)
     * @see \Pyrite\Layer\AbstractLayer::before()
     */
    protected function before(ResponseBag $responseBag)
    {
        /* @var $request Request */
        $request = $this->request;
        $authentication = $request->getSession()->get(self::$sessionVarName, null);
        if ($authentication == null) {
            $authentication = new AnonymousAuthenticationToken();
        }
        if ($this->logger) {
            $this->logger->info('entering authentication layer', array(
                'auth'          => $authentication->getPrincipal(),
                'phpsessid'     => $request->cookies->get(ini_get('session.name')),
                'signature'     => md5($request->headers->get('user-agent') . $request->getClientIp()),
                'ip'            => $request->getClientIp(),
                'method'        => $request->getMethod(),
                'uri'           => $request->getUri(),
                'ua'            => $request->headers->get('user-agent'),
            ));
        }

        $responseBag->set(self::VAR_NAME, $authentication);
        $this->securityContext->setAuthentication($authentication);
    }
    
    /**
     * (non-PHPdoc)
     * @see \Pyrite\Layer\AbstractLayer::after()
     */
    protected function after(ResponseBag $responseBag)
    {
        /* @var $authentication Authentication */
        $authentication = $responseBag->get(self::VAR_NAME, null);

        /* @var $request Request */
        $request = $this->request;
        $request->getSession()->set(self::$sessionVarName, $authentication);
    }

    /**
     * @inheritdoc
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }
}
