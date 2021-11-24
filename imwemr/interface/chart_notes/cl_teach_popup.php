<?php
/*
// The MIT License (MIT)
// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
*/
?>
<?php
include_once('../../config/globals.php');
include_once($GLOBALS['fileroot'].'/library/classes/common_function.php');
include_once($GLOBALS['fileroot'].'/library/classes/cls_common_function.php');
include_once($GLOBALS['fileroot'].'/library/classes/class.language.php');
include_once($GLOBALS['fileroot'].'/library/classes/work_view/wv_functions.php');
include_once($GLOBALS['fileroot'].'/interface/chart_notes/cl_functions.php'); 
include_once($GLOBALS['fileroot'].'/library/classes/work_view/Patient.php');

// code To save CLTEACh option//
$lensBlockId = isset($_POST['lensBlockId']) ? $_POST['lensBlockId'] : false;
//$gloabalDateFormatSQL = getSqlDateFormat();
$gloabalDateFormatSQL = date("m-d-Y");
$saved = false;
if ($_REQUEST['recordSave'] == "saveTrue") {

    $chkClsQry = "SELECT clTeachId FROM clteach WHERE patient_id='" . $_REQUEST['patient_id'] . "' and workSheetID='" . $_REQUEST["workSheetID"] . "'";
    $chkClsRes = imw_query($chkClsQry) or die(imw_error());
    $chkClsNumRow = imw_num_rows($chkClsRes);
    $insUpdtQry = 'INSERT INTO ';
    $whereQry = '';
    if ($chkClsNumRow > 0) {
        $insUpdtQry = 'UPDATE ';
        $whereQry = "WHERE patient_id='" . $_REQUEST['patient_id'] . "' and workSheetID='" . $_REQUEST["workSheetID"] . "' ";
    }

    $inssql = $insUpdtQry . " `clteach` set ";
    $inssql .= "patient_id='" . addslashes($_REQUEST["patient_id"]) . "',
		provider_id='" . addslashes($_REQUEST["provider_id"]) . "', 
		schedule_cltech_chk='" . addslashes($_REQUEST["schedule_cltech_chk"]) . "',
		multipurpose_cltech_chk='" . addslashes($_REQUEST["multipurpose_cltech_chk"]) . "', 
		peroxide_cltech_chk='" . addslashes($_REQUEST["peroxide_cltech_chk"]) . "',
		enzyme_cltech_chk='" . addslashes($_REQUEST["enzyme_cltech_chk"]) . "', 
		clwearingtime_dw_cltech_chk='" . addslashes($_REQUEST["clwearingtime_dw_cltech_chk"]) . "',
		clwearingtime_fw_cltech_chk='" . addslashes($_REQUEST["clwearingtime_fw_cltech_chk"]) . "',
		clwearingtime_ew_cltech_chk='" . addslashes($_REQUEST["clwearingtime_ew_cltech_chk"]) . "',
		clwearingtime_fwparttime_cltech_chk='" . addslashes($_REQUEST["clwearingtime_fwparttime_cltech_chk"]) . "',
		clwearingtime_hrs_cltech_txt='" . addslashes($_REQUEST["clwearingtime_hrs_cltech_txt"]) . "', 
		contactsfortech_clbin_cltech_chk='" . addslashes($_REQUEST["contactsfortech_clbin_cltech_chk"]) . "',
		contactsfortech_trialdisplays_cltech_chk='" . addslashes($_REQUEST["contactsfortech_trialdisplays_cltech_chk"]) . "', 
		contactsfortech_other_cltech_chk='" . addslashes($_REQUEST["contactsfortech_other_cltech_chk"]) . "', 
		contactsfortech_other_cltech_txt='" . addslashes($_REQUEST["contactsfortech_other_cltech_txt"]) . "', 
		contactsfortech_Pt_instructed_txt='" . addslashes($_REQUEST["contactsfortech_Pt_instructed_txt"]) . "', 
		contactsfortech_Pt_instructed_txtarea='" . addslashes($_REQUEST["contactsfortech_Pt_instructed_txtarea"]) . "', 
		wearingtime_cltech_sel='" . addslashes($_REQUEST["wearingtime_cltech_sel"]) . "', 
		followup_cltech_txt='" . addslashes($_REQUEST["followup_cltech_txt"]) . "', 
		anotherCLTech_cltech_txt='" . addslashes($_REQUEST["anotherCLTech_cltech_txt"]) . "',
		workSheetID='" . addslashes($_REQUEST["workSheetID"]) . "',
		technician_id='" . addslashes($_REQUEST["technician_id"]) . "',
		wearingtime_select='" . addslashes($_REQUEST["wearingtime_select"]) . "',
		saveDateTime=now() " . $whereQry . "
		";

    $ressulsuccess = imw_query($inssql) or print(imw_error());
    if ($ressulsuccess) {
        $saved = true;
    }
}


