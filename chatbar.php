<?php

require_once 'common.php';
header('Access-Control-Allow-Origin: *');  

mysql_connection();
update_lastact();

if(!(isset($_GET['act'])) OR !(preg_match('/^(update_chat_bar|chat_friends_list)$/', $_GET['act']))) exit;

switch ($_GET['act']) {
	case 'update_chat_bar':
		update_chat_bar();
		break;
	
	case 'chat_friends_list':
		chat_friends_list();
		break;
}

function update_chat_bar() {
	$count = sizeof(getcontacts($_GET['me']));
	echo t('Chat').' ('.$count.')';
	exit;
}

function chat_friends_list() {
	$friends = getcontacts($_GET['me']);
	$count = sizeof($friends);
	if($count) {
		$result = null;
		echo '<input class="filter_users" style="width:100%" placeholder="Search..." />';
		foreach($friends as $friend) {
			$result .= '<a href="#" onclick="javascript:chatWith(\''.$friend.'\', \''.get_display_name($friend).'\');hide_chat_list();return false;" class="chat_boxes" ><li class="chat_boxes">'.get_display_name($friend).'</li></a>';
		}
		// echo '<div class="sub chat_boxes">'.t('Chat').' ('.$count.')</div>';
		echo '<ul id="f_users"  class="chat_boxes">'.$result.'</ul>';
	} else {
		echo t('No online users.');
	}
	?>
<script type="text/javascript">
	$(document).ready(function(){
	  $(".filter_users").on("keyup", function() {
	    var value = $(this).val().toLowerCase();
	    $("#f_users a li").filter(function() {
	      $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
	    });
	  });
	});
</script>
<style>
	.filter_users{
		border: 0;
	    float: none;
	    box-shadow: none;
	    border-radius: 0;
	    padding: 6px;
    	margin: 0px;
    	border-bottom: 1px #14b9ce1f solid;
	}
</style>
	<?php
}
