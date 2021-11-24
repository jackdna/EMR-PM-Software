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
require_once("../../admin_header.php");
$arr_ch = array("Patient Choices", "MBN", "ECP", "IOL Master K's Recommendations" );
if(isset($_POST["el_op"])){
	$flg_redirect=0;
	foreach($arr_ch as $k => $ctype){		
		for($i=0;1==1;$i++){	

			if(isset($_POST["el_chnm".$i."_".$k])){
			
				$flg_redirect=1;
				$el_id = $_POST["el_eid".$i."_".$k];
				$el_chdel = $_POST["el_chdel".$i."_".$k];	
				if($_POST["el_op"] == "delete"){
					if(!empty($el_chdel)){					
						//update
						$sql = "UPDATE admin_sps_options SET del_status='1' WHERE id='".$el_id."'  ";
						$row=sqlQuery($sql);
					}
					continue;
				}
			
				if(!empty($_POST["el_chnm".$i."_".$k])){
					
					$el_chnm = $_POST["el_chnm".$i."_".$k];	
					
					if(!empty($el_id)){
						//update
						$sql = "UPDATE admin_sps_options SET choice_nm='".imw_real_escape_string($el_chnm)."' WHERE id='".$el_id."'  ";
						$row=sqlQuery($sql);
						
					}else{
						//update
						$sql = "INSERT INTO admin_sps_options SET choice_nm='".imw_real_escape_string($el_chnm)."', type='".imw_real_escape_string($ctype)."', pro_id='0'  ";
						$row=sqlQuery($sql);
						
					}
				}
			
			}else{break;}
		}
	}
	
	if($flg_redirect==0){
		unset($_POST);
		header("location: sx_planning.php");
		exit();
	}
	
}
?>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/core_main.js"></script>
<script type="text/javascript">
	function saveform(){
		document.frm_sx_pln.submit();
	}
	function deleteSelectet(){
		$("#el_op").val("delete");
		saveform();	
	}	
</script>
<body>
<div class="whtbox">
	<form name="frm_sx_pln" action="sx_planning.php" method="post">
	<input type="hidden" id="el_op" name="el_op" value="save">
		<?php
			foreach($arr_ch as $k => $ctype){
		?>
		<div class="purple_bar"><?php echo $ctype;?></div>
		<div class="sx_cont_div table-responsive respotable adminnw" style="overflow-x:hidden; overflow-y:scroll;">
			<table class="table table-bordered table-hover">
			<?php
				$sql = "select * FROM admin_sps_options where del_status='0' AND type='".imw_real_escape_string($ctype)."' AND pro_id='0' ORDER BY choice_nm ";
				$rez=imw_query($sql);
				for($i=0;$row=imw_fetch_array($rez);$i++){
					$id=$row["id"];
					$choice_nm=$row["choice_nm"];
			?>
			<tbody>
				<tr>
					<td style="width:20px; padding-left:8px;">
						<div class="checkbox">
							<input type="checkbox" id="el_chdel<?php echo $i."_".$k;?>" name="el_chdel<?php echo $i."_".$k;?>" value="<?php echo $id; ?>">
							<label for="el_chdel<?php echo $i."_".$k;?>" ></label>
						</div>
						<input type="hidden" id="el_eid<?php echo $i."_".$k;?>" name="el_eid<?php echo $i."_".$k;?>" value="<?php echo $id; ?>">
					</td>
					<td>
						<input type="text" id="el_chnm<?php echo $i."_".$k;?>" name="el_chnm<?php echo $i."_".$k;?>" value="<?php echo $choice_nm; ?>" class="form-control">
					</td>
				</tr>
				<?php } $j=$i+2; for(;$i<=$j;$i++){ ?>
				<tr>
					<td style="width:20px; padding-left:8px;">
						<div class="checkbox">
							<input type="checkbox" id="el_chdel<?php echo $i."_".$k;?>" name="el_chdel<?php echo $i."_".$k;?>" value="">
							<label for="el_chdel<?php echo $i."_".$k;?>" ></label>
						</div>
					</td>
				<td><input type="text" id="el_chnm<?php echo $i."_".$k;?>" name="el_chnm<?php echo $i."_".$k;?>" value="" class="form-control"> </td>
			</tr>
			<?php } ?>
			</tbody>
			</table>
		</div>
		<?php }//?>
		</div>                
	</form> 
</div>	
<script type="text/javascript">		
	var ar = [["save","Save","top.fmain.saveform();"],["dx_cat_del","Delete","top.fmain.deleteSelectet();"]];
	top.btn_show("ADMN",ar);
	show_loading_image('none');
	set_header_title('Sx Planning');
	$(document).ready(function(){
		$('.sx_cont_div').each(function(id,elem){
			var window_height = $(window).height();
			div_height = parseInt(window_height/4 - 45);
			$(elem).height(div_height);
		});
	});	
</script>
<?php
	require_once("../../admin_footer.php");
?>        