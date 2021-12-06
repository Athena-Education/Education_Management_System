<?php
require APPPATH . '/libraries/TokenHandler.php';
defined('BASEPATH') OR exit('No direct script access allowed');
class Jwt_model extends CI_Model {
	// constructor
	function __construct()
	{
		$this->tokenHandler = new TokenHandler();
		parent::__construct();
	}

	public function token_data_get($auth_token)
	 {
	    //$received_Token = $this->input->request_headers('Authorization');
	    if (isset($auth_token)) {
	      try
	      {

	        $jwtData = $this->tokenHandler->DecodeToken($auth_token);
	        return json_encode($jwtData);
	      }
	      catch (Exception $e)
	      {
	        echo 'catch';
	        http_response_code('401');
	        echo json_encode(array( "status" => false, "message" => $e->getMessage()));
	        exit;
	        
	      }
	    }else{
	      echo json_encode(array( "status" => false, "message" => "Invalid Token"));
	    }
	  }
}