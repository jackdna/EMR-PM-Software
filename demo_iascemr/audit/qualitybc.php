<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
include_once("../globalsSurgeryCenter.php");
include("common/auditLinkfile.php");
 $type= $_POST['chbx_quality'];
?>

	<html>
	<head>
	</head>
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
		fillDate = ''+ padout(month - 0 + 1) + '-'  + padout(day) + '-' +  year;
		if(q==8){
			if(fillDate > todaydate){
				alert("Date Of Service can not be a future date")
				return false;
			}
		}
		document.getElementById("date"+q).value=fillDate;
		mywindow.close();
	}
function padout(number){
return (number < 10) ? '0' + number : number;
}

function qualitypop(){

		var patient=document.quality_check_form.patient_name.value;
		var user=document.quality_check_form.user_name.value;
		var lname1 =document.quality_check_form.start.value;
		var lname2 =document.quality_check_form.end.value;
		var quant=document.quality_check_form.quantity.value;
		var month=document.quality_check_form.month.value;
		var year=document.quality_check_form.year.value;
		if(document.getElementById("chbx_quality_yes").value!="" && document.getElementById("chbx_quality_yes").checked==true){
			var det=document.getElementById("chbx_quality_yes").value;
		}else if(document.getElementById("chbx_quality_no").value!="" && document.getElementById("chbx_quality_no").checked==true){
			var det=document.getElementById("chbx_quality_no").value;
		}
		var user=document.quality_check_form.user_name.value;
		//alert(user);
		url='qualitypopbc.php?name='+user+'&patient='+patient+'&lnamest='+lname1+'&lnamend='+lname2+'&number='+quant+'&mon='+month+'&year1='+year+'&sum='+det;
		window.open(url,'wind','width=750,height=600,top=70,left=200, scrollbars=1,resizable=1');
		
	}
	
	</script>
	<form name="quality_check_form" action="" method="post">
	
