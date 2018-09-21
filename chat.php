<?php
require_once 'common.php';
header('Access-Control-Allow-Origin: *');  

global $dbh;
$dbh = mysql_connect(DBPATH,DBUSER,DBPASS);
mysql_selectdb(DBNAME,$dbh);
update_lastact($_GET['me']);

if ($_GET['action'] == "chatheartbeat") { chatHeartbeat(); } 
if ($_GET['action'] == "sendchat") { sendChat(); } 
if ($_GET['action'] == "closechat") { closeChat(); } 
if ($_GET['action'] == "startchatsession") { startChatSession(); } 
if ($_GET['action'] == "openChat") { openChat(); } 
if ($_GET['action'] == "getName") { getName(); } 
if (!isset($_SESSION['chatHistory'])) {
	$_SESSION['chatHistory'] = array();	
}

if (!isset($_SESSION['openChatBoxes'])) {
	$_SESSION['openChatBoxes'] = array();	
}
//////////////////////////////////////////////
function getName(){
	$userid = mysql_real_escape_string(stripslashes($_POST['id']));
	$qry = mysql_query("SELECT CONCAT_WS(' ',first_name,last_name) AS name FROM users WHERE user_id='".$userid."'");
	if(mysql_num_rows($qry)) {
		$fetch = mysql_fetch_array($qry);
		echo $fetch['name'];
	} else return null;
}

function chatHeartbeat() {
	
	$sql = "select * from chat where (chat.to = '".mysql_real_escape_string($_GET['me'])."' AND recd = 0) order by id ASC";
	$query = mysql_query($sql);
	$items = '';

	$chatBoxes = array();

	while ($chat = mysql_fetch_array($query)) {

		if (!isset($_SESSION['openChatBoxes'][$chat['from']]) && isset($_SESSION['chatHistory'][$chat['from']])) {
			$items = $_SESSION['chatHistory'][$chat['from']];
		}

		$chat['message'] = sanitize($chat['message']);
		//$chat['displayname'] = get_display_name($chat['from']);
		$n=explode(' ',get_display_name($chat['from']));
		$chat['displayname'] =$n[0].' '.$n[1];
		$items .= <<<EOD
					   {
			"s": "0",
			"f": "{$chat['from']}",
			"d": "{$chat['displayname']}",
			"m": "{$chat['message']}",
			"t": "{$chat['sent']}"
	   },
EOD;

	if (!isset($_SESSION['chatHistory'][$chat['from']])) {
		$_SESSION['chatHistory'][$chat['from']] = '';
	}

	//$chat['displayname'] = get_display_name($chat['from']);
	$n=explode(' ',get_display_name($chat['from']));
	$chat['displayname'] =$n[0].' '.$n[1];
	$_SESSION['chatHistory'][$chat['from']] .= <<<EOD
						   {
			"s": "0",
			"f": "{$chat['from']}",
			"d": "{$chat['displayname']}",
			"m": "{$chat['message']}",
			"t": "{$chat['sent']}"
	   },
EOD;
		
		unset($_SESSION['tsChatBoxes'][$chat['from']]);
		$_SESSION['openChatBoxes'][$chat['from']] = $chat['sent'];
	}

	if (!empty($_SESSION['openChatBoxes'])) {
	foreach ($_SESSION['openChatBoxes'] as $chatbox => $time) {
		if (!isset($_SESSION['tsChatBoxes'][$chatbox])) {
			$now = time()-strtotime($time);
			$time = date(TIMEFORMAT, strtotime($time));

			$message = t('Sent at')." $time";
// 			if ($now > 180) {
// 				$displayname = get_display_name($chatbox);
// 				$items .= <<<EOD
// {
// "s": "2",
// "f": "$chatbox",
// "d": "{$displayname}",
// "m": "{$message}"
// },
// EOD;

// 	if (!isset($_SESSION['chatHistory'][$chatbox])) {
// 		$_SESSION['chatHistory'][$chatbox] = '';
// 	}

// 	$displayname = get_display_name($chatbox);
// 	$_SESSION['chatHistory'][$chatbox] .= <<<EOD
// 		{
// "s": "2",
// "f": "$chatbox",
// "d": "{$displayname}",
// "m": "{$message}"
// },
// EOD;
// 			$_SESSION['tsChatBoxes'][$chatbox] = 1;
// 		}
		}
	}
}

	$sql = "update chat set recd = 1 where chat.to = '".mysql_real_escape_string($_GET['me'])."' and recd = 0";
	$query = mysql_query($sql);

	if ($items != '') {
		$items = substr($items, 0, -1);
	}
header('Content-type: application/json');
?>
{
		"items": [
			<?php echo $items;?>
        ]
}

<?php
			exit(0);
}
//////////////////////////////////////////////
function chatBoxSession($chatbox) {
	
	$items = '';
	
	if (isset($_SESSION['chatHistory'][$chatbox])) {
		$items = $_SESSION['chatHistory'][$chatbox];
	}

	return $items;
}
//////////////////////////////////////////////
function startChatSession() {
	$items = '';
	if (!empty($_SESSION['openChatBoxes'])) {
		foreach ($_SESSION['openChatBoxes'] as $chatbox => $void) {
			$items .= chatBoxSession($chatbox);
		}
	}


	if ($items != '') {
		$items = substr($items, 0, -1);
	}

header('Content-type: application/json');
?>
{
		"username": "<?php echo $_GET['me']; ?>",
		"items": [
			<?php echo $items;?>
        ]
}

<?php


	exit(0);
}
//////////////////////////////////////////////
function sendChat() {
	$from = $_GET['me'];
	$to = $_POST['to'];
	$message = $_POST['message'];
	if(function_exists('hook_message_text') AND hook_message_text($message)!='') $message = hook_message_text($message);

	$_SESSION['openChatBoxes'][$_POST['to']] = date('Y-m-d H:i:s', time());
	
	$messagesan = sanitize(stripslashes($message));

	if (!isset($_SESSION['chatHistory'][$_POST['to']])) {
		$_SESSION['chatHistory'][$_POST['to']] = '';
	}

	$displayname = t('Me'); //get_display_name($_GET['me']);
	$_SESSION['chatHistory'][$_POST['to']] .= <<<EOD
					   {
			"s": "1",
			"f": "{$to}",
			"d": "{$displayname}",
			"m": "{$messagesan}"
	   },
EOD;


	unset($_SESSION['tsChatBoxes'][$_POST['to']]);

	$sql = "insert into chat (chat.from,chat.to,message,sent) values ('".mysql_real_escape_string($from)."', '".mysql_real_escape_string($to)."','".mysql_real_escape_string(stripslashes($message))."',NOW())";
	$query = mysql_query($sql);
	if(function_exists('hook_message_sent')) hook_message_sent($from, $to, $message, time(), mysql_insert_id());
	echo "1";
	exit(0);
}
//////////////////////////////////////////////
function closeChat() {

	unset($_SESSION['openChatBoxes'][$_POST['chatbox']]);
	
	echo "1";
	exit(0);
}
//////////////////////////////////////////////
function sanitize($text) {
	$text = htmlspecialchars($text, ENT_QUOTES);
	$text = str_replace("\n\r","\n",$text);
	$text = str_replace("\r\n","\n",$text);
	$text = str_replace("\n","<br />",$text);
	return $text;
}


