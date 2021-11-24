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
require_once("../admin_header.php");
$wn_height = $_SESSION['wn_height']-350;
$library_path = $GLOBALS['webroot'].'/library';
/**********FUNCTIONS*******/
function get_users_arr($type = 1,$cols = 'id,lname,fname,mname'){
	$return = false;
	$res = imw_query("SELECT $cols FROM users WHERE user_type IN ($type) AND delete_status=0 ORDER BY lname,fname");
	if($res && imw_num_rows($res)>0){
		$return = array();
		while($rs = imw_fetch_assoc($res)){
			$return[] = $rs;
		}
	}else echo imw_error();
	return $return;
}

function get_test_names($return_type='all'){
	$return = false;
	$res = imw_query("SELECT * FROM tests_name WHERE del_status=0 AND status='1' ORDER BY id");
	if($res && imw_num_rows($res)>0){
		$return = array();
		if($return_type=='testNamesOptions'){$return='';}
		while($rs = imw_fetch_assoc($res)){
			if($return_type=='all')$return[] = $rs;
			if($return_type=='testNames')$return[$rs['id']] = $rs['test_name'];
			if($return_type=='testNamesOptions'){$return .= '<option value="'.$rs['test_table'].'~@~'.$rs['id'].'~@~'.$rs['script_file'].'">'.$rs['temp_name'].'</option>';}
		}
	}else echo imw_error();
	return $return;
}

?>
<script type="text/javascript" src="../../../library/js/admin/admin_test_preferences.js?<?php echo filemtime('../../../library/js/admin/admin_test_preferences.js');?>"></script>
<script type="text/javascript">
$(document).ready(function(e) {
	$('.left_phy_tree').click(function(){
		if($(this).find('span').text()=='+'){
			isEditMode=false;
			loadSavedProfiles($(this).prop('id'),$(this));
		}else if(isEditMode==false){
			$(this).find('span').text('+');
			$('.div_saved_profiles').html('');
		}

	});
	var ar = [["addTestTemplatesTab","Add New","top.fmain.addnew();"],
		  ["span","20"],
		  ["saveTestTemplatesTab","Save","top.fmain.saveFormData();"],
  		  ["span","20"],
		  ["loadTestTemplatesTab","Test Templates","top.fmain.loadTestTemplates();"]
		 ];
	top.btn_show("ADMN",ar);
	set_header_title('Test Preferences');	

});

function loadTestTemplates(){
	window.location.href= top.JS_WEB_ROOT_PATH+'/interface/admin/chart_notes/test_templates.php';	
}
</script>

<style type="text/css">
.right_border{border-right:2px groove #ccc;}
#left_div{height:<? echo $wn_height;?>px; overflow-x:hidden; overflow:auto;}
#div_test_form{height:<? echo $wn_height-90;?>px; overflow-x:hidden; overflow:auto; border:1px solid #ccc;}
.left_phy_tree{cursor:pointer; padding:4px 0px; font-size:13px;}
.left_phy_tree:hover{background-color:#efefef;}
/*.div_saved_profiles{border-bottom:1px solid #ddd;}*/
/*.div_saved_profiles span:hover{background-color: #CF6;}*/
</style>
<body>
<div class="whtbox">
	<div class="row">
    	<div class="col-sm-2 right_border" >
            <div class="table-responsive respotable adminnw">
                <table class="table table-hover">
                    <thead>
                      <tr>
                           <th>Saved Preferences<span></span></th>
                      </tr>
                    </thead>
                    <tbody>
                    	<tr><td>
                        <div id="left_div">
                        <?php $physicians_arr = get_users_arr('1,11,12,19');
							  $options_val = array();
							//pre($physicians_arr);
							for($i=0;$i<count($physicians_arr);$i++){
								$phyID = $physicians_arr[$i]['id'];
								$phyNM = $physicians_arr[$i]['lname'].', '.$physicians_arr[$i]['fname'].' '.substr($physicians_arr[$i]['mname'],0,1);
								echo '<div><div id="'.$phyID.'" class="left_phy_tree"><span>+</span> <b>'.$phyNM.'</b></div><div class="m5 ml20 div_saved_profiles"></div></div>';
								$options_val[$phyID] = $phyNM;
							}
							?>
                        </div> 
                        </td></tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-sm-10">
        	<div class="table-responsive  adminnw">
                <form name="frm_new_profile" method="post">
                <input type="hidden" name="id" id="id" value="">
                <input type="hidden" name="test_id" id="test_id" value="">
                <table class="table">
                    <thead>
                      <tr>
                           <th colspan="5">Create/Edit Preferences<span></span></th>
                      </tr>
                    </thead>
                    <tbody>
                    	<tr>
                        	<td class="cl-sm-3">
                            	<div class="form-group">
                                	<label for="profile_name">Preference Name</label>
                                    <input type="text" class="form-control" name="profile_name" id="profile_name">
                                </div>
                            </td>
                        	<td class="cl-sm-2">
                            	<div class="form-group">
                                	<label for="physician_id">Physician Name</label>
                                    <select disabled class="form-control minimal" name="physician_id" id="physician_id">
                                    	<option value="">--SELECT--</option>
										<?php  
										foreach($options_val as $phyID=>$phyNM){
											echo '<option value="'.$phyID.'">'.$phyNM.'</option>';
										}
										?>
                                     </select>
                                </div>
                            </td>
                        	<td class="cl-sm-2">
                            	<div class="form-group">
                                	<label for="sel_test_id">Test Name</label>
                                    <select name="sel_test_id" class="form-control minimal" id="sel_test_id" onChange="load_test_page(this.value,'','')">
                                    	<option value="">--SELECT--</option>
										<?php echo get_test_names('testNamesOptions');?>
                                     </select>
                                </div>
                            </td>
                        	<td class="cl-sm-3">
                            	<div class="form-group">
                                	<label for="favorite">Default Profile</label>
                                    <input type="checkbox" class="form-control" name="favorite" id="favorite" value="1">
                                </div>
                            </td>
                            <td class="cl-sm-2" id="copy_profile_from" style="display:none;">
                            	<div class="form-group">
                                	<label for="favorite">Copy Preference From</label>
                                    <select name="copy_profile" id="copy_profile" class="form-control minimal" onchange="copySavedProfiles(this);" style="width:200px;">
                                    </select>
                                    <input type="hidden" name="copy_pre_val" id="copy_pre_val" />
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
                </form>
            </div>
            <div id="div_test_form"></div>
        </div>
		
	</div>
</div>
</body></html>