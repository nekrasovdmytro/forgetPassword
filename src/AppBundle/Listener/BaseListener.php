<?php
/**
 * Created by PhpStorm.
 * User: nekrasov
 * Date: 20.07.16
 * Time: 20:46
 */

namespace AppBundle\Listener;

use Symfony\Component\DependencyInjection\ContainerInterface;

class BaseListener
{
	protected $container;

	/**
	 * BaseListener constructor.
	 *
	 * @constructor
	 * @param ContainerInterface $container
	 */
	public function __construct(ContainerInterface $container)
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
	 * @param ContainerInterface $container
	 */
	public function setContainer(ContainerInterface $container)
	{
		$this->container = $container;
	}
}