function openChat() {
	
	$sql = "select * from chat where (chat.to = '".mysql_real_escape_string($_GET['me'])."' AND chat.from= '".mysql_real_escape_string($_GET['from'])."'  ) OR (chat.to = '".mysql_real_escape_string($_GET['from'])."' AND chat.from= '".mysql_real_escape_string($_GET['me'])."'  ) AND recd = 1  limit 100 ";
	$query = mysql_query($sql);
	$items = '';

	$chatBoxes = array();

	while ($chat = mysql_fetch_array($query)) {

		if (!isset($_SESSION['openChatBoxes'][$chat['from']]) && isset($_SESSION['chatHistory'][$chat['from']])) {
			$items = $_SESSION['chatHistory'][$chat['from']];
		}

		$chat['message'] = sanitize($chat['message']);
		//$chat['displayname'] = get_display_name($chat['from']);
		$n=explode(' ',get_display_name($chat['from']));
		$chat['displayname'] =$n[0].' '.$n[1];
		$items .= <<<EOD
					   {
			"s": "0",
			"f": "{$chat['from']}",
			"d": "{$chat['displayname']}",
			"m": "{$chat['message']}",
			"t": "{$chat['sent']}"
	   },
EOD;

	if (!isset($_SESSION['chatHistory'][$chat['from']])) {
		$_SESSION['chatHistory'][$chat['from']] = '';
	}

	//$chat['displayname'] = get_display_name($chat['from']);
	$n=explode(' ',get_display_name($chat['from']));
	$chat['displayname'] =$n[0].' '.$n[1];
	$_SESSION['chatHistory'][$chat['from']] .= <<<EOD
						   {
			"s": "0",
			"f": "{$chat['from']}",
			"d": "{$chat['displayname']}",
			"m": "{$chat['message']}",
			"t": "{$chat['sent']}"
	   },
EOD;
		
		unset($_SESSION['tsChatBoxes'][$chat['from']]);
		$_SESSION['openChatBoxes'][$chat['from']] = $chat['sent'];
	}

	if (!empty($_SESSION['openChatBoxes'])) {
	foreach ($_SESSION['openChatBoxes'] as $chatbox => $time) {
		if (!isset($_SESSION['tsChatBoxes'][$chatbox])) {
			$now = time()-strtotime($time);
			$time = date(TIMEFORMAT, strtotime($time));

			$message = t('Sent at')." $time";
// 			if ($now > 180) {
// 				$displayname = get_display_name($chatbox);
// 				$items .= <<<EOD
// {
// "s": "2",
// "f": "$chatbox",
// "d": "{$displayname}",
// "m": "{$message}"
// },
// EOD;

// 	if (!isset($_SESSION['chatHistory'][$chatbox])) {
// 		$_SESSION['chatHistory'][$chatbox] = '';
// 	}

// 	$displayname = get_display_name($chatbox);
// 	$_SESSION['chatHistory'][$chatbox] .= <<<EOD
// 		{
// "s": "2",
// "f": "$chatbox",
// "d": "{$displayname}",
// "m": "{$message}"
// },
// EOD;
// 			$_SESSION['tsChatBoxes'][$chatbox] = 1;
// 		}
		}
	}
}

	$sql = "update chat set recd = 1 where chat.to = '".mysql_real_escape_string($_GET['me'])."' and recd = 0";
	$query = mysql_query($sql);

	if ($items != '') {
		$items = substr($items, 0, -1);
	}
header('Content-type: application/json');
?>
{
		"items": [
			<?php echo $items;?>
        ]
}

<?php
			exit(0);
}