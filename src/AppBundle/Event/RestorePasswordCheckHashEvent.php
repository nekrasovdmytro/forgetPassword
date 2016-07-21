<?php
/**
 * Created by PhpStorm.
 * User: nekrasov
 * Date: 20.07.16
 * Time: 18:07
 */

namespace AppBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Request;


class RestorePasswordCheckHashEvent extends Event
{
	private $request;
	private $hash;

	/**
	 * @param Request $request
	 */
	public function setRequest(Request $request)
	{
		$this->request = $request;
	}

	/**
	 * @return Request
	 */
	public function getRequest()
	{
		return $this->request;
	}

	/**
	 * @param string $hash
	 */
	public function setHash($hash)
	{
		$this->hash = $hash;
	}

	/**
	 * @return string
	 */
	public function getHash()
	{
		return $this->hash;
	}
}
