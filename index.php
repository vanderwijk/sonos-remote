<html>
<head>
	<title>Hue</title>
	<script src="https://code.jquery.com/jquery-3.1.0.min.js" type="text/javascript" charset="utf-8"></script>

	<script type="text/javascript" charset="utf-8">
	function addmsg( type, lastupdated, buttonevent ) {

		$( "#messages" ).html(
			"<div class='msg " + type + "'>Last update:<span class='lastupdated'>" + lastupdated + "</span> Button event:" + buttonevent + "</div>"
		);

		if ($('#timestampfield').val() != lastupdated) {
			if ( buttonevent == 1002 ) {
				console.log ( 'Play' );
				$.ajax({
					url: 'sonos/index.php?command=play',
					success: function(data){}
				})
			} else if ( buttonevent == 4002 ) {
				console.log ( 'Stop' );
				$.ajax({
					url: 'sonos/index.php?command=stop',
					success: function(data){}
				})
			} else if ( buttonevent == 2002 ) {
				console.log ( 'Up' );
				$.ajax({
					url: 'sonos/index.php?command=up',
					success: function(data){}
				})
			} else if ( buttonevent == 3002 ) {
				console.log ( 'Down' );
				$.ajax({
					url: 'sonos/index.php?command=down',
					success: function(data){}
				})
			}
		}
	}

	function pollHueDimmer() {
		$.ajax({
			type: "GET",
			url: "hue.php",
			async: true,
			cache: false,
			timeout: 50000,
			dataType: 'json',

			success: function( data ) {
				addmsg( "new", data.lastupdated, data.buttonevent );
				$('#timestampfield').val(data.lastupdated);
				setTimeout(
					pollHueDimmer,
					500
				)
			},
			error: function( XMLHttpRequest, textStatus, errorThrown ) {
				addmsg( "error", textStatus + " (" + errorThrown + ")");
				setTimeout(
					pollHueDimmer,
					15000);
			}
		});
	};

	$(document).ready(function(){
		pollHueDimmer();
	});
	</script>

</head>
<body>
	<div id="messages"></div>
	<input type="text" value="" id="timestampfield" >
</body>
</html>
