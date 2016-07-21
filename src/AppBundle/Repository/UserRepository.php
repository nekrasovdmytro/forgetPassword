<?php

namespace AppBundle\Repository;
use Symfony\Component\DependencyInjection\ContainerInterface;
use AppBundle\DQL\MD5;

/**
 * UserRepository
 */
class UserRepository extends \Doctrine\ORM\EntityRepository
{
	/**                                      
	 * Get User by hash generated link
	 * 
	 * @param string $hash
	 * @param string $applicationSecret, secret from configuration
	 * @return array
	 */
	public function getUserByTempHash($hash, $applicationSecret)
	{
		$query = $this->createQueryBuilder('u')
			->where('MD5(CONCAT(u.restoreDate, u.salt, :applicationSecret)) = :hash')
			->setParameter('applicationSecret', $applicationSecret)
			->setParameter('hash', $hash)
			->getQuery();

		$result = $query->getResult();
		
		return count($result) ? $result[0] : null;
	}
}
