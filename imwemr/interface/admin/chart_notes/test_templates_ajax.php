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

require_once("../../../config/globals.php");

if(isset($_POST['action'])){
    $action = $_POST['action'];
    
    switch($action){
        
        case "test_templates_switch":
            $test_ids = $_POST['test'];
            
            $sql = "UPDATE `tests_name` SET `status`='0';"; /*Disable all Tests*/
            imw_query($sql);
            
            if(count($test_ids)>0){
                $test_ids = array_keys($test_ids);
                $test_ids = implode(",",$test_ids);
                
                $sql1 = "UPDATE `tests_name` SET `status`='1' WHERE `id` IN(".$test_ids.");";
                imw_query($sql1);
            }
        break;
        case "fetch_list":
            $sql = "SELECT `id`, `temp_name` AS 'test_name', `status`, `t_manager`, `test_type`, `version` FROM `tests_name` WHERE `del_status`='0' ORDER BY `id` ASC";
            $result = imw_query($sql);
            $data = "";
            $allChecked = "checked";
            if($result){
                if(imw_num_rows($result)>0){
                    while($row = imw_fetch_assoc($result)){
                        
                        if($row['status']=='1'){$checked = "checked";}else{$allChecked = $checked = "";}
						$tick_img = '';
						if($row['t_manager']=='1'){$tick_img = '<img src="../../../library/images/confirm3.png">';}
                        
                        $data .= "<tr>";
						$data .= '<td style="width:20px; padding-left:10px;"><div class="checkbox"><input type="checkbox"  onClick="switch_val()" class="test_chkbx" name="test['.$row['id'].']" id="test['.$row['id'].']" value="1" '.$checked.'><label for="test['.$row['id'].']"></label></div></td>';
						$data .= '<td onClick="EditRecord(\''.$row['test_name'].'\',\''.$row['id'].'\',\''.$row['t_manager'].'\',\''.$row['version'].'\');">'.$row['test_name'].'</td>';
                        $data .= '<td style="text-align:center;" onClick="EditRecord(\''.$row['test_name'].'\',\''.$row['id'].'\',\''.$row['t_manager'].'\',\''.$row['version'].'\');">'.$tick_img.'</td>';
                        $data .= "</tr>";
                    }
                }
                else{
                    
                }
            echo $data;
            }
        break;
        case "addTemplate":
            if($_POST['tempName']!=""){
				$t_manager = isset($_POST['t_manager']) ? intval($_POST['t_manager']) : 0;
				$templateName = addslashes(trim($_POST['tempName']));
                $sql = "INSERT INTO `tests_name`(`test_name`, `test_type`, `temp_name`,`t_manager`,`test_table`,`patient_key`,`phy_id_key`,`exam_date_key`) VALUES('".$templateName."', '1', '".$templateName."','".$t_manager."','test_other','patientId','phyName','examDate')";
                $resp = imw_query($sql);
                if($resp){
					/*--INSERT ENTRY IN superbill_test TABLE--*/
					$resp2 = imw_query("INSERT INTO superbill_test (test,test_type) VALUES ('".$templateName."','1')");
                    echo "success";
                }
                else{
                    echo "error";
                }
            }
        break;
        case "editTemplate":
            if($_POST['tempName']!="" && $_POST['templateId']!=""){
				$template_id = $_POST['templateId'];
				$templateName = addslashes(trim($_POST['tempName']));
				$superbill_test_id = 0;
				$insert_in_superbill = false;
				$edited_test_type = 0;
				/*--GET OLD TEMPLATE NAME TO MATCH IN superbill_test TABLE (for template based tests only)--*/
				$res_get_temp_name = imw_query("SELECT temp_name,test_type FROM tests_name WHERE id='".$template_id."' LIMIT 0,1");
				if($res_get_temp_name && imw_num_rows($res_get_temp_name)==1){
					$rs_get_temp_name = imw_fetch_assoc($res_get_temp_name);
					$old_template_name= $rs_get_temp_name['temp_name'];
					$edited_test_type = $rs_get_temp_name['test_type'];
					//GETTING MOST RECENT MATCHING TEST NAME (JUST FOR MORE SAFETY, NOT TO DESTROY ANY OLD DATA);
					if($edited_test_type=='1'){
						$res_get_sup_test_id = imw_query("SELECT id FROM superbill_test WHERE test='".$old_template_name."' AND test_type='1' ORDER BY id DESC LIMIT 0,1");
						if($res_get_sup_test_id && imw_num_rows($res_get_sup_test_id)==1){
							$rs_get_sup_test_id = imw_fetch_assoc($res_get_sup_test_id);
							$superbill_test_id = $rs_get_sup_test_id['id'];
						}else if($res_get_sup_test_id && imw_num_rows($res_get_sup_test_id)==0){
							/*--INSERT IN superbill_test TABLE--*/
							$insert_in_superbill = true;
						}
					}					
				}
				
				/*--UPDATING RECORD--*/
				$t_manager = isset($_POST['t_manager']) ? intval($_POST['t_manager']) : 0;
                $sql = "UPDATE `tests_name` SET `temp_name`='".$templateName."',`t_manager`='".$t_manager."' WHERE `id`='".$template_id."'";
                $resp = imw_query($sql);
                if($resp){
					/*--UPDATING MAIN NAME IF IT IS TEMPLATE BASED TEST, NOT CHANGING MAIN NAME FOR OLD TESTS--*/
					$res_update_master_name = imw_query("UPDATE `tests_name` SET `test_name`='".$templateName."' WHERE `id`='".$template_id."' AND test_type='1'");
					if($superbill_test_id>0){
						$res_update_superbill_test = imw_query("UPDATE superbill_test SET test='".$templateName."' WHERE id='".$superbill_test_id."'");
					}else if($insert_in_superbill==true && $superbill_test_id==0){
						$res_update_superbill_test = imw_query("INSERT INTO superbill_test SET test='".$templateName."', test_type='1'");
					}
                    echo "success";
                }
                else{
                    echo "error";
                }
            }
        break;
    }
}
?>