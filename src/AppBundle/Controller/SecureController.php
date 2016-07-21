<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Event\RestorePasswordCheckHashEvent;
use AppBundle\Event\RestorePasswordSendEmailEvent;
use AppBundle\Listener\RestorePasswordCheckHashListener;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Security\Csrf\CsrfToken;

/**
 * Class SecureController 
 * 
 * @todo user Form component for generating forms and validation
 * @todo use checking for secure https ($request->isSecure())
 * @package AppBundle\Controller
 */
class SecureController extends Controller
{

	const CSRF_AUTH_TOKEN_ID = 'authenticate';

	/**
	 * Action to login user
	 *
	 * @Route("/login", name="login")
	 */
	public function loginAction()
	{
		$authenticationUtils = $this->get('security.authentication_utils');

		$error = $authenticationUtils->getLastAuthenticationError();
		$lastUsername = $authenticationUtils->getLastUsername();

		return $this->render(
			'AppBundle:security:login.html.twig', [
				'last_username' => $lastUsername,
				'error'         => $error,
			]
		);
	}

	/**
	 * @Route("/logout", name="logout")
	 */
	public function logoutAction(){}

	/**
	 * Action to restore password by email
	 *
	 * @param Request $request
	 * @param string $hash
	 * @return Response
	 *
	 * @Route("/restore", name="restore_page")
	 * @Route("/restore/{hash}", name="restore_action")
	 */
	public function restorePasswordAction(Request $request, $hash = null)
	{
		$dispatcher = $this->get('event_dispatcher');

		// if restore form sent
		if ($request->isMethod('post')) {

			$this->checkCsrfToken($request->get('token'), self::CSRF_AUTH_TOKEN_ID);

			$event = new RestorePasswordSendEmailEvent();
			$event->setRequest($request);
			
			$dispatcher->dispatch('restore.password.send.email.event', $event);
		}
		
		// if hash is set
		if (null !== $hash) {
			$event = new RestorePasswordCheckHashEvent();
			$event->setRequest($request);
			$event->setHash($hash);

			$dispatcher->dispatch('restore.password.event', $event);
		}
		
		return $this->render(
			'AppBundle:security:restore.html.twig'
		);
	}

	/**
	 *  Action to set new password
	 *
	 *  @todo refactoring method
	 *  @Route("/new-password/{hash}", name="new_password_action")
	 */
	public function newPasswordAction(Request $request, $hash)
	{
		/**
		 * @var \Symfony\Component\HttpFoundation\Session\Session $session
		 */
		$session = $this->get('session');

		// session new password key is not found
		if (!$sessionNewPassword = $session->get(RestorePasswordCheckHashListener::NEW_PASSWORD_ACTION_SESSION_KEY)) {
			throw new \Exception("Error. Forbidden!");
		}

		list($id, $sessionHash) = unserialize($sessionNewPassword);

		if (!hash_equals($sessionHash, $hash)) { // hash is incorrect
			 throw new \Exception("Error. Hash is wrong!");
		}

		if ($request->isMethod('post')) {
			$newPassword = $this->validateNewPasswordForm($request); // validate new password form

			$em = $this->getDoctrine()
				->getEntityManager();

			$user = $em->getRepository('AppBundle:User')
				->findOneById($id);

			if (!$user) {
				throw new \Exception("User Id is wrong!");
			}

			$this->setEncodedPassword($user, $newPassword); //set encoded password

			$em->flush();

			return new RedirectResponse($this->generateUrl('login'));
		}
		
		return $this->render(
			'AppBundle:security:new_password.html.twig', [
				'hash' => $hash
			]
		);
	}

	/**
	 * Validate new password form
	 *
	 * @todo use Form component to create and validate forms
	 * @param Request $request
	 * @return string
	 * @throws \Exception
	 */
	protected function validateNewPasswordForm(Request $request)
	{
		// check Csrf token
		$this->checkCsrfToken($request->get('token'), self::CSRF_AUTH_TOKEN_ID);
		
		$newPassword = $request->get('password');
		$newPasswordConfirm = $request->get('password');
		
		// passwords are not empty
		$isPasswordSet = $newPassword && $newPasswordConfirm;
		if (!$isPasswordSet) {
			throw new \Exception("Password should be set");
		}
		
		// confirm passwords
		$isPasswordConfirmed = $newPassword === $newPasswordConfirm;
		if (!$isPasswordConfirmed) {
			throw new \Exception("Passwords should be same");
		}

		/**
		 * @todo check quality of password
		 */

		return $newPassword;
	}

	/**
	 * check Csrf token
	 * @param string $tokenValue
	 * @param string $tokenId
	 *
	 */
	protected function checkCsrfToken($tokenValue, $tokenId)
	{
		/**
		 * @var \Symfony\Component\Security\Csrf\CsrfTokenManager $tokenManager
		 */
		$tokenManager = $this->get('security.csrf.token_manager');

		$token = new CsrfToken($tokenId, $tokenValue);
		
		if (!$tokenManager->isTokenValid($token)) {
			throw new HttpException(400, 'Invalid token');
		}
	}

	/**
	 * encode password for User object
	 *
	 * @param User $user
	 * @param string $password
	 */
	protected function setEncodedPassword(User &$user, $password)
	{
		/**
		 * @var \Symfony\Component\Security\Core\Encoder\EncoderFactory $encoderFactory
		 */
		$encoderFactory = $this->get('security.encoder_factory');
		$encoder = $encoderFactory->getEncoder($user);

		$salt = md5(time());
		$encodedPassword = $encoder->encodePassword($password, $salt);

		$user->setSalt($salt);
		$user->setPassword($encodedPassword);
	}

	/**
	 * Action to register user
	 *
	 * @todo create a registration form
	 *
	 * @param  Request $request
	 * @return Response
	 * @Route("/register", name="registration")
	 */
	public function registrationAction(Request $request)
	{
		/**
		 * $var User $user
		 */
		$user = new User();

		/**
		 * @var \Symfony\Component\Security\Core\Encoder\EncoderFactory $encoderFactory
		 */
		$encoderFactory = $this->get('security.encoder_factory');
		$encoder = $encoderFactory->getEncoder($user);

		$email = 'nekrasov.dmytro@gmail.com';
		$username = 'test';

		$this->setEncodedPassword($user, 'password');

		$user->setUsername($username);
		$user->setEmail($email);

		$em = $this->getDoctrine()->getManager();
		$em->persist($user);
		$em->flush();

		return new Response('Test user registered');
	}
}
