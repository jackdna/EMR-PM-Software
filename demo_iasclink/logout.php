<?php
// Under MIT License
// Use, Modify, Distribute under MIT License.
// MIT License 2019
?>
<?php
if(!$_SESSION['iolink_loginUserId']){
	session_destroy();
	header('location:index.php');
}

?>