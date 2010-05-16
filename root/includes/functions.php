<?php
/**
*
*===================================================================
*
*  Salve - Functions
*-------------------------------------------------------------------
*	Script info:
* Version:		1.0.0 - "Bolt"
* Copyright:	(c) 2010 - Lone Wolf
* License:		http://opensource.org/licenses/gpl-license.php  |  GNU Public License v2
* Package:		Salve
*
*===================================================================
*
*/

if (!defined('IN_SALVE'))
{
	exit;
}
//set_var - Set variable, used by the request_var function.
function set_var(&$result, $var, $type, $multibyte = false)
{
	settype($var, $type);
	$result = $var;

	if ($type == 'string')
	{
		$result = trim(htmlspecialchars(str_replace(array("\r\n", "\r", "\0"), array("\n", "\n", ''), $result), ENT_COMPAT, 'UTF-8'));

		if (!empty($result))
		{
			// Make sure multibyte characters are wellformed
			if ($multibyte)
			{
				if (!preg_match('/^./u', $result))
				{
					$result = '';
				}
			}
			else
			{
				// no multibyte, allow only ASCII (0-127)
				$result = preg_replace('/[\x80-\xFF]/', '?', $result);
			}
		}

		$result = (STRIP) ? stripslashes($result) : $result;
	}
}

// request_var - Used to get passed variable
function request_var($var_name, $default, $multibyte = false, $cookie = false)
{
	if (!$cookie && isset($_COOKIE[$var_name]))
	{
		if (!isset($_GET[$var_name]) && !isset($_POST[$var_name]))
		{
			return (is_array($default)) ? array() : $default;
		}
		$_REQUEST[$var_name] = isset($_POST[$var_name]) ? $_POST[$var_name] : $_GET[$var_name];
	}

	$super_global = ($cookie) ? '_COOKIE' : '_REQUEST';
	if (!isset($GLOBALS[$super_global][$var_name]) || is_array($GLOBALS[$super_global][$var_name]) != is_array($default))
	{
		return (is_array($default)) ? array() : $default;
	}

	$var = $GLOBALS[$super_global][$var_name];
	if (!is_array($default))
	{
		$type = gettype($default);
	}
	else
	{
		list($key_type, $type) = each($default);
		$type = gettype($type);
		$key_type = gettype($key_type);
		if ($type == 'array')
		{
			reset($default);
			$default = current($default);
			list($sub_key_type, $sub_type) = each($default);
			$sub_type = gettype($sub_type);
			$sub_type = ($sub_type == 'array') ? 'NULL' : $sub_type;
			$sub_key_type = gettype($sub_key_type);
		}
	}

	if (is_array($var))
	{
		$_var = $var;
		$var = array();

		foreach ($_var as $k => $v)
		{
			set_var($k, $k, $key_type);
			if ($type == 'array' && is_array($v))
			{
				foreach ($v as $_k => $_v)
				{
					if (is_array($_v))
					{
						$_v = null;
					}
					set_var($_k, $_k, $sub_key_type);
					set_var($var[$k][$_k], $_v, $sub_type, $multibyte);
				}
			}
			else
			{
				if ($type == 'array' || is_array($v))
				{
					$v = null;
				}
				set_var($var[$k], $v, $type, $multibyte);
			}
		}
	}
	else
	{
		set_var($var, $var, $type, $multibyte);
	}

	return $var;
}

// Retrieve contents from remotely stored file
function get_remote_file($host, $directory, $filename, &$errstr, &$errno, $port = 80, $timeout = 10)
{
	if ($fsock = @fsockopen($host, $port, $errno, $errstr, $timeout))
	{
		@fputs($fsock, "GET $directory/$filename HTTP/1.1\r\n");
		@fputs($fsock, "HOST: $host\r\n");
		@fputs($fsock, "Connection: close\r\n\r\n");

		$file_info = '';
		$get_info = false;

		while (!@feof($fsock))
		{
			if ($get_info)
			{
				$file_info .= @fread($fsock, 1024);
			}
			else
			{
				$line = @fgets($fsock, 1024);
				if ($line == "\r\n")
				{
					$get_info = true;
				}
				else if (stripos($line, '404 not found') !== false)
				{
					$errstr = 'The requested file could not be found.' . ': ' . $filename;
					return false;
				}
			}
		}
		@fclose($fsock);
	}
	else
	{
		if ($errstr)
		{
			$errstr = utf8_convert_message($errstr);
			return false;
		}
		else
		{
			$errstr = 'The operation could not be completed because the <var>fsockopen</var> function has been disabled or the server being queried could not be found.';
			return false;
		}
	}

	return $file_info;
}

/**
 * Checks to see if the installed version of the Code Repository is current.
 * 
 * @param boolean &$up_to_date - Is the installation up-to-date?
 * @param string &$latest_version - What /is/ the latest version, anyways?
 * @param string &$announcement_url - Where's the release-topic/release-announcement URL?
 */
function check_version(&$up_to_date, &$latest_version, &$announcement_url)
{
	global $user, $config;
	// Check the version, load out remote version check file!
	$errstr = '';
	$errno = 0;
	$info = get_remote_file('subversion.assembla.com', '/versions', ((!defined('SALVE_DEV_COPY')) ? 'xerxes.txt' : 'xerxes_dev.txt'), $errstr, $errno);
	if ($info === false)
	{
		trigger_error($errstr, E_USER_WARNING);
	}
	$info = explode("\n", $info);
	$latest_version = trim($info[0]);
	$announcement_url = htmlspecialchars(trim($info[1]));
	$up_to_date = (!version_check($config['version'], $latest_version, '<'));
}

/**
 * Wrapper function for version_compare(), just does all the str_replace() and strtolower() stuff that phpBB does.
 * 
 * @param string $base - First parameter for version compare
 * @param string $compare - Second parameter for version compare
 * @param string $type - How do we compare the parameters?
 * 
 * @return boolean - Does the version evaluate?
 */
function version_check($base, $compare, $type)
{
	return version_compare(str_replace('rc', 'RC', strtolower($base)), str_replace('rc', 'RC', strtolower($compare)), $type);
}

// Now we shall get rid of anything that is useless to us
function garbage_collection()
{
	
}