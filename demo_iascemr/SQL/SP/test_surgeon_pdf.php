<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
include_once("common/conDb.php");
include("common_functions.php");
include("common/linkfile.php");
include_once("admin/classObjectFunction.php");
$objManageData = new manageData;
?>
<html>
	<head>
		<title>Operative Report</title>
		<script>
			function printSurgeonPdf() {
				var msg="Please fill all the mandatory fields:- \n";
				var flag = 0;
				
				var surgeonValue = document.frmSurgeonPdf.surgeonId.value;
				var dateValue = document.frmSurgeonPdf.date1.value;
				
				if(surgeonValue==''){ msg = msg+"\t� Surgeon_Name\n"; ++flag; }
				if(dateValue==''){ msg = msg+"\t� Date\n"; ++flag; }
				
				if(flag > 0){
						alert(msg);
						return false;
				}else {
					//alert(surgeonValue+'\n'+dateValue);
					window.open('test_surgeon_pdf_display.php?surgeonId='+surgeonValue+'&surgery_date='+dateValue);
					return true;
				}
			}
		</script>
	
		<script language="javascript">
			var today = new Date();
			var day = today.getDate();
			var month = today.getMonth()
			var year = y2k(today.getYear());
			var mon=month+1;
			if(mon<=9){
				mon='0'+mon;
			}
			var todaydate=mon+'-'+day+'-'+year;
			function y2k(number){
				return (number < 1000)? number+1900 : number;
			}
			function newWindow(q){
				
				mywindow=open('mycal1.php?md='+q,'','width=200,height=250,top=200,left=300');
				mywindow.location.href = 'mycal1.php?md='+q;
				if(mywindow.opener == null)
					mywindow.opener = self;
			}
			function restart(q){
				var fillDate = ''+ padout(month - 0 + 1) + '-'  + padout(day) + '-' +  year;
				if(q==8){
					if(fillDate > todaydate){
						alert("Date Of Service can not be a future date")
						return false;
					}
				}
				document.getElementById("date"+q).value=fillDate;
				mywindow.close();
			}
			function padout(number1){
				return (number1 < 10) ? '0' + number1 : number1;
			}
			function resetdate() {
	   			document.frmSurgeonPdf.date1.value="";
				document.frmSurgeonPdf.surgeonId.value="";
			}
		</script>	
	</head>
	<body><br>
		<form name="frmSurgeonPdf" action="test_surgeon_pdf_display.php" method="post">
			<table border="0" align="center" cellpadding="0" cellspacing="5" width="100%">
				<tr>
					<td class="text_10b" align="right">Select Surgeon:</td>
					
					<td colspan="2"  align="left" class="text_10b"><img src="images/tpixel.gif" width="2" height="1">
						<?php
						if(!$surgeonId) {
							$surgeonId = $_REQUEST['surgeonId'];
						}
						?>
						<select name="surgeonId" class="text_10" style="width:200px;" >
							<option value="">Select Surgeon</option>
								<?php
								
								$userSurgeonsDetails = $objManageData->getArrayRecords('users', 'user_type', 'Surgeon','lname','ASC');
								if($userSurgeonsDetails) {
									foreach($userSurgeonsDetails as $surgeon){
										$deleteStatus = $surgeon->deleteStatus;
										if($deleteStatus=="Yes") { //IF THIS USER HAS BEEN COMMITTED AS DELETED(BY SETTING ITS deleteStatus TO Yes)
											////DO NOT SHOW DELETED USER IN DROP DOWN 
										}else {
										
										?>
											<option value="<?php echo $surgeon->usersId; ?>" ><?php echo $surgeon->lname.', '.$surgeon->fname; ?></option>
										<?php
										}
									}
								}	
								?>
						</select>
					</td>
				</tr>
				<tr>
			        <td class="text_10b" valign="middle" width="44%" align="right">Date:</td>
				    <td width="10%" align="center" class="text_10"><img src="images/tpixel.gif" width="7" height="1"><input type="text" readonly class="field text" style=" border:1px solid #ccccc; width:90px;" tabindex="1" id="date1" name="date1" value="<?php echo $date;?>"/></td>
			        <td width="46%" align="left"><img src="images/tpixel.gif" width="10" height="1"><img src="images/icon_cal.jpg" width="20" height="20" border="0" align="middle" onClick="return newWindow(1);" ></td>
			  </tr>
			  <tr height='20'><td colspan="3">&nbsp;</td></tr>	
			  <tr align="left" valign="middle"  height="24">	
					<td colspan="3" align="center">
					  <a href="javascript:void(0);" onClick="MM_swapImage('generate_report','','images/generate_report_click.jpg',1)" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('generate_report','','images/generate_report_hover.jpg',1)"><img src="images/generate_report.jpg" name="generate_report" width="140" height="25" border="0" id="generate_report" onClick="javascript:return printSurgeonPdf();"/></a>
					  <a href="javascript:void(0);" onClick="MM_swapImage('reset','','images/reset_click.jpg',1)" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('reset','','images/reset_hover.jpg',1)"><img src="images/reset.jpg" name="reset" width="70" height="25" border="0" id="reset" onClick="return resetdate();" /></a>
				   </td>
			 </tr>
			</table>
		</form>	
	</body>
</html>