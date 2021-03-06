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
 * @author Lea McDuffie
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
	$pdo = connectToEncryptedMySQL("path .ini here, Lea");

	$_SESSION["profile"] = Profile::getProfileByProfileId($pdo, 729);

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

		// Gets a profile by content
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

		// Retrieve the profile to be updated
		$profile = Profile::getProfileByProfileId($pdo, $id);
		if($profile === null) {
			throw(new RuntimeException("Profile does not exist", 404));
		}
		if(empty($requestObject->newPassword) === true) {

			// Enforce that the XSRF token is present in the header
			verifyXsrf();

			// Profile username
			if(empty($requestObject->profileUserName) === true) {
				throw(new \InvalidArgumentException ("No profile username", 405));
			}
			// Profile location is a required field
			if(empty($requestObject->profileLocation) === true) {
				throw(new \InvalidArgumentException ("No profile location present", 405));
			}
			$profile->setProfileUserName($requestObject->profileUserName);
			$profile->setProfileLocation($requestObject->profileLocation);
			$profile->setProfileJoinDate($requestObject->profileJoinDate);
			$profile->setProfileHash($requestObject->profileHash);
			$profile->setProfileSalt($requestObject->profileSalt);
			$profile->update($pdo);

			// Update reply
			$reply->message = "Profile information updated";
		}
		/**
		 * Update the password if requested
		 **/

		// Enforce that password is present and confirmed
		if(empty($requestObject->ProfilePassword) === false && empty($requestObject->profileConfirmPassword) === false && empty($requestContent->ConfirmPassword) === false) {

			// Make sure the new password and confirm password exist
			if($requestObject->newProfilePassword !== $requestObject->profileConfirmPassword) {
				throw(new RuntimeException("New passwords do not match", 401));
			}

			// Hash the previous password
			$currentPasswordHash = hash_pbkdf2("sha512", $requestObject->currentProfilePassword, $profile->getProfileSalt(), 262144);

			// Make sure the hash given by the end user matches what is in the database
			if($currentPasswordHash !== $profile->getProfileHash()) {
				throw(new \RuntimeException("Old password is incorrect", 401));
			}

			// Salt and hash the new password and update the profile object
			$newPasswordSalt = bin2hex(random_bytes(16));
			$newPasswordHash = hash_pbkdf2("sha512", $requestObject->newProfilePassword, $newPasswordSalt, 262144);
			$profile->setProfileHash($newPasswordHash);
			$profile->setProfileSalt($newPasswordSalt);
		}

		//Preform the actual update to the database and update the message
		$profile->update($pdo);
		$reply->message = "Profile password successfully updated";
	} else if($method === "DELETE") {

		// Verify the XSRF Token
		verifyXsrf();
		$profile = Profile::getProfileByProfileId($pdo, $id);
		if($profile === null) {
			throw (new RuntimeException("Profile does not exist"));
		}

		// Enforce the user is signed in and only trying to edit their own profile
		if(empty($_SESSION["profile"]) === true || $_SESSION["profile"]->getProfileId() !== $profile->getProfileId()) {
			throw(new \InvalidArgumentException("You are not allowed to access this profile", 403));
		}

		// Delete the profile from the database
		$profile->delete($pdo);
		$reply->message = "Profile Deleted";
	} else {
		throw (new InvalidArgumentException("Invalid HTTP request", 400));
	}

	// Catch any exceptions that were thrown and update the status and message state variable fields
} catch(\Exception | \TypeError $exception ) {
	$reply->status = $exception->getCode();
	$reply->message = $exception->getMessage();
}
header("Content-type: application/json");
if($reply->data === null) {
	unset($reply->data);
}

// Encode and return reply to front end caller
echo json_encode($reply);