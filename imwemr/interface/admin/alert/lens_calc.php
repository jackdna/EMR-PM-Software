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
?><?php
require_once('../admin_header.php');
//require_once("../../main/Functions.php");
//save
$flg_redirect = 0;
for ($i = 0; $i < 100; $i++) {
	if (isset($_POST["el_lens_calc" . $i]) && isset($_POST["el_lens_url" . $i])) {
		$el_lens_calc = $_POST["el_lens_calc" . $i];
		$el_lens_url = $_POST["el_lens_url" . $i];
		$flg_redirect = 1;
		if (!empty($el_lens_calc) && !empty($el_lens_url)) {
			$id = $_POST["el_edid" . $i];
			//el_del frm_op
			if (isset($_POST["frm_op"]) && !empty($_POST["frm_op"]) && ($_POST["frm_op"] == "delete")) {
				if (isset($_POST["el_del" . $i]) && !empty($_POST["el_del" . $i])) {
					//delete
					$sql = "UPDATE sps_admin_lens_calc SET del_status='1' WHERE id='" . $_POST["el_del" . $i] . "' ";
					$row = sqlQuery($sql);
				}
				//
				continue;
			}

			//--
			$sql_in = "INSERT INTO sps_admin_lens_calc SET ";
			$sql_up = "UPDATE sps_admin_lens_calc SET ";
			$sql_w = "WHERE id='" . $id . "' ";
			$sql_con = " lens_calc='" . imw_real_escape_string($el_lens_calc) . "', 
						url='" . imw_real_escape_string($el_lens_url) . "'
					";


			if (isset($_POST["el_edid" . $i]) && !empty($_POST["el_edid" . $i])) {
				//update
				$sql = $sql_up . $sql_con . $sql_w;
			} else {
				//insert
				$sql = $sql_in . $sql_con;
			}

			$row = sqlQuery($sql);
			//--
		}
	} else {
		break;
	}
}

//--
if ($flg_redirect == "1") {
	unset($_POST);
	header("location: lens_calc.php");
	exit();
}
//--
?>
<!DOCTYPE html>
<html>
	<head>
		<title>imwemr</title>
		<meta name="viewport" content="width=device-width, maximum-scale=1.0" />
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<!--	<link rel="stylesheet" type="text/css" href="../../themes/default/common.css">
			<link rel="stylesheet" href="<?php echo $css_header; ?>" type="text/css">
			<link rel="stylesheet" href="<?php echo $css_patient; ?>" type="text/css">
			<script type="text/javascript" src="../../../js/jquery.js"></script>
			<script type="text/javascript" src="../../../js/common.js"></script>
			<script type="text/javascript" src="../../common/script_function.js"></script>-->
		<?php echo $scriptMsg; ?>
		<script type="text/javascript">
			/*
			 var btnArr = new Array();
			 btnArr[0] = "lense_users__Save__lense_users";
			 parent.parent.show_submit_button (btnArr);
			 */
			//Btn --
			var ar = [["lense_users", "Save", "top.fmain.frm_submit();"],
				["lense_users_del", "Delete", "top.fmain.delete_sel();"]
			];
			top.btn_show("ADMN", ar);
			$(document).ready(function () {
				set_header_title('Lens Calculator');
			});
			//Btn --
			/*
			 function chkStatus() {
			 var flag = 0;
			 var eleLength = document.lenseDefineFrm.elements.length;
			 for (i = 0; i < eleLength; i++) {
			 var typeid = document.lenseDefineFrm.elements[i].id;
			 if (typeid.indexOf("typeId") != -1) {
			 if (document.lenseDefineFrm.elements[i].checked == true) {
			 var flag = flag + 1;
			 }
			 }
			 }
			 if (flag > 4) {
			 fAlert("Only four types can be selected.")
			 return false;
			 }
			 }*/

			function frm_submit() {
				var ar = new Array();
				var er = 0;
				$("input[name*=el_lens_calc]").each(function () {
					if (this.value != "") {
						var t = $.trim(this.value);
						if (ar.indexOf(t) == -1) {
							ar[ar.length] = t;
						} else {
							er = 1;
						}
					}
				})

				if (er == 0) {
					$('#lenseCalcFrm').submit();
				} else {
					alert("Please enter unique button names.");
				}
			}

			function delete_sel() {
				$("#frm_op").val("delete");
				$('#lenseCalcFrm').submit();
			}

		</script>
	</head>
	<body class="body_c">
		<div class="whtbox">


			<form action="lens_calc.php" method="post" name="lenseCalcFrm" id="lenseCalcFrm">
				<input type="hidden" id="frm_op" name="frm_op" value="save">
				<table class="table table-bordered adminnw tbl_fixed" width="100%">
					<thead>
						<tr>
							<th style="width:80px;">#</th>
							<th>Lens Calculator</th>
							<th>URL</th>
							
						</tr>
					</thead>
					<tbody>
						<?php
						$sql = " SELECT * FROM `sps_admin_lens_calc` WHERE del_status='0' ORDER BY lens_calc  ";
						$rez = sqlStatement($sql);
						for ($i = 0; $row = sqlFetchArray($rez); $i++) {

							$id = $row["id"];
							$lens_calc = $row["lens_calc"];
							$url = $row["url"];

							$bgcolor = (($i % 2) == 0) ? 'alt3' : '';
							?>
							<tr class="<?php echo $bgcolor; ?>">
								
								<td><div class="checkbox">
									<input id="el_del<?php echo $i; ?>" type="checkbox" name="el_del<?php echo $i; ?>" value="<?php echo $id; ?>" >
									<label for="el_del<?php echo $i; ?>"></label>
									<input id="el_edid<?php echo $i; ?>" type="hidden" name="el_edid<?php echo $i; ?>" value="<?php echo $id; ?>" >
								</div>
								
								</td>
								<td ><input id="el_lens_calc<?php echo $i; ?>" type="text" name="el_lens_calc<?php echo $i; ?>" class="form-control" value="<?php echo $lens_calc; ?>" style="width:90%;" ></td>
								<td><input id="el_lens_url<?php echo $i; ?>" type="text" name="el_lens_url<?php echo $i; ?>" class="form-control" value="<?php echo $url; ?>" style="width:90%;" ></td>
												
							</tr>
							<?php
						}
						$j = $i + 2;
						for (; $i <= $j; $i++) {
							?>
							<tr class="<?php echo $bgcolor; ?>">
								<td></td>
								<td ><input id="el_lens_calc<?php echo $i; ?>" type="text" name="el_lens_calc<?php echo $i; ?>" class="form-control" value="" style="width:90%;" placeholder="Enter Name" ></td>
								<td><input id="el_lens_url<?php echo $i; ?>" type="text" name="el_lens_url<?php echo $i; ?>" class="form-control" value="" style="width:90%;" placeholder="Enter Url" ></td>
												
							</tr>
							<?php
						}
						?>	
					</tbody>
				</table>


			</form>

			<script type="text/javascript">
				parent.parent.show_loading_image('none');
			</script>
	</body>
</html>