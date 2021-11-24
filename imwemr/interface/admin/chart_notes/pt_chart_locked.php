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
require_once($GLOBALS['srcdir']."/classes/work_view/wv_functions.php");
require_once($GLOBALS['fileroot'].'/library/classes/work_view/ChartPtLock.php');

$oChartLock = new ChartPtLock($_SESSION["authId"]);
if(isset($_POST["elem_unlock"]) && !empty($_POST["elem_unlock"])){	
	$arrTmp = array();
	$arrTmp = $_POST["elem_unlock"];
	$oChartLock->releaseRecords($arrTmp);
}
?>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/admin/admin_pt_chart_locked.js"></script>
<body>
<input type="hidden" name="ord_by_field" id="ord_by_field" value="pt_id">
<input type="hidden" name="ord_by_ascdesc" id="ord_by_ascdesc" value="ASC">
<div class="whtbox">
	<form name="elem_frm_Lock" action="pt_chart_locked.php" method="post" onSubmit="return checkLockedPt(this);">
		<div class="table-responsive respotable adminnw">
			<table class="table table-bordered table-hover">
				<thead>
					<tr>
						<th style="width:20px; padding-left:8px;"><div class="checkbox"><input type="checkbox" name="chk_sel_all" id="chk_sel_all" value=""><label for="chk_sel_all"></label></div></th>
						<th onClick="LoadResultSet('','','','patient_data.lname',this,'Locked');">Patient<span></span></th>
						<th onClick="LoadResultSet('','','','users.lname',this,'Locked');">User<span></span></th>
						<th onClick="LoadResultSet('','','','users.lname',this,'Locked');">Tab<span></span></th>
					<tr>
				</thead>
				<tbody id="result_set"></tbody>
			</table>
		</div>
	</form>
</div>
<?php	
	require_once("../admin_footer.php");
?>