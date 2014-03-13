<?php
namespace fsutils\path;

/**
 * @param string $path
 * @param string $directorySeparator
 * @return string
 */
function unixify($path, $directorySeparator = null) {
	if(is_null($directorySeparator)) {
		$directorySeparator = DIRECTORY_SEPARATOR;
	}
	$path = str_replace($directorySeparator, '/', $path);
	if($directorySeparator !== '\\') {
		$path = str_replace('\\', '/', $path);
	}
	return $path;
}