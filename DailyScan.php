<?php

// Initiate global variables
$modTime = 0;
$auctionScanArray = '';
$auctionCharacterArray = '';
$guildCharacterArray = '';
$characterOutdatedArray = '';
$class = 0;
$race = 0;
$level = 0;
$points = 0;
$httpCode = 0;
$iterations = 0;
$guild = '';
$conn = '';
$realm = '';
$characterAdd = 0;
$characterUpdate = 0;
$characterUnmod = 0;
$guildAdd = 0;
$guildUpdate = 0;
$auctionScan = 0;
$guildScanned = 0;
$DAYLIGHTSAVINGS = 3600;

// Main class of WoW armory crawler
include('AuctionParse.php'); // Parses AH of $realm and pulls all names to $auctionCharacterParse array
include('CharacterParse.php'); // Adds/ updates all characters in $auctionCharacterParse array
include('DBConnect.php'); // Connect to armory DB
include('GuildParse.php'); // Parse guild for character list
include('Log.php'); // Logs all actions

dbConnect();
auctionCheck();

// Parse auction house listings for characters and add characters/ guilds not in DB
if($auctionScanArray!='') {
	foreach($auctionScanArray as $realm) {
	  auctionParse($realm);
	  if($auctionCharacterArray!='') 
		foreach($auctionCharacterArray as $characterName) 
		  characterExists($characterName);
	}
}
createLog(date('c', time()) . ' ' . "AH Scan done! Scanning guilds...\n");

// Parse guilds for characters and add/ update to DB
guildCheck();
if($guildScanArray!='') {
  foreach($guildScanArray as $array) {
    guildParse($array[0], $array[1]);
	$guildScanned++;
	if(time()%86400 > (6300 + $DAYLIGHTSAVINGS) && time()%86400 < (10800 + $DAYLIGHTSAVINGS)) {
      createLog(date('c', time()) . ' ' . "Computer backup starting soon, stopping script.\n");
      closeLogging();
	  die(dbClose());
    }
	else if($iterations > 5000) {
	  createLog(date('c', time()) . ' ' . "Maximum daily scans reached, ending script.\n");
	  closeLogging();
	  die(dbClose());
	}
    else {
	if($guildCharacterArray!='') {	  
	  $realm = $array[0];
	  $guild = rawurlencode($array[1]);
	  foreach($guildCharacterArray as $characterName) {
	    if(time()%86400 > (6300 + $DAYLIGHTSAVINGS) && time()%86400 < (10800 + $DAYLIGHTSAVINGS)) {
		  createLog(date('c', time()) . ' ' . "Computer backup starting soon, stopping script.\n");
		  closeLogging();
		  die(dbClose());
		}
		else if($iterations > 5000) {
		  createLog(date('c', time()) . ' ' . "Maximum daily scans reached, ending script.\n");
		  closeLogging();
		  die(dbClose());
		}
	    else characterExists($characterName);
      }
    }
	}
  }
}
createLog(date('c', time()) . ' ' . "Guild Scan done! Scanning characters...\n");

// Parse out of date characters and update
getOutdatedCharacters();
if($characterOutdatedArray!='') {
  foreach($characterOutdatedArray as $array) {
    $realm = $array[0];
	$characterName = $array[1];
	if(time()%86400 > (6300 + $DAYLIGHTSAVINGS) && time()%86400 < (10800 + $DAYLIGHTSAVINGS)) {
      createLog(date('c', time()) . ' ' . "Computer backup starting soon, stopping script.\n");
      closeLogging();
	  die(dbClose());
    }
	else if($iterations > 5000) {
	  createLog(date('c', time()) . ' ' . "Maximum daily scans reached, ending script.\n");
	  closeLogging();
	  die(dbClose());
	}
    else updateDatabase($characterName);
  }
}
closeLogging();
dbClose();
?>
