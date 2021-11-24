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

$ignoreAuth = true;

/*Set Practice Name - for dynamically including config file*/
if( $argv[1] )
{
	$practicePath = trim($argv[1]);
	$_SERVER['REQUEST_URI'] = $practicePath;
	$_SERVER['HTTP_HOST']= $practicePath;
}
else
{
    die('Please provide practice name'.PHP_EOL);
}

$currentDatePlain = date('Ymd');
$currentDate = date('Y-m-d');

include_once(dirname(__FILE__)."/../../config/globals.php");

/**
 * Create directory to hold the exported ccda files for the day
 */
$verasityDataDirectory = data_path().'verasity';

if( !is_dir($verasityDataDirectory) )
{
    mkdir( $verasityDataDirectory, 0755, true );
    chown( $verasityDataDirectory, 'apache' );
}

/**
 * Archieve data directory
 */
$verasityArchieveDirectory = $verasityDataDirectory.'/archieve';

if( !is_dir($verasityArchieveDirectory) )
{
    mkdir( $verasityArchieveDirectory, 0755, true );
    chown( $verasityArchieveDirectory, 'apache' );
}


/**
 * Get Remote Sftp Credentials
 */
$sql = "SELECT
            `ccda_host_name`, `port_number`, `ccda_sftp_username`, `ccda_sftp_password`, `ccda_directory_path`
        FROM `ccda_sftp_credentials`
        WHERE `ccda_host_name` != '' AND  `port_number` != '' AND  `ccda_sftp_username` != '' AND
                `ccda_sftp_password` != '' AND  `ccda_directory_path` != ''";
$resp = imw_query($sql);

$sftpCreds = [];
if( $resp && imw_num_rows($resp) > 0)
{
    $resp = imw_fetch_assoc($resp);

    $sftpCreds['host'] = trim($resp['ccda_host_name']);
    $sftpCreds['port'] = trim($resp['port_number']);
    $sftpCreds['user'] = trim($resp['ccda_sftp_username']);
    $sftpCreds['pass'] = $resp['ccda_sftp_password'];
    $sftpCreds['path'] = trim($resp['ccda_directory_path']);
}
else
{
    exit('Sftp credential to upload the ccda files does not exists!'.PHP_EOL);
}


/** Query data containers */
$_REQUEST = [];
$_REQUEST['arrData'] = [];

/**
 * List Chart notes finalized on the day
 */
$sql = "SELECT 
            `id`, `patient_id`
        FROM `chart_master_table`
        WHERE 
            `finalize` = 1 AND 
            `delete_status` = 0 AND 
            `purge_status` = 0 AND 
            DATE(`finalizeDate`) = '".$currentDate."'";

$resp = imw_query($sql);

if( $resp && imw_num_rows($resp) > 0 )
{
    while( $row = imw_fetch_assoc($resp) )
    {
        array_push(
            $_REQUEST['arrData'],
            [
                'pat_id' => $row['patient_id'],
                'form_id' => $row['id']
            ]
        );
    }
}

if( count($_REQUEST['arrData']) < 1 )
{
    exit('Records not found for export.'.PHP_EOL);
}

include_once($GLOBALS["fileroot"]."/library/classes/ccda_functions.php");
include_once($GLOBALS["fileroot"]."/library/classes/work_view/wv_funtions.php");
include_once($GLOBALS["fileroot"]."/library/classes/work_view/wv_functions_new.php");
include_once($GLOBALS["fileroot"]."/library/classes/work_view/ChartAP.php");

/**
 * Stop output buffering for debugging
 */
ob_start();

include(dirname(__FILE__).'/create_ccda.php');

ob_end_clean();

print('Data export done'.PHP_EOL);


/**
 * Upload the files to remote Location
 */

/** List files */
$files = glob( $verasityDataDirectory."/*.xml" );

if( count($files) > 0 )
{
    $connection = ssh2_connect( $sftpCreds['host'], $sftpCreds['port'] );
    ssh2_auth_password($connection, $sftpCreds['user'], $sftpCreds['pass']);
    
    $sftp = ssh2_sftp($connection);
    $sftp_fd = intval($sftp);

    $file_root = "ssh2.sftp://$sftp_fd/".$sftpCreds['path'];
    

    foreach( $files as $file )
    {
        if( file_exists($file) )
        {
            $fileName = basename($file);
            $data = file_get_contents($file);

            $success = file_put_contents($file_root."/".$fileName, $data);
            
            if($success !== false)
            {
                rename($file, $verasityArchieveDirectory.'/'.$fileName);//move that file to archieve folder
            }
        }
    }
}
else
{
    exit('Files not found to be uploaded!'.PHP_EOL);
}
