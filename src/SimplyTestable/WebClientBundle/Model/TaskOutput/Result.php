<?php

namespace SimplyTestable\WebClientBundle\Model\TaskOutput;

class Result {
    
    const OUTCOME_PASSED = 'passed';
    const OUTCOME_FAILED = 'failed';
    
    /**
     * Collection of Message objects
     * 
     * @var array
     */
    private $messages;
    
    
    /**
     *
     * @var string
     */
    private $outcome;
    
    public function __construct() {
        $this->messages = array();
    }
    
    
    /**
     *
     * @param Message $message
     * @return \SimplyTestable\WebClientBundle\Model\TaskOutput\Result 
     */
    public function addMessage(Message $message) {
        $this->messages[] = $message;
        return $this;
    }
    
    
    /**
     * Get collection of error messages
     *  
     * @return array 
     */    
    public function getErrors() {
        $errors = array();

        foreach ($this->messages as $message) {
            /* @var $message Message */
            if ($message->getType() == Message::TYPE_ERROR) {
                $errors[] = $message;
            }
        }
        
        return $errors;
    }
    
    
    public function getFirstError() {        
        $errors = $this->getErrors();
        return $errors[0];
    }
    
    
    /**
     *
     * @return int
     */
    public function getErrorCount() {
        return count($this->getErrors());
    }
    
    
    /**
     *
     * @return boolean
     */
    public function hasErrors() {
        return $this->getErrorCount() > 0;
    }
    
    
    /**
     *
     * @return string
     */
    public function getOutcome() {
        if ($this->hasErrors()) {
            return self::OUTCOME_FAILED;
        }
        
        return self::OUTCOME_PASSED;
    }
    
    
    /**
     *
     * @return boolean 
     */
    public function isHttpRedirectLoopFailure() {
        return $this->isOfErrorClass('/http-retrieval-redirect-loop/');        
    } 
    
    
    /**
     *
     * @return boolean 
     */
    public function isHttpRedirectLimitFailure() {
        return $this->isOfErrorClass('/http-retrieval-redirect-limit-reached/');       
    }     
    
    
    /**
     *
     * @return boolean 
     */
    public function isCharacterEncodingFailure() {
        return $this->isOfErrorClass('/character-encoding/');
    }  
    
    /**
     * 
     * @return boolean
     */
    public function isCurlTimeoutFailure() {
        return $this->isOfErrorClass('/http-retrieval-curl-code-28/');      
    }
    
    /**
     * 
     * @return boolean
     */
    public function isCurlDnsResolutionFailulre() {
        return $this->isOfErrorClass('/http-retrieval-curl-code-6/');      
    }
    
    /**
     * 
     * @return boolean
     */
    public function isCurlUrlFormatFailure() {
        return $this->isOfErrorClass('/http-retrieval-curl-code-3/');      
    }  
    
    
    /**
     * 
     * @return boolean
     */
    public function isHttpClientErrorFailure() {
        return $this->isOfErrorClass('/http-retrieval-4\d\d/');
    }
    
    
    
    /**
     * 
     * @param string $errorClassPattern
     * @return boolean
     */
    private function isOfErrorClass($errorClassPattern) {
        foreach ($this->getErrors() as $error) {
            if (preg_match($errorClassPattern, $error->getClass()) > 0) {
                return true;
            }
        }
        
        return false;          
    }
        
    
}