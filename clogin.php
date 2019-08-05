<?php
/********************************************************
 * 170404, vpr
 *
 * Project: 	any
 * Platform: 	any
 * Subject: 	handler for login/logout requests
 * Remarks:		Handles GET-request href="clogin.php?logout" for logout
 *				Handles POST-request with 'user' $ 'password' parameters for login
 *				Redirects users successfully logged in    to 'vmain.php'
 *				Redirects  users that failed to logged in to 'index.php'
 *				Uses $_SESSION['user'] variable to know if the user logged in
 *				Uses $userDB array as users DB
 */
?>
<?php
include 'libauth.php';
session_start();
//--------Logout GET-handler. Should be placed first
if( isset( $_GET['logout'] ) ) { //Exiting
	unset( $_SESSION['user'] );
	setcookie('login', "", time() - 3600, '/');
	setcookie('password', "", time() - 3600, '/');
	header('Location: index.php');
	exit();
}
//--------Login POST-handler
if( !isAuthorized() ) 
{
	if( !empty( $_POST['login'] ) && !empty( $_POST['password'] ) ) 
	{   
		if( getUserPassword( $_POST['login'] ) == encodePassword( $_POST['password'] ) ) 
		{
			$_SESSION['user'] = $_POST['login'];
			if(isset($_POST['remember'])) {
				setcookie('login', $_POST['login'], time() + 60*30, '/');
				setcookie('password', encodePassword( $_POST['password'] ), time() + 60*30, '/');
			}
			header('Location: vmain.php');
			exit();
		}
	}
	$_SESSION['badusrmessage'] = 'Bad user name or password';
	header('Location: index.php');
	exit();
}
else
{   
	header('Location: vmain.php');
	exit();
}
?>