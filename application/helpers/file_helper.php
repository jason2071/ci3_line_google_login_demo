<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

function deletemedialike($filename)
{
	$media_path = config_item('media_path');

	if (is_file($media_path.$filename)) {
		$path = substr($filename, 0, strrpos($filename, '/'));
		$filename = substr($filename, strrpos($filename, '/')+1);
		$file_compare = substr($filename, 0, strrpos($filename, '.'));
		$handle = opendir($media_path.$path);
		while (false !== ($file = readdir($handle))) {
			if (is_file($media_path.$path.'/'.$file) && strpos($file, $file_compare) === 0) {
				unlink($media_path.$path.'/'.$file);
			}
		}
	}
}

function deletemedia($filename)
{
	$media_path = config_item('media_path');

	if (is_file($media_path.$filename)) {
		unlink($media_path.$filename);
	}
}

function deletemediaresize($filename)
{
	$media_path = config_item('media_path');
	$media_path .= 'resize-cache/';

	$handle = opendir($media_path);
	while (false !== ($path = readdir($handle))) {
		if (is_dir($media_path.$path) && $path != '..') {
			if (is_file($media_path.$path.'/'.$filename)) {
				unlink($media_path.$path.'/'.$filename);
			}
		}
	}
}

function createmediapath($newpath)
{
	$media_path = config_item('media_path');

	$dir_path = $media_path.$newpath;
	if (!is_dir($dir_path)) {
		$arr = explode("/", $newpath);
		$dir = $media_path;
		for ($i=0; $i<count($arr); $i++)
		{
			if ($i > 0) $dir .= "/";
			$dir .= $arr[$i];
			if (!is_dir($dir)) {
				//$oldmask = umask(0);
				mkdir($dir);
				//umask($oldmask);
			}
		}
	}
}

function hashPath($no, $split=3000, $level=1)
{
	$path = ceil($no/$split).'/'.$no;
	if ($level == 2) {
		$path = ceil($no/($split*$split)).'/'.$path;
	}
	return $path;
}

function mediatmpname($path='', $prefix='')
{
	$media_path = config_item('media_path');
	
	if (!empty($path)) {
		$path .= '/';
	}

	return $media_path.'tmp/'.$path.uniqid($prefix);
}
?>