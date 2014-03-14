<?php
namespace fsutils\file;

use DateTime;
use Exception;
use fsutils\exceptions\FileException;
use fsutils\path;

/**
 * @param string $path
 * @throws Exception
 * @return DateTime
 */
function mtime($path) {
	$mtime = @filemtime($path);
	$err = error_get_last();
	if($err['type'] == E_WARNING) {
		throw new FileException($err['message']);
	}
	return $mtime;
}

/**
 * @param string $path
 * @throws Exception
 * @return DateTime
 */
function changedDateTime($path) {
	$timestamp = @filemtime($path);
	$err = error_get_last();
	if($err['type'] == E_WARNING) {
		throw new FileException($err['message']);
	}
	$res = new DateTime();
	$res->setTimestamp($timestamp);
	return $res;
}

/**
 * @param string $path
 * @throws Exception
 * @return DateTime
 */
function inodeChangedDateTime($path) {
	$timestamp = @filectime($path);
	$err = error_get_last();
	if($err['type'] == E_WARNING) {
		throw new FileException($err['message']);
	}
	$res = new DateTime();
	$res->setTimestamp($timestamp);
	return $res;
}

/**
 * @param string $path
 * @throws Exception
 * @return DateTime
 */
function lastAccessDateTime($path) {
	$timestamp = @fileatime($path);
	$err = error_get_last();
	if($err['type'] == E_WARNING) {
		throw new FileException($err['message']);
	}
	$res = new DateTime();
	$res->setTimestamp($timestamp);
	return $res;
}