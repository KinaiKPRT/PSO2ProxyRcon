WebRCON for PSO2Proxy
=============

This script will allow you to utilise the WebRCON support that the PSO2Proxy provides.
<p>
You will be able to use commands from any phone with Internet access.

Installation
=============

<b>To use this code, you will need a web-server with PHP support.</b>

You will need to edit the following file(s):
```
	php/config.php
```

Configuring 'Config.php'
=============

Open the file "config.php" in the "php" folder.

You will need to change the below variables to connect to your proxy:
```
	$settings['host'] = "host";
	$settings['port'] = "port";
	$settings['rkey'] = "rcon_key";
	$settings['commandPrefix'] = "!";
```

$settings['host']: Your proxy IP address or host name.
<p>
$settings['port']: Your proxy webAPI port. (Default: 8080)
<p>
$settings['rkey']: Your WebRCON key.


Only change $settings['commandPrefix'] if you have set a different prefix on your proxy.

That is all you need to do! Just upload the files to your web-server.

Configuring Proxy RCON
=============

To be able to use WebRCON, you will need to enable to "WebAPI" plugin.
Simply move the file "WebAPI.py" from the "disabled" folder into the "plugins" folder.
(Make sure to restart your server to generate the 'webapi.config.yml' file.)

Your 'webapi.config.yml' should look somewhat like this:

```
	{"port": 8080, "ServerName": "Unnamed Server", 'webRconEnabled': false, 'webRconKey': ''}
```

You must change the 'webRconEnabled' and 'webRconKey' values:

```
	{"port": 8080, "ServerName": "ProjectGE PSO2Proxy Server", 'webRconEnabled': True, 'webRconKey': 'enter a key here'}
```

Change 'webRconEnabled' to <b>True</b> and set any key in the 'webRconKey'
<b>You must treat the 'webRconKey' as you would a password.</b> It will stop any commands from being run by unauthorised users.