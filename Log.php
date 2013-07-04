<?php 

// Creates log
$logName = date('Y-m-d') . '.log'; 
$logHandle = fopen("/var/log/armory/$logName",'a');

function createLog($logString) {
  global $logHandle;
  fwrite($logHandle, $logString);
}

createLog(date('c', time()) . ' ' . "Starting Script! \n");

function closeLogging() {
  global $startTime, $logHandle, $characterAdd, $characterUpdate, $characterUnmod, $guildAdd, $guildUpdate, $guildScanned;
  createLog(date('c', time()) . ' ' . 'All done! Completed in ' . (time() - $startTime) . "! Added $characterAdd characters, updated $characterUpdate characters, ignored $characterUnmod characters, added $guildAdd guilds, and updated $guildUpdate guilds. \n");
  
  // Close log file
  fclose($logHandle);
}

?>