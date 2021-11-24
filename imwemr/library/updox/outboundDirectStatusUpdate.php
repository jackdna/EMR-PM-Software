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
$listenerCount = 0;
if( $argv[1] )
{
	$practicePath = trim($argv[1]);
	$_SERVER['REQUEST_URI'] = $practicePath;
	$_SERVER['HTTP_HOST']= $practicePath;
}

require_once(dirname(__FILE__)."/../../config/globals.php");
include($GLOBALS['srcdir'].'/updox/updoxDirect.php');

try
{
    /**
     * Initialize iMW updox direct class object
     */
    $updox = new updoxDirect();


    /*Allow Request only from updox*/
    if($updox->validateInboundIP()){

        if( $argv[1] && isset($argv[2]) && !empty($argv[2]) )
        {
            $dataFile = data_path().'updoxPendingStatusUpdate/'.$argv[2];
            $data = file_get_contents($dataFile);
        }
    }

    if( !isset($data) || empty($data) )
    {  
        exit('Data Not found');
    }

    /*containers to hold updox accound id and userId posted in call from webhook*/
    $userId = '';
    $messageId = '';
    if( $data && $data !== '' ) {
        $data = json_decode($data);
        $userId = trim($data->userId);
        $messageId = get_number($data->messageId);
    }
    else
    {
        exit('Required data not found');
    }

    /** List of Direc messages pending to be downloaded from Updox */
    $directStatuses = $updox->listMdnStatuses( $messageId, $userId );

    if( 
        $directStatuses['data']->successful === false
    )
    {
        if(
            in_array($directStatuses['data']->responseCode, [4430, 4410])
        )
        {
            unlink($dataFile);
        }
        exit('Message Status update failed');
    }

    $statusList = $directStatuses['data']->statuses;

    /**
     * Process Status Updates
     */
    foreach( $statusList as $status )
    {
        /**
         * Check if Status record already exists
         */
        $sql = "SELECT `log_id` FROM `direct_messages_log` WHERE `status`='".imw_real_escape_string($status->mdnStatus)."' AND `updox_message_id`=".$messageId;
        
        $resp = imw_query($sql);

        if( $resp && imw_num_rows($resp) == 0 )
        {
            $sql = "INSERT INTO `direct_messages_log` SET `updox_message_id`='".$messageId."', `status`='".$status->mdnStatus."', entered_date_time='".date('Y-m-d H:i:s')."'";
            imw_query($sql);
            
            if( 
                in_array($status->mdnStatus, ['failed', 'dispatched'])
            )
            {
                unlink($dataFile);
            }
        }

    }

    /**
     * Return with HTTP success response on succcessful execution
     */
    header("HTTP/1.1 200");
}
catch( Exception $e )
{
    /*Set response code to service unavailable. So, that Updox web hook can retry to push the fax data - if any error occoured.*/
    // http_response_code(503);
    header("HTTP/1.1 503");
}
