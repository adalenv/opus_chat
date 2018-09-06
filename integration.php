<?php
/*
	Set the name of the session that your app uses to save the current logged on user id.
*/
//define('SESSION', $_SESSION['user_id']);

/* Functions */

/*
	Received arguments:
		 - $userid = current logged in user ID (same as getuserid())

	Must return a single one-dimension array with all the IDs of the
	logged on users that are friends with $userid (or not, if you don't want).
	An empty array (0 values) must be returned if no users are online.
*/
function getcontacts($userid,$role) {
	$userid = mysql_real_escape_string(stripslashes($userid));
	switch ($role) {
		case "backoffice":
			$qry = mysql_query("SELECT user_id from users where user_id!='".$userid."' and role='supervisor' ");
			break;
		case "supervisor":
			$qry = mysql_query("SELECT user_id from users where user_id!='".$userid."' and (role='backoffice' or role='operator') ");
			break;
		case "operator":
			$qry = mysql_query("SELECT user_id from users where user_id!='".$userid."' and role='supervisor' ");
			break;
	}
	$users = array();
	if(mysql_num_rows($qry)) {
		while($row = mysql_fetch_array($qry)) {
			$users[] = $row['user_id'];
		}
	}
	return $users;
}

/*
	Received arguments:
		- $userid = User ID to get the display name

		Must return a string with the user display name.
		It can be the username/login or user real name. Up to you.
*/

function get_display_name($userid) {
	$userid = mysql_real_escape_string(stripslashes($userid));
	$qry = mysql_query("SELECT CONCAT_WS(' ',first_name,last_name) AS name FROM users WHERE user_id='".$userid."'");
	if(mysql_num_rows($qry)) {
		$fetch = mysql_fetch_array($qry);
		return $fetch['name'];
	} else return null;
}

/* Optional: Edit file "integrated_functions.php" for more options */