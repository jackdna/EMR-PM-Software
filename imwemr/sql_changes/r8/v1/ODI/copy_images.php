<?php
set_time_limit(0);
$ignoreAuth = true;

if( $argv[1] )
{
	$practicePath = trim($argv[1]);
	if( isset($argv[2]) && $argv[2] != '')
	{
		$_GET['fun'] = $argv[2];
	}
	$_SERVER['REQUEST_URI'] = $practicePath;
}

require_once(dirname(__FILE__)."/../../../../config/globals.php");

$https = true;
if ($phpHTTPProtocol == "http://") {
    $https = false;
}

//R6 Directory Name
if (!$GLOBALS['r7_directory_name'] || !$GLOBALS['r7_db_directory_name']) {
    echo 'Please define R7 directory name and R7 db directory name in config file.';
    exit();
}

$two_folder = false;
$p_path = explode('/', constant('PRACTICE_PATH'));
if(count($p_path)>1) {
    $two_folder = true;
}
if ($_SERVER['REQUEST_URI'] == $GLOBALS['webroot'] . '/sql_changes/r8/v1/ODI/copy_images.php') {
    header("location:" . $GLOBALS['webroot'] . "/sql_changes/r8/v1/ODI/select_section.php");
}
$main_dir = '/' . $GLOBALS['r7_directory_name'];
$main_dir_db = '/' . $GLOBALS['r7_db_directory_name'];
$r7main_dir = $GLOBALS['r7_directory_name'];
$r7main_dir_db = $GLOBALS['r7_db_directory_name'];

$upload_path = $GLOBALS['fileroot'] . "/data/" . constant('PRACTICE_PATH') . "/";
$upload_url = $phpHTTPProtocol . $_SERVER['SERVER_NAME'] . $GLOBALS['webroot'] . "/data/" . constant('PRACTICE_PATH') . "/";

//R6 URL and Path
$src_url = $phpHTTPProtocol . $_SERVER['SERVER_NAME'] . $main_dir . "/";
$src_url_db = $phpHTTPProtocol . $_SERVER['SERVER_NAME'] . $main_dir_db . "/";
$src_path = $GLOBALS['fileroot'] . "/.." . $main_dir . "/";
$src_path_db = $GLOBALS['fileroot'] . "/.." . $main_dir_db . "/";

function fetchImages($parms = array()) {
    $images = array();

    $table_name = $parms['table_name'];
    $fields = $parms['fields'];
    $where = '';
    if ($parms['where'] != '') {
        $where = ' WHERE ' . $parms['where'];
    }

    $qry = "SELECT $fields FROM $table_name  $where ";
    if (isset($parms['db_name']) && $parms['db_name'] != '') {
        $db_name = $parms['db_name'];
        $qry = "SELECT $fields FROM $db_name.$table_name $where ";
    }

    $result_rs = imw_query($qry) or $msg_info[] = imw_error();
    return $result_rs;
    while ($row = imw_fetch_assoc($result_rs)) {
        $images[] = $row;
    }
    return $images;
}

