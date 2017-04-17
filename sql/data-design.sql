DROP TABLE IF EXISTS 'favorite';
DROP TABLE IF EXISTS 'product';
DROP TABLE IF EXISTS 'profile';

-- The profile entity.
CREATE TABLE profile (
		-- This creates the attribute for the primary key.
		-- Auto_increment tells mySQL to number them {1, 2, 3, ...}
		-- Not null means the attribute is required!
		profileId INT UNSIGNED AUTO_INCREMENT NOT NULL,
		-- ^ Primary Key ^
		profileUserName VARCHAR(32) NOT NULL,
		profileLocation VARCHAR(64) NOT NULL,
		profileJoinDate DATETIME(6) NOT NULL,
		profileHash CHAR(128) NOT NULL,
		profileSalt CHAR(64) NOT NULL,
		UNIQUE(profileUserName),
		PRIMARY KEY(profileId)
);

-- The product entity.
CREATE TABLE product (
		-- this is for another primary key...
		productId INT UNSIGNED AUTO_INCREMENT NOT NULL,
		productProfileId INT UNSIGNED NOT NULL,
		productDescription VARCHAR(1000) NOT NULL,
		productPrice DECIMAL(11,2) NOT NULL,
		productPostDate DATETIME(6) NOT NULL,
		INDEX(productProfileId),
	-- ^ This creates an index before making a foreign key.
		FOREIGN KEY(productProfileId) REFERENCES profile(profileId),
	-- ^ This creates the actual foreign key relation.
		PRIMARY KEY(productId)
		-- ^ This creates the primary key.
);

-- The favorite entity.
CREATE TABLE `favorite` (
		favoriteProfileId INT UNSIGNED NOT NULL,
		favoriteProductId INT UNSIGNED NOT NULL,
		favoriteDate DATETIME(6) NOT NULL,
		-- ^ These are not auto_increment because they're still foreign keys.
		INDEX(favoriteProfileId),
		INDEX(favoriteProductId),
	-- ^ Index the foreign keys.
		FOREIGN KEY(favoriteProfileId) REFERENCES profile(profileId),
		FOREIGN KEY(favoriteProductId) REFERENCES product(productId),
	-- ^ Create the foreign key relations.
		PRIMARY KEY(favoriteProfileId, favoriteProductId)
	-- ^ Finally, create a composite foreign key with the two foreign keys.
);