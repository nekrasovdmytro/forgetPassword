<?php
/**
 * Created by PhpStorm.
 * User: nekrasov
 * Date: 20.07.16
 * Time: 18:42
 */

namespace AppBundle\TempLinkGenerator;


use AppBundle\Entity\User;
use Symfony\Component\Validator\Exception\InvalidArgumentException;

class RestoreUserPasswordLinkGenerator extends AbstractTempLinkGenerator
{
	const ROUTING_NAME = 'restore_action';

	/**
	 * RestoreUserPasswordLinkGenerator constructor.
	 * @constructor
	 * @param int|null $time
	 */
	public function __construct($time = null)
	{
		parent::__construct($time);
	}

	/**
	 * @var User
	 */
	protected $user;

	/**
	 * @param User $user
	 */
	public function setUser(User $user)
	{
		$this->user = $user;
	}

	/**
	 * @return User
	 */
	public function getUser()
	{
		return $this->user;
	}

	/**
	 * @return string, generate temp hash
	 */
	protected function generateTempHash()
	{
		if (!($this->user instanceof User)) {
			throw new InvalidArgumentException('$this->user should be instance of User');
		}

		$string = $this->user->getSalt();
		
		return $this->generateMd5TempHash($string);
	}
}
