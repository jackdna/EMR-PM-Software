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
	$hcfa_save_data="";
	$hcfa_pat_id=$patientListData->patient_id;
	$hcfa_enc_id=$patientListData->encounter_id;
	$hcfa_created_date=date('Y-m-d');
	$hcfa_opr_id=$_SESSION['authUserID'];
	//pre($prev_hcfa_arr);
	$hcfa_save_data=implode('',$prev_hcfa_arr);
	$hcfa_save_data = imw_real_escape_string($hcfa_save_data);
	$balance_proc_charges = str_replace(',','',$balance_proc_charges);
	$prev_hcfa_qry="insert into previous_hcfa set operator_id='$hcfa_opr_id',patient_id='$hcfa_pat_id',enc_id='$hcfa_enc_id',
					created_date='$hcfa_created_date',hcfa_data='$hcfa_save_data',enc_balance='$balance_proc_charges'";
    $prev_hcfa_run=imw_query($prev_hcfa_qry);
?>