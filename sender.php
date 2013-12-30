<?php
	require_once("config.php");
?>
<html data-cast-api-enabled="true">
	<head>
		<title>Hello World Chrome Sender</title>
		<link rel="stylesheet" type="text/css" href="/css/sender.css" />
	</head>
	<body>
		<div class="receiver-div">
			<h3>Choose a Receiver</h3>
			<ul class="receiver-list">
				<li>Looking for receivers...</li>
			</ul>
		</div>
		<button class="kill" disabled>Kill the Connection</button><br/><br/>
		<input id="msg" type="text" />
		<button class="sendMsg">Send Message</button>
	</body>

	<script src="http://code.jquery.com/jquery-2.0.3.min.js"></script>
	<script src="http://underscorejs.org/underscore-min.js"></script>

	<script>
		var cast_api,
			cv_activity,
			receiverList,
			$killSwitch = $('.kill');

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
				cast_api.addReceiverListener('<?=$appId?>', onReceiverList);
			}
		};

		onReceiverList = function(list) {
			console.log(list);
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
				var request = new cast.LaunchRequest('<?=$appId?>', receiver);

				$killSwitch.prop('disabled', false);

				cast_api.launch(request, onLaunch);
			}
		};

		onLaunch = function(activity) {
			if (activity.status === 'running') {
				console.log('App Launched!');
				cv_activity = activity;
				
				cast_api.sendMessage(cv_activity.activityId, '<?=$namespace?>', {type: 'HelloWorld'});
				console.log('message sent');
			}
		};

		$('.sendMsg').on('click', function(){
			cast_api.sendMessage(cv_activity.activityId, '<?=$namespace?>', { type: $('#msg').val() });
			$('#msg').val('');
		});

		$killSwitch.on('click', function() {
			cast_api.stopActivity(cv_activity.activityId, function(){
				cv_activity = null;
			
				$killSwitch.prop('disabled', true);
			});
		});
	</script>
</html>
