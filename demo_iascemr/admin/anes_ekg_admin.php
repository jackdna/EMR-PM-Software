<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.

?><?php
include_once("../globalsSurgeryCenter.php");
include_once("logout.php");
?>
<!DOCTYPE html>
<html>
<head>
<title>Ekg Grid Medication</title>
<meta name="viewport" content="width=device-width, maximum-scale=1.0">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<?php
include("adminLinkfile.php");
include_once("classObjectFunction.php");
$objManageData = new manageData;

$anes_ekg_admin_id = '1'; //SET DEFAULT VALUE
$saveRecord = $_POST['saveRecord'];
if($saveRecord=='Yes') {
	unset($arrayRecord);
	
	$arrayRecord['mgPropofol_label'] = $_REQUEST['mgPropofol_label'];
	$arrayRecord['mgMidazolam_label'] = $_REQUEST['mgMidazolam_label'];
	$arrayRecord['mgKetamine_label'] = $_REQUEST['mgKetamine_label'];
	$arrayRecord['mgLabetalol_label'] = $_REQUEST['mgLabetalol_label'];
	$arrayRecord['mcgFentanyl_label'] = $_REQUEST['mcgFentanyl_label'];
	
	$chkAnesEkgAdminTblDetails = $objManageData->getRowRecord('anes_ekg_admin_tbl', 'anes_ekg_admin_id', $anes_ekg_admin_id);
	
	if($chkAnesEkgAdminTblDetails){
		$c=$objManageData->UpdateRecord($arrayRecord, 'anes_ekg_admin_tbl', 'anes_ekg_admin_id', $anes_ekg_admin_id);
	}else{
		$d=$objManageData->addRecords($arrayRecord, 'anes_ekg_admin_tbl');
	}
	if($c)
	{
		echo "<script>top.frames[0].alert_msg('update')</script>";
	}
	if($d)
	{
		echo "<script>top.frames[0].alert_msg('success')</script>";
	}
	
}

//VIEW RECORD FROM DATABASE
	$anesEkgAdminTblDetails = $objManageData->getRowRecord('anes_ekg_admin_tbl', 'anes_ekg_admin_id', $anes_ekg_admin_id);
	if($anesEkgAdminTblDetails) {
		$mgPropofol_label	= $anesEkgAdminTblDetails->mgPropofol_label;
		$mgMidazolam_label 	= $anesEkgAdminTblDetails->mgMidazolam_label;
		$mgKetamine_label	= $anesEkgAdminTblDetails->mgKetamine_label;
		$mgLabetalol_label 	= $anesEkgAdminTblDetails->mgLabetalol_label;
		$mcgFentanyl_label 	= $anesEkgAdminTblDetails->mcgFentanyl_label;
	}	
//END VIEW RECORD FROM DATABASE
?>


</head>

