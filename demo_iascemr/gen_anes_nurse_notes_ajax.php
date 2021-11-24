<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
header("Cache-control: private, no-cache"); 
header("Expires: Mon, 26 Jun 1997 05:00:00 GMT"); 
header("Pragma: no-cache");

	include_once("common/conDb.php");
include_once("admin/classObjectFunction.php");
$objManageData = new manageData;
	include_once("common/commonFunctions.php");
	include("common/link_new_file.php");
		
	//SAVE NEW NOTES TIME AND DESCRIPTION
			$ajax_newNotesTime = $objManageData->getTmFormat(date("H:i:s"));
			$ajax_newNotesDesc = addslashes($_GET['txt_areaNewNotesDesc']);
			$patient_id					=	$_GET["patient_id"];
			$confirmation_id		=	$_GET["pConfId"];
			
			$SaveNewNotesAjaxQry = "insert into `genanesthesianursesnewnotes` set 
										newnotes_time = '".$ajax_newNotesTime."',
										newnotes_desc = '".$ajax_newNotesDesc."', 										
										confirmation_id='".$confirmation_id."',
										patient_id = '".$patient_id."'";
								//echo $SaveNewNotesAjaxQry;		
			$SaveNewNotesAjaxRes = imw_query($SaveNewNotesAjaxQry) or die(imw_error());
	//END SAVE NEW NOTES TIME AND DESCRIPTION
	?>
		<!-- <td id="newNotesId"  colspan="2"  align="left" > -->
        <table class="col-xs-12 col-md-12 col-lg-12 col-sm-12 table-bordered  table-condensed cf ">
              <tbody>
	<?php
			if(!empty($_REQUEST["pConfId"]))
			{
					$newnotesQry = "select * from `genanesthesianursesnewnotes` where  confirmation_id = '".$_REQUEST["pConfId"]."'";
					$newnotesRes = imw_query($newnotesQry) or die(imw_error()); 
					$newnotesNumRow = imw_num_rows($newnotesRes);
					if($newnotesNumRow>0) 
					{
								while($newnotesRow = imw_fetch_array($newnotesRes)) 
								{
									$newnotes_id = $newnotesRow["newnotes_id"];
									$newnotes_desc = $newnotesRow["newnotes_desc"];
									$newnotes_timeTemp = $newnotesRow["newnotes_time"];
								//CODE TO SET THE TIME									
									$time_split = explode(":",$newnotes_timeTemp);
									if($time_split[0]>=12) {
										$am_pm = "PM";
									}else {
										$am_pm = "AM";
									}
									if($time_split[0]>=13) {
										$time_split[0] = $time_split[0]-12;
										if(strlen($time_split[0]) == 1) {
											$time_split[0] = "0".$time_split[0];
										}
									}else {
										//DO NOTHNING
									}
									$newnotes_time = $time_split[0].":".$time_split[1]." ".$am_pm;
								//END CODE TO SET THE TIME									
									$border_blue_local_anes="#323CC0";
					?>
												<tr>
														<td class="text-left  col-md-2 col-lg-2 col-sm-3 col-xs-3">
                                                                                                		<?php echo $newnotes_time;?>
                                                                                                </td>
                                                                                    			<td class="text-left col-md-7 col-lg-7 col-sm-7 col-xs-7" id="noteEdtId<?php echo $newnotes_id;?>">
																										<?php echo stripslashes($newnotes_desc);?>
                                                                                                </td>
                                                                                                <td  class="col-md-2 col-lg-2 col-sm- col-xs-2 text-center" id="editBtnId<?php echo $newnotes_id;?>">
                                                                                                		<a class="btn btn-primary glyphicon glyphicon-edit margin_0"  name="edit<?php echo $newnotes_id;?>" href="javascript:void(0)"onClick="editEntry('<?php echo $newnotes_id; ?>');"></a>
                                                                                                </td>
                                                                                                <td  class="col-md-1 col-lg-1 col-sm-2 col-xs-1 text-center">
                                                                                                		<a class="btn btn-danger glyphicon glyphicon-remove margin_0"  onClick="return delentry('<?php echo $newnotes_id; ?>');" name="del<?php echo $newnotes_id;?>" href="javascript:void(0)"></a>
                                                                                                </td>
												</tr>
							   
									 
									
							
			<?php
								}
						}	
				}
			?>
            
            </tbody>
         </table>	
		<!-- </td> -->