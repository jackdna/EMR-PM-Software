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
?>
	<script type="text/javascript">
			var arrAllShownRecords = new Array();
			var totalRecords	   = 0;
			var formObjects		   = new Array('id','comment');
			function LoadResultSet(p,f,s,so,currLink){//p=practice code, f=fac code, s=status, so=sort by;
				top.show_loading_image('hide');
				top.show_loading_image('show','300', 'Loading Dx Category...');
				
				if(typeof(s)!='string' || s==''){s = 'Active';}
				s_url = "&s="+s;
				
				if(typeof(p)=='undefined'){p_url='';}else{p_url='&p='+p};
				if(typeof(f)=='undefined'){f_url='';}else{f_url='&f='+f};
				
				oso		= $('#ord_by_field').val(); //old_so
				soAD	= $('#ord_by_ascdesc').val();
				if(typeof(so)=='undefined' || so==''){
					so 		= $('#ord_by_field').val();
				}else{
					$('#ord_by_field').val(so);
					if(oso==so){
						if(soAD=='ASC') soAD = 'DESC';
						else  soAD = 'ASC';
					}else{
						soAD = 'ASC';
					}
					$('#ord_by_ascdesc').val(soAD);
				};
				if(soAD=='ASC') $(currLink).find('span').removeClass('glyphicon glyphicon-chevron-down pull-right').addClass('glyphicon glyphicon-chevron-up pull-right');
				else $(currLink).find('span').removeClass('glyphicon glyphicon-chevron-up pull-right').addClass('glyphicon glyphicon-chevron-down pull-right');
				so_url='&so='+so+'&soAD='+soAD;
						
				ajaxURL = "cpt_categories_ajax.php?task=show_list"+s_url+p_url+f_url+so_url;
				$.ajax({
				  url: ajaxURL,
				  success: function(r) {
					showRecords(r);
				  }
				});
			}
			function showRecords(r){
				r = JSON.parse(r);
				result = r.records;
				h='';
				if(r != null){
					row = '';
					for(x in result){
						s = result[x];
						rowData = new Array();
						row += '<tr>';
						for(y in s){
							tdVal = s[y];
							if(y=='cpt_cat_id'){pkId = tdVal; row += '<td style="width:20px; padding-left:10px;"><div class="checkbox"><input type="checkbox" name="id" id="'+tdVal+'" class="chk_sel" value="'+pkId+'"><label for="'+tdVal+'"></label></div></td>';}
							rowData[y] = tdVal;
							if(y=='cpt_category'){
								row	+= '<td onclick="addNew(1,\''+pkId+'\');">&nbsp;'+tdVal+'</td>';
							}
						}
						totalRecords++;
						row += '</tr>';
						arrAllShownRecords[pkId] = rowData;//this array will be used to fill edit record data in form.
					}
					h = row;
				}
				$('#result_set').html(h);		
				top.show_loading_image('hide');
			}
			function addNew(ed,pkId){
				var modal_title = '';
				if(typeof(ed)!='undefined' && ed!=''){modal_title = 'Edit Record';}
				else {
					modal_title = 'Add New Record';
					$('#cpt_cat_id').val('');
					document.add_edit_frm.reset();
				}
				$('#myModal .modal-header .modal-title').text(modal_title);
				$('#myModal').modal('show');
				if((typeof(ed)!='undefined' && ed!='') && (typeof(pkId)!='undefined' && pkId>0)){fillEditData(pkId);}
			}
			function saveFormData(){
				top.show_loading_image('hide');
				top.show_loading_image('show','300', 'Saving data...');
				frm_data = $('#add_edit_frm').serialize()+'&task=save_update';
				$.ajax({
					type: "POST",
					url: "cpt_categories_ajax.php",
					data: frm_data,
					success: function(d) {
						top.show_loading_image('hide');
						if(d=='enter_unique'){
							top.fAlert('Catagory already exist.');		
							return false;
						}
						if(d.toLowerCase().indexOf('success') > 0){
							top.alert_notification_show(d);
						}else{
							top.fAlert(d);
						}
						$('#myModal').modal('hide');
						LoadResultSet();
					}
				});
			}
			function deleteSelectet(){
				pos_id = '';
				$('.chk_sel').each(function(){
					if($(this).is(':checked')){
						pos_id += $(this).val()+', ';
					}
				})
				if(pos_id!=''){
					a = confirm('Are you sure, want to delete?');
					if(!a) return false;
					pos_id = pos_id.substr(0,pos_id.length-2);
					top.show_loading_image('hide');
					top.show_loading_image('show','300', 'Deleting Record(s)...');
					frm_data = 'pkId='+pos_id+'&task=delete';
					$.ajax({
						type: "POST",
						url: "cpt_categories_ajax.php",
						data: frm_data,
						success: function(d) {
							top.show_loading_image('hide');
							if(d=='1'){top.alert_notification_show('Record deleted.'); LoadResultSet();}
							else{top.fAlert('Record delete failed. Please try again.');}
						}
					});
				}else{
					top.fAlert('No Record Selected.');
				}
			}
			function fillEditData(pkId){
				f = document.add_edit_frm;
				e = f.elements;
				$('#cpt_cat_id').val(pkId);
				for(i=0;i<e.length;i++){
					o = e[i];
					if($.inArray(o.comment,formObjects)){
						on	= o.name;
						//alert(arrAllShownRecords);
						v	= arrAllShownRecords[pkId][on];
						if (o.tagName == "INPUT" || o.tagName == "SELECT"){
							if (o.type == "checkbox" || o.type == "radio"){
								oid = on+'_'+v;
								$('#'+oid).attr('checked',true);
							} else if(o.type!='submit' && o.type!='button'){
								o.value = v;
							}
						}
					}
				}		
			}
			function cptlist(){
				var btnArr = new Array();
				top.btn_show("ADMN",btnArr);
				show_loading_image('inline');
				top.fmain.location.href = "../admin/billing/cpt_fee_tbl/index.php";
			}
		</script>
	</head>
	<body>
    <input type="hidden" name="ord_by_field" id="ord_by_field" value="cpt_category">
	<input type="hidden" name="ord_by_ascdesc" id="ord_by_ascdesc" value="ASC">
    <div class="whtbox">
		<div class="table-responsive respotable adminnw">
			<table class="table table-bordered table-hover table-striped">
				<thead>
					<tr>
						<th style="width:20px; padding-left:8px;"><div class="checkbox"><input type="checkbox" name="chk_sel_all" id="chk_sel_all" value=""><label for="chk_sel_all"></label></div></th>
						<th onClick="LoadResultSet('','','','cpt_category',this);">Categories<span></span></th>
					</tr>
				</thead>
				<tbody id="result_set"></tbody>
			</table>
		</div>
	</div>
	<div id="myModal" class="modal fade" role="dialog">
		<div class="modal-dialog"> 
			<div class="modal-content">
				<div class="modal-header bg-primary">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title" id="modal_title">Modal Header</h4>
				</div>
				<form name="add_edit_frm" id="add_edit_frm" onSubmit="saveFormData();return false;">
					<input type="hidden" name="cpt_cat_id" id="cpt_cat_id" value="">	
					<div class="modal-body">
						<div class="form-group">
							<label for="DepartmentCode">Name</label>
							<input type="text" name="cpt_category" id="cpt_category" required class="form-control" />
						</div>
					</div>	
					<div class="modal-footer">
						<button type="submit" class="btn btn-success">Save</button>
						<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
					</div>
				</form>
			</div>
		</div>
	</div>		
	</body>
    <script type="text/javascript">
		LoadResultSet();
		var ar = [["add_new","Add New","top.fmain.addNew();"],["dx_cat_del","Delete Selected","top.fmain.deleteSelectet();"],["dx_cat","CPT List","top.fmain.cptlist();"]];
		top.btn_show("ADMN",ar);
		$(document).ready(function(){
			check_checkboxes();
		});
		show_loading_image('none');
	</script>

</html>
       