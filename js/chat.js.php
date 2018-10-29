<?php
if(session_id() == ''){
    session_start();
}

header("Content-type: text/javascript");
require_once '../common.php';
chdir("../");
info();

$txt_says = t('says');
$txt_me = t('Me');
?>



!function(t,e){"object"==typeof exports&&"undefined"!=typeof module?module.exports=e():"function"==typeof define&&define.amd?define(e):t.timeago=e()}(this,function(){"use strict";var t="second_minute_hour_day_week_month_year".split("_"),e="秒_分钟_小时_天_周_月_年".split("_"),n=[60,60,24,7,365/7/12,12],r={en:function(e,n){if(0===n)return["just now","right now"];var r=t[parseInt(n/2)];return e>1&&(r+="s"),[e+" "+r+" ago","in "+e+" "+r]},zh_CN:function(t,n){if(0===n)return["刚刚","片刻后"];var r=e[parseInt(n/2)];return[t+" "+r+"前",t+" "+r+"后"]}},a=function(t){return parseInt(t)},i=function(t){return t instanceof Date?t:!isNaN(t)||/^\d+$/.test(t)?new Date(a(t)):(t=(t||"").trim().replace(/\.\d+/,"").replace(/-/,"/").replace(/-/,"/").replace(/(\d)T(\d)/,"$1 $2").replace(/Z/," UTC").replace(/([\+\-]\d\d)\:?(\d\d)/," $1$2"),new Date(t))},o=function(t,e,i){e=r[e]?e:r[i]?i:"en";for(var o=0,u=t<0?1:0,c=t=Math.abs(t);t>=n[o]&&o<n.length;o++)t/=n[o];return(t=a(t))>(0===(o*=2)?9:1)&&(o+=1),r[e](t,o,c)[u].replace("%s",t)},u=function(t,e){return((e=e?i(e):new Date)-i(t))/1e3},c=function(t,e){return t.getAttribute?t.getAttribute(e):t.attr?t.attr(e):void 0},f=function(t){return c(t,"data-timeago")||c(t,"datetime")},d=[],l=function(t){t&&(clearTimeout(t),delete d[t])},s=function(t){if(t)l(c(t,"data-tid"));else for(var e in d)l(e)},h=function(){function t(t,e){for(var n=0;n<e.length;n++){var r=e[n];r.enumerable=r.enumerable||!1,r.configurable=!0,"value"in r&&(r.writable=!0),Object.defineProperty(t,r.key,r)}}return function(e,n,r){return n&&t(e.prototype,n),r&&t(e,r),e}}();var p=function(){function t(e,n){!function(t,e){if(!(t instanceof e))throw new TypeError("Cannot call a class as a function")}(this,t),this.nowDate=e,this.defaultLocale=n||"en"}return h(t,[{key:"setLocale",value:function(t){this.defaultLocale=t}},{key:"doRender",value:function(t,e,r){var a=this,i=u(e,this.nowDate);t.innerHTML=o(i,r,this.defaultLocale);var c=function(t,e){var n=setTimeout(function(){l(n),t()},e);return d[n]=0,n}(function(){a.doRender(t,e,r)},Math.min(1e3*function(t){for(var e=1,r=0,a=Math.abs(t);t>=n[r]&&r<n.length;r++)t/=n[r],e*=n[r];return a=(a%=e)?e-a:e,Math.ceil(a)}(i),2147483647));!function(t,e){t.setAttribute?t.setAttribute("data-tid",e):t.attr&&t.attr("data-tid",e)}(t,c)}},{key:"render",value:function(t,e){void 0===t.length&&(t=[t]);for(var n=void 0,r=0,a=t.length;r<a;r++)n=t[r],s(n),this.doRender(n,f(n),e)}},{key:"format",value:function(t,e){return o(u(t,this.nowDate),e,this.defaultLocale)}}]),t}(),v=function(t,e){return new p(t,e)};return v.register=function(t,e){r[t]=e},v.cancel=s,v});

ta = timeago();




var windowFocus = true;
var username;
var chatHeartbeatCount = 0;
var minChatHeartbeat = 1000;
var maxChatHeartbeat = 33000;
var chatHeartbeatTime = minChatHeartbeat;
var originalTitle;
var blinkOrder = 0;

