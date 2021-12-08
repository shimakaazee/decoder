<?php 

header('Content-type:text/html;charset=utf8');
date_default_timezone_set('prc');
define('ROOT' , dirname(__DIR__));


/**
 *
 *
 * @return null|resource
 */
function conn() {
	static $conn = null;
	if($conn === null) {
		$cfg = require(ROOT . '\lib\config.php');
		$conn = mysql_connect($cfg['host'] , $cfg['user'] , $cfg['pwd']);
		mysql_query('use '.$cfg['db'] , $conn);
		mysql_query('set names '.$cfg['charset'] , $conn);
	}

	return $conn;
}

/**
 * @param $sql
 * @return resource
 */
function query($sql) {
	$rs  = mysql_query($sql,conn());
	if($rs) {
		mLog($sql);
	} else {
		mLog($sql. "\n" . mysql_error());
		mLog($sql. "\n" . mysql_error());
	}

	return $rs;
}

function mLog($str) {
	$filename = ROOT. "/log/".date('Ymd') . '.txt';
	$log = "-----------------------------------------\n".date('Y/m/d H:i:s') . "\n" . $str . "\n" . "-----------------------------------------\n\n";
	return file_put_contents($filename, $log , FILE_APPEND);
}

function createDir() {
	$path = '/upload/'.date('Y/m/d');
	$fpath = ROOT . $path;
	if(is_dir($fpath) || mkdir($fpath , 0777 , true)) {
		return $path;
	} else {
		return false;
	}
}

function getExt($filename) {
	return strrchr($filename, '.');
}

function randStr($num=6) {
	$str = str_shuffle('abcedfghjkmnpqrstuvwxyzABCEDFGHJKMNPQRSTUVWXYZ23456789');
	return substr($str, 0 , $num);
}

?>
