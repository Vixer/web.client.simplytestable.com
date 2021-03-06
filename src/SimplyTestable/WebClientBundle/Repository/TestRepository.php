<?php
namespace SimplyTestable\WebClientBundle\Repository;

use Doctrine\ORM\EntityRepository;
use SimplyTestable\WebClientBundle\Entity\Test\Test;

class TestRepository extends EntityRepository
{  
    public function hasForWebsite($website) {            
        $queryBuilder = $this->createQueryBuilder('Test');
        $queryBuilder->select('count(Test.testId)');
        $queryBuilder->where('Test.website = :Website');
        $queryBuilder->setParameter('Website', $website);        
        $queryBuilder->orderBy('Test.id', 'DESC');
        
        $result = $queryBuilder->getQuery()->getResult();        

        return $result[0][1] > 0;
    }
    
    
    public function getLatestId($website) {        
        $queryBuilder = $this->createQueryBuilder('Test');
        $queryBuilder->select('Test.testId');
        $queryBuilder->where('Test.website = :Website');
        $queryBuilder->setParameter('Website', $website);
        $queryBuilder->orderBy('Test.id', 'DESC');
        $queryBuilder->setMaxResults(1);
        
        $result = $queryBuilder->getQuery()->getResult();
        
        if (count($result) == 0) {
            return null;
        }        
        
        return (int)$result[0]['testId'];      
    }
}