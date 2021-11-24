<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php 
session_start();
require_once('common/conDb.php'); 

?>
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, maximum-scale=1.0" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Surgerycenter EMR - Progress Notes</title>
<link rel="stylesheet" href="css/style_surgery.css" type="text/css" >
<style>
	form{margin:0px;}
	a.black:hover{color:"Red";	text-decoration:none;}
	body{
		scrollbar-face-color: #D1E0C9;
		scrollbar-shadow-color: #A4A4A4;
		scrollbar-highlight-color: #808080;
		scrollbar-3dlight-color: #D1E0C9;
		scrollbar-darkshadow-color: #0a7225;
		scrollbar-track-color: #FFFFFF;
		scrollbar-arrow-color: #000000;
	}
	.text_10 { 
		font-family:"verdana"; 
		font-size:14px; 
		color:#000000; 
		font-weight:normal;
	}
	.text_10b { 
		font-family:"verdana"; 
		font-size:14px; 
		color:#000000; 
		font-weight:bold;  
	}
	.blue_txt{
		color:#3232F0;
	}
.style8 {font-size: 16px}
.style9 {font-family: "verdana"; font-size: 16px; color: #000000; font-weight: normal; }
</style>
<?php
$spec = "
</head>
	<body style=\"background-color:#ECF1EA; margin:0px; overflow:hidden;\">
";

include("common/link_new_file.php");
$loginUserId = $_SESSION['loginUserId'];
//$loginUserType = $_SESSION['loginUserType'];
$progrModifyVisi = 'hidden';
if($_SESSION['loginUserType']=='Surgeon' || $_SESSION['loginUserType']=='Anesthesiologist' || $_SESSION['loginUserType']=='Nurse') {  
	$progrModifyVisi = 'visibile';
}

$pConfId = $_REQUEST["pConfId"];
if(!$pConfId) {
	$pConfId = $_SESSION["pConfId"];
}	
$ascID = $_REQUEST['ascId'];
$id_del = $_REQUEST['delid'];  
  if($id_del != '')
	{
		$qry_delete = "delete from tblprogress_report where intProgressID = $id_del";
		$del_Notes = imw_query($qry_delete);
		echo "<script>\n";
		//echo "opener.location.reload();\n";
		echo "</script>";
	}
	


function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
{
  $theValue = (!get_magic_quotes_gpc()) ? addslashes($theValue) : $theValue;

  switch ($theType) {
    case "text":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;    
    case "long":
    case "int":
      $theValue = ($theValue != "") ? intval($theValue) : "NULL";
      break;
    case "double":
      $theValue = ($theValue != "") ? "'" . doubleval($theValue) . "'" : "NULL";
      break;
    case "dated":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;
    case "defined":
      $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
      break;
  }
  return $theValue;
}
//print_r($_REQUEST);
$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1") && $_REQUEST['editid']=='') {
 
 $insertSQL = sprintf("INSERT INTO tblprogress_report (txtNote, usersId, asc_id, dtDateTime, tTime, confirmation_id, userType) VALUES (%s, %s, %s, %s, %s, %s, %s)",
					   GetSQLValueString($_POST['txtNote'], "text"),
                       //GetSQLValueString(nl2br($progressUserId_list), "int"),
					   GetSQLValueString($_POST['hidd_progressUserId'], "int"),
                       GetSQLValueString('0', "text"),
					   GetSQLValueString($_POST['dtDateTime'], "dated"),
                       GetSQLValueString($_POST['tTime'], "text"),
					   GetSQLValueString($_POST['confirmation_id'], "int"),
					   GetSQLValueString($_POST['progress_UserType'], "text"));
					   
					   

  imw_select_db($database_connDb);
  
  $Result1 = imw_query($insertSQL, $link) or die(imw_error());
	
	echo "<script>\n";
	//echo "opener.location.reload();\n";
	echo "</script>";
  //}elseif($_REQUEST['editid']!='' && $_REQUEST['submit']<>""){
  }elseif($_REQUEST['editid']!='' && $_REQUEST['submit_click']<>""){
  
   $updateSQL = "update tblprogress_report set 
   					txtNote = '".addslashes($_POST['txtNote'])."', 
					usersId = '".$_POST['hidd_progressUserId']."',
					userType = '".$_POST['progress_UserType']."',
					dtDateTime = '".$_POST['dtDateTime']."',
					tTime = '".$_POST['tTime']."'
				where intProgressID ='".$_REQUEST['editid']."'";
  imw_query($updateSQL); 
  $_REQUEST['editid']=""; 
  echo "<script>\n";
	//echo "opener.location.reload();\n";
	echo "</script>";
}

