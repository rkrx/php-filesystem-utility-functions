<?php
namespace fsutils\dir\recursive;

use fsutils\dir;
use fsutils\path;

/**
 * @param string $workDir
 * @param callable $filterCallback
 * @param int $globFlags
 * @return mixed[]
 */
function contents($workDir, $filterCallback = null, $globFlags = null) {
	$res = array();
	$rec = function ($baseDir, $subDir = null) use (&$rec, &$res, $filterCallback, $globFlags) {
		$workDir = path\concat($baseDir, $subDir);
		$items = dir\contents($workDir, $filterCallback, $globFlags);
		$items = array_map(function ($item) use ($subDir) { return path\concat($subDir, $item); }, $items);
		$res = array_merge($res, $items);
		$directories = dir\directories($workDir);
		foreach($directories as $directory) {
			$subWorkDir = path\concat($subDir, $directory);
			$rec($baseDir, $subWorkDir);
		}
	};

	$rec($workDir);

	return $res;
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