<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
if(!$_SESSION['loginUserId']){
	session_destroy();
	header('location:index.php');
}

?>