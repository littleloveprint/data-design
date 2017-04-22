<?php
namespace Edu\Cnm\DataDesign;
/**
 * Trait to Validate a mySQL Date/
 *
 * This trait will inject a private method to validate a mySQL style date (e.g., 2016-01-15 15:32:48.643216).
 * It will convert a string representation to a DateTime object or throw an exception.
 *
 * @author Lea McDuffie <littleloveprint@gmail.com>
 * @version 1.0
 **/
trait ValidateDate {
	/**
	 * Custom filter for mySQL date.
	 *
	 * Converts a date into a DateTime object; this is designed to be used within a mutator method.
	 *
	 * @param \DateTime|string $newdate date to validate
	 * @return \DateTime DateTime object containing the validate date
	 * @see http://php.net/manual/en/class.datetime.php PHP's DateTime class
	 * @throws \InvalidArgumentException if the date is in an invalid format
	 * @throws \RangeException if the date is not a Gregorian date
	 * @throws \TypeError when type hints fail
	 **/
	private static function ValidateDate($newDate) : \DateTime {
		// Base case: If the date is a DateTime object, there's no work to be done.
		if(is_object($newDate) === true && get_class($newDate) === "DateTime") {
			return ($newDate);
		}
		// Treat the date as a mySQL date string: Y-m-d
		$newDate = trim($newDate);
		if((preg_match("/^(\d{4})-(\d{2})-(\d{2})$/", $newDate, $matches)) !== 1) {
			throw(new \InvalidArgumentException("date is not a valid date"));
		}
		// verify the date is really a valid calendar date
		$year = intval($matches[1]);
		$month = intval($matches[2]);
		$day = intval($matches[3]);
		if(checkdate($month, $day, $year) === false) {
			throw(new \RangeException("date is not a Gregorian date"));
		}
		// if we got here, the date is clean
		$newDate = \DateTime::createFromFormat("Y-m-d H:i:s", $newDate . " 00:00:00");
		return ($newDate);
	}
}