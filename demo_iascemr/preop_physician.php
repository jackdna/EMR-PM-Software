<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
	session_start();
	include("common/header.php");
?>
	<form name="frm_health_ques" class="wufoo topLabel" enctype="multipart/form-data" method="post" style="margin:0px; " action="">
	<tr>
		<td ><img src="images/tpixel.gif" width="1" height="3"></td>
	</tr>
	<tr>
		<td valign="top"  >
		
			<table width="99%" align="center" height="25" border="0" cellpadding="0" cellspacing="0" bgcolor="#B3DD8C" class="all_border">
				<tr bgcolor="#EFEFEF" >
					<td align="left" class="text_10b1" ><img src="images/tpixel.gif" width="5">Patient Name</td>
					<td align="left" bgcolor="#FFFFFF" class="text_10"><img src="images/tpixel.gif" width="2">William R Duphorn</td>
				
					<td align="left" class="text_10b1"><img src="images/tpixel.gif" width="2">ASC-ID</td>
					<td align="left" bgcolor="#FFFFFF" class="text_10"><img src="images/tpixel.gif" width="2">9998499854</td>
				
					<td align="left" class="text_10b1"><img src="images/tpixel.gif" width="2">Date</td>
					<td align="left" bgcolor="#FFFFFF" class="text_10"><img src="images/tpixel.gif" width="2">03-19-2008</td>
					
					<td align="left" class="text_10b1"><img src="images/tpixel.gif" width="2">Nurse</td>
					<td align="left" bgcolor="#FFFFFF" class="text_10"><img src="images/tpixel.gif" width="2">Micky James</td>
					<td align="left" class="text_10b1"><img src="images/tpixel.gif" width="2">Progress Notes</td>
					<td align="left" bgcolor="#FFFFFF" class="text_10"><img src="images/tpixel.gif" width="2">Special test is Ok</td>
					
					<td align="left" class="text_10b1"><img src="images/tpixel.gif" width="2">Time</td>
					<td align="left" bgcolor="#FFFFFF" class="text_10"><img src="images/tpixel.gif" width="2">16:27</td>
			  </tr>
	 	  </table>
		</td>
	</tr>
	<tr>
		<td ><img src="images/tpixel.gif" width="1" height="5"></td>
	</tr>
	<tr>
		<td  valign="top" align="center">
			<table width="24%" border="0" align="center" cellpadding="0" cellspacing="0">
							<tr>
								<td width="6" align="right"><img src="images/left.gif" width="3" height="24"></td>
								<td width="229" align="center" valign="middle" bgcolor="#BCD2B0" class="text_10b" >Post-Op Physician Orders</td>
							  <td align="left" valign="top" width="10"><img src="images/right.gif" width="3" height="24"></td>
							</tr>
		  </table>
		</td>
	</tr>
	<tr>
	  <td><img src="images/tpixel.gif" width="4" height="1"></td>

	</tr>
	<tr>
	  <td bgcolor="#ECF1EA" align="left">
	  
	  <table width="99%"  align="center" border="0" cellpadding="0" cellspacing="0" class="all_border">
          <tr bgcolor="#D1E0C9" height="25">
		  <td>
		  <table cellpadding="0" cellspacing="0" border="0" width="101%">
		    <tr>
		    <td width="15"></td>
            <td width="133" height="25" class="text_10b">Post Op Orders</td>
			<td width="390" class="text_10"   align="center" nowrap="nowrap"> <div style="background-color:#CCCCCC; padding:3px;">On arrival the following drops will be given to the operative eye</div></td>
             <td width="428">&nbsp;</td>
			</tr>
			</table>
		  </td>
		  </tr>
		  <tr height="25" bgcolor="#F1F4F0">
		     <td colspan="3">
			   <table width="100%" border="0" cellpadding="0" cellspacing="0">
			       <tr>
				      <td  width="13">&nbsp;</td>
				      <td class="text_10" width="234" style="color:'800080';">List of Pre-OP Medication Orders</td>
				     <td class="text_10" width="104"></td>
						  <td width="11">&nbsp;</td>
						  <td class="text_10" nowrap="nowrap" width="603"></td>
				   </tr>
			   </table>	
			   </td>
			   </tr>
			   <tr height="25" bgcolor="#FFFFFF">
			      <td colspan="5">
				     <table cellpadding="0" cellspacing="0" border="0" width="100%">
					   <tr height="25">
					      <td width="14"></td>
					      <td width="171" class="text_10b">Medication</td>
						  <td width="150" class="text_10b">Strength</td>
						  <td width="613" class="text_10b">Directions</td>
					   </tr>
					   <tr height="25" bgcolor="#F1F4F0">
					      <td width="14" class="text_10"></td>
					      <td width="171" class="text_10">ALCAINE</td>
						  <td width="150" class="text_10">0.5%</td>
						  <td width="613" class="text_10">1 GG in the OPERATIVE EYE</td>
					   </tr>
					      <tr height="25">
					      <td width="14"></td>
					      <td width="171" class="text_10">CYCLOGYL</td>
						  <td width="150" class="text_10">1%</td>
						  <td width="613" class="text_10">1 GTT X 2 Q 5 MIN</td>
					   </tr>
					      <tr height="25" bgcolor="#F1F4F0">
					      <td width="14"></td>
					      <td width="171" class="text_10">MYDRIACYL</td>
						  <td width="150" class="text_10">1%</td>
						  <td width="613" class="text_10">1 GTT X 2 Q 5 MIN</td>
					   </tr>
				     </table>
				  </td>
			   </tr>
			      <tr bgcolor="#FFFFFF">
			      <td>
				     <table cellpadding="0" cellspacing="0" border="0" width="102%">
					   <tr height="25">
					      <td width="15"></td>
					      <td width="232" nowrap="nowrap" class="text_10">Define new medication</td>
						  
						  <td width="718"><textarea id="Field3" class="field textarea justi" style="font-family:verdana; font-size:11px; border:1px solid #cccccc; width:307px; " rows="10" cols="50" tabindex="6" ></textarea></td>
					   </tr>
				    </table>
				  </td>
			   </tr>
			   <tr height="25">
			      <td>
				     <table cellpadding="0" cellspacing="0" border="0" width="101%">
					   <tr height="25">
					      <td width="14"></td>
					      <td width="36" nowrap="nowrap" class="text_10"><img src="images/clock.gif" border="0"/></td>
						  <td width="34" class="text_10">Time</td>
						  <td width="68" class="text_10">Medicated</td>
						  <td width="39" class="text_10">16:27</td>
						  <td width="38" class="text_10">16:27</td>
						  <td width="709" class="text_10">16:27</td>
					</tr>
				    </table>
				  </td>
			   </tr>
			   <tr height="20" bgcolor="#FFFFFF">
			      <td>
				     <table cellpadding="0" cellspacing="0" border="0" width="101%">
					   <tr height="25">
					      <td width="14"></td>
					      <td width="40" nowrap="nowrap" class="text_10">Start</td>
						  <td width="192" class="text_10" nowrap="nowrap">Heparin Lock or IV</td>
						  <td width="144" class="text_10" nowrap="nowrap">
						  <select name="postop_physician" style="font-family:verdana; font-size:11px; border:1px solid #cccccc;">
					      <option>No IV</option>
						  <option>Hand</option>
						  <option>Wrist</option>
						  <option>Arm</option>
						  <option>Antecubital</option>
						  <option>Other</option>
                         </select> </td>
						 <td width="37" class="text_10">Right</td>
					     <td width="21" class="text_10"><input type="checkbox" name="chbx_ec" class="field checkbox" value="Yes" id="chbx_ec_yes"/></td>
					     <td width="32" class="text_10">Left</td>
						 <td width="32" class="text_10"><input type="checkbox" name="chbx_ec" class="field checkbox" value="Yes" id="chbx_ec_yes"/></td>
						  <td class="text_10" width="426">TKO IN Patient</td>
					</tr>
				    </table>
				  </td>
			   </tr>
			   <tr height="20">
			      <td>
				     <table cellpadding="0" cellspacing="0" border="0" width="101%">
					   <tr height="25">
					      <td width="15"></td>
					      <td width="376" nowrap="nowrap" class="text_10">HONAN BALLON to Operative Eye Site</td>
						  <td width="37" class="text_10" nowrap="nowrap">Yes</td>
					
					     <td width="23" class="text_10"><input type="checkbox" name="chbx_ec" class="field checkbox" value="Yes" id="chbx_ec_yes"/></td>
					     <td width="29" class="text_10">No</td>
						 <td width="33" class="text_10"><input type="checkbox" name="chbx_ec" class="field checkbox" value="Yes" id="chbx_ec_yes"/></td>
					     <td width="2"></td>
						 <td class="text_10" width="53">
						  <select name="postop_physician" style="font-family:verdana; font-size:11px; border:1px solid #cccccc;">
					      <?php
						  for($i=1;$i<=60;$i++)
						  {
						  ?>
						   <option><?php echo $i; ?></option>
						  <?php 
						   }
						  ?>
                         </select></td>
						   <td width="32" class="text_10">min</td>
						   <td width="338"></td>
					</tr>
				    </table>
				  </td>
			   </tr>
			   <tr bgcolor="#FFFFFF">
			      <td>
				     <table cellpadding="0" cellspacing="0" border="0" width="102%">
					   <tr height="25">
					      <td width="15"></td>
					      <td width="232" nowrap="nowrap" class="text_10">Other Pre-Op Orders</td>
						  
						  <td width="718"><textarea id="Field3" class="field textarea justi" style="font-family:verdana; font-size:11px; border:1px solid #cccccc; width:307px; " rows="10" cols="50" tabindex="6"  ></textarea></td>
					   </tr>
				    </table>
				  </td>
			   </tr>
			   </table>
			   		 </td>
	</tr>
	<tr>
		<td> <img src="images/tpixel.gif" width="1" height="5"></td>
	</tr>
	<tr>
		<td valign="top">
			<table align="center" cellpadding="0" cellspacing="0" width="99%"  bgcolor="#FFFFFF" class="all_border"  >
				<tr><td colspan="8"><img src="images/tpixel.gif" width="1" height="5"></td></tr>
				<tr align="left" valign="middle" bgcolor="#F1F4F0">
					<td width="6%" class="text_10b"><img src="images/tpixel.gif" width="15" height="1" />Time</td>
				  <td class="text_10" width="8%">16:27</td>
				  <td width="2%"></td>
					<td width="10%" class="text_10">Surgeon Name</td>
				  <td width="16%" bgcolor="#F1F4F0" class="text_10"><input type="text"  class="all_border" style="height:50px; width:150px; " disabled>
			      </td>
				  <td width="3%"></td>
					<td width="10%" class="text_10">Anesthesiologist</td>
				  <td width="24%" class="text_10"><input type="text"  class="all_border" style="height:50px; width:150px; " disabled></td>
					<td width="5%" class="text_10"></td>
				  <td width="16%" class="text_10"></td>
			  </tr>
				<tr><td colspan="8"><img src="images/tpixel.gif" width="1" height="5"></td></tr>
		  </table>
		</td>
		  </tr>
		  <tr>
		               <td><img src="images/tpixel.gif" width="1" height="5"></td>
	                </tr>
		  <tr>
			   <td>
			    	<table align="center" cellpadding="0" cellspacing="0" width="99%"  bgcolor="#FFFFFF" class="all_border"  >
				<tr><td colspan="8"><img src="images/tpixel.gif" width="1" height="5"></td></tr>
				<tr align="left" valign="middle" bgcolor="#F1F4F0">
					<td height="25" nowrap="nowrap" class="text_10b"> 
					<table cellpadding="0" cellspacing="0" border="0" width="99%">
					<tr>
		               <td><img src="images/tpixel.gif" width="1" height="5"></td>
	                </tr>
					<tr>
					   <td width="123"></td>
					   <td width="124" nowrap="nowrap" class="text_10b"> Electronically Signed </td>
					   <td width="27" class="text_10">Yes</td>
				      <td width="28" height="20" align="center" onClick="javascript:checkSingle('chbx_sur_yes','chbx_sur')">
			          <input type="checkbox" name="chbx_sur" value="Yes" class="field checkbox" id="chbx_sur_yes"/></td>
					  <td width="23" class="text_10" >No</td>
					  <td width="47" height="20" align="center" onClick="javascript:checkSingle('chbx_sur_no','chbx_sur')">
				      <input type="checkbox" name="chbx_sur" value="No" class="field checkbox" id="chbx_sur_no"/></td>
					  <td width="32"></td>
					  <td width="131" class="text_10b" nowrap="nowrap" > Electronically Signed</td>
					  <td width="24" class="text_10" >Yes</td>
				      <td width="30" height="20" align="center" onClick="javascript:checkSingle('chbx_nur_yes','chbx_nur')">
			          <input type="checkbox" name="chbx_nur" value="Yes" class="field checkbox" id="chbx_nur_yes"/></td>
					  <td width="20" class="text_10">No</td>
					  <td width="324" height="20" align="center" onClick="javascript:checkSingle('chbx_nur_no','chbx_nur')">
				      <input type="checkbox" name="chbx_nur" class="field checkbox" value="No" id="chbx_nur_no"/></td>
					</table>
					</td> 
				    <td align="right">&nbsp;</td>
				</tr>
				<tr><td colspan="8"><img src="images/tpixel.gif" width="1" height="5"></td></tr>
		        
		  </table>
		  <tr>
			<td> <img src="images/tpixel.gif" width="1" height="10"></td>
		  </tr>
		  <tr>
		<td valign="top">
			<table width="99%" border="0" align="center" cellpadding="0" cellspacing="0">
				<tr >
					<td align="right" valign="top">
						<input type="button" class="button" style="width:70px; " value="Save">
						<input type="button" class="button" style="width:70px; " value="Cancel">
						<input type="button" class="button" style="width:70px; " value="Print">
						<input type="button" class="button" style="width:70px; " value="Save & Print"></td>
					<td align="right" valign="top"><img src="images/logo1.gif" width="168" height="24"></td>
				</tr>	
		  </table>		
		</td>
	</tr>
			    </td>
			 </tr>
		  </form>
</table>
<script>
	top.setPNotesHeight();
</script>
			   
			   </form>
			   <script>
	top.setPNotesHeight();
</script>
	  
	  
		