var chatboxFocus = new Array();
var newMessages = new Array();
var newMessagesWin = new Array();
var chatBoxes = new Array();
var newDisplay = new Array();

$(document).ready(function(){
	originalTitle = document.title;
	startChatSession();

	$([window, document]).blur(function(){
		windowFocus = false;
	}).focus(function(){
		windowFocus = true;
		document.title = originalTitle;
	});
});
//////////////////////////////////////////////
function restructureChatBoxes() {
	align = 0;
	for (x in chatBoxes) {
		chatboxtitle = chatBoxes[x];

		if ($("#chatbox_"+chatboxtitle).css('display') != 'none') {
			if (align == 0) {
				$("#chatbox_"+chatboxtitle).css('right', '280px');
			} else {
				width = (align)*(225+7)+280;
				$("#chatbox_"+chatboxtitle).css('right', width+'px');
			}
			align++;
		}
	}
}
//////////////////////////////////////////////
function chatWith(chatuser, displayname) {
	createChatBox(chatuser);
	setdisplayname(chatuser, displayname);
	$("#chatbox_"+chatuser+" .chatboxtextarea").focus();
	openChat(chatuser);
}
//////////////////////////////////////////////
function setdisplayname(chatbox, displayname) {
	$("#chatbox_"+chatbox+" .chatboxtitle").text(displayname);
}
//////////////////////////////////////////////
function createChatBox(chatboxtitle,minimizeChatBox) {
	if(chatboxtitle=='<?=$_GET['me'];?>'){return;}
	if ($("#chatbox_"+chatboxtitle).length > 0) {
		if ($("#chatbox_"+chatboxtitle).css('display') == 'none') {
			$("#chatbox_"+chatboxtitle).css('display','block');
			restructureChatBoxes();
		}
		$("#chatbox_"+chatboxtitle+" .chatboxtextarea").focus();
		return;
	}

	$(" <div />" ).attr("id","chatbox_"+chatboxtitle)
	.addClass("chatbox")
	.html('<div href="javascript:void(0)" onclick="javascript:toggleChatBoxGrowth(\''+chatboxtitle+'\')" class="chatboxhead"><div  class="chatboxtitle">'+chatboxtitle+'</div><div class="chatboxoptions"><!-- <a href="javascript:void(0)" onclick="javascript:toggleChatBoxGrowth(\''+chatboxtitle+'\')">-</a> --> <a href="javascript:void(0)" onclick="javascript:closeChatBox(\''+chatboxtitle+'\')">×</a></div><br clear="all"/></div><div class="chatboxcontent"></div><div class="chatboxinput"><textarea class="chatboxtextarea" onkeydown="javascript:return checkChatBoxInputKey(event,this,\''+chatboxtitle+'\');"></textarea></div>')
	.appendTo($( "body" ));
			   
	$("#chatbox_"+chatboxtitle).css('bottom', '0px');
	
	chatBoxeslength = 0;

	for (x in chatBoxes) {
		if ($("#chatbox_"+chatBoxes[x]).css('display') != 'none') {
			chatBoxeslength++;
		}
	}

	if (chatBoxeslength == 0) {
		$("#chatbox_"+chatboxtitle).css('right', '280px');
	} else {
		width = (chatBoxeslength)*(225+7)+280;
		$("#chatbox_"+chatboxtitle).css('right', width+'px');
	}
	
	chatBoxes.push(chatboxtitle);

	if (minimizeChatBox == 1) {
		minimizedChatBoxes = new Array();

		if ($.cookie('chatbox_minimized')) {
			minimizedChatBoxes = $.cookie('chatbox_minimized').split(/\|/);
		}
		minimize = 0;
		for (j=0;j<minimizedChatBoxes.length;j++) {
			if (minimizedChatBoxes[j] == chatboxtitle) {
				minimize = 1;
			}
		}

		if (minimize == 1) {
			$('#chatbox_'+chatboxtitle+' .chatboxcontent').css('display','none');
			$('#chatbox_'+chatboxtitle+' .chatboxinput').css('display','none');
		}
	}

	chatboxFocus[chatboxtitle] = false;

	$("#chatbox_"+chatboxtitle+" .chatboxtextarea").blur(function(){
		chatboxFocus[chatboxtitle] = false;
		$("#chatbox_"+chatboxtitle+" .chatboxtextarea").removeClass('chatboxtextareaselected');
	}).focus(function(){
		chatboxFocus[chatboxtitle] = true;
		newMessages[chatboxtitle] = false;
		$('#chatbox_'+chatboxtitle+' .chatboxhead').removeClass('chatboxblink');
		$("#chatbox_"+chatboxtitle+" .chatboxtextarea").addClass('chatboxtextareaselected');
	});

	$("#chatbox_"+chatboxtitle).click(function() {
		if ($('#chatbox_'+chatboxtitle+' .chatboxcontent').css('display') != 'none') {
			//$("#chatbox_"+chatboxtitle+" .chatboxtextarea").focus();
		}
	});

	$("#chatbox_"+chatboxtitle).show();
}

