<?php
namespace fsutils\path;

/**
 * Takes path-parts as an argument-list. If an argument is an array, it will be recursivly unwrapped
 *
 * @param string|array ...$path
 * @return bool
 */
function exists($path) {
	$args = func_get_args();
	$path = concat($args);
	return file_exists($path);
}

/**
 * Credits go to Christian @ http://stackoverflow.com/questions/4049856/replace-phps-realpath
 *
 * @param $path
 * @return mixed|string
 */
function normalize($path) {
	$unipath = strlen($path) == 0 || $path{0} != '/';
	if(strpos($path, ':') === false && $unipath) {
		$path = getcwd().DIRECTORY_SEPARATOR.$path;
	}
	$path = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $path);
	$parts = array_filter(explode(DIRECTORY_SEPARATOR, $path), 'strlen');
	$absolutes = array();
	foreach($parts as $part) {
		if('.' == $part) {
			continue;
		}
		if('..' == $part) {
			array_pop($absolutes);
		} else {
			$absolutes[] = $part;
		}
	}
	$path = implode(DIRECTORY_SEPARATOR, $absolutes);
	if(file_exists($path) && linkinfo($path) > 0) {
		$path = readlink($path);
	}
	$path = !$unipath ? '/'.$path : $path;
	return $path;
}

/**
 * Takes path-parts as an argument-list. If an argument is an array, it will be recursivly unwrapped
 *
 * @param string|array ...$path
 * @return string
 */
function concat($path) {
	$arguments = func_get_args();
	$parts = array();
	foreach($arguments as $argument) {
		if(is_array($argument)) {
			$parts[] = call_user_func_array(__FUNCTION__, $argument);
		} else {
			$parts[] = unixify((string) $argument);
		}
	}
	$parts = array_filter($parts);
	$count = count($parts);
	foreach($parts as $idx => $arg) {
		if($idx == 0) {
			$parts[$idx] = rtrim($arg, '/');
		} elseif($idx == $count - 1) {
			$parts[$idx] = ltrim($arg, '/');
		} else {
			$parts[$idx] = trim($arg, '/');
		}
	}
	return join('/', $parts);
}

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