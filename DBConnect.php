<?php

function dbConnect() {
  global $conn;
  //Initiate connection to the database
  $conn = mysql_connect('localhost', 'username', 'password') or die(createLog(date('c', time()) . ' ' . "Could not connect to server. \n"));
  $db_selected = mysql_select_db('armory', $conn) or die(createLog(date('c', time()) . ' ' . "Could not connect to DB. \n"));
}

function dbClose() {
  global $conn;
  // Close connection
  mysql_close($conn);
}

?>