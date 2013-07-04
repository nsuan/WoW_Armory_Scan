<?php

// Checks to see if character exists, if not, put character/ guild in DB
function characterExists($characterName) {
  global $realm, $guild, $httpCode;
  $existQuery = "SELECT name, realm ";
  $existQuery.= "FROM CharactersUS ";
  $existQuery.= "WHERE name = \"$characterName\" and realm = \"$realm\""; 
  $existQuery = mysql_query($existQuery);
  $characterExists = mysql_fetch_row($existQuery);
  if(!$characterExists) {
    parseArmoryData();	  
	if($httpCode == 200) {
	  if($guild!='') addGuild($guild);
	  updateCharacters();
	} 
	else 
      createLog(date('c', time()) . ' ' . "$characterName not found in the armory. \n");
  }	  
}

// Gets all outdated characters from CharactersUS table
function getOutdatedCharacters() {
  global $characterOutdatedArray;
  $query = "SELECT name, realm ";
  $query.= "FROM CharactersUS ";
  $query.= "WHERE checktime < '" . date('c',strtotime("now")) . "'"; 
  $result = mysql_query($query);
  if($result) {
    while($row = mysql_fetch_assoc($result))  
      $characterOutdatedArray[] = array($row['realm'],$row['name']);
	createLog(date('c', time()) . ' ' . "Found " . mysql_num_rows($result) . " characters to scan. \n");
  }	  
}

// Takes armory data and assigns to variables
function  parseArmoryData() {
  global $modTime, $class, $race, $level, $points, $realm, $guild, $httpCode, $characterExists, $characterName, $iterations;
  sleep(5); // Add delay to smooth out traffic spike
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_HEADER, true);
  curl_setopt($ch, CURLOPT_URL, "us.battle.net/api/wow/character/$realm/$characterName?fields=guild");
  if($characterExists) curl_setopt($ch, CURLOPT_HTTPHEADER, array('If-Modified-Since:' . $modTime));
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  $headerString = curl_exec($ch);  
  $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  curl_close($ch);  
  $headerArray = explode("\n", $headerString);
  $iterations+=1;
  // Possible IP throttle, stop script for today.
  if($httpCode >= 500) {
    $reason = json_decode($headerArray[9]);    
    createLog(date('c', time()) . ' ' . "Error code 50X with warning '$reason->reason', ending script for today.\n");
	die(closeLogging());
  }
  if($httpCode == 200) {
    $charDataArray = json_decode($headerArray[10]);
    
    // Save results of specified fields as variables
    $modTime = $charDataArray->lastModified;
    $class = $charDataArray->class;
    $race  = $charDataArray->race;
    $level = $charDataArray->level;
    $points= $charDataArray->achievementPoints;
	$guild = $charDataArray->guild->name;
  }
} // end parseArmoryData()