$query_rsNotes = "SELECT tblprogress_report.intProgressID, tblprogress_report.txtNote, tblprogress_report.confirmation_id, users.fname, users.mname, users.lname, users.user_type, tblprogress_report.dtDateTime, tblprogress_report.tTime FROM tblprogress_report, users WHERE tblprogress_report.confirmation_id = '".$pConfId."'  AND users.usersId = tblprogress_report.usersId ORDER BY dtDateTime DESC, tTime DESC";
$rsNotes = imw_query($query_rsNotes, $link) or die(imw_error());
//$row_rsNotes = imw_fetch_assoc($rsNotes);
$totalRows_rsNotes = imw_num_rows($rsNotes);
$i=0;
?>
<script language="javascript">
window.focus();

function validate_form ( )
{
    valid = true;

    if ( document.form1.txtNote.value == "" )
    {
        alert ( "Please enter note" );
        valid = false;
    //}else if ( document.form1.hidd_userid.value == "" )
	}else if ( document.form1.hidd_progressUserId.value == "" )
    
	{
        alert ( "Please Select Created By" );
        valid = false;
    }

    //return valid;
	if(valid==false) {
		return false;
	}else {
		document.form1.submit();
		return true;
	}
}

function checkUserType(ProgressUserName) {
	document.form1.hidd_userid.value=""; //SET USERNAME TO BLANK AT INITIAL
	var ProgressUserNameValue = ProgressUserName.value;
	if(ProgressUserNameValue=="Surgeon") { 
		document.getElementById("progress_SurgeonId").style.display="block";
		document.getElementById("progress_AnesthesiologistId").style.display="none";
		document.getElementById("progress_NurseId").style.display="none";
		document.getElementById("progress_BlankId").style.display="none";
		
	}else if(ProgressUserNameValue=="Anesthesiologist")	{
		document.getElementById("progress_SurgeonId").style.display="none";
		document.getElementById("progress_AnesthesiologistId").style.display="block";
		document.getElementById("progress_NurseId").style.display="none";
		document.getElementById("progress_BlankId").style.display="none";
	}else if(ProgressUserNameValue=="Nurse")	{
		document.getElementById("progress_SurgeonId").style.display="none";
		document.getElementById("progress_AnesthesiologistId").style.display="none";
		document.getElementById("progress_NurseId").style.display="block";
		document.getElementById("progress_BlankId").style.display="none";
	}else if(ProgressUserNameValue=="")	{
		document.getElementById("progress_SurgeonId").style.display="none";
		document.getElementById("progress_AnesthesiologistId").style.display="none";
		document.getElementById("progress_NurseId").style.display="none";
		document.getElementById("progress_BlankId").style.display="block";
	}
}
function prntPgNotesFun(pConfId) {
	var url;
	url='progress_report_pdf.php?pConfId='+pConfId;
	window.open(url,'','width=850,height=600,top=100,left=100,resizable=yes,scrollbars=yes');
}
</script>
<!-- Safari -->
	<table class="table_pad_bdr alignCenter" style="width:99%">
		<tr>
			<td style="height:32%; background-color:#D1E0C9; background-image:url(images/top_bg.jpg);" class="text_10b valignTop">
				<table class="table_collapse alignCenter">
					<tr class="text_10b alignLeft">
						<td style="width:37%; height:8px; width:8px;" class="valignMiddle"><span class="text_10" style="color:#FFFFFF; font-weight:normal; "></span> <span style="color:#FFFFFF; "></span></td>
						<td style="width:33%" class="valignMiddle">
							<div style="color:#CB6B43; " class="top_headding txt_10b alignCenter"><?php echo "Progress Notes" ?></div>
							<!-- <a href="#"><img src="images/top_headding.jpg" width="253" height="26" border="0"></a> -->
						</td>
						<!-- <td width="33%" valign="top"><a href="#"><img src="images/top_headding.jpg" width="253" height="26" border="0"></a></td> -->
						<td style="width:23%" class="alignRight valignMiddle" ><div id="dt_tm" style="color:#FFFFFF; font-weight:normal; "></div></td>
						<td style="width:7%" class="alignCenter valignMiddle">&nbsp;&nbsp;<img src="images/close.jpg" alt="Close" style="width:20px; height:22px;" onClick="javascript:window.close();"></td>
					</tr>
				</table>
			</td>
		</tr>
	</table>


