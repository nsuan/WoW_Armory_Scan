WoW_Armory_Scan
===============

PHP Script that will scan the World of Warcraft armory for simple character data, and save it to a DB.

Full credit for the inspiration and methodology of scanning for characters goes to http://wow.realmpop.com/.

Overview:
The flow of the script for parsing character information from the WoW Armory is as follows:<br/>
1. Check all realms in the AuctionUS table to see if any need their auction houses(AH) scanned.<br/>
2. Any realms that haven't had their AH scanned in a week, scan all characters that have something posted,
add/ update any characters to the CharacterUS table, and add their guild to the GuildUS table if it is not
already there.<br/>
3. Check the GuildUS table to see if any guilds need to be scanned.<br/>
4. For all outdated guilds, scan all the characters and add/update their data to the CharacterUS table.<br/>
5. Check the CharacterUS table to see if any characters need to be scanned.<br/>
6. For all outdated characters, scan all the characters and add/update their data to the CharacterUS table.<br/>

Note: Once the proper MySQL tables have been made and the login credentials have been updated in DBConnect.php,
this script should work out of the box. The current settings are for those who do not have a Blizzard API key 
and are limited to approximently 3000 queries per day. If the user does have an API key, the iterations variable
can be modified in DailyScan.php to determine how much data to parse on a given run of the script.
