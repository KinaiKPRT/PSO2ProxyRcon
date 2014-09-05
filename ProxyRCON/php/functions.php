<?php
	if(!defined("PSO2ProxyRcon"))
	{
		die("<h1>Config Error</h1><p>You cannot run this file directly.");
	}

	/**********************************************************
		Functions for the RCON script

			checkVersion:
				This will ensure you keep WebRCON up-to-date.

		More functions will come as features are added.
	**********************************************************/

	function checkVersion($version){
		try
		{
			$checker = file_get_contents("http://dev.projectge.com/rconver.php");
			$checker = json_decode($checker, true);
			if($checker['version']!==$version AND $checker['version']!=="")
			{
				return("A new version of this script is available! Please use the <a href='https://github.com/XenoWarrior/PSO2ProxyRcon'>GitHub</a> to download and see the changes.");
			}
		}
		catch(Exception $e)
		{
			return("Unable to check for updates!");
		}
	}
?>