<table cellpadding="0" bordercolor=#BCD2B0 align="center" cellspacing="0" width="100%" height="80%" bgcolor="#FFFFFF" border="1">
	<tr bgcolor="#FFFFFF">
	  <td  class="text_10b" valign="top">
	     <table class="text_10b" border="0"  cellpadding="0" cellspacing="0" width="100%" align="center">
		 	<tr height=22><td colspan="8"></td></tr>
			<tr bgcolor="#F1F4F0" height=22>
				<td colspan="8"><img src="../images/tpixel.gif" width="8" />Select UserName
				</td>
			</tr>
			<tr bgcolor="#FFFFFF" height=22>
				<td width="25%" align="right" colspan="1" class="text_10" ><img src="../images/tpixel.gif" width="10" />User Name<img src="../images/tpixel.gif" width="4" />
				</td>
				<td  width="50%" colspan="5" align="left"><img src="../images/tpixel.gif" width="4" />
			<select class="text_10"  name="user_name">				
					<option value="">Select User</option>
					<?php 
						$qry=imw_query("select * from users");
						while($uname=imw_fetch_array($qry))
						{
						$user= $uname[1]." ".$uname[3];
						$uid=$uname[0];
						
					?>						

					<option value="<?php echo $uname[0]; ?>"   <?php //if($_REQUEST['patient_name']==$uid){ echo 'selected="selected"';}  ?>  ><?php echo $user;  ?></option>
					<?php } ?>
				</select>
			</td>
		</tr>		
			<tr bgcolor="#F1F4F0" height=22>
				<td colspan="8"><img src="../images/tpixel.gif" width="8" />Search By Patient Name
				</td>
			</tr>
		
			
			<tr  height=22 >
				
				<td width="25%" align="right" colspan="1" class="text_10" ><img src="../images/tpixel.gif" width="10" />Patient Name<img src="../images/tpixel.gif" width="4" />
				</td>
				<td  width="50%" colspan="5" align="left"><img src="../images/tpixel.gif" width="4" />
				<input type="text" name="patient_name">
				</td>
				<td colspan="2" align="left">
				<img src="../images/tpixel.gif" width="4" />
				
				</td>
			</tr>
			<tr bgcolor="#F1F4F0" height="22"><td colspan="8"></td></tr>
			<tr bgcolor="" height="22"><td colspan="8"></td></tr>
			<tr bgcolor="#F1F4F0" height="22" >
				<td colspan="8">
					<table width="100%" class="text_10b">
						<tr>
							<td>
								<img src="../images/tpixel.gif" width="8" />Search By Last Name Range 
							</td>
							
						</tr>
					</table>
				</td>
				
			</tr>
			<tr>
				<td colspan="8">
					<table width="100%" class="text_10">
						<tr>
							<td width="22%" align="right" ><img src="tpixel.gif" width="4">From<img src="tpixel.gif" width="8">
							</td>
							<td width="9%" align="center"><input type="text" name="start"  class="field text" style=" border:1px solid #ccccc; width:120px;" tabindex="1" value="" /></td>	
							<td width="28%" align="center" >To <img src="tpixel.gif" width="8"><input type="text" name="end"  class="field text" style=" border:1px solid #ccccc; width:120px;" tabindex="1" value="" />
							</td>
							<td></td>
						</tr>
					</table>
				</td>			
			</tr>
			<tr bgcolor="#F1F4F0" height="22"><td colspan="8"></td></tr>
			<tr bgcolor="" height="22"><td colspan="8"></td></tr>
			<tr bgcolor="#F1F4F0" height="22" >
				<td colspan="8">
					<table width="100%" class="text_10b">
						<tr>
							<td>
								<img src="../images/tpixel.gif" width="8" />Search By Random Number
							</td>
							
						</tr>
					</table>
				</td>
				
			</tr>
			<tr>
				<td colspan="8">
					<table width="100%" border="0" class="text_10">
						<tr>
							<td width="17%" align="right" >Quantity<img src="tpixel.gif" width="4">
							</td>
							
							<td align="left" width="9%"><input type="text" name="quantity"  class="field text" style=" border:1px solid #ccccc; width:70px;" tabindex="1" value="" /></td>
							<td width="4%" align="right">Month</td>	
							<td width="12%" align="left" >  <img src="tpixel.gif" width="4">
								<select class="text_10" name="month">
									<option value="">Select</option>
									<option value="1">January</option>
									<option value="2">February</option>
									<option value="3">March</option>
									<option value="4">April</option>
									<option value="5">May</option>
									<option value="6">June</option>
									<option value="7">July</option>
									<option value="8">August</option>
									<option value="9">September</option>
									<option value="10">October</option>
									<option value="11">November</option>
									<option value="12">December</option>	
								</select>
							</td>
							<td width="4%" align="right" >Year <img src="tpixel.gif" width="5">
								</td><td width="30%" align="left"><select name="year" class="text_10">
									<option value="">Select</option>
									<?php 
									$date=date("Y");
									for($i=$date-50;$i<=$date;$i++)
									{
									?>
									
									<option value="<?php echo $i; ?>"><?php echo $i; }?></option>
									
								</select>
							</td>
							
							
						</tr>
					</table>
				</td>			
			</tr>
			<tr bgcolor="#F1F4F0" height="22" class="text_10"><td colspan="8"></td></tr>
			<tr  height="22"><td colspan="8"></td></tr>
			<tr bgcolor="#F1F4F0" height="22" class="text_10b">
				<td colspan="8" align="left"><img src="tpixel.gif" width="8">Select Audit Type
								
				</td>
			</tr>
			<tr height="22" class="text_10">
				<td colspan="2">
					<table class="text_10" width="100%" border="0">
						<tr height="22">
							<td width="230" align="right">Summary<img src="tpixel.gif" width="7"></td>
							<td align="right" width="26" onClick="javascript:checkSingle('chbx_quality_yes','chbx_quality')"><input class="field checkbox" <?php if ($type=='summary')echo 'checked';  ?> type="checkbox" value="summary" name="chbx_quality" id="chbx_quality_yes" tabindex="7" ></td>
							<td width="60"  align="right">Details<img src="tpixel.gif" width="7"></td>
							<td align="left" width="26" onClick="javascript:checkSingle('chbx_quality_no','chbx_quality')"><input class="field checkbox" <?php if ($type=='detail')echo 'checked';  ?> type="checkbox" value="detail" name="chbx_quality" id="chbx_quality_no" tabindex="7" ></td>

						</tr>
					</table>
				</td>
							
	
				<td colspan="6"></td>		
			</tr>	
			<tr bgcolor="#F1F4F0" height="22" class="text_10"><td colspan="8"></td></tr>
			<tr  height="22"><td colspan="8"></td></tr>
			<tr bgcolor="#F1F4F0">
				<td colspan="8" align="center">
					<table width="100%" class="text_10b">
						
						<tr bgcolor="" height="" class="text_10">
							<td width="50%" align="right" class="text_10b">
								<input type="button" name="elem_submit"  class="button" style="width:75px;" onClick="return qualitypop();"   value="Audit">
							</td>
							<td width="2%"></td>
							<td width="48%" align="left">
								<input type="submit" name="elem_submit" value="Reset" class="button" style="width:75px;">
							</td>
						</tr>
					</table>
				</td>		
			</tr>
		 	
		</table>
	</td>
</tr>
</table>		
</form>		