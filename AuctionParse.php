<?php

// Function to parse a server's auction house data for character names
function auctionParse($realm) {
  global $auctionCharacterArray, $realm, $auctionScan, $iterations;
  $auctionCharacterArray = '';
  // Grab AH data JSON file
  $ahData = file_get_contents('http://us.battle.net/api/wow/auction/data/' .$realm);
  if(!$ahData) createLog(date('c', time()) . ' ' . "Could not find $realm auction data. \n");
  $json = json_decode($ahData);

  // Grab names from AH
  $ahData2 = file_get_contents($json->files[0]->url); 
  if(!$ahData) createLog(date('c', time()) . ' ' . "Could not get characters from $realm auction house. \n");
  else {
	  createLog(date('c', time()) . ' ' . "Scanning $realm auction house.\n");
	  $ahData2 = json_decode($ahData2);
	  $charArray = "";
	  for($i=0;$i<sizeof($ahData2->alliance->auctions);$i++)
	   $charArray[] .= $ahData2->alliance->auctions[$i]->owner;
	  for($i=0;$i<sizeof($ahData2->horde->auctions);$i++)
	   $charArray[] .= $ahData2->horde->auctions[$i]->owner;
	  for($i=0;$i<sizeof($ahData2->neutral->auctions);$i++)
	   $charArray[] .= $ahData2->neutral->auctions[$i]->owner;
	  $charArray = array_unique($charArray);
	  $auctionScan++;
	  $iterations+=5;
  }
}

// Function to check DB to see if any servers need parsing, if so, add to array and update checktime
function auctionCheck() {
  global $auctionScanArray;
  $auctionScanArray = array();
  $query = "SELECT realm ";
  $query.= "FROM AuctionUS ";
  $query.= "WHERE checktime < '" . date('c',strtotime("now")) . "'";
  $checkResult = mysql_query($query);
  if($checkResult) {
    while($row = mysql_fetch_assoc($checkResult)) {
      $auctionScanArray[] .= $row['realm'];	  
	  $query = "UPDATE AuctionUS ";
      $query.= "SET checktime = '" . date('c',strtotime("+1 week")) . "'";
      $query.= "WHERE realm = '" . $row['realm'] . "'";
	  $result = mysql_query($query);
	  if(!$result)
	    createLog(date('c', time()) . ' ' . "MySQL error, broke when updating AuctionUS checktime for " . $row['realm'] . ".\n"); 
	}
	createLog(date('c', time()) . ' ' . "Found " . mysql_num_rows($checkResult) . " new auction houses to scan. \n");
  }
}

?>
