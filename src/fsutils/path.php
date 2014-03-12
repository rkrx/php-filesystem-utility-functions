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
	return str_replace($directorySeparator, '/', $path);
}