<table class="table_pad_bdr" style="width:99%;">

   <tr>
     <td colspan="2" style="height:22px; background-color:#F1F4F0" class="alignCenter valignTop"><span class="text_10b">Enter Your note</span></td>
     <td class="alignCenter"></td>
     <td rowspan="2" style="width:64%;" class="alignLeft valignTop">
	 <div style="border:0px solid red; height:370px; width:600px; overflow:x-hidden; overflow:auto;">
	 <table class="table_pad_bdr valignTop" style="width:95%">
		  <?php 
		  if($totalRows_rsNotes > 0) { 
				$a='';  
				while ($row_rsNotes = imw_fetch_array($rsNotes)) { 
					if($i%2==0) { 
						$a="#FFFFFF"; 
					} else { 
						$a="#F1F4F0";
					} 
					$ProgressNotesTime = $row_rsNotes['tTime'];
					//CODE TO SET $ProgressNotesTime 
						if($ProgressNotesTime=="00:00:00" || $ProgressNotesTime=="") {
							
						$ProgressNotesTime=date("h:i A");
						}else {
							$ProgressNotesTime=$ProgressNotesTime;
						}
							
						$time_split_ProgressNotesTime = explode(":",$ProgressNotesTime);
						if($time_split_ProgressNotesTime[0]>=12) {
							$am_pm = "PM";
						}else {
							$am_pm = "AM";
						}
						if($time_split_ProgressNotesTime[0]>=13) {
							$time_split_ProgressNotesTime[0] = $time_split_ProgressNotesTime[0]-12;
							if(strlen($time_split_ProgressNotesTime[0]) == 1) {
								$time_split_ProgressNotesTime[0] = "0".$time_split_ProgressNotesTime[0];
							}
						}else {
							//DO NOTHNING
						}
						$ProgressNotesTime = $time_split_ProgressNotesTime[0].":".$time_split_ProgressNotesTime[1]." ".$am_pm;
					
					//END CODE TO SET ProgressNotesTime
					
		  
		 ?>
		<tr style="background-color:<?php echo $a;?>; height:22px">
			<td class="text_10b valignTop nowrap" rowspan="" ><?php $datestring= $row_rsNotes['dtDateTime']; 
				$d=explode("-",$datestring);
				echo $d[1]."/".$d[2]."/".$d[0];
				
				?>
			</td>
			<td>&nbsp;</td>
			<td class="text_10b valignTop nowrap" rowspan="0" ><?php echo $ProgressNotesTime; ?></td>
			
			<td class="text_10 nowrap alignLeft valignTop" style="height:22px;" ><?php echo $row_rsNotes['user_type'];?></td>
			<td class="text_10b nowrap alignLeft valignTop" style="height:22px;" ><?php echo $row_rsNotes['fname']." ".$row_rsNotes['lname']; ?></td>
			
			<td class="text_10b valignTop"><?php //echo $row_rsNotes['user_type']; ?></td>
			
			<td>&nbsp;</td>
			<td colspan="0"  class="text_10b valignTop" >
				<a style="visibility:<?php echo $progrModifyVisi;?>;" class="link_home" href="progress_report.php?editid=<?php echo $row_rsNotes['intProgressID']; ?>&amp;pConfId=<?php echo $pConfId;?>">Edit</a>
			</td>
			<td>&nbsp;</td>
			<td class="text_10b valignTop" >
				<a style="visibility:<?php echo $progrModifyVisi;?>;"  class="link_home" href="progress_report.php?delid=<?php echo $row_rsNotes['intProgressID']; ?>&amp;pConfId=<?php echo $pConfId;?>" onClick="if(!confirm('Do you really want to delete')) return false;">Delete</a>
			</td>
		
		</tr>
	
		<tr bgcolor="<?php echo $a;?>" class="alignLeft" >
			<td  colspan="10" class="text_10 alignLeft"><?php echo $row_rsNotes['txtNote']; ?></td>
<!-- 			<td colspan="0" align="center" class="text_10b">
				<a class="link_home" href="progress_report.php?editid=<?php echo $row_rsNotes['intProgressID']; ?>">Edit</a>
			</td>
			<td align="center" class="text_10b">
				<a  class="link_home" href="progress_report.php?delid=<?php echo $row_rsNotes['intProgressID']; ?>" onClick="if(!confirm('Do you really want to delete')) return false;">Delete</a>
			</td>
 -->		
 		</tr>
	
		<tr style="background-color:<?php echo $a;?>;"><td colspan="4"></td></tr>
	  <?php 
				$i++;
			} //END WHILE
		?>
		<script>
			if(opener.document.getElementById('progress_notesBtn')) {
				opener.document.getElementById('progress_notesBtn').src="images/progress_notes_hover.gif";
			}
		</script>
		<?php
		} else { echo "<tr><td colspan='5' class='text_10b alignCenter'>No record found</td></tr>"; 
		?>
		<script>
			if(opener.document.getElementById('progress_notesBtn')) {
				opener.document.getElementById('progress_notesBtn').src="images/progress_notes.gif";
			}
		</script>
		<?php
		} ?>
 
	</table></div>
