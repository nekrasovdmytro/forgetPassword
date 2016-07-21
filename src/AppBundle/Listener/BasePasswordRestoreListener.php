<?php
/**
 * Created by PhpStorm.
 * User: nekrasov
 * Date: 20.07.16
 * Time: 21:17
 */

namespace AppBundle\Listener;


use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class BasePasswordRestoreListener
 * @package AppBundle\Listener
 */
class BasePasswordRestoreListener extends BaseListener
{
	/**
	 * @var \Twig_Environment $twig
	 */
	protected $twig;
	
	/**
	 * @var \Doctrine\Bundle\DoctrineBundle\Registry $doctrine
	 */
	protected $doctrine;
	
	public function __construct(ContainerInterface $container)
	{
		parent::__construct($container);
		
		$this->twig = $this->getContainer()->get('twig');
		$this->doctrine = $this->getContainer()->get('doctrine');
	}

	/**              
	 * Get max time of restore link
	 * 
	 * @return string
	 */
	protected function getMaxHoursForRestoreLink()
	{
		return $this->getContainer()->getParameter('max_hours_to_use_restore_link');
	}

	/**              
	 * Get application secret key
	 * 
	 * @return string
	 */
	protected function getApplicationSecret()
	{
		return $this->getContainer()->getParameter('secret');
	}
}
