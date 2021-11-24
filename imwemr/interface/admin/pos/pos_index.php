<?php
include_once('../admin_header.php');

/* Get Task Manager Categories */
$merchantArr = array();
$sql = 'SELECT * FROM tsys_merchant where merchant_status=0 ';
$resp = imw_query($sql);
$merchant_option = "";
if ($resp && imw_num_rows($resp) > 0) {
    while ($row = imw_fetch_assoc($resp)) {
        $merchantArr[$row['id']] = $row['merchantName'];
        $merchant_option .= "<option value='" . $row['id'] . "'>" . $row['merchantName'] . "</option>";
    }
}
$facilityArr = array();
$sql1 = "select id, name from `facility` order by `name`";
$resp1 = imw_query($sql1);
$facility_option = "";
if ($resp1 && imw_num_rows($resp1) > 0) {
    while ($row1 = imw_fetch_assoc($resp1)) {
        $facilityArr[$row1['id']] = $row1['name'];
        $facility_option .= "<option value='" . $row1['id'] . "'>" . $row1['name'] . "</option>";
    }
}
?>
<body>
    <div class="container-fluid">
        <input type="hidden" name="preObjBack" value="">
        <textarea id="hidd_reason_text" style="display:none;"></textarea>
        <div class="whtbox" style="height:<?php echo ($_SESSION['wn_height'] - 305); ?>px; overflow-x:hidden; overflow-y:auto;">
            <div class="table-responsive provtab">
                <table class="table table-bordered table-hover adminnw">
                    <thead>
                        <tr>
                            <th width="1%">
                                <div class="checkbox">
                                    <input type="checkbox" name="chk_sel_all" id="chk_sel_all" value="" autocomplete="off">
                                    <label for="chk_sel_all">&nbsp;</label>
                                </div>
                            </th>
                            <th width="19%">Merchant Name</th>
                            <th width="15%">Device Name</th>
                            <th width="10%">Device ID</th>
                            <th width="10%">Developer ID</th>
                            <th width="10%">Application ID</th>
                            <th width="10%">IP Address</th>
                            <th width="5%">Port</th>
                            <th width="15%">Facility Name</th>
                            <th width="5%"></th>
                        </tr>
                    </thead>
                    <tbody id="tsys_result_set"></tbody>
                </table>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>


    <div class="common_wrapper">
        <div id="deviceModal" class="modal" role="dialog">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <form name="add_edit_frm" id="add_edit_frm">
                        <input type="hidden" name="id" id="id" value="" class="form-control">
                        <div class="modal-header bg-primary">
                            <button type="button" class="close" data-dismiss="modal">x</button>
                            <h4 class="modal-title" id="modal_title">POS Device</h4>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="merchant_id">Merchant</label>
                                        <select name="merchant_id" id="merchant_id"  class="selectpicker" data-width="100%" data-title="Select">
                                            <?php echo $merchant_option; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="facility_id">Facility</label>
                                        <select name="facility_id" id="facility_id"  class="selectpicker" data-width="100%" data-title="Select">
                                            <?php echo $facility_option; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="deviceName">Device Name</label>
                                        <input type="text" name="deviceName" id="deviceName" value="" class="form-control">
                                    </div>	
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="deviceID">Device ID</label>
                                        <input type="text" name="deviceID" id="deviceID" value="" class="form-control">
                                    </div>	
                                </div>
                                <div class="clearfix"></div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="developerID">Developer ID</label>
                                        <input type="text" name="developerID" id="developerID" value="003066" disabled readonly class="form-control">
                                    </div>	
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="applicationID">Application ID</label>
                                        <input type="text" name="applicationID" id="applicationID" value="B900" disabled readonly class="form-control">
                                    </div>	
                                </div>
                                <div class="clearfix"></div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="ipAddress">IP Address</label>
                                        <input type="text" name="ipAddress" id="ipAddress" value="" class="form-control">
                                    </div>	
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="port">Port Number</label>
                                        <input type="text" name="port" id="port" value="" class="form-control">
                                    </div>	
                                </div>

                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-success" onclick="saveFormData()">Save</button>
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                        </div>
                    </form>
                </div>	
            </div>
        </div>	
    </div>	



    <script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/admin/admin_pos_machine.js"></script>
    <script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/library/js/pos/jquery.base64.js"></script>
    <script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/pos/pos.js"></script>
    <script>
        
        set_header_title('Manage POS Devices');
        
        var ar = [["add_new","Add New","top.fmain.addNew();"],["list_merchant","List Merchant","top.fmain.showMerchantList();"],["pos_del","Delete","top.fmain.deleteSelected();"]];
        top.btn_show("ADMN",ar);
        
        $(document).ready(function(){
            LoadResultSet();
        });
    </script>
    <?php include_once('../admin_footer.php'); ?>