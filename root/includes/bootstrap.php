<?php
/**
*
*===================================================================
*
*  Salve - Bootstrap
*-------------------------------------------------------------------
*	Script info:
* Version:		1.0.0 - "Bolt"
* Copyright:	(c) 2010 - Lone Wolf
* License:		http://opensource.org/licenses/gpl-license.php  |  GNU Public License v2
* Package:		Salve
*
*===================================================================
*
* Bootstrap will kill uninstalled/unauthorized access attempts and try to force installation/upgrades automatically 
* 	if new updated files are detected, and will automatically load needed classes & files for each mode.
*
*/

if (!defined('IN_SALVE'))
{
	exit;
}

// - Load commonly required files first of all
require SALVE_ROOT_PATH . 'includes/constants.' . PHP_EXT; // Constants always load first.
require SALVE_ROOT_PATH . 'includes/core.' . PHP_EXT;
require SALVE_ROOT_PATH . 'includes/functions.' . PHP_EXT;
require SALVE_ROOT_PATH . 'includes/api.' . PHP_EXT;

if (file_exists(SALVE_ROOT_PATH . 'config.' . PHP_EXT))
{
	core::read_config_file(SALVE_ROOT_PATH . 'config.' . PHP_EXT);
}

// Let's set the error handler to ours.
set_error_handler(defined('SALVE_MSG_HANDLER') ? SALVE_MSG_HANDLER : 'msg_handler');