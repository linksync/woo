<?php if ( ! defined( 'ABSPATH' ) ) exit;  


/**
 * Main Api Request Class 
 * @author 		linksync
 */

class LS_Api{
	
	/**
	 * @var Api configuration
	 */
	protected $config;

	/**
	* @var Linksync API key
	*/
	protected $laid;

	/**
	 * @var Api Url
	 */
	protected $url;

	/**
	 * @var The api's Response
	 */
	public $response;


	/**
	* @param config array
	* @param $laid The Linksync API key(LAID)
	**/
	function __construct($config, $laid){

		$this->config = $config;
		$this->laid   = $laid;

		$this->url    = $config['url'];

	}

	/**
	 * Api Post Request
	 *
	 * @param $endpoint string
	 * @param $data array
	 */
	public function post($endpoint,$data){
		return $this->request($endpoint,'POST',$data);
	}


	/**
	* Api Get Request
	* @param $endpoint string
	*/

	public function get($endpoint){
		return $this->request($endpoint,'GET');
	}

	/**
	* Api Delete Request
	* @param $endpoint
	*/
	public function delete($endpoint){
		return $this->request($endpoint,'DELETE');
	}


	/**
	 * Api Call using curl(http://php.net/manual/en/function.curl-setopt.php)
	 * @param $endpoint string 
	 * @param $http_method string
	 * @param $data array
	 * 
	 */
	public function request($endpoint, $http_method, $data = null ){

		$curl  = curl_init($this->url.$endpoint);

		/**
		 *	FALSE to stop cURL from verifying the peer's certificate. 
		 */
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

		switch ($http_method) {
			
			/**
			* Set up post request
			*/
			case 'POST':
				curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
                curl_setopt($curl, CURLOPT_POST, true);
                
                if(!empty($data))
                	curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
				break;
			
			/**
			 * Set up PUT request
			 */
			case "PUT":
                curl_setopt($curl, CURLOPT_PUT, 1);
                break;
            /**
            * Set up DELETE request
            */
            case "DELETE":
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "DELETE");
                break;
		}

		//TODO Set up curl properly
		curl_setopt($curl, CURLOPT_TIMEOUT, 100);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            "Content-Type:application/json",
            "LAID: " . $this->laid
        ));

        if (curl_error($curl)) {
            $error = "Connection Error: " . curl_errno($curl) . ' - ' . curl_error($curl);
            return array(
                'errorCode' => 007,
                'userMessage' => $error
            );
        }

        $this->response = json_decode(curl_exec($curl),true);
        return $this->get_response();

	}

	/**
	 * Returns the api's Response
	 * @return array()
	 */
	public function get_response(){
		return $this->response;
	}


}