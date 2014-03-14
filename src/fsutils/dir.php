<?php
namespace fsutils\dir;

use fsutils\path;

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
	$path = path\concat(array($workDir, '*'));
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

