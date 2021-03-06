<?php
namespace SimplyTestable\WebClientBundle\Services;

use Doctrine\ORM\EntityManager;
use SimplyTestable\WebClientBundle\Entity\CacheValidatorHeaders;


class CacheValidatorHeadersService {    
    
    const ENTITY_NAME = 'SimplyTestable\WebClientBundle\Entity\CacheValidatorHeaders';      
    
    /**
     *
     * @var EntityManager 
     */
    private $entityManager;    
    

    /**
     *
     * @var \Doctrine\ORM\EntityRepository
     */
    private $entityRepository;    

    
    /**
     *
     * @param EntityManager $entityManager 
     */
    public function __construct(EntityManager $entityManager) {
        $this->entityManager = $entityManager;
    }
    
    
    /**
     *
     * @param string $identifier
     * @return \SimplyTestable\WebClientBundle\Entity\CacheValidatorHeaders 
     */
    public function get($identifier) {        
        $cacheValidatorHeader = $this->fetch($identifier);
        if (is_null($cacheValidatorHeader)) {
            $cacheValidatorHeader = $this->create($identifier);
        }
        
        return $cacheValidatorHeader;
    }
    
    
    /**
     *
     * @param CacheValidatorHeaders $cacheValidatorHeaders 
     */
    public function store(CacheValidatorHeaders $cacheValidatorHeaders) {
        $this->entityManager->persist($cacheValidatorHeaders);
        $this->entityManager->flush();
    }
    
    
    /**
     *
     * @param string $identifier
     * @return \SimplyTestable\WebClientBundle\Entity\CacheValidatorHeaders 
     */    
    private function fetch($identifier) {
        return $this->getEntityRepository()->findOneBy(array(
            'identifier' => $identifier
        ));        
    }
    
    
    /**
     *
     * @param string $identifier
     * @return \SimplyTestable\WebClientBundle\Entity\CacheValidatorHeaders 
     */
    private function create($identifier) {
        $cacheValidatorHeaders = new CacheValidatorHeaders();
        $cacheValidatorHeaders->setIdentifier($identifier);
        $cacheValidatorHeaders->setLastModifiedDate(new \DateTime());
        
        $this->entityManager->persist($cacheValidatorHeaders);
        $this->entityManager->flush();
        
        return $cacheValidatorHeaders;
    }
    
    
    /**
     *
     * @return \Doctrine\ORM\EntityRepository
     */
    public function getEntityRepository() {
        if (is_null($this->entityRepository)) {
            $this->entityRepository = $this->entityManager->getRepository(self::ENTITY_NAME);
        }
        
        return $this->entityRepository;
    }
    
}