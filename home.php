<?php
session_start();
require_once('settings.php');

$_SESSION['user_id'] = '655577166';
$_SESSION['new_user'] = 1;
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8"> 
<link rel="stylesheet" type="text/css" href="http://fonts.googleapis.com/css?family=Mouse+Memoirs">
<link rel="stylesheet" type="text/css" href="css/home.css" />
<link rel="stylesheet" type="text/css" href="css/font-awesome.css" />
</head>

<body>
<div id="fb-root"></div>
<script type="text/javascript">
var FB_LOADED = 0; 

(function(d){
 var js, id = 'facebook-jssdk'; if (d.getElementById(id)) {return;}
 js = d.createElement('script'); js.id = id; js.async = true;
 js.src = "//connect.facebook.net/en_US/all.js";
 d.getElementsByTagName('head')[0].appendChild(js);
}(document));

window.fbAsyncInit = function() {
	FB_LOADED = 1;

	FB.init({ appId : '<?php echo FACEBOOK_APP_ID; ?>', status : true, cookie : true, xfbml : true });
	FB.Canvas.setAutoGrow();

	FB.XFBML.parse($("#user-name").get(0), function() {
		if(window.GameInfo) {
			var pattern = />.+?<\//g;
            var result = pattern.exec($("#user-name").html());
            var name = result[0].replace(/>|<|\//g, '');
			GameInfo.user_name = name;
		}
	});

	FB.getLoginStatus(function(response) {
		if(response.status == 'connected') {
			Initialize(<?php echo $_SESSION['new_user'];?>);
		} 
	});
};

function Initialize(new_user) {
	if(new_user == 1) {
		FB.api('/me', { fields: 'name,id,email,gender,birthday' }, function(response) { console.log(response);
			if(!response.error) {
				$.ajax({ 
					url: 'controller.php',
					cache: false,
					type: 'POST',
					data: { command: 'SaveUserInformation', name: response.name, id: response.id, email: response.email },
					success: function(response) {
					}
				});
			}
		});
	}
}

</script>

<div id="contents"></div>

<script type="text/template" id="initial-view">
	<div class="header">
		<div id="app-name">Logo Quiz</div>
		<img id="user-image" src="http://graph.facebook.com/<?php echo $_SESSION['user_id']; ?>/picture?type=normal" />
		<div id="user-info">
			<div id="welcome-container">Welcome <span id="user-name"><fb:name linked="false" uid="<?php echo $_SESSION['user_id']; ?>" capitalize="true" useyou="false" /></span> !</div>
			<div id="tokens-container">You have <span id="user-hint-tokens"><%= GameInfo['hint_tokens'] %></span> hint tokens</div>
		</div>
	</div>
	<div id="lightbox-area">
		<div id="play-area"></div>
		<div class="footer">
			<%= GameInfo.background_music == 1 ? '<div id="music-control"><i class="icon-volume-up"></i></div><audio id="background-music" autoplay loop src="background-music/background-music.mp3"></audio>' : '' %>
			<div id="privacy-policy">Privacy Policy</div>
			<div id="reset-game">Reset Game</div>
			<div id="game-rules">Game Rules</div>
		</div>
		<div id="game-lightbox"></div>
	</div>
	<%
	if(FB_LOADED == 1) {
		FB.XFBML.parse($("#user-name").get(0), function() {
			if(window.GameInfo) {
				var pattern = />.+?<\//g;
	            var result = pattern.exec($("#user-name").html());
	            var name = result[0].replace(/>|<|\//g, '');
				GameInfo.user_name = name;
			}
		});
	}
	%>
</script>

<script type="text/javascript">var SHARE_URL = '<?php echo 'https://www.facebook.com/' . FACEBOOK_PAGE_ID . '?sk=app_' . FACEBOOK_APP_ID; ?>';</script>

<script type="text/template" id="levels-view">
	<%
	for(var i=0;i<GameInfo.levels.length;i++) {
		var level_id = GameInfo.levels[i];
		var level_class = '';
		var icon = '';

		var logos_answered = 0;
		if(level_id in GameInfo.user_levels) {
			
			for(var j=0;j<20;j++) {
				if(GameInfo.user_levels[level_id][(j+1)]['answered'] == '1')
					logos_answered++;
			}

			if(logos_answered < 20) {
				level_class = 'progress';
				icon = 'icon-unlock-alt';
			}
				
			else if(logos_answered == 20) {
				level_class = 'full';
				icon = 'icon-ok';
			}
		}
		else {
			level_class = 'locked';
			icon = 'icon-lock';
		}
		print('<div class="can-play level ' + level_class + '" id="level-' + level_id + '">Level ' + (i+1) + '<i class="' + icon + '"></i><div class="level-answered">' + logos_answered + ' / 20</div></div>'); 
	}
	print('<div class="level locked">Coming Soon</div>');
	%>
</script>

<script type="text/template" id="game-rules-view">
	<div id="game-rules-dialog"><% print(GameInfo.game_rules) %></div>
	<i class="icon-remove-sign" id="close-game-rules"></i>
</script>

<script type="text/template" id="reset-game-view">
	<div id="reset-heading">Reset Game ?</div>
	<div id="reset-controls">
		<div id="do-reset">Yes</div>
		<div id="cancel-reset">No</div>
	</div>
	<img src="images/loader.gif" id="reset-loader" />
</script>

<script type="text/template" id="restricted-level-view">
	<div id="restricted-header">To unlock this level you must guess 15 logos from Level <% print(_.keys(GameInfo.user_levels).length) %></div>
	<i class="icon-remove-sign" id="close-restricted-dialog"></i>
</script>

<script type="text/template" id="this-level-view">
	<div id="this-level-header">
		<div id="all-levels-link">All Levels</div>
		<i class="icon-angle-right"></i>
		<div id="this-level-name"><% print('Level ' + (parseInt(GameInfo.levels.indexOf(level_id), 10) + 1)) %></div>
		<div id="this-level-score">
		<% 
		var logos_answered = 0;
		for(var j=0;j<20;j++) {
			if(GameInfo.user_levels[level_id][(j+1)]['answered'] == '1')
				logos_answered++;
		}
		print(logos_answered + ' / 20');
		%>
		</div> 
	</div>
	<div id="this-level-logos">
	<%
	for(var j=0;j<20;j++) {
		var html = '<div class="level-logo"><img src="images/logos/' + level_id + '-' + (j+1) + '.jpg?' + GameInfo.random + '" />';
		
		if(GameInfo.user_levels[level_id][(j+1)]['answered'] == '1')
			html += '<div class="logo-answered"><i class="icon-ok-circle"></i></div>'

		html += '</div>';
		print(html);
	}
	%>
	</div>
</script>

<script type="text/template" id="logo-view">
	<div id="logo-header">
		<div id="all-levels-link">All Levels</div>
		<i class="icon-angle-right"></i>
		<div id="this-level-link" data-level-id="<%= logo_id %>"><% print('Level ' + (parseInt(GameInfo.levels.indexOf(level_id), 10) + 1)) %></div>
		<i class="icon-angle-right"></i>
		<div id="logo-name">Logo <%= logo_id %></div>
	</div>	
	<img src="images/logos/<% print(level_id + '-' + logo_id + '.jpg?' + GameInfo.random) %>" id="logo-picture" />
	<div id="logo-info">
		<% if(GameInfo.user_levels[level_id][logo_id]['answered'] == 0) { %>
		<div id="logo-answer-container">
			<input id="logo-answer" type="text" />
			<i id="correct-or-wrong"></i>
			<div data-pending="0" class="logo-button" id="answer-check">Check</div>
		</div>
		<%
		var html = '<div class="logo-button" id="ask-friends">Ask From Friends</div>';
		html += '<div class="logo-button' + (GameInfo.user_levels[level_id][logo_id]['hint_1'] == 1 ? ' logo-button-inactive' : '' ) + '" id="ask-hint-1">Hint 1</div>';
		html += '<div class="logo-button' + (GameInfo.user_levels[level_id][logo_id]['hint_2'] == 1 ? ' logo-button-inactive' : '' ) + '" id="ask-hint-2">Hint 2</div>';
		print(html);
		%>
		<% } %>
		<div id="logo-description">Description</div>
		<div id="logo-error">Error</div>
		<img id="logo-loader" src="images/loader.gif" />
	</div>
</script>

<script type="text/template" id="level-unlocked-view">
	<div id="level-unlocked-header">Congratulations you have guessed 15 logos from Level <%= _.keys(GameInfo.user_levels).length-1 + ( next_level == -1 ? '' : ' and unlocked Level ' + _.keys(GameInfo.user_levels).length ) %><br />You have also earned 10 hint tokens</div>
	<div id="level-unlocked-controls">
		<div id="level-unlocked-share">Share</div>
		<div id="level-unlocked-cancel">Cancel</div>
	</div>
	<div id="level-unlocked-error"></div>
</script>

<script type="text/template" id="level-completed-view">
	<div id="level-completed-header">Congratulations you guessed all logos from Level <%= level_name %></div>
	<div id="level-completed-controls">
		<div id="level-completed-share">Share</div>
		<div id="level-completed-cancel">Cancel</div>
	</div>
	<div id="level-completed-error"></div>
</script>

<script type="text/javascript" src="js/jquery-2.0.3.min.js"></script>
<script type="text/javascript" src="js/underscore-1.5.1.min.js"></script>
<script type="text/javascript" src="js/backbone-1.0.0.min.js"></script>
<script type="text/javascript" src="js/home.js"></script>

</body>
</html>