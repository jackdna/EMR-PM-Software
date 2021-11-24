<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<html>
<head>
<?php
include("common/auditLinkfile.php");
?>
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


function chartpop(q){
		
		mywindow=open('chart_pop.php?md='+q,'','width=650,height=600,top=70,left=200');
		mywindow.location.href = 'chart_pop.php?md='+q;
		if(mywindow.opener == null)
			mywindow.opener = self;
	}

	
	</script>
<table cellpadding="0" bordercolor="#BCD2B0" align="center" cellspacing="0" width="100%" height="80%" bgcolor="#FFFFFF" border="1">
	<tr bgcolor="#FFFFFF">
	  <td  class="text_10b" valign="top">
      	 
	     <table class="text_10"   cellpadding="0" border="0" cellspacing="0" width="100%" align="center">
		 	<tr height="22" bgcolor="#F1F4F0">
				<td colspan="8" align="center" class="text_10b">Chart Note Audit Report</td>
			</tr>
			<tr height="22"><td></td></tr>
			
			
			<tr bgcolor="#F1F4F0" height="22">
			<td width="20%" ></td>
				
				<td colspan="2" width="15%"  class="text_10" align="right">
					Select User <img src="form_design/images/tpixel.gif" width="21" height="1" /></td>
				
				<td  colspan="2" align="left" >
					<select style="font-family:verdana; font-size:11px;">
					<option value="1">Jane Doe</option>
					<option value="2">John Smith</option>
					<option value="3">Kavin</option>
					<option value="4">Jubin</option>
					<option value="5">Lara Angelina</option>
				</select>
				</td>
				<td colspan="4"></td>
				
				
			</tr>
			<tr height="22"><td colspan="8"></td></tr>
			<tr bgcolor="#F1F4F0">
			<td width="20%" ></td>
				
				<td colspan="2" width="15%"  class="text_10" align="center"  ><img src="form_design/images/tpixel.gif" width="9" />
					Date Range <img src="form_design/images/tpixel.gif" width="33" height="1" /> From</td>
				
				<td width="5%" align="left" ><input type="text" name="date1" id="date1" class="field text" style=" border:1px solid #ccccc;  width:60px;" tabindex="1" value="" />
							</td>
				<td width="6%" align="left">
					<img src="form_design/images/icon_cal.jpg"   onClick="return newWindow(1);" />
				</td>				
				<td width="1%" class="text_10">To<img src="admin/images/tpixel.gif" width="2" />
				</td>
				<td width="6%" align="center"><input type="text" id="date2" name="date2" class="field text" style=" border:1px solid #ccccc;  width:60px;" tabindex="1" value=""  />
				</td>
				<td width="20%" align="left"><img src="form_design/images/icon_cal.jpg" onClick="return newWindow(2);" /></td>
				
			</tr>
			<tr   height="22"><td colspan="8"></td></tr>
			<tr bgcolor="#F1F4F0" height="22">
				<td width="20%"></td>
				<td colspan="2" width="15%"  class="text_10" align="right">
				Audit Type <img src="form_design/images/tpixel.gif" width="21"  height="1" />
				</td>	
				<td colspan="2" align="left">
					<select  style="font-family:verdana; font-size:11px;">
						<option value="1">All</option>
						<option value="2">Created</option>
						<option value="3">Modified</option>
						<option value="4">Viewed</option>
						<option value="5">Printed</option>
					</select>
				</td>
				<td colspan="4"></td>
			</tr>
			<tr height="22"><td colspan="8"></td></tr>
			<tr>
				<td colspan="8" align="center">
					<table width="100%" class="text_10b">
						<!--<tr>
							<td width="6" align="right"><img id="img1Left" src="form_design/images/leftDark.gif" width="3" height="24"></td>
						</tr>-->
						<tr bgcolor="#F1F4F0" height="" class="text_10">
							<td width="50%" align="right" class="text_10b">
								<input type="submit" name="elem_submit"  class="button" style="width:75px;" onClick="return chartpop(1);"   value="Audit">
							</td>
							<td width="5%"></td>
							<td align="left" 
							width="49%">
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
</html> 	 
	

 
				



				
				
				