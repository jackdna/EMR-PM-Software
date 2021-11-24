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
*
****************************************************************************
*
* File: get_co_ins.php
* Purpose: Get insurance company copay
* Access Type: Direct 
*
****************************************************************************/

require_once("../../../../config/globals.php");
$insurance_id = $_REQUEST["ins_id"];
$qry_id = "select co_ins from insurance_companies where id = '".$insurance_id."' limit 0,1";
$res_id = imw_query($qry_id);
$co_ins = "";
while($row_id = imw_fetch_array($res_id)){

	$co_ins = $row_id["co_ins"];
}
if($co_ins  != ""){
	echo $co_ins;
}
else{
	echo "00/00";
}
?>