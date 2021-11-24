<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.

?><?php

header("Cache-control: private, no-cache"); 
header("Expires: Mon, 26 Jun 1997 05:00:00 GMT"); 
header("Pragma: no-cache");

include_once("../globalsSurgeryCenter.php");
include_once("logout.php");
include_once("funcSurgeryCenter.php");
include_once("classObjectFunction.php");
include_once("../common/user_agent.php");
$objManageData = new manageData;

//print_r($_SESSION);
if($_POST)
{
	$str="";
	if($_POST['headquarter']==1)
	{
		//reset all facilities to headquarter null
		imw_query("update facility_tbl set fac_head_quater=0");
		$str="fac_head_quater=1, ";
	}
	//save new
	if($_POST['form_act']=='New')	
	{
		$tableAct="insert into ";
		$str.="fac_entered_date='".date('Y-m-d')."',
				fac_entered_time='".date('H:i:s')."',
				fac_entered_by='$_SESSION[loginUserId]',";
	}
	//update existing
	elseif($_POST['form_act']=='Update')	
	{
		$tableAct="update ";
		$str.="fac_modified_date='".date('Y-m-d')."',
				fac_modified_time='".date('H:i:s')."',
				fac_modified_by='$_SESSION[loginUserId]',";
		$where=" where fac_id='$_POST[elem_facilityId]'";
	}
	//$idoc_facility=implode(',',$_POST['idoc_facility']);
	$idoc_facility=$_POST['idoc_facility'];
/*	if(!empty($_FILES["elem_surgeryCenterLogo"]["name"])){
		$sql .= "fac_logo = '$elem_surgeryCenterLogo',
				fac_logoName = '$logoName',
				fac_logoType = '$logoType',";
	}*/
	
	imw_query("$tableAct facility_tbl set fac_name='$_POST[sergeryCenerName]',
			fac_npi='$_POST[sergeryCenerNPI]',
			fac_federal_ein='$_POST[sergeryCenerFederal]',
			fac_address1='$_POST[sergeryCenerAddress]',
			fac_address2='$_POST[sergeryCenerAddress2]',
			fac_city='$_POST[elem_city]',
			fac_state='$_POST[elem_state]',
			fac_zip='$_POST[elem_zip]',
			fac_contact_name='$_POST[sergeryCenerContactName]',
			fac_contact_phone='$_POST[sergeryCenerPhone]',
			fac_contact_fax='$_POST[sergeryCenerFax]',
			fac_contact_email='$_POST[sergeryCenerEmail]',
			fac_group_institution = '$_POST[group_institution_list]',
			fac_group_anesthesia = '$_POST[group_anesthesia_list]',
			fac_group_practice = '$_POST[group_practice_list]', $str
			fac_idoc_link_id='$idoc_facility' $where")or die(imw_error());
			
	if($_POST['form_act']=='Update')
	{
		echo "<script>top.frames[0].alert_msg('update');</script>";	
	}
	elseif($_POST['form_act']=='New')
	{
		echo "<script>
		top.frames[0].alert_msg('success');
		top.frames[0].frames[0].frames[0].location = 'facilityRegistration.php';
		</script>";	
	}
}
$facility = $_REQUEST['facility'];
$elem_frmAction = $_POST["elem_frmAction"];
$elem_facilityId = $_POST["elem_facilityId"];
$elem_mode = $_POST["elem_mode"];

$w = 450;
$h =  90;
imw_close($link); //CLOSE SURGERYCENTER CONNECTION
include("../connect_imwemr.php"); // imwemr connection
$groupNewRow = array();
$groupNewQry = "SELECT gro_id,name,del_status FROM groups_new ORDER BY `name` ";
$groupNewRes = imw_query($groupNewQry);
if(imw_num_rows($groupNewRes)>0) {
	while($groupTempRow = imw_fetch_assoc($groupNewRes)) {
		$groupNewRow[] = $groupTempRow;	
	}
}

$query=imw_query("select name,id from facility order by name asc");
while($data=imw_fetch_object($query))
{
	$facility_iDoc[$data->id]=$data->name;
}
//$query.close;
imw_close($link_imwemr); //CLOSE IMWEMR CONNECTION

//reconnect to surgery center database
require("../common/conDb.php");
//USER RECORD
if($facility){
	$getUserDetails = $objManageData->getRowRecord('facility_tbl', 'fac_id', $facility);
	$elem_usersId = $user_id;
	$elem_mode = 2;
	
}

//facility detail
if($facility)
{
	$fac_query=imw_query("select * from facility_tbl where fac_id='$facility'")or die(imw_error());
	$facData=imw_fetch_object($fac_query);
	
}
?>
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, maximum-scale=1.0">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>User Registration</title>
<?php include("adminLinkfile.php");?>
<style>
	form {margin:0px}	
	.sigdrw{border:1px solid orange;display:inline-block;}

</style>

<script type="text/javascript" src="../js/jquery-1.9.0.min.js" ></script>
<script type="text/javascript" src="../js/simple_drawing.js"></script>
</script>


<script>

var LD	=	function()
{
		var T	=	parent.$("#userFrame").height() - $(".head_scheduler").outerHeight(true);
		$('#data-body').css( {'overflow':'hidden', 'overflow-y':'auto', 'min-height': T+'px', 'max-height': T+'px'} );
		//console.log('User Registration Form Height :' T );
}
$(window).load(function(){ LD(); });
$(window).resize(function(e) { LD(); });

<?php if($facility){?>
		top.frames[0].document.getElementById('addNew').style.display = 'none';
		top.frames[0].document.getElementById('deleteSelected').style.display = 'none';
		top.frames[0].document.getElementById('saveButton').style.display = 'inline-block';
		top.frames[0].document.getElementById('cancelButton').style.display = 'inline-block';
<?php }?>			
</script>
</head>
<body onLoad="MM_preloadImages('../images/unlock-_account_hover.gif','../images/lock_account_hover.gif','../images/reset_account_hover.gif','../images/save_hover1.jpg')">

<div class=" wrap_inside_admin">
    <form name="frmFacilityRegistration" id="frmFacilityRegistration" action="" method="post"  style="margin:0px;">
        <input type="hidden" name="frmName" id="frmName" value="Facility Registration">
        <input type="hidden" name="elem_facilityId" id="elem_facilityId" value="<?php  echo $facility;?>">
        <input type="hidden" name="form_act" id="form_act" value="<?php echo($facility)?'Update':'New';?>">
    
        <div class="subtracting-head">	
            <div class="head_scheduler new_head_slider padding_head_adjust_admin ">
                <span>ASC</span>
            </div>
        </div>
        <div class=" wrap_inside_admin " id="dataBody">
          <div class="">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="form_inner_m">
                            <div class="row">
                                <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                    <label for="sergeryCenerName" class="text-left"> 
                                         Name		
                                    </label>
                                </div>
                                <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                    <div class="row">                            
                                        <div class="col-md-4 col-lg-4 col-xs-12 col-sm-4">
                                         <div class="row">                            
                                            <div class="col-md-6 col-lg-6 col-xs-6 col-sm-6">
                                              <input type="text" class="form-control" id="sergeryCenerName" name="sergeryCenerName" value="<?php echo $facData->fac_name; ?>" required>
                                            <small>
                                               ASC Name
                                             </small>
                                          
                                            </div>
                                          <div class="col-md-6 col-lg-6 col-xs-6 col-sm-6 ">
                                              <select name="idoc_facility" id="idoc_facility" class="selectpicker form-control">
                                                  <option value="">Select</option>
                                                <?php
												$iDocFacArr=explode(',',$facData->fac_idoc_link_id);
                                                foreach($facility_iDoc as $id=>$name) {
                                                ?>
                                                    <option value="<?php echo $id;?>" <?php if(in_array($id,$iDocFacArr)) echo 'selected'; ?>><?php echo $name;?></option>
                                                <?php
                                                }
												unset($iDocFacArr);
                                                ?>
                                                </select>
                                               <small>
                                                    iASC Facility
                                                </small> 	
                                          </div>
                                       </div>
                                        </div>
                                      <div class="col-md-4 col-lg-4 col-xs-12 col-sm-4 ">
                                      
                                          <input type="text" class="form-control" id="sergeryCenerNPI" name="sergeryCenerNPI" value="<?php echo $facData->fac_npi; ?>">
                                          <small>
                                            NPI#
                                          </small>
                                      </div>
                                       <div class="col-md-4 col-lg-4 col-xs-12 col-sm-4">
                                       
                                       <div class="row">                            
                                            <div class="col-md-5 col-lg-5 col-xs-5 col-sm-5">
                                             <input type="text" class="form-control" id="sergeryCenerFederal" name="sergeryCenerFederal" value="<?php echo $facData->fac_federal_ein; ?>">
                                            <small>
                                               Federal EIN#
                                             </small>
                                          
                                            </div>
                                          <div class="col-md-7 col-lg-7 col-xs-7 col-sm-7 padding_0">
                                              <input type="checkbox" name="headquarter" id="headquarter" value="1" <?php echo ($facData->fac_head_quater==1)?' checked onClick="alert(\'Choose other facility as headquarter to remove this one.\');return false;" ':''; ?>>
                                               <small>
                                                   Headquarter ASC
                                                </small> 	
                                          </div>
                                       </div>
                                      </div>
                                   </div>   
                                </div>
                            </div>
                        </div>
                    </div>	
                <!-- First ROw Ends -->
                
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="form_inner_m">
                            <div class="row">
                                <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                    <label for="add" class="text-left"> 
                                         Address		
                                    </label>
                                </div>
                                <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                    <div class="row">                            
                                        <div class="col-md-4 col-lg-4 col-xs-12 col-sm-4">
                                          <input type="text" class="form-control" id="sergeryCenerAddress" name="sergeryCenerAddress" value="<?php echo $facData->fac_address1; ?>">
                                           <small>
                                                Street Address
                                          </small>
                                        </div>
                                      <div class="col-md-4 col-lg-4 col-xs-12 col-sm-4 ">
                                          <input type="text" class="form-control" id="sergeryCenerAddress2" name="sergeryCenerAddress2" value="<?php echo $facData->fac_address2; ?>">
                                          <small>
                                            Address Line 2
                                          </small>
                                      </div>
                                       <div class="col-md-4 col-lg-4 col-xs-12 col-sm-4">
                                        <div class="row">                            
                                            <div class="col-md-5 col-lg-5 col-xs-5 col-sm-5">
                                              <input type="text" class="form-control" id="elem_city" name="elem_city" value="<?php echo $facData->fac_city;?>">
                                            <small>
                                                City
                                             </small>
                                          
                                            </div>
                                          <div class="col-md-2 col-lg-2 col-xs-2 col-sm-2 padding_0">
                                              <input type="text" class="form-control" id="elem_state" name="elem_state" value="<?php  echo $facData->fac_state; ?>">
                                               <small>
                                                    State
                                                </small> 	
                                          </div>
                                          <div class="col-md-5 col-lg-5 col-xs-5 col-sm-5">
                                              <input type="text" class="form-control" id="elem_zip" name="elem_zip" value="<?php echo $facData->fac_zip; ?>" onBlur="return getCityStateFn(this);">
                                               <small>
                                                    Zip
                                                </small> 	
                                          </div>
                                       </div>
                                         
                                      </div>
                                   </div>   
                                </div>
                                
                            </div>
                        </div>
                    </div>
        
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="form_inner_m">
                            <div class="row">
                                <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                    <label for="contact" class="text-left"> 
                                         Contact		
                                    </label>
                                </div>
                                <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                    <div class="row">   
                                        <div class="col-md-4 col-lg-4 col-xs-12 col-sm-4">
                                        <div class="row">                            
                                            <div class="col-md-6 col-lg-6 col-xs-6 col-sm-6">
                                              <input type="text" class="form-control" id="sergeryCenerContactName" name="sergeryCenerContactName" value="<?php echo $facData->fac_contact_name; ?>">
                                            <small>
                                               Name
                                             </small>
                                          
                                            </div>
                                          <div class="col-md-6 col-lg-6 col-xs-6 col-sm-6 ">
                                              <input type="text" class="form-control" maxlength="12" id="sergeryCenerPhone" name="sergeryCenerPhone" value="<?php echo $facData->fac_contact_phone; ?>" onBlur="ValidatePhone(this);">
                                               <small>
                                                    Phone	
                                                </small> 	
                                          </div>
                                       </div>
                                         
                                      </div>                         
                                        <div class="col-md-4 col-lg-4 col-xs-12 col-sm-4">
                                          <input type="text" class="form-control" id="sergeryCenerFax" name="sergeryCenerFax" value="<?php echo $facData->fac_contact_fax; ?>" onBlur="ValidatePhone(this);">
                                           <small>
                                               Fax
                                          </small>
                                        </div>
                                      <div class="col-md-4 col-lg-4 col-xs-12 col-sm-4 ">
                                          <input type="text" class="form-control" id="sergeryCenerEmail" name="sergeryCenerEmail" value="<?php echo $facData->fac_contact_email; ?>">
                                          <small>
                                            Email
                                          </small>
                                      </div>
                                       
                                   </div>   
                                </div>
                            </div>
                        </div>
                    </div>
         
         		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="form_inner_m">
                        <div class="row">
                            <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                <label for="group_institution_list" class="text-left"> 
                                     Group		
                                </label>
                            </div>
                            <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                <div class="row">                            		<div class="col-md-4 col-lg-4 col-xs-12 col-sm-4">
                                        <select class="selectpicker form-control " id="group_practice_list" name="group_practice_list"  >
                                            <option value="">Select</option>
                                            <?php
											if(count($groupNewRow)>0) {
												foreach($groupNewRow as $grpRow) {
													if($grpRow["del_status"]=='0' || $group_practice == $grpRow["gro_id"]) {
												?>
	                                            		<option value="<?php echo $grpRow["gro_id"];?>" <?php if($facData->fac_group_practice == $grpRow["gro_id"]) { echo "selected"; }?> > <?php echo $grpRow["name"];?> </option>    
                                                <?php	
													}
												}
											}
											?>
                                        </select>
                                      <small>
                                            iASC Group Practice
                                      </small>
                                  </div>
                                    <div class="col-md-4 col-lg-4 col-xs-12 col-sm-4">
                                        <select class="selectpicker form-control " id="group_institution_list" name="group_institution_list">
                                            <option value="">Select</option>
											<?php
                                            if(count($groupNewRow)>0) {
                                                foreach($groupNewRow as $grpRow) {
                                                    if($grpRow["del_status"]=='0' || $group_institution == $grpRow["gro_id"]) {
                                                ?>
                                                        <option value="<?php echo $grpRow["gro_id"];?>" <?php if($facData->fac_group_institution == $grpRow["gro_id"]) { echo "selected"; }?> > <?php echo $grpRow["name"];?> </option>    
                                                <?php	
                                                    }
                                                }
                                            }
                                            ?>
                                        </select>
                                      	<small>
                                        iASC Group Institution(Facility)
                                      	</small>
                                    </div>
                                  <div class="col-md-4 col-lg-4 col-xs-12 col-sm-4 ">
                                        <select class="selectpicker form-control " id="group_anesthesia_list" name="group_anesthesia_list">
                                            <option value="">Select</option>
                                            <?php
											if(count($groupNewRow)>0) {
												foreach($groupNewRow as $grpRow) {
													if($grpRow["del_status"]=='0' || $group_anesthesia == $grpRow["gro_id"]) {
												?>
	                                            		<option value="<?php echo $grpRow["gro_id"];?>" <?php if($facData->fac_group_anesthesia == $grpRow["gro_id"]) { echo "selected"; }?> > <?php echo $grpRow["name"];?> </option>    
                                                <?php	
													}
												}
											}
											?>
                                        </select>
                                      	<small>
                                        iASC Group Anesthesia
                                      	</small>
                                  </div>
                                   
                               </div>   
                            </div>
                        </div>
                    </div>
                </div>
                
                </div>	 <!-- wrpa row 2-->		     
          </div>
          <!-- NEcessary PUSH     -->	 
          <Div class="push"></Div>
          <!-- NEcessary PUSH     -->
	</form>
        
</div>
</body>
</html>
