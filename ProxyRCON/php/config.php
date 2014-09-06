<?php
	if(!defined("PSO2ProxyRcon"))
	{
		die("<h1>Config Error</h1><p>You cannot run this file directly.");
	}

	/**********************************************************
		PSO2 Proxy RCON Configuration
			$settings['host']:
				Use this to define the IP/Host of your proxy
			$settings['port']:
				Default: 8080
			$settings['rkey']:
				Change this to your proxy RCON key
			$settings['commandPrefix']:
				This is not important, but just used to show
				your prefix on the proxy WebRCON
	**********************************************************/
	$settings['host'] = "host";
	$settings['port'] = "port";
	$settings['rkey'] = "proxy_key";
	$settings['commandPrefix'] = "!";

	/**********************************************************
		Extra Options
			$settings['showInfo']:
			This will display information about your server.
			Setting it to false will speed up page load.
			Setting it to true will display information about
			the proxy, but may slow down the load speed.
	**********************************************************/
	$settings['showInfo'] = false;

	/**********************************************************
		PSO2 Proxy User Account
			This MUST be changed. It will act as a secondary 
			authorisation to your proxy console.
	**********************************************************/
	$user['username'] = "username";
	$user['password'] = "password";

	/**********************************************************
		General
	**********************************************************/
	$author = "Ashley (XenoWarrior)";
	$version = "3";

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