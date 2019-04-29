<a href="<?php echo base_url().'login/line'?>">
	<img id="line_login_button" src="<?php echo base_url().'assets/image/btn_login_base.png'?>" alt="Line login" />
</a>
<hr>
<a href="<?php echo $get_login_url; ?>">
Google</a>
<hr>
<button id="button1">Button1</button>
<button id="button2">Button2</button>
<hr>
<!-- <div id="button3" class="g-signin2" data-onsuccess="onSignIn" data-theme="light"></div> -->
<button id="button3" class="g-signin2">Button2</button>
<script>
$( document ).ready(function() {
	$("#button1").click(function () {
		alert("1")
		update()
	});

	$("#button2").click(function () {
		alert("2")
		update()
	});

});

	$("#button3").click(function () {
		alert("3")
	});

	function update() {
		alert("0")
		$( "#button3" ).trigger( "click" );
	}

	function onSignIn(googleUser) {
		// Useful data for your client-side scripts:
		var profile = googleUser.getBasicProfile();
		console.log("ID: " + profile.getId()); // Don't send this directly to your server!
		console.log('Full Name: ' + profile.getName());
		console.log('Given Name: ' + profile.getGivenName());
		console.log('Family Name: ' + profile.getFamilyName());
		console.log("Image URL: " + profile.getImageUrl());
		console.log("Email: " + profile.getEmail());

		// The ID token you need to pass to your backend:
		var id_token = googleUser.getAuthResponse().id_token;
		console.log("ID Token: " + id_token);
	}

</script>
