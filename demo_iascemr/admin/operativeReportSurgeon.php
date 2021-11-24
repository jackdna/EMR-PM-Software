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
<title>Operative Report</title>
<meta name="viewport" content="width=device-width, maximum-scale=1.0">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<script>
	function changeRefresh(val){
		location.href='operativeReport.php?surgeonId='+val;
	}
	top.frames[0].document.getElementById('saveButton').style.display = 'none';
	top.frames[0].document.getElementById('deleteSelected').style.display = 'none';
	top.frames[0].document.getElementById('cancelButton').style.display = 'none';	
</script>
<?php
include("adminLinkfile.php");
?>
</head>
<body>
<?php
include_once("fckeditor/fckeditor.php");
include_once("classObjectFunction.php");
$objManageData = new manageData;
?>

	
		<form name="frmOperativeReportSurgeon" action="operativeReport.php" method="post">
        <Div class="all_admin_content_agree wrap_inside_admin">      
     <Div class="subtracting-head" id="surgeon-header">
         <div class="head_scheduler new_head_slider padding_head_adjust_admin">
            <span>
                Op-Report
            </span>
          </div>
		 <Div class="wrap_inside_admin" id="surgeonsList_id"> <!-- all_admin_content height_adjust_prefer -->
                      
       <div class="form_outer custom_surgeon_margin" style="">
                        <Div class="col-lg-4 visible-lg"></Div>
                        <Div class="col-md-4 visible-md"></Div>
                        <Div class="col-sm-2 visible-sm"></Div>
                        
                       <div class="col-lg-4 col-md-4 col-sm-8 col-xs-12">
                                    <div class="form_reg wrap_surgeon border_customize_r" id="hid_surgeon">	 
                                            <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                             <?php
											if(!$surgeonId) {
												$surgeonId = $_REQUEST['surgeonId'];
											}
											?>
                                        <select name="surgeonId" onChange="javascript:changeRefresh(this.value);" class="selectpicker">
                                            <option value="">Select Surgeon</option>
                                            <option value="0">Community</option>
                                                <?php
                                                
                                                $userSurgeonsDetails = $objManageData->getArrayRecords('users', 'user_type', 'Surgeon','lname','ASC');
                                                if($userSurgeonsDetails) {
                                                    foreach($userSurgeonsDetails as $surgeon){
                                                        $deleteStatus = $surgeon->deleteStatus;
                                                        if($deleteStatus=="Yes") { //IF THIS USER HAS BEEN COMMITTED AS DELETED(BY SETTING ITS deleteStatus TO Yes)
                                                            ////DO NOT SHOW DELETED USER IN DROP DOWN 
                                                        }else {
                                                        
                                                        ?>
                                                            <option value="<?php echo $surgeon->usersId; ?>" <?php if($surgeonId == $surgeon->usersId) echo "SELECTED"; ?>><?php echo $surgeon->lname.', '.$surgeon->fname; ?></option>
                                                        <?php
                                                        }
                                                    }
                                                }	
                                                ?>
                                        </select>
                                            </div>
                                        
                                    </div><!-------------------Form Reg-----------------------------> 	
                             </div>	
                        
     </div>
     </Div>
    
           
    </Div>
        
     
      </Div>
			
		</form>	
	</body>
</html>	