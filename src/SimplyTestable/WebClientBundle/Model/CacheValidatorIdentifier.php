<?php

namespace SimplyTestable\WebClientBundle\Model;

class CacheValidatorIdentifier {
    
    /**
     *
     * @var array
     */
    private $parameters = array();
   
    
    /**
     *
     * @param string $key
     * @param mixed $value 
     */
    public function setParameter($key, $value) {
        $this->parameters[$key] = $value;
    }
    
    
    /**
     *
     * @return string
     */
    public function __toString() {
        $keyValuePairs = array();
        foreach ($this->parameters as $key => $value) {
            $keyValuePairs[] = $key . ':' . $value;
        }
        
        return md5(implode('+', $keyValuePairs));
    } 
    
    
}