<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">

		<title>Conceptual Model</title>
	</head>
	<body>
		<header>
			<h1>Conceptual Model</h1>
		</header>
		<main>
			<h2>Entities &amp; Attributes</h2>
			<h3>Profile</h3>
			<ul>
				<li>profileSalt</li>
				<li>profileHash</li>
				<li>profileId</li>
				<li>profileImage</li>
				<li>profileUserName</li>
				<li>profileLocation</li>
				<li>profileJoinDate</li>
				<li>profileAbout</li>
				<li>profileFavoriteItems</li>
			</ul>
			<h3>Product</h3>
			<ul>
				<li>productId</li>
				<li>productProfileId</li>
				<li>productDescription</li>
				<li>productPrice</li>
				<li>productPostDate</li>
			</ul>
			<h3>Favorite</h3>
			<ul>
				<li>favoriteProfileId</li>
				<li>favoriteProductId</li>
				<li>favoriteDate</li>
			</ul>
			<p><strong>Subject: </strong>Courtney Thompson<br><strong>Verb: </strong>Favorites<br><strong>Object: </strong>Coozies</p>

			<h2>Relationships</h2>
			<ul>
				<li>One profile can favorite many products.</li>
				<li>Many profiles can favorite many products.</li>
				<li>Many products can be favorited many times.</li>
			</ul>
		</main>
	</body>
</html>