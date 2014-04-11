<?php
namespace Trolamine\Factory;

use Trolamine\Core\SecurityContext;
class Secured {
    
    const PRE_AUTHORIZE = 'preAuthorize';
    const POST_AUTHORIZE = 'postAuthorize';
    const PRE_FILTER = 'preFilter';
    const POST_FILTER = 'postFilter';
    
    /**
     * The associative array of parameters :
     * array (
     *     ['methodNameToSecure'] => array (
     *         ['preAuthorize'] => array<ConfigAttribute>(...),
     *         ['postAuthorize'] => array<ConfigAttribute>(...)
     *     ),
     *     ...
     * )
     * 
     * @var array
     */
    protected $config;
    
    /**
     * The securityContext
     * 
     * @var SecurityContext
     */
    protected $securityContext;
    
    /**
     * Constructor
     * 
     * @param SecurityContext $securityContext
     * @param array $config
     */
    public function __construct(SecurityContext $securityContext, array $config = array()) {
        $this->securityContext = $securityContext;
        $this->config = $config;
    }
    
    /**
     * Retrieves the conditions to check and checks them
     * 
     * @param string $method     the method name to check
     * @param array  $parameters the method parameters
     * @param string $actionName the security action (PRE/POST-AUTH/FILT)
     * @param mixed  $object     the reference object
     */
    protected function check($method, array $parameters, $actionName, $object=null) {
        $methodName = $method;
        
        if (is_array($this->config) && count($this->config)>0 && array_key_exists($methodName, $this->config)) {
            $actions = $this->config[$methodName];
            if (is_array($actions) && count($actions)>0 && array_key_exists($actionName,  $actions)) {
                $checks = $actions[$actionName];
                if (is_array($checks) && count($checks)>0) {
                    //TODO replace the ref args by the real value
                    $this->securityContext->getAccessDecisionManager()->decide(
                        $this->securityContext->getAuthentication(),
                        $object,
                        $checks
                    );
                }
            }
        }
    }
    
    /**
     * The PreAuthorize method to be called before the real method call
     */
    public function preAuthorize($method, array $parameters=array()) {
        $this->check($method, $parameters, self::PRE_AUTHORIZE);
    }
    
    /**
     * The PostAuthorize method to be called after the real method has returned a value
     * 
     * @param mixed $response the response of the method to secure
     */
    public function postAuthorize($method, array $parameters=array(), $response) {
        $this->check($method, $parameters, self::POST_AUTHORIZE, $response);
    }
    
}