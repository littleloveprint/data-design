<?php
require_once(dirname(__DIR__, 3) . "/vendor/autoload.php");
require_once(dirname(__DIR__, 3) . "/php/classes/autoload.php");
require_once(dirname(__DIR__, 3) . "/php/lib/xsrf.php");
require_once("path to encrypted file goes here, Lea!");
use Edu\Cnm\DataDesign\ {
	Profile
};
/**
 * API for Profile
 *
 * @author Lea McDUffie littleloveprint
 * @version 1.0
 */

// Verify the session. If it is not active, start it.
if(session_status() !== PHP_SESSION_ACTIVE) {
	session_start();
}
// Prepare an empty reply
$reply = new stdClass();
$reply->status = 200;
$reply->data = null;
try {

	// Grab the mySQL connection
	$pdo = connectToEncryptedMySQL(".ini file goes here, Lea!");

	// Determine which HTTP method was used
	$method = array_key_exists("HTTP_X_HTTP_METHOD", $_SERVER) ? $_SERVER["HTTP_X_HTTP_METHOD"] : $_SERVER["REQUEST_METHOD"];

	// Sanitize input
	$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);
	$profileUserName = filter_input(INPUT_GET, "profileUserName", FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
	$profileLocation = filter_input(INPUT_GET, "profileLocation", FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES); $profileJoinDate = filter_input(INPUT_GET, "profileJoinDate");

	// Make sure the id is valid for methods that require it
	if(($method === "DELETE" || $method === "PUT") && (empty($id) === true || $id < 0)) {
		throw(new InvalidArgumentException("id cannot be empty or negative", 405));
	}
	if($method === "GET") {

		// Set XSRF cookie
		setXsrfCookie();

		// Gets a post by content
		if(empty($id) === false) {
			$profile = Profile::getProfileByProfileId($pdo, $id);
			if($profile !== null) {
				$reply->data = $profile;
			}
		} else if(empty($profileUserName) === false) {
			$profile = Profile::getProfileByProfileUserName($pdo, $profileUserName);
			if($profile !== null) {
				$reply->data = $profile;
			}
		} else if(empty($profileLocation) === false) {
			$profile = Profile::getProfileByProfileLocation($pdo, $profileLocation);
			if($profile !== null) {
				$reply->data = $profile;
			}
		}
	} else if($method === "PUT") {

		// Enforce the user is signed in and only trying to edit their own profile
		if(empty($_SESSION["profile"]) === true || $_SESSION["profile"]->getProfileId() !== $id) {
			throw(new \InvalidArgumentException("You are not allowed to access this profile", 403));
		}
		// Decode the response from the front end
		$requestContent = file_get_contents("php://input");
		$requestObject = json_decode($requestContent);
