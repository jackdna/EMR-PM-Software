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
require_once($GLOBALS['fileroot'].'/library/classes/work_view/wv_functions.php');
require_once($GLOBALS['fileroot'].'/library/classes/work_view/Admn.php');
$oadmn = new Admn();
function get_data_array($qry){
	$query_to_run = imw_query($qry);
	while($row = imw_fetch_array($query_to_run)){
		$return[] = $row;
	}
	return $return;
}
// Save --
if(!empty($_POST["el_submit"]) && $_POST["el_submit"]=="save"){
	$el_anes = $_POST["el_anes"];
	$el_anes_id = $_POST["el_anes_id"];
	
	$el_dilate = $_POST["el_dilate"];
	$el_dilate_id = $_POST["el_dilate_id"];
	
	$el_ood = $_POST["el_ood"];
	$el_ood_id = $_POST["el_ood_id"];
	
	if(count($el_anes_id)>0){
		foreach($el_anes_id as $k => $v){
			$sql="";
			$t_nm = $el_anes[$k];
			if(!empty($v) && !empty($t_nm)){
				$sql = "UPDATE  chart_admn_drop_options SET  ";			
				$sqlW=" WHERE id= '".$v."' ";
			
			}else if(!empty($t_nm)){
				$sql = "INSERT INTO chart_admn_drop_options SET type='anes', del='0', ";
				$sqlW="";
			}
			
			if(!empty($sql)){
			$sql .= " name='".imw_real_escape_string($t_nm)."' ".$sqlW;
			$row = get_data_array($sql);
			}
		}
	}
	
	if(count($el_dilate_id)>0){
		foreach($el_dilate_id as $k => $v){
			$sql="";
			$t_nm = $el_dilate[$k];
			if(!empty($v) && !empty($t_nm)){
				$sql = "UPDATE  chart_admn_drop_options SET  ";			
				$sqlW=" WHERE id= '".$v."' ";
			
			}else if(!empty($t_nm)){
				$sql = "INSERT INTO chart_admn_drop_options SET type='dilate', del='0', ";
				$sqlW="";
			}
			
			if(!empty($sql)){
			$sql .= " name='".imw_real_escape_string($t_nm)."' ".$sqlW;
			$row = get_data_array($sql);
			}
		}
	}
	
	if(count($el_ood_id)>0){
		foreach($el_ood_id as $k => $v){
			$sql="";
			$t_nm = $el_ood[$k];
			if(!empty($v) && !empty($t_nm)){
				$sql = "UPDATE  chart_admn_drop_options SET  ";			
				$sqlW=" WHERE id= '".$v."' ";
			
			}else if(!empty($t_nm)){
				$sql = "INSERT INTO chart_admn_drop_options SET type='ood', del='0', ";
				$sqlW="";
			}
			
			if(!empty($sql)){
			$sql .= " name='".imw_real_escape_string($t_nm)."' ".$sqlW;
			$row = get_data_array($sql);
			}
		}
	}
}

if(!empty($_POST["el_submit"]) && $_POST["el_submit"]=="delete"){
	$el_anes_del=$_POST["el_anes_del"];
	$el_ood_del=$_POST["el_ood_del"];
	$el_dilate_del=$_POST["el_dilate_del"];
	
	if(count($el_anes_del)>0){
		foreach($el_anes_del as $k => $v){
			$sql = "UPDATE  chart_admn_drop_options SET del='1' WHERE id= '".$v."'  ";
			$row = imw_query($sql);
		}
	}
	
	if(count($el_ood_del)>0){
		foreach($el_ood_del as $k => $v){
			$sql = "UPDATE  chart_admn_drop_options SET del='1' WHERE id= '".$v."'  ";
			$row = imw_query($sql);
		}
	}
	
	if(count($el_dilate_del)>0){
		foreach($el_dilate_del as $k => $v){
			$sql = "UPDATE  chart_admn_drop_options SET del='1' WHERE id= '".$v."'  ";
			$row = imw_query($sql);
		}
	}
		
}