//////////////////////////////////////////////
function chatHeartbeat(){

	var itemsfound = 0;
	
	if (windowFocus == false) {
 
		var blinkNumber = 0;
		var titleChanged = 0;
		for (x in newMessagesWin) {
			if (newMessagesWin[x] == true) {
				++blinkNumber;
				if (blinkNumber >= blinkOrder) {
					txtdisplay = newDisplay[x];
					document.title = txtdisplay+' <?php echo $txt_says; ?>';
					titleChanged = 1;
					break;	
				}
			}
		}
		
		if (titleChanged == 0) {
			document.title = originalTitle;
			blinkOrder = 0;
		} else {
			++blinkOrder;
		}

	} else {
		for (x in newMessagesWin) {
			newMessagesWin[x] = false;
		}
	}

	for (x in newMessages) {
		if (newMessages[x] == true) {
			if (chatboxFocus[x] == false) {
				//FIXME: add toggle all or none policy, otherwise it looks funny
				$('#chatbox_'+x+' .chatboxhead').toggleClass('chatboxblink');
			}
		}
	}
	
	$.ajax({
	  url: "<?php base(); ?>chat.php?action=chatheartbeat&me=<?=$_GET['me']; ?>",
	  cache: false,
	  dataType: "json",
	  success: function(data) {

		$.each(data.items, function(i,item){
			if (item)	{ // fix strange ie bug

				chatboxtitle = item.f;

				if ($("#chatbox_"+chatboxtitle).length <= 0) {
					createChatBox(chatboxtitle);
					setdisplayname(chatboxtitle, item.d);
				}
				if ($("#chatbox_"+chatboxtitle).css('display') == 'none') {
					$("#chatbox_"+chatboxtitle).css('display','block');
					restructureChatBoxes();
				}
				
				if (item.s == 1) {
					item.f = username;
				}

				var audio = new Audio('<?php base(); ?>themes/beep-08b.mp3');
				audio.play();

				if (item.s == 2) {
					$("#chatbox_"+chatboxtitle+" .chatboxcontent").append('<div class="chatboxmessage"><span class="chatboxinfo">'+item.m+'</span></div>');
				} else {
					newMessages[chatboxtitle] = true;
					newMessagesWin[chatboxtitle] = true;
					newDisplay[chatboxtitle] = item.d;
					//$("#chatbox_"+chatboxtitle+" .chatboxcontent").append('<div class="chatboxmessage"><span class="chatboxmessagefrom">'+item.d+':&nbsp;&nbsp;</span><span class="chatboxmessagecontent">'+item.m+'</span></div>');
					$("#chatbox_"+chatboxtitle+" .chatboxcontent").append('<div class="chatboxmessage recived" ><!-- <span class="chatboxmessagefrom">'+item.d+':&nbsp;&nbsp;</span> --><span class="chatboxmessagecontent">'+item.m+'</span></div>');
					SaveDataToLocalStorage(item.f);
					openChat(item.f);
				}

				$("#chatbox_"+chatboxtitle+" .chatboxcontent").scrollTop($("#chatbox_"+chatboxtitle+" .chatboxcontent")[0].scrollHeight);
				itemsfound += 1;
			}
		});

		chatHeartbeatCount++;

		if (itemsfound > 0) {
			chatHeartbeatTime = minChatHeartbeat;
			chatHeartbeatCount = 1;
		} else if (chatHeartbeatCount >= 10) {
			chatHeartbeatTime *= 2;
			chatHeartbeatCount = 1;
			if (chatHeartbeatTime > maxChatHeartbeat) {
				chatHeartbeatTime = maxChatHeartbeat;
			}
		}
		
		setTimeout('chatHeartbeat();',chatHeartbeatTime);
	}});
}
//////////////////////////////////////////////
function closeChatBox(chatboxtitle) {
	$('#chatbox_'+chatboxtitle).css('display','none');
	restructureChatBoxes();

	$.post("<?php base(); ?>chat.php?action=closechat&me=<?=$_GET['me']; ?>", { chatbox: chatboxtitle} , function(data){	
	});
	DeleteDataFromLocalStorage(chatboxtitle);
}
//////////////////////////////////////////////
function toggleChatBoxGrowth(chatboxtitle) {
	if ($('#chatbox_'+chatboxtitle+' .chatboxcontent').css('display') == 'none') {  
		
		var minimizedChatBoxes = new Array();
		
		if ($.cookie('chatbox_minimized')) {
			minimizedChatBoxes = $.cookie('chatbox_minimized').split(/\|/);
		}

		var newCookie = '';

		for (i=0;i<minimizedChatBoxes.length;i++) {
			if (minimizedChatBoxes[i] != chatboxtitle) {
				newCookie += chatboxtitle+'|';
			}
		}

		newCookie = newCookie.slice(0, -1)


		$.cookie('chatbox_minimized', newCookie);
		$('#chatbox_'+chatboxtitle+' .chatboxcontent').css('display','block');
		$('#chatbox_'+chatboxtitle+' .chatboxinput').css('display','block');
		$("#chatbox_"+chatboxtitle+" .chatboxcontent").scrollTop($("#chatbox_"+chatboxtitle+" .chatboxcontent")[0].scrollHeight);
	} else {
		
		var newCookie = chatboxtitle;

		if ($.cookie('chatbox_minimized')) {
			newCookie += '|'+$.cookie('chatbox_minimized');
		}


		$.cookie('chatbox_minimized',newCookie);
		$('#chatbox_'+chatboxtitle+' .chatboxcontent').css('display','none');
		$('#chatbox_'+chatboxtitle+' .chatboxinput').css('display','none');
	}
	
}

