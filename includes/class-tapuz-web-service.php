<?php
/**
 * Class Tapuz_web_service
 * This is used to communicate with Tapuz API
 */

class Tapuz_web_service {

	private $tapuz_login = array();

	public function __construct() {
		$this->tapuz_login['url'] = get_option( 'tapuz_service_url', TAPUZ_DEFAULT_URL );
		$this->tapuz_login['username'] = get_option('tapuz_username');
		$this->tapuz_login['password'] = get_option('tapuz_password');
		$this->tapuz_login['code'] = get_option('tapuz_customer_code');
		$this->tapuz_login['collect_street'] = get_option('tapuz_collect_street_name');
		$this->tapuz_login['collect_street_number'] = get_option('tapuz_collect_street_number');
		$this->tapuz_login['collect_city'] = get_option('tapuz_collect_city_name');
		$this->tapuz_login['collect_company'] = get_option('tapuz_collect_company_name');
	}

	/**
	 * Open CURL with Tapuz API
	 * @param $data
	 * @param $tapuz_func
	 *
	 * @return mixed
	 * @throws Exception
	 */
	private function tapuz_connection( $data, $tapuz_func ) {
		$url = $this->tapuz_login['url'].$tapuz_func;
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url );
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($curl);
		if($response === false) {
			throw new Exception('Communication error');
		}
		curl_close($curl);
		return $response;

	}

	/**
	 * Convert response from XML to JSON
	 *
	 * @param $tapuz_response
	 *
	 * @return array
	 */
	private function tapuz_xml_json( $tapuz_response ) {
		$tapuz_xml = html_entity_decode($tapuz_response);
		$xml = new SimpleXMLElement($tapuz_xml);
		return (array)$xml ;
	}

	/**
	 * Get Tapuz Ship Status
	 * @param $tapuz_id
	 *
	 * @return array
	 */
	public function get_ship_status( $tapuz_id ) {
		$data = array();
		$data['customerId'] = $this->tapuz_login['code'];
		$data['deliveryNumbers'] = $tapuz_id;
		try {
			$response = $this->tapuz_connection( $data, TAPUZ_GET_BY_ID );
			return $this->tapuz_xml_json( $response );
		} catch ( Exception $e ) {
			print_r( $e->getMessage() );
			die();
		}
	}

	/**
	 * Open Tapuz ship
	 * @param $ship_data
	 *
	 * @return array
	 */
	public function create_ship( $ship_data ) {
		$ship_data['tapuz_static'] = '0';
		$ship_data['tapuz_empty'] = '';
		$pParam ='1;'
         .$this->tapuz_login['collect_street'].';'
	     .$this->tapuz_login['collect_street_number'].';'
	     .$this->tapuz_login['collect_city'].';'
	     .$ship_data['street'].';'
	     .$ship_data['number'].';'
	     .$ship_data['city'].';'
	     .$this->tapuz_login['collect_company'].';'
	     .$ship_data['company'].';'
	     .$ship_data['note'].';'
	     .$ship_data['urgent'].';'
	     .$ship_data['tapuz_static'].';'
	     .$ship_data['motor'].';'
	     .$ship_data['packages'].';'
	     .$ship_data['return'].';'
	     .$ship_data['tapuz_static'].';'
	     .$ship_data['woo_id'].';'
	     .$this->tapuz_login['code'].';'
	     .$ship_data['tapuz_static'].';'
	     .$ship_data['extra_note'].';'
	     .$ship_data['tapuz_static'].';'
	     .$ship_data['tapuz_empty'].';'
	     .$ship_data['tapuz_empty'].';'
	     .$ship_data['contact_name'].';'
	     .$ship_data['contact_phone'].';'
	     .$ship_data['contact_mail'].';'
	     .$ship_data['exaction_date'].';'
	     .$ship_data['collect'];
		$data_send = array();
		$data_send['pParam'] = $pParam;
		try {
			$tapuz_response = $this->tapuz_connection( $data_send, TAPUZ_SAVE_NEW );
			return $this->tapuz_xml_json( $tapuz_response );
		} catch ( Exception $e ) {
			print_r( $e->getMessage() );
			die();
		}
	}
	
	public function change_ship_status( $tapuz_id ) {
		$data = array();
		$data['p1'] = $this->tapuz_login['code'];
		$data['p2'] = $tapuz_id;
		$data['p3'] = '8';
		try {
			$response = $this->tapuz_connection( $data, TAPUZ_CHANGE_STATUS );
			return $this->tapuz_xml_json( $response );
		} catch ( Exception $e ) {
			print_r( $e->getMessage() );
			die();
		}
	}
}


