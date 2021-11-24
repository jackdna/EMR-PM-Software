<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<html>
<head>
	<title>Interface</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
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
</head>
<body topmargin="0" leftmargin="0" rightmargin="0" onload="startTime()">
<div style="position:static;width:100%;overflow:hidden; ">
<?php
	//SLIDER TOP POSITION $top = 153;	
$top = 152;	
$left = -185;	//SLIDER LET POSITION
$width = 175;	//SLIDER Width
$right = -185;	//SLIDER LET POSITION

//------------------------------	LEFT SLIDER	------------------------------
$menuListArr = array('Consent Form', 'Pre-Op Health', 'Nursing Record', 'Physician Orders', 'Anesthesia', 'Operating Room', 'Surgical', 'Quality Assurance', 'Discharge Summary', 'Post Op Inst. Sheet', 'Physician Notes');
$subMenuListArr[0] = array('Surgery', 'HIPAA', 'Assign Benefits', 'Insurance Card');
$subMenuListArr[1] = array('Health Questionnaire');
$subMenuListArr[2] = array('Pre-Op', 'Post-Op');
$subMenuListArr[3] = array('Pre-Op ', 'Post-Op ');
$subMenuListArr[4]  = array('MAC/Regional', 'Pre-Op General', 'General');
$subMenuListArr[5]  = array('Intra-Op Record');
$subMenuListArr[6]  = array('Operative Record');
$subMenuListArr[7]  = array('QA Check List');
$subMenuListArr[8]  = array('Discharge Summary');
$subMenuListArr[9]  = array('Instruction Sheet');
$subMenuListArr[10]  = array('Amendments');

$sliderBar = "sliderBarLEFT";
$image = "imageLeft";
$leftCounter = count($subMenuListArr[0]);
include('slider2.php');
//------------------------------	LEFT SLIDER	------------------------------

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

//------------------------------	RIGHT SLIDER	------------------------------
$image = "imageRight";
$menuListArr = array('Amendments', 'Pre-Op Health', 'Nursing Record', 'Physician Order', 'Anesthesia', 'Operating Room', 'Surgical', 'Discharge Summary', 'Post-Op Inst. Sheet', 'Consent Forms');
$subMenuListArr[0] = array('Amendment');
$subMenuListArr[1] = array('Questionnaire');
$subMenuListArr[2] = array('Pre-OP', 'Post-Op');
$subMenuListArr[3] = array('Pre-OP', 'Post-Op');
$subMenuListArr[4]  = array('MAC/Regional');
$subMenuListArr[5]  = array('Intra-Op Record');
$subMenuListArr[6]  = array('Cataract');
$subMenuListArr[7]  = array('Discharge Summary Sheet');
$subMenuListArr[8]  = array('Instruction sheet');
$subMenuListArr[9]  = array('Surgery', 'HIPAA', 'Assign Benefits');

