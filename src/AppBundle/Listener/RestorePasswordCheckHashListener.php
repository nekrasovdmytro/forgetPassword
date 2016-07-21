<?php
/**
 * Created by PhpStorm.
 * User: nekrasov
 * Date: 20.07.16
 * Time: 18:13
 */

namespace AppBundle\Listener;


use AppBundle\Event\RestorePasswordCheckHashEvent;
use AppBundle\Helper\DateTimeHelper;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Class RestorePasswordListener
 *   
 * Class provides listener for activation link to check hash to restore password 
 * @package AppBundle\Listener
 */
class RestorePasswordCheckHashListener extends BasePasswordRestoreListener
{
	const NEW_PASSWORD_ACTION_SESSION_KEY = 'new-password-hash';
	
	/**
	 * @var \AppBundle\TempLinkGenerator\NewPasswordLinkGenerator
	 */
	protected $newPasswordLinkGenerator;

	/**
	 * @var \Symfony\Component\HttpFoundation\Session\Session
	 */
	protected $session;

	/**
	 * RestorePasswordListener constructor.  
	 * 
	 * @constructor
	 * @param ContainerInterface $container
	 */
	public function __construct(ContainerInterface $container)
	{
		parent::__construct($container);

		$this->newPasswordLinkGenerator = $this->getContainer()->get('temp.link.generator.to.new.password');
		$this->session = $this->getContainer()->get('session');
	}

	/**                                              
	 * Event handler to check password on restore action
	 * 
	 * @param RestorePasswordCheckHashEvent $event
	 * @return RedirectResponse
	 * @throws \Exception, hash is wrong
	 * @throws \Exception, temp link is came lifetime
	 */
	public function onCheckHashEvent(RestorePasswordCheckHashEvent $event)
	{
		$request = $event->getRequest();
		$hash = $event->getHash();

		$em = $this->doctrine->getEntityManager();

		/**
		 * @var \AppBundle\Entity\User $user
		 */
		$user = $em->getRepository('AppBundle:User')
			->getUserByTempHash($hash, $this->getApplicationSecret());
		
		try {
			if (null === $user) {
				  throw new \Exception("Hash is wrong");
			}

			$hoursFromLastRestoreLinkSend = DateTimeHelper::getDiffInHours(
				$user->getRestoreDate(),
				new \DateTime()
			);
			$maxHoursToRestore = $this->getMaxHoursForRestoreLink();

			// check if link is actual
			if ($hoursFromLastRestoreLinkSend > $maxHoursToRestore) {
				throw new \Exception("Error! Hash link came lifetime.");
			}

			/**
			 * @var \AppBundle\TempLinkGenerator\TempLink $tempLink
			 */
			$tempLink = $this->newPasswordLinkGenerator->generateLink();

			$sessionValue = [
				$user->getId(),
				$tempLink->hash
			];
			
			// set new hash for to go on page to new password
			$this->session->set(self::NEW_PASSWORD_ACTION_SESSION_KEY, serialize($sessionValue));

			//temporary redirect new password
			throw new HttpException(
				Response::HTTP_TEMPORARY_REDIRECT, 
				null, 
				null, 
				[
					'Location' => $tempLink->link
				]
			);

		} catch (HttpException $e) { //catch http exceptions first
			throw $e;
		} catch (\Exception $e) {
			$this->twig->addGlobal('message', $e->getMessage());
		}
	}
}