$counter = 0;
if (isset($_GET['fun']) && $_GET['fun'] != '') {
    switch ($_GET['fun']) {

        case 'previous_statement':
            $parms = array();
            $parms['table_name'] = 'previous_statement_detail';
            $parms['fields'] = 'id, previous_statement_id, statement_data, statement_txt_data';
            $parms['where'] = "statement_data !=' ' OR statement_txt_data !=' ' ";

            $previous_statement = fetchImages($parms);
            while ($row = imw_fetch_assoc($previous_statement)) {
//print_r($row);


                if ($row['statement_data']) {
                    $doc = new DOMDocument();
                    $doc->loadHTML((html_entity_decode($row['statement_data'])));
                    $imgs = $doc->getElementsByTagName('img');
                    if ($imgs->length != 0) {
                        $updated = false;
                        $fcount=0;
                        $afcount=0;
                        $vfcount=0;
                        $mfcount=0;
                        foreach ($imgs as $img) {
                            //var_dump($img);continue;
                            $new_url = '';
                            $old_src = ($img->getAttribute('src'));

                            $filename = $old_src;
                            $old_filepath = $GLOBALS['fileroot'] . "/.." . $main_dir . "/interface/common/new_html2pdf";
                            $old_filesrc = $main_dir . "/interface/common/new_html2pdf" . $filename;
                            $old_filepath_db = $GLOBALS['fileroot'] . "/.." . $main_dir_db . "/interface/common/new_html2pdf";
                            $old_filesrc_db = $main_dir_db . "/interface/common/new_html2pdf" . $filename;

                            $new_filepath = $GLOBALS['fileroot'] . "/data/" . constant('PRACTICE_PATH') . "/gn_images";
                            $new_url = $GLOBALS['webroot'] . "/data/" . constant('PRACTICE_PATH') . "/gn_images/" . $filename;
                            if ($filename == 'visa.jpg') {
                                $vfcount++;
                                if($vfcount>1) {
                                    continue;
                                }
                                $new_filepath = $GLOBALS['fileroot'] . "/library/images";
                                $new_url = $GLOBALS['webroot'] . "/library/images/" . $filename;
                            }
                            if ($filename == 'master.jpg') {
                                $mfcount++;
                                if($mfcount>1) {
                                    continue;
                                }
                                $new_filepath = $GLOBALS['fileroot'] . "/library/images";
                                $new_url = $GLOBALS['webroot'] . "/library/images/" . $filename;
                            }
                            if ($filename == 'checkbox.jpg') {
                                $fcount++;
                                if($fcount>1) {
                                    continue;
                                }
                                $new_filepath = $GLOBALS['fileroot'] . "/data/" . constant('PRACTICE_PATH') . "/gn_images";
                                $new_url = $GLOBALS['webroot'] . "/data/" . constant('PRACTICE_PATH') . "/gn_images/" . $filename;
                            }
                            if ($filename == 'amr.jpg') {
                                $afcount++;
                                if($afcount>1) {
                                    continue;
                                }
                                $new_filepath = $GLOBALS['fileroot'] . "/data/" . constant('PRACTICE_PATH') . "/gn_images";
                                $new_url = $GLOBALS['webroot'] . "/data/" . constant('PRACTICE_PATH') . "/gn_images/" . $filename;
                            }
                            
                            if ($https) {
                                $new_url = str_replace(array('https://', 'http://'), 'https://', $new_url);
                            } else {
                                $new_url = str_replace(array('https://', 'http://'), 'http://', $new_url);
                            }

                            if (file_exists($old_filepath . '/' . $filename)) {
                                if (!is_dir($new_filepath)) {
                                    mkdir($new_filepath, 0777, true);
                                }
                                if (!file_exists($new_filepath . '/' . $filename)) {
                                    $content = file_get_contents($old_filepath . '/' . $filename);
                                    file_put_contents($new_filepath . '/' . $filename, $content);
                                }
                            }

                            if (strtolower($old_filepath_db) != strtolower($new_filepath) && $new_url !='') {
                                $row['statement_data'] = str_replace(array($old_filesrc, $old_src, $old_filesrc_db), $new_url, $row['statement_data']);
                                $updated = true;
                            }
                        }

                        if ($updated) {
                            $sql = "UPDATE previous_statement_detail SET statement_data='" . (addslashes($row['statement_data'])) . "' WHERE id={$row['id']}";
                            imw_query($sql) or $msg_info[] = imw_error();

                            $counter++;
                        }
                    }
                }

                if ($row['statement_txt_data']) {
                    $doc1 = $doc = new DOMDocument();
                    $doc1->loadHTML(stripslashes(html_entity_decode($row['statement_txt_data'])));
                    $imgs1 = $doc1->getElementsByTagName('img');

                    if ($imgs1->length != 0) {
                        $updated1 = false;
                        $fcount1=0;
                        $afcount1=0;
                        $vfcount1=0;
                        $mfcount1=0;
                        foreach ($imgs1 as $img1) {
                            $new_url1 = '';
                            $old_src1 = ($img1->getAttribute('src'));

                            $filename1 = $old_src1;
                            $old_filepath1 = $GLOBALS['fileroot'] . "/.." . $main_dir . "/interface/common/new_html2pdf";
                            $old_filesrc1 = $main_dir . "/interface/common/new_html2pdf" . $filename1;
                            $old_filepath1_db = $GLOBALS['fileroot'] . "/.." . $main_dir_db . "/interface/common/new_html2pdf";
                            $old_filesrc1_db = $main_dir_db . "/interface/common/new_html2pdf" . $filename1;

                            $new_filepath1 = $GLOBALS['fileroot'] . "/data/" . constant('PRACTICE_PATH') . "/gn_images";
                            $new_url1 = $GLOBALS['webroot'] . "/data/" . constant('PRACTICE_PATH') . "/gn_images/" . $filename1;
                            if ($filename1 == 'visa.jpg') {
                                $vfcount1++;
                                if($vfcount1>1) {
                                    continue;
                                }
                                $new_filepath1 = $GLOBALS['fileroot'] . "/library/images";
                                $new_url1 = $GLOBALS['webroot'] . "/library/images/" . $filename1;
                            }
                            if ($filename1 == 'master.jpg') {
                                $mfcount1++;
                                if($mfcount1>1) {
                                    continue;
                                }
                                $new_filepath1 = $GLOBALS['fileroot'] . "/library/images";
                                $new_url1 = $GLOBALS['webroot'] . "/library/images/" . $filename1;
                            }
                            if ($filename1 == 'checkbox.jpg') {
                                $fcount1++;
                                if($fcount1>1) {
                                    continue;
                                }
                                $new_filepath1 = $GLOBALS['fileroot'] . "/data/" . constant('PRACTICE_PATH') . "/gn_images";
                                $new_url1 = $GLOBALS['webroot'] . "/data/" . constant('PRACTICE_PATH') . "/gn_images/" . $filename1;
                            }
                            if ($filename1 == 'amr.jpg') {
                                $afcount1++;
                                if($afcount1>1) {
                                    continue;
                                }
                                $new_filepath1 = $GLOBALS['fileroot'] . "/data/" . constant('PRACTICE_PATH') . "/gn_images";
                                $new_url1 = $GLOBALS['webroot'] . "/data/" . constant('PRACTICE_PATH') . "/gn_images/" . $filename1;
                            }

                            if ($https) {
                                $new_url1 = str_replace(array('https://', 'http://'), 'https://', $new_url1);
                            } else {
                                $new_url1 = str_replace(array('https://', 'http://'), 'http://', $new_url1);
                            }

                            if (file_exists($old_filepath1 . '/' . $filename1)) {
                                if (!is_dir($new_filepath1)) {
                                    mkdir($new_filepath1, 0777, true);
                                }
                                if (!file_exists($new_filepath1 . '/' . $filename1)) {
                                    $content1 = file_get_contents($old_filepath1 . '/' . $filename1);
                                    file_put_contents($new_filepath1 . '/' . $filename1, $content1);
                                }
                            }

                            if (strtolower($old_filepath1_db) != strtolower($new_filepath1) && $new_url1 != '') {
                                $row['statement_txt_data'] = str_replace(array($old_filesrc1, $old_src1, $old_filesrc1_db), $new_url1, $row['statement_txt_data']);
                                $updated1 = true;
                            }
                        }

                        if ($updated1) {
                            $sql1 = "UPDATE previous_statement_detail SET statement_txt_data='" . (addslashes($row['statement_txt_data'])) . "' WHERE id={$row['id']}";
                            imw_query($sql1) or $msg_info[] = imw_error();

                            $counter++;
                        }
                    }
                }
                continue;
            }

            break;
        //Billing   Batch processing   upload_lab_rad_data
        case 'upload_lab_rad_data':
            return;
            $parms = array();
            $parms['table_name'] = 'upload_lab_rad_data';
            //$parms['db_name'] = 'imw_dev_scan';
            $parms['fields'] = 'upload_lab_rad_data_id,patient_id,upload_file_name';
            $parms['where'] = " upload_file_name != '' ";

            $upload_lab_rad_data = fetchImages($parms);

            //pre($upload_lab_rad_data); die();
            if (empty($upload_lab_rad_data)) {
                echo '<font face="Arial, Helvetica, sans-serif" size="2" color="red"> No image found in "' . $parms['table_name'] . '" table.</font> <br />';
                return false;
            }
            while ($row = imw_fetch_assoc($upload_lab_rad_data)) {

                //foreach ($upload_lab_rad_data as $row) {
                $pathinfo = pathinfo($row['upload_file_name']);
                $dirname = $pathinfo['dirname'];
                $filename = $pathinfo['basename'];
                //$pathinfo = array_pop($pathinfo);
                $pathinfo1 = pathinfo($dirname);

                $dirname1 = $pathinfo1['basename'];

                $old_src = $src_url . "interface/main/uploaddir/../" . $dirname . "/" . $filename;
                $old_filepath = $src_path . "interface/main/uploaddir/../" . $dirname;
                $old_src_db = $src_url_db . "interface/main/uploaddir/../" . $dirname . "/" . $filename;
                $old_filepath_db = $src_path_db . "interface/main/uploaddir/../" . $dirname;

                if ($https) {
                    $old_url = str_replace(array('https://', 'http://'), 'https://', $old_src);
                    $old_url_db = str_replace(array('https://', 'http://'), 'https://', $old_src_db);
                } else {
                    $old_url = str_replace(array('https://', 'http://'), 'http://', $old_src);
                    $old_url_db = str_replace(array('https://', 'http://'), 'http://', $old_src_db);
                }
                $new_filepath = $GLOBALS['fileroot'] . "/data/" . constant('PRACTICE_PATH') . "/" . $dirname1;

                if (file_exists($old_filepath . "/" . $filename)) {
//                    if (!is_dir($new_filepath)) {
//                        mkdir($new_filepath, 0777, true);
//                    }
//                    $content = file_get_contents($old_filepath . "/" . $filename);
//                    file_put_contents($new_filepath . "/" . $filename, $content);

                    $counter++;

                    //Update file path for R8 in upload_lab_rad_data table
                    $qry = "UPDATE upload_lab_rad_data SET upload_file_name='/" . $dirname1 . "/" . $filename . "' WHERE upload_lab_rad_data_id={$row['upload_lab_rad_data_id']} ";
                    imw_query($qry);

                    //continue;
                }
            }
            break;
            
        /*    
        case 'chart_signatures':
            $parms = array();
            $parms['table_name'] = 'chart_signatures';
            //$parms['db_name'] = constant('IMEDIC_SCAN_DB');
            $parms['fields'] = 'id,sign_path';
            $parms['where'] = " sign_path != '' ";

            $chart_signatures = fetchImages($parms);
            if (empty($chart_signatures)) {
                echo '<font face="Arial, Helvetica, sans-serif" size="2" color="red"> No image found in "' . $parms['table_name'] . '" table.</font> <br />';
                return false;
            }
            while ($row = imw_fetch_assoc($chart_signatures)) {
                $pathinfo = pathinfo(trim($row['sign_path']));
                $dirname = $pathinfo['dirname'];
                $filename = $pathinfo['basename'];

                $old_src = $src_url . "interface/main/uploaddir" . $dirname . "/" . $filename;
                $old_filepath = $src_path . "interface/main/uploaddir" . $dirname;
                $old_src_db = $src_url_db . "interface/main/uploaddir" . $dirname . "/" . $filename;
                $old_filepath_db = $src_path_db . "interface/main/uploaddir" . $dirname;

                if ($https) {
                    $old_url = str_replace(array('https://', 'http://'), 'https://', $old_src);
                    $old_url_db = str_replace(array('https://', 'http://'), 'https://', $old_src_db);
                } else {
                    $old_url = str_replace(array('https://', 'http://'), 'http://', $old_src);
                    $old_url_db = str_replace(array('https://', 'http://'), 'http://', $old_src_db);
                }
                $new_filepath = $GLOBALS['fileroot'] . "/data/" . constant('PRACTICE_PATH') . $dirname;

                if (file_exists($old_filepath . "/" . $filename)) {
                    if (!is_dir($new_filepath)) {
                        mkdir($new_filepath, 0777, true);
                    }
                    $content = file_get_contents($old_filepath . "/" . $filename);
                    file_put_contents($new_filepath . "/" . $filename, $content);
                    $counter++;
                }
            }
            break;
        */
        
       /*     //Work View   idoc_drawing
        case 'idoc_drawing':
            $parms = array();
            $parms['table_name'] = 'idoc_drawing';
            $parms['db_name'] = constant('IMEDIC_SCAN_DB');
            $parms['fields'] = 'id,patient_id,drawing_image_path';
            $parms['where'] = " drawing_image_path != '' ";

            $idoc_drawing = fetchImages($parms);
            if (empty($idoc_drawing)) {
                echo '<font face="Arial, Helvetica, sans-serif" size="2" color="red"> No image found in "' . $parms['table_name'] . '" table.</font> <br />';
                return false;
            }
            while ($row = imw_fetch_assoc($idoc_drawing)) {
                $pathinfo = pathinfo($row['drawing_image_path']);
                $dirname = $pathinfo['dirname'];
                $filename = $pathinfo['basename'];

                $old_src = $src_url . "interface/main/uploaddir" . $dirname . "/" . $filename;
                $old_filepath = $src_path . "interface/main/uploaddir" . $dirname;
                $old_src_db = $src_url_db . "interface/main/uploaddir" . $dirname . "/" . $filename;
                $old_filepath_db = $src_path_db . "interface/main/uploaddir" . $dirname;

                if ($https) {
                    $old_url = str_replace(array('https://', 'http://'), 'https://', $old_src);
                    $old_url_db = str_replace(array('https://', 'http://'), 'https://', $old_src_db);
                } else {
                    $old_url = str_replace(array('https://', 'http://'), 'http://', $old_src);
                    $old_url_db = str_replace(array('https://', 'http://'), 'http://', $old_src_db);
                }
                $new_filepath = $GLOBALS['fileroot'] . "/data/" . constant('PRACTICE_PATH') . $dirname;

                if (file_exists($old_filepath . "/" . $filename)) {
                    if (!is_dir($new_filepath)) {
                        mkdir($new_filepath, 0777, true);
                    }
                    $content = file_get_contents($old_filepath . "/" . $filename);
                    file_put_contents($new_filepath . "/" . $filename, $content);
                    $counter++;

                    //with background
                    $drawing_image_path_b = str_replace(".png","_b.png",$filename);	
                    if(file_exists($old_filepath . "/" . $drawing_image_path_b)){
                        @copy($old_filepath . "/" . $drawing_image_path_b, $new_filepath . "/" . $drawing_image_path_b);
                        $counter++;
                    }

                    //thumb					
                    $drawing_image_path_s = str_replace(".png","_s.png",$filename);	
                    if(file_exists($old_filepath . "/" . $drawing_image_path_s)){
                        @copy($old_filepath . "/" . $drawing_image_path_s, $new_filepath . "/" . $drawing_image_path_s);
                        $counter++;
                    }
                }
            }
            break;
        */ 

       /* //Work View   scan_doc_tbl
        case 'scan_doc_tbl':
            $parms = array();
            $parms['table_name'] = 'scan_doc_tbl';
            $parms['db_name'] = constant('IMEDIC_SCAN_DB');
            $parms['fields'] = 'scan_doc_id,patient_id,file_path';
            $parms['where'] = " file_path != '' ";

            $scan_doc_tbl = fetchImages($parms);
            if (empty($scan_doc_tbl)) {
                echo '<font face="Arial, Helvetica, sans-serif" size="2" color="red"> No image found in "' . $parms['table_name'] . '" table.</font> <br />';
                return false;
            }
            while ($row = imw_fetch_assoc($scan_doc_tbl)) {
                //foreach ($scan_doc_tbl as $row) {
                $pathinfo = pathinfo($row['file_path']);
                $dirname = $pathinfo['dirname'];
                $filename = $pathinfo['basename'];

                $old_src = $src_url . "interface/main/uploaddir" . $dirname . "/" . $filename;
                $old_filepath = $src_path . "interface/main/uploaddir" . $dirname;
                $old_src_db = $src_url_db . "interface/main/uploaddir" . $dirname . "/" . $filename;
                $old_filepath_db = $src_path_db . "interface/main/uploaddir" . $dirname;

                if ($https) {
                    $old_url = str_replace(array('https://', 'http://'), 'https://', $old_src);
                    $old_url_db = str_replace(array('https://', 'http://'), 'https://', $old_src_db);
                } else {
                    $old_url = str_replace(array('https://', 'http://'), 'http://', $old_src);
                    $old_url_db = str_replace(array('https://', 'http://'), 'http://', $old_src_db);
                }
                $new_filepath = $GLOBALS['fileroot'] . "/data/" . constant('PRACTICE_PATH') . $dirname;

                if (file_exists($old_filepath . "/" . $filename)) {
                    if (!is_dir($new_filepath)) {
                        mkdir($new_filepath, 0777, true);
                    }
                    $content = file_get_contents($old_filepath . "/" . $filename);
                    file_put_contents($new_filepath . "/" . $filename, $content);

                    $counter++;
                }
            }
            break;
`       */
        
        /* //Work View   AR Image
        case 'ar_scan':
            $parms = array();
            $parms['table_name'] = 'chart_ar_scan';
            //$parms['db_name'] = 'imw_dev_scan';
            $parms['fields'] = 'id,patient_id,path';
            $parms['where'] = " path != '' ";

            $ar_scan = fetchImages($parms);
            if (empty($ar_scan)) {
                echo '<font face="Arial, Helvetica, sans-serif" size="2" color="red"> No image found in "' . $parms['table_name'] . '" table.</font> <br />';
                return false;
            }
            while ($row = imw_fetch_assoc($ar_scan)) {
                //foreach ($ar_scan as $row) {
                $pathinfo = pathinfo($row['path']);
                $dirname = $pathinfo['dirname'];
                $filename = $pathinfo['basename'];

                $old_src = $src_url . "interface/main/uploaddir" . $dirname . "/" . $filename;
                $old_filepath = $src_path . "interface/main/uploaddir" . $dirname;
                $old_src_db = $src_url_db . "interface/main/uploaddir" . $dirname . "/" . $filename;
                $old_filepath_db = $src_path_db . "interface/main/uploaddir" . $dirname;

                if ($https) {
                    $old_url = str_replace(array('https://', 'http://'), 'https://', $old_src);
                    $old_url_db = str_replace(array('https://', 'http://'), 'https://', $old_src_db);
                } else {
                    $old_url = str_replace(array('https://', 'http://'), 'http://', $old_src);
                    $old_url_db = str_replace(array('https://', 'http://'), 'http://', $old_src_db);
                }
                $new_filepath = $GLOBALS['fileroot'] . "/data/" . constant('PRACTICE_PATH') . $dirname;

                if (file_exists($old_filepath . "/" . $filename)) {
                    if (!is_dir($new_filepath)) {
                        mkdir($new_filepath, 0777, true);
                    }
                    $content = file_get_contents($old_filepath . "/" . $filename);
                    file_put_contents($new_filepath . "/" . $filename, $content);

                    $counter++;
                }
            }
            break;
        */

        /* //Tests scans (All Tests, EOM Exam, External Exam, L&A, IOP/Gonio, SLE, Fundus, iol_master)
        case 'tests':
            $parms = array();
            $parms['table_name'] = 'scans';
            $parms['db_name'] = constant('IMEDIC_SCAN_DB');
            $parms['fields'] = 'scan_id,patient_id,image_name,file_path';
            $parms['where'] = " image_name != '' OR file_path != '' ";

            $tests = fetchImages($parms);
            if (empty($tests)) {
                echo '<font face="Arial, Helvetica, sans-serif" size="2" color="red"> No image found in "' . $parms['table_name'] . '" table.</font> <br />';
                return false;
            }
            while ($row = imw_fetch_assoc($tests)) {
                //foreach ($tests as $row) {
                $pathinfo = pathinfo($row['file_path']);
                $dirname = $pathinfo['dirname'];
                $filename = $pathinfo['basename'];

                $old_src = $src_url . "interface/main/uploaddir" . $dirname . "/" . $filename;
                $old_filepath = $src_path . "interface/main/uploaddir" . $dirname;
                $old_src_db = $src_url_db . "interface/main/uploaddir" . $dirname . "/" . $filename;
                $old_filepath_db = $src_path_db . "interface/main/uploaddir" . $dirname;

                if ($https) {
                    $old_url = str_replace(array('https://', 'http://'), 'https://', $old_src);
                    $old_url_db = str_replace(array('https://', 'http://'), 'https://', $old_src_db);
                } else {
                    $old_url = str_replace(array('https://', 'http://'), 'http://', $old_src);
                    $old_url_db = str_replace(array('https://', 'http://'), 'http://', $old_src_db);
                }
                $new_filepath = $GLOBALS['fileroot'] . "/data/" . constant('PRACTICE_PATH') . $dirname;

                if (file_exists($old_filepath . "/" . $filename)) {
                    if (!is_dir($new_filepath)) {
                        mkdir($new_filepath, 0777, true);
                    }
                    $content = file_get_contents($old_filepath . "/" . $filename);
                    file_put_contents($new_filepath . "/" . $filename, $content);

                    $counter++;
                }
            }
            break;
        */

        /* //Medical HX scans
        case 'mh_scans':
            $parms = array();
            $parms['table_name'] = 'scans';
            $parms['db_name'] = constant('IMEDIC_SCAN_DB');
            $parms['fields'] = 'scan_id,patient_id,image_name,file_path';
            $parms['where'] = " image_name != '' OR file_path != '' ";

            $mh_scans = fetchImages($parms);
            if (empty($mh_scans)) {
                echo '<font face="Arial, Helvetica, sans-serif" size="2" color="red"> No image found in "' . $parms['table_name'] . '" table.</font> <br />';
                return false;
            }
            while ($row = imw_fetch_assoc($mh_scans)) {
                //foreach ($mh_scans as $row) {
                $pathinfo = pathinfo($row['file_path']);
                $dirname = $pathinfo['dirname'];
                $filename = $pathinfo['basename'];

                $old_src = $src_url . "interface/main/uploaddir" . $dirname . "/" . $filename;
                $old_filepath = $src_path . "interface/main/uploaddir" . $dirname;
                $old_src_db = $src_url_db . "interface/main/uploaddir" . $dirname . "/" . $filename;
                $old_filepath_db = $src_path_db . "interface/main/uploaddir" . $dirname;

                if ($https) {
                    $old_url = str_replace(array('https://', 'http://'), 'https://', $old_src);
                    $old_url_db = str_replace(array('https://', 'http://'), 'https://', $old_src_db);
                } else {
                    $old_url = str_replace(array('https://', 'http://'), 'http://', $old_src);
                    $old_url_db = str_replace(array('https://', 'http://'), 'http://', $old_src_db);
                }
                $new_filepath = $GLOBALS['fileroot'] . "/data/" . constant('PRACTICE_PATH') . $dirname;

                if (file_exists($old_filepath . "/" . $filename)) {
                    if (!is_dir($new_filepath)) {
                        mkdir($new_filepath, 0777, true);
                    }
                    $content = file_get_contents($old_filepath . "/" . $filename);
                    file_put_contents($new_filepath . "/" . $filename, $content);

                    $counter++;
                }
            }
            break;
        */
            
        /* //surgery_center_patient_scan_docs
        case 'sc_scan_docs':
            $parms = array();
            $parms['table_name'] = 'surgery_center_patient_scan_docs';
            $parms['fields'] = 'id,patient_id,scan_doc_add';
            $parms['where'] = " scan_doc_add != '' ";

            $sc_scan_docs = fetchImages($parms);
            if (empty($sc_scan_docs)) {
                echo '<font face="Arial, Helvetica, sans-serif" size="2" color="red"> No image found in "' . $parms['table_name'] . '" table.</font> <br />';
                return false;
            }
            while ($row = imw_fetch_assoc($sc_scan_docs)) {
                //foreach ($sc_scan_docs as $row) {
                $pathinfo = pathinfo($row['scan_doc_add']);
                $dirname = $pathinfo['dirname'];
                $filename = $pathinfo['basename'];

                $old_src = $src_url . "interface/main/uploaddir" . $dirname . "/" . $filename;
                $old_filepath = $src_path . "interface/main/uploaddir" . $dirname;
                $old_src_db = $src_url_db . "interface/main/uploaddir" . $dirname . "/" . $filename;
                $old_filepath_db = $src_path_db . "interface/main/uploaddir" . $dirname;

                if ($https) {
                    $old_url = str_replace(array('https://', 'http://'), 'https://', $old_src);
                    $old_url_db = str_replace(array('https://', 'http://'), 'https://', $old_src_db);
                } else {
                    $old_url = str_replace(array('https://', 'http://'), 'http://', $old_src);
                    $old_url_db = str_replace(array('https://', 'http://'), 'http://', $old_src_db);
                }
                $new_filepath = $GLOBALS['fileroot'] . "/data/" . constant('PRACTICE_PATH') . $dirname;

                if (file_exists($old_filepath . "/" . $filename)) {
                    if (!is_dir($new_filepath)) {
                        mkdir($new_filepath, 0777, true);
                    }
                    $content = file_get_contents($old_filepath . "/" . $filename);
                    file_put_contents($new_filepath . "/" . $filename, $content);

                    $counter++;
                }
            }
            break;
        */
            
        /* //insurance_scan_documents
        case 'ins_scan_docs':
            $parms = array();
            $parms['table_name'] = 'insurance_scan_documents';
            $parms['fields'] = 'scan_documents_id,patient_id,scan_card,scan_card2';
            $parms['where'] = " scan_card != '' OR scan_card2 != '' ";

            $ins_scan_docs = fetchImages($parms);
            if (empty($ins_scan_docs)) {
                echo '<font face="Arial, Helvetica, sans-serif" size="2" color="red"> No image found in "' . $parms['table_name'] . '" table.</font> <br />';
                return false;
            }
            while ($row = imw_fetch_assoc($ins_scan_docs)) {
                //foreach ($ins_scan_docs as $row) {
                if ($row['scan_card'] != '') {
                    $pathinfo1 = pathinfo($row['scan_card']);
                    $dirname1 = $pathinfo1['dirname'];
                    $filename1 = $pathinfo1['basename'];

                    $old_src1 = $src_url . "interface/main/uploaddir" . $dirname1 . "/" . $filename1;
                    $old_filepath1 = $src_path . "interface/main/uploaddir" . $dirname1;
                    $old_src1_db = $src_url_db . "interface/main/uploaddir" . $dirname1 . "/" . $filename1;
                    $old_filepath1_db = $src_path_db . "interface/main/uploaddir" . $dirname1;

                    if ($https) {
                        $old_url1 = str_replace(array('https://', 'http://'), 'https://', $old_src1);
                        $old_url1_db = str_replace(array('https://', 'http://'), 'https://', $old_src1_db);
                    } else {
                        $old_url1 = str_replace(array('https://', 'http://'), 'http://', $old_src1);
                        $old_url1_db = str_replace(array('https://', 'http://'), 'http://', $old_src1_db);
                    }
                    $new_filepath1 = $GLOBALS['fileroot'] . "/data/" . constant('PRACTICE_PATH') . $dirname1;

                    if (file_exists($old_filepath1 . "/" . $filename1)) {
                        if (!is_dir($new_filepath1)) {
                            mkdir($new_filepath1, 0777, true);
                        }
                        $content1 = file_get_contents($old_filepath1 . "/" . $filename1);
                        file_put_contents($new_filepath1 . "/" . $filename1, $content1);

                        $counter++;
                    }
                }

                if ($row['scan_card2'] != '') {
                    $pathinfo2 = pathinfo($row['scan_card2']);
                    $dirname2 = $pathinfo2['dirname'];
                    $filename2 = $pathinfo2['basename'];

                    $old_src2 = $src_url . "interface/main/uploaddir" . $dirname2 . "/" . $filename2;
                    $old_filepath2 = $src_path . "interface/main/uploaddir" . $dirname2;
                    $old_src2_db = $src_url_db . "interface/main/uploaddir" . $dirname2 . "/" . $filename2;
                    $old_filepath2_db = $src_path_db . "interface/main/uploaddir" . $dirname2;

                    if ($https) {
                        $old_url2 = str_replace(array('https://', 'http://'), 'https://', $old_src2);
                        $old_url2_db = str_replace(array('https://', 'http://'), 'https://', $old_src2_db);
                    } else {
                        $old_url2 = str_replace(array('https://', 'http://'), 'http://', $old_src2);
                        $old_url2_db = str_replace(array('https://', 'http://'), 'http://', $old_src2_db);
                    }
                    $new_filepath2 = $GLOBALS['fileroot'] . "/data/" . constant('PRACTICE_PATH') . $dirname2;

                    if (file_exists($old_filepath2 . "/" . $filename2)) {
                        if (!is_dir($new_filepath2)) {
                            mkdir($new_filepath2, 0777, true);
                        }
                        $content2 = file_get_contents($old_filepath2 . "/" . $filename2);
                        file_put_contents($new_filepath2 . "/" . $filename2, $content2);

                        $counter++;
                    }
                }
            }
            break;
        */
            
        //resp_party
        case 'resp_party':
            $parms = array();
            $parms['table_name'] = 'resp_party';
            $parms['fields'] = 'id,patient_id,licence_image';
            $parms['where'] = " licence_image != '' ";

            $resp_party_imgs = fetchImages($parms);
//            if (empty($resp_party_imgs)) {
//                echo '<font face="Arial, Helvetica, sans-serif" size="2" color="red"> No image found in "' . $parms['table_name'] . '" table.</font> <br />';
//                return false;
//            }
            while ($row = imw_fetch_assoc($resp_party_imgs)) {
                //foreach ($resp_party_imgs as $row) {
                $filename = $row['licence_image'];
                $old_src = $src_url . "interface/patient_access/patient_photos/" . $filename;
                $old_filepath = $src_path . "interface/patient_access/patient_photos/";
                $old_src_db = $src_url_db . "interface/patient_access/patient_photos/" . $filename;
                $old_filepath_db = $src_path_db . "interface/patient_access/patient_photos/";

                $new_filepath = $GLOBALS['fileroot'] . "/data/" . constant('PRACTICE_PATH') . "/PatientId_" . $row['patient_id'] . "/";
                if ($https) {
                    $old_url = str_replace(array('https://', 'http://'), 'https://', $old_src);
                    $old_url_db = str_replace(array('https://', 'http://'), 'https://', $old_src_db);
                } else {
                    $old_url = str_replace(array('https://', 'http://'), 'http://', $old_src);
                    $old_url_db = str_replace(array('https://', 'http://'), 'http://', $old_src_db);
                }

                if (file_exists($old_filepath . $filename)) {
                    if (!is_dir($new_filepath)) {
                        mkdir($new_filepath, 0777, true);
                    }
                    $content = file_get_contents($old_filepath . $filename);
                    file_put_contents($new_filepath . $filename, $content);

                    $counter++;

                    $query = "UPDATE resp_party SET licence_image='" . "/PatientId_" . $row['patient_id'] . "/" . $filename . "' WHERE id={$row['id']}";
                    imw_query($query) or $msg_info[] = imw_error();
                }
            }
            break;

        /* //Demographic
        case 'demographic':
            $parms = array();
            $parms['table_name'] = 'patient_data';
            $parms['fields'] = 'id,p_imagename,licence_photo';
            $parms['where'] = " p_imagename != '' OR licence_photo != '' ";

            $patient_data_imgs = fetchImages($parms);
            while ($row = imw_fetch_assoc($patient_data_imgs)) {
                //foreach ($patient_data_imgs as $row) {
                if ($row['p_imagename'] != '') {
                    $pathinfo1 = pathinfo($row['p_imagename']);
                    $dirname1 = $pathinfo1['dirname'];
                    $filename1 = $pathinfo1['basename'];

                    $old_src1 = $src_url . "interface/main/uploaddir" . $dirname1 . "/" . $filename1;
                    $old_filepath1 = $src_path . "interface/main/uploaddir" . $dirname1;
                    $old_src1_db = $src_url_db . "interface/main/uploaddir" . $dirname1 . "/" . $filename1;
                    $old_filepath1_db = $src_path_db . "interface/main/uploaddir" . $dirname1;

                    if ($https) {
                        $old_url1 = str_replace(array('https://', 'http://'), 'https://', $old_src1);
                        $old_url1_db = str_replace(array('https://', 'http://'), 'https://', $old_src1_db);
                    } else {
                        $old_url1 = str_replace(array('https://', 'http://'), 'http://', $old_src1);
                        $old_url1_db = str_replace(array('https://', 'http://'), 'http://', $old_src1_db);
                    }
                    $new_filepath1 = $GLOBALS['fileroot'] . "/data/" . constant('PRACTICE_PATH') . $dirname1;

                    if (file_exists($old_filepath1 . "/" . $filename1)) {
                        if (!is_dir($new_filepath1)) {
                            mkdir($new_filepath1, 0777, true);
                        }
                        $content1 = file_get_contents($old_filepath1 . "/" . $filename1);
                        file_put_contents($new_filepath1 . "/" . $filename1, $content1);

                        $counter++;
                    }
                }

                if ($row['licence_photo'] != '') {
                    $pathinfo2 = pathinfo($row['licence_photo']);
                    $dirname2 = $pathinfo2['dirname'];
                    $filename2 = $pathinfo2['basename'];

                    $old_src2 = $src_url . "interface/main/uploaddir" . $dirname2 . "/" . $filename2;
                    $old_filepath2 = $src_path . "interface/main/uploaddir" . $dirname2;
                    $old_src2_db = $src_url_db . "interface/main/uploaddir" . $dirname2 . "/" . $filename2;
                    $old_filepath2_db = $src_path_db . "interface/main/uploaddir" . $dirname2;

                    if ($https) {
                        $old_url2 = str_replace(array('https://', 'http://'), 'https://', $old_src2);
                        $old_url2_db = str_replace(array('https://', 'http://'), 'https://', $old_src2_db);
                    } else {
                        $old_url2 = str_replace(array('https://', 'http://'), 'http://', $old_src2);
                        $old_url2_db = str_replace(array('https://', 'http://'), 'http://', $old_src2_db);
                    }
                    $new_filepath2 = $GLOBALS['fileroot'] . "/data/" . constant('PRACTICE_PATH') . $dirname2;

                    if (file_exists($old_filepath2 . "/" . $filename2)) {
                        if (!is_dir($new_filepath2)) {
                            mkdir($new_filepath2, 0777, true);
                        }
                        $content2 = file_get_contents($old_filepath2 . "/" . $filename2);
                        file_put_contents($new_filepath2 . "/" . $filename2, $content2);

                        $counter++;
                    }
                }
            }
            break;
        */
        
        /* //iPortal => Preferred Images
        case 'preferred_images':
            $parms = array();
            $parms['table_name'] = 'iportal_preferred_images';
            $parms['fields'] = 'id,name';
            $parms['where'] = "";

            $preferred_images = fetchImages($parms);
            while ($row = imw_fetch_assoc($preferred_images)) {
                //foreach ($preferred_images as $row) {
                if ($row['name'] == '') {
                    //echo '<font face="Arial, Helvetica, sans-serif" size="2" color="red"> No image found for ' . $row['alertId'] . '</font> <br />';
                    continue;
                }

                $filename = $row['name'];
                $old_src = $src_url . "interface/admin/iportal/preferred_images/" . $filename;
                $old_filepath = $src_path . "interface/admin/iportal/preferred_images/";
                $old_src_db = $src_url_db . "interface/admin/iportal/preferred_images/" . $filename;
                $old_filepath_db = $src_path_db . "interface/admin/iportal/preferred_images/";

                $new_filepath = $GLOBALS['fileroot'] . "/data/" . constant('PRACTICE_PATH') . "/preferred_images/";
                if ($https) {
                    $old_url = str_replace(array('https://', 'http://'), 'https://', $old_src);
                    $old_url_db = str_replace(array('https://', 'http://'), 'https://', $old_src_db);
                } else {
                    $old_url = str_replace(array('https://', 'http://'), 'http://', $old_src);
                    $old_url_db = str_replace(array('https://', 'http://'), 'http://', $old_src_db);
                }

                if (file_exists($old_filepath . $filename)) {
                    if (!is_dir($new_filepath)) {
                        mkdir($new_filepath, 0777, true);
                    }
                    $content = file_get_contents($old_filepath . $filename);
                    file_put_contents($new_filepath . $filename, $content);

                    $counter++;
                }
            }
            break;
        */
            
        /* //Site Care Plan alert_tbl
        case 'alert_tbl':
            $parms = array();
            $parms['table_name'] = 'alert_tbl';
            $parms['fields'] = 'alertId,upload_path';
            $parms['where'] = "";

            $alerts = fetchImages($parms);
            while ($row = imw_fetch_assoc($alerts)) {
                //foreach ($alerts as $row) {
                if ($row['upload_path'] == '') {
                    //echo '<font face="Arial, Helvetica, sans-serif" size="2" color="red"> No image found for ' . $row['alertId'] . '</font> <br />';
                    continue;
                }
                $pathinfo = pathinfo($row['upload_path']);
                $dirname = $pathinfo['dirname'];
                $filename = $pathinfo['basename'];

                //$filename = $row['upload_path'];
                $old_src = $src_url . "interface/admin/console/alert/upload/" . $dirname . "/" . $filename;
                $old_filepath = $src_path . "interface/admin/console/alert/upload/" . $dirname;
                $old_src_db = $src_url_db . "interface/admin/console/alert/upload/" . $dirname . "/" . $filename;
                $old_filepath_db = $src_path_db . "interface/admin/console/alert/upload/" . $dirname;

                $new_filepath = $GLOBALS['fileroot'] . "/data/" . constant('PRACTICE_PATH') . "/site_care_plan/" . $dirname;
                if ($https) {
                    $old_url = str_replace(array('https://', 'http://'), 'https://', $old_src);
                    $old_url_db = str_replace(array('https://', 'http://'), 'https://', $old_src_db);
                } else {
                    $old_url = str_replace(array('https://', 'http://'), 'http://', $old_src);
                    $old_url_db = str_replace(array('https://', 'http://'), 'http://', $old_src_db);
                }

                if (file_exists($old_filepath . "/" . $filename)) {
                    if (!is_dir($new_filepath)) {
                        mkdir($new_filepath, 0777, true);
                    }
                    $content = file_get_contents($old_filepath . "/" . $filename);
                    file_put_contents($new_filepath . "/" . $filename, $content);

                    $counter++;
                }
            }
            break;
        */
            
        /* //chart_drawicon
        case 'chart_drawicon':
            $parms = array();
            $parms['table_name'] = 'chart_drawicon';
            $parms['fields'] = 'id,drwico_path';
            $parms['where'] = "";

            $chart_drawicons = fetchImages($parms);
            while ($row = imw_fetch_assoc($chart_drawicons)) {
                //foreach ($chart_drawicons as $row) {
                if ($row['drwico_path'] == '') {
                    //echo '<font face="Arial, Helvetica, sans-serif" size="2" color="red"> No image found for ' . $row['id'] . '</font> <br />';
                    continue;
                }
                $filename = $row['drwico_path'];
                $old_src = $src_url . "interface/main/uploaddir/drawicon" . $filename;
                $old_filepath = $src_path . "interface/main/uploaddir/drawicon";
                $old_src_db = $src_url_db . "interface/main/uploaddir/drawicon" . $filename;
                $old_filepath_db = $src_path_db . "interface/main/uploaddir/drawicon";

                $new_filepath = $GLOBALS['fileroot'] . "/data/" . constant('PRACTICE_PATH') . "/drawicon";

                if ($https) {
                    $old_url = str_replace(array('https://', 'http://'), 'https://', $old_src);
                    $old_url_db = str_replace(array('https://', 'http://'), 'https://', $old_src_db);
                } else {
                    $old_url = str_replace(array('https://', 'http://'), 'http://', $old_src);
                    $old_url_db = str_replace(array('https://', 'http://'), 'http://', $old_src_db);
                }

                if (file_exists($old_filepath . $filename)) {
                    if (!is_dir($new_filepath)) {
                        mkdir($new_filepath, 0777, true);
                    }
                    $content = file_get_contents($old_filepath . $filename);
                    file_put_contents($new_filepath . $filename, $content);

                    $counter++;
                }
            }
            break;
        */
            
        //document_logos
        case 'doc_logos':
            $parms = array();
            $parms['table_name'] = 'document_logos';
            $parms['fields'] = 'id,img_url';
            $parms['where'] = "";

            $doc_logos = fetchImages($parms);
            while ($row = imw_fetch_assoc($doc_logos)) {
                //foreach ($doc_logos as $row) {
                if ($row['img_url'] == '') {
                    //echo '<font face="Arial, Helvetica, sans-serif" size="2" color="red"> No image found for ' . $row['id'] . '</font> <br />';
                    continue;
                }
                $pathinfo = pathinfo($row['img_url']);
                //$dirname = $pathinfo['dirname'];
                //$filename = $pathinfo['basename'];

                $filename = $pathinfo['basename'];
                $old_src = $src_url . "interface/main/uploaddir/document_logos/" . $filename;
                $old_filepath = $src_path . "interface/main/uploaddir/document_logos";
                $old_src_db = $src_url_db . "interface/main/uploaddir/document_logos/" . $filename;
                $old_filepath_db = $src_path_db . "interface/main/uploaddir/document_logos";

                $new_filepath = $GLOBALS['fileroot'] . "/data/" . constant('PRACTICE_PATH') . "/gn_images";
                if ($https) {
                    $old_url = str_replace(array('https://', 'http://'), 'https://', $old_src);
                    $old_url_db = str_replace(array('https://', 'http://'), 'https://', $old_src_db);
                } else {
                    $old_url = str_replace(array('https://', 'http://'), 'http://', $old_src);
                    $old_url_db = str_replace(array('https://', 'http://'), 'http://', $old_src_db);
                }

                if (file_exists($old_filepath . '/' . $filename)) {
//                    if (!is_dir($new_filepath)) {
//                        mkdir($new_filepath, 0777, true);
//                    }
//                    $content = file_get_contents($old_filepath . '/' . $filename);
//                    file_put_contents($new_filepath . '/' . $filename, $content);

                    $counter++;

                    $query = "UPDATE document_logos SET img_url='" . $GLOBALS['webroot'] . "/data/" . constant('PRACTICE_PATH') . '/gn_images/' . $filename . "' WHERE id={$row['id']}";
                    imw_query($query) or $msg_info[] = imw_error();
                }
            }
            break;

        /* //facility
        case 'facility':
            $parms = array();
            $parms['table_name'] = 'facility';
            $parms['fields'] = 'id,logo';
            $parms['where'] = "";

            $facilities = fetchImages($parms);
            while ($row = imw_fetch_assoc($facilities)) {
                //foreach ($facilities as $row) {
                if ($row['logo'] == '') {
                    //echo '<font face="Arial, Helvetica, sans-serif" size="2" color="red"> No image found for ' . $row['id'] . '</font> <br />';
                    continue;
                }
                $filename = $row['logo'];
                $old_src = $src_url . "images/facilitylogo/" . $filename;
                $old_filepath = $src_path . "images/facilitylogo";
                $old_src_db = $src_url_db . "images/facilitylogo/" . $filename;
                $old_filepath_db = $src_path_db . "images/facilitylogo";

                $new_filepath = $GLOBALS['fileroot'] . "/data/" . constant('PRACTICE_PATH') . "/facilitylogo";
                if ($https) {
                    $old_url = str_replace(array('https://', 'http://'), 'https://', $old_src);
                    $old_url_db = str_replace(array('https://', 'http://'), 'https://', $old_src_db);
                } else {
                    $old_url = str_replace(array('https://', 'http://'), 'http://', $old_src);
                    $old_url_db = str_replace(array('https://', 'http://'), 'http://', $old_src_db);
                }

                if (file_exists($old_filepath . '/' . $filename)) {
                    if (!is_dir($new_filepath)) {
                        mkdir($new_filepath, 0777, true);
                    }
                    $content = file_get_contents($old_filepath . '/' . $filename);
                    file_put_contents($new_filepath . '/' . $filename, $content);

                    $counter++;
                }
            }
            break;
        */
            
        //Providers
        case 'providers':
            $parms = array();
            $parms['table_name'] = 'users';
            $parms['fields'] = 'id,sign_path';
            $parms['where'] = "";

            $providers = fetchImages($parms);
            while ($row = imw_fetch_assoc($providers)) {
                //foreach ($providers as $row) {
                if ($row['sign_path'] != '') {
                    $pathinfo = pathinfo(trim($row['sign_path']));
                    $dirname = $pathinfo['dirname'];
                    $filename = $pathinfo['basename'];

                    $old_src = $src_url . "interface/main/uploaddir" . $dirname . "/" . $filename;
                    $old_filepath = $src_path . "interface/main/uploaddir" . $dirname;
                    $old_src_db = $src_url_db . "interface/main/uploaddir" . $dirname . "/" . $filename;
                    $old_filepath_db = $src_path_db . "interface/main/uploaddir" . $dirname;

                    $new_filepath = $GLOBALS['fileroot'] . "/data/" . constant('PRACTICE_PATH') . $dirname;

                    if ($https) {
                        $old_url = str_replace(array('https://', 'http://'), 'https://', $old_src);
                        $old_url_db = str_replace(array('https://', 'http://'), 'https://', $old_src_db);
                    } else {
                        $old_url = str_replace(array('https://', 'http://'), 'http://', $old_src);
                        $old_url_db = str_replace(array('https://', 'http://'), 'http://', $old_src_db);
                    }

                    if (file_exists($old_filepath . '/' . $filename)) {
//                        if (!is_dir($new_filepath)) {
//                            mkdir($new_filepath, 0777, true);
//                        }
//                        $content = file_get_contents($old_filepath . '/' . $filename);
//                        file_put_contents($new_filepath . '/' . $filename, $content);

                        $counter++;

                        $sql = "UPDATE users SET sign_path='" . $dirname . "/" . $filename . "' WHERE id={$row['id']}";
                        imw_query($sql) or $msg_info[] = imw_error();
                    }
                }
            }
            break;

        //groups //hippa_setting
        case 'groups':
            $parms = array();
            $parms['table_name'] = 'hippa_setting';
            $parms['fields'] = 'id,loginLegalNotice';
            $parms['where'] = "";

            $groups = fetchImages($parms);
            while ($row = imw_fetch_assoc($groups)) {
                //foreach ($groups as $group) {
                $doc = new DOMDocument();
                $doc->loadHTML($group['loginLegalNotice']);
                $imgs = $doc->getElementsByTagName('img');
                if ($imgs->length == 0) {
                    //echo '<font face="Arial, Helvetica, sans-serif" size="2" color="red"> No image found for ' . $row['id'] . '</font> <br />';
                    return false;
                }

                foreach ($imgs as $img) {
                    $new_url = '';
                    $old_src = $img->getAttribute('src');

                    $pathinfo = pathinfo($old_src);
                    $parse_url = parse_url($pathinfo['dirname']);

                    $count = count(explode('/', $parse_url['path']));
                    $dirname = '';
                    for ($i = 0; $i < ($count - 2); $i++) {
                        if ($dirname == '') {
                            $dirname = dirname($parse_url['path']);
                        } else {
                            $dirname = dirname($dirname);
                        }
                    }
                    $filename = $pathinfo['filename'] . '.' . $pathinfo['extension'];
                    $old_filepath1 = $GLOBALS['fileroot'] . "/.." . $parse_url['path'];
                    $old_filepath = $GLOBALS['fileroot'] . "/.." . $parse_url['path'];
                    if(strpos($old_filepath, $r7main_dir_db) != false) {
                        $old_filepath = str_replace($r7main_dir_db, $r7main_dir, $old_filepath);
                    }
                    $new_filepath = str_replace($dirname, $GLOBALS['webroot'] . "/data/" . constant('PRACTICE_PATH'), $old_filepath1);
                    $new_url = str_replace($dirname, $GLOBALS['webroot'] . "/data/" . constant('PRACTICE_PATH'), $old_src);
                    if ($https) {
                        $new_url = str_replace(array('https://', 'http://'), 'https://', $new_url);
                        $old_url = str_replace(array('https://', 'http://'), 'https://', $old_src);
                    } else {
                        $new_url = str_replace(array('https://', 'http://'), 'http://', $new_url);
                        $old_url = str_replace(array('https://', 'http://'), 'http://', $old_src);
                    }

                    if (file_exists($old_filepath . '/' . $filename)) {
                        if (!is_dir($new_filepath)) {
                            mkdir($new_filepath, 0777, true);
                        }
                        $content = file_get_contents($old_filepath . '/' . $filename);
                        file_put_contents($new_filepath . '/' . $filename, $content);
                    }
                    $img->setAttribute('src', $new_url);
                    $counter++;
                }

                $result = $doc->saveHTML();

                $sql = "UPDATE hippa_setting SET loginLegalNotice='" . $result . "' WHERE id={$group['id']}";
                imw_query($sql) or $msg_info[] = imw_error();
            }
            break;

        //collection_letter_templates
        case 'cl_templates':
            $parms = array();
            $parms['table_name'] = 'collection_letter_template';
            $parms['fields'] = 'id,collection_data';
            $parms['where'] = "";

            $collection_data = fetchImages($parms);
            while ($row = imw_fetch_assoc($collection_data)) {
                //foreach ($collection_data as $row) {

                $doc = new DOMDocument();
                $doc->loadHTML($row['collection_data']);
                $imgs = $doc->getElementsByTagName('img');
                if ($imgs->length == 0) {
                    //echo '<font face="Arial, Helvetica, sans-serif" size="2" color="red"> No image found for ' . $row['id'] . '</font> <br />';
                    continue;
                }

                foreach ($imgs as $img) {
                    $new_url = '';
                    $old_src = $img->getAttribute('src');

                    $pathinfo = pathinfo($old_src);
                    $parse_url = parse_url($pathinfo['dirname']);

                    $count = count(explode('/', $parse_url['path']));
                    $dirname = '';
                    for ($i = 0; $i < ($count - 2); $i++) {
                        if ($dirname == '') {
                            $dirname = dirname($parse_url['path']);
                        } else {
                            $dirname = dirname($dirname);
                        }
                    }
                    $filename = $pathinfo['filename'] . '.' . $pathinfo['extension'];
                    $old_filepath1 = $GLOBALS['fileroot'] . "/.." . $parse_url['path'];
                    $old_filepath = $GLOBALS['fileroot'] . "/.." . $parse_url['path'];
                    if(strpos($old_filepath, $r7main_dir_db) != false) {
                        $old_filepath = str_replace($r7main_dir_db, $r7main_dir, $old_filepath);
                    }
                    $new_filepath = str_replace($dirname, $GLOBALS['webroot'] . "/data/" . constant('PRACTICE_PATH'), $old_filepath1);
                    $new_url = str_replace($dirname, $GLOBALS['webroot'] . "/data/" . constant('PRACTICE_PATH'), $old_src);
                    if ($https) {
                        $new_url = str_replace(array('https://', 'http://'), 'https://', $new_url);
                        $old_url = str_replace(array('https://', 'http://'), 'https://', $old_src);
                    } else {
                        $new_url = str_replace(array('https://', 'http://'), 'http://', $new_url);
                        $old_url = str_replace(array('https://', 'http://'), 'http://', $old_src);
                    }

                    if (file_exists($old_filepath . '/' . $filename)) {
                        if (!is_dir($new_filepath)) {
                            mkdir($new_filepath, 0777, true);
                        }
                        $content = file_get_contents($old_filepath . '/' . $filename);
                        file_put_contents($new_filepath . '/' . $filename, $content);
                    }
                    $img->setAttribute('src', $new_url);
                    $counter++;
                }

                $result = $doc->saveHTML();

                $sql = "UPDATE collection_letter_template SET collection_data='" . $result . "' WHERE id={$row['id']}";
                imw_query($sql) or $msg_info[] = imw_error();
            }
            break;
            
            //patient_consent_form_information
        case 'pt_consent_form_info':
            $parms = array();
            $parms['table_name'] = 'patient_consent_form_information';
            $parms['fields'] = 'form_information_id,patient_id,operator_id,consent_form_content_data';
            $parms['where'] = "";

            $consent_form_info = fetchImages($parms);
            while ($row = imw_fetch_assoc($consent_form_info)) {
                //foreach ($consent_form_info as $row) {
                $doc = new DOMDocument();
                $doc->loadHTML(stripslashes(html_entity_decode($row['consent_form_content_data'])));
                $imgs = $doc->getElementsByTagName('img');


                if ($imgs->length == 0) {
                    //echo '<font face="Arial, Helvetica, sans-serif" size="2" color="red"> No image found for ' . $row['id'] . '</font> <br />';
                    continue;
                }
                $updated = false;
                foreach ($imgs as $img) {
                    $new_url = '';
                    $old_src_org = ($img->getAttribute('src'));
                    $old_src = ($img->getAttribute('src'));
                    //$old_src = str_replace(array('\"'), array(''), $old_src);
                    $pathinfo = pathinfo($old_src);
                    $parse_url = parse_url($pathinfo['dirname']);

                    //$path1='';
                    // if (strpos($pathinfo['dirname'], 'new_html2pdf') != false) {
                    $uri_segments = explode('/', $pathinfo['dirname']);
                    $path1 = array_pop($uri_segments);
                    //}
                    $count = count(explode('/', $parse_url['path']));
                    $dirname = '';
                    for ($i = 0; $i < ($count - 2); $i++) {
                        if ($dirname == '') {
                            $dirname = dirname($parse_url['path']);
                        } else {
                            $dirname = dirname($dirname);
                        }
                    }
                    if($two_folder) {
                        $parse_url['path'] = str_replace(array('PEI', 'pei/imwemr'), $r7main_dir_db, $parse_url['path']);
                    }
                    $filename = $pathinfo['filename'] . '.' . $pathinfo['extension'];
                    $old_filepath = $GLOBALS['fileroot'] . "/.." . $parse_url['path'];
                    
                    if($dirname == 'interface/common') {
                        $old_src = str_replace(array('PEI'), $r7main_dir_db, $old_src);
                        //$parse_url['path'] = str_replace(array('PEI'), $r7main_dir_db, $parse_url['path']);
                        
                        if(strcasecmp($dirname, $main_dir_db)==0) {
                            $old_src = $old_src;
                            $old_filepath = $GLOBALS['fileroot'] . "/..". $parse_url['path'];
                            $old_filepath = str_replace($r7main_dir_db, $r7main_dir, $old_filepath);
                        } else {
                            $old_src = '/'.$main_dir.'/'.$old_src;
                            $old_filepath = $GLOBALS['fileroot'] . "/..".$main_dir."/" . $parse_url['path'];
                        }
                    }
                    if(strcasecmp($dirname, $main_dir_db)==0) {
                        $old_src = str_replace(array('PEI'), $r7main_dir_db, $old_src);
                        if(strcasecmp($dirname, $main_dir_db)==0) {
                            $old_src = $old_src;
                            $old_filepath = $GLOBALS['fileroot'] . "/..". $parse_url['path'];
                            $old_filepath = str_replace($r7main_dir_db, $r7main_dir, $old_filepath);
                        } else {
                            $old_src = '/'.$main_dir.'/'.$old_src;
                            $old_filepath = $GLOBALS['fileroot'] . "/..".$main_dir."/" . $parse_url['path'];
                        }
                    }
                    if ($path1 == 'iportal_sig') {
                        $old_filepath = $GLOBALS['fileroot'] . "/..".$main_dir."/" . "interface/common/new_html2pdf/iportal_sig";
                    }
                    
                    if ($path1 == 'images' && strpos($old_src, 'redactor/images')) {
                        $new_filepath = $GLOBALS['fileroot'] . "/data/" . constant('PRACTICE_PATH') . "/redactor/images";
                        $new_url = $GLOBALS['webroot'] . "/data/" . constant('PRACTICE_PATH') . "/redactor/images/" . $filename;
                    }
                    if ($path1 == 'document_logos' || $path1 == 'html2pdf') {
                        $new_filepath = $GLOBALS['fileroot'] . "/data/" . constant('PRACTICE_PATH') . "/gn_images";
                        $new_url = $GLOBALS['webroot'] . "/data/" . constant('PRACTICE_PATH') . "/gn_images/" . $filename;
                    }
                    if ($path1 == 'SigPlus_images') {
                        $new_filepath = $GLOBALS['fileroot'] . "/data/" . constant('PRACTICE_PATH') . "/SigPlus_images";
                        $new_url = $GLOBALS['webroot'] . "/data/" . constant('PRACTICE_PATH') . "/SigPlus_images/" . $filename;
                    }
                    if ($path1 == 'iportal_sig') {
                        $new_filepath = $GLOBALS['fileroot'] . "/data/" . constant('PRACTICE_PATH') . "/PatientId_" . $row['patient_id'] . "/consent_forms/iportal_sig";
                        $new_url = $GLOBALS['webroot'] . "/data/" . constant('PRACTICE_PATH') . "/PatientId_" . $row['patient_id'] . "/consent_forms/iportal_sig/" . $filename;
                    }
                    if ($path1 == 'new_html2pdf') {
                        $new_filepath = $GLOBALS['fileroot'] . "/data/" . constant('PRACTICE_PATH') . "/PatientId_" . $row['patient_id'] . "/consent_forms";
                        $new_url = $GLOBALS['webroot'] . "/data/" . constant('PRACTICE_PATH') . "/PatientId_" . $row['patient_id'] . "/consent_forms/" . $filename;
                    }
                    if ($path1 == 'sign') {
                        $new_filepath = $GLOBALS['fileroot'] . "/data/" . constant('PRACTICE_PATH') . "/sign";
                        $new_url = $GLOBALS['webroot'] . "/data/" . constant('PRACTICE_PATH') . "/UserId_" . $row['operator_id'] . "/sign/" . $filename;
                    }
                    if ($path1 == 'consent_forms') {
                        continue;
                        //$new_filepath = $GLOBALS['fileroot'] . "/data/" . constant('PRACTICE_PATH') ."/PatientId_".$row['patient_id']."/consent_forms";
                        //$new_url = $GLOBALS['webroot'] . "/data/" . constant('PRACTICE_PATH') ."/PatientId_".$row['patient_id']."/consent_forms/".$filename;
                    }

                    if ($https) {
                        $new_url = str_replace(array('https://', 'http://'), 'https://', $new_url);
                        $old_url = str_replace(array('https://', 'http://'), 'https://', $old_src);
                    } else {
                        $new_url = str_replace(array('https://', 'http://'), 'http://', $new_url);
                        $old_url = str_replace(array('https://', 'http://'), 'http://', $old_src);
                    }

//file_put_contents('file_name.txt',($path1.PHP_EOL.$old_src.PHP_EOL.$old_filepath.PHP_EOL.$new_filepath.PHP_EOL.$new_url).PHP_EOL.PHP_EOL,FILE_APPEND);continue;
                    $new_url = str_replace('"', '', $new_url);
//file_put_contents('file_name.txt',($path1.PHP_EOL.$old_src.PHP_EOL.$old_filepath.PHP_EOL.$new_filepath.PHP_EOL.$new_url).PHP_EOL,FILE_APPEND);
                    if (file_exists($old_filepath . '/' . $filename)) {
                        if (!is_dir($new_filepath)) {
                            mkdir($new_filepath, 0777, true);
                        }
                        $content = file_get_contents($old_filepath . '/' . $filename);
                        file_put_contents($new_filepath . '/' . $filename, $content);
                    }


                    if (strtolower($old_filepath) != strtolower($new_filepath) && $new_url != '') {
                        $row['consent_form_content_data'] = str_replace($old_src_org, htmlentities($new_url), $row['consent_form_content_data']);
                        $updated = true;
                    }
                } 

                if ($updated) {
                    $sql = "UPDATE patient_consent_form_information SET consent_form_content_data='" . $row['consent_form_content_data'] . "' WHERE form_information_id='" . $row['form_information_id'] . "'";
                    imw_query($sql);

                    $counter++;
                }
            }
            break;
            
            //surgery_center_pre_op_health_ques
        case 'surgery_center_pre_op_health_ques':
            $parms = array();
            $parms['table_name'] = 'surgery_center_pre_op_health_ques';
            $parms['fields'] = 'preOpHealthQuesId,patient_id,patient_sign_image_path,witness_sign_image_path';
            $parms['where'] = " patient_sign_image_path != '' OR witness_sign_image_path != '' ";

            $consent_form_signature = fetchImages($parms);

            while ($row = imw_fetch_assoc($consent_form_signature)) {
                if ($row['patient_sign_image_path'] != '') {
                    $pathinfo1 = pathinfo($row['patient_sign_image_path']);
                    $dirname1 = $pathinfo1['dirname'];
                    $filename1 = $pathinfo1['basename'];

                    $old_src1 = $src_url . "interface/main" . $dirname1 . "/" . $filename1;
                    $old_filepath1 = $src_path . "interface/main/uploaddir/" . $dirname1;
                    $old_src1_db = $src_url_db . "interface/main" . $dirname1 . "/" . $filename1;
                    $old_filepath1_db = $src_path_db . "interface/main/uploaddir/" . $dirname1;

                    if ($https) {
                        $old_url1 = str_replace(array('https://', 'http://'), 'https://', $old_src1);
                        $old_url1_db = str_replace(array('https://', 'http://'), 'https://', $old_src1_db);
                    } else {
                        $old_url1 = str_replace(array('https://', 'http://'), 'http://', $old_src1);
                        $old_url1_db = str_replace(array('https://', 'http://'), 'http://', $old_src1_db);
                    }
                    //$new_filepath1 = $GLOBALS['fileroot'] . "/data/" . constant('PRACTICE_PATH') . $dirname1;
                    
                    $new_filepath1 = $GLOBALS['fileroot'] . "/data/" . constant('PRACTICE_PATH') . "/PatientId_" . $row['patient_id'] . "/consent_forms";
                    $new_url1 = "/PatientId_" . $row['patient_id'] . "/consent_forms/" . $filename1;

                    if (file_exists($old_filepath1 . "/" . $filename1)) {
                        if (!is_dir($new_filepath1)) {
                            mkdir($new_filepath1, 0777, true);
                        }
                        $content1 = file_get_contents($old_filepath1 . "/" . $filename1);
                        file_put_contents($new_filepath1 . "/" . $filename1, $content1);

                        $counter++;
                        if (strtolower($old_filepath1_db) != strtolower($new_filepath1) && $new_url1) {
                            $sql = "UPDATE surgery_center_pre_op_health_ques SET patient_sign_image_path='" . $new_url1 . "' WHERE preOpHealthQuesId='" . $row['preOpHealthQuesId'] . "'";
                            imw_query($sql) or $msg_info[] = imw_error();
                        }
                    }
                }

                if ($row['witness_sign_image_path'] != '') {
                    $pathinfo2 = pathinfo($row['witness_sign_image_path']);
                    $dirname2 = $pathinfo2['dirname'];
                    $filename2 = $pathinfo2['basename'];

                    $old_src2 = $src_url . "interface/main" . $dirname2 . "/" . $filename2;
                    $old_filepath2 = $src_path . "interface/main/uploaddir/" . $dirname2;
                    $old_src2_db = $src_url_db . "interface/main" . $dirname2 . "/" . $filename2;
                    $old_filepath2_db = $src_path_db . "interface/main/uploaddir/" . $dirname2;

                    if ($https) {
                        $old_url2 = str_replace(array('https://', 'http://'), 'https://', $old_src2);
                        $old_url2_db = str_replace(array('https://', 'http://'), 'https://', $old_src2_db);
                    } else {
                        $old_url2 = str_replace(array('https://', 'http://'), 'http://', $old_src2);
                        $old_url2_db = str_replace(array('https://', 'http://'), 'http://', $old_src2_db);
                    }
                    //$new_filepath2 = $GLOBALS['fileroot'] . "/data/" . constant('PRACTICE_PATH') . $dirname2;
                    
                    $new_filepath2 = $GLOBALS['fileroot'] . "/data/" . constant('PRACTICE_PATH') . "/PatientId_" . $row['patient_id'] . "/consent_forms";
                    $new_url2 = "/PatientId_" . $row['patient_id'] . "/consent_forms/" . $filename2;

                    if (file_exists($old_filepath2 . "/" . $filename2)) {
                        if (!is_dir($new_filepath2)) {
                            mkdir($new_filepath2, 0777, true);
                        }
                        $content2 = file_get_contents($old_filepath2 . "/" . $filename2);
                        file_put_contents($new_filepath2 . "/" . $filename2, $content2);

                        $counter++;
                        if (strtolower($old_filepath2_db) != strtolower($new_filepath2) && $new_url2) {
                            $sql = "UPDATE surgery_center_pre_op_health_ques SET witness_sign_image_path='" . $new_url2 . "' WHERE preOpHealthQuesId='" . $row['preOpHealthQuesId'] . "'";
                            imw_query($sql) or $msg_info[] = imw_error();
                        }

                    }
                }
                
            }
            break;


            
        //surgery_consent_filled_form
        case 'surgery_consent_filled_form':
            $parms = array();
            $parms['table_name'] = 'surgery_consent_filled_form';
            $parms['fields'] = 'surgery_consent_id,patient_id,surgery_consent_data';
            $parms['where'] = "";

            $consent_form_info = fetchImages($parms);
            while ($row = imw_fetch_assoc($consent_form_info)) {
                //foreach ($consent_form_info as $row) {
                $doc = new DOMDocument();
                //$doc->loadHTML(stripslashes(html_entity_decode($row['surgery_consent_data'])));
                $doc->loadHTML(stripslashes(htmlspecialchars_decode(html_entity_decode($row['surgery_consent_data']))));
                $imgs = $doc->getElementsByTagName('img');


                if ($imgs->length == 0) {
                    //echo '<font face="Arial, Helvetica, sans-serif" size="2" color="red"> No image found for ' . $row['id'] . '</font> <br />';
                    continue;
                }
                $updated = false;
                foreach ($imgs as $img) {
                    $new_url = '';
                    $old_src_org = ($img->getAttribute('src'));
                    $old_src = ($img->getAttribute('src'));
                    //$old_src = str_replace(array('\"'), array(''), $old_src);
                    $pathinfo = pathinfo($old_src);
                    $parse_url = parse_url($pathinfo['dirname']);

                    //$path1='';
                    // if (strpos($pathinfo['dirname'], 'new_html2pdf') != false) {
                    $uri_segments = explode('/', $pathinfo['dirname']);
                    $path1 = array_pop($uri_segments);
                    //}
                    $count = count(explode('/', $parse_url['path']));
                    $dirname = '';
                    for ($i = 0; $i < ($count - 2); $i++) {
                        if ($dirname == '') {
                            $dirname = dirname($parse_url['path']);
                        } else {
                            $dirname = dirname($dirname);
                        }
                    }

                    if($two_folder) {
                        $parse_url['path'] = str_replace(array('PEI', 'pei/imwemr'), $r7main_dir_db, $parse_url['path']);
                    }

                    $filename = $pathinfo['filename'] . '.' . $pathinfo['extension'];
                    $old_filepath = $GLOBALS['fileroot'] . "/.." . $parse_url['path'];
                    if($dirname == 'interface/common') {
                        $old_src = str_replace(array('imwdemo'), $r7main_dir_db, $old_src);
                        
                        if(strcasecmp($dirname, $main_dir_db)==0) {
                            $old_src = $old_src;
                            $old_filepath = $GLOBALS['fileroot'] . "/..". $parse_url['path'];
                            $old_filepath = str_replace($r7main_dir_db, $r7main_dir, $old_filepath);
                        } else {
                            $old_src = '/'.$main_dir.'/'.$old_src;
                            $old_filepath = $GLOBALS['fileroot'] . "/..".$main_dir."/" . $parse_url['path'];
                        }
                    }
                    if(strcasecmp($dirname, $main_dir_db)==0) {
                        $old_src = str_replace(array('imwemr_demo'), $r7main_dir_db, $old_src);
                        if(strcasecmp($dirname, $main_dir_db)==0) {
                            $old_src = $old_src;
                            $old_filepath = $GLOBALS['fileroot'] . "/..". $parse_url['path'];
                            $old_filepath = str_replace($r7main_dir_db, $r7main_dir, $old_filepath);
                        } else {
                            $old_src = '/'.$main_dir.'/'.$old_src;
                            $old_filepath = $GLOBALS['fileroot'] . "/..".$main_dir."/" . $parse_url['path'];
                        }
                    }
                    if ($path1 == 'iportal_sig') {
                        $old_filepath = $GLOBALS['fileroot'] . "/..".$main_dir."/" . "interface/common/new_html2pdf/iportal_sig";
                    }
                    if ($path1 == 'images' && strpos($old_src, 'redactor/images')) {
                        $new_filepath = $GLOBALS['fileroot'] . "/data/" . constant('PRACTICE_PATH') . "/redactor/images";
                        $new_url = $GLOBALS['webroot'] . "/data/" . constant('PRACTICE_PATH') . "/redactor/images/" . $filename;
                    }
                    if ($path1 == 'document_logos' || $path1 == 'html2pdf') {
                        $new_filepath = $GLOBALS['fileroot'] . "/data/" . constant('PRACTICE_PATH') . "/gn_images";
                        $new_url = $GLOBALS['webroot'] . "/data/" . constant('PRACTICE_PATH') . "/gn_images/" . $filename;
                    }
                    if ($path1 == 'SigPlus_images') {
                        $new_filepath = $GLOBALS['fileroot'] . "/data/" . constant('PRACTICE_PATH') . "/SigPlus_images";
                        $new_url = $GLOBALS['webroot'] . "/data/" . constant('PRACTICE_PATH') . "/SigPlus_images/" . $filename;
                    }
                    if ($path1 == 'iportal_sig') {
                        $new_filepath = $GLOBALS['fileroot'] . "/data/" . constant('PRACTICE_PATH') . "/PatientId_" . $row['patient_id'] . "/consent_forms/iportal_sig";
                        $new_url = $GLOBALS['webroot'] . "/data/" . constant('PRACTICE_PATH') . "/PatientId_" . $row['patient_id'] . "/consent_forms/iportal_sig/" . $filename;
                    }
                    if ($path1 == 'new_html2pdf') {
                        $new_filepath = $GLOBALS['fileroot'] . "/data/" . constant('PRACTICE_PATH') . "/PatientId_" . $row['patient_id'] . "/consent_forms";
                        $new_url = $GLOBALS['webroot'] . "/data/" . constant('PRACTICE_PATH') . "/PatientId_" . $row['patient_id'] . "/consent_forms/" . $filename;
                    }
                    if ($path1 == 'sign') {
                        $new_filepath = $GLOBALS['fileroot'] . "/data/" . constant('PRACTICE_PATH') . "/sign";
                        $new_url = $GLOBALS['webroot'] . "/data/" . constant('PRACTICE_PATH') . "/UserId_" . $row['operator_id'] . "/sign/" . $filename;
                    }
                    if ($path1 == 'consent_forms') {
                        continue;
                        //$new_filepath = $GLOBALS['fileroot'] . "/data/" . constant('PRACTICE_PATH') ."/PatientId_".$row['patient_id']."/consent_forms";
                        //$new_url = $GLOBALS['webroot'] . "/data/" . constant('PRACTICE_PATH') ."/PatientId_".$row['patient_id']."/consent_forms/".$filename;
                    }

                    
                    if ($https) {
                        $new_url = str_replace(array('https://', 'http://'), 'https://', $new_url);
                        $old_url = str_replace(array('https://', 'http://'), 'https://', $old_src);
                    } else {
                        $new_url = str_replace(array('https://', 'http://'), 'http://', $new_url);
                        $old_url = str_replace(array('https://', 'http://'), 'http://', $old_src);
                    }

                    $new_url = str_replace('"', '', $new_url);
//file_put_contents('file_name.txt',($path1.PHP_EOL.$old_src.PHP_EOL.$old_filepath.PHP_EOL.$new_filepath.PHP_EOL.$new_url).PHP_EOL,FILE_APPEND);
                    if (file_exists($old_filepath . '/' . $filename)) {
                        if (!is_dir($new_filepath)) {
                            mkdir($new_filepath, 0777, true);
                        }
                        $content = file_get_contents($old_filepath . '/' . $filename);
                        file_put_contents($new_filepath . '/' . $filename, $content);
                    }

                    if (strtolower($old_filepath) != strtolower($new_filepath) && $new_url != '') {
                        $row['surgery_consent_data'] = str_replace($old_src_org, htmlentities($new_url), $row['surgery_consent_data']);
                        $updated = true;
                    }
                }

                if ($updated) {
//                    $result = $doc->saveHTML();
                    $sql = "UPDATE surgery_consent_filled_form SET surgery_consent_data='" . addslashes($row['surgery_consent_data']) . "' WHERE surgery_consent_id='" . $row['surgery_consent_id'] . "'";
                    //$sql = "UPDATE surgery_consent_filled_form SET surgery_consent_data='" . $row['surgery_consent_data'] . "' WHERE surgery_consent_id='" . $row['surgery_consent_id'] . "' ";
                    imw_query($sql) or $msg_info[] = imw_error();

                    $counter++;
                }
            }
            break;

                 //sigplus_images consent_form_signature
        case 'consent_form_signature':
            $parms = array();
            $parms['table_name'] = 'consent_form_signature';
            $parms['fields'] = 'consent_form_signature_id,patient_id,signature_image_path';
            $parms['where'] = " signature_image_path != '' ";

            $consent_form_signature = fetchImages($parms);

            while ($row = imw_fetch_assoc($consent_form_signature)) {
                $old_src = $row['signature_image_path'];
                $pathinfo = pathinfo($old_src);
                $filename = $pathinfo['basename'];

                $old_filepath = $GLOBALS['fileroot'] . "/.." . $main_dir ."/interface/SigPlus_images/".$filename;
                $new_filepath = $GLOBALS['fileroot'] . "/data/" . constant('PRACTICE_PATH') . "/SigPlus_images/";

                if (file_exists($old_filepath)) {
                    if (!is_dir($new_filepath)) {
                        mkdir($new_filepath, 0777, true);
                    }
                    $content = file_get_contents($old_filepath);
                    file_put_contents($new_filepath . $filename, $content);

                    $counter++;

                    $query = "UPDATE consent_form_signature SET signature_image_path='" . $new_filepath.$filename . "' WHERE consent_form_signature_id={$row['consent_form_signature_id']}";
                    imw_query($query) or $msg_info[] = imw_error();
                }
                
            }
            break;
            
         //surgery_consent_form_signature
        case 'surgery_consent_form_signature':
            $parms = array();
            $parms['table_name'] = 'surgery_consent_form_signature';
            $parms['fields'] = 'consent_form_signature_id,patient_id,signature_image_path';
            $parms['where'] = " signature_image_path != '' ";

            $consent_form_signature = fetchImages($parms);

            while ($row = imw_fetch_assoc($consent_form_signature)) {
                $old_src = $row['signature_image_path'];
                $pathinfo = pathinfo($old_src);
                $filename = $pathinfo['basename'];

                $old_filepath = $GLOBALS['fileroot'] . "/.." . $main_dir ."/interface/common/html2pdf/".$filename;
                $new_filepath = $GLOBALS['fileroot'] . "/data/" . constant('PRACTICE_PATH') . "/PatientId_" . $row['patient_id'] . "/consent_forms/";

                if(strpos($old_src, 'html2pdf') !== false) {
                    $old_filepath = $GLOBALS['fileroot'] . "/.." . $main_dir ."/interface/common/html2pdf/".$filename;
                    $new_filepath = $GLOBALS['fileroot'] . "/data/" . constant('PRACTICE_PATH') . "/PatientId_" . $row['patient_id'] . "/consent_forms/";
                }
                if(strpos($old_src, 'new_html2pdf') !== false) {
                    $old_filepath = $GLOBALS['fileroot'] . "/.." . $main_dir ."/interface/common/new_html2pdf/".$filename;
                    $new_filepath = $GLOBALS['fileroot'] . "/data/" . constant('PRACTICE_PATH') . "/PatientId_" . $row['patient_id'] . "/consent_forms/";
                }
                if($old_src == '/'.$filename) {
                    continue;
                }

                if (file_exists($old_filepath)) {
                    if (!is_dir($new_filepath)) {
                        mkdir($new_filepath, 0777, true);
                    }
                    $content = file_get_contents($old_filepath);
                    file_put_contents($new_filepath . $filename, $content);

                    $counter++;

                    $query = "UPDATE surgery_consent_form_signature SET signature_image_path='" . $new_filepath.$filename . "' WHERE consent_form_signature_id={$row['consent_form_signature_id']}";
                    imw_query($query) or $msg_info[] = imw_error();
                }
                
            }
            break;

            
        //patient_consent_form_information
        case 'patient_consult_letter_tbl':
            $parms = array();
            $parms['table_name'] = 'patient_consult_letter_tbl';
            $parms['fields'] = 'patient_consult_id,patient_id,operator_id,templateData';
            $parms['where'] = "";

            $patient_consult_letter = fetchImages($parms);
            while ($row = imw_fetch_assoc($patient_consult_letter)) {
                //foreach ($consent_form_info as $row) {
                $doc = new DOMDocument();
                $doc->loadHTML(stripslashes(($row['templateData'])));
                //$doc->loadHTML($row['templateData']);
                $imgs = $doc->getElementsByTagName('img');


                if ($imgs->length == 0) {
                    //echo '<font face="Arial, Helvetica, sans-serif" size="2" color="red"> No image found for ' . $row['id'] . '</font> <br />';
                    continue;
                }
                $updated = false;
                foreach ($imgs as $img) {
                    $new_url = '';
                    $old_src_org = ($img->getAttribute('src'));
                    $old_src = ($img->getAttribute('src'));
                    
                   //pre($row['patient_consult_id']); pre($old_src); continue;
                    $pathinfo = pathinfo($old_src);
                    $parse_url = parse_url($pathinfo['dirname']);

                    $uri_segments = explode('/', $pathinfo['dirname']);
                    $path1 = array_pop($uri_segments);

                    $count = count(explode('/', $parse_url['path']));
                    $dirname = '';
                    for ($i = 0; $i < ($count - 2); $i++) {
                        if ($dirname == '') {
                            $dirname = dirname($parse_url['path']);
                        } else {
                            $dirname = dirname($dirname);
                        }
                    }

                    $filename = $pathinfo['filename'] . '.' . $pathinfo['extension'];
                    $old_filepath = $GLOBALS['fileroot'] . "/.." . $parse_url['path'];
                    if ($path1 == "." || $path1 == 'tmp') {
                        $old_filepath = $GLOBALS['fileroot'] . "/..".$main_dir."/interface/common/new_html2pdf";
                    }
                    
                    if($path1 == 'theme531' || $path1 == 'https:') {
                        continue;
                    }
                    if ($path1 == 'document_logos' || $path1 == 'html2pdf' || $path1 == "." || $path1 == 'tmp') {
                        $new_filepath = $GLOBALS['fileroot'] . "/data/" . constant('PRACTICE_PATH') . "/gn_images";
                        $new_url = $GLOBALS['webroot'] . "/data/" . constant('PRACTICE_PATH') . "/gn_images/" . $filename;
                    }
                    if ($path1 == 'sign') {
                        $new_filepath = $GLOBALS['fileroot'] . "/data/" . constant('PRACTICE_PATH') . "/sign";
                        $new_url = $GLOBALS['webroot'] . "/data/" . constant('PRACTICE_PATH') . "/UserId_" . $row['operator_id'] . "/sign/" . $filename;
                    }
                    
                    if ($https) {
                        $new_url = str_replace(array('https://', 'http://'), 'https://', $new_url);
                        $old_url = str_replace(array('https://', 'http://'), 'https://', $old_src);
                    } else {
                        $new_url = str_replace(array('https://', 'http://'), 'http://', $new_url);
                        $old_url = str_replace(array('https://', 'http://'), 'http://', $old_src);
                    }

//file_put_contents('file_name.txt',($path1.PHP_EOL.$old_src.PHP_EOL.$old_filepath.PHP_EOL.$new_filepath.PHP_EOL.$new_url).PHP_EOL,FILE_APPEND);continue;
                    if (file_exists($old_filepath . '/' . $filename)) {
                        if (!is_dir($new_filepath)) {
                            mkdir($new_filepath, 0777, true);
                        }
                        $content = file_get_contents($old_filepath . '/' . $filename);
                        file_put_contents($new_filepath . '/' . $filename, $content);
                    }

                    if (strtolower($old_filepath) != strtolower($new_filepath) && $new_url != '') {
                        $row['templateData'] = str_replace($old_src_org, "".$new_url."", $row['templateData']);
                        $updated = true;
                    }
                }
                
                
//file_put_contents($counter_file,$row['form_information_id']);
                if ($updated) {
//                    $result = $doc->saveHTML();
                    $sql = "UPDATE patient_consult_letter_tbl SET templateData='" . addslashes($row['templateData']) . "' WHERE patient_consult_id={$row['patient_consult_id']}";
                    imw_query($sql) or $msg_info[] = imw_error();

                    $counter++;
                }
            }
            break;
            
        /* //sigplus_images consent_form_signature
        case 'document_patient_rel':
            $parms = array();
            $parms['table_name'] = 'document_patient_rel';
            $parms['fields'] = 'id,p_id,upload_doc_file_path';
            $parms['where'] =  " upload_doc_file_path != '' ";

            $document_patient_rel = fetchImages($parms);

            while ($row = imw_fetch_assoc($document_patient_rel)) {
                $old_src = $row['upload_doc_file_path'];
                $pathinfo = pathinfo($old_src);
                $filename = $pathinfo['basename'];
                $dirname = $pathinfo['dirname'];

                $old_filepath = $GLOBALS['fileroot'] . "/.." . $main_dir . "/interface/main/uploaddir" .$dirname. "/" . $filename;
                $new_filepath = $GLOBALS['fileroot'] . "/data/" . constant('PRACTICE_PATH') .$dirname. "/";

                if (file_exists($old_filepath)) {
                    if (!is_dir($new_filepath)) {
                        mkdir($new_filepath, 0777, true);
                    }
                    $content = file_get_contents($old_filepath);
                    file_put_contents($new_filepath . $filename, $content);

                    $counter++;
                }
            }
            break;
        */
            
        //pt_docs_template
        case 'pt_docs_template':
            $parms = array();
            $parms['table_name'] = 'pt_docs_template';
            $parms['fields'] = 'pt_docs_template_name,pt_docs_template_id,pt_docs_template_content';
            $parms['where'] = "";

            $pt_docs_template = fetchImages($parms);
            while ($row = imw_fetch_assoc($pt_docs_template)) {
                $doc = new DOMDocument();
                $doc->loadHTML(stripslashes(html_entity_decode($row['pt_docs_template_content'])));
                $imgs = $doc->getElementsByTagName('img');

                if ($imgs->length == 0) {
                    //echo '<font face="Arial, Helvetica, sans-serif" size="2" color="red"> No image found for ' . $row['id'] . '</font> <br />';
                    continue;
                }
                $updated = false;
                foreach ($imgs as $img) {
                    $new_url = '';
                    $new_filepath = '';
                    $old_src_org = ($img->getAttribute('src'));
                    $old_src = ($img->getAttribute('src'));

                    $pathinfo = pathinfo($old_src);
                    $parse_url = parse_url($pathinfo['dirname']);

                    $uri_segments = explode('/', $pathinfo['dirname']);
                    $path1 = array_pop($uri_segments);

                    $count = count(explode('/', $parse_url['path']));
                    $dirname = '';
                    for ($i = 0; $i < ($count - 2); $i++) {
                        if ($dirname == '') {
                            $dirname = dirname($parse_url['path']);
                        } else {
                            $dirname = dirname($dirname);
                        }
                    }

                    $filename = $pathinfo['filename'] . '.' . $pathinfo['extension'];
                    $old_filepath = $GLOBALS['fileroot'] . "/.." . $parse_url['path'];
                    if(strpos($old_filepath, $r7main_dir_db) != false) {
                        $old_filepath = str_replace($r7main_dir_db, $r7main_dir, $old_filepath);
                    }
                    if ($path1 == 'gn_images') {
                        continue;
                    }
                    if ($path1 == 'document_logos' || $path1 == 'new_html2pdf' || $path1 == 'html2pdf') {
                        $new_filepath = $GLOBALS['fileroot'] . "/data/" . constant('PRACTICE_PATH') . "/gn_images";
                        $new_url = $GLOBALS['webroot'] . "/data/" . constant('PRACTICE_PATH') . "/gn_images/" . $filename;
                    }

                    if ($https) {
                        $new_url = str_replace(array('https://', 'http://'), 'https://', $new_url);
                        $old_url = str_replace(array('https://', 'http://'), 'https://', $old_src);
                    } else {
                        $new_url = str_replace(array('https://', 'http://'), 'http://', $new_url);
                        $old_url = str_replace(array('https://', 'http://'), 'http://', $old_src);
                    }

                    if (file_exists($old_filepath . '/' . $filename)) {
                        if (!is_dir($new_filepath)) {
                            mkdir($new_filepath, 0777, true);
                        }
                        $content = file_get_contents($old_filepath . '/' . $filename);
                        file_put_contents($new_filepath . '/' . $filename, $content);
                    }
                    if (strtolower($old_filepath) != strtolower($new_filepath) && $new_url != '') {
                        $row['pt_docs_template_content'] = str_replace($old_src_org, "" . htmlentities($new_url) . "", $row['pt_docs_template_content']);
                        $updated = true;
                    }
                }


                if ($updated) {
                    $sql = "UPDATE pt_docs_template SET pt_docs_template_content='" . addslashes($row['pt_docs_template_content']) . "' WHERE pt_docs_template_id={$row['pt_docs_template_id']}";
                    imw_query($sql) or $msg_info[] = imw_error();

                    $counter++;
                }
            }
            break;

            
        //pt_docs_patient_templates
        case 'pt_docs_patient_templates':
            $parms = array();
            $parms['table_name'] = 'pt_docs_patient_templates';
            $parms['fields'] = 'pt_docs_patient_templates_id,patient_id,template_content';
            $parms['where'] = "";

            $pt_docs_patient_templates = fetchImages($parms);
            while ($row = imw_fetch_assoc($pt_docs_patient_templates)) {
                $doc = new DOMDocument();
                $doc->loadHTML(stripslashes(html_entity_decode($row['template_content'])));
                $imgs = $doc->getElementsByTagName('img');

                if ($imgs->length == 0) {
                    //echo '<font face="Arial, Helvetica, sans-serif" size="2" color="red"> No image found for ' . $row['id'] . '</font> <br />';
                    continue;
                }
                $updated = false;
                foreach ($imgs as $img) {
                    $new_url = '';
                    $old_src_org = ($img->getAttribute('src'));
                    $old_src = ($img->getAttribute('src'));

                    $pathinfo = pathinfo($old_src);
                    $parse_url = parse_url($pathinfo['dirname']);

                    $uri_segments = explode('/', $pathinfo['dirname']);
                    $path1 = array_pop($uri_segments);

                    $count = count(explode('/', $parse_url['path']));
                    $dirname = '';
                    for ($i = 0; $i < ($count - 2); $i++) {
                        if ($dirname == '') {
                            $dirname = dirname($parse_url['path']);
                        } else {
                            $dirname = dirname($dirname);
                        }
                    }

                    $filename = $pathinfo['filename'] . '.' . $pathinfo['extension'];
                    $old_filepath = $GLOBALS['fileroot'] . "/.." . $parse_url['path'];
                    if(strpos($old_filepath, $r7main_dir_db) != false) {
                        $old_filepath = str_replace($r7main_dir_db, $r7main_dir, $old_filepath);
                    }
                    if ($path1 == 'gn_images') {
                        continue;
                    }
                    if ($path1 == 'document_logos' || $path1 == 'new_html2pdf' || $path1 == 'html2pdf') {
                        $new_filepath = $GLOBALS['fileroot'] . "/data/" . constant('PRACTICE_PATH') . "/gn_images";
                        $new_url = $GLOBALS['webroot'] . "/data/" . constant('PRACTICE_PATH') . "/gn_images/" . $filename;
                    }

                    if ($https) {
                        $new_url = str_replace(array('https://', 'http://'), 'https://', $new_url);
                        $old_url = str_replace(array('https://', 'http://'), 'https://', $old_src);
                    } else {
                        $new_url = str_replace(array('https://', 'http://'), 'http://', $new_url);
                        $old_url = str_replace(array('https://', 'http://'), 'http://', $old_src);
                    }

                    if (file_exists($old_filepath . '/' . $filename)) {
                        if (!is_dir($new_filepath)) {
                            mkdir($new_filepath, 0777, true);
                        }
                        $content = file_get_contents($old_filepath . '/' . $filename);
                        file_put_contents($new_filepath . '/' . $filename, $content);
                    }
                    if (strtolower($old_filepath) != strtolower($new_filepath) && $new_url != '') {
                        $row['template_content'] = str_replace($old_src_org, "" . htmlentities($new_url) . "", $row['template_content']);
                        $updated = true;
                    }
                }


                if ($updated) {
                    $sql = "UPDATE pt_docs_patient_templates SET template_content='" . addslashes($row['template_content']) . "' WHERE pt_docs_patient_templates_id={$row['pt_docs_patient_templates_id']}";
                    imw_query($sql) or $msg_info[] = imw_error();

                    $counter++;
                }
            }
            break;

            
            //consent_form
        case 'consent_form':
            $parms = array();
            $parms['table_name'] = 'consent_form';
            $parms['fields'] = 'consent_form_id,consent_form_content';
            $parms['where'] = "";

            $consent_form = fetchImages($parms);
            while ($row = imw_fetch_assoc($consent_form)) {
                $doc = new DOMDocument();
                $doc->loadHTML(stripslashes(html_entity_decode($row['consent_form_content'])));
                $imgs = $doc->getElementsByTagName('img');

                if ($imgs->length == 0) {
                    //echo '<font face="Arial, Helvetica, sans-serif" size="2" color="red"> No image found for ' . $row['id'] . '</font> <br />';
                    continue;
                }
                $updated = false;
                foreach ($imgs as $img) {
                    $new_url = '';
                    $new_filepath = '';
                    $old_src_org = ($img->getAttribute('src'));
                    $old_src = ($img->getAttribute('src'));

                    $pathinfo = pathinfo($old_src);
                    $parse_url = parse_url($pathinfo['dirname']);

                    $uri_segments = explode('/', $pathinfo['dirname']);
                    $path1 = array_pop($uri_segments);

                    $count = count(explode('/', $parse_url['path']));
                    $dirname = '';
                    for ($i = 0; $i < ($count - 2); $i++) {
                        if ($dirname == '') {
                            $dirname = dirname($parse_url['path']);
                        } else {
                            $dirname = dirname($dirname);
                        }
                    }

                    $filename = $pathinfo['filename'] . '.' . $pathinfo['extension'];
                    $old_filepath = $GLOBALS['fileroot'] . "/.." . $parse_url['path'];
                    if(strpos($old_filepath, $r7main_dir_db) != false) {
                        $old_filepath = str_replace($r7main_dir_db, $r7main_dir, $old_filepath);
                    }
                    
                    if ($path1 == 'document_logos' || $path1 == 'new_html2pdf' || $path1 == 'html2pdf') {
                        $new_filepath = $GLOBALS['fileroot'] . "/data/" . constant('PRACTICE_PATH') . "/gn_images";
                        $new_url = $GLOBALS['webroot'] . "/data/" . constant('PRACTICE_PATH') . "/gn_images/" . $filename;
                    }
                    if ($path1 == 'images' && strpos($old_src, 'redactor/images')) {
                        $new_filepath = $GLOBALS['fileroot'] . "/data/" . constant('PRACTICE_PATH') . "/redactor/images";
                        $new_url = $GLOBALS['webroot'] . "/data/" . constant('PRACTICE_PATH') . "/redactor/images/" . $filename;
                    }
                    if ($path1 == 'gn_images') {
                        continue;
                    }
                    
                    if ($https) {
                        $new_url = str_replace(array('https://', 'http://'), 'https://', $new_url);
                        $old_url = str_replace(array('https://', 'http://'), 'https://', $old_src);
                    } else {
                        $new_url = str_replace(array('https://', 'http://'), 'http://', $new_url);
                        $old_url = str_replace(array('https://', 'http://'), 'http://', $old_src);
                    }

                    if (file_exists($old_filepath . '/' . $filename)) {
                        if (!is_dir($new_filepath)) {
                            mkdir($new_filepath, 0777, true);
                        }
                        $content = file_get_contents($old_filepath . '/' . $filename);
                        file_put_contents($new_filepath . '/' . $filename, $content);
                    }
                    if (strtolower($old_filepath) != strtolower($new_filepath) && $new_url != '') {
                        $row['consent_form_content'] = str_replace($old_src_org, "" . htmlentities($new_url) . "", $row['consent_form_content']);
                        $updated = true;
                    }
                }


                if ($updated) {
                    $sql = "UPDATE consent_form SET consent_form_content='" . addslashes($row['consent_form_content']) . "' WHERE consent_form_id={$row['consent_form_id']}";
                    imw_query($sql) or $msg_info[] = imw_error();

                    $counter++;
                }
            }
            break;
            
            //consulttemplate
        case 'consulttemplate':
            $parms = array();
            $parms['table_name'] = 'consultTemplate';
            $parms['fields'] = 'consultLeter_id,consultTemplateData';
            $parms['where'] = "";

            $consent_form = fetchImages($parms);
            while ($row = imw_fetch_assoc($consent_form)) {
                $doc = new DOMDocument();
                $doc->loadHTML(stripslashes(html_entity_decode($row['consultTemplateData'])));
                $imgs = $doc->getElementsByTagName('img');

                if ($imgs->length == 0) {
                    //echo '<font face="Arial, Helvetica, sans-serif" size="2" color="red"> No image found for ' . $row['id'] . '</font> <br />';
                    continue;
                }
                $updated = false;
                foreach ($imgs as $img) {
                    $new_url = '';
                    $new_filepath = '';
                    $old_src_org = ($img->getAttribute('src'));
                    $old_src = ($img->getAttribute('src'));

                    $pathinfo = pathinfo($old_src);
                    $parse_url = parse_url($pathinfo['dirname']);

                    $uri_segments = explode('/', $pathinfo['dirname']);
                    $path1 = array_pop($uri_segments);

                    $count = count(explode('/', $parse_url['path']));
                    $dirname = '';
                    for ($i = 0; $i < ($count - 2); $i++) {
                        if ($dirname == '') {
                            $dirname = dirname($parse_url['path']);
                        } else {
                            $dirname = dirname($dirname);
                        }
                    }

                    $filename = $pathinfo['filename'] . '.' . $pathinfo['extension'];
                    $old_filepath = $GLOBALS['fileroot'] . "/.." . $parse_url['path'];
                    if(strpos($old_filepath, $r7main_dir_db) != false) {
                        $old_filepath = str_replace($r7main_dir_db, $r7main_dir, $old_filepath);
                    }
                    
                    if ($path1 == 'document_logos' || $path1 == 'new_html2pdf' || $path1 == 'html2pdf') {
                        $new_filepath = $GLOBALS['fileroot'] . "/data/" . constant('PRACTICE_PATH') . "/gn_images";
                        $new_url = $GLOBALS['webroot'] . "/data/" . constant('PRACTICE_PATH') . "/gn_images/" . $filename;
                    }               
                    
                    if ($path1 == 'gn_images') {
                        continue;
                    }
                    
                    if ($https) {
                        $new_url = str_replace(array('https://', 'http://'), 'https://', $new_url);
                        $old_url = str_replace(array('https://', 'http://'), 'https://', $old_src);
                    } else {
                        $new_url = str_replace(array('https://', 'http://'), 'http://', $new_url);
                        $old_url = str_replace(array('https://', 'http://'), 'http://', $old_src);
                    }

                    if (file_exists($old_filepath . '/' . $filename)) {
                        if (!is_dir($new_filepath)) {
                            mkdir($new_filepath, 0777, true);
                        }
                        $content = file_get_contents($old_filepath . '/' . $filename);
                        file_put_contents($new_filepath . '/' . $filename, $content);
                    }
                    if (strtolower($old_filepath) != strtolower($new_filepath) && $new_url != '') {
                        $row['consultTemplateData'] = str_replace($old_src_org, "" . htmlentities($new_url) . "", $row['consultTemplateData']);
                        $updated = true;
                    }
                }


                if ($updated) {
                    $sql = "UPDATE consultTemplate SET consultTemplateData='" . addslashes($row['consultTemplateData']) . "' WHERE consultLeter_id={$row['consultLeter_id']}";
                    imw_query($sql) or $msg_info[] = imw_error();

                    $counter++;
                }
            }
            break;
            
            
            //pn_reports
        case 'pn_reports':
            $parms = array();
            $parms['table_name'] = 'pn_reports';
            $parms['fields'] = 'pn_rep_id,patient_id,opid,txt_data';
            $parms['where'] = "";

            $pn_reports = fetchImages($parms);
            while ($row = imw_fetch_assoc($pn_reports)) {
                //foreach ($consent_form_info as $row) {
                $doc = new DOMDocument();
                $doc->loadHTML(stripslashes(html_entity_decode($row['txt_data'])));
                $imgs = $doc->getElementsByTagName('img');


                if ($imgs->length == 0) {
                    //echo '<font face="Arial, Helvetica, sans-serif" size="2" color="red"> No image found for ' . $row['id'] . '</font> <br />';
                    continue;
                }
                $updated = false;
                foreach ($imgs as $img) {
                    $new_url = '';
                    $old_src_org = ($img->getAttribute('src'));
                    $old_src = ($img->getAttribute('src'));
                    //$old_src = str_replace(array('\"'), array(''), $old_src);
                    $pathinfo = pathinfo($old_src);
                    $parse_url = parse_url($pathinfo['dirname']);

                    //$path1='';
                    // if (strpos($pathinfo['dirname'], 'new_html2pdf') != false) {
                    $uri_segments = explode('/', $pathinfo['dirname']);
                    $path1 = array_pop($uri_segments);
                    //}
                    $count = count(explode('/', $parse_url['path']));
                    $dirname = '';
                    for ($i = 0; $i < ($count - 2); $i++) {
                        if ($dirname == '') {
                            $dirname = dirname($parse_url['path']);
                        } else {
                            $dirname = dirname($dirname);
                        }
                    }
                    $filename = $pathinfo['filename'] . '.' . $pathinfo['extension'];
                    $old_filepath = $GLOBALS['fileroot'] . "/.." . $parse_url['path'];
                    if($dirname == '/imwdemo') {
                        $old_filepath = str_replace(array('imwemrdemo'), $r7main_dir, $old_filepath);
                    }
                    if($dirname == '/imwemr') {
                        $old_filepath = str_replace(array('imwemr'), $r7main_dir, $old_filepath);
                    }
                    if($dirname == '../..') {
                        $old_filepath = str_replace(array('../..'), $main_dir.'/interface', $old_filepath);
                    }

                    if($dirname == 'interface/common') {
                        $old_src = str_replace(array('imwdemo'), $r7main_dir_db, $old_src);
                        
                        if(strcasecmp($dirname, $main_dir_db)==0) {
                            $old_src = $old_src;
                            $old_filepath = $GLOBALS['fileroot'] . "/..". $parse_url['path'];
                            $old_filepath = str_replace($r7main_dir_db, $r7main_dir, $old_filepath);
                        } else {
                            $old_src = '/'.$main_dir.'/'.$old_src;
                            $old_filepath = $GLOBALS['fileroot'] . "/..".$main_dir."/" . $parse_url['path'];
                        }
                    }
                    if(strcasecmp($dirname, $main_dir_db)==0) {
                        $old_src = str_replace(array('imwdemo'), $r7main_dir_db, $old_src);
                        if(strcasecmp($dirname, $main_dir_db)==0) {
                            $old_src = $old_src;
                            $old_filepath = $GLOBALS['fileroot'] . "/..". $parse_url['path'];
                            $old_filepath = str_replace($r7main_dir_db, $r7main_dir, $old_filepath);
                        } else {
                            $old_src = '/'.$main_dir.'/'.$old_src;
                            $old_filepath = $GLOBALS['fileroot'] . "/..".$main_dir."/" . $parse_url['path'];
                        }
                    }
                    if(strpos($old_src, "https://production.imedicapps.com/")) {
                        continue;
                    }
                    
                    if ($path1 == 'images' && strpos($old_src, 'redactor/images')) {
                        $new_filepath = $GLOBALS['fileroot'] . "/data/" . constant('PRACTICE_PATH') . "/redactor/images";
                        $new_url = $GLOBALS['webroot'] . "/data/" . constant('PRACTICE_PATH') . "/redactor/images/" . $filename;
                    }
                    if ($path1 == 'document_logos' || $path1 == 'html2pdf') {
                        $new_filepath = $GLOBALS['fileroot'] . "/data/" . constant('PRACTICE_PATH') . "/gn_images";
                        $new_url = $GLOBALS['webroot'] . "/data/" . constant('PRACTICE_PATH') . "/gn_images/" . $filename;
                    }

                    if ($path1 == 'new_html2pdf') {
                        $new_filepath = $GLOBALS['fileroot'] . "/data/" . constant('PRACTICE_PATH') . "/gn_images";
                        $new_url = $GLOBALS['webroot'] . "/data/" . constant('PRACTICE_PATH') . "/gn_images/" . $filename;
                    }
                    if ($path1 == 'sign' || $path1 == 'tmp') {
                        $new_filepath = $GLOBALS['fileroot'] . "/data/" . constant('PRACTICE_PATH') . "/UserId_" . $row['opid'] . "/sign";
                        $new_url = $GLOBALS['webroot'] . "/data/" . constant('PRACTICE_PATH') . "/UserId_" . $row['opid'] . "/sign/" . $filename;
                    }


                    if ($https) {
                        $new_url = str_replace(array('https://', 'http://'), 'https://', $new_url);
                        $old_url = str_replace(array('https://', 'http://'), 'https://', $old_src);
                    } else {
                        $new_url = str_replace(array('https://', 'http://'), 'http://', $new_url);
                        $old_url = str_replace(array('https://', 'http://'), 'http://', $old_src);
                    }
//pre($path1);
//pre($old_src);
//pre($old_filepath);
//pre($new_filepath);
//pre($new_url);
//
//file_put_contents('file_name.txt',($path1.PHP_EOL.$old_src.PHP_EOL.$old_filepath.PHP_EOL.$new_filepath.PHP_EOL.$new_url).PHP_EOL.PHP_EOL,FILE_APPEND);
////file_put_contents('file_name.txt',($path1.PHP_EOL.$old_src.PHP_EOL.$old_filepath).PHP_EOL.PHP_EOL,FILE_APPEND);
//continue;
                    $new_url = str_replace('"', '', $new_url);
//file_put_contents('file_name.txt',($path1.PHP_EOL.$old_src.PHP_EOL.$old_filepath.PHP_EOL.$new_filepath.PHP_EOL.$new_url).PHP_EOL,FILE_APPEND);
                    if (file_exists($old_filepath . '/' . $filename)) {
                        if (!is_dir($new_filepath)) {
                            mkdir($new_filepath, 0777, true);
                        }
                        $content = file_get_contents($old_filepath . '/' . $filename);
                        file_put_contents($new_filepath . '/' . $filename, $content);
                    }


                    if (strtolower($old_filepath) != strtolower($new_filepath) && $new_url != '') {
                        $row['txt_data'] = str_replace($old_src_org, htmlentities($new_url), $row['txt_data']);
                        $updated = true;
                    }
                } 

                if ($updated) {
                    $sql = "UPDATE pn_reports SET txt_data='" . $row['txt_data'] . "' WHERE pn_rep_id='" . $row['pn_rep_id'] . "'";
                    imw_query($sql);
                    $counter++;
                }    
            }
            $sql1 = "update `pn_template` set temp_data = REPLACE(temp_data,'/".strtolower($argv[1])."/interface/main/uploaddir/document_logos/','/".$argv[1]."/data/".$argv[1]."/gn_images/');";
            imw_query($sql1);
            $sql2 = "update `pn_template` set temp_data = REPLACE(temp_data,'/".strtoupper($argv[1])."/interface/main/uploaddir/document_logos/','/".$argv[1]."/data/".$argv[1]."/gn_images/');";
            imw_query($sql2);
            
            $sql3 = "update `pn_template` set temp_data = REPLACE(temp_data,'/".strtolower($argv[1])."/interface/common/new_html2pdf/','/".$argv[1]."/data/".$argv[1]."/gn_images/');";
            imw_query($sql3);
            $sql4 = "update `pn_template` set temp_data = REPLACE(temp_data,'/".strtoupper($argv[1])."/interface/common/new_html2pdf/','/".$argv[1]."/data/".$argv[1]."/gn_images/');";
            imw_query($sql4);
            break;
            
            
        /*    //document
        case 'document':
            return;
            $parms = array();
            $parms['table_name'] = 'document';
            $parms['fields'] = 'id,content';
            $parms['where'] = "";

            $document = fetchImages($parms);
            while ($row = imw_fetch_assoc($document)) {
                $doc = new DOMDocument();
                $doc->loadHTML(stripslashes(html_entity_decode($row['content'])));
                $imgs = $doc->getElementsByTagName('img');

                if ($imgs->length == 0) {
                    //echo '<font face="Arial, Helvetica, sans-serif" size="2" color="red"> No image found for ' . $row['id'] . '</font> <br />';
                    continue;
                }
                $updated = false;
                foreach ($imgs as $img) {
                    $new_url = '';
                    $new_filepath = '';
                    $old_src_org = ($img->getAttribute('src'));
                    $old_src = ($img->getAttribute('src'));

                    $pathinfo = pathinfo($old_src);
                    $parse_url = parse_url($pathinfo['dirname']);

                    $uri_segments = explode('/', $pathinfo['dirname']);
                    $path1 = array_pop($uri_segments);

                    $count = count(explode('/', $parse_url['path']));
                    $dirname = '';
                    for ($i = 0; $i < ($count - 2); $i++) {
                        if ($dirname == '') {
                            $dirname = dirname($parse_url['path']);
                        } else {
                            $dirname = dirname($dirname);
                        }
                    }

                    $filename = $pathinfo['filename'] . '.' . $pathinfo['extension'];
                    $old_filepath = $GLOBALS['fileroot'] . "/.." . $parse_url['path'];
                    if(strpos($old_filepath, $r7main_dir_db) != false) {
                        $old_filepath = str_replace($r7main_dir_db, $r7main_dir, $old_filepath);
                    }
                    
                    if ($path1 == 'document_logos' || $path1 == 'new_html2pdf') {
                        $new_filepath = $GLOBALS['fileroot'] . "/data/" . constant('PRACTICE_PATH') . "/gn_images";
                        $new_url = $GLOBALS['webroot'] . "/data/" . constant('PRACTICE_PATH') . "/gn_images/" . $filename;
                    }               
                    
                    if ($path1 == 'gn_images') {
                        continue;
                    }
                    
                    if ($https) {
                        $new_url = str_replace(array('https://', 'http://'), 'https://', $new_url);
                        $old_url = str_replace(array('https://', 'http://'), 'https://', $old_src);
                    } else {
                        $new_url = str_replace(array('https://', 'http://'), 'http://', $new_url);
                        $old_url = str_replace(array('https://', 'http://'), 'http://', $old_src);
                    }

                    if (file_exists($old_filepath . '/' . $filename)) {
                        if (!is_dir($new_filepath)) {
                            mkdir($new_filepath, 0777, true);
                        }
                        $content = file_get_contents($old_filepath . '/' . $filename);
                        file_put_contents($new_filepath . '/' . $filename, $content);
                    }
                    if (strtolower($old_filepath) != strtolower($new_filepath) && $new_url != '') {
                        $row['content'] = str_replace($old_src_org, "" . htmlentities($new_url) . "", $row['content']);
                        $updated = true;
                    }
                }


                if ($updated) {
                    $sql = "UPDATE document SET content='" . addslashes($row['content']) . "' WHERE id={$row['id']}";
                    imw_query($sql) or $msg_info[] = imw_error();

                    $counter++;
                }
            }
            break;
        */

        /* case 'editor':
            $src = $src_path . "redactor/images/";
            $dst = $upload_path . "redactor/images/";

            $files = glob($src . "*.*");
            foreach ($files as $file) {
                if (!is_dir($dst)) {
                    mkdir($dst, 0777, true);
                }
                $file_to_go = str_replace($src, $dst, $file);
                @copy($file, $file_to_go);

                $counter++;
            }
            break;
        */
/*
        case 'update_paths_in_editor':
            return;
            $arr = array();
            $arr['hippa_setting'] = 'hippa_setting,id,loginLegalNotice';
            $arr['consent_form'] = 'consent_form,consent_form_id,consent_form_content';
            $arr['collection_letter_template'] = 'collection_letter_template,id,collection_data';
            $arr['consulttemplate'] = 'consulttemplate,consultLeter_id,consultTemplateData';
            $arr['document'] = 'document,id,content';
            $arr['pn_template'] = 'pn_template,temp_id,temp_data';
            $arr['recalltemplate'] = 'recalltemplate,recallLeter_id,recallTemplateData';
            $arr['pt_docs_template'] = 'pt_docs_template,pt_docs_template_id,pt_docs_template_content';
            $arr['statement_template'] = 'statement_template,id,statement_data';

            $arr['document_panels1'] = 'document_panels,id,leftpanel';
            $arr['document_panels2'] = 'document_panels,id,header';
            $arr['document_panels3'] = 'document_panels,id,footer';

            $arr['prescription_template'] = 'prescription_template,id,prescription_template_content';
            $arr['surgery_center_consent_forms_template'] = 'surgery_center_consent_forms_template,consent_id,consent_data';
            $arr['order_template'] = 'order_template, template_id,template_content';
            $arr['iportal_autoresponder_templates'] = 'iportal_autoresponder_templates,id,data';

            $arr['patient_consult_letter_tbl'] = 'patient_consult_letter_tbl,patient_consult_id,templateData';

            foreach ($arr as $key => $value) {
                $value_arr = explode(',', $value);

                $data_arr = array();
                $data_arr['table'] = $value_arr[0];
                $data_arr['id'] = $value_arr[1];
                $data_arr['column'] = $value_arr[2];

                $parms = array();
                $parms['table_name'] = $data_arr['table'];
                $parms['fields'] = $data_arr['id'] . ',' . $data_arr['column'];
                $parms['where'] = "";

                $data = fetchImages($parms);
                //echo "Table Name    => " . $parms['table_name'] . ", Updated Column     => " . $data_arr['column'] . "<br />";
                while ($row = imw_fetch_assoc($data)) {
                    //foreach ($data as $row) {

                    $doc = new DOMDocument();
                    $doc->loadHTML(html_entity_decode($row[$data_arr['column']]));
                    $imgs = $doc->getElementsByTagName('img');
                    if ($imgs->length == 0) {
                        //echo '<font face="Arial, Helvetica, sans-serif" size="2" color="red"> No image found for ' . $row['id'] . '</font> <br />';
                        continue;
                    }

                    $updated = false;
                    foreach ($imgs as $img) {
                        $new_url = '';
                        $old_src = $img->getAttribute('src');

                        $pathinfo = pathinfo($old_src);
                        $parse_url = parse_url($pathinfo['dirname']);

                        $count = count(explode('/', $parse_url['path']));
                        $dirname = '';
                        for ($i = 0; $i < ($count - 2); $i++) {
                            if ($dirname == '') {
                                $dirname = dirname($parse_url['path']);
                            } else {
                                $dirname = dirname($dirname);
                            }
                        }
                        $filename = $pathinfo['filename'] . '.' . $pathinfo['extension'];
                        $old_filepath = $GLOBALS['fileroot'] . "/.." . $parse_url['path'];
                        if (strtolower($main_dir) === strtolower($dirname)) {
                            $new_filepath = str_replace($dirname, $GLOBALS['webroot'] . "/data/" . constant('PRACTICE_PATH'), $old_filepath);
                        } else {
                            $new_filepath = $old_filepath;
                        }
                        $new_url = str_replace($dirname, $GLOBALS['webroot'] . "/data/" . constant('PRACTICE_PATH'), $old_src);
                        if ($https) {
                            $new_url = str_replace(array('https://', 'http://'), 'https://', $new_url);
                            $old_url = str_replace(array('https://', 'http://'), 'https://', $old_src);
                        } else {
                            $new_url = str_replace(array('https://', 'http://'), 'http://', $new_url);
                            $old_url = str_replace(array('https://', 'http://'), 'http://', $old_src);
                        }

                        if (file_exists($old_filepath . '/' . $filename)) {
                            if (!is_dir($new_filepath)) {
                                mkdir($new_filepath, 0777, true);
                            }
                            $content = file_get_contents($old_filepath . '/' . $filename);
                            file_put_contents($new_filepath . '/' . $filename, $content);
                        }

                        //if (strpos($old_url, $main_dir) != false) {
                        if (strtolower($old_filepath) !== strtolower($new_filepath)) {
                            $img->setAttribute('src', $new_url);
                            $updated = true;
                        }
                    }

                    if ($updated) {
                        $result = $doc->saveHTML();

                        $sql = "UPDATE {$data_arr['table']} SET {$data_arr['column']}='" . $result . "' WHERE {$data_arr['id']}={$row[$data_arr['id']]}";
                        imw_query($sql) or $msg_info[] = imw_error();

                        $counter++;
                    }
                }
            }
            break; */
    }
}


if ($counter == 0) {
    $msg_info[] = "<b>No Image found for {$_GET['fun']}!</b>";
    $color = "red";
} else {
    $msg_info[] = "<b>{$counter} Image" . ($counter == 1 ? '' : 's') . " Copied for {$_GET['fun']} successfully.</b>";
    $color = "green";
}

if ($counter == 0 && isset($_GET['fun']) && $_GET['fun'] == 'update_paths_in_editor') {
    $msg_info = array();
    $msg_info[] = "<b>There is nothing to update for {$_GET['fun']}!</b>";
    $color = "red";
}
?>
<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>", $msg_info)); ?></font>