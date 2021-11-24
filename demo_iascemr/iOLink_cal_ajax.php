<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
session_start();
header("Cache-control: private, no-cache"); 
header("Expires: Mon, 26 Jun 1997 05:00:00 GMT"); 
header("Pragma: no-cache");

include_once("common/conDb.php");
include_once("admin/classObjectFunction.php");
$objManageData = new manageData;
$loginUser = $_SESSION['loginUserId'];
$selected_month_number=$_REQUEST["sel_month_number"];
$year_now=$_REQUEST["year_now"];
$reqUserId= $_REQUEST['reqUserId'];

include("common/link_new_file.php");
include("common/iOlinkFunction.php");
$practiceName = iOLinkPracticeName($loginUser,'Coordinator');

?>

<td class="text_10" id="iOLink_cal_ajax_id">
	<table width="100%" border="0" cellpadding="0" cellspacing="0" >
		<tr valign="top">
			<td width="1" class=""><img border="0" src="images/left_new.gif"></td>
			<td class="text_10b all_border" width="100%">
				<?php 
				//CODE FOR CALENDER
					if(!$year_now) { $year_now=date('Y'); }
					if($_REQUEST["sel_month_number"]<>""){
						$selected_month_number=$_REQUEST["sel_month_number"];
						$year_now=$_REQUEST["year_now"];
						if($selected_month_number>12) {
							$selected_month_number = 1;
							if(strlen($selected_month_number)==1) {
								$selected_month_number = '0'.$selected_month_number;
							}
							$year_now = $year_now+1;
						}
						if($selected_month_number==0) {
							$selected_month_number = 12;
							$year_now = $year_now-1;
						}
					}else{	
						$selected_month_number = date("m");
						$year_now = date("Y");
					}
					$selected_month_number_IncrByOne = $selected_month_number+1;//date("m",mktime(0,0,0,$selected_month_number+1,1,$year_now));
					$selected_month_number_DecrByOne = $selected_month_number-1;//date("m",mktime(0,0,0,$selected_month_number-1,1,$year_now));
					$year_now_IncrByOne = $year_now+1;//date("Y",mktime(0,0,0,$selected_month_number,1,$year_now+1));
					$year_now_DecrByOne = $year_now-1;//date("Y",mktime(0,0,0,$selected_month_number,1,$year_now-1));
					$lastday=date("t",mktime(0,0,0,$selected_month_number,1,$year_now));
					$weekday=date("w",mktime(0,0,0,$selected_month_number,1,$year_now));
					if($weekday==0) {
						$weekday = 7;
					}
					$days=array("Mon","Tue","Wed","Thu","Fri","Sat","Sun");
					$month_name = date("F",mktime(0,0,0,$selected_month_number,1,$year_now));
				//CODE FOR CALENDER
				?>	
				
				<table border="0" cellpadding="0" cellspacing="0" width="100%">
					<tr height="22">
						<?php $reqUserId= $_REQUEST['reqUserId'];?>
						<td width="20%" nowrap class="text_10b" bgcolor="#c0aa1e" style=" font-size:12px;">
							Surgeon&nbsp;<select name="surgeon_name_id" id="surgeon_name_id" class="field text_10" onChange="javascript:iOLink_change_month(<?php echo $selected_month_number;?>,'<?php echo $year_now;?>',document.getElementById('surgeon_name_id').value);"  style=" font-size:11px;width:120px;border:1px solid #cccccc;">
								<?php
								unset($conditionArr);
								$conditionArr['user_type']='Surgeon';
								$conditionArr['practiceName']=$practiceName;
								$getSurgeosDetails = $objManageData->getMultiChkArrayRecords('users', $conditionArr, 'lname','ASC');
								if(count($getSurgeosDetails)>=2) {
								?>
									<option value="">All Surgeon</option>
								<?php
								}
								
								foreach($getSurgeosDetails as $surgeonsList){
									$usersId = $surgeonsList->usersId;
									$surgeonFname = trim($surgeonsList->fname);
									$surgeonLname = trim($surgeonsList->lname);
									$surgeonMname = trim($surgeonsList->mname);
									if($surgeonMname) {
										$surgeonMname = ' '.$surgeonMname;
									}
									$surgeonName = $surgeonFname.$surgeonMname.' '.$surgeonLname;
									$surgeon_deleteStatus = $surgeonsList->deleteStatus;
									if($surgeon_deleteStatus=="Yes") {
									}else{
									?>
										<option value="<?php echo $usersId; ?>" <?php if($reqUserId==$usersId) { echo 'selected'; }?>><?php echo stripslashes($surgeonLname.', '.$surgeonFname.' '.$surgeonMname); ?></option>
									<?php
									}
								}
								?>
							</select>&nbsp;
						</td>
						<td width="20%"  align="left" class="text_10" bgcolor="#c0aa1e">
							<table align="center" border="0" cellpadding="0" cellspacing="0" >
								<tr>
									<td align="center">
										<a href="javascript:iOLink_change_month(<?php echo $selected_month_number;?>,'<?php echo $year_now_DecrByOne;?>',document.getElementById('surgeon_name_id').value);">
											<img border="0" src="images/cal2_back.jpg" width="15" height="15" >
										</a>		
									</td>
									<td width="5">&nbsp;</td>
									<td align="center">
										<a href="javascript:iOLink_change_month(<?php echo $selected_month_number_DecrByOne;?>,'<?php echo $year_now;?>',document.getElementById('surgeon_name_id').value);">
											<img border="0" src="images/cal_back.jpg" width="15" height="15">
										</a>
									</td>
									<td width="5">&nbsp;</td>					
									<td align="center"  class="text_10b" nowrap style="font-size:9px; ">
										<select class="text_10" name="monthList" id="monthList" onChange="javascript:iOLink_change_month(this.value,'<?php echo $year_now;?>',document.getElementById('surgeon_name_id').value);" style=" font-size:10px;width:90px;border:1px solid #cccccc;">
											<?php
											for($k=1;$k<=12;$k++) {
												$monthListValue = date("F",mktime(0,0,0,$k,1,$year_now));
												if(strlen($k)==1) { $k='0'.$k;}
												if(strlen($selected_month_number)==1) { $selected_month_number='0'.$selected_month_number;}
											?>
												<option value="<?php echo $k;?>" <?php if($selected_month_number==$k) { echo "selected"; }?>><?php echo $monthListValue;?></option>
											<?php
											}
											?>	
										</select>
										
										
									</td>
									<td width="5" class="text_10b" style="font-size:12px; ">&nbsp;<?php echo $year_now;?>&nbsp;</td>
									<td align="center">
										<a href="javascript:iOLink_change_month(<?php echo $selected_month_number_IncrByOne;?>,'<?php echo $year_now;?>',document.getElementById('surgeon_name_id').value);">
											<img border="0" src="images/cal_next.jpg" width="15" height="15">
										</a>
									</td>
									<td width="5">&nbsp;</td>
									<td align="center">
										<a href="javascript:iOLink_change_month(<?php echo $selected_month_number;?>,'<?php echo $year_now_IncrByOne;?>',document.getElementById('surgeon_name_id').value);">
											<img border="0" src="images/cal2_next.jpg" width="15" height="15">
										</a>
									</td>
								</tr>
							</table>
						</td>
						<td  align="left" class="text_10" bgcolor="#c0aa1e"></td>
					</tr>
				</table>
			</td>
			<td width="1" class=""><img border="0" src="images/right_new.gif"></td>
			
			
			
		</tr>	
		<tr valign="top">
			<!-- 
			<td colspan="3">
				This is <b>iOLink</b> Scheduler
			</td>	 
			-->
			<td colspan="3">
				<table border="0" width="100%" height="100%" align="center" cellpadding="0" cellspacing="2" >
					<Tr bgcolor="#F8F9F7" class="text_homeb" height="10">
						<?php 
						for($i=0;$i<7;$i++){?>
							<Td class="text_10"><?php echo $days[$i];?></Td>
							<?php 
						}
						?>
					</Tr>
					<TR align='right' valign='top' bgcolor='#FFFFFF' height='47'>
						<?php 
						$j=1;
						$weekday2 = 0;
						$days = false;
						$p = 1;
						$emptyBlocks = 0;
						$calHTML='';
						$rowCount = 1;	
						$intLastDisplay = 0;			
						while($p<$lastday){
							if($days == true){
								$p++;
							}
							$selDos=$year_now.'-'.$selected_month_number.'-'.$p;
							if($j<=7){				
								$weekday2++;
								if($p==date("d")){
									//$color="#ECF1EA";
									$color="#FBD78D";
								}else{
									$color="";
								}
								if($weekday2==$weekday || $days == true){				
									$days = true;
									
									$calHTML .= "<TD aligh='left' width='50' id='mon_".$p."' bgcolor='$color' onMouseOver='javascript:iOLink_swap_cal_color(this.id,\"Yes\");'  onMouseOut='javascript:iOLink_swap_cal_color(this.id,\"No\");'  >
													".getFirstSurgeryTime($selDos,$reqUserId,'makeDivYes',$practiceName).
													"<table align='left' border='0' cellpadding='0' cellspacing='0' width='100%'>
														<tr>
															<td width='90%' align='left' class='text_10' style='font-size:9px; cursor:hand; ' onMouseOver='javascript:iOLinkDisplayTimeDiv(parseInt(findPos_X(\"mon_".$p."\"))+40,parseInt(findPos_Y(\"mon_".$p."\"))+30,\"iOLinkSurgeryTimeId".$p."\",\"Yes\");' onMouseOut='javascript:iOLinkDisplayTimeDiv(parseInt(findPos_X(\"mon_".$p."\")),parseInt(findPos_Y(\"mon_".$p."\")),\"iOLinkSurgeryTimeId".$p."\",\"No\");'>".getFirstSurgeryTime($selDos,$reqUserId,'',$practiceName)."</td>
												 			<td width='10%' valign='top' align='right' class='text_10'><a class='link_home text_10' href='javascript:void(0);' onClick='javascript:schClick(\"$year_now\",\"$selected_month_number\",\"$p\",\"$reqUserId\");'>$p</a></td>
														</tr>
													</table>		
												 </td>";	
								}else{
									$emptyBlocks++;
									$calHTML .= "<TD aligh='left' width='50' >{".$emptyBlocks."}</td>";	
								}
								if($j%7==0){
									$calHTML .= "</Tr><tr align='right' valign='top' height='47' bgcolor='#FFFFFF'>";
								}
							}else{	
								if($p==date("d")){
									$color="#FBD78D";
								}else{
									$color="";
								}
								if($rowCount <= 4){
									$calHTML .= "<TD aligh='left' width='50'  id='mon_".$p."'  onMouseOver='javascript:iOLink_swap_cal_color(this.id,\"Yes\");'  onMouseOut='javascript:iOLink_swap_cal_color(this.id,\"No\");' bgcolor=$color>
													".getFirstSurgeryTime($selDos,$reqUserId,'makeDivYes',$practiceName).
													"<table align='left' border='0' cellpadding='0' cellspacing='0' width='100%'>
														<tr>
															<td width='90%' align='left' class='text_10' style='font-size:9px; ' onMouseOver='javascript:iOLinkDisplayTimeDiv(parseInt(findPos_X(\"mon_".$p."\"))+40,parseInt(findPos_Y(\"mon_".$p."\"))+30,\"iOLinkSurgeryTimeId".$p."\",\"Yes\");' onMouseOut='javascript:iOLinkDisplayTimeDiv(parseInt(findPos_X(\"mon_".$p."\")),parseInt(findPos_Y(\"mon_".$p."\")),\"iOLinkSurgeryTimeId".$p."\",\"No\");'>".getFirstSurgeryTime($selDos,$reqUserId,'',$practiceName)."</td>
												 			<td width='10%' valign='top' align='right' class='text_10'><a class='link_home text_10' href='javascript:void(0);' onClick='javascript:schClick(\"$year_now\",\"$selected_month_number\",\"$p\",\"$reqUserId\");'>$p</a></td>
														</tr>
													</table>		
									
												</td>";
									$intLastDisplay = $p;
									if($j%7==0){
										$rowCount ++;
										$calHTML .= "</Tr><tr align='right' valign='top' bgcolor='#FFFFFF' height='47' >";
									}
								}
							}
							$j++;
						}
						$totalBlocks = $emptyBlocks + $lastday;
						$totalRows = ceil($totalBlocks / 7);
						if($totalRows > 5){
							$r = 1;					
							while($lastday > $intLastDisplay){							
								if($lastday==date("d")){
									$color="#FBD78D";
								}else{
									$color="";
								}
								$intLastDisplay++;
								$selDosLastDays=$year_now.'-'.$selected_month_number.'-'.$intLastDisplay;
								$calHTML = str_replace(">{".$r."}",
											"id='mon_".$intLastDisplay."' bgcolor='$color' onMouseOver='javascript:iOLink_swap_cal_color(this.id,\"Yes\");'  onMouseOut='javascript:iOLink_swap_cal_color(this.id,\"No\");'  >
											".getFirstSurgeryTime($selDosLastDays,$reqUserId,'makeDivYes',$practiceName).
											"<table align='left' border='0' cellpadding='0' cellspacing='0' width='100%'>
												<tr>
													<td width='90%' align='left' class='text_10' style='font-size:9px; ' onMouseOver='javascript:iOLinkDisplayTimeDiv(parseInt(findPos_X(\"mon_".$intLastDisplay."\"))+40,parseInt(findPos_Y(\"mon_".$intLastDisplay."\"))+30,\"iOLinkSurgeryTimeId".$intLastDisplay."\",\"Yes\");' onMouseOut='javascript:iOLinkDisplayTimeDiv(parseInt(findPos_X(\"mon_".$intLastDisplay."\")),parseInt(findPos_Y(\"mon_".$intLastDisplay."\")),\"iOLinkSurgeryTimeId".$intLastDisplay."\",\"No\");'>".getFirstSurgeryTime($selDosLastDays,$reqUserId,'',$practiceName)."</td>
													<td width='10%' valign='top' align='right' class='text_10'><a class='link_home text_10' href='javascript:void(0);' onClick='javascript:schClick(\"$year_now\",\"$selected_month_number\",\"$intLastDisplay\",\"$reqUserId\");'>".$intLastDisplay."</a></td>
												</tr>
											</table>",
											$calHTML);
								$r++;
							}
						}
						$calHTML = preg_replace("/{[0-9]}/","",$calHTML);
						echo $calHTML;
					
					?>
				</table>
			</td>
		</tr>
	</table>
</td>