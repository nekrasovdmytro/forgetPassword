<?php
/**
 * Created by PhpStorm.
 * User: nekrasov
 * Date: 20.07.16
 * Time: 18:45
 */

namespace AppBundle\TempLinkGenerator;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class AbstractTempLinkGenerator     
 * Provides a structure to build generators of temp link
 * @package AppBundle\TempLinkGenerator
 */
abstract class AbstractTempLinkGenerator
{
	const ROUTING_NAME = null;

	/**
	 * @var ContainerInterface
	 */
	protected $container;
	
	/**
	 * @var string
	 */
	protected $hash;

	/**
	 * @var string
	 */
	protected $time;

	/**
	 * @var string
	 */
	protected $link;

	/**
	 * AbstractTempLinkGenerator constructor.
	 * @constructor                  
	 * @param int $time, timestamp
	 */
	public function __construct($time = null)
	{
		$this->time = (null === $time) ? time() : $time;
	}

	/**
	 * generate hash for inherited classes
	 * @return string
	 */
	abstract protected function generateTempHash();
	
	/**
	 * @param ContainerInterface $container
	 */
	public function setContainer(ContainerInterface $container)
	{
		$this->container = $container;
	}

	/**
	 * @return ContainerInterface
	 */
	public function getContainer()
	{
		return $this->container;
	}

	/**
	 * get config of secure hash from app/config/parameters.yml
	 *
	 * @return mixed
	 */
	protected function getApplicationSecureHash()
	{
		return $this->getContainer()->getParameter('secret');
	}

	/**
	 * generate url using Routing component
	 *
	 * @param string $name
	 * @param array|null $params
	 * @return string
	 */
	protected function generateUrl($name, array $params = null)
	{
		return $this->getContainer()->get('router')->generate($name, $params);
	}

	/**              
	 * to generate temp hash with timestamp inside
	 * 
	 * @param string $string
	 * @return string
	 */
	protected function generateMd5TempHash($string)
	{
		return md5($this->time . $string . $this->getApplicationSecureHash());
	}
	

	/**
	 * generate temp link
	 *                    
	 * @throws \Exception, if routing was not redefined
	 * @return TempLink, url
	 */
	public function generateLink()
	{
		if (null === static::ROUTING_NAME) {
			throw new \Exception("Routing name should be redefined at inherited class");
		}
		
		$hash = $this->generateTempHash();

		$link = $this->generateUrl(static::ROUTING_NAME, [
			'hash' => $hash
		]);
		
		return new TempLink($this->time, $hash, $link);
	}


	/**            
	 * method to check hash
	 * 
	 * @param string $hash
	 * @return bool
	 */
	public function checkHash($hash)
	{
		$expected = $this->generateTempHash();
		
		return hash_equals($expected, $hash);
	}
}
