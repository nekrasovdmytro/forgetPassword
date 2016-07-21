<?php
/**
 * Created by PhpStorm.
 * User: nekrasov
 * Date: 20.07.16
 * Time: 19:14
 */

namespace tests\AppBundle\TempLinkGenerator;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use AppBundle\TempLinkGenerator\RestoreUserPasswordLinkGenerator;

class RestoreUserPasswordLinkGeneratorTest extends KernelTestCase
{
	/**
	 * @var RestoreUserPasswordLinkGenerator
	 */
	private $generator;
	private $user;
	private $salt = '36ae93b9e66c23aa17adeaa1c6fe9a5d';
	private $expectedHash = '695cbd87bbd1646254a435f0bc2954b0';

	public function setUp()
	{
		self::bootKernel();

		$this->user = $this->getMockBuilder('\AppBundle\Entity\User')
			->setMethods(['getId','getSalt'])
			->getMock();

		$fixedTimestamp = 1469081439;

		$this->generator = new RestoreUserPasswordLinkGenerator($fixedTimestamp);
		$this->generator->setContainer(self::$kernel->getContainer());
		$this->generator->setUser($this->user);

	}

	/**
	 * @param string $name
	 * @return \ReflectionMethod
	 */
	protected static function getPrivateMethod($name)
	{
		$class = new \ReflectionClass('\AppBundle\TempLinkGenerator\RestoreUserPasswordLinkGenerator');
		$method = $class->getMethod($name);
		$method->setAccessible(true);

		return $method;
	}

	private function setUserMockExpects()
	{
		$this->user->expects($this->once())
			->method('getSalt')
			->willReturn($this->salt);
	}

	public function testInstanceOfAbstractTempLink()
	{
		$this->assertInstanceOf(RestoreUserPasswordLinkGenerator::class, $this->generator);
	}

	public function testGenerateHash()
	{
		$this->setUserMockExpects();

		$foo = self::getPrivateMethod('generateTempHash');
		$hash = $foo->invoke($this->generator);

		$this->assertSame($this->expectedHash, $hash);
	}

	public function testCheckHash()
	{
		$this->setUserMockExpects();

		$result = $this->generator->checkHash($this->expectedHash);

		$this->assertTrue($result);
	}

	public function testGenerateUrlWithHash()
	{
		$this->setUserMockExpects();

		$result = $this->generator->generateLink();

		$this->assertContains($this->expectedHash, $result->link);
	}                             
}
