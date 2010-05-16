<?php
/**
*
*===================================================================
*
*  Salve - Core
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

/**
* Salve - Core class,
* 		Used as the core for Salve
* Author: Lone Wolf
*/
abstract class core
{
	// Function to read the configuration file.
	public static function read_config_file($file)
	{
		if (!file_exists($file) || !is_readable($file))
		{
			die('<p>The Salve configuration file could not be found or is inaccessible. Check your configuration.</p>');
		}

		require($file);
	}
	
	// Lets make ourselves a nice error page if everything goes down the tubes.
	// Error and message handler, call with trigger_error if required
	public static function msg_handler($error)
	{
		// Do not display notices if we suppress them via @
		if (error_reporting() == 0 && $error != E_USER_ERROR && $error != E_USER_WARNING && $error != E_USER_NOTICE)
		{
			return;
		}
	}
}