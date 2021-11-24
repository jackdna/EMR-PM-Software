<?php

$ignoreAuth = true;
include("../config/globals.php");
set_time_limit(0);
//error_reporting(-1);
$src = "../config/";
$file_src = "../data/";
$sql_src = "../sql_changes/r8/v1/";

//Get all existing practices on server.
$filecount = glob($src . "*");
$practice_names = array();
foreach ($filecount as $file) {
    if (is_dir($file)) {
        $file_info = pathinfo($file);
        if (!isset($file_info['extension']) && strpos($file_info['filename'], 'htaccess') === false) {
            $practice_names[] = $file_info['filename'];
        }
    }
}

//Get all directoy containing sql update script
$sql_update_dir = glob($sql_src . "*");
$dir_name = array();
foreach ($sql_update_dir as $dir) {
    $dir_info = pathinfo($dir);
    $dir_name[] = $dir_info['filename'];
}

ob_start();
$temp_errors = array();
$temp_practice = array();
$old_explode_hash = array();

$pcounter = 0;
foreach ($practice_names as $pname) {
    //get content for file strtolower($pname).'_sql.log'
    $explode_hash = array();
    if (file_exists($file_src . $pname . '/' . strtolower($pname) . '_sql.log')) {
        $file_content = file_get_contents($file_src . $pname . '/' . strtolower($pname) . '_sql.log');
        if ($file_content) {
            $explode_hash = explode("||", $file_content);
            $old_explode_hash[$pname] = $explode_hash;
        }
    }

    unset($GLOBALS['dbh']);
    if (file_exists($webserver_root . "/config/" . $pname . '/config_' . strtolower($pname) . '.php')) {
        include($webserver_root . "/config/" . $pname . '/config_' . strtolower($pname) . '.php');
    } else {
        $temp_errors[$pname] = "Config file not found for $pname. <br /><br />";
        $pcounter++;
        continue;
    }

    //echo $pname . "<br />";
    //echo "==================<br />";
    //$db_name = constant('IMEDIC_IDOC');
    if (!isset($sqlconf['idoc_db_name']) && $sqlconf['idoc_db_name'] == '') {
        $temp_errors[$pname] = "Please set variable sqlconf['idoc_db_name'] in config file.<br /><br />";
        $pcounter++;
        continue;
    }

    $idoc_db_name = $sqlconf['idoc_db_name'];
    $sc_db_name = $sqlconf['sc_db_name'];
    $scan_db_name = $sqlconf['scan_db_name'];

    $database_name = array();
    if (isset($GLOBALS['dbh']) == false) {
        $conLink = mysqli_connect($sqlconf["host"], $sqlconf["login"], $sqlconf["pass"], $idoc_db_name);
        $GLOBALS['dbh'] = $conLink;
        if ($conLink) {
            $result = $conLink->query("SELECT DATABASE()");
            $database_name = $result->fetch_row();
            $result->close();
        }
    }

    if (empty($database_name)) {
        imw_close($GLOBALS['dbh']);
        $temp_errors[$pname] = "No database selected for $pname.<br /><br />";
        $pcounter++;
        continue;
    }

    $temp_dir = array();
    $counter = 0;
    foreach ($dir_name as $dir) {
        if (is_dir($sql_src . $dir)) {
            $files = scandir($sql_src . $dir);
            natsort($files);
            
            $temp_files = array();
            foreach ($files as $key => $file) {
                if (!in_array($file, array(".", "..")) && strpos($file, 'update_') !== false) {
                    $filemodifieddatetime = filemtime($sql_src . $dir . '/' . $file);
                    $filehash = $pname . '~~' . $dir . '~~' . $file . '~~' . $filemodifieddatetime;

                    if (empty($explode_hash) || !in_array($filehash, $explode_hash)) {
                        $q = array();
                        $sql = array();
                        $qry = array();
                        if (strpos(file_get_contents($sql_src . $dir . '/' . $file), 'skipthisfile') !== false) {
                            continue;
                        }
                        exec(execution_wait($sql_src . $dir . '/' . $file, $sqlconf));
                        if (!is_dir($file_src . $pname)) {
                            mkdir($file_src . $pname, 0777, true);
                        }
                        
                        file_put_contents($file_src . $pname . '/' . strtolower($pname) . '_sql.log', ($pname . '~~' . $dir . '~~' . $file . '~~' . $filemodifieddatetime . "||"), FILE_APPEND);
                        ob_clean();
                    }
                }
            }
            
        }
        $counter++;
    }

    imw_close($GLOBALS['dbh']);
    
}

    foreach ($practice_names as $pname) {
        
    $explode_hash = array();
    if (file_exists($file_src . $pname . '/' . strtolower($pname) . '_sql.log')) {
        $file_content = file_get_contents($file_src . $pname . '/' . strtolower($pname) . '_sql.log');
        if ($file_content) {
            $explode_hash = explode("||", $file_content);
                }
            }

    echo "<div style='background-color:green;padding:5px;color:#fff;margin:15px 0px 15px 0px;font-weight:bold;'>" . $pname . "</div>";

    if (!isset($old_explode_hash[$pname])) {
        $old_explode_hash[$pname] = array();
        }
    $diff_result = array_diff($explode_hash, $old_explode_hash[$pname]);
        
    if (!empty($diff_result)) {
        $arr_dir=array();
        foreach ($diff_result as $result) {
            $result_arr = explode('~~', $result);
            if ($result_arr[0] == $pname) {
                if (is_dir($sql_src . $result_arr[1])) {
                    if (!in_array($pname . '#' . $result_arr[1], $arr_dir)) {
                        $arr_dir[] = $pname . '#' . $result_arr[1];
                        echo "\t" . $result_arr[1] . "<br />";
                    }
        foreach ($dir_name as $dir) {
                        if ($result_arr[1] == $dir) {
                            echo "\tFile Executed : " . $result_arr[2] . "<br />";
            }
                }
            }
        }
        }
    }
        
    if (!empty($temp_errors)) {
        foreach ($temp_errors as $keypname => $error) {
            if ($pname == $keypname) {
                echo "<font color='red'>$error</font>";
        }
    }
}

    if (empty($diff_result)) {
        echo "There is no update available.";
    }
}

function execution_wait($file, $sqlconf) {
    if (strpos(file_get_contents($file), 'require_once') !== false) {
        error_reporting(-1);
        include($file);
    } else {
        error_reporting(-1);
        include($file);
    }
}

?>