function checkChatBoxInputKey(event,chatboxtextarea,chatboxtitle) {
	 
	if(event.keyCode == 13 && event.shiftKey == 0)  {
		message = $(chatboxtextarea).val();
		message = message.replace(/^\s+|\s+$/g,"");

		$(chatboxtextarea).val('');
		$(chatboxtextarea).focus();
		$(chatboxtextarea).css('height','20px');
		if (message != '') {
			$.post("<?php base(); ?>chat.php?action=sendchat&me=<?=$_GET['me']; ?>", {to: chatboxtitle, message: message.replace(/[^a-zA-Z0-9 ,]/g,'')} , function(data){
				message = message.replace(/</g,"&lt;").replace(/>/g,"&gt;").replace(/\"/g,"&quot;");
				//$("#chatbox_"+chatboxtitle+" .chatboxcontent").append('<div class="chatboxmessage"><span style="float: right !important;" class="chatboxmessagecontent">'+message+'</span></div><br/>');
				$("#chatbox_"+chatboxtitle+" .chatboxcontent").append('<div class="chatboxmessage sent"><span  class="chatboxmessagecontent">'+message+'</span></div>');
				$("#chatbox_"+chatboxtitle+" .chatboxcontent").scrollTop($("#chatbox_"+chatboxtitle+" .chatboxcontent")[0].scrollHeight);
			});
		}
		chatHeartbeatTime = minChatHeartbeat;
		chatHeartbeatCount = 1;

		return false;
	}
	
	if(event.keyCode == 27) {
		closeChatBox(chatboxtitle);
	}

	var adjustedHeight = chatboxtextarea.clientHeight;
	var maxHeight = 94;

	if (maxHeight > adjustedHeight) {
		adjustedHeight = Math.max(chatboxtextarea.scrollHeight, adjustedHeight);
		if (maxHeight)
			adjustedHeight = Math.min(maxHeight, adjustedHeight);
		if (adjustedHeight > chatboxtextarea.clientHeight)
			$(chatboxtextarea).css('height',adjustedHeight+8 +'px');
	} else {
		$(chatboxtextarea).css('overflow','auto');
	}
	 
}
//////////////////////////////////////////////
function startChatSession(){  
	$.ajax({
	  url: "<?php base(); ?>chat.php?action=startchatsession&me=<?=$_GET['me']; ?>",
	  cache: false,
	  dataType: "json",
	  success: function(data) {
 
		username = data.username;

		$.each(data.items, function(i,item){
			if (item)	{ // fix strange ie bug

				chatboxtitle = item.f;

				if ($("#chatbox_"+chatboxtitle).length <= 0) {
					createChatBox(chatboxtitle,1);
					setdisplayname(chatboxtitle, item.d);
				}
				
				if (item.s == 1) {
					item.f = username;
				}

				if (item.s == 2) {
					$("#chatbox_"+chatboxtitle+" .chatboxcontent").append('<div class="chatboxmessage"><span class="chatboxinfo">'+item.m+'</span></div>');
				} else {
					$("#chatbox_"+chatboxtitle+" .chatboxcontent").append('<div class="chatboxmessage"><span class="chatboxmessagefrom">'+item.d+':&nbsp;&nbsp;</span><span class="chatboxmessagecontent">'+item.m+'</span></div>');
				}
			}
		});
		
		for (i=0;i<chatBoxes.length;i++) {
			chatboxtitle = chatBoxes[i];
			$("#chatbox_"+chatboxtitle+" .chatboxcontent").scrollTop($("#chatbox_"+chatboxtitle+" .chatboxcontent")[0].scrollHeight);
			setTimeout('$("#chatbox_"+chatboxtitle+" .chatboxcontent").scrollTop($("#chatbox_"+chatboxtitle+" .chatboxcontent")[0].scrollHeight);', 100); // yet another strange ie bug
		}
	
	setTimeout('chatHeartbeat();',chatHeartbeatTime);
		
	}});
}

/**
 * Cookie plugin
 *
 * Copyright (c) 2006 Klaus Hartl (stilbuero.de)
 * Dual licensed under the MIT and GPL licenses:
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.gnu.org/licenses/gpl.html
 *
 */

jQuery.cookie = function(name, value, options) {
    if (typeof value != 'undefined') { // name and value given, set cookie
        options = options || {};
        if (value === null) {
            value = '';
            options.expires = -1;
        }
        var expires = '';
        if (options.expires && (typeof options.expires == 'number' || options.expires.toUTCString)) {
            var date;
            if (typeof options.expires == 'number') {
                date = new Date();
                date.setTime(date.getTime() + (options.expires * 24 * 60 * 60 * 1000));
            } else {
                date = options.expires;
            }
            expires = '; expires=' + date.toUTCString(); // use expires attribute, max-age is not supported by IE
        }
        // CAUTION: Needed to parenthesize options.path and options.domain
        // in the following expressions, otherwise they evaluate to undefined
        // in the packed version for some reason...
        var path = options.path ? '; path=' + (options.path) : '';
        var domain = options.domain ? '; domain=' + (options.domain) : '';
        var secure = options.secure ? '; secure' : '';
        document.cookie = [name, '=', encodeURIComponent(value), expires, path, domain, secure].join('');
    } else { // only name given, get cookie
        var cookieValue = null;
        if (document.cookie && document.cookie != '') {
            var cookies = document.cookie.split(';');
            for (var i = 0; i < cookies.length; i++) {
                var cookie = jQuery.trim(cookies[i]);
                // Does this cookie string begin with the name we want?
                if (cookie.substring(0, name.length + 1) == (name + '=')) {
                    cookieValue = decodeURIComponent(cookie.substring(name.length + 1));
                    break;
                }
            }
        }
        return cookieValue;
    }
};

function openChat(from){

	var itemsfound = 0;
	
/* 	if (windowFocus == false) {
 
		var blinkNumber = 0;
		var titleChanged = 0;
		for (x in newMessagesWin) {
			if (newMessagesWin[x] == true) {
				++blinkNumber;
				if (blinkNumber >= blinkOrder) {
					txtdisplay = newDisplay[x];
					document.title = txtdisplay+' <?php echo $txt_says; ?>';
					titleChanged = 1;
					break;	
				}
			}
		}
		
		if (titleChanged == 0) {
			document.title = originalTitle;
			blinkOrder = 0;
		} else {
			++blinkOrder;
		}

	} else {
		for (x in newMessagesWin) {
			newMessagesWin[x] = false;
		}
	}

	for (x in newMessages) {
		if (newMessages[x] == true) {
			if (chatboxFocus[x] == false) {
				//FIXME: add toggle all or none policy, otherwise it looks funny
				$('#chatbox_'+x+' .chatboxhead').toggleClass('chatboxblink');
			}
		}
	*/
	
	$.ajax({
	  url: "<?php base(); ?>chat.php?action=openChat&me=<?=$_GET['me']; ?>&from="+from,
	  cache: false,
	  dataType: "json",
	  success: function(data) {

		$.each(data.items, function(i,item){
			if (item)	{ // fix strange ie bug

				chatboxtitle = from;

				if ($("#chatbox_"+chatboxtitle).length <= 0) {
					createChatBox(chatboxtitle);
					setdisplayname(chatboxtitle, item.d);
				}
				if ($("#chatbox_"+chatboxtitle).css('display') == 'none') {
					$("#chatbox_"+chatboxtitle).css('display','block');
					restructureChatBoxes();
				}
				
				if (item.s == 1) {
					item.f = username;
				}
				var ts=new Date(item.t).toLocaleString('sq-AL');
				ts=ts.split(':')[0]+':'+ts.split(':')[1];
				
				if (item.s == 2) {
					$("#chatbox_"+chatboxtitle+" .chatboxcontent").append('<div class="chatboxmessage"><span class="chatboxinfo">'+item.m+'</span></div>');
				} else {
					if(item.f!=from){
						$("#chatbox_"+chatboxtitle+" .chatboxcontent").append('<div class="chatboxmessage sent"><!-- <span style="float:right !important" class="chatboxmessagefrom">:&nbsp;&nbsp;'+item.d+'</span> --><span  class="chatboxmessagecontent">'+item.m+'</span><div class="timestamp" title="'+ts+'">'+ta.format(ts)+'</div></div>');
					} else {
						$("#chatbox_"+chatboxtitle+" .chatboxcontent").append('<div class="chatboxmessage recived" ><!-- <span class="chatboxmessagefrom">'+item.d+':&nbsp;&nbsp;</span> --><span class="chatboxmessagecontent">'+item.m+'</span><div class="timestamp" title="'+ts+'">'+ta.format(ts)+'</div></div>');
					}
					
				}

				$("#chatbox_"+chatboxtitle+" .chatboxcontent").scrollTop($("#chatbox_"+chatboxtitle+" .chatboxcontent")[0].scrollHeight);
				itemsfound += 1;
			}
		});

		//openChatCount++;

/*		if (itemsfound > 0) {
			openChatTime = minopenChat;
			openChatCount = 1;
		} else if (openChatCount >= 10) {
			openChatTime *= 2;
			openChatCount = 1;
			if (openChatTime > maxopenChat) {
				openChatTime = maxopenChat;
			}
		}
*/
		
		//setTimeout('chatHeartbeat();',chatHeartbeatTime);

		SaveDataToLocalStorage(from);

			
	}});
}

function SaveDataToLocalStorage(data)
{
	if (localStorage.getItem("openedChats") === null) {
	  var a = [];
	  localStorage.setItem('openedChats', JSON.stringify(a));
	}
    var a = [];
    a = JSON.parse(localStorage.getItem('openedChats'));
    if(!a.includes(data) && data!=null){
    	a.push(data);
    	localStorage.setItem('openedChats', JSON.stringify(a));
	}
}

function DeleteDataFromLocalStorage(data){
	var a=JSON.parse(localStorage.getItem("openedChats"));
	for(i=0;i<a.length;i++){
		if(a[i]==data){
			a.splice(i,1);
			localStorage.setItem('openedChats', JSON.stringify(a));
		}
	}
}

function OpenLastChats(){
	if(localStorage.getItem("openedChats")==null){return;}
	var chats=JSON.parse(localStorage.getItem("openedChats"));
	for(i=0;i<chats.length;i++){
		chatWith(chats[i],GetUserFullName(chats[i]));
		toggleChatBoxGrowth(chats[i]);
	}
}

function GetUserFullName(id){
	$.ajax({
        type: "POST",
        data: { id: id},
         dataType: 'text',
        url: "<?php base(); ?>chat.php?action=getName&",
        success: function(data){
			$("#chatbox_"+id+" .chatboxtitle").text(data);
        }
    });
}
OpenLastChats();
