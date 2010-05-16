<?php
/**
*
*===================================================================
*
*  Salve - Error Handler
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

define('IN_SALVE', true);
if(!defined('SALVE_ROOT_PATH')) define('SALVE_ROOT_PATH', './');
if(!defined('PHP_EXT')) define('PHP_EXT', substr(strrchr(__FILE__, '.'), 1));
require SALVE_ROOT_PATH . 'includes/bootstrap.' . PHP_EXT;

$mode = salve_request_var('mode', '');

switch ($mode)
{
	case '400': // Error 400 - Bad Request
		core::msg_handler('');
	break;
	
	case '401': // Error 401 - Unauthorized
		core::msg_handler('');
	break;
	
	case '403': // Error 403 - Forbidden
		core::msg_handler('');
	break;
	
	case '404': // Error 404 - Not Found
		core::msg_handler('');
	break;
	
	case '500': // Error 500 - Internal Server Error
		core::msg_handler('');
	break;
	
	default:	// Unknown Error - Panic!
		core::msg_handler('');
	break;
}