if(!empty($_POST["el_submit"])){	
	header("location:chart_admn_ophth_drops.php?op=".$_POST["el_submit"]);
	exit();
}
//--
	//Anas _db_options
	$arr_db_drops= $oadmn->get_drop_options_admin("", "1");
	$arr_db_anas = $arr_db_drops["anes"];
	$arr_db_dilate = $arr_db_drops["dilate"];
	$arr_db_ood = $arr_db_drops["ood"];
//--
	$addNew=3;//new fields

//--
?>
<script type="text/javascript">
	function delOphthDrops(){
		var l = $(":checked[name*=el_anes_del], :checked[name*=el_dilate_del], :checked[name*=el_ood_del]").length;
		if(l<=0){ top.fAlert("Please select options to delete.");  }
		else{
			if(confirm("Are you sure to delete records?")){	
				top.show_loading_image('show','300', 'Saving data...');
				$("#el_submit").val("delete");
				document.frm_ophth_drops.submit();
			}
		}
	}
	
	function saveOphthDrops(){		
		top.show_loading_image('show','300', 'Saving data...');
		$("#el_submit").val("save");
		document.frm_ophth_drops.submit();
	}
</script>
</head>
<body>
	<div class="whtbox">
	<form method="post" name="frm_ophth_drops" id="frm_ophth_drops">        	
	<div id="dv_anes" style="overflow:auto;">
		<div class="table-responsive respotable adminnw">
		  <table class="table table-bordered table-hover">
			<thead>
				<tr>                     
					<th style="width:50px;" class="text-center">S.No.</th>                    
					<th style="width:90%;" >Anesthetic</th>                    
					<th style="width:30px;" class="text-center">Delete</th>			    
				</tr>
			</thead>
			<tbody>
			<?php			
			$cntr=0;
			if(count($arr_db_anas)>0){
				$echo ="";
				foreach($arr_db_anas as $key => $ar_val){
					$nm = $ar_val[0];
					$id = $ar_val[1];
					
					if(!empty($nm)){
						$cntr=($key+1);
						$echo .= "<tr>
								<td style=\"width:45px;\" class=\"text-center\">".$cntr.".</td>
								<td style=\"width:90%;\" ><input type=\"text\" class=\"form-control\" name=\"el_anes[]\" value=\"".$nm."\"><input type=\"hidden\" name=\"el_anes_id[]\" value=\"".$id."\"></td>                    
								<td style=\"width:30px;\" class=\"text-center\"><div class=\"checkbox\"><input type=\"checkbox\" name=\"el_anes_del[]\" id=\"el_anes_del".$id."\" value=\"".$id."\"><label for=\"el_anes_del".$id."\"></label></div></td>								
							</tr>";
					}
				}				
			}
			
			//add five empty
			for($i=0; $i<$addNew;$i++){
			$cntr=$cntr+1;
			$echo .= "<tr>
					<td style=\"width:45px;\" class=\"text-center\">".$cntr.".</td>
					<td style=\"width:90%;\" ><input type=\"text\" class=\"form-control\" name=\"el_anes[]\" value=\"\"><input type=\"hidden\" name=\"el_anes_id[]\" value=\"\"></td> 
					<td style=\"width:30px;\" class=\"text-center\"></td>					
				</tr>";
			}
			
			echo $echo;	
			?>
      </tbody>
      </table>
   	</div>
    
	</div>  
	
	<div id="dv_dilate" style="overflow:auto;">
		<div class="table-responsive respotable adminnw">
			<table class="table table-bordered table-hover">
			<thead>
			<tr>                     
			    <th style="width:50px;" class="text-center">S.No.</th>                    
			    <th style="width:90%;" >Dilation</th>                    
			    <th style="width:30px;" class="text-center">Delete</th>			    
			</tr>
			</thead>
			<tbody>
		
		
		<?php	
			$echo ="";
			$cntr=0;
			if(count($arr_db_dilate)>0){
				
				foreach($arr_db_dilate as $key => $ar_val){
					$nm = $ar_val[0];
					$id = $ar_val[1];
					
					if(!empty($nm)){
						$cntr=($key+1);
						$echo .= "<tr>
								<td style=\"width:45px;\" class=\"text-center\">".$cntr.".</td>
								<td style=\"width:90%;\" ><input type=\"text\" class=\"form-control\" name=\"el_dilate[]\" value=\"".$nm."\"><input type=\"hidden\" name=\"el_dilate_id[]\" value=\"".$id."\"></td>                    
								<td style=\"width:30px;\" class=\"text-center\"><div class=\"checkbox\"><input type=\"checkbox\" name=\"el_dilate_del[]\" id=\"el_dilate_del".$id."\" value=\"".$id."\"><label for=\"el_dilate_del".$id."\"></label></div></td>	
							</tr>";
					}
				}
			}
			
			//add five empty
			for($i=0; $i<$addNew;$i++){
			$cntr=$cntr+1;
			$echo .= "<tr>
					<td style=\"width:45px;\" class=\"text-center\">".$cntr.".</td>
					<td style=\"width:90%;\" ><input type=\"text\" class=\"form-control\" name=\"el_dilate[]\" value=\"\"><input type=\"hidden\" name=\"el_dilate_id[]\" value=\"\"></td> 
					<td style=\"width:30px;\" class=\"text-center\"></td>
				</tr>";
			}
			
			echo $echo;	
		?>
    </tbody></table></div>
	</div>
	
	<div id="dv_ood" style="overflow:auto;">
		<div class="table-responsive respotable adminnw">
			<table class="table table-bordered table-hover">
			<thead>
			<tr>                     
			    <th style="width:50px;" class="text-center">S.No.</th>                    
			    <th style="width:90%;" >Other Ophtha. Drops</th>                    
			    <th style="width:30px;" class="text-center">Delete</th>			    
			</tr>
			</thead>
			<tbody>
			<?php
			$echo ="";
			$cntr=0;
			if(count($arr_db_ood)>0){
				
				foreach($arr_db_ood as $key => $ar_val){
					$nm = $ar_val[0];
					$id = $ar_val[1];
					
					if(!empty($nm)){
						$cntr=($key+1);
						$echo .= "<tr>
								<td style=\"width:45px;\" class=\"text-center\">".$cntr.".</td>
								<td style=\"width:90%;\" ><input type=\"text\"  class=\"form-control\" name=\"el_ood[]\" value=\"".$nm."\"><input type=\"hidden\" name=\"el_ood_id[]\" value=\"".$id."\"></td>
								<td style=\"width:30px;\" class=\"text-center\"><div class=\"checkbox\"><input type=\"checkbox\" name=\"el_ood_del[]\" id=\"el_ood_del".$id."\" value=\"".$id."\"><label for=\"el_ood_del".$id."\"></label></div></td>
							</tr>";
					}
				}
			}
			
			//add five empty
			for($i=0; $i<$addNew;$i++){
			$cntr=$cntr+1;
			$echo .= "<tr>
					<td style=\"width:45px;\" class=\"text-center\">".$cntr.".</td>
					<td style=\"width:90%;\" ><input type=\"text\" class=\"form-control\" name=\"el_ood[]\" value=\"\"><input type=\"hidden\" name=\"el_ood_id[]\" value=\"\"></td>
					<td style=\"width:30px;\" class=\"text-center\"></td>
				</tr>";
			}
			
			echo $echo;	
		?>
    </tbody>
    </table>
    </div>
	</div>
	<input type="hidden" id="el_submit" name="el_submit" value="">
    </form>											
</div>
<script>
var msg = '<?php echo $_GET["op"]; ?>';
if(msg!=""){
	msg = (msg=="save") ? "Records are saved." : "Records are deleted. ";
	top.alert_notification_show(msg);
}
top.show_loading_image('none');
var ar = [["saveOphthDrops","Save","top.fmain.saveOphthDrops();"],["deleteOphthDrops","Delete","top.fmain.delOphthDrops();"]];
top.btn_show("ADMN",ar);
set_header_title('Ophth. Drops');
var window_height = parseInt(window.innerHeight - $('footer',top.document).outerHeight());
var	divHeight = (window_height / 3);
$("#dv_anes, #dv_dilate, #dv_ood").css('height', divHeight);
</script>
<?php	
	require_once("../admin_footer.php");
?>