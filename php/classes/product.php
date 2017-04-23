<?php
namespace Edu\Cnm\DataDesign;
require_once("autoload.php");
/**
 * Small Cross Section of an Etsy favorite Product.
 *
 *This product can be considered a small example of what services like Etsy store when messages are sent and received using Etsy.
 * This can easily be extended to emulate more features of Etsy.
 *
 * @author Lea McDuffie <littleloveprint@gmail.com>
 * @version 1
 **/
class Product implements \JsonSerializable {
	use ValidateDate;
	/**
	 * Id for this Product; this is the primary key.
	 * @var int $productId
	 **/
	private $productId;
	/**
	 * Id of the Profile that posted this Product; this is a foreign key.
	 * @var int $productProfileId
	 **/
	private $productProfileId;
	/**
	 * Actual textual content of this Product.
	 * @var string $productDescription
	 **/
	private $productDescription;
	/**
	 * Cost of the product.
	 * @var int $productPrice
	 **/
	private $productPrice;
	/**
	 * Date and time this Product was posted, in a PHP DateTime object.
	 * @var \DateTime $productPostDate
	 **/
	private $productPostDate;
	/**
	 * Constructor for this Product.
	 *
	 * @param int|null $newProductId id of this Product or null if a new Product
	 * @param int $newProductProfileId id of the Profile that posted this Product
	 * @param string $newProductDescription string containing actual Product data
	 * @param float $newProductPrice float containing price of product
	 * @param \DateTime|string|null $newProductPostDate date and time Product was posted or null if set to current date and time
	 * @throws \InvalidArgumentException if data types are not valid
	 * @throws \RangeException if data values are out of bounds (e.g., strings too long, negative integers)
	 * @throws \TypeError if data types violate type hints
	 * @throws \Exception if some other exception occurs
	 * @documentation https://php.net/manual/en/language.oop5.decon.php
	 **/
	public function __construct(?int $newProductId, int $newProductProfileId, string $newProductDescription, float $newProductPrice, $newProductPostDate = null) {
		try {
			$this->setProductId($newProductId);
			$this->setProductProfileId($newProductProfileId);
			$this->setProductDescription($newProductDescription);
			$this->setProductPrice($newProductPrice);
			$this->setProductPostDate($newProductPostDate);
		}
			//determine what exception type was thrown
		catch(\InvalidArgumentException | \RangeException | \Exception | \TypeError $exception) {
			$exceptionType = get_class($exception);
			throw(new $exceptionType($exception->getMessage(), 0, $exception));
		}
	}
	/**
	 * Accessor method for product id.
	 *
	 * @return int|null value of product id
	 **/
	public function getProductId(): ?int {
		return ($this->productId);
	}
	/**
	 * Mutator method for product id.
	 *
	 * @param int|null $newProductId new value of product id
	 * @throws \RangeException if $newProductId is not positive
	 * @throws \TypeError if $newProductId is not an integer
	 **/
	public function setProductId(?int $newProductId): void {
		//If product id is null immediately return it.
		if($newProductId === null) {
			$this->productId = null;
			return;
		}
		// Verify the product id is positive.
		if($newProductId <= 0) {
			throw(new \RangeException("product id is not positive"));
		}
		// Convert and store the product id.
		$this->productId = $newProductId;
	}
	/**
	 * Accessor method for product profile id.
	 *
	 * @return int value of product profile id
	 **/
	public function getProductProfileId(): int {
		return ($this->productProfileId);
	}
	/**
	 * Mutator method for product profile id.
	 *
	 * @param int $newProductProfileId new value of product profile id
	 * @throws \RangeException if $newProfileId is not positive
	 * @throws \TypeError if $newProfileId is not an integer
	 **/
	public function setProductProfileId(int $newProductProfileId): void {
		// Verify the profile id is positive.
		if($newProductProfileId <= 0) {
			throw(new \RangeException("product profile id is not positive"));
		}
		// Convert and store the profile id
		$this->productProfileId = $newProductProfileId;
	}
	/**
	 * Accessor method for product description.
	 *
	 * @return string value of product description
	 **/
	public function getProductDescription(): string {
		return ($this->productDescription);
	}
	/**
	 * Mutator method for product description.
	 *
	 * @param string $newProductDescription new value of product description.
	 * @throws \InvalidArgumentException if $newProductDescription is not a string or insecure
	 * @throws \RangeException if $newProductDescription is > 1000 characters
	 * @throws \TypeError if $newProductDescription is not a string
	 **/
	public function setProductDescription(string $newProductDescription): void {
		// Verify the product description is secure.
		$newProductDescription = trim($newProductDescription);
		$newProductDescription = filter_var($newProductDescription, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
		if(empty($newProductDescription) === true) {
			throw(new \InvalidArgumentException("product content is empty or insecure"));
		}
		// Verify the product description will fit in the database.
		if(strlen($newProductDescription) < 1000) {
			throw(new \RangeException("product description too large"));
		}
		// Store the product description.
		$this->productDescription = $newProductDescription;
	}
	/**
	 * Accessor method for product price.
	 *
	 * @return float value of product price.
	 **/
	public function getProductPrice() : float {
		return($this->productPrice);
	}
	/**
	 * Mutator method for product price.
	 *
	 * @param int|null $newProductPrice new value of product price
	 * @throws \RangeException if $newProductPrice is not positive
	 * @throws \TypeError if $newProductPrice is not an integer
	 **/
	public function setProductPrice(float $newProductPrice): void {
		//If product price is null immediately return it.
		$newProductPrice = filter_var($newProductPrice, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_THOUSAND);

		if($newProductPrice === null) {
			$this->productPrice = null;
			return;
		}
		// Verify the product price is positive.
		if($newProductPrice <= 0) {
			throw(new \RangeException("product price is not positive"));
		}
		// Convert and store the product price.
		$this->productIPrice = $newProductPrice;
		}
		//

	/**
	 * Accessor method for product post date
	 *
	 * @return \DateTime value of product post date
	 **/
	public function getProductPostDate(): \DateTime {
		return ($this->productPostDate);
	}
	/**
	 * Mutator method for product post date.
	 *
	 * @param \DateTime|string|null $newProductPostDate product post date as a DateTime object or string (or null to load the current time)
	 * @throws \InvalidArgumentException if $newProductPostDate is not a valid object or string
	 * @throws \RangeException if $newProductPostDate is a date that does not exist
	 **/
	public function setProductPostDate($newProductPostDate = null): void {
		// Base case: if the date is nul, use the current date and time
		if($newProductPostDate === null) {
			$this->productPostDate = new \DateTime();
			return;
		}
		// Store the product post date using the ValidateDate trait.
		try {
			$newProductPostDate = self::validateDateTime($newProductPostDate);
		} catch(\InvalidArgumentException | \RangeException $exception) {
			$exceptionType = get_class($exception);
			throw(new $exceptionType($exception->getMessage(), 0, $exception));
		}
		$this->productPostDate = $newProductPostDate;
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
		$query = "INSERT INTO product(productProfileId, productDescription, productPrice, productPostDate) VALUES(:productProfileId, :productDescription, :productPrice, :productPostDate)";
		$statement = $pdo->prepare($query);
		// Bind the member variables to the place holders in the template.
		$formattedDate = $this->productPostDate->format("Y-m-d H:i:s");
		$parameters = ["productProfileId" => $this->productProfileId, "productDescription" => $this->productDescription, "productPrice" => $this->productPrice, "productPostDate" => $formattedDate];
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
		$query = "UPDATE product SET productProfileId = :productProfileId, productDescription = :productDescription, productPrice = :productPrice, productPostDate = :productPostDate WHERE productId = :productId";
		$statement = $pdo->prepare($query);
		// Bind the member variables to the place holders in the template.
		$formattedDate = $this->productPostDate->format("Y-m-d H:i:s");
		$parameters = ["productProfileId" => $this->productProfileId, "productDescription" => $this->productId];
		$statement->execute($parameters);
	}
	/**
	 * Gets the product by description.
	 *
	 * @param \PDO $pdo PDO connection object
	 * @param string $productDescription product description to search for
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
		$query = "SELECT productId, productProfileId, productDescription, productPrice, productPostDate FROM product WHERE productDescription LIKE :productDescription";
		$statement = $pdo->prepare($query);
		// Bind the product description to the place holder in the template.
		$productDescription = "%productDescription%";
		$parameters = ["productDescription" => $productDescription];
		$statement->execute($parameters);
		// Build an array of products.
		$products = new \SplFixedArray($statement->rowCount());
		$statement->setFetchMode(\PDO::FETCH_ASSOC);
		while(($row = $statement->fetch()) !== false) {
			try {
				$product = new Product($row["productId"], $row["productProfileId"], $row["productDescription"], $row["productPrice"], $row["productPostDate"]);
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
			$query = "SELECT productId, productProfileId, productDescription, productPrice, productPostDate FROM product WHERE productId = :productId";
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
					$product = new Product($row["productId"], $row["productProfileId"], $row["productDescription"], $row["productPrice"], $row["productPostDate"]);
				}
				$products[$products->key()] = $product;
				$products->next();
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
			$query = "SELECT productId, productProfileId, productDescription, productPrice, productPostDate FROM product WHERE productProfileId = :productProfileId";
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
	 * Gets the Product by product price.
	 *
	 * @param \PDO $pdo PDO connection object
	 * @param int $productPrice to search by product price
	 * @return \SplFixedArray SplFixedArray of Product Prices found
	 * @throws \PDOException when mySQL related errors occur
	 * @throws \TypeError when variables are not the correct data type
	 **/
	public static function getProductByProductPrice(\PDO $pdo, int $productPrice) : \SplFixedArray {
		// Ensure product price is valid.
		if($productPrice <= 0) {
			throw(new \RangeException("product price must be positive"));
		}
		// Create query template
		$query = "SELECT productId, productProfileId, productDescription, productPrice, productDescription FROM product WHERE productPrice = :productPrice";
		$statement = $pdo->prepare($query);
		// Bind the product price to the place holder in the template.
		$parameters = ["productPrice" => $productPrice];
		$statement->execute($parameters);
		// Build an array of product prices.
		$products = new \SplFixedArray($statement->rowCount());
		$statement->setFetchMode(\PDO::FETCH_ASSOC);
		while(($row = $statement->fetch()) !== false) {
			try {
				$product = new Product($row["productId"], $row["productProfileId	"], $row["productContent"], $row["productPrice"], $row["productPostDate"]);
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
		 * Gets all Products
		 *
		 * @param \PDO $pdo PDO connection object
		 * @return \SplFixedArray SplFixedArray of Products found or null if not found
		 * @throws \PDOException when mySQL related errors occur
		 * @throws \TypeError when variables are not the correct data type
		 **/
		public static function getAllProducts(\PDO $pdo) : \SplFixedArray {
			// create query template
			$query = "SELECT productId, productProfileId, productDescription, productPrice, productPostDate FROM product";
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
