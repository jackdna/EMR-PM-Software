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

include_once('../admin_header.php');
$library_path = $GLOBALS['webroot'].'/library';
?>
 	<input type="hidden" name="ord_by_field" id="ord_by_field" value="LastName,FirstName">
	<input type="hidden" name="ord_by_ascdesc" id="ord_by_ascdesc" value="ASC">
	<input type="hidden" name="pg_aplhabet" id="pg_aplhabet" value="A">
	<input type="hidden" name="page" id="page" value="1">
	<input type="hidden" name="status" id="status" value="">
	<div class="whtbox">
		<div class="table-responsive respotable">
			<table class="table table-bordered table-hover table-striped adminnw">
				<thead>
					<tr>
						<th style="width:20px; padding-left:10px;">
							<div class="checkbox"><input type="checkbox" name="chk_sel_all" id="chk_sel_all" value="">
								<label for="chk_sel_all"></label>
							</div>
						</th>
						<th onClick="LoadResultSet('','LastName',this);" class="pointer">Name<span></span></th>
						<th onClick="LoadResultSet('','Address1',this);" class="pointer">Address<span></span></th>
						<th onClick="LoadResultSet('','NPI',this);" class="pointer">NPI#<span></span></th>
						<th onClick="LoadResultSet('','MDCR',this);" class="pointer">MDCR# / CCN#<span></span></th>
						<th onClick="LoadResultSet('','MDCD',this);" class="pointer">MDCD# / TIN#<span></span></th>
						<th onClick="LoadResultSet('','Texonomy',this);" class="pointer">Taxonomy#<span></span></th>
						<th onClick="LoadResultSet('','start_date',this);" class="pointer">I.R.Date<span></span></th>
						<th onClick="LoadResultSet('','end_date',this);" class="pointer">L.R.Date<span></span></th>
						<th class="">Password<span></span></th>
						<th class="">Lock<span></span></th>
						<th class="">Status<span></span></th>
					</tr>
				</thead>
				<tbody id="result_set"></tbody>
			</table>
		</div>
	</div>
	<div class="pgn_prnt">
		<div class="row">
		<!-- Paging -->
			<div class="col-sm-8 pagingcs text-center" id="div_pages" ></div>
			<div class="col-sm-1 ">&nbsp;</div>

			<!-- Records Per Page -->        
			<div class="col-sm-3 form-inline recodpag">Records per page 
				<select class="form-control minimal" name="record_limit" id="record_limit" onChange="LoadResultSet()">
					<option value="20">20</option>
					<option value="50">50</option>
					<option value="100">100</option>
					<option value="200">200</option>
				</select>
			</div>
		</div>
		<div class="clearfix"></div>
		<div class="row">
			<!-- Alphabet/Number Filter -->
			<div class="col-sm-8 text-center" id="pagenation_alpha_order">
				<nav aria-label="...">
					<ul class="pagination">
						<?php 
						$start = (int) ord('A');
						$end = (int) ord('Z');
						$html = '';
						for( $i = $start; $i <= $end; $i++)
						{
							$char = chr($i);
							$li_class = ( $char == 'A' ) ? 'active' : '';
							$onClick = 'onClick="LoadResultSet(\'\',\'\',\'\',\''.$char.'\')"';
							$html	.=	'<li class="pointer '.$li_class.'"><a id="'.$char.'" '.$onClick.'>'.$char.'</a></li>';
						}
						echo $html;
						?>
					</ul>
				</nav>
			</div>
            <div class="col-sm-1 form-inline text-center"><ul class="pagination pgn_az"><li class="pointer"><a onClick="LoadResultSet('','','','az')">A - Z</a></li></ul></div>
			<div class="col-sm-3 form-inline activuser">
				<!-- Search -->
				<div class="input-group">
					<input type="text" class="form-control" name="search_reff" id="search_reff" placeholder="Search" />
				  <div class="input-group-addon pointer" onClick="srh_referring_phy();"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></div>
					</div>

				<!-- Status Filter -->
				<select class="form-control minimal" name="srchStatus" id="srchStatus" onChange="javascript:LoadResultSet(this.value);">
					<option value="0">Active</option>
				  <option value="1">Inactive</option>
				  <option value="2">Not Confirmed</option>
				  <option value="all">All</option>
				</select>
			</div>
		</div>
	</div>
	<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/admin/admin_ref_phy.js"></script>
<?php 
	include 'modals.php'; 
	include '../admin_footer.php'; 
?>