</td>
</tr>
<?php 
	$id_edit = $_REQUEST['editid'];
	
	if($id_edit!='')
	{
		$qry_Notes = "select * from tblprogress_report where intProgressID = $id_edit";
		$fetch_Notes = imw_query($qry_Notes);
		$row_notes = imw_fetch_array($fetch_Notes);
		$EditUserType = $row_notes['userType'];
		$EditusersId = $row_notes['usersId'];
		
		$EditUserNameQry = "select * from users where usersId = '$EditusersId'";
		$EditUserNameRes = imw_query($EditUserNameQry) or die(imw_error());
		$EditUserNumRow = imw_num_rows($EditUserNameRes);
		if($EditUserNumRow>0) {
			$EditUserRow = imw_fetch_array($EditUserNameRes);
			$EditUserName = $EditUserRow["fname"]." ".$EditUserRow["mname"]." ".$EditUserRow["lname"];
		}
		//$EditUserType = $row_notes['user_type']
	}
	
	
//GET LOGIN USER NAME AND USER TYPE
	$LoginUserNameQry = "select * from users where usersId = '$loginUserId'";
	$LoginUserNameRes = imw_query($LoginUserNameQry) or die(imw_error());
	$LoginUserNumRow = imw_num_rows($LoginUserNameRes);
	if($LoginUserNumRow) {
		$LoginUserRow = imw_fetch_array($LoginUserNameRes);
		$LoginUserType = $LoginUserRow["user_type"];
		$LoginUserName = $LoginUserRow["fname"]." ".$LoginUserRow["mname"]." ".$LoginUserRow["lname"];
	}
	if($EditUserType=="") {
		$EditUserType = $LoginUserType;
	}
	if($EditusersId=="") {
		$EditusersId = $loginUserId;
		$EditUserName = $LoginUserName;
	}
