<?php
	define("PSO2ProxyRcon", true); // Defined to stop running "includes" directly

	require('php/config.php'); // Get the config file

	session_name("PSO2ProxyRcon"); // Session name
	if(!isset($_SESSION)) // Check if a session is not running
	{
		session_start(); // Start the session
	}

	/**********************************************************
		PSO2 Proxy Connection Test
	**********************************************************/
	// Make sure the session is running
	if(isset($_SESSION['loggedIn']))
	{
		// Check if the user has logged in
		if($_SESSION['loggedIn']==true)
		{
			try // Try to run the code below (Mostly for the file_get_contents function)
			{
				// Make simple connection to the server testing the RCON key
				$connection = file_get_contents("http://".$settings['host'].":".$settings['port']."/rcon?key=".$settings['rkey']);
				$connection = json_decode($connection, true);
				
				if($connection['reason']=="Your RCON key is invalid!") // Check if the server returned invalid key message
				{
					$error = $connection['reason']; // Sets the error message and help message
					$help = "You can set your RCON key in the config.php file.<br>Make sure to change <b>settings['rkey']</b> to the key you have set on your proxy.";
					$_SESSION['correctKey'] = false; // Tells other functions not to connect by setting to false
				}
				else
				{
					$_SESSION['correctKey'] = true; // Tells other functions that your key is correct
				}
			}
			catch(Exception $e) // If an error occured, it is most likely HTTP GET request
			{
				$error = "Unable to connect to remote server! (".$settings['host'].":".$settings['port'].")";
				$help = "Please ensure that the host and port have been defined and you have access to the resource.";
			}
		}
	}
	// Check if there is a POST request and if the session is running
	if(isset($_POST['command_send']) AND isset($_SESSION) AND isset($_SESSION['correctKey']))
	{
		// Check the POST request, logged in status and key status
		if($_POST['command_send']=="Execute" AND $_SESSION['loggedIn']==true AND $_SESSION['correctKey']==true)
		{
			// Replace all spaces in the command with nothing
			$command = str_replace(" ", "", $_POST['command_text']);
			if($command!=="") // Ensure that the command is not empty
			{
				try // Send command
				{
					// Remove all entities from URL because &param is one of them
					$url = htmlentities("http://".$settings['host'].":".$settings['port']."/rcon?key=".$settings['rkey']."&command=".$command."&params=".$_POST['command_args']);
					$url = str_replace(" ", "%20", $url); // Replace any spaces with %20 space equiv
					$output = file_get_contents($url); // Request the file from the server
					$output = json_decode($output, true); // Decode the JSON output from server
					
				}
				catch(Exception $e) // If there was an error, most likely HTTP GET error
				{
					// Set the error message and help message
					$error = "Unable to connect to remote server! (".$settings['host'].":".$settings['port'].")"; 
					$help = "Please ensure that the host and port have been defined and you have access to the resource.";
				}
			}
			else
			{
				// Since the command was empty, just fall back to an error reason and success false
				$output['reason'] = "Invalid command.";
				$output['success'] = 0;
			}
		}
	}
	
	/**********************************************************
		Data submission
	**********************************************************/
	if(isset($_GET['logout'])) // For logging out
	{
		session_destroy(); // End session
		unset($_SESSION); // Remove session variable
		unset($_POST); // Remove POST variable
		header("Location: ./"); // Set the location to root
	}

	if(isset($_POST['submit'])) // For running POST requests
	{
		if($_POST['submit']=="Login") // For the login request
		{
			if($_POST['username'] == $user['username'] AND $_POST['password'] == $user['password']) // Checks for username and password defined in config.php
			{
				try // Try to run the code below (Mostly for the file_get_contents function)
				{
					// Make simple connection to the server testing the RCON key
					$connection = file_get_contents("http://".$settings['host'].":".$settings['port']."/rcon?key=".$settings['rkey']);
					$connection = json_decode($connection, true);

					if($connection['reason']=="Your RCON key is invalid!") // Check if the server returned invalid key message
					{
						$error = $connection['reason']; // Sets the error message and help message
						$help = "You can set your RCON key in the config.php file.<br>Make sure to change <b>settings['rkey']</b> to the key you have set on your proxy.";
						$_SESSION['correctKey'] = false; // Tells other functions not to connect by setting to false
					}
					else // Successful connection
					{
						$output = $connection; // Gets the first output from server
						$_SESSION['correctKey'] = true; // Sets the correctKey to true so login can start running commands

						if($settings['showInfo']) // This can be set in config.php, allows the WebRCON to get information from the server
						{
							$server = file_get_contents("http://".$settings['host'].":".$settings['port']); // Server information
							$config = file_get_contents("http://".$settings['host'].":".$settings['port']."/config.json"); // Config JSON
							
							// Pack data into variables
							$_SESSION['serverInfo'] = json_decode($server, true);
							$_SESSION['serverConfig'] = json_decode($config, true);
						}
					}
				}
				catch(Exception $e) // If an error occured, it is most likely HTTP GET request
				{
					$error = "Unable to connect to remote server! (".$settings['host'].":".$settings['port'].")";
					$help = "Please ensure that the host and port have been defined and you have access to the resource.";
				}

				$_SESSION['loggedIn'] = true; // Tell the script the user is logged in (this will allow connections to the server now)
				$_SESSION['username'] = $_POST['username']; // Append the username to the session (will be useful for more than one account)

				unset($_POST); // Just unset as the values are not needed
			}
			else // If the username and password did not match up
			{
				// Set the error and help message
				$error = "Your username or password has been entered incorrectly!";
				$help = "You can set your account details in config.php.<br><a href=\"./\">Click here</a> to try again.";

				unset($_POST); // Unset the POST variable
			}
		}
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<title>PSO2Proxy Remote Console</title>
		<link rel="stylesheet" type="text/css" href="css/style.css"/>
	</head>
	<body>
		<?php
			if(isset($error) AND $error!=="") // This will be used to output any error messages
			{ ?>
				<center>
					<div id="container">
						<div id="title">WebRCON Error</div>
						<br>
						<?php
							echo("<font color='red'>".$error."</font><br><br>".$help);
						?>
					</div>
				</center>
				<?php
			}
			else // If no errors, check if the user is logged in
			{ 
				if(!isset($_SESSION['loggedIn'])) // Checking if loggedIn is false
				{ ?>
					<center>
						<div id="container">
							<div id="title">WebRCON Login</div>
							<br>
							<form action="" method="post">
								<table>
									<tr>
										<td>
											<input id="input" name="username" type="text" placeholder="Enter username here..."/>
										</td>
										<td>
											<input id="input" name="password" type="password" placeholder="Enter password here..."/>
										</td>
										<td>
											<input id="button" name="submit" type="submit" value="Login"/>
										</td>
									</tr>
								</table>
							</form>
							<?php 
								if($user['username'] == "username" AND $user['password'] == "password")
								{
									echo("<br><font color='red'>WARNING: You have not changed the default username and password in config.php!</font>");
								}
							?>
						</div>
						<?php
							if($settings['wrInfo'])
							{ ?>
								<div id="container">
									<div id="title">WebRCON Information</div>
									<br>
									WebRCON is a PSO2Proxy Remote Console. It will allow you to run commands as you would in the console.
									<br>
									This page will work on mobile phones and any other device with a browser and Internet connection.
									<br><br>
									All commands will show the output generated from the command in the console output container.
									<br><br>
									All console commands will work via the WebRCON, including all plugin commands. Use the command [help] to view a list of commands.
									<br><br>
									To start, simply login above.
									<br><br>
									<font color="grey" size="2">If you want to hide this message, you can disable it in <b>config.php</b> by setting <b>$settings['wrInfo']</b> to <b>false</b>.</font>
								</div>
								<?php
							}
						?>
					</center>
					<?php
				}
				else // If loggedIn is true, start the RCON
				{
					if($_SESSION['loggedIn']==true) // Check if the user is logged in
					{ ?>
						<center>
							<div id="container">
								<div id="title">Navigation</div>
								<br>
								<table>
									<tr>
										<td>
											<a target="_BLANK" style="text-decoration:none;" href="https://github.com/XenoWarrior/PSO2ProxyRcon">
												<div id="button">
													RCON GitHub
												</div>
											</a>
										</td>
										<td>
											<a target="_BLANK" style="text-decoration:none;" href="https://github.com/CyberKitsune/PSO2Proxy">
												<div id="button">
													Proxy GitHub
												</div>
											</a>
										</td>
										<td>
											<a style="text-decoration:none;" href="?logout">
												<div id="button">
													Logout
												</div>
											</a>
										</td>
									</tr>
								</table>
							</div>
							<div id="container">
								<div id="title">Console Output</div>
								<br>
								<div style="text-align:left;overflow:auto;">
									<?php
										if(isset($output['reason'])) // If the output was a reason message
										{
											if($output['reason']=="Command not specified.") // This message is when running the connection
											{
												$string = "Type your command below."; // Replace the message with something userfriendly
											}
											else // If there was a command run
											{
												$string = $output['reason']; // Set the output to a new variable
												$string = str_replace("\n","<br>",$string); // Replace and new lines with a break
											}
											echo("<pre>".htmlentities($output['reason'])."</pre>"); // Display the string
										}
										else if(isset($output['output'])) // If the output was an output message
										{
											$string = $output['output']; // Set to string
											$string = str_replace("\n","<br>",$string); // Replace and new lines with a break
											echo("<pre>".htmlentities($output['output'])."</pre>"); // Display string
										}
										else
										{
											echo("<pre>Type a command below.<br>Use help for a list of commands.</pre>");
										}
									?>
								</div>
							</div>
							<div id="container">
								<div id="title">Execute Commands</div>
								<br>
								<form action="" method="post">
									<table>
										<tr>
											<td>
												<input id="input" name="command_text" type="text" placeholder="Enter command here..."/>
											</td>
											<td>
												<input id="input" name="command_args" type="text" placeholder="Enter arguments here..."/>
											</td>
											<td>
												<input id="button" name="command_send" type="submit" value="Execute"/>
											</td>
										</tr>
									</table>
								</form>
							</div>
							<?php 
								if($settings['showInfo']) // If this is set to true in config.php, the WebRCON will get information about your server
								{ ?>
									<div id="container">
										<div id="title">Server Information</div>
										<div style="text-align:left;">
											<?php
												// Echo the table that contains the information, will clean this up more
												echo(
													"<table>".
														"<tr>".
															"<td>".
																"<b>Server Name</b>".
															"</td>".
															"<td>".
																"<input class='serverInfo' type='text' value='".$_SESSION['serverConfig']['name']."' />".
															"</td>".
														"</tr>".
														"<tr style='height:12px;'></tr>".
														"<tr>".
															"<td>".
																"<b>Host</b>".
															"</td>".
															"<td>".
																"<input class='serverInfo' type='text' value='".$settings['host']."' />".
															"</td>".
														"</tr>".
														"<tr>".
															"<td>".
																"<b>Port</b>".
															"</td>".
															"<td>".
																"<input class='serverInfo' type='text' value='".$settings['port']."' />".
															"</td>".
														"</tr>".
														"<tr style='height:12px;'></tr>".
														"<tr>".
															"<td>".
																"<b>Live Since</b>".
															"</td>".
															"<td>".
																"<input class='serverInfo' type='text' value='".date('d/m/y h:i A', $_SESSION['serverInfo']['upSince'])."' />".
															"</td>".
														"</tr>".
														"<tr>".
															"<td>".
																"<b>Connected Players</b>".
															"</td>".
															"<td>".
																"<input class='serverInfo' type='text' value='".$_SESSION['serverInfo']['playerCount']."' />".
															"</td>".
														"</tr>".
														"<tr style='height:12px;'></tr>".
														"<tr>".
															"<td>".
																"<b>Peak Players</b>".
															"</td>".
															"<td>".
																"<input class='serverInfo' type='text' value='".$_SESSION['serverInfo']['peakPlayers']."' />".
															"</td>".
														"</tr>".
														"<tr>".
															"<td>".
																"<b>Players Cached</b>".
															"</td>".
															"<td>".
																"<input class='serverInfo' type='text' value='".$_SESSION['serverInfo']['playersCached']."' />".
															"</td>".
														"</tr>".
														"<tr style='height:12px;'></tr>".
														"<tr>".
															"<td>".
																"<b>Blocks Cached</b>".
															"</td>".
															"<td>".
																"<input class='serverInfo' type='text' value='".$_SESSION['serverInfo']['blocksCached']."' />".
															"</td>".
														"</tr>".
													"</table>"
												);
											?>
										</div>
									</div>
									<?php
								}
							?>
						</center>
						<?php
					}
					else // The user is not logged in
					{
						header("Location: ./"); // Return to root
						unset($_SESSION); // Unset the session variable
						unset($_POST); // Unset the POST variable
					}
				}
			}
		?>
	</body>
</html>