<?php
namespace fsutils\file;

use fsutils\dir;

/**
 * Takes path-parts as an argument-list. If an argument is an array, it will be recursivly unwrapped
 *
 * @param string|array ...$path
 * @return bool
 */
function exists($path) {
	$args = func_get_args();
	$path = dir\concat($args);
	return file_exists($path);
}