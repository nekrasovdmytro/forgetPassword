<?php
/**
 * Created by PhpStorm.
 * User: nekrasov
 * Date: 20.07.16
 * Time: 20:33
 */

namespace AppBundle\Listener;

use AppBundle\Event\RestorePasswordSendEmailEvent;
use AppBundle\Helper\DateTimeHelper;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class RestorePasswordSendEmailListener 
 * Provides a listener to send email with restore link
 * 
 * @package AppBundle\Listener
 */
class RestorePasswordSendEmailListener extends BasePasswordRestoreListener
{
	/**
	 * @var \Swift_Mailer
	 */
	protected $mailer;

	/**
	 * @var \AppBundle\TempLinkGenerator\RestoreUserPasswordLinkGenerator
	 */
	protected $tmpLinkGenerator;
	
	public function __construct(ContainerInterface $container)
	{
		parent::__construct($container);
		
		$this->mailer = $this->getContainer()->get('mailer');
		$this->tmpLinkGenerator =  $this->getContainer()->get('temp.link.generator.to.restore.password');
	}
	
	/**                                           
	 * On this event will be generated and sent email with link to restore password
	 * 
	 * @param RestorePasswordSendEmailEvent $event
	 */
	public function onSendEmailToRestorePassword(RestorePasswordSendEmailEvent $event)
	{
		$request = $event->getRequest();
		$email = $request->get('email');

		$em = $this->doctrine->getEntityManager();

		/**
		 * @var \AppBundle\Entity\User $user
		 */
		$user = $em->getRepository('AppBundle:User')
			->findOneByEmail($email);

		try {
			if (!$user) {
				throw new \Exception("Not found any person with email : $email");
			}

			$hoursFromLastRestoreLinkSend = DateTimeHelper::getDiffInHours(
				$user->getRestoreDate(),
				new \DateTime()
			);
			$maxHoursToRestore = $this->getMaxHoursForRestoreLink();

			if ($hoursFromLastRestoreLinkSend < $maxHoursToRestore) {
				echo  $hoursFromLastRestoreLinkSend;
				throw new \Exception("Restore link already sent, try again later");
			}

			//generate temp link
			$this->tmpLinkGenerator->setUser($user);

			/**
			 * @var \AppBundle\TempLinkGenerator\TempLink $tempLink
			 */
			$tempLink = $this->tmpLinkGenerator->generateLink();

			//send email to restore password by link
			$isSent = $this->sendEmailWithTempLink($email, $tempLink->link);

			//is sent email
			if (!$isSent) {
				throw new \Exception('Can\'t send email');
			}

			$restoreTime = new \DateTime();
			$restoreTime->setTimestamp($tempLink->time);

			$user->setRestoreDate($restoreTime);
			$em->flush();

			/**
			 * @todo refactoring put all messages to dictionary and use only aliases
			 */
			$this->twig->addGlobal('message', "Message is sent");

		} catch (\Exception $e) {
			$this->twig->addGlobal('message', $e->getMessage());
		}
	}

	/**    
	 * send email with restore message
	 * 
	 * @param $email
	 * @param string $link
	 * @return boolean
	 * @throws \Twig_Error
	 */
	protected function sendEmailWithTempLink($email, $link)
	{
		/**
		 * @var \Symfony\Bundle\TwigBundle\TwigEngine $templating
		 */
		$templating = $this->getContainer()->get('templating');
		
		$message = \Swift_Message::newInstance()
			->setSubject('Restore Password')
			->setFrom($this->getContainer()->getParameter('support_email'))
			->setTo($email)
			->setBody(
				$templating->render(
					'AppBundle:email:restore.html.twig', [
						'link' => $link
					]
				),
				'text/html'
			);


		return $this->mailer->send($message);
	}
}
