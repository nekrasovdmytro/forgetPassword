<?php
/**
 * Created by PhpStorm.
 * User: nekrasov
 * Date: 21.07.16
 * Time: 11:42
 */

namespace AppBundle\TempLinkGenerator;


class NewPasswordLinkGenerator extends AbstractTempLinkGenerator
{
	const ROUTING_NAME = 'new_password_action';

	protected function generateTempHash()
	{
		return $this->generateMd5TempHash(uniqid($this->getApplicationSecureHash()));
	}
}
