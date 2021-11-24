<?php
include_once("../../library/dss_api/dss_enc_visit_notes.php");

$dssObj=new Dss_enc_visit_notes();

// $servicelist=$dssObj->ConsultGetServiceSpecialtyList();

// $service_options='';
// foreach($servicelist as $key=>$service) {
//     $service['svcIen'];
//     $orderableItem=isset($service['orderableItem'])?$service['orderableItem']:'';
//     $service_options.='<option value="'.$service['svcIen'].'" data-dss_orderId="'.$orderableItem.'">'.$service['svcName'].'</option>';
// }

$dialogArr=$dssObj->ConsultGetDialogSelections();

$dialog_options='';
$code=array();
$currentLoopKey = "";
$testArr = array();
foreach($dialogArr as $key=>$dialog) {
    if(isset($dialog['code']) && (strpos($dialog['code'],'~')!== false) && count($dialog) == 1 ){
        $dialog_code=str_replace(' ','_',$dialog['code']);
        $currentLoopKey = str_replace('~','',$dialog_code);
        $code[]=$currentLoopKey;
    }else{
        if(empty($currentLoopKey) == false){
            if(!$testArr[$currentLoopKey]) $testArr[$currentLoopKey] = array();
            if(count($dialog) > 1) $testArr[$currentLoopKey][] = $dialog;
        }
    }
}

$Outpt_Place_options='';
foreach($testArr['Outpt_Place'] as $key=>$dialogs) {
    $Outpt_Place_options.='<option value="'.$dialogs['gmrcCode'].'" data-code="'.$dialogs['code'].'">'.$dialogs['name'].'</option>';
}

// pre($_REQUEST);

// Get Service Speciality details as per the test.
$currentTestId = $_REQUEST['test_master_id'];
$ser_query = "SELECT id, service_ien, service_name, service_orderable_item FROM dss_test_services WHERE status = 0 AND test_id = '$currentTestId'";
$sqlResult = imw_query($ser_query);
$row = imw_fetch_assoc($sqlResult);

$service_name = $row['service_name'];
$service_ien = $row['service_ien'];
$service_orderable_item = $row['service_orderable_item'];

$tData = '';
$readonly = '';
if(isset($_REQUEST['tId']) && ($_REQUEST['tId'] != "" || $_REQUEST['tId'] !=0)) {
    $readonly = 'readonly=true';
    $sql = imw_query('SELECT `dss_orderable_item`, `dss_service`, `dss_service_ien`, `dss_placeOfConsult`, `dss_reasonForRequest`, `dss_dxCode`, `dss_dxText`, `dss_orderNumber`, `dss_group`, `dss_orderTime`, `dss_status` FROM test_custom_patient WHERE test_id = '.$_REQUEST['tId']);
    if(imw_num_rows($sql) > 0) {
        $tData = imw_fetch_assoc($sql);

        $service_name = $tData['dss_service'];
        $service_ien = $tData['dss_service_ien'];
        $service_orderable_item = $tData['dss_orderable_item'];
    }
}
?>

<div class="dssfields pd10">
    <div class="row">
        <div class="col-sm-3">
            <label>Service/Specialty</label>

            <input type="text" name="dss_service" id="dss_service" class="form-control minimal" value="<?php echo $service_name; ?>" readonly="true">
            <input type="hidden" name="dss_service_ien" id="dss_service_ien" value="<?php echo $service_ien; ?>">
            <input type="hidden" name="dss_service_orderable_item" id="dss_service_orderable_item" value="<?php echo $service_orderable_item; ?>">

<!--             <select name="dss_service" id="dss_service" class="form-control minimal"><?php echo $service_options;?></select> -->
        </div>
        <div class="col-sm-3">
            <label>Place Of Consult</label>
            <select name="dss_placeOfConsult" id="dss_placeOfConsult" class="form-control minimal" <?php echo $readonly; ?>>
                <?php echo $Outpt_Place_options;?>
            </select>
            <script>
                top.fmain.$('#dss_placeOfConsult').val('<?php echo $tData['dss_placeOfConsult']; ?>');
            </script>

        </div>

        <div class="col-sm-6">
            <label>Reason For Request</label>
            <textarea class="form-control" rows="2" cols="20" id="dss_reasonForRequest" name="dss_reasonForRequest" autocomplete="off" <?php echo $readonly; ?>><?php echo $tData['dss_reasonForRequest']; ?></textarea>
        </div>

        <div class="col-sm-3">
            <label>Diagnosis Code</label>
            <input type="text" class="form-control" id="elem_dxCode_dss" name="elem_dxCode_dss" value="<?php echo $tData['dss_dxCode']; ?>" <?php echo $readonly; ?>>
        </div>
        <div class="col-sm-3">
            <label>Diagnosis Name</label>
            <input type="text" class="form-control" id="elem_dxText_dss" name="elem_dxText_dss" value="<?php echo $tData['dss_dxText']; ?>" <?php echo $readonly; ?>>
        </div>

    </div>
</div>
<div class="clearfix"></div>
