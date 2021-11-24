<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
session_start();
$userPrivileges = $_SESSION['userPrivileges'];
?>
<!DOCTYPE html>
<html>
<head>
<title>Audit Tabs</title>
<meta name="viewport" content="width=device-width, maximum-scale=1.0">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<?php include("common/auditLinkfile.php");?>
<style>
	a.black:hover{ color:"Red";	text-decoration:none; }
	a.white { color:#FFFFFF; text-decoration:none; }
	.text_10b_audit
	{
		font-family:verdana;
		font-size:11px;
		font-weight:bold;
	}	
</style>
<script type="text/javascript">
	function frameSrc(source){
		//alert(source)
		top.frames[0].frames[0].location.href = source;				
	}
	function changeMe(tab, tabLink, n){
		for(var i=1; i<=4; i++){
			document.getElementById('Tab'+i).style.background = "#BCD2B0";
			document.getElementById('Tab'+i+'Link').className = "black";
			document.getElementById('img'+i+'Left').src ="../images/left.gif";
			document.getElementById('img'+i+'Right').src ="../images/right.gif";
		}
		document.getElementById(tab).style.background = "#003300";
		document.getElementById(tabLink).className = "white";
		document.getElementById('img'+n+'Left').src ="../images/leftDark.gif";
		document.getElementById('img'+n+'Right').src ="../images/rightDark.gif";
	}	
	function closeAdminMe(){
		top.document.getElementById("auditTab").className="link_a";
		<?php 
		if(($userPrivileges=='Admin') || ($userPrivileges=='Super User')){ 
			?>
			top.frames[0].location = '../home_inner_front.php';
			<?php
		}else{
			?>
			top.location = '../home_inner_front.php';
			<?php
		}
		?>
	}
	function closeMe(){
	//CHANGE TAB COLOR OF ADMIN
	if(top.document.getElementById("auditTab"))	{
		top.document.getElementById("auditTab").className="link_a";
		top.document.getElementById("TDauditTopTab").innerHTML='<img src="images/bg_tableft.jpg" width="3" height="30" hspace="0" vspace="0" border="0">';
		top.document.getElementById("TDauditMiddleTab").style.background ="url(images/bg_tab.jpg)"
		top.document.getElementById("TDauditBottomTab").innerHTML='<img src="images/bg_tabright.jpg" width="3" height="30">';
	}
	//END CHANGE TAB COLOR OF ADMIN
	top.frames[0].location = '../home_inner_front.php';
}

</script>
</head>
<body>
<table class="table_collapse">
	<tr>
		<td style="width:11%;"></td>
		<td style="width:13%;" class="alignLeft">
			<table class="text_10b table_pad_bdr">
				<tr onClick="javascript:frameSrc('login.php');" style="cursor:pointer;">
					<td style="width:3px;" class="alignRight padd0"><img id="img1Left" src="../images/leftDark.gif" style="width:3px; height:24px;"></td>
					<td style="width:130px; background-color:#003300;" class="text_10b_audit alignCenter padd0" id="Tab1" onClick="return changeMe('Tab1', 'Tab1Link', '1');"><a id="Tab1Link" href="javascript:frameSrc('login.php');" class="white">Login/Logout</a></td>
					<td style="width:3px;" class="alignLeft padd0"><img id="img1Right" src="../images/rightDark.gif" style="width:3px; height:24px;"></td>
				</tr>
			</table>
		</td>
		<td style="width:28%;" class="alignLeft">
			<table class="text_10b table_pad_bdr">
				<tr onClick="javascript:frameSrc('pass.php');" style="cursor:pointer;">
					<td style="width:3px;" class="alignRight padd0"><img id="img2Left" src="../images/left.gif" style="width:3px; height:24px;"></td>
					<td style="width:460px; background-color:#BCD2B0;" id="Tab2" onClick="return changeMe('Tab2', 'Tab2Link', '2');" class="padd0 text_10b_audit alignCenter"><a id="Tab2Link" href="javascript:frameSrc('pass.php');" class="black">Password Change/Reset/Lockouts</a></td>
					<td style="width:3px;" class="alignLeft padd0"><img id="img2Right" src="../images/right.gif" style="width:3px; height:24px;"></td>
				</tr>
			</table>
		</td>
		<td style="width:17%;" class="alignLeft">
			<table class="text_10b table_pad_bdr">
				<tr onClick="javascript:frameSrc('chart_note.php');" style="cursor:hand;">
					<td style="width:3px;" class="alignRight padd0"><img id="img3Left" src="../images/left.gif" style="width:3px; height:24px;"></td>
					<td style="width:170px; background-color:#BCD2B0;" id="Tab3" onClick="return changeMe('Tab3', 'Tab3Link', '3');" class="padd0 alignCenter text_10b_audit"><a id="Tab3Link" href="javascript:frameSrc('chart_note.php');" class="black">Patient Chart Notes</a></td>
					<td style="width:3px;" class="alignLeft padd0"><img id="img3Right" src="../images/right.gif" style="width:3px; height:24px;"></td>
				</tr>
			</table>
		</td>
		<td style="width:14%;" class="alignLeft">
			<table class="text_10b table_pad_bdr">
				<tr onClick="javascript:frameSrc('quality.php');" style="cursor:hand;">
					<td style="width:3px;" class="alignRight padd0"><img id="img4Left" src="../images/left.gif" style="width:3px; height:24px;"></td>
					<td style="width:144px; background-color:#BCD2B0;" id="Tab4" onClick="return changeMe('Tab4', 'Tab4Link', '4');" class="padd0 alignCenter text_10b_audit"><a id="Tab4Link" href="javascript:frameSrc('quality.php');" class="black">Quality Check</a></td>
					<td style="width:3px;" class="alignLeft padd0"><img id="img4Right" src="../images/right.gif" style="width:3px; height:24px;"></td>
				</tr>
			</table>
		</td>
		<td style="width:11%;" class="alignRight padd0"><img src="../images/close.jpg" style="cursor:pointer;" onClick="return closeMe();"></td>
	</tr>
	<tr style="height:4px; background-color:#003300;"><td colspan="6"></td></tr>
	<tr>
		<td class="alignCenter padd0" colspan="6" style="background-color:#EAEAFF;">
			<iframe name="iframeaudit" src="login.php" style="width:100%; height:700px;" frameborder="0" scrolling="no"></iframe>
		</td>
	</tr>
</table>
</body>
</html>