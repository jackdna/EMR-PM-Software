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
//----------------------- Auth Info -----------------------//
require_once("../../config/globals.php");
require_once("../../library/classes/common_function.php"); 
$patient_id=$_SESSION['patient'];

$case_id = (int)xss_rem($_REQUEST['case_id'], 3);	/* Sanitization to prevent arbitrary values - Security Fix */

$current_auth_name=$_REQUEST['auth_name'];
$qry =imw_query("select auth_name,a_id,AuthAmount,ins_case_id from patient_auth 
				where patient_id='$patient_id' and ins_case_id='$case_id' and auth_status='0' and no_of_reffs>0 order by a_id desc");
//----------------------- Auth Info -----------------------//
$auth_info="";
$auth_info='<div class="div_popcontent">
		<table class="table table-bordered table-hover table-striped result_data">
            <tr class="grythead">
				<th>&nbsp;</th>
                <th>
                    Authorization#
                </th>
                <th>
                    Amount
                </th>
            </tr>';
            if(imw_num_rows($qry)>0){
				while($authQryRes=imw_fetch_array($qry)){
					if($current_auth_name==$authQryRes['auth_name']){
						$color="E1E5EF";
					}else{
						$color="ffffff";
					}
					$auth_info.='<tr class="text-center"><td>';
					if($current_auth_name==$authQryRes['auth_name']){
						$auth_info.='<img src="../../library/images/confirm.gif" width="16px">';
					}
					$auth_info.='</td><td>';
					$auth_info.=$authQryRes['auth_name']; 
					$auth_info.='</td><td>';
					$auth_info.='$'.$authQryRes['AuthAmount'];
					$auth_info.='</td> </tr>';
            	} 
			} 
$auth_info.='</table></div>';
echo $auth_info;
?>