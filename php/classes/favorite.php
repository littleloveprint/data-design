<?php
namespace Edu\Cnm\DataDesign;
require_once("autoload.php");
/**
 * Favoriting a product.
 *
 *This is an example of how I would create a favorite button on a social or ecommerce site.
 *
 * @author Lea McDuffie <littleloveprint@gmail.com>
 * @version 1
 **/
class Favorite implements \JsonSerializable {
	use ValidateDate;
	/**
	 * Id for this Favorite; this is the primary key.
	 * @var int $favoriteProfileId
	 **/
	private $favoriteProfileId;
	/**
	 * Id of the Profile that posted this Product; this is a foreign key.
	 * @var int $favoriteProductId
	 **/
	private $favoriteProductId;
	/**
	 * Actual textual content of this Product.
	 * @var string $favoriteDate
	 **/
	private $favoriteDate;
	/**
	 * Constructor for Favorite.
	 *
	 * @param int|null $newFavoriteId id of this Favorite or null if a new Favorite
	 * @param int $newFavoriteProfileId id of the Profile that favorited this product.
	 * @param \DateTime|string|null $newFavoriteDate date and time Product was favorited or null if set to current date and time
	 * @throws \InvalidArgumentException if data types are not valid
	 * @throws \RangeException if data values are out of bounds (e.g., strings too long, negative integers)
	 * @throws \TypeError if data types violate type hints
	 * @throws \Exception if some other exception occurs
	 * @documentation https://php.net/manual/en/language.oop5.decon.php
	 **/
	public function __construct(?int $newFavoriteProfileId, int $newFavoriteProductId, \DateTime $newFavoriteDate = null) {
		try {
			$this->setFavoriteProfileId($newFavoriteProfileId);
			$this->setFavoriteProductId($newFavoriteProductId);
			$this->setFavoriteDate($newFavoriteDate);
		}
			// Determine what exception type was thrown.
		catch(\InvalidArgumentException | \RangeException | \Exception | \TypeError $exception) {
			$exceptionType = get_class($exception);
			throw(new $exceptionType($exception->getMessage(), 0, $exception));
		}
	}
	/**
	 * Accessor method for favorite profile id.
	 *
	 * @return int|null value of favorite profile id
	 **/
	public function getFavoriteProfileId(): ?int {
		return ($this->favoriteProfileId);
	}
	/**
	 * Mutator method for favorite profile id.
	 *
	 * @param int|null $newFavoriteProfileId new value of favorite profile id
	 * @throws \RangeException if $newFavoriteProfileId is not positive
	 * @throws \TypeError if $newFavoriteProfileId is not an integer
	 **/
	public function setFavoriteProfileId(?int $newFavoriteProfileId): void {
		//If favorite profile id is null immediately return it.
		if($newFavoriteProfileId === null) {
			$this->favoriteProfileId = null;
			return;
		}
		// Verify the favorite profile id is positive.
		if($newFavoriteProfileId <= 0) {
			throw(new \RangeException("favorite profile id is not positive"));
		}
		// Convert and store the favorite profile id.
		$this->favoriteProfileId = $newFavoriteProfileId;
	}
	/**
	 * Accessor method for favorite product id.
	 *
	 * @return int value of favorite product id
	 **/
	public function getFavoriteProductId(): int {
		return ($this->favoriteProductId);
	}
	/**
	 * Mutator method for favorite product id.
	 *
	 * @param int $newFavoriteProductId new value of favorite product id
	 * @throws \RangeException if $newFavoriteProductId is not positive
	 * @throws \TypeError if $newFavoriteProductId is not an integer
	 **/
	public function setFavoriteProductId(int $newFavoriteProductId): void {
		// Verify the profile id is positive.
		if($newFavoriteProductId <= 0) {
			throw(new \RangeException("favorite product id is not positive"));
		}
		// Convert and store the favorite product id
		$this->favoriteProductId = $newFavoriteProductId;
	}
	/**
	 * Accessor method for favorite date
	 *
	 * @return \DateTime value of favorite date
	 **/
	public function getFavoriteDate(): \DateTime {
		return ($this->favoriteDate);
	}
	/**
	 * Mutator method for favorite date.
	 *
	 * @param \DateTime|string|null $newFavoriteDate favorite date as a DateTime object or string (or null to load the current time)
	 * @throws \InvalidArgumentException if $newFavoriteDate is not a valid object or string
	 * @throws \RangeException if $newFavoriteDate is a date that does not exist
	 **/
	public function setFavoriteDate($newFavoriteDate = null): void {
		// Base case: if the date is null, use the current date and time
		if($newFavoriteDate === null) {
			$this->favoriteDate = new \DateTime();
			return;
		}
		// Store the favorite date using the ValidateDate trait.
		try {
			$newFavoriteDate = self::validateDateTime($newFavoriteDate);
		} catch(\InvalidArgumentException | \RangeException $exception) {
			$exceptionType = get_class($exception);
			throw(new $exceptionType($exception->getMessage(), 0, $exception));
		}
		$this->favoriteDate = $newFavoriteDate;
	}
	/**
	 * Inserts this favorite into mySQL.
	 *
	 * @param \PDO $pdo PDO connection object
	 * @throws \PDOException when mySQL related errors occur
	 * @throws \TypeError is $pdo is not a PDO connection object
	 **/
	public function insert(\PDO $pdo): void {
		// Enforce the favoriteProfileId is null (i.e., don't insert a product that already exists).
		if($this->favoriteProfileId !== null) {
			throw(new \PDOException("favorite already exists"));
		}
		// Create query template
		$query = "INSERT INTO favorite(favoriteProfileId, favoriteProductId, favoriteDate) VALUES(:favoriteProfileId, :favoriteProductId, :favoriteDate)";
		$statement = $pdo->prepare($query);
		// Bind the member variables to the place holders in the template.
		$formattedDate = $this->favoriteDate->format("Y-m-d H:i:s.u");
		$parameters = ["favoriteProfileId" => $this->favoriteProfileId, "favoriteProductId" => $this->favoriteProductId, "favoriteDate" => $formattedDate];
		$statement->execute($parameters);
		// Update the null productId with what mySQL just gave us.
		$this->favoriteProfileId = intval($pdo->lastInsertId());
	}
	/**
	 * Deletes this favorite from mySQL
	 *
	 * @param \PDO $pdo PDO connection object
	 * @throws \PDOException when mySQL related errors occur
	 * @throws \TypeError if $pdo is not a PDO connection object
	 **/
	public function delete(\PDO $pdo) : void {
		// Enforce the favoriteProfileId is not null (i.e. don't delete a favorite that hasn't been inserted).
		if($this->favoriteProfileId === null) {
			throw(new \PDOException("unable to remove a favorite that doesn't exist"));
		}
		// Create query template.
		$query = "DELETE FROM favorite WHERE favoriteProfileId = :favoriteProfileId";
		$statement = $pdo->prepare($query);
		// Bind the member variables to the place holder in the template.
		$parameters = ["favoriteProfileId" => $this->favoriteProfileId];
		$statement->execute($parameters);
	}
	/**
	 * Updates this favorite in mySQL.
	 *
	 * @param \PDO $pdo PDO connection object
	 * @throws \PDOException when mySQL related errors occur
	 * @throws \TypeError if $pdo is not a PDO connection object
	 **/
	public function update(\PDO $pdo) : void {
		// Enforce the favoriteProfileId is not null (i.e. don't update a favorite that hasn't been inserted).
		if($this->favoriteProfileId === null) {
			throw(new \PDOException("unable to update a favorite that does not exist"));
		}
		// Create query template.
		$query = "UPDATE favorite SET favoriteProfileId = :favoriteProfileId, favoriteProductId = :favoriteProductId, favoriteDate = :favoriteDate WHERE favoriteProfileId = :favoriteProfileId";
		$statement = $pdo->prepare($query);
		// Bind the member variables to the place holders in the template.
		$formattedDate = $this->favoriteDate->format("Y-m-d H:i:s.u");
		$parameters = ["favoriteProfileId" => $this->favoriteProfileId, "favoriteProductId" => $this->favoriteProductId];
		$statement->execute($parameters);
	}
	/**
	 * Gets the Favorite by favoriteProfileId
	 *
	 * @param \PDO $pdo PDO connection object
	 * @param int $favoriteProfileId favorite profile id to search by
	 * @return \SplFixedArray SplFixedArray of Favorites found
	 * @throws \PDOException when mySQL related errors occur
	 * @throws \TypeError when variables are not the correct data type
	 **/
	public static function getFavoriteByFavoriteProfileId(\PDO $pdo, int $favoriteProfileId) : \SplFixedArray {
		// Sanitize the favorite profile id before searching
		if($favoriteProfileId <= 0) {
			throw(new \RangeException("favorite profile id must be positive"));
		}
		// Create query template
		$query = "SELECT favoriteProfileId, favoriteProductId, favoriteDate FROM favorite WHERE favoriteProfileId = :favoriteProfileId";
		$statement = $pdo->prepare($query);
		// Bind the favorite profile id to the place holder in the template.
		$parameters = ["favoriteProfileId" => $favoriteProfileId];
		$statement->execute($parameters);
		// Build an array of favorites.
		$favorite = new \SplFixedArray($statement->rowCount());
		$statement->setFetchMode(\PDO::FETCH_ASSOC);
		while(($row = $statement->fetch()) !== false) {
			try {
				$favorite = new Favorite($row["favoriteProfileId"], $row["favoriteProductId	"], $row["favoriteDate"]);
			} catch(\Exception $exception) {
				// If the row couldn't be converted, rethrow it.
				throw (new \PDOException($exception->getMessage(), 0, $exception));
			}
		}
		return($favorite);
	}
	/**
	 * Gets the Favorite by favoriteProductId.
	 *
	 * @param \PDO $pdo PDO connection object
	 * @param int $favoriteProductId favorite product id to search for
	 * @return Favorite|null Favorite found or null if not found
	 * @throws \PDOException when mySQL errors occur
	 * @throws \TypeError when variables are not the correct data type
	 **/
	public static function getFavoriteByFavoriteProductId(\PDO $pdo, int $favoriteProductId) : ?Favorite {
		// Sanitize the favoriteProductId before searching.
		if($favoriteProductId <= 0) {
			throw(new \PDOException("favorite product id is not positive"));
		}
		// Create query template.
		$query = "SELECT favoriteProfileId, favoriteProductId, favoriteDate FROM favorite WHERE favoriteProductId = :favoriteProductId";
		$statement = $pdo->prepare($query);
		// Bind the favorite product id to the place holder in the template.
		$parameters = ["favoriteProductId" => $favoriteProductId];
		$statement->execute($parameters);
		// Grab the favorite from mySQL
		try {
			$favorite = null;
			$statement->setFetchMode(\PDO::FETCH_ASSOC);
			$row = $statement->fetch();
			if($row !== false) {
				$favorite = new Favorite($row["favoriteProfileId"], $row["favoriteProductId"], $row["favoriteDate"]);
			}
		} catch(\Exception $exception) {
			// If the row couldn't be converted, rethrow it.
			throw(new \PDOException($exception->getMessage(), 0, $exception));
		}
		return ($favorite);
	}
	/**
	 * gets all Favorites
	 *
	 * @param \PDO $pdo PDO connection object
	 * @return \SplFixedArray SplFixedArray of Favorites found or null if not found
	 * @throws \PDOException when mySQL related errors occur
	 * @throws \TypeError when variables are not the correct data type
	 **/
	public static function getAllFavorites(\PDO $pdo) : \SplFixedArray {
		// create query template
		$query = "SELECT favoriteProfileId, favoriteProductId, favoriteDate FROM favorite";
		$statement = $pdo->prepare($query);
		$statement->execute();
		// build an array of favorites
		$favorites = new \SplFixedArray($statement->rowCount());
		$statement->setFetchMode(\PDO::FETCH_ASSOC);
		while(($row = $statement->fetch()) !== false) {
			try {
				$favorite = new Favorite($row["favoriteProfileId"], $row["favoriteProductId"], $row["favoriteDate"]);
				$favorites[$favorites->key()] = $favorite;
				$favorites->next();
			} catch(\Exception $exception) {
				// If the row couldn't be converted, rethrow it.
				throw(new \PDOException($exception->getMessage(), 0, $exception));
			}
		}
		return ($favorites);
	}
	/**
	 * Formats the state variables for JSON serialization.
	 *
	 * @return array resulting state variables to serialize
	 **/
	public function jsonSerialize() {
		$fields = get_object_vars($this);
		//Format the date so that the front end can consume it.
		$fields["favoriteDate"] = round(floatval($this->favoriteDate->format("U.u")) * 1000);
		return($fields);
	}
}