// end Code To save cl teach option
//START
$authUserID = $_SESSION['authUserID'];
$qry_user = "SELECT * from users where id = '$authUserID'";
$res_user = imw_query($qry_user);
$row = imw_fetch_array($res_user);
$fname = $row['fname'];
$mname = $row['mname'];
$lname = $row['lname'];
if ($mname != '' && $mname != 'NULL') {
    $physicianLoggedname = $fname . "&nbsp;" . $mname . "&nbsp;" . $lname;
} else {
    $physicianLoggedname = $fname . "&nbsp;" . $lname;
}
$tech_comORPhycian = false;
$LOGGEDtechnician_id = "";
$LOGGEDphysician_id = "";
if (in_array($row['user_type'], $GLOBALS['arrValidCNPhy']) || in_array($row['user_type'], $GLOBALS['arrValidCNTech'])) {
    $tech_comORPhycian = true;
    if (in_array($row['user_type'], $GLOBALS['arrValidCNPhy'])) {
        $provider_id = $_SESSION['authUserID']; //Physcian ID
        $technician_id = $_SESSION['authUserID'];
        $techName = $physicianLoggedname;
        $LOGGEDphysician_id = $provider_id;
        $disableCLTech = "";
        $disableCLPhy = "";
    }
    if (in_array($row['user_type'], $GLOBALS['arrValidCNTech'])) {
        $disableCLTech = "";
        //$disableCLPhy ="readonly";
        $provider_id = $_SESSION['authUserID'];
        $technician_id = $_SESSION['authUserID'];
        $LOGGEDtechnician_id = $technician_id;
    }
} else {
    $physicianLoggedname = "";
    $techName = "";
    $provider_id = $_SESSION['authUserID'];
    $technician_id = $_SESSION['authUserID'];

    //$disableCLTech="readonly";
    //$disableCLPhy ="readonly";
}
//Get Saved data//

if ($_SESSION['patient'] && $_REQUEST["workSheetID"] != "") {
    $GetDataQuery = "SELECT *, DATE_FORMAT( `saveDateTime` , '$gloabalDateFormatSQL' ) AS saveDateTimeShow FROM clteach WHERE patient_id='" . $_SESSION['patient'] . "'  and workSheetID='" . $_REQUEST["workSheetID"] . "'";
    $GetDataRes = imw_query($GetDataQuery) or die(imw_error());
    $GetDataNumRow = imw_num_rows($GetDataRes);
    if ($GetDataNumRow > 0) {
        $resRow = imw_fetch_assoc($GetDataRes);
        @extract($resRow);
        if ($LOGGEDtechnician_id != "") {
            $technician_id = $LOGGEDtechnician_id;
        }
        /* if($LOGGEDphysician_id!=""){
          $provider_id=$LOGGEDphysician_id;
          } */
    }
}
//End To get Saved Data//


