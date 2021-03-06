<?php

namespace SimplyTestable\WebClientBundle\Resque\Job;

use SimplyTestable\WebClientBundle\Exception\JobPrepareException;
use Symfony\Component\DependencyInjection\ContainerInterface;

abstract class CommandLineJob extends AbstractJob {    
    
    abstract protected function getQueueName();
    abstract protected function getArgumentOrder();
    abstract protected function getCommand();
    abstract protected function failureHandler($output, $returnValue);
    
    public function perform() {
        $output = array();
        $returnValue = null;       
        
        exec($this->buildCommand(), $output, $returnValue);
        
        if ($returnValue !== 0) {
            $this->failureHandler($output, $returnValue);
        }
    }
    
    
    /**
     * The command line command to run, including all arguments in the correct
     * order
     * 
     * @return string
     */
    private function buildCommand() {
        $command = $this->getCommand();
        
        if (!is_array($this->args) || count($this->args) == 0) {
            return $command;
        }
        
        $argumentOrder = $this->getArgumentOrder();
        
        $argumentString = '';
        foreach ($argumentOrder as $argumentName) {
            $argumentString .= ' ' . $this->args[$argumentName];
        }
        
        return $command . $argumentString;
    }
}