<?php

namespace SimplyTestable\WebClientBundle\Controller;

//use SimplyTestable\WebClientBundle\Entity\Test\Test;
//use SimplyTestable\WebClientBundle\Entity\Task\Task;
//use Symfony\Bundle\FrameworkBundle\Controller\Controller;
//use Symfony\Component\HttpFoundation\Response;

/**
 * Redirects valid-looking URLs to those that match actual controller actions
 * 
 */
class RedirectController extends BaseController
{   
    private $website = null;
    private $test_id = null;
    
    
    private $testFinishedStates = array(
        'cancelled',
        'completed',
        'no-sitemap',
    );
    
    public function testAction($website, $test_id = null) {        
        $this->prepareNormalisedWebsiteAndTestId($website, $test_id);   
        
        if ($this->hasWebsite() && !$this->hasTestId()) {
            if (!$this->getTestService()->getEntityRepository()->hasForWebsite($this->website)) {
                return $this->redirect($this->generateUrl('app', array(), true));
            }
            
            $test_id = $this->getTestService()->getEntityRepository()->getLatestId($this->website);
            
            return $this->redirect($this->getRedirectorUrl($this->website, $test_id));
        }        

        if ($this->hasWebsite() && $this->hasTestId()) {            
            if (!$this->getTestService()->has($this->website, $this->test_id)) {
                return $this->redirect($this->getWebsiteUrl($website));
            } 

            $test = $this->getTestService()->get($this->website, $this->test_id);

            if (in_array($test->getState(), $this->testFinishedStates)) {
                return $this->redirect($this->getResultsUrl($this->website, $this->test_id));
            } else {
                return $this->redirect($this->getProgressUrl($this->website, $this->test_id));
            }              
        }      
    }
    
    public function taskAction($website, $test_id = null, $task_id = null) {
        return $this->redirect($this->getTaskResultsUrl($website, $test_id, $task_id));
    }
    
    
    /**
     * 
     * @return boolean
     */
    private function hasWebsite() {
        return !is_null($this->website);
    }
    
    
    /**
     * 
     * @return boolean
     */
    private function hasTestId() {
        return !is_null($this->test_id);
    }
    
    
    private function prepareNormalisedWebsiteAndTestId($website, $test_id) {        
        $normalisedWebsite = $this->getNormalisedRequestUrl();        
        if ($normalisedWebsite->hasHost() === false) {
            $normalisedWebsite = new \webignition\NormalisedUrl\NormalisedUrl($website . '/' . $test_id);
            
            $this->website = (string)$normalisedWebsite;
            $this->test_id = null;
            return;
        }
        
        if (ctype_digit($test_id)) {
            $this->website = (string)$normalisedWebsite;
            $this->test_id = (int)$test_id;
            return;
        }
        
        $pathParts = explode('/', $normalisedWebsite->getPath());
        $pathPartLength = count($pathParts);
        
        for ($pathPartIndex = $pathPartLength - 1; $pathPartIndex >= 0; $pathPartIndex--) {
            if (ctype_digit($pathParts[$pathPartIndex])) {
                $normalisedWebsite->setPath('');
                
                $this->website = (string)$normalisedWebsite;
                $this->test_id = (int)$pathParts[$pathPartIndex];
                return;
            }
        }
        
        $this->website = (string)$normalisedWebsite;
        $this->test_id = null;
        return;          
    }
    
    
    /**
     * 
     * @param string $website
     * @return string
     */
    protected function getWebsiteurl($website) {
        return $this->generateUrl(
            'app_website',
            array(
                'website' => $website               
            ),
            true
        );        
    }
    
    
    /**
     * 
     * @param string $website
     * @param string $test_id
     * @return string
     */
    protected function getRedirectorUrl($website, $test_id) {
        return $this->generateUrl(
            'app_test_redirector',
            array(
                'website' => $website,
                'test_id' => $test_id
            ),
            true
        );
    }
    
    
    /**
     *
     * @return \SimplyTestable\WebClientBundle\Services\TestService 
     */
    private function getTestService() {
        return $this->container->get('simplytestable.services.testservice');
    }
}