// Function to parse armory and add/ update DB
function updateCharacters() {
  // Add global variables
  global $realm, $characterName, $guild, $characterExists, $modTime, $class, $race, $level, $points, $httpCode, $characterAdd, $iterations, $characterUpdate, $characterUnmod;
  
  if($httpCode == 200) {
  $week = 1; 
  // Add if character isn't in DB
  if(!$characterExists) {   
    $checkTime = date('c',strtotime("+$week week"));  
    $query = "INSERT INTO CharactersUS(name, realm, guild, class, race, modified, points, checktime) ";
    $query.= "VALUES(\"$characterName\",\"$realm\",\"$guild\",\"$class\",\"$race\",\"$modTime\",\"$points\",\"$checkTime\")";
    $result = mysql_query($query);
	if(!$result) 
	  (createLog(date('c', time()) . ' ' . "MySQL error, broke on inserting $characterName with values $characterName, $realm, $guild, $class, $race, $modTime, $points, $checkTime. \n")); 
	createLog(date('c', time()) . ' ' . "$characterName added successfully! \n");	
	$characterAdd++;
	//$iterations+=.5;
  }
      
  // Update out of date character
  else {
    // Grab weekincrement from table
    $query = "SELECT weekincrement ";
    $query.= "FROM CharactersUS ";
    $query.= "WHERE name = \"$characterName\" and realm = \"$realm\"";
    $result = mysql_query($query); 
    if(!$result)
      (createLog(date('c', time()) . ' ' . "MySQL error, broke when retrieving weekincrement from $characterName. \n")); 
    $week = mysql_result($result, 0);
	$checkTime = date('c',strtotime("+$week week"));
	
	// DB not up to date
    $query = "UPDATE CharactersUS ";
    $query.= "SET guild = \"$guild\", 
	              weekincrement = '1',
				  modified = '$modTime',
				  points = '$points',
				  checktime = '$checkTime'";
    $query.= "WHERE name = '$characterName' and realm = \"$realm\"";
    $result = mysql_query($query); 
    if(!$result)
      (createLog(date('c', time()) . ' ' . "MySQL error, broke when updating $characterName. \n")); 
	createLog(date('c', time()) . ' ' . "$characterName updated successfully! \n");
	$characterUpdate++;
	//$iterations+=.5;
  }
  } // end of $httpCode 200
  
  // Character already in DB and not updated, $httpCode 304
  else {
    $query = "SELECT weekincrement ";
    $query.= "FROM CharactersUS ";
    $query.= "WHERE name = '$characterName' and realm = \"$realm\"";
    $result = mysql_query($query); 
    if(!$result)
      (createLog(date('c', time()) . ' ' . "MySQL error, broke when retrieving weekincrement from $characterName. \n")); 
    $week = mysql_result($result, 0);
	$checkTime = date('c',strtotime("+$week week"));
	
	$query = "UPDATE CharactersUS ";
    $query.= "SET checktime = '$checkTime', weekincrement = weekincrement + 1 ";
    $query.= "WHERE name = '$characterName' and realm = \"$realm\"";
    $result = mysql_query($query); 
    if(!$result)
      (createLog(date('c', time()) . ' ' . "MySQL error, broke when updating weekincrement from $characterName. \n")); 
	createLog(date('c', time()) . ' ' . "$characterName already updated! \n");
	$characterUnmod++;
  }  
} // end updateCharacters() 

// Iterate through list of parsed character names and update DB
function updateDatabase($characterName) {
  global $characterExists, $guild, $modTime, $httpCode, $realm, $auctionCharacterArray, $iterations;
    
	// Check for character in DB
    $existQuery = "SELECT name, realm ";
    $existQuery.= "FROM CharactersUS ";
    $existQuery.= "WHERE name = \"$characterName\" and realm = \"$realm\""; 
    $existQuery = mysql_query($existQuery);
    $characterExists = mysql_fetch_row($existQuery);      
      
    // Character exists in DB
    if($characterExists) {
      $modQuery = "SELECT modified ";
      $modQuery.= "FROM CharactersUS ";
      $modQuery.= "WHERE name = \"$characterName\" and realm = \"$realm\""; 
      $modQuery = mysql_query($modQuery);
      $modQuery = mysql_result($modQuery, 0);
	  $modTime = date('r', $modQuery);
	  
	  parseArmoryData();
	  
	  // Update character in DB
	  if($httpCode == 200 || $httpCode == 304) 
	    updateCharacters();
	  
	  else {
	    $query = 'DELETE ';
        $query.= 'FROM CharactersUS ';
        $query.= "WHERE name = \"$characterName\" and realm = \"$realm\""; 
        $result = mysql_query($query); 
        if(!$result)
          (createLog(date('c', time()) . ' ' . "MySQL broke when deleting $characterName.\n"));
		createLog(date('c', time()) . ' ' . "$characterName not found in armory! Removing from DB.\n");
	  }
    }
	
	// Character doesn't exist in DB
	else {
	  parseArmoryData();
	  if($httpCode == 200) {
	    if($guild!='') addGuild($guild);
	    updateCharacters();
	  }
      else {
		createLog(date('c', time()) . ' ' . "$characterName not found in the armory. \n");
	  }
    }  
} // end updateDatabase()  
 
?>