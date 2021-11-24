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


    /** List of Direc messages pending to be downloaded from Updox */
    $pendingDirect = $updox->listPendingInboundDirect();


    /**
     * Push the pending direct messages to the Direct download Queue.
     * The queue is basically the waiting are.
     * The messages from here will be downloaed by separate script in serial order.
     * It is implemented basically to manage the load and improve fault tolerance.
     */

    /**
     * Create the "updoxPendingInboundDirect" data directory, if it does not exists already
     */
    $holdingDir = data_path().'updoxPendingInboundDirect';
    if( !is_dir($holdingDir) )
    {
        mkdir( $holdingDir, 0700, true );
        chown( $holdingDir, 'apache' );
    }

    /**
     * Iterate through the list of Updox Users configured for the practice
     */
    if( array_key_exists('data', $pendingDirect) )
    {
        $pendingDirect = $pendingDirect['data'];

        foreach( $pendingDirect->fetchList as $userDirect )
        {
            /**
             * Iterate through the list of Pneding messages fot the user
             * Push them to the holding area
             */
            if( $userDirect->messageCount > 0 )
            {
                $userId = $userDirect->userId;

                foreach( $userDirect->messageList as $directId )
                {
                    $directDlData = [
                        'accountId' => $updox->accountId(),
                        'userId' => $userDirect->userId,
                        'messageId' => $directId
                    ];
                    $directDlData = json_encode($directDlData);

                    file_put_contents($holdingDir.'/'.$directId.'.json', $directDlData);
                }
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
