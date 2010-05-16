<?php
/**
*
*===================================================================
*
*  Salve - Index
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