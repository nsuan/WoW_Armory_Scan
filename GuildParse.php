<?php

function addGuild($guild) {
  global $realm, $guildAdd;  
  $query = "SELECT realm, guild ";
  $query.= "FROM GuildUS ";
  $query.= "WHERE realm = \"$realm\" and guild = \"$guild\"";
  $result = mysql_query($query);
  $guildExists = mysql_fetch_row($result);
  if(!$guildExists) {
    $checkTime = date('c',strtotime("now"));
    $query = "INSERT INTO GuildUS(realm, guild, checktime) ";
    $query.= "VALUES(\"$realm\",\"$guild\",'$checkTime')";
    $result = mysql_query($query);
	createLog(date('c', time()) . ' ' . "Added $guild to GuildUS table.\n");
	$guildAdd++;
  }    
}

function guildParse($realm, $guild) {
  global $guildCharacterArray, $guildUpdate, $iterations;
  $guildCharacterArray = '';
  $guild = rawurlencode($guild);
  // Parse guild for all characters and save to array $list
  $guildData = file_get_contents('http://us.battle.net/api/wow/guild/' . $realm . '/' . $guild . '?fields=members');
  if(!$guildData) {
    createLog(date('c', time()) . ' ' . "Could not find guild $guild. Deleting. \n"); 
    $guild = rawurldecode($guild);	
	$query = 'DELETE ';
    $query.= 'FROM GuildUS ';
    $query.= "WHERE realm = '$realm' and guild = '$guild'";
    $query = mysql_query($query); 
  }
  else {
	$guild = rawurldecode($guild);
	createLog(date('c', time()) . ' ' . "Scanning guild $guild.\n");
	$guildList = json_decode($guildData);
	for($i=0;$i<sizeof($guildList->members);$i++)
	  if($guildList->members[$i]->character->level >= 10)  $guildCharacterArray[] .= $guildList->members[$i]->character->name;	
	$guild = preg_replace("[']","\'",$guild);
	$query = "UPDATE GuildUS ";
	$query.= "SET checktime = '" . date('c',strtotime("+1 week")) . "' ";
	$query.= "WHERE realm = '$realm' and guild = '$guild'";
	$result = mysql_query($query);
	$guildUpdate++;
	$iterations+=3;
  }
}

// Function to check DB to see if any guilds need parsing
function guildCheck() {
  global $guildScanArray;
  $guildScanArray = array();
  $query = "SELECT * ";
  $query.= "FROM GuildUS ";
  $query.= "WHERE checktime < '" . date('c',strtotime("now")) . "'";
  $query.= "ORDER BY checktime asc";
  $result = mysql_query($query);
  if($result) {
    while($row = mysql_fetch_assoc($result))  
      $guildScanArray[] = array($row['realm'],$row['guild']);
	createLog(date('c', time()) . ' ' . "Found " . mysql_num_rows($result) . " guilds to scan. \n");
  }
}


?>
