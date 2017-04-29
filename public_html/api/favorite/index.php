<?php
require_once dirname(__DIR__, 3) . "/vendor/autoload.php";
require_once dirname(__DIR__, 3) . "/php/classes/autoload.php";
require_once dirname(__DIR__, 3) . "/php/lib/xsrf.php";
require_once("Encrypted file .php");
use Edu\Cnm\DataDesign\{
	Profile,
	Favorite
};

/**
 * API for Favorite class
 *
 * @author Lea McDuffie
 * @version 1.0
 */

// Verify the session, start if not active
if(session_status() !== PHP_SESSION_ACTIVE) {
	session_start();
}
// Prepare an empty reply
$reply = new stdClass();
$reply->status = 200;
$reply->data = null;
try {
	$pdo = connectToEncryptedMySQL(".ini file");

	// Mock a logged in user by mocking the session and assigning a specific user to it.
	// This is only for testing purposes and should not be in the live code.
	$_SESSION["profile"] = Profile::getProfileByProfileId($pdo, 732);

	// Determine which HTTP method was used
	$method = array_key_exists("HTTP_X_HTTP_METHOD", $_SERVER) ? $_SERVER["HTTP_X_HTTP_METHOD"] : $_SERVER["REQUEST_METHOD"];
	var_dump($method);

	// Sanitize the search parameters
	$favoriteProfileId = filter_input(INPUT_GET, "FavoriteProfileId", FILTER_VALIDATE_INT);
	$FavoriteProductId = filter_input(INPUT_GET, "favoriteProductId", FILTER_VALIDATE_INT);
	var_dump($favoriteProfileId);
	var_dump($favoriteProductId);
	if($method === "GET") {

		// Set XSRF cookie
		setXsrfCookie();

		// Gets a specific favorite associated based on its composite key
		if ($favoriteProfileId !== null && $favoriteProductId !== null) {
			$favorite = Favorite::getFavoriteByFavoriteProductIdAndProfileId($pdo, $favoriteProfileId, $favoriteProductId);
			if($favorite!== null) {
				$reply->data = $favorite;
			}

			// If none of the search parameters are met throw an exception
		} else if(empty($favoriteProfileId) === false) {
			$favorite = Favorite::getFavoriteByFavoriteProfileId($pdo, $favoriteProfileId)->toArray();
			if($favorite !== null) {
				$reply->data = $favorite;
			}

			// Get all the favorites associated with the productId
		} else if(empty($favoriteProductId) === false) {
			$favorite = Favorite::getFavoriteByFavoriteProductId($pdo, $favoriteProductId)->toArray();
			if($favorite !== null) {
				$reply->data = $favorite;
			}
		} else {
			throw new InvalidArgumentException("incorrect search parameters ", 404);
		}
	} else if($method === "POST" || $method === "PUT") {

		// Decode the response from the front end
		$requestContent = file_get_contents("php://input");
		$requestObject = json_decode($requestContent);
		if(empty($requestObject->favoriteProfileId) === true) {
			throw (new \InvalidArgumentException("No Profile linked to the Favorite", 405));
		}
		if(empty($requestObject->favoriteProductId) === true) {
			throw (new \InvalidArgumentException("No Product linked to the Favorite", 405));
		}
		if(empty($requestObject->favoriteDate) === true) {
			$requestObject->favoriteDate = null;
		}
		if($method === "POST") {

			// Enforce the user is signed in
			if(empty($_SESSION["profile"]) === true) {
				throw(new \InvalidArgumentException("you must be logged in too favorite products", 403));
			}
			$favorite = new Favorite($requestObject->favoriteProfileId, $requestObject->favoriteProductId, $requestObject->favoriteDate);
			$favorite->insert($pdo);
			$reply->message = "Successfully marked product a favorite";
		} else if($method === "PUT") {

			// Enforce that the end user has a XSRF token.
			verifyXsrf();

			// Grab the favorite by its composite key
			$favorite = Favorite::getFavoriteByFavoriteProductIdAndProfileId($pdo, $requestObject->favoriteProfileId, $requestObject->favoriteProductId);
			if($favorite === null) {
				throw (new RuntimeException("Favorite does not exist"));
			}

			// Enforce the user is signed in and only trying to edit their own favorite
			if(empty($_SESSION["profile"]) === true || $_SESSION["profile"]->getProfileId() !== $favorite->getFavoriteProfileId()) {
				throw(new \InvalidArgumentException("You are not allowed to delete this favorite", 403));
			}

			// Preform the actual delete
			$favorite->delete($pdo);

			// Update the message
			$reply->message = "Favorite successfully deleted";
		}

		// If any other HTTP request is sent, throw an exception
	} else {
		throw new \InvalidArgumentException("invalid http request", 400);
	}

	// Catch any exceptions that is thrown and update the reply status and message
} catch(\Exception | \TypeError $exception) {
	$reply->status = $exception->getCode();
	$reply->message = $exception->getMessage();
}
header("Content-type: application/json");
if($reply->data === null) {
	unset($reply->data);
}

// Encode and return reply to front end caller
echo json_encode($reply);