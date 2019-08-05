<?php
/***
 *	Functions for performing user authorisation
 *  Only users from array with Users database supported
 *	New users creation is not available
 *	New passwords if changes, are stored in cookie variable 'newPassword_'.$user
 *	Uses 'login' & 'password' cookie variables to save that datas
 *	Uses 'user' session variable to identify current login user
 */
/*	Users database with default passwords*/
$userDB = array( 
'admin' =>  '481b2e4f7a9e8dd3972408051d7b5d44',
'root'  =>  '49e27e090bf5ef2fd274a4350b7cda29'
);
/*	Magic string */
define('SALT', 'As913yr-1u3 -ru1 mr=1r=1 m=9r814'); 
/***
 * Password encoder
 */
function encodePassword( $password ){ return md5( $password.SALT ); }
/***
 *  Tests the 'user' session variable. If is set, returns true.
 *	Otherwise looks for cookies with Login name and Password.
 *	If there files not exist, returns false.
 *  Otherwise compares  username/passwords from cookies with ones in user database (array of users)
 *  if matched, sets the 'user' session variable and returns true
 */
function authorizeFromCookie()
{	
global $userDB; 
	if( isset( $_SESSION['user'] ) && ( FALSE !== getUserPassword( $_SESSION['user'] ) ) ) return TRUE;
	if( isset( $_COOKIE['login'] ) && isset( $_COOKIE['password'] ) && isset( $userDB[$_COOKIE['login']] ) ) 
	{
		if( getUserPassword($_COOKIE['login']) != $_COOKIE['password'] ) return false;
		$_SESSION['user'] = $_COOKIE['login'];
		return TRUE;
	}
	return FALSE;
}
/***
 * Returns userstring with encoded user password
 * Returns FALSE if user not defined in user's DB
 */
function getUserPassword($user)
{
global $userDB;
	if( !isset( $userDB[$user] ) )return FALSE;
	if( FALSE !== ( $userdbFileH = @fopen( 'forsave/newPassword_'.$user, "r" ) ) )
		if( FALSE !== ( $psw = fgets( $userdbFileH ) ) ) 
	{ 
		fclose( $userdbFileH );
		return  $psw;
	}
	return $userDB[ $user ];
}
/***
 * Tests if logged in
 */
function isAuthorized()
{
global $userDB;
	if( isset( $_SESSION['user'] ) && isset( $userDB[$_SESSION['user']] ) )  return TRUE;
	return FALSE;
}
/***
 * Saves user new password in a local file
 */
function changePassword( $user, $newpassword )
{
global $userDB;
	if( FALSE === ( $userdbFileH = @fopen( 'forsave/newPassword_'.$user, "w" ) ) )return FALSE;
	if( FALSE == fputs( $userdbFileH, encodePassword( $newpassword ) ) ) return FALSE;
	$userDB[$user] = encodePassword( $newpassword );
	//print $user."=".$newpassword."=".$passwords[$user];
	fclose( $userdbFileH );
	return TRUE;
}
/***
 * Deletes new user password saved previously in cookie that never expires
 */
function restoreDefaultPassword( $user )
{
	setcookie('newPassword_'.$user, '', time() - 3600 * 24 * 365, '/');
}


?>