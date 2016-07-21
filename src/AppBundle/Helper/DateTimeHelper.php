<?php
/**
 * Created by PhpStorm.
 * User: nekrasov
 * Date: 20.07.16
 * Time: 21:27
 */

namespace AppBundle\Helper;


class DateTimeHelper
{
	/**
	 * @param \DateTime $date1
	 * @param \DateTime $date2
	 * @return int
	 */
	public static function getDiffInHours(\DateTime $date1, \DateTime $date2)
	{
		$diff = $date2->diff($date1);
		
		return $diff->h + $diff->days * 24;
	}
}
