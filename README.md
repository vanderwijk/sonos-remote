# sonos-remote
Using the Philips Hue Dimmer as remote for Sonos

NOTE that this is just a proof of concept.

Step 1: Upload all the files from this project to a PHP webserver

Step 2: Generate a user account on the HUE bridge, see http://www.developers.meethue.com/documentation/getting-started

Step 3: Add the ip address of your HUE bridge and your username to the file hue.php:

Example:
$bridge_hostname = '192.168.1.7';
$bridge_username = 'HE1PHw5f3yBFztLniGleiPd4nH-lykybMSHF2mEm';

Watch a demo of the HUE dimmer controlling the Sonos system: https://www.youtube.com/watch?v=31EyKMU6xbg
