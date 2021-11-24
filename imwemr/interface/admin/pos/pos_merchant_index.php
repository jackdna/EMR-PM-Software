<?php
include_once('../admin_header.php');

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

                            <th width="15%">Merchant Name</th>
                            <th width="15%">MID</th>
                            <th width="10%">User ID</th>
<!--                            <th width="15%">Merchant Password</th>-->
                            <th width="15%">Company</th>
<!--                            <th width="30%">API URL</th>-->
                        </tr>
                    </thead>
                    <tbody id="tsys_merch_result_set"></tbody>
                </table>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>


    <div id="merchantModal" class="modal" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form action="" method="post" name="merchant_add_edit_frm" id="merchant_add_edit_frm">
                    <input type="hidden" name="mrchnt_id" id="mrchnt_id" value="" class="form-control">
                    <div class="modal-header bg-primary">
                        <button type="button" class="close" data-dismiss="modal">x</button>
                        <h4 class="modal-title" id="modal_title">POS Merchant</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="merchantName">Merchant Name</label>
                                    <input type="text" name="merchantName" id="merchantName" value="" class="form-control">
                                </div>	
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="mid">MID</label>
                                    <input type="text" name="mid" id="mid" value="" class="form-control">
                                </div>	
                            </div>
                            <div class="clearfix"></div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="userID">User ID</label>
                                    <input type="text" name="userID" id="userID" value="" class="form-control">
                                </div>	
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="mid_paswrd">Merchant Password</label>
                                    <input type="password" name="mid_paswrd" id="mid_paswrd" value="" class="form-control">
                                </div>	
                            </div>
                            <div class="clearfix"></div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="Company">Company</label>
                                    <input type="text" name="Company" id="Company" value="" class="form-control">
                                </div>	
                            </div>
<!--                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="api_url">API URL</label>
                                    <input type="text" name="api_url" id="api_url" value="" class="form-control">
                                </div>	
                            </div>-->

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-success" onclick="saveMerchantData();">Save</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>	
        </div>
    </div>

    <script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/admin/admin_pos_machine.js"></script>
    <script>
        
        set_header_title('Manage POS Merchant');
        
        var ar = [["add_merchant","Add Merchant","top.fmain.addNewMerchant();"],["list_devices","List Devices","top.fmain.showDevicesList();"],["pos_del","Delete","top.fmain.deleteSelectedMerchant();"]];
        top.btn_show("ADMN",ar);
        
        $(document).ready(function(){
            LoadMerchantResultSet();
        });
    </script>
    <?php include_once('../admin_footer.php'); ?>