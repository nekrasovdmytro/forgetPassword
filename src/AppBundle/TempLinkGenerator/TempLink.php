<?php

namespace AppBundle\TempLinkGenerator;

/**
 * Class TempLink                      
 * provides a class with information about temp link
 * @package AppBundle\TempLinkGenerator
 */
class TempLink
{
	/**
	 * @var int, timestamp of generated link
	 */
	public $time;

	/**
	 * @var string, hash of generated link
	 */
	public $hash;

	/**
	 * @var string, temp link
	 */
	public $link;

	/**
	 * TempLink constructor. 
	 * 
	 * @constructor
	 * @param $time
	 * @param $hash
	 * @param $link
	 */
	public function __construct($time, $hash, $link)
	{
		$this->time = $time;
		$this->hash = $hash;
		$this->link = $link;
	}
}
