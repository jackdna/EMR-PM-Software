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

require_once(dirname(__FILE__) . "/../../library/uga_finance/uga_base.php");

class UGA extends uga_base
{
    public function __construct()
	{
        parent::__construct();
    }

    /**
     * Get Customer
     * @param: customer id
     */
    public function getCustomer($customerId)
    {
        if(empty($customerId) || $customerId == '')
            throw new Exception('Customer id is required');
        
        $endpoint = 'customers/'.$customerId;
        $result = $this->CURL($endpoint, 'GET');
        return $result;
    }

    /**
     * Credit Application
     */
    public function creditApplication($postArray = array())
    {
        if(empty($postArray))
            throw new Exception('Customer data not valid');

        $result = $this->CURL('creditApplication', 'POST', $postArray);
        return $result;
    }
   
}

