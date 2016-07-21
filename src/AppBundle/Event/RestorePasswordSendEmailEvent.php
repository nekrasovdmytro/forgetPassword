<?php
/**
 * Created by PhpStorm.
 * User: nekrasov
 * Date: 20.07.16
 * Time: 20:31
 */

namespace AppBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Request;


class RestorePasswordSendEmailEvent extends Event
{
	private $request;
	
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
}

