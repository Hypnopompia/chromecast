<?php
	require_once("config.php");
?>
<html data-cast-api-enabled="true">
	<head>
		<title>Youtube Chromecast</title>
		<link rel="stylesheet" type="text/css" href="/css/sender.css" />
		<style type="text/css">
			input {
				width: 400px;
			}
		</style>
	</head>
	<body>
		<div class="receiver-div">
			<h3>1) Enter a youtube url</h3>
			<input id="youtubeUrl" value="http://www.youtube.com/watch?v=VioPM4C-vdA" />
			<h3>2) Choose a Chromecast</h3>
			<ul class="receiver-list">
				<li>Looking for Chromecast Receivers...</li>
			</ul>
		</div>
		<button class="kill" disabled>Stop</button><br/><br/>
		<div id="log">
			
		</div>
	</body>

	<script src="http://code.jquery.com/jquery-2.0.3.min.js"></script>
	<script src="http://underscorejs.org/underscore-min.js"></script>

	<script>
		var cast_api,
			cv_activity,
			receiverList,
			$killSwitch = $('.kill');

		function youtubeParser(url){
			var regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=)([^#\&\?]*).*/;
			var match = url.match(regExp);
			if (match&&match[2].length==11){
				return match[2];
			}else{
				//error
			}
		}

		window.addEventListener('message', function(event) {
			if (event.source === window && event.data &&
					event.data.source === 'CastApi' &&
					event.data.event === 'Hello') {
				initializeApi();
			}
		});

		initializeApi = function() {
			if (!cast_api) {
				cast_api = new cast.Api();
				cast_api.addReceiverListener('YouTube', onReceiverList);
			}
		};

		onReceiverList = function(list) {
			if (list.length > 0) {
				receiverList = list;
				$('.receiver-list').empty();
				receiverList.forEach(function(receiver) {
					$listItem = $('<li><a href="#" data-id="' + receiver.id + '">' + receiver.name + '</a></li>');
					$listItem.on('click', receiverClicked);
					$('.receiver-list').append($listItem);
				});
			}
		};

		receiverClicked = function(e) {
			e.preventDefault();

			var $target = $(e.target),
				receiver = _.find(receiverList, function(receiver) {
					return receiver.id === $target.data('id');
				});

			doLaunch(receiver);
		};

		doLaunch = function(receiver) {
			if (!cv_activity) {
				var request = new cast.LaunchRequest('YouTube', receiver);
				request.parameters = "v=" + youtubeParser( $('#youtubeUrl').val() );
				$("#log").append('<p>Playing ' + request.parameters + '</p>');

				$killSwitch.prop('disabled', false);

				cast_api.launch(request, onLaunch);
			}
		};

		onLaunch = function(activity) {
			if (activity.status === 'running') {
				$("#log").append('<p>App Launched!</p>');
				cv_activity = activity;
			}
		};

		$killSwitch.on('click', function() {
			cast_api.stopActivity(cv_activity.activityId, function(){
				cv_activity = null;
			
				$killSwitch.prop('disabled', true);
			});
		});
	</script>
</html>
