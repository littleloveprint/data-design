<?php
namespace Edu\Cnm\DataDesign;
require_once("autoload.php");
/**
 * Small Cross Section of an Etsy Profile.
 *
 *This is an example of how the Profile is built.
 *
 * @author Lea McDuffie <littleloveprint@gmail.com>
 * @version 1
 **/
class Profile implements \JsonSerializable {
	use ValidateDate;
	/**
	 * Id for this Profile; this is the primary key.
	 * @var int $profileId
	 **/
	private $profileId;
	/**
	 * Username for this profile.
	 * @var string $profileUserName
	 **/
	private $profileUserName;
	/**
	 * Location of this Profile.
	 * @var string $profileLocation
	 **/
	private $profileLocation;
	/**
	 * Date and time user created their profile, in a PHP DateTime object.
	 * @var int $profileJoinDate
	 **/
	private $profileJoinDate;
	/**
	 * Jumbles users' passwords.
	 * @var int $profileHash
	 **/
	private $profileHash;
	/**
	 * Adds extra security to users' passwords.
	 * @var int $profileSalt
	 **/
	private $profileSalt;
	/**
	 * Constructor for this Profile.
	 *
	 * @param int $newProfileId id of the Profile
	 * @param string $newProfileUserName string containing actual Profile user name
	 * @param string $newProfileLocation string containing user's declared location
	 * @param \DateTime|string|null $newProfileDate date and time Profile was created or null if set to current date and time
	 * @throws \InvalidArgumentException if data types are not valid
	 * @throws \RangeException if data values are out of bounds (e.g., strings too long, negative integers)
	 * @throws \TypeError if data types violate type hints
	 * @throws \Exception if some other exception occurs
	 * @documentation https://php.net/manual/en/language.oop5.decon.php
	 **/
	public function __construct(?int $newProfileId, string $newProfileUserName, $newProfileLocation, $newProfileJoinDate, $newProfileHash, $newProfileSalt = null) {
		try {
			$this->setProfileId($newProfileId);
			$this->setProfileUserName($newProfileUserName);
			$this->setProfileLocation($newProfileLocation);
			$this->setProfileJoinDate($newProfileJoinDate);
			$this->setProfileHash($newProfileHash);
			$this->setProfileSalt($newProfileSalt);
		}
			//determine what exception type was thrown
		catch(\InvalidArgumentException | \RangeException | \Exception | \TypeError $exception) {
			$exceptionType = get_class($exception);
			throw(new $exceptionType($exception->getMessage(), 0, $exception));
		}
	}
	/**
	 * Accessor method for profile id.
	 *
	 * @return int value of profile id
	 **/
	public function getProfileId(): ?int {
		return ($this->profileId);
	}
	/**
	 * Mutator method for profile id.
	 *
	 * @param int $newProfileId new value of profile id
	 * @throws \RangeException if $newProfileId is not positive
	 * @throws \TypeError if $newProfileId is not an integer
	 **/
	public function setProfileId(?int $newProfileId): void {
		//If profile id is null immediately return it.
		if($newProfileId === null) {
			$this->profileId = null;
			return;
		}
		// Verify the profile id is positive.
		if($newProfileId <= 0) {
			throw(new \RangeException("profile id is not positive"));
		}
		// Convert and store the profile id.
		$this->profileId = $newProfileId;
	}
	/**
	 * Accessor method for profile username.
	 *
	 * @return int value of profile username
	 **/
	public function getProfileUserName(): string {
		return ($this->profileUserName);
	}
	/**
	 * Mutator method for profile username.
	 *
	 * @param string $newProfileUserName new value of profile username
	 * @throws \RangeException if $newProfileUserName > 32 characters
	 * @throws \TypeError if $newProfileUserName is an integer
	 **/
	public function setProfileUserName(string $newProfileUserName): void {
		// Verify the profile username is secure.
		$newProfileUserName = trim($newProfileUserName);
		$newProfileUserName = filter_var($newProfileUserName, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
		if(empty($newProfileUserName) === true) {
			throw(new \InvalidArgumentException("profile username is empty or insecure"));
		}
		// Verify the profile username will fit in the database.
		if(strlen($newProfileUserName) < 32) {
			throw(new \RangeException("profile username too large"));
		}
		// Store the profile username.
		$this->profileUserName = $newProfileUserName;
	}
	/**
	 * Accessor method for profile location.
	 *
	 * @return string value of profile location
	 **/
	public function getProfileLocation(): string {
		return ($this->profileLocation);
	}
	/**
	 * Mutator method for profile location.
	 *
	 * @param string $newProfileLocation new value of profile location.
	 * @throws \InvalidArgumentException if $newProfileLocation is not a string or insecure
	 * @throws \RangeException if $newProfileLocation is > 32 characters
	 * @throws \TypeError if $newProfileLocation is not a string
	 **/
	public function setProfileLocation(string $newProfileLocation): void {
		// Verify the profile location is secure.
		$newProfileLocation = trim($newProfileLocation);
		$newProfileLocation = filter_var($newProfileLocation, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
		if(empty($newProfileLocation) === true) {
			throw(new \InvalidArgumentException("profile location is empty or insecure"));
		}
		// Verify the profile location will fit in the database.
		if(strlen($newProfileLocation) > 64) {
			throw(new \RangeException("profile location too large"));
		}
		// Store the profile location.
		$this->profileLocation = $newProfileLocation;
	}
	/**
	 * Accessor method for profile join date
	 *
	 * @return \DateTime value of profile join date
	 **/
	public function getProfileJoinDate(): \DateTime {
		return ($this->profileJoinDate);
	}
	/**
	 * Mutator method for profile join date.
	 *
	 * @param \DateTime|string|null $newProfileJoinDate profile join date as a DateTime object or string (or null to load the current time)
	 * @throws \InvalidArgumentException if $newProfileJoinDate is not a valid object or string
	 * @throws \RangeException if $newProfileJoinDate is a date that does not exist
	 **/
	public function setProfileJoinDate($newProfileJoinDate = null): void {
		// Base case: if the date is null, use the current date and time
		if($newProfileJoinDate === null) {
			$this->profileJoinDate = new \DateTime();
			return;
		}
		// Store the profile join date using the ValidateDate trait.
		try {
			$newProfileJoinDate = self::validateDateTime($newProfileJoinDate);
		} catch(\InvalidArgumentException | \RangeException $exception) {
			$exceptionType = get_class($exception);
			throw(new $exceptionType($exception->getMessage(), 0, $exception));
		}
		$this->profileJoinDate = $newProfileJoinDate;
	}
	/**
	 * Accessor method for profile hash.
	 *
	 * @return int value of profile hash
	 **/
	private static function getProfileHash(): ?int {
		return;
	}
	/**
	 * Mutator method for profile hash.
	 *
	 * @param int $newProfileHash new value of profile hash
	 * @throws \RangeException if $newProfileHash is not positive
	 * @throws \TypeError if $newProfileHash is not an integer
	 **/
	private function setProfileHash(?int $newProfileId): void {
		//If profile id is null immediately return it.
		if($newProfileId === null) {
			$this->profileId = null;
			return;
		}
		// Verify the profile id is positive.
		if($newProfileId <= 0) {
			throw(new \RangeException("profile id is not positive"));
		}
		// Convert and store the profile id.
		$this->profileId = $newProfileId;
	}
	/**
	 * Inserts this product into mySQL.
	 *
	 * @param \PDO $pdo PDO connection object
	 * @throws \PDOException when mySQL related errors occur
	 * @throws \TypeError is $pdo is not a PDO connection object
	 **/
	public function insert(\PDO $pdo): void {
		// Enforce the productId is null (i.e., don't insert a product that already exists).
		if($this->productId !== null) {
			throw(new \PDOException("product already exists"));
		}
		// Create query template
		$query = "INSERT INTO product(productProfileId, productDescription, productPostDate) VALUES(:productProfileId, :productDescription, :productPostDate)";
		$statement = $pdo->prepare($query);
		// Bind the member variables to the place holders in the template.
		$formattedDate = $this->productPostDate->format("Y-m-d H:i:s");
		$parameters = ["productProfileId" => $this->productProfileId, "productDescription" => $this->productDescription, "productPostDate" => $formattedDate];
		$statement->execute($parameters);
		// Update the null productId with what mySQL just gave us.
		$this->productId = intval($pdo->lastInsertId());
	}
	/**
	 * Deletes this product from mySQL
	 *
	 * @param \PDO $pdo PDO connection object
	 * @throws \PDOException when mySQL related errors occur
	 * @throws \TypeError if $pdo is not a PDO connection object
	 **/
	public function delete(\PDO $pdo) : void {
		// Enforce the productId is not null (i.e. don't delete a product that hasn't been inserted).
		if($this->productId === null) {
			throw(new \PDOException("unable to delete a product that doesn't exist"));
		}
		// Create query template.
		$query = "DELETE FROM product WHERE productId = :productId";
		$statement = $pdo->prepare($query);
		// Bind the member variables to the place holder in the template.
		$parameters = ["productId" => $this->productId];
		$statement->execute($parameters);
	}
	/**
	 * Updates this product in mySQL.
	 *
	 * @param \PDO $pdo PDO connection object
	 * @throws \PDOException when mySQL related errors occur
	 * @throws \TypeError if $pdo is not a PDO connection object
	 **/
	public function update(\PDO $pdo) : void {
		// Enforce the productId is not null (i.e. don't update a product that hasn't been inserted).
		if($this->productId === null) {
			throw(new \PDOException("unable to update a product that does not exist"));
		}
		// Create query template.
		$query = "UPDATE product SET productProfileId = :productProfileId, productDescription = :productDescription, productPostDate = :productPostDate WHERE productId = :productId";
		$statement = $pdo->prepare($query);
		// Bind the member variables to the place holders in the template.
		$formattedDate = $this->productPostDate->format("Y-m-d H:i:s");
		$parameters = ["productProfileId" => $this->productProfileId, "productDescription" => $this->productId];
		$statement->execute($parameters);
	}
	/**
	 * Gets the product by content.
	 *
	 * @param \PDO $pdo PDO connection object
	 * @param string $productDescription product content to search for
	 * @return \SplFixedArray SplFixedArray of products found
	 * @throws \PDOException when mySQL related errors occur
	 * @throws \TypeError when variables are not the correct data type
	 **/
	public static function getProductByProductDescription(\PDO $pdo, string $productDescription) {
		// Sanatize the description before searching
		$productDescription = trim($productDescription);
		$productDescription = filter_var($productDescription, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
		if(empty($productDescription) === true) {
			throw(new \PDOException("product description invalid"));
		}
		// Create query template.
		$query = "SELECT productId, productProfileId, productDescription, productPostDate FROM product WHERE productDescription LIKE :productDescription";
		$statement = $pdo->prepare($query);
		// Bind the product description to the place holder in the template.
		$productDescription = "%productDescription%";
		$parameters = ["productDescription" => $profileLocation];
		$statement->execute($parameters);
		// Build an array of profile locations.
		$products = new \SplFixedArray($statement->rowCount());
		$statement->setFetchMode(\PDO::FETCH_ASSOC);
		while(($row = $statement->fetch()) !== false) {
			try {
				$product = new Product($row["productId"], $row["productPostDate"]);
				$products[$products->key()] = $product;
				$products->next();
			} catch(\Exception $exception) {
				// If the row couldn't be converted, rethrow it.
				throw(new \PDOException($exception->getMessage(), 0, $exception));
			}
		}
		return($products);
	}
	/**
	 * Gets the Product by productId.
	 *
	 * @param \PDO $pdo PDO connection object
	 * @param int $productId product id to search for
	 * @return Product|null Product found or null if not found
	 * @throws \PDOException when mySQL errors occur
	 * @throws \TypeError when variables are not the correct data type
	 **/
	public static function getProductByProductId(\PDO $pdo, int $productId) : ?Product {
		// Sanitize the productId before searching.
		if($productId <= 0) {
			throw(new \PDOException("product id is not positive"));
		}
		// Create query template.
		$query = "SELECT productId, productProfileId, productDescription, productPostDate FROM product WHERE productId = :productId";
		$statement = $pdo->prepare($query);
		// Bind the product id to the place holder in the template.
		$parameters = ["productId" => $productId];
		$statement->execute($parameters);
		// Grab the product from mySQL
		try {
			$product = null;
			$statement->setFetchMode(\PDO::FETCH_ASSOC);
			$row = $statement->fetch();
			if($row !== false) {
				$product = new Product($row["productId"], $row["productProfileId"], $row["productDescription"], $row["productPostDate"]);
			}
		} catch(\Exception $exception) {
			// If the row couldn't be converted, rethrow it.
			throw(new \PDOException($exception->getMessage(), 0, $exception));
		}
		return ($product);
	}
	/**
	 * Gets the Product by profile id
	 *
	 * @param \PDO $pdo PDO connection object
	 * @param int $productProfileId profile id to search by
	 * @return \SplFixedArray SplFixedArray of Products found
	 * @throws \PDOException when mySQL related errors occur
	 * @throws \TypeError when variables are not the correct data type
	 **/
	public static function getProductByProductProfileId(\PDO $pdo, int $productProfileId) : \SplFixedArray {
		// Sanitize the profile id before searching
		if($productProfileId <= 0) {
			throw(new \RangeException("product profile id must be positive"));
		}
		// Create query template
		$query = "SELECT productId, productProfileId, productContent, productPostDate FROM product WHERE productProfileId = :productProfileId";
		$statement = $pdo->prepare($query);
		// Bind the product profile id to the place holder in the template.
		$parameters = ["productProfileId" => $productProfileId];
		$statement->execute($parameters);
		// Build an array of products.
		$products = new \SplFixedArray($statement->rowCount());
		$statement->setFetchMode(\PDO::FETCH_ASSOC);
		while(($row = $statement->fetch()) !== false) {
			try {
				$product = new Product($row["productId"], $row["productProfileId	"], $row["productContent"], $row["productPostDate"]);
				$products[$products->key()] = $product;
				$products->next();
			} catch(\Exception $exception) {
				// If the row couldn't be converted, rethrow it.
				throw (new \PDOException($exception->getMessage(), 0, $exception));
			}
		}
		return($products);
	}
	/**
	 * gets all Products
	 *
	 * @param \PDO $pdo PDO connection object
	 * @return \SplFixedArray SplFixedArray of Products found or null if not found
	 * @throws \PDOException when mySQL related errors occur
	 * @throws \TypeError when variables are not the correct data type
	 **/
	public static function getAllProducts(\PDO $pdo) : \SplFixedArray {
		// create query template
		$query = "SELECT productId, productProfileId, productContent, productPostDate FROM product";
		$statement = $pdo->prepare($query);
		$statement->execute();
		// build an array of tweets
		$products = new \SplFixedArray($statement->rowCount());
		$statement->setFetchMode(\PDO::FETCH_ASSOC);
		while(($row = $statement->fetch()) !== false) {
			try {
				$product = new Product($row["productId"], $row["productProfileId"], $row["productContent"], $row["productPostDate"]);
				$products[$products->key()] = $product;
				$products->next();
			} catch(\Exception $exception) {
				// If the row couldn't be converted, rethrow it.
				throw(new \PDOException($exception->getMessage(), 0, $exception));
			}
		}
		return ($products);
	}
	/**
	 * Formats the state variables for JSON serialization.
	 *
	 * @return array resulting state variables to serialize
	 **/
	public function jsonSerialize() {
		$fields = get_object_vars($this);
		//Format the date so that the front end can consume it.
		$fields["productPostDate"] = round(floatval($this->productPostDate->format("U.u")) * 1000);
		return($fields);
	}
}
