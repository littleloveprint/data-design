<?php
/**
 * PSR-4 Compliant Autoloader
 *
 * This will dynamically load classes by resolving the prefix and class name. This is the methos that frameworks such as Laravel and Composer automatically resolce class names and load them. To use it, simply set the configurable parameters inside the closure. This example is taken from PHP-FIG, references below.
 *
 * @param string $class fully qualified class name to load
 * @see http://www.php-fig.org/psr/psr-4/examples/ PSR-4 Example Autoloader
 **/
spl_autoload_register(function($class) {
	/**
	 * CONFIGURABLE PARAMETERS
	 * prefix: The prefix for all classes (i.e., the namespace)
	 * baseDir: The base directory for all classes (default = current directory)
	 **/
	$prefix = "Edu\\Cnm\\DataDesign";
	$baseDir = __DIR__;

	// Does the class use the namespace prefix?
	$len = strlen($prefix);
	if (strncmp($prefix, $class, $len) !== 0) {
		// no, move to the next registered autoloader
		return;
	}

	// Det the relative class name
	$className = substr($class, $len);

	// Replace the namespace prefix with the base directory, replace namespace
	// Separators with directory separators in the relative class name, append with .php
	$file = $baseDir . str_replace("\\", "/", $className) . ".php";

	// if the file exists, require it
	if(file_exists($file)) {
		require_once($file);
	}
});