?>
<!DOCTYPE html>
<html>
    <head>
        <?php if ($saved == true) { ?>
            <script type="text/javascript">
                alert('CL Teach data saved successfully.');
                <?php if($lensBlockId) { ?>
                    var lensBlockId = <?php echo $lensBlockId; ?>;
                    //$('#cl_teach_popup_'+lensBlockId).modal('toggle');
                <?php } ?>
            </script>
        <?php } ?>
        <title>CONTACT LENS CL Teach</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, maximum-scale=1.0" />
        <style>
            .la_sel_lts{
                background:url(images/la_sel_left.png); background-repeat:no-repeat; height:25px;width:7px;
            }
            .la_sel_mds2{
            }
            .la_sel_mds{ background:#999999;}
            .la_sel_rts{ background-image:url(images/la_sel_right.png); background-repeat:no-repeat; height:25px;width:7px;}
            .la_bg2{
                background-color:#3F7696;
                background-attachment: scroll;
                background-repeat: repeat-x;
                background-position: left;
            }
            .model_width {
                width: 1400px !important;
            }
        </style>

        <?php
        if ($ressulsuccess) {
            echo("<script>window.close(); </script>");
        }
        ?>
<link href="<?php echo $GLOBALS['webroot'];?>/library/css/jquery-ui.min.css" type="text/css" rel="stylesheet">
<link href="<?php echo $GLOBALS['webroot'];?>/library/css/bootstrap.min.css" type="text/css" rel="stylesheet">
<link href="<?php echo $GLOBALS['webroot'];?>/library/css/bootstrap-select.css" type="text/css" rel="stylesheet">
<link href="<?php echo $GLOBALS['webroot'];?>/library/css/common.css" type="text/css" rel="stylesheet">
<link href="<?php echo $GLOBALS['webroot'];?>/library/css/accounting.css" type="text/css" rel="stylesheet">
<link href="<?php echo $GLOBALS['webroot'];?>/library/css/jquery.datetimepicker.min.css" type="text/css" rel="stylesheet">
<link rel="stylesheet" href="<?php echo $GLOBALS['webroot'] ?>/library/messi/messi.css">
<?php /* HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries */ ?>
    </head>
    <body topmargin=0 rightmargin=0 leftmargin=0 bottommargin=0 class="scrol_la_color" style="background:#ffffff;">
    	<div class="mainwhtbox contlns">
    	<form action="../chart_notes/cl_teach_popup.php" method="post" name="frmContactlensNew">
    		<input type="hidden" name="recordSave" id="recordSave" value="saveTrue">
            <input type="hidden" name="patient_id" id="patient_id" value="<?php echo($_SESSION['patient']); ?>">
            <input type="hidden" name="provider_id" id="provider_id" value="<?php echo($provider_id); ?>">
            <input type="hidden" name="technician_id" id="technician_id" value="<?php echo($technician_id); ?>">
            <input type="hidden" name="lensBlockId" id="lensBlockId" value="<?php echo $lensBlockId ? $lensBlockId : ''; ?>">
            <input type="hidden" name="workSheetID" id="workSheetID" value="<?php echo($workSheetID); ?>">
    		<div class="purple_bar">CL Teach</div>
				<div class="cltech"><div class="row">
					<div class="col-xs-4"><?php echo($physicianLoggedname); ?></div>
                    <div class="col-xs-4"><input <?php print($disableCLPhy); ?> type="checkbox" name="schedule_cltech_chk" value="Yes" <?php
                        if ($schedule_cltech_chk == "Yes")
                        {
                            echo("Checked");
                        }
                        ?> />&nbsp;Schedule CL Teach</div>
                    <div class="col-xs-4">Date:<?php
                            if ($saveDateTimeShow) {
                                echo($saveDateTimeShow);
                            } else {
                                echo date("m-d-Y");
                            }
                            ?></div>
				</div></div>
                <div class="row">
                <div class="col-xs-4">
              <div class="cltech2">  <span>Doctor Recommends</span>
                <div class="row cladocrec">
                <div class="col-xs-5">Multipurpose: </div>
               <div class="col-xs-7"> <input <?php print($disableCLPhy); ?> type="text" value="<?php echo($multipurpose_cltech_chk); ?>" name="multipurpose_cltech_chk" class="txt_10" /></div>
                </div>
                <div class="row cladocrec">
                <div class="col-xs-5">Peroxide: </div>
               <div class="col-xs-7"> <input <?php print($disableCLPhy); ?> type="text"   value="<?php echo($peroxide_cltech_chk); ?>" name="peroxide_cltech_chk" class="txt_10" /></div>
                </div>
                <div class="row cladocrec">
                <div class="col-xs-5">Enzyme:</div>
						<div class="col-xs-7"><input <?php print($disableCLPhy); ?> type="text"  value="<?php echo($enzyme_cltech_chk); ?>" name="enzyme_cltech_chk" class="txt_10" /></div>
                </div></div>
                
                </div>
                <div class="col-xs-4"><div class="cltech2"><span>CL Wearing Time</span>
                <div class="clearfix"></div>
                <div><input <?php print($disableCLPhy); ?> type="checkbox" name="clwearingtime_dw_cltech_chk" value="dw" <?php
                        if ($clwearingtime_dw_cltech_chk == "dw"){
                            echo("Checked");
                        } ?> />
                        &nbsp;DW (No Overnight)</div>
                        
                        
                <div class="clearfix"></div>        
                <div><input <?php print($disableCLPhy); ?> type="checkbox" name="clwearingtime_ew_cltech_chk" value="ew" <?php
                        if ($clwearingtime_ew_cltech_chk == "ew") {
                            echo("Checked");
                        }
                        ?> />
						&nbsp;FW (Occasional Overnight)</div>
                <div class="clearfix"></div>        
                <div>
                <input <?php print($disableCLPhy); ?> type="checkbox" name="clwearingtime_ew_cltech_chk" value="ew" <?php
                        if ($clwearingtime_ew_cltech_chk == "ew") {
                            echo("Checked");
                        }
                        ?> />
						EW (Overnight)
                
                </div>
                <div class="clearfix"></div>        
                <div><input <?php print($disableCLPhy); ?> type="checkbox" name="clwearingtime_fwparttime_cltech_chk" value="parttime" <?php
                        if ($clwearingtime_fwparttime_cltech_chk == "parttime") {
                            echo("Checked");
                        }
                        ?> />
                        Part Time
						<input <?php print($disableCLPhy); ?> type="text" name="clwearingtime_hrs_cltech_txt" size="2" value="<?php echo($clwearingtime_hrs_cltech_txt); ?>" class="txt_10"/> hrs.</div>
                </div>
                </div>
                <div class="col-xs-4"><div class="cltech2"><span>Contacts for Tech</span>
                <div class="clearfix"></div>        
                <div><input <?php print($disableCLPhy); ?> type="checkbox" name="contactsfortech_clbin_cltech_chk" value="ClBin" <?php
                        if ($contactsfortech_clbin_cltech_chk == "ClBin") {
                            echo("Checked");
                        } ?> />
						&nbsp;Cl Bin</div>
                <div class="clearfix"></div>        
                <div><input <?php print($disableCLPhy); ?>  type="checkbox" name="contactsfortech_trialdisplays_cltech_chk" value="trial_displays" <?php
                        if ($contactsfortech_trialdisplays_cltech_chk == "trial_displays") {
                            echo("Checked");
                        }
                        ?> />
                        &nbsp;Trial Displays</div>
                 <div class="clearfix"></div>        
                <div><input <?php print($disableCLPhy); ?> type="checkbox" name="contactsfortech_other_cltech_chk" value="Other_contactsfortec" <?php
                        if ($contactsfortech_other_cltech_chk == "Other_contactsfortec")
                        {
                            echo("Checked");
                        }
                        ?> />
                        Other
						<input <?php print($disableCLPhy); ?> type="text" name="contactsfortech_other_cltech_txt" value="<?php echo($contactsfortech_other_cltech_txt); ?>" style="width:50px;" class="txt_10" /></div>
                 <div class="clearfix"></div>        
                             
                </div>
                </div>
                
                </div>
				
				
				<div class="row">
				
					<div class="col-sm-3">
						
					</div>
					<div class="col-sm-3">
						
					</div>
    			</div>
    			<div class="row">
    				
					<div class="col-sm-3">
						
					</div>
					<div class="col-sm-3">
						
					</div>
    			</div>
    			<div class="row">
    				
					<div class="col-sm-3">
						
					</div>
					<div class="col-sm-3">
						
					</div>
    			</div>
    			<div class="row">
    				<div class="col-sm-3"></div>
    				<div class="col-sm-3">
    					
    				</div>
    				<div class="col-sm-3"></div>
    				<div class="col-sm-3"></div>
    			</div>
    			<div class="row">
    				<div class="col-sm-9">
    					Pt. instructed on I+R&nbsp;<input  <?php print($disableCLPhy); ?> type="text" name="contactsfortech_Pt_instructed_txt" size="2" value="<?php echo($contactsfortech_Pt_instructed_txt); ?>" class="txt_10" />&nbsp;x each eye w/o incident
    				</div>
    			</div>
    			<div class="ptinstru"><textarea <?php print($disableCLPhy); ?> name="contactsfortech_Pt_instructed_txtarea" rows="2"  class="txt_10"><?php echo($contactsfortech_Pt_instructed_txtarea); ?></textarea></div>
                <div class="clearfix"></div>
                <div class="techadmin">
                <div class="techhead"><div class="row">
                <div class="col-xs-9">Technician: <?php echo($techName); ?></div>
                <div class="col-xs-3">Date:&nbsp;
    					<?php
    					if ($saveDateTimeShow)
    					{
                            echo($saveDateTimeShow);
                        }
                        else
                        {
                            echo date("m-d-Y");
                        }
                        ?></div>
                
                </div></div>
                
               <div class="row"> <div class="col-xs-4">Pt. to Increase Wearing Time:
                <select name="wearingtime_cltech_sel" class="txt_10" style="background:#FFF;color:#000000;width:100px;" <?php print($disableCLTech); ?>>
                            <option value="2" <?php
                            if ($wearingtime_cltech_sel == "2") {
                                echo("selected");
                            }
                            ?>>2hrs</option>
                            <option value="4" <?php
                            if ($wearingtime_cltech_sel == "4") {
                                echo("selected");
                            }
                            ?>>4hrs</option>
                            <option value="6" <?php
                            if ($wearingtime_cltech_sel == "6") {
                                echo("selected");
                            }
                            ?>>6hrs</option>
                            <option value="8" <?php
                            if ($wearingtime_cltech_sel == "8") {
                                echo("selected");
                            }
                            ?>>8hrs</option>
                            <option value="other" <?php
                            if ($wearingtime_cltech_sel == "other") {
                                echo("selected");
                            }
                            ?>>Other</option>
                        </select>
                        </div>
                 <div class="col-xs-4">
                 Pt. has followup to see Doctor in:
                 <input <?php print($disableCLTech); ?> type="text" name="followup_cltech_txt" value="<?php echo($followup_cltech_txt); ?>" size="7" class="txt_10" style="color:#000000;" />&nbsp;Weeks
                 </div>
                  <div class="col-xs-4 claptned" >
                  Pt. needs another CL Teach:&nbsp;
                  <input <?php print($disableCLTech); ?> type="text" name="anotherCLTech_cltech_txt" value="<?php echo($anotherCLTech_cltech_txt); ?>" size="7" class="txt_10" style="color:#000000;" />
    					<select name="wearingtime_select" class="txt_10" <?php print($disableCLTech); ?> style="width:100px;">
                            <option value="Weeks" <?php
                            if ($wearingtime_select == "Weeks") {
                                echo("selected");
                            }
                            ?>>Weeks</option>
                            <option value="Days" <?php
                            if ($wearingtime_select == "Days") {
                                echo("selected");
                            }
                            ?> >Days</option>
                        </select>
                  </div></div>
                
                
                </div>
    			
    			
    			
    			<div class="row" style="margin-top:20px;">
    				<div class="col-sm-6 ">
    					<input type="button" style="margin-right:5px;" class="btn btn-success pull-right"  id="DoneBtn" <?php print(($disableCLTech == "readonly") ? "disabled" : ""); ?>  value="Done"  data-dismiss="modal" onClick="this.form.submit();"/>
    				</div>
    				<div class="col-sm-6">
						<input type="button"  class="btn btn-success" data-dismiss="modal" id="CloseSave" value="Cancel" onClick="window.close();">
					</div>
    			</div>
    		</form>
    	</div>
    </body>
   </html>