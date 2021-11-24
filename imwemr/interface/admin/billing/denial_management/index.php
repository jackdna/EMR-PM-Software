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
$operator_id=$_SESSION['authId'];
$cur_date_time=date('Y-m-d H:i:s');

$sql_qry = imw_query("select cpt_fee_id,cpt_prac_code,cpt_desc from cpt_fee_tbl where cpt_prac_code!='' order by cpt_prac_code ASC");
if(imw_num_rows($sql_qry) > 0){
	while($sql_row=imw_fetch_array($sql_qry)){
		$cpt_code_arr[$sql_row["cpt_fee_id"]]=$sql_row;	
	}
}

$sql_qry = imw_query("select * from cas_reason_code order by cas_code ASC");
if(imw_num_rows($sql_qry) > 0){
	while($sql_row=imw_fetch_array($sql_qry)){
		$cas_code_arr[$sql_row["cas_id"]]=$sql_row;	
	}
}	

if($txtsave){
	$cpt_code_imp=implode(',',$_POST['cpt_code']);
	$cas_code_imp=implode(',',$_POST['cas_code']);
	$denial_resp_all=$_POST['denial_resp_all'];
	imw_query("update denial_resp set denial_resp_all='$denial_resp_all',cpt_code_resp='$cpt_code_imp',cas_code_resp='$cas_code_imp',modified_date='$cur_date_time',modified_by='$operator_id' where denial_resp_id='1'");
}

$den_qry = imw_query("select * from denial_resp where denial_resp_id='1'");
$den_row=imw_fetch_array($den_qry);
$ins_cpt_code_arr=explode(',',$den_row['cpt_code_resp']);	
$ins_cas_code_arr=explode(',',$den_row['cas_code_resp']);	
$denial_resp_all=$den_row['denial_resp_all'];	
?>
</script>
</head>
<link href="<?php echo $GLOBALS['webroot']; ?>/library/css/core.css" rel="stylesheet">
<style>
	#div_loading_image { position: inherit; top: inherit; }
</style>
<body>
<form name="denialFrm" id="denialFrm" method="post">
<input type="hidden" name="txtsave" id="txtsave" value="save">
<div class="whtbox tblBg grpbox">
	<div>
        <div class="row pt10">
            <div class="col-sm-12">
                <div class="form-group">
                    <div class="checkbox">
                        <input type="checkbox" name="denial_resp_all" id="denial_resp_all" value="1" <?php if($denial_resp_all=="1"){ echo "checked";} ?> onClick="show_cpt_cas()"/>
                        <label for="denial_resp_all">Default - Move all Denial transactions to next responsible party</label>
                    </div>
                </div>
            </div>
        </div>
        <?php $cpt_cas_cls="";if($denial_resp_all==1){$cpt_cas_cls=" hide";}?>   
        <div class="row pt10 cpt_cas <?php echo $cpt_cas_cls; ?>">
            <div class="col-sm-12">
                <div class="form-group">
                   <label>Move Denial transaction to next responsible for selected CPT Codes</label><br>
                    <select name="cpt_code[]" id="cpt_code" class="selectpicker" data-width="350" multiple data-actions-box="true" data-title="Select CPT Code" data-header="Select CPT Code" data-size="15" size="10" data-live-search="true">
                        <?php 
                            foreach($cpt_code_arr as $cpt_key => $cpt_val){
                                $cpt_fee_id = $cpt_code_arr[$cpt_key]['cpt_fee_id'];
                                $cpt_prac_code = $cpt_code_arr[$cpt_key]['cpt_prac_code'];
                                $cpt_desc = $cpt_code_arr[$cpt_key]['cpt_desc'];
                                
                                if(in_array($cpt_fee_id,$ins_cpt_code_arr)){
                                    $sel = 'selected="selected"';
                                }else{
                                    $sel = '';
                                }
                                print '<option '.$sel.' value="'.$cpt_fee_id.'">'.$cpt_prac_code.' - '.$cpt_desc.'</option>';
                            }
                        ?>
                    </select>
                </div>
            </div>
            
            <div class="col-sm-12 pt10" style="margin-left:120px;"> <strong>OR</strong> </div>
            
            <div class="col-sm-12 pt10">
                <div class="form-group">
                    <label>Move Denial transaction to next responsible for selected CAS Codes</label><br>
                    <select name="cas_code[]" id="cas_code" class="selectpicker" data-width="350" multiple data-actions-box="true" data-title="Select CAS Code" data-header="Select CAS Code" data-size="15" data-live-search="true">
                        <?php 
                            foreach($cas_code_arr as $cas_key => $cas_val){
                                $cas_id = $cas_code_arr[$cas_key]['cas_id'];
                                $cas_code = $cas_code_arr[$cas_key]['cas_code'];
                                $cas_desc = $cas_code_arr[$cas_key]['cas_desc'];
                                
                                if(in_array($cas_code,$ins_cas_code_arr)){
                                    $sel = 'selected="selected"';
                                }else{
                                    $sel = '';
                                }
                                print '<option '.$sel.' value="'.$cas_code.'">'.$cas_code.' - '.substr($cas_desc,0,110).'</option>';
                            }
                        ?>
                    </select>
                </div>
            </div>
        </div>
        <div class="row pt10"></div>
    </div>    
</div>
</form>
<script type="text/javascript">	
	var loder_icon = '<div id="div_loading_image" class="text-center"><div class="loading_container"><div class="process_loader"></div><div id="div_loading_text" class="text-info"></div></div></div>';
	var btnArr = new Array();
	btnArr[0] = new Array("save_denial","Save","top.fmain.document.denialFrm.submit();");
	top.btn_show("ADMN",btnArr);
	set_header_title('Denial Management');
	show_loading_image('none');
	function show_cpt_cas(){
		if($('#denial_resp_all').is(':checked')){
			$('.cpt_cas').addClass("hide");
		}else{
			$('.cpt_cas').removeClass("hide");
		}
	}
</script>
<?php 
	require_once("../../admin_footer.php");
?>