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
require_once(dirname(__FILE__).'/../../config/globals.php');
//require_once($GLOBALS['fileroot'].'/library/classes/scheduler/appt_schedule_functions.php');

//disp date
$disp_date=get_date_format($_REQUEST["load_dt"],'','','',"/");

$qry0 = "SELECT fname, lname, mname FROM users WHERE id = '".$_REQUEST["prov_id"]."'";
$res0 = imw_query($qry0);
if(imw_num_rows($res0) > 0){
	$arr0 = imw_fetch_assoc($res0);
	$disp_prov = core_name_format($arr0["lname"], $arr0["fname"], $arr0["mname"]);
}

$qry = "SELECT pn.operator_id, pn.provider_notes_id, pn.provider_notes, u2.fname as ofname, u2.lname as olname, u2.mname as omname FROM provider_notes pn LEFT JOIN users u2 ON u2.id = pn.operator_id WHERE pn.provider_id = '".$_REQUEST["prov_id"]."' AND pn.notes_date = '".$_REQUEST["load_dt"]."' AND pn.delete_status = 0";
$res = imw_query($qry);
?>
	<div><?php echo $disp_date;?> - Notes for <?php echo $disp_prov;?></div>
~~~~~
	<div style="height:150px;overflow:auto;overflow-x:hidden;">
		<table class="table table-striped table-bordered table-hover">
			<?php
			if(imw_num_rows($res) > 0){
				//$arr = $res->GetArray();
				while($arr=imw_fetch_assoc($res)){
					$a++;
					//operator name
					$disp_oper = core_name_format($arr["olname"], $arr["ofname"], $arr["omname"]);

					if($arr["operator_id"] == $_SESSION["authId"] || core_check_privilege(array("priv_Sch_Override")) == true){
						$edit_link_act = "edit_provider_notes('".$arr["provider_notes_id"]."');";
					}else{
						$edit_link_act = "void(0);";
					}
					?>
			<tr>
				<td><?php echo $a;?></td>
				<td id="existing_notes<?php echo $arr["provider_notes_id"];?>" onclick="javascript:<?php echo $edit_link_act;?>">
					<?php echo stripslashes($arr["provider_notes"]);?>
                </td>
				<td><?php echo $disp_oper;?></td>
				<td>
				<?php 
				if($arr["operator_id"] == $_SESSION["authId"] || core_check_privilege(array("priv_Sch_Override")) == true){
				?>
				<img src="<?php echo $GLOBALS['webroot']?>/library/images/edit.png"  onclick="javascript:<?php echo $edit_link_act;?>" style="cursor:pointer;" />
				<?php } ?>
				</td>
                
				<td>
				<?php 
				if($arr["operator_id"] == $_SESSION["authId"]){
					?>
				<img src="<?php echo $GLOBALS['webroot']?>/library/images/close14.png"  onclick="javascript:delete_provider_notes('<?php echo $arr["provider_notes_id"];?>', '<?php echo $_REQUEST["load_dt"];?>');" style="cursor:pointer;" />
					<?php
				}
				?>
				</td>
			</tr>
					<?php
				}
			}else{
				?>
			<tr>
            	<td class="col-sm-12">
					No Record Found.
				</td>
			</tr>
				<?php
			}
			?>
		</table>
	</div>
    <div class="clearfix"></div>
	<?php
	if(core_check_privilege(array("priv_Sch_Override")) == true){
		?>
	<div class="grythead row"><div class="col-sm-12">Add / Update Note</div></div>
	<div class="row"><div class="col-sm-12">
		<input type="hidden" id="new_prov_note_act" name="new_prov_note_act" value="0" />
		<input type="hidden" id="new_prov_note_id" name="new_prov_note_id" value="<?php echo $_REQUEST["prov_id"];?>" />
		<textarea id="new_prov_note" name="new_prov_note" class="form-control" cols="75" rows="2"></textarea>
	</div></div>
		<?php
	}
	?>
~~~~~
	<?php
    if(core_check_privilege(array("priv_Sch_Override")) == true){
    ?>
        <button type="button" class="btn btn-success" onclick="javascript:new_provider_notes();">New</button>
        <button type="button" class="btn btn-success" onClick="javascript:save_provider_notes('<?php echo $_REQUEST["load_dt"];?>');">Save</button>
        <button type="button" class="btn btn-danger" onclick="javascript:hide_provider_notes();">Close</button>
    <?php
    }else{
    ?>
        <button type="button" class="btn btn-danger" onclick="javascript:hide_provider_notes();">Close</button>
    <?php
    }
    ?>