<body>
		<form name="frmSaveAnesEkgAdmin" class="wufoo topLabel" enctype="multipart/form-data" action="anes_ekg_admin.php" method="post">
			<input type="hidden" id="selected_frame_name_id" name="selected_frame_name" value="">
			<input type="hidden" name="divId">
			<input type="hidden" name="counter">
			<input type="hidden" name="secondaryValues">
			<input type="hidden" name="tertiaryValues">
			<input type="hidden" name="anesthesiologistList" value="<?php echo $anesthesiologistList;?>">
			<input type="hidden" name="anesthesiologistId" value="<?php echo $anesthesiologistList;?>">
			<input type="hidden" name="hiddPreDefineId" id="hiddPreDefineId">
			<input type="hidden" name="saveRecord" value="Yes">
			<input type="hidden" id="bp" name="bp_hidden">
			<!--<table class="table_pad_bdr" style="width:90%; border:none;">
				<tr>
					
					<td class="text_10bAdmin all_border" style="width:100%;">
						<table class="table_collapse" style="border:none;" >
							<tr style="height:22px;">
								<td class="alignRight valignBottom" style=" padding:0px;"><img style="border:none; height:22px;" src="../images/left_new.gif"></td>
                                <td class="text_10b" style="width:99%; background-color:#c0aa1e; padding-left:3px;">Anesthesia Intra-Operative Medication</td>
								<td class="text_10b alignLeft" style="background-color:#c0aa1e;"></td>
								<td class="alignLeft" style="padding:0px;"><img style="border:none;" src="../images/right_new.gif"></td>
                            </tr>
						</table>
					</td>
					
				</tr>
				               
				<tr>
					<td class="text_10bAdmin all_border alignLeft" style="width:100%;">
						<table style="border:none; padding:2px; width:100%;" >
							<tr class="valignMiddle" style="height:24px; background-color:#FFFFFF;">
								<td class="text_10" style="width:40px;">Med1</td>
								<td style="width:160px;">
									<input type="text" name="mgPropofol_label" value="<?php echo stripslashes($mgPropofol_label);?>"  class="field text" style=" border:1px solid #ccccc;width:120px; vertical-align:middle;" tabindex="1" />
								</td>
								<td  class="text_10" style="width:40px;">Med2</td>
								<td  style="width:160px;">
									<input type="text" name="mgMidazolam_label" value="<?php echo stripslashes($mgMidazolam_label);?>"  class="field text" style=" border:1px solid #ccccc;width:120px; vertical-align:middle;" tabindex="2" />
								</td>
								<td class="text_10" style="width:40px;">Med3</td>
								<td style="width:160px;">
									<input type="text" name="mgKetamine_label" value="<?php echo stripslashes($mgKetamine_label);?>"  class="field text" style=" border:1px solid #ccccc;width:120px; vertical-align:middle;" tabindex="3" />
								</td>
							</tr>
							<tr>
								<td  class="text_10" style=" width:40px;">Med4</td>
								<td class="alignLeft" >
									<input type="text" name="mgLabetalol_label" value="<?php echo stripslashes($mgLabetalol_label);?>"  class="field text" style=" border:1px solid #ccccc;width:120px; vertical-align:middle;" tabindex="4" />
								</td>
								<td  class="text_10" style="width:40px;">Med5</td>
								<td colspan="3"  class="alignLeft" >
									<input type="text" name="mcgFentanyl_label" value="<?php echo stripslashes($mcgFentanyl_label);?>"  class="field text" style=" border:1px solid #ccccc;width:120px; vertical-align:middle;" tabindex="5" />
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>-->
            
            <div class=" margin_bottom_mid_adjustment scheduler_margins_head">
    	<div class="container-fluid padding_0">
        	<div class="inner_surg_middle ">
					
                  
                  <div style="" id="" class="all_content1_slider ">	         
                          <div class="all_admin_content_agree wrap_inside_admin">
                          <div class=" subtracting-head">
                          <div class="head_scheduler new_head_slider padding_head_adjust_admin">
                                    <span>
                                          Anesthesia Intra-Operative Medication
                                    </span>
                          </div>
                           
                        </div>   
                    	  <Div class="wrap_inside_admin scrollable_yes">
								<div class="form_outer">
                         			<div class="col-lg-1 visible-lg"></div>
                           			<div class="col-md-1 visible-md"></div>
                                    <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
                                        <div class="form_reg">
                                            <div class="row">
                                                <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                                    <label for="mgPropofol_label" class="text-left"> 
                                                          Med1	
                                                    </label>
                                                </div>
                                                <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                                  <input type="text" class="form-control" id="mgPropofol_label"  name="mgPropofol_label" value="<?php echo stripslashes($mgPropofol_label);?>" />
                                                </div> <!----------------------- Full Inout col-12    ------------------------------>
                                            </div><!----------------------- Full wrap Row ------------------------------>
                                        </div><!-------------------Form Reg-----------------------------> 	
                                    </div>
                                    <div class="clearfix  margin_adjustment_only visible-xs"></div>
                                    <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
                                        <div class="form_reg">
                                            <div class="row">
                                                <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                                    <label for="mgMidazolam_label" class="text-left"> 
                                                          Med2	
                                                    </label>
                                                </div>
                                                <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                                  <input type="text" class="form-control" id="mgMidazolam_label" name="mgMidazolam_label" value="<?php echo stripslashes($mgMidazolam_label);?>" />
                                                </div> <!----------------------- Full Inout col-12    ------------------------------>
                                            </div><!----------------------- Full wrap Row ------------------------------>
                                        </div><!-------------------Form Reg-----------------------------> 	
                                    </div>
                                   <div class="clearfix  margin_adjustment_only visible-xs"></div>
                                    <div class="clearfix  margin_adjustment_only visible-sm"></div>
                                     <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
                                        <div class="form_reg">
                                            <div class="row">
                                                <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                                    <label for="mgKetamine_label" class="text-left"> 
                                                          Med3	
                                                    </label>
                                                </div>
                                                <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                                  <input type="text" class="form-control" id="mgKetamine_label" name="mgKetamine_label" value="<?php echo stripslashes($mgKetamine_label);?>" />
                                                </div> <!----------------------- Full Inout col-12    ------------------------------>
                                            </div><!----------------------- Full wrap Row ------------------------------>
                                        </div><!-------------------Form Reg-----------------------------> 	
                                    </div>
							        <div class="clearfix  margin_adjustment_only visible-xs"></div>
                                    <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
                                        <div class="form_reg">
                                            <div class="row">
                                                <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                                    <label for="mgLabetalol_label" class="text-left"> 
                                                          Med4	
                                                    </label>
                                                </div>
                                                <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                                  <input type="text" class="form-control" id="mgLabetalol_label"  name="mgLabetalol_label" value="<?php echo stripslashes($mgLabetalol_label);?>" />
                                                </div> <!----------------------- Full Inout col-12    ------------------------------>
                                            </div><!----------------------- Full wrap Row ------------------------------>
                                        </div><!-------------------Form Reg-----------------------------> 	
                                    </div>
                                   <div class="clearfix  margin_adjustment_only visible-xs"></div>
                                    <div class="clearfix  margin_adjustment_only visible-sm"></div>
                                    <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
                                        <div class="form_reg">
                                            <div class="row">
                                                <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                                    <label for="mcgFentanyl_label" class="text-left"> 
                                                          Med5	
                                                    </label>
                                                </div>
                                                <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                                  <input type="text" class="form-control" id="mcgFentanyl_label" name="mcgFentanyl_label" value="<?php echo stripslashes($mcgFentanyl_label);?>" />
                                                </div> <!----------------------- Full Inout col-12    ------------------------------>
                                            </div><!----------------------- Full wrap Row ------------------------------>
                                        </div><!-------------------Form Reg-----------------------------> 	
                                    </div>
                                </div>
                          </div>		
                     </Div>
                    </div> 
                  </div>  
               </div>
        </div>
		</form>	
</body>
</html>
