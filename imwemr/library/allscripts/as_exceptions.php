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

class asException extends Exception
{
	/*Error Type*/
	protected $type;
	public $date;
	
	public function __construct($type, $message, $code = 0, Exception $previous = null)
	{
		$this->type = $type;
		$this->date = date( 'mdYHis' );
		
		parent::__construct($message, $code, $previous);
	}
	
	public function showMessage()
	{
		$response = array();
		$response['type'] = $this->type;
		$response['message'] = "Error: ".($this->getMessage()).'<br />Reference No.: '.$this->date.'<br />Please Contact Support.';
		return $response;
	}
	
	public function getErrorText()
	{
		return $this->message;
	}
	
	public function getErrorType()
	{
		return $this->type;
	}
}

/* error handler function */
function asErrorHandler($errno, $errstr, $errfile, $errline)
{
    if (!(error_reporting() & $errno)) {
        // This error code is not included in error_reporting
        return;
    }
	try{
		throw new asException('Error', '['.$errno.'] '.$errstr);
	}
	catch( asException $e)
	{
		$response = $e->showMessage();
		
		/*Log Error text to file*/
		$errData = $e->date."\n".($e->getErrorText())."\n".$e->getTraceAsString()."\n";
		$writePath = data_path().'as_data/';
		if(!is_dir($writePath)){
			mkdir( $writePath, 0755, true );
			chown( $writePath, 'apache' );
		}
		file_put_contents( $writePath.'error.txt', $errData, FILE_APPEND );
		
		if( isset($GLOBALS['rethrow']) && $GLOBALS['rethrow'] )
			echo json_encode( $response ) . PHP_EOL ;
		else
			throw $e;
	}
	
    /* Don't execute PHP internal error handler */
    return true;
}
//set_error_handler("asErrorHandler");
//restore_error_handler();