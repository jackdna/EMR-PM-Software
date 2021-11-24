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
// function get_users_arr($type = 1,$cols = 'id,lname,fname,mname'){
// 	$return = false;
// 	$res = imw_query("SELECT $cols FROM users WHERE user_type IN ($type) AND delete_status=0 ORDER BY lname,fname");
// 	if($res && imw_num_rows($res)>0){
// 		$return = array();
// 		while($rs = imw_fetch_assoc($res)){
// 			$return[] = $rs;
// 		}
// 	}else echo imw_error();
// 	return $return;
// }

function get_test_names($return_type='all'){
	$return = false;
	$res = imw_query("SELECT * FROM tests_name WHERE del_status=0 AND status='1' ORDER BY id");
	if($res && imw_num_rows($res)>0){
		$return = array();
		if($return_type=='testNamesOptions'){$return='';}
		while($rs = imw_fetch_assoc($res)){
			// if($return_type=='all')$return[] = $rs;
			// if($return_type=='testNames')$return[$rs['id']] = $rs['test_name'];
			// if($return_type=='testNamesOptions'){
			// 	$return .= '<option data-id="'.$rs['id'].'" value="'.$rs['test_table'].'~@~'.$rs['id'].'~@~'.$rs['script_file'].'">'.$rs['temp_name'].'</option>';
			// }
			if($return_type=='testNamesOptions'){
				$return .= '<option value="'.$rs['id'].'">'.$rs['temp_name'].'</option>';
			}
		}
	}else echo imw_error();
	return $return;
}

?>
<script type="text/javascript" src="../../../library/js/admin/admin_dss_service.js"></script>

<style type="text/css">
.right_border{border-right:2px groove #ccc;}
#left_div{height:<? echo $wn_height;?>px; overflow-x:hidden; overflow:auto;}
#div_test_form{height:<? echo $wn_height-90;?>px; overflow-x:hidden; overflow:auto; border:1px solid #ccc;}
.left_phy_tree{cursor:pointer; padding:4px 0px; font-size:13px;}
.left_phy_tree:hover{background-color:#efefef;}
/*.div_saved_profiles{border-bottom:1px solid #ddd;}*/
/*.div_saved_profiles span:hover{background-color: #CF6;}*/
.w25 { width: 25%!important; float: left; }
</style>
<body>
<div class="whtbox">
	<div class="row">
    	<div class="col-sm-12">
	       	<div class="table-responsive  adminnw">
                <form name="frm_new_profile" method="post">
            	<input type="hidden" name="id" id="id" value="">
	            <input type="hidden" name="test_id" id="test_id" value="">

                <table class="table">
                    <thead>
                      <tr>
                           <th colspan="4">Link Active Test with the Speciality Service<span></span></th>
                      </tr>
                    </thead>
                    <tbody>
						<tr>
							<td class="w25">
                            	<div class="form-group">
                            		<label for="dssServiceSpeciality">DSS Service Specility</label><br/>
                                    <select name="dssServiceSpeciality" class="form-control minimal" id="dssServiceSpeciality" onchange="getServiceData()"></select>
                                    <input type="hidden" name="svcName" id="svcName">
									<input type="hidden" name="orderableItem" id="orderableItem">
                                    <script>
                                    	function dssServiceSpeciality() {
                                    		$.ajax({
                                    			url: top.JS_WEB_ROOT_PATH + "/interface/admin/chart_notes/dss_services_ajax.php?do=dssServiceSpeciality",
                                    			type: 'GET',
                                    		}).done(function(response) {
                                    			$('#dssServiceSpeciality').append(response);
                                    		});
                                    	}dssServiceSpeciality();
                                    </script>
                                </div>
                            </td>
                        	<td class="w25">
                            	<div class="form-group">
                                	<label for="sel_test_id">Test Name</label>
                                    <select name="sel_test_id" class="form-control minimal" id="sel_test_id" onChange="load_test_page(this.value,'','')">
                                    	<option value="">--SELECT--</option>
										<?php echo get_test_names('testNamesOptions');?>
                                    </select>
                                </div>
                            </td>
						</tr>
                    </tbody>
                </table>
                </form>
            </div>
            <div id="div_test_form">
	
				<?php
            		$sql = imw_query("SELECT `id`,`test_id`,`service_ien`,`service_name`,`service_orderable_item` FROM dss_test_services WHERE `status` = 0");
            		if(imw_num_rows($sql) > 0) {
		      	?>           
            	<table class="table">
            		<thead>
            			<tr>
            				<th>Test</th>
            				<th>Service Name</th>
            				<th width="2%"></th>
            			</tr>
            		</thead>
            		<tbody>
            			<?php
							while ($row = imw_fetch_assoc($sql)) {
								// pre($row);
								$query = "SELECT test_name FROM tests_name WHERE del_status=0 AND status='1' AND id = ".$row['test_id'];
								$tst = imw_query($query);
								$tn = imw_fetch_assoc($tst);

		            			echo '<tr onclick="loadServData('.$row['id'].','.$row["test_id"].','.$row["service_ien"].','.$row["service_orderable_item"].',\''.$row['service_name'].'\')">
		            				<td>'.$tn['test_name'].'</td><td>'.$row['service_name'].'</td><td><a href="javascript:void(0)" onclick="deleteRecord('.$row['id'].')"><span class="glyphicon glyphicon-remove link_cursor"></span></a></td>
		            				</tr>';
							}
            			?>
            		</tbody>
            	</table>
            	<?php
            		} else {
        				echo '<p>No Record Availlable</p>';
	         		}
            	?>
            </div>
        </div>
		
	</div>
</div>
</body></html>