<?php
require APPPATH . '/libraries/TokenHandler.php';
//include Rest Controller library
require APPPATH . 'libraries/REST_Controller.php';

class Api_instructor extends REST_Controller {

  protected $token;
  public function __construct()
  {
    parent::__construct();
    $this->load->database();
    $this->load->model('api_instructor_model');
    // creating object of TokenHandler class at first
    $this->tokenHandler = new TokenHandler();
    header('Content-Type: application/json');
  }
  public function token_data_get($auth_token)
  {
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

  public function login_post() {
    $userdata = $this->api_instructor_model->login_post();
    if ($userdata['validity'] == 1) {      
      $userdata['token'] = $this->tokenHandler->GenerateToken($userdata);
    }
    return $this->set_response($userdata, REST_Controller::HTTP_OK);
  }

  public function change_password_post(){
    $response = array();
    if (isset($_POST['auth_token']) && !empty($_POST['auth_token']) && !empty($_POST['current_password']) && !empty($_POST['new_password']) && !empty($_POST['confirm_password'])) {
      $auth_token = $_POST['auth_token'];
      $logged_in_user_details = json_decode($this->token_data_get($auth_token), true);
      if ($logged_in_user_details['user_id'] > 0) {
        $response = $this->api_instructor_model->change_password_post($logged_in_user_details['user_id']);
      }
    }else{
      $response['message'] = get_phrase('access_denied');
      $response['status'] = 403;
      $response['validity'] = false;
    }
    return $this->set_response($response, REST_Controller::HTTP_OK);
  }



  public function forgot_password_post(){
    $response = array();
    if(isset($_POST['email']) && !empty($_POST['email'])){
      $new_password = rand(10000, 100000);
      $this->email_model->password_reset_email($new_password, $_POST['email']);
      $this->api_instructor_model->forgot_password_post($new_password);

      $response['message'] = get_phrase('new_password_successfully_has_been_send_to_your_inbox');
      $response['status'] = 200;
      $response['validity'] = true;
    }else{
      $response['message'] = get_phrase('access_denied');
      $response['status'] = 403;
      $response['validity'] = false;
    }
    return $this->set_response($response, REST_Controller::HTTP_OK);
  }


  public function change_profile_photo_post(){
    $response = array();
    if(isset($_POST['auth_token']) && !empty($_POST['auth_token'])){
      $user_info = json_decode($this->token_data_get($_POST['auth_token']), true);
        $response = $this->api_instructor_model->change_profile_photo_post($user_info['user_id']);
      }else{
        $response['message'] = get_phrase('access_denied');
      $response['status'] = 403;
      $response['validity'] = false;
    }
    return $this->set_response($response, REST_Controller::HTTP_OK);
  }

  public function userdata_get(){
    $userdata = array();    
      if (isset($_GET['auth_token']) && !empty($_GET['auth_token'])) {
        $auth_token = $_GET['auth_token'];
        $user_info = json_decode($this->token_data_get($auth_token), true);
        $response = $this->api_instructor_model->userdata_get($user_info['user_id']);
      }else{
        $response['message'] = get_phrase('access_denied');
        $response['status'] = 403;
        $response['validity'] = false;
      }
      return $this->set_response($response, REST_Controller::HTTP_OK);
  }

  public function update_userdata_post(){
    $response = array();
      if (isset($_POST['auth_token']) && !empty($_POST['auth_token'])) {
      $user_info = json_decode($this->token_data_get($_POST['auth_token']), true);
      $response = $this->api_instructor_model->update_userdata_post($user_info['user_id']);
      }else{
      $response['message'] = get_phrase('access_denied');
      $response['status'] = 403;
      $response['validity'] = false;
      }
      return $this->set_response($response, REST_Controller::HTTP_OK);
  }


  public function courses_get() {
    $response = array();

    if(isset($_GET['auth_token']) && !empty($_GET['auth_token'])){
      $auth_token = $_GET['auth_token'];
      $user_details = json_decode($this->token_data_get($auth_token), true);
      $response = $this->api_instructor_model->courses_get($user_details['user_id']);
      return $this->set_response($response, REST_Controller::HTTP_OK);
    }
  }


  public function add_course_form_get() {
    $response = array();
    $response = $this->api_instructor_model->add_course_form_get();
    return $this->set_response($response, REST_Controller::HTTP_OK);
  }
  
  public function add_course_post() {
    $response = array();

    if(isset($_POST['auth_token']) && !empty($_POST['auth_token'])){
      $auth_token = $_POST['auth_token'];
      $user_details = json_decode($this->token_data_get($auth_token), true);
      $response = $this->api_instructor_model->add_course_post($user_details['user_id']);
    }

    return $this->set_response($response, REST_Controller::HTTP_OK);
  }

   public function edit_course_form_get() {
     $response = array();

    if(isset($_GET['auth_token']) && !empty($_GET['auth_token']) && isset($_GET['course_id']) && !empty($_GET['course_id'])){
      $course_id = $_GET['course_id'];
      $auth_token = $_GET['auth_token'];
      $user_details = json_decode($this->token_data_get($auth_token), true);
      $response = $this->api_instructor_model->edit_course_form_get($course_id, $user_details['user_id']);
    }
      return $this->set_response($response, REST_Controller::HTTP_OK);
  }
  
  public function update_course_post() {
    $response = array();

    if(isset($_POST['auth_token']) && !empty($_POST['auth_token'])){
      $auth_token = $_POST['auth_token'];
      $course_id = $_POST['course_id'];
      $user_details = json_decode($this->token_data_get($auth_token), true);
      $response = $this->api_instructor_model->update_course_post($course_id, $user_details['user_id']);
    }

    return $this->set_response($response, REST_Controller::HTTP_OK);
  }

  public function update_course_status_get(){
    $response = array();

    if(isset($_GET['auth_token']) && !empty($_GET['auth_token'])){
      $auth_token = $_GET['auth_token'];
      $course_id = $_GET['course_id'];
      $status = $_GET['status'];
      $user_details = json_decode($this->token_data_get($auth_token), true);
      $response = $this->api_instructor_model->update_course_status_get($course_id, $status, $user_details['user_id']);
    }

    return $this->set_response($response, REST_Controller::HTTP_OK);
  }

  public function edit_course_requirements_get(){
    $response = array();
    if(isset($_GET['auth_token']) && !empty($_GET['auth_token'])){
      $auth_token = $_GET['auth_token'];
      $course_id = $_GET['course_id'];
      $user_details = json_decode($this->token_data_get($auth_token), true);
      $response = $this->api_instructor_model->edit_course_requirements_get($course_id, $user_details['user_id']);
    }

    return $this->set_response($response, REST_Controller::HTTP_OK);
  }

  public function update_course_requirements_post(){
    $response = array();
    if(isset($_POST['auth_token']) && !empty($_POST['auth_token'])){
      $auth_token = $_POST['auth_token'];
      $course_id = $_POST['course_id'];
      $user_details = json_decode($this->token_data_get($auth_token), true);
      $response = $this->api_instructor_model->update_course_requirements_post($course_id, $user_details['user_id']);
    }

    return $this->set_response($response, REST_Controller::HTTP_OK);
  }

  public function edit_course_outcomes_get(){
    $response = array();
    if(isset($_GET['auth_token']) && !empty($_GET['auth_token'])){
      $auth_token = $_GET['auth_token'];
      $course_id = $_GET['course_id'];
      $user_details = json_decode($this->token_data_get($auth_token), true);
      $response = $this->api_instructor_model->edit_course_outcomes_get($course_id, $user_details['user_id']);
    }

    return $this->set_response($response, REST_Controller::HTTP_OK);
  }

  public function update_course_outcomes_post(){
    $response = array();
    if(isset($_POST['auth_token']) && !empty($_POST['auth_token'])){
      $auth_token = $_POST['auth_token'];
      $course_id = $_POST['course_id'];
      $user_details = json_decode($this->token_data_get($auth_token), true);
      $response = $this->api_instructor_model->update_course_outcomes_post($course_id, $user_details['user_id']);
    }

    return $this->set_response($response, REST_Controller::HTTP_OK);
  }

  public function delete_course_get() {
    $response = array();

    if(isset($_GET['auth_token']) && !empty($_GET['auth_token'])){
      $auth_token = $_GET['auth_token'];
      $course_id = $_GET['course_id'];
      $user_details = json_decode($this->token_data_get($auth_token), true);
      $response = $this->api_instructor_model->delete_course_get($course_id, $user_details['user_id']);
    }

    return $this->set_response($response, REST_Controller::HTTP_OK);
  }

  public function section_and_lesson_get() {
    $response = array();

    if(isset($_GET['auth_token']) && !empty($_GET['auth_token']) && isset($_GET['course_id']) && !empty($_GET['course_id'])){
      $auth_token = $_GET['auth_token'];
      $course_id = $_GET['course_id'];
      $user_details = json_decode($this->token_data_get($auth_token), true);
      $response = $this->api_instructor_model->section_and_lesson_get($course_id, $user_details['user_id']);
    }
    return $this->set_response($response, REST_Controller::HTTP_OK);
  }

  public function sections_get() {
    $response = array();

    if(isset($_GET['auth_token']) && !empty($_GET['auth_token']) && isset($_GET['course_id']) && !empty($_GET['course_id'])){
      $auth_token = $_GET['auth_token'];
      $course_id = $_GET['course_id'];
      $user_details = json_decode($this->token_data_get($auth_token), true);
      $response = $this->api_instructor_model->sections_get($course_id, $user_details['user_id']);
    }
    return $this->set_response($response, REST_Controller::HTTP_OK);
  }

  public function add_section_post() {
    $response = array();

    if(isset($_POST['auth_token']) && !empty($_POST['auth_token']) && isset($_POST['course_id']) && !empty($_POST['course_id'])){
      $auth_token = $_POST['auth_token'];
      $course_id = $_POST['course_id'];
      $user_details = json_decode($this->token_data_get($auth_token), true);
      $response = $this->api_instructor_model->add_section_post($course_id, $user_details['user_id']);
    }
    return $this->set_response($response, REST_Controller::HTTP_OK);
  }

  public function update_section_post() {
    $response = array();

    if(isset($_POST['auth_token']) && !empty($_POST['auth_token']) && isset($_POST['section_id']) && !empty($_POST['section_id'])){
      $auth_token = $_POST['auth_token'];
      $section_id = $_POST['section_id'];
      $user_details = json_decode($this->token_data_get($auth_token), true);
      $response = $this->api_instructor_model->update_section_post($section_id, $user_details['user_id']);
    }
    return $this->set_response($response, REST_Controller::HTTP_OK);
  }

  public function delete_section_post() {
    $response = array();

    if(isset($_POST['auth_token']) && !empty($_POST['auth_token']) && isset($_POST['section_id']) && !empty($_POST['section_id']) && isset($_POST['course_id']) && !empty($_POST['course_id'])){
      $auth_token = $_POST['auth_token'];
      $course_id = $_POST['course_id'];
      $section_id = $_POST['section_id'];
      $user_details = json_decode($this->token_data_get($auth_token), true);
      $response = $this->api_instructor_model->delete_section_post($section_id, $course_id, $user_details['user_id']);
    }
    return $this->set_response($response, REST_Controller::HTTP_OK);
  }


  public function add_lesson_post() {
    $response = array();

    if(isset($_POST['auth_token']) && !empty($_POST['auth_token']) && isset($_POST['course_id']) && !empty($_POST['course_id']) && isset($_POST['section_id']) && !empty($_POST['section_id'])){
      $auth_token = $_POST['auth_token'];
      $user_details = json_decode($this->token_data_get($auth_token), true);
      $response = $this->api_instructor_model->add_lesson_post($user_details['user_id']);
    }
    return $this->set_response($response, REST_Controller::HTTP_OK);
  }

  public function lesson_all_data_get(){
    $response = array();

    if(isset($_GET['lesson_id']) && !empty($_GET['lesson_id'])){
      $response = $this->api_instructor_model->lesson_all_data_get($_GET['lesson_id']);
    }
    return $this->set_response($response, REST_Controller::HTTP_OK);
  }

  public function update_lesson_post() {
    $response = array();

    if(isset($_POST['auth_token']) && !empty($_POST['auth_token']) && isset($_POST['lesson_id']) && !empty($_POST['lesson_id'])){
      $auth_token = $_POST['auth_token'];
      $lesson_id = $_POST['lesson_id'];
      $user_details = json_decode($this->token_data_get($auth_token), true);
      $response = $this->api_instructor_model->update_lesson_post($lesson_id, $user_details['user_id']);
    }
    return $this->set_response($response, REST_Controller::HTTP_OK);
  }

  public function delete_lesson_get() {
    $response = array();

    if(isset($_GET['auth_token']) && !empty($_GET['auth_token']) && isset($_GET['lesson_id']) && !empty($_GET['lesson_id'])){
      $auth_token = $_GET['auth_token'];
      $lesson_id = $_GET['lesson_id'];
      $user_details = json_decode($this->token_data_get($auth_token), true);
      $response = $this->api_instructor_model->delete_lesson_get($lesson_id, $user_details['user_id']);
    }
    return $this->set_response($response, REST_Controller::HTTP_OK);
  }

  public function sort_post() {
    $response = array();

    if(isset($_POST['auth_token']) && !empty($_POST['auth_token'])){
      $auth_token = $_POST['auth_token'];
      $type = $_POST['type'];
      $user_details = json_decode($this->token_data_get($auth_token), true);
      $response = $this->api_instructor_model->sort_post($user_details['user_id'], $type);
    }
    return $this->set_response($response, REST_Controller::HTTP_OK);
  }



  public function course_pricing_form_get() {
    $response = array();

    if(isset($_GET['auth_token']) && !empty($_GET['auth_token'])){
      $auth_token = $_GET['auth_token'];
      $course_id = $_GET['course_id'];
      $user_details = json_decode($this->token_data_get($auth_token), true);
      $response = $this->api_instructor_model->course_pricing_form_get($user_details['user_id'], $course_id);
    }
    return $this->set_response($response, REST_Controller::HTTP_OK);
  }

  public function update_course_price_post() {
    $response = array();

    if(isset($_POST['auth_token']) && !empty($_POST['auth_token'])){
      $auth_token = $_POST['auth_token'];
      $course_id = $_POST['course_id'];
      $user_details = json_decode($this->token_data_get($auth_token), true);
      $response = $this->api_instructor_model->update_course_price_post($user_details['user_id'], $course_id);
    }
    return $this->set_response($response, REST_Controller::HTTP_OK);
  }

  public function sales_report_get(){
    $response = array();

    if(isset($_GET['auth_token']) && !empty($_GET['auth_token'])){
    $auth_token = $_GET['auth_token'];
    $user_details = json_decode($this->token_data_get($auth_token), true);
    $response = $this->api_instructor_model->sales_report_get($user_details['user_id']);
    }
    return $this->set_response($response, REST_Controller::HTTP_OK);
  }

  public function details_of_sales_report_get(){
    $response = array();

    if(isset($_GET['payment_id']) && !empty($_GET['payment_id'])){
    $payment_id = $_GET['payment_id'];
    $response = $this->api_instructor_model->details_of_sales_report_get($payment_id);
    }
    return $this->set_response($response, REST_Controller::HTTP_OK);
  }

  public function payout_report_get(){
    $response = array();

    if(isset($_GET['auth_token']) && !empty($_GET['auth_token'])){
      $auth_token = $_GET['auth_token'];
      $user_details = json_decode($this->token_data_get($auth_token), true);
      $response = $this->api_instructor_model->payout_report_get($user_details['user_id']);
    }
    return $this->set_response($response, REST_Controller::HTTP_OK);
  }

  public function add_withdrawal_request_post() {
    $response = array();
    
    if(isset($_POST['auth_token']) && !empty($_POST['auth_token'])){
      $auth_token = $_POST['auth_token'];
      $user_details = json_decode($this->token_data_get($auth_token), true);
      $response = $this->api_instructor_model->add_withdrawal_request_post($user_details['user_id']);
    }
    return $this->set_response($response, REST_Controller::HTTP_OK);

  }

  public function delete_withdrawal_request_get() {
    $response = array();
    
    if(isset($_GET['auth_token']) && !empty($_GET['auth_token'])){
      $auth_token = $_GET['auth_token'];
      $user_details = json_decode($this->token_data_get($auth_token), true);
      $response = $this->api_instructor_model->delete_withdrawal_request_get($user_details['user_id']);
    }
    return $this->set_response($response, REST_Controller::HTTP_OK);

  }



  






}