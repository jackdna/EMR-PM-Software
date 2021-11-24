<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.

?><?php
include_once("../globalsSurgeryCenter.php");
include_once("logout.php");
include("adminLinkfile.php");
$updatePassword = $_REQUEST['updatePassword'];
?>
<html>
<head>
<title>User Registration</title>
<style>
	form {margin:0px}	
</style>
<script>
var LDL	=	function()
{
		var H	=	$(window).height(); 		//parent.top.$("#div_middle").height() - top.frames[0].$("#div_innr_btn").outerHeight();
		//console.log('User Frame Height is ' + H)
		$("#userFrame").attr('height', H );
}
$(window).load(function()	{ LDL(); });
$(window).resize(function(e) { LDL(); });

</script>
</head>
<body>
	<table cellpadding="0" cellspacing="0"  border="0" width="100%" align="center">
		<tr height="3"><td></td></tr>
		<tr >
			<td valign="top" align="center" >
					
                    <div id="tdFrameUserRegistration" style="width:100%" class="row padding_o clear"> 
							<iframe id="userFrame" name="iFrameUserRegistration" width="100%" scrolling="no" frameborder="0" align="top" src="listUsers.php?updatePassword=<?php echo $updatePassword; ?>"></iframe>
				</div>
				
			</td>
					
		</tr>
	</table>
<form name="frmGetUserInfo" action="" method="post">
	<input type="hidden" name="elem_frmAction" value="Get User Info">
	<input type="hidden" name="elem_usersId" value="">
	<input type="hidden" name="elem_mode" value="4">
</form>
</body>
</html>