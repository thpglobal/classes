<?php
namespace Thpglobal;

class App {
  public function login($client_id='NA'){
?>  
<html lang="en">
  <head>
    <meta name="google-signin-scope" content="profile email">
    <meta name="google-signin-client_id" content="<?php echo($client_id)?>">
    <script src="https://apis.google.com/js/platform.js" async defer></script>
  </head>
  <body>
    <div class="g-signin2" data-onsuccess="onSignIn" data-theme="dark"></div>
	<form method=post action=/login2>
	<input type=hidden id=idx name=idtoken>
	</form>
    <script>
      function onSignIn(googleUser) {
        var id_token = googleUser.getAuthResponse().id_token;
	document.getElementById('idx').value=id_token;
	document.forms[0].submit();
      }      
    </script>
  </body>
</html>
<?php } // end of function ?>

