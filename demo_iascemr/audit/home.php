<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<LINK HREF="css/style_surgery.css" TYPE="text/css" REL="stylesheet">
	<link rel="stylesheet" href="css/form.css" type="text/css" />
	<link rel="stylesheet" href="css/theme.css" type="text/css" />
	<link rel="stylesheet" href="css/sfdc_header.css" type="text/css" />
	<link rel="stylesheet" href="css/simpletree.css" type="text/css" />
	<script type="text/javascript" src="js/wufoo.js"></script>
	<script type="text/javascript" src="js/mootools.v1.11.js"></script>
	<script type="text/javascript" src="js/moocheck.js"></script>
	<script type="text/javascript" src="js/jsFunction.js"></script>
	<script type="text/javascript" src="js/cur_timedate.js"></script>
	<script type="text/javascript" src="js/simpletreemenu.js"></script>
<title>welcome to Imedic ware</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script>
	function openTab(id)
	{
		var objFrame = document.getElementById("iframeHome");		
		if(id=="Admin")
		{
			objFrame.src = "admin/index.php";	
			document.getElementById("adminTab").className="link_slid";
			document.getElementById("auditTab").className="link_a";
			document.getElementById("reportsTab").className="link_a";
		}
		else if(id=="Audit")
		{
			objFrame.src = "auditing/audit.php";			
		}
		else if(id=="Reports")
		{
			objFrame.src = "report.php";
			document.getElementById("adminTab").className="link_a";
			document.getElementById("auditTab").className="link_a";
			document.getElementById("reportsTab").className="link_slid";
		}
		else
		{
			objFrame.location.href = "homepage.html";
		}
	}
</script>
</head>
<body onload="startTime()">	

<table align="center" bgcolor="#ECF1EA"  width="1000" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td width="5" valign="top"><img src="images/top_left.jpg" width="5" height="43" hspace="0" vspace="0" border="0"></td>
		<td width="985" valign="top" class="top_bg">
		<form name="form1" method="post" action="" style="margin:0px; ">
			<table cellpadding="0" cellspacing="0" align="center" width="100%">
				<tr>
					<td width="18%"  align="left" valign="top"><img src="images/logo_top.jpg" width="173" height="33" hspace="0" vspace="0" border="0"></td>
					<td width="4%"  align="right" valign="top"><img src="images/man.jpg" width="29" height="38"></td>
					<td width="10%" align="left" valign="middle" class="red_txt"><img src="images/tpixel.gif" width="10" height="8">Arun Kapur</td>
					<td width="26%" valign="top">
						<div><img src="images/tpixel.gif" height="10"></div>
						<table cellpadding="0" cellspacing="0" width="99%">
							<tr>
								<td width="20%" valign="top">
									<table cellpadding="0" cellspacing="0" align="center" width="99%">
										<tr>
											<td width="3" valign="top"  align="right"><img src="images/bg_tableft.jpg" width="3" height="30" hspace="0" vspace="0" border="0"></td>
											<td nowrap valign="middle" class="tab_bg text_10b" style="color:#FFFFFF;"><a href="#" onclick="openTab('Admin')" class="link_a" id="adminTab">Admin</a></td>
											<td width="3" valign="middle" class="red_txt"><img src="images/bg_tabright.jpg" width="3" height="30"></td>
			
										</tr>
								  </table>
								</td>
								<td width="20%" valign="top">
									<table cellpadding="0" cellspacing="0" align="center" width="99%">
										<tr>
											<td width="3" valign="top"  align="right"><img src="images/bg_tableft.jpg" width="3" height="30" hspace="0" vspace="0" border="0"></td>
											<td  valign="middle" nowrap class="tab_bg text_10b" style="color:#FFFFFF; "><a href="#" onclick="openTab('Audit')" class="link_a" id="auditTab">Audit</a></td>
											<td width="3" valign="middle" class="red_txt"><img src="images/bg_tabright.jpg" width="3" height="30"></td>
			
										</tr>
								  </table>
								</td>
								<td width="22%" valign="top">
									<table cellpadding="0" cellspacing="0" align="center" width="99%">
										<tr>
											<td width="3" valign="top"  align="right"><img src="images/bg_tableft.jpg" width="3" height="30" hspace="0" vspace="0" border="0"></td>
											<td valign="middle" nowrap class="tab_bg text_10b" style="color:#FFFFFF; "><a href="#"  onclick="openTab('Reports')" class="link_a" id="reportsTab">Reports</a></td>
											<td width="3" valign="middle" class="red_txt"><img src="images/bg_tabright.jpg" width="3" height="30"></td>
			
										</tr>
								  </table>
								</td>
								
							</tr>
					  </table>
					</td>
				  <td width="19%" align="left" valign="middle" nowrap><img src="images/tpixel.gif" width="5" height="1"><input type="text" name="textfield2"  style="border:1px solid #BBBBBB;  width:120px; font-size:12px;"><img src="images/tpixel.gif" width="1" height="1">
				  <!-- <input name="" type="button" class="button" value="Search" style="font-size:11px; font-weight:bold; border:1px solid #CC4E05; background-color:#F1F1F1; color:#CC4E05; white-space:nowrap; "> --><a href="mainpage.php" class="link_top"  style=" font-weight:bold; ">Search</a><img src="images/tpixel.gif" width="8" height="8"></td>
					<td width="14%" align="left"  valign="middle" nowrap><div id="dt_tm" style="font-weight:normal; "></div></td>
					<td width="3%" align="right" valign="middle" nowrap><a href="index.php" class="link_top" title="Logout" style="font-weight:bold; "><img src="images/logout.jpg" width="25" height="23" border="0"></a></td>
					<td width="6%" align="left" valign="middle" nowrap><a href="index.php" class="link_top" title="Logout" style="font-weight:bold; color:#cc0000; "><img src="images/tpixel.gif" width="2" border="0"> Log Out</a></td>
				</tr>
		  </table>
		  </form>
	  </td>
		<td width="5" valign="top"><img src="images/top_right.jpg" width="5" height="43" hspace="0" vspace="0" border="0"></td>
	</tr>
	<tr><td colspan="3"><img src="images/tpixel.gif" width="1" height="3"></td>
	</tr>
	<tr>
		<td colspan="3" valign="top" bgcolor="red" height="606"> 
			<!--<iframe name="iframeHome" src="homepage.html" width="100%" height="630" frameborder="0" scrolling="no"></iframe>-->
              <iframe name="iframeHome" src="report.php" width="100%" height="630" frameborder="0" scrolling="no"></iframe>		 
		 </td>
	</tr>			
</table>
</body>
</html>