//END GET LOGIN USER NAME AND USER TYPE							
?>   
   
      
   <tr>
     <td class="alignCenter valignTop" style="width:1%; height:196px;">&nbsp;</td>
	 <td  class="alignLeft valignTop" style="width:35%; height:196px;">
	 <form method="post" name="form1" action="<?php echo $editFormAction; ?>" >
        <!-- Safari -- delete cols="30" rows="8"-->
              <textarea name="txtNote"  class="field justi text_10 " style="border:1px solid #cccccc; width:290px; height:280px; "><?php echo $row_notes['txtNote']; ?></textarea>
            
         <input type="hidden" name="editid" value="<?php echo $_REQUEST['editid']; ?>">   
        <!-- <input type="hidden" name="usersId" value="<?php //echo $loginUserId; ?>"> -->
        <input type="hidden" name="asc_id" value="<?php echo $ascID; ?>">
        <input type="hidden" name="dtDateTime" value="<?php echo date("Y-m-d"); ?>">
		<input type="hidden" name="tTime" value="<?php echo date('H:i:s'); ?>">
        <input type="hidden" name="MM_insert" value="form1"><br>
		<input type="hidden" name="confirmation_id" value="<?php echo $pConfId; ?>">
		<input type="hidden" name="hidd_userid" value="<?php echo $_REQUEST["editid"];?>">
		
		<input type="hidden" name="progress_UserType" value="<?php echo $EditUserType; ?>">
		<input type="hidden" name="hidd_progressUserId" value="<?php echo $EditusersId; ?>">
		
		<table class="table_collapse" style="border:none;">
			<tr>
				<td class="text_10b nowrap"><?php echo $EditUserType; ?>&nbsp;&nbsp;
					<!-- <select name="progress_UserType" class="text_10"  onChange="checkUserType(this)">
						<option value="">Who</option>
						<option value="Surgeon" <?php if($row_notes['userType']=='Surgeon') { echo 'selected'; }?>>Surgeon</option>
						<option value="Anesthesiologist" <?php if($row_notes['userType']=='Anesthesiologist') { echo 'selected'; }?>>Anesthesiologist</option>
						<option value="Nurse" <?php if($row_notes['userType']=='Nurse') { echo 'selected'; }?>>Nurse</option>
					</select> -->
				</td>
				
				<td class="text_10 nowrap">
					<?php echo $EditUserName; ?>
				</td>
				<!-- 
				<td nowrap class="text_10">
					<?php
					if($row_notes['userType']=='') { $blankDisplay = "block"; } else { $blankDisplay = "none"; }
					if($row_notes['userType']=='Surgeon') { $SurgeonDisplay = "block"; } else { $SurgeonDisplay = "none"; }
					if($row_notes['userType']=='Anesthesiologist') { $AnesthesiologistDisplay = "block"; } else { $AnesthesiologistDisplay = "none"; }
					if($row_notes['userType']=='Nurse') { $NurseDisplay = "block"; } else { $NurseDisplay = "none"; }
					?>
					<select name="progressBlankId_list" class="text_10" id="progress_BlankId" onChange="document.form1.hidd_userid.value=this.value" style="display:<?php echo $blankDisplay;?>;width:167px; ">
						<option value="">Created By</option>
					</select>	 
					<select name="progressSurgeonId_list" id="progress_SurgeonId" onChange="document.form1.hidd_userid.value=this.value" style="display:<?php echo $SurgeonDisplay;?>; width:167px; ">
						<option value="">Created By</option>
						<?php 
						$progressUserNameQry = "select * from users where user_type = 'Surgeon'";
						//$progressUserNameQry = "select * from users where user_type = 'Surgeon'";
						$progressUserNameRes = imw_query($progressUserNameQry) or die(imw_error());
						$progressUserNumRow = imw_num_rows($progressUserNameRes);
						if($progressUserNumRow) {
							while($progressUserRow = imw_fetch_array($progressUserNameRes)) {
								$progressUserId = $progressUserRow["usersId"];
								$progressUserName = $progressUserRow["fname"]." ".$progressUserRow["mname"]." ".$progressUserRow["lname"];
						?>
						<option value="<?php echo $progressUserId;?>" <?php if($row_notes['usersId']==$progressUserId) { echo 'selected'; }?>><?php echo $progressUserName;?></option>
						<?php
							}
						}
						
						?>
					</select>
					<select name="progressAnesthesiologistId_list" class="text_10" id="progress_AnesthesiologistId" onChange="document.form1.hidd_userid.value=this.value" style="display:<?php echo $AnesthesiologistDisplay;?>;width:167px; ">
						<option value="">Created By</option>
						<?php 
						$progressUserNameQry = "select * from users where user_type = 'Anesthesiologist'";
						//$progressUserNameQry = "select * from users where user_type = 'Surgeon'";
						$progressUserNameRes = imw_query($progressUserNameQry) or die(imw_error());
						$progressUserNumRow = imw_num_rows($progressUserNameRes);
						if($progressUserNumRow) {
							while($progressUserRow = imw_fetch_array($progressUserNameRes)) {
								$progressUserId = $progressUserRow["usersId"];
								$progressUserName = $progressUserRow["fname"]." ".$progressUserRow["mname"]." ".$progressUserRow["lname"];
						?>
						<option value="<?php echo $progressUserId;?>" <?php if($row_notes['usersId']==$progressUserId) { echo 'selected'; }?>><?php echo $progressUserName;?></option>
						<?php
							}
						}
						
						?>
					</select>
					<select name="progressNurseId_list" class="text_10" id="progress_NurseId" onChange="document.form1.hidd_userid.value=this.value" style="display:<?php echo $NurseDisplay;?>;width:167px; ">
						<option value="">Created By</option>
						<?php 
						$progressUserNameQry = "select * from users where user_type = 'Nurse'";
						//$progressUserNameQry = "select * from users where user_type = 'Surgeon'";
						$progressUserNameRes = imw_query($progressUserNameQry) or die(imw_error());
						$progressUserNumRow = imw_num_rows($progressUserNameRes);
						if($progressUserNumRow) {
							while($progressUserRow = imw_fetch_array($progressUserNameRes)) {
								$progressUserId = $progressUserRow["usersId"];
								$progressUserName = $progressUserRow["fname"]." ".$progressUserRow["mname"]." ".$progressUserRow["lname"];
						?>
						<option value="<?php echo $progressUserId;?>" <?php if($row_notes['usersId']==$progressUserId) { echo 'selected'; }?>><?php echo $progressUserName;?></option>
						<?php
							}
						}
						
						?>
					</select>
				
				</td> -->
			</tr>
			<tr>
				<td colspan="2" style="height:4px;">&nbsp;
				</td>
			</tr>
			<tr>
				<td class="alignCenter nowrap" colspan="2">
                    <!-- <input name="submit" type="submit" value="Save" align="left" class="button text_10" style="width:70px;"> -->
					<input  type="hidden" name="submit_click" value="Save">
					<a href="#" style="visibility:<?php echo $progrModifyVisi;?>;" onClick="MM_swapImage('saveBtnPg','','images/save_onclick1.jpg',1);" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('saveBtnPg','','images/save_hover1.jpg',1)"><img src="images/save.jpg" name="saveBtnPg" border="0" id="saveBtnPg" alt="save" onClick="return validate_form ( );"/></a>
					<img src="images/tpixel.gif" width="2" height="1">
					<!-- <input type="button" name="Submit" value="Close" class="button text_10" style="width:70px;" onClick="javascript:window.close();"> -->
					<a href="#" onClick="MM_swapImage('CloseBtnPg','','images/close_click.gif',1);" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('CloseBtnPg','','images/close_hover.gif',1)"><img src="images/close.gif" name="CloseBtnPg" width="70" height="25" border="0" id="CloseBtnPg" alt="Close" onClick="javascript:window.close();" /></a>
					<?php
					if($totalRows_rsNotes > 0) { 
					?>
						<img src="images/tpixel.gif" width="2" height="1">
						<a href="#" onClick="MM_swapImage('PrintBtnPg','','images/print_onclick1.jpg',1)" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('PrintBtnPg','','images/print_hover1.jpg',1)"><img src="images/print.jpg" name="PrintBtnPg" width="70" height="25" border="0" id="PrintBtnPg" alt="Print Progress Notes" onClick="prntPgNotesFun('<?php echo $pConfId; ?>');"/></a>
					<?php 
					}
					?>
				</td>
			</tr>
		</table>

		
     </form>       </td>
     <td class="alignCenter valignTop" style="width:1%; height:196px;">&nbsp;</td>
	 
  </tr>   
</table>

</div>
</div> 
</body>
</html>
<?php
//imw_free_result($Recordset1);

imw_free_result($rsNotes);
?>
