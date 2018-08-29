<?php

if(session_id() == ''){
    session_start();
}

require_once 'config.inc.php';
//require_once 'integration.php';
//require_once 'integrated_functions.php';



function getcontacts($userid) {
	$userid = mysql_real_escape_string(stripslashes($_GET['me']));
	$qry = mysql_query("SELECT user_id from users where user_id!='".$userid."' ");
	$users = array();
	if(mysql_num_rows($qry)) {
		while($row = mysql_fetch_array($qry)) {
			$users[] = $row['user_id'];
		}
	}
	//print_r($users);
	return $users;
}


function get_display_name($userid) {
	$userid = mysql_real_escape_string(stripslashes($userid));
	$qry = mysql_query("SELECT CONCAT_WS(' ',first_name,last_name) AS name FROM users WHERE user_id='".$userid."'");
	if(mysql_num_rows($qry)) {
		$fetch = mysql_fetch_array($qry);
		return $fetch['name'];
	} else return null;
}


function base() {
	echo ORANGE_BASE;
}

function mysql_connection() {
	mysql_connect(DBPATH, DBUSER, DBPASS) or die("Can't connect to MySQL server.");
	mysql_select_db(DBNAME) or die("Wasn't able to use database.");
	mysql_query("SET NAMES utf8;");
}

function update_lastact() {
	$userid = mysql_real_escape_string(stripslashes($_GET['me']));
	$time = time();
	$qry = mysql_query("SELECT 1 FROM chat_lastactivity WHERE user='$userid' ORDER BY id DESC LIMIT 1");
	if(mysql_num_rows($qry)) {
		mysql_query("UPDATE chat_lastactivity SET time='$time' WHERE user='$userid'");
	} else {
		mysql_query("INSERT INTO chat_lastactivity (`user`, `time`) VALUES ('$userid', '$time');");
	}
}

function t($string) {
	if(!file_exists('lang/'.LANGUAGE.'.xml')) return $string;
	$xml = simplexml_load_file('lang/'.LANGUAGE.'.xml');
	$result = $xml->xpath('/lang/message[@original="'.$string.'"]');
	if(isset($result[0][0])) {
		return trim($result[0][0]);	
	} else {
		return $string;
	}
}

function info() {
	echo <<<HTML
/*
	OpusChat

*/


HTML;
}

function is_online($userid) {
	$userid = mysql_real_escape_string(stripslashes($userid));
	$qry = mysql_query("SELECT time FROM chat_lastactivity WHERE user='$userid' ORDER BY id DESC LIMIT 1");
	if(!mysql_num_rows($qry)) return false;
	else {
		$fetch = mysql_fetch_array($qry);
		$lastact = $fetch['time'];
		$limit = strtotime("-20 seconds"); // update interval is 9 seconds
		return ($lastact>$limit);
	}
}

/*
	Receives no arguments.
	Must return the current logged in user ID.

	Change it only if you know exactly what you're doing.
*/
// function $_GET['me'] {
// 	global $_GET;
// 	//print_r($_COOKIE);
// 	print_r($_GET['me']);
// 	return $_GET['me'];
// }

/*
	Received arguments:
	- $userid

	Same as getcontacts() but returns online friends only.
*/
function getonlinecontacts($userid) {
	$return = array();
	$friends = getcontacts($userid);
	foreach($friends as $friend) {
		//if(is_online($friend)) {
			$return[] = $friend;
	}
	return $return;
}

/*
	Received arguments:
	- $message

	Work with the message text before it's sent.
	Useful if you want to block words, add bbcode etc.
*/
function hook_message_text($message) {
	return $message;
}

/*
	Received arguments:
		- $from = sender ID
		- $to = receiver ID
		- $message = message text
		- $time = timestamp of the message time
		- $message_id = message ID on chat table

	Optional. Write this function only if you want it to do something automatically
	when a message is sent (like updating some messages table).
*/
function hook_message_sent($from, $to, $message, $time, $message_id) {
	// do nothing
}