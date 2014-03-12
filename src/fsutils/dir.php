<?php
namespace fsutils\dir;

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
			$parts[] = (string) $argument;
		}
	}
	$parts = array_filter($parts);
	$parts = array_map(function ($value) {
		return str_replace('\\', '/', $value);
	}, $parts);
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
 * @param string $workDir
 * @param callable $filterCallback
 * @param int $globFlags
 * @return mixed[]
 */
function contents($workDir, $filterCallback = null, $globFlags = null) {
	if($globFlags === null) {
		$globFlags = GLOB_NOCHECK | GLOB_NOSORT;
	}
	$path = concat(array($workDir, '*'));
	$files = glob($path, $globFlags);
	if(is_callable($filterCallback)) {
		$files = array_map($filterCallback, $files);
	}
	$files = array_filter($files);
	$files = array_map('basename', $files);
	return $files;
}

/**
 * @param string $workDir
 * @param callable $filterCallback
 * @param int $globFlags
 * @return mixed[]
 */
function directories($workDir, $filterCallback = null, $globFlags = null) {
	if($globFlags === null) {
		$globFlags = GLOB_NOCHECK | GLOB_NOSORT;
	}
	$globFlags |= GLOB_ONLYDIR;
	return contents($workDir, $filterCallback, $globFlags);
}

/**
 * @param string $workDir
 * @param callable $filterCallback
 * @param int $globFlags
 * @return mixed[]
 */
function files($workDir, $filterCallback = null, $globFlags = null) {
	$innerFilterCallback = function ($filename) use ($filterCallback) {
		if(!is_file($filename)) {
			return null;
		}
		if($filterCallback !== null) {
			return call_user_func($filterCallback, $filename);
		}
		return $filename;
	};
	$files = contents($workDir, $innerFilterCallback, $globFlags);
	return $files;
}

