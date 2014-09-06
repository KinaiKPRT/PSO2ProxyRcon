<?php
	if(!defined("PSO2ProxyRcon"))
	{
		die("<h1>Config Error</h1><p>You cannot run this file directly.");
	}

	/**********************************************************
		PSO2 Proxy RCON Configuration
	**********************************************************/
	$settings['host'] = "host";
	$settings['port'] = "port";
	$settings['rkey'] = "rcon_key";
	$settings['commandPrefix'] = "!";

	/**********************************************************
		PSO2 Proxy User Account
	**********************************************************/
	$user['username'] = "username";
	$user['password'] = "password";

	/**********************************************************
		General
	**********************************************************/
	$author = "Ashley (XenoWarrior)";
	$version = "2";

	/**********************************************************
		Set timezone and custom error handler
	**********************************************************/
	date_default_timezone_set("UTC");

	set_error_handler(
		function($severity, $message, $file, $line)
		{
			throw new ErrorException($message, $severity, $severity, $file, $line);
		}
	);
?>