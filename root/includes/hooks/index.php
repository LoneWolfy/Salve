<?php
/**
*
*===================================================================
*
*  Salve - Hook System
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

class salve_hook
{
	var $hooks = array();
	var $hook_result = array();
	var $current_hook = NULL;
	
	// Initialize hook class
	function __construct($valid_hooks)
	{
		foreach ($valid_hooks as $_null => $method)
		{
			$this->add_hook($method);
		}

		if (function_exists('salve_hook_register'))
		{
			salve_hook_register($this);
		}
	}
	
	function register($definition, $hook, $mode = 'normal')
	{
		$class = (!is_array($definition)) ? '__global' : $definition[0];
		$function = (!is_array($definition)) ? $definition : $definition[1];

		// Method able to be hooked?
		if (isset($this->hooks[$class][$function]))
		{
			switch ($mode)
			{
				case 'standalone':
					if (!isset($this->hooks[$class][$function]['standalone']))
					{
						$this->hooks[$class][$function] = array('standalone' => $hook);
					}
					else
					{
						trigger_error('Hook not able to be called standalone, previous hook already standalone.', E_NOTICE);
					}
				break;

				case 'first':
				case 'last':
					$this->hooks[$class][$function][$mode][] = $hook;
				break;

				case 'normal':
				default:
					$this->hooks[$class][$function]['normal'][] = $hook;
				break;
			}
		}
	}
	
	function call_hook($definition)
	{
		$class = (!is_array($definition)) ? '__global' : $definition[0];
		$function = (!is_array($definition)) ? $definition : $definition[1];

		if (!empty($this->hooks[$class][$function]))
		{
			// Developer tries to call a hooked function within the hooked function...
			if ($this->current_hook !== NULL && $this->current_hook['class'] === $class && $this->current_hook['function'] === $function)
			{
				return false;
			}

			// Call the hook with the arguments attached and store result
			$arguments = func_get_args();
			$this->current_hook = array('class' => $class, 'function' => $function);
			$arguments[0] = &$this;

			// Call the hook chain...
			if (isset($this->hooks[$class][$function]['standalone']))
			{
				$this->hook_result[$class][$function] = call_user_func_array($this->hooks[$class][$function]['standalone'], $arguments);
			}
			else
			{
				foreach (array('first', 'normal', 'last') as $mode)
				{
					if (!isset($this->hooks[$class][$function][$mode]))
					{
						continue;
					}

					foreach ($this->hooks[$class][$function][$mode] as $hook)
					{
						$this->hook_result[$class][$function] = call_user_func_array($hook, $arguments);
					}
				}
			}

			$this->current_hook = NULL;
			return true;
		}

		$this->current_hook = NULL;
		return false;
	}
	
	function previous_hook_result($definition)
	{
		$class = (!is_array($definition)) ? '__global' : $definition[0];
		$function = (!is_array($definition)) ? $definition : $definition[1];

		if (!empty($this->hooks[$class][$function]) && isset($this->hook_result[$class][$function]))
		{
			return array('result' => $this->hook_result[$class][$function]);
		}

		return false;
	}
	
	function hook_return($definition)
	{
		$class = (!is_array($definition)) ? '__global' : $definition[0];
		$function = (!is_array($definition)) ? $definition : $definition[1];

		if (!empty($this->hooks[$class][$function]) && isset($this->hook_result[$class][$function]))
		{
			return true;
		}

		return false;
	}
	
	function hook_return_result($definition)
	{
		$class = (!is_array($definition)) ? '__global' : $definition[0];
		$function = (!is_array($definition)) ? $definition : $definition[1];

		if (!empty($this->hooks[$class][$function]) && isset($this->hook_result[$class][$function]))
		{
			$result = $this->hook_result[$class][$function];
			unset($this->hook_result[$class][$function]);
			return $result;
		}

		return;
	}
	
	function add_hook($definition)
	{
		if (!is_array($definition))
		{
			$definition = array('__global', $definition);
		}

		$this->hooks[$definition[0]][$definition[1]] = array();
	}

	function remove_hook($definition)
	{
		$class = (!is_array($definition)) ? '__global' : $definition[0];
		$function = (!is_array($definition)) ? $definition : $definition[1];

		if (isset($this->hooks[$class][$function]))
		{
			unset($this->hooks[$class][$function]);

			if (isset($this->hook_result[$class][$function]))
			{
				unset($this->hook_result[$class][$function]);
			}
		}
	}
}