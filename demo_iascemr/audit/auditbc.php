<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
include("common/auditLinkfile.php");
?>
<html>
<head>
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
<script>
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
	function closeMe(){
	top.document.getElementById("auditTab").className="link_a";
	top.frames[0].location = '../home_front.php';
}
</script>
</head>
<table width="100%" height="100%" align="center" cellpadding="0" cellspacing="0" bordercolor="#009966" border="0" >
	<tr>
	
		<td width="11%"></td>
		<td width="13%" align="left">
			<table class="text_10b" border="0" align="left" cellpadding="0" cellspacing="0">
				<tr onClick="javascript:frameSrc('login.php');" style="cursor:hand;">
					<td width="3" align="left"><img id="img1Left" src="../images/leftDark.gif" width="3" height="24"></td>
					<td width="130" align="center" valign="middle" bgcolor="#003300" id="Tab1" onClick="return changeMe('Tab1', 'Tab1Link', '1');" class="text_10b_audit"><a id="Tab1Link" href="javascript:frameSrc('login.php');" class="white">Login/Logout</a></td>
					<td align="left" valign="top"><img id="img1Right" src="../images/rightDark.gif" width="3" height="24"></td>
				</tr>
			</table>
		</td>
		<td width="28%"  align="left">
			<table border="0" align="left" cellpadding="0" cellspacing="0">
				<tr onClick="javascript:frameSrc('pass.php');" style="cursor:hand;">
					<td width="6" align="right"><img id="img2Left" src="../images/left.gif" width="3" height="24"></td>
					<td width="460" align="center" valign="middle" bgcolor="#BCD2B0" id="Tab2" onClick="return changeMe('Tab2', 'Tab2Link', '2');" class="text_10b_audit"><a id="Tab2Link" href="javascript:frameSrc('pass.php');" class="black">Password Change/Reset/Lockouts</a></td>
					<td align="left" valign="top"><img id="img2Right" src="../images/right.gif" width="3" height="24"></td>
				</tr>
			</table>
		</td>
		<td width="17%" align="left">
			<table border="0" align="left" cellpadding="0" cellspacing="0">
				<tr onClick="javascript:frameSrc('chart_note.php');" style="cursor:hand;">
					<td width="6" align="right"><img id="img3Left" src="../images/left.gif" width="3" height="24"></td>
					<td width="170" align="center" valign="middle" bgcolor="#BCD2B0" id="Tab3" onClick="return changeMe('Tab3', 'Tab3Link', '3');" class="text_10b_audit"><a id="Tab3Link" href="javascript:frameSrc('chart_note.php');" class="black">Patient Chart Notes</a></td>
					<td align="left" valign="top"><img id="img3Right" src="../images/right.gif" width="3" height="24"></td>
				</tr>
			</table>
		</td>
		<td width="14%" align="left">
			<table border="0" align="left" cellpadding="0" cellspacing="0">
				<tr onClick="javascript:frameSrc('quality.php');" style="cursor:hand;">
					<td width="6" align="right"><img id="img4Left" src="../images/left.gif" width="3" height="24"></td>
					<td width="144" align="center" valign="middle" bgcolor="#BCD2B0" id="Tab4" onClick="return changeMe('Tab4', 'Tab4Link', '4');" class="text_10b_audit"><a id="Tab4Link" href="javascript:frameSrc('qualitybc.php');" class="black">Quality Check</a></td>
					<td align="left" valign="top"><img id="img4Right" src="../images/right.gif" width="3" height="24"></td>
				</tr>
			</table>
		</td>
		<td align="right" width="11%"><img border="0" src="../images/close.jpg" onClick="return closeMe();"></td>

	</tr>			
	<tr height="4" bgcolor="#003300">
		<td colspan="6"></td>
	<tr>
		<td align="center" colspan="6" bgcolor="#EAEAFF">
			<iframe name="iframeaudit" src="login.php" width="100%" height="700" frameborder="0" scrolling="no"></iframe>
		</td>
	</tr>
	
	
</table>


</html>