$sliderBar = "sliderBarRight";
$rightCounter = count($subMenuListArr[0]);
include('slider.php');
//------------------------------	RIGHT SLIDER	------------------------------
?>


 <table align="center" bgcolor="#ECF1EA"  width="1005"  border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td valign="top">
			<table cellpadding="0" cellspacing="0" align="center" width="99%">
				<tr>
					
					<td height="32"   valign="top" background="images/top_bg.jpg" bgcolor="#D1E0C9"   class="text_10b">
						<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
							<tr align="left"  class="text_10b">
								<td width="37%" valign="middle" ><img src="images/tpixel.gif" width="10" height="1"><span class="text_10" style="color:#FFFFFF; font-weight:normal; ">Logged in </span> <span style="color:#FFFFFF; ">Lou Sheffler</span></td>
								<td width="33%" valign="top"><a href="#"><img src="images/top_headding.jpg" width="253" height="26" border="0"></a></td>
							  <td width="23%" align="right" valign="middle"><div id="dt_tm" style="color:#FFFFFF; font-weight:normal; "></div></td>
								<td width="7%" align="center" valign="middle"><a href="#" class="text_10b" style="text-decoration:none; "><img src="images/help.jpg" width="50" height="22" border="0"></a></td>
						  </tr>
					  </table>
				  </td>
				</tr>
				<!-- <tr>
					<td><img src="images/tpixel.gif" width="1" height="2"></td>
				</tr> -->
				<tr>
				  <td bgcolor="#FFFFFF"  class="all_border all_pad" style=" padding-top:3px; padding-bottom:3px; border-top-style:none; ">
				  		<table cellpadding="0" cellspacing="0"  width="100%">
							<tr>
								<td width="25%" valign="top"  style="border-right:1px solid #ffffff; ">
									<table width="100%" align="center" cellpadding="0" cellspacing="0">
										<tr bgcolor="#F1F4F0">
											<td height="20" class="text_10b" nowrap>Patient Name</td>
											<td class="text_10">William R Duphorn</td>
										</tr>
										<tr valign="top" bgcolor="#FFFFFF" class="pad_top_bottom2px">
											<td width="37%" height="20" class="text_10b">Address</td>
										  <td width="63%" align="left"  class="text_10">22 Plainfield Ave.</td>
									  </tr>
									  <tr valign="top" bgcolor="#F1F4F0" class="pad_top_bottom2px">
											<td height="20" class="text_10b">&nbsp;</td>
											<td align="left" class="text_10">Plainfield, NJ 08757</td>
									  </tr>
								  </table>
							  </td>
								<td width="30%" valign="top" bgcolor="#F1F4F0" style="border-right:1px solid #ffffff; ">
									<table width="100%" align="center" cellpadding="0" cellspacing="0">
										<tr>
											<td height="20" colspan="2" bgcolor="#F1F4F0" class="text_10b">ASC<span class="text_10"><img src="images/tpixel.gif" width="10">9998499854</span></td>
										</tr>
										<tr bgcolor="#FFFFFF">
											<td height="20" colspan="2" bgcolor="#FFFFFF" class="text_10b" >DOB<span class="text_10"><img src="images/tpixel.gif" width="10">12-13-1930</span><img src="images/tpixel.gif" width="50" height="8">Age<span class="text_10"><img src="images/tpixel.gif" width="10">78 yrs</span> </td>
										</tr>
										<tr>
											<td height="20" colspan="2" bgcolor="#F1F4F0" class="text_10b">Tel.<span class="text_10"><img src="images/tpixel.gif" width="10">(732) 555-1234</span></td>
										</tr>
								  </table> 
							  </td>
								<td width="20%" valign="top" bgcolor="#F1F4F0" style="border-right:1px solid #ffffff; ">
							   	<table width="100%" align="center" cellpadding="0" cellspacing="0">
										
										<tr bgcolor="#F1F4F0">
											<td width="44%" height="20" align="left" class="text_10b" nowrap>Surgery Date<span class="text_10"><img src="images/tpixel.gif" width="10"></span></td>
											<td width="56%" align="left" class="text_10">10-17-2007</td>
								  </tr>
										<tr bgcolor="#FFFFFF">
											<td height="20" align="left" class="text_10b">Sex<span class="text_10"><img src="images/tpixel.gif" width="10"></span></td>
											<td align="left" class="text_10">Male</td>
								  </tr>
										
										<tr bgcolor="#F1F4F0">
											<td height="20" class="text_10b">Allergies</td>
											<td height="20" align="left" valign="middle" class="text_10"><img src="images/Interface_clip_image001.gif" width="17" height="15" align="middle"></td>
								  </tr>
								 </table>
								<td width="23%" valign="top" bgcolor="#F1F4F0">
									<table width="100%" align="center" cellpadding="0" cellspacing="0">
										<tr bgcolor="#FFFFFF">
											<td height="20" colspan="2" bgcolor="#F1F4F0" class="text_10b">Surgeon<span class="text_10"><img src="images/tpixel.gif" width="10">Brain Harvey</span></td>
										</tr>
										<tr bgcolor="#F1F4F0">
											<td height="20" colspan="2" bgcolor="#FFFFFF" class="text_10b">Anesthesiologist<span class="text_10"><img src="images/tpixel.gif" width="10">Brian Rogers</span></td>
										</tr>
										
										<tr bgcolor="#F1F4F0">
											<td width="88%" height="20" nowrap class="text_10b">Assisted by Translator</td>
										  <td width="12%" align="left" class="text_10"><input class="checkbox"  type="checkbox" value="Yes"  tabindex="7" ></td>
										</tr>
								  </table>
							  </td>
							  <td width="2%" valign="top">
							  	<table width="100%" cellpadding="0" cellspacing="0">
									<tr>
										<td><a href="home.php"><img src="images/close.jpg" alt="Close" width="19" height="19" border="0"></a></td>
									</tr>
									<tr>
										<td height="21" bgcolor="#FFFFFF">&nbsp;</td>
									</tr>
									<tr>
										<td height="20" bgcolor="#F1F4F0">&nbsp;</td>
									</tr>
									
								</table>
							  </td>
							</tr>
							<tr>
								<td colspan="5" bgcolor="#F1F4F0">
									<table width="100%" height="25" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#F1F4F0" >
										<tr valign="middle" bgcolor="#FFFFFF" >
											<td width="25%" align="left" nowrap class="text_10b1" >Site<span class="text_10"><img src="images/tpixel.gif" width="10">Left Eye</span></td>
											<td width="30%" align="left" nowrap class="text_10b1" >Primary Proc.<span class="text_10"> Cataract Extension W/IOL</span></td>
											<td width="22%" align="left" nowrap class="text_10b1" >Secondary Proc.<span class="text_10"><img src="images/tpixel.gif" width="10">N/A</span></td>
										  <td width="22%" height="22" align="center" nowrap class="text_10b1" ><input type="button" class="button" style=" font-weight:bold ; font-size:11px; color:#336699; border:1px solid #336699;   background-color:#DFF4FF; " value="Progress Notes"></td>
										  <td width="1%">&nbsp;</td>
								      </tr>
						      	</table>
							  </td>
							</tr>
					</table>
				  </td>
			  </tr>
		  </table>
		</td>
	</tr>
	<tr>
		<td ><img src="images/tpixel.gif" width="1" height="2"></td>
	</tr>
	<tr>
		<td valign="top"  >
		
			<table width="99%" align="center" height="25" border="0" cellpadding="0" cellspacing="0" bgcolor="#B3DD8C" class="all_border" style="border-color:#993300; ">
				<tr bgcolor="#F1F4F0" >
					<td width="26%" align="center" bgcolor="#F5F5F5" class="text_10b1" style="border-right:1px solid #993300; color:#993300; "  ><img src="images/tpixel.gif" width="10">Base Line Vital Signs</td>
					<td width="23%" align="left" bgcolor="#FFFFFF" class="text_10b1" style="border-right:1px solid #993300; "  ><img src="images/tpixel.gif" width="10">B/P<img src="images/tpixel.gif" width="10"><span class="text_10 red_txt">120/80</span></td>
					<td width="16%" align="left" bgcolor="#FFFFFF" class="text_10b1" style="border-right:1px solid #993300; " ><img src="images/tpixel.gif" width="10">P<img src="images/tpixel.gif" width="10"><span class="text_10 red_txt">65</span></td>
					<td width="13%" align="left" bgcolor="#FFFFFF" class="text_10b1" style="border-right:1px solid #993300; " ><img src="images/tpixel.gif" width="10">R<img src="images/tpixel.gif" width="10"><span class="text_10 red_txt">45</span></td>
					<td width="22%" align="left" bgcolor="#FFFFFF" class="text_10b1"  ><img src="images/tpixel.gif" width="10">Temp<img src="images/tpixel.gif" width="10"><span class="text_10 red_txt">98.6 F</span></td>
		      </tr>
	 	  </table>
		</td>
	</tr>