<?php
require_once dirname(__DIR__, 3) . "/vendor/autoload.php";
require_once dirname(__DIR__, 3) . "/php/classes/autoload.php";
require_once dirname(__DIR__, 3) . "/php/lib/xsrf.php";
require_once("Encrypted file, Lea");
use Edu\Cnm\DataDesign\{
	Product,

	// We use the profile class for testing

	Profile
};
	/**
	 * API for Product class
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

	// Grab the mySQL connection
	$pdo = connectToEncryptedMySQL(".ini file");

	// Mock a logged in user by mocking the session and assigning a specific user to it.
	// This is only for testing purposes and should not be in the live code.
	$_SESSION["profile"] = Profile::getProfileByProfileId($pdo, 732);

	// Determine which HTTP method was used
	$method = array_key_exists("HTTP_X_HTTP_METHOD", $_SERVER) ? $_SERVER["HTTP_X_HTTP_METHOD"] : $_SERVER["REQUEST_METHOD"];

	// Sanitize input
	$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);
	$productProfileId = filter_input(INPUT_GET, "productProfileId", FILTER_VALIDATE_INT);
	$productDescription = filter_input(INPUT_GET, "productDescription", FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);

	// Make sure the id is valid for methods that require it
	if(($method === "DELETE" || $method === "PUT") && (empty($id) === true || $id < 0)) {
		throw(new InvalidArgumentException("id cannot be empty or negative", 405));
	}

	// Handle GET request - if id is present, that tweet is returned, otherwise all products are returned
	if($method === "GET") {

		// Set XSRF cookie
		setXsrfCookie();

		// Get a specific product or all products and update reply
		if(empty($id) === false) {
			$product = Product::getProductByProductId($pdo, $id);
			if($product !== null) {
				$reply->data = $product;
			}
		} else if(empty($productProfileId) === false) {
			$product = Product::getProductByProductId($pdo, $productProfileId)->toArray();
			if($product !== null) {
				$reply->data = $product;
			}
		} else if(empty($productDescription) === false) {
			$products = Product::getProductByProductDescription($pdo, $productDescription)->toArray();
			if($products !== null) {
				$reply->data = $products;
			}
		} else {
			$products = Product::getAllProducts($pdo)->toArray();
			if($products !== null) {
				$reply->data = $products;
			}
		}
	} else if($method === "PUT" || $method === "POST") {
		verifyXsrf();
		$requestContent = file_get_contents("php://input");

		// Retrieves the JSON package that the front end sent, and stores it in $requestContent. Here we are using file_get_contents("php://input") to get the request from the front end. file_get_contents() is a PHP function that reads a file into a string. The argument for the function, here, is "php://input". This is a read only stream that allows raw data to be read from the front end request which is, in this case, a JSON package.
		$requestObject = json_decode($requestContent);

		// This Line Then decodes the JSON package and stores that result in $requestObject
		// Make sure product description is available (required field)
		if(empty($requestObject->productDescription) === true) {
			throw(new \InvalidArgumentException ("No product description.", 405));
		}
		// Make sure product post date is accurate (optional field)
		if(empty($requestObject->productPostDate) === true) {
			$requestObject->productPostDate = null;
		}
		//  Make sure profileId is available
		if(empty($requestObject->productProfileId) === true) {
			throw(new \InvalidArgumentException ("No Profile ID.", 405));
		}

		// Perform the actual put or post
		if($method === "PUT") {

			// Enforce that the end user has a XSRF token.
			verifyXsrf();

			// Retrieve the product to update
			$product = Product::getProductByProductId($pdo, $id);
			if($product === null) {
				throw(new RuntimeException("Product does not exist", 404));
			}

			// Enforce the user is signed in and only trying to edit their own product
			if(empty($_SESSION["profile"]) === true || $_SESSION["profile"]->getProfileId() !== $product->getProductId()) {
				throw(new \InvalidArgumentException("You are not allowed to edit this product", 403));
			}

			// Update all attributes
			$product->setProductPostDate($requestObject->productPostDate);
			$product->setProductDescription($requestObject->productDescription);
			$product->update($pdo);

			// Update reply
			$reply->message = "Product updated OK";
		} else if($method === "POST") {

			// Enforce that the end user has a XSRF token.
			verifyXsrf();

			// Enforce the user is signed in
			if(empty($_SESSION["profile"]) === true) {
				throw(new \InvalidArgumentException("you must be logged in to post products", 403));
			}

			// Create new product and insert into the database
			$product = new Product(null, $requestObject->productProfileId, $requestObject->productDescription, null);
			$product->insert($pdo);

			// Update reply
			$reply->message = "Product created OK";
		}
	} else if($method === "DELETE") {

		// Enforce that the end user has a XSRF token.
		verifyXsrf();

		// Retrieve the Product to be deleted
		$product = Product::getProductByProductId($pdo, $id);
		if($product === null) {
			throw(new RuntimeException("Product does not exist", 404));
		}

		// Enforce the user is signed in and only trying to edit their own product
		if(empty($_SESSION["profile"]) === true || $_SESSION["profile"]->getProfileId() !== $product->getProductProfileId()) {
			throw(new \InvalidArgumentException("You are not allowed to delete this product", 403));
		}

		// Delete product
		$product->delete($pdo);

		// Update reply
		$reply->message = "Product deleted OK";
	} else {
		throw (new InvalidArgumentException("Invalid HTTP method request"));
	}

// Update the $reply->status $reply->message
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