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

//
if(isset($_REQUEST["op"])){
include_once(dirname(__FILE__)."/../../../config/globals.php");

	if($_REQUEST["op"]=="getform" && !empty($_GET["tstnm"])){
		
		$sbtp = $_GET["tst_sb_typ"]; $sbtp_ph="";
		$sbtp = xss_rem($sbtp);	/** Reject parameter with arbitrary values - Security Fix */	
		if(!empty($sbtp)){ $sbtp_ph = " AND test_sub_type='".$sbtp."' "; }
		
		
		$html="";
		$sql = "select * from test_diagnosis where test_id='".sqlEscStr($_GET["tstnm"])."' AND del_by='0' ".$sbtp_ph."
				Order by test_sub_type, diag_nm ";
		$res = sqlStatement($sql);
		
		for($i=1;$row=sqlFetchArray($res);$i++){
			$tmp_diag = $row["diag_nm"];
			$tmp_did = $row["id"];
			
			$html.="<tr>
					<td><div class=\"checkbox\"><input type=\"checkbox\" name=\"el_chk".$i."\" id=\"el_chk".$i."\" class=\"test_chkbx\"  /><label for=\"el_chk".$i."\">".$i.".</label></div></td>
					<td><input type=\"text\" name=\"el_diag".$i."\" id=\"el_diag".$i."\" value=\"".$tmp_diag."\" class=\"form-control\" >
						<input type=\"hidden\" name=\"el_did".$i."\" id=\"el_did".$i."\" value=\"".$tmp_did."\"  >
					</td>
					</tr>	
			";
		
		}	
		
		//Default / empty
		//if($i==1){
			for($j=1;$j<=4;$j++,$i++){
			$html.="<tr>
					<td><div class=\"checkbox\"><input type=\"checkbox\" name=\"el_chk".$i."\" id=\"el_chk".$i."\" class=\"test_chkbx\" /><label for=\"el_chk".$i."\">".$i."</label></div></td>
					<td><input type=\"text\" name=\"el_diag".$i."\" id=\"el_diag".$i."\" value=\"\" class=\"form-control\" >
						<input type=\"hidden\" name=\"el_did".$i."\" id=\"el_did".$i."\" value=\"\"  >
					</td>
					</tr>	
			";	
			}
		//}
		echo $html;
		
	}else if( $_REQUEST["op"] == "test_diagnosis_save" ){
		
		$el_test_nm = $_POST["el_test_nm"];
		$el_test_sb_typ = xss_rem($_POST["el_test_sb_typ"]);	/* Reject if arbitrary values found - Security Fix */
		$el_test_nm_tmp =  $el_test_nm_tst = "";
		$ar_test_nm = explode("-@-", $el_test_nm);
		$el_test_nm_tmp = trim($ar_test_nm[0]);
		$el_test_nm_tst = trim($ar_test_nm[1]);

		/* Prevent saving invalid value - Security Fix */
		if( !is_numeric($el_test_nm_tst) )
		{
			exit;
		}		
		
		$c=0;
		while(true){
			$c+=1;
			
			if(isset($_POST["el_did".$c])){
				$tid = $_POST["el_did".$c];
				$tid = xss_rem($tid);	/* Reject parameter with arbitrary values - Security Fix */

				$tdns = trim($_POST["el_diag".$c]);
				$tdns = xss_rem($tdns);	/* Reject parameter with arbitrary values - Security Fix */
				
				if(!empty($tid)){
					$sql = "UPDATE test_diagnosis SET ";
					$sql_w = " WHERE id = '".$tid."' ";
					
					if(empty($tdns) || (!empty($_POST["opdel"]) && !empty($_POST["el_chk".$c]))){
						$sql .= " del_by='".$_SESSION["authId"]."', ";
					}
					
				}else{
					if(!empty($_POST["opdel"])){ exit($tdns); continue; }
					$sql = "INSERT test_diagnosis SET "; $sql_w= "";
				}
				
				$sql_m = "diag_nm = '".$tdns."', test_id='".$el_test_nm_tst."', test_sub_type='".$el_test_sb_typ."' ";
				
				if(!empty($tdns) || !empty($tid)){
				$sql = $sql.$sql_m.$sql_w;
				$row = sqlQuery($sql);
				}
				
			}else{
				break;
			}
			
			if($c>100){break;}
		}		
		
	}

exit();
}


//--
require_once("../admin_header.php");
require($GLOBALS['srcdir']."/classes/class.tests.php");
$objTests				= new Tests;
$ar_tst = $objTests->get_active_tests();
$str_tst_opt ="";

foreach($ar_tst as $k => $v){
	if($v["test_name"] == "A/Scan" || $v["test_name"] == "IOL Master"){continue;}
	$str_tst_opt .= "<option value=\"".$v["temp_name"]."-@-".$v["id"]."\">".$v["temp_name"]."</option>";
}

?>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/admin/admin_test_diagnosis.js"></script>
<script type="text/javascript">

</script>
<body>
	<form name="test_diagnosis" id="test_diagnosis">
	<input type="hidden" name="op" value="test_diagnosis_save" />
	<input type="hidden" id="opdel" name="opdel" value="" />
	<div class="whtbox">
		<div class="table-responsive respotable adminnw">
			<table class="table table-bordered table-hover">
				<thead>
					<tr >
						<th style="width:20px; padding-left:8px;"><div class="checkbox"><input type="checkbox" onClick="switch_allVals(this)" name="select_all" id="select_all" <?php echo $allChecked; ?> /><label for="select_all"></label></div></th>
						<th class="">
							<div class="form-group">
								<label class="control-label col-sm-1" for="el_test_nm">Select Test:</label>
								<div class="col-sm-3">
								<select name="el_test_nm" id="el_test_nm" onchange="load_diag(this)" class="form-control minimal">
									<option value=""></option>
									<?php echo $str_tst_opt; ?>		
								</select>
								</div>
							</div>
							
							<div id="dv_test_sb_typ" class="form-group hidden">
								<label class="control-label col-sm-2" for="el_test_nm">Select Test Sub Type:</label>
								<div class="col-sm-3">
								<select name="el_test_sb_typ" id="el_test_sb_typ" onchange="load_diag(this)" class="form-control minimal">
									<option value=""></option>
											
								</select>
								</div>
							</div>
						</th>
							
					</tr>
				</thead>
				<tbody id="tests_data"></tbody>
			</table>
		</div>
	</div>
	</form>	
<?php	
	require_once("../admin_footer.php");
?>