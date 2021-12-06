<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Email_model extends CI_Model {

	function __construct()
	{
		parent::__construct();
	}

	public function send_email_verification_mail($to = "", $verification_code = "") {
		$to_name = $this->db->get_where('users', array('email' => $to))->row_array();

		$email_data['subject'] = "Verify email address";
		$email_data['from'] = get_settings('system_email');
		$email_data['to'] = $to;
		$email_data['to_name'] = $to_name['first_name'].' '.$to_name['last_name'];
		$email_data['verification_code'] = $verification_code;
		$email_template = $this->load->view('email/email_verification', $email_data, TRUE);
		$this->send_smtp_mail($email_template, $email_data['subject'], $email_data['to'], $email_data['from'], 'verification');
	}


	function password_reset_email($new_password = '' , $email = '') {
		$query = $this->db->get_where('users' , array('email' => $email));
		if($query->num_rows() > 0) {
			$email_data['subject'] = "Password reset request";
			$email_data['from'] = get_settings('system_email');
			$email_data['to'] = $email;
			$email_data['to_name'] = $query->row('first_name').' '.$query->row('last_name');
			$email_data['message'] = 'Your password has been changed. Your new password is : <b style="cursor: pointer;"><u>'.$new_password.'</u></b><br />';
			$email_template = $this->load->view('email/common_template', $email_data, TRUE);
			$this->send_smtp_mail($email_template, $email_data['subject'], $email_data['to'], $email_data['from']);
			return true;
		}else {
			return false;
		}
	}

	public function send_mail_on_course_status_changing($course_id = "", $mail_subject = "", $mail_body = "") {
		$instructor_id		 = 0;
		$course_details    = $this->crud_model->get_course_by_id($course_id)->row_array();
		if ($course_details['user_id'] != "") {
			$instructor_id = $course_details['user_id'];
		}else {
			$instructor_id = $this->session->userdata('user_id');
		}
		$instuctor_details = $this->user_model->get_all_user($instructor_id)->row_array();


		$email_data['subject'] = $mail_subject;
		$email_data['from'] = get_settings('system_email');
		$email_data['to'] = $instuctor_details['email'];
		$email_data['to_name'] = $instuctor_details['first_name'].' '.$instuctor_details['last_name'];
		$email_data['message'] = $mail_body;
		$email_template = $this->load->view('email/common_template', $email_data, TRUE);
		$this->send_smtp_mail($email_template, $email_data['subject'], $email_data['to'], $email_data['from']);
	}

	public function course_purchase_notification($student_id = "", $payment_method = "", $amount_paid = ""){
		$purchased_courses 	= $this->session->userdata('cart_items');
		$student_data 		= $this->user_model->get_all_user($student_id)->row_array();
		$student_full_name 	= $student_data['first_name'].' '.$student_data['last_name'];
		$admin_id 			= $this->user_model->get_admin_details()->row('id');
	    foreach ($purchased_courses as $course_id) {
	    	$course_owner_user_id = $this->crud_model->get_course_by_id($course_id)->row('user_id');
	    	if($course_owner_user_id != $admin_id):
				$this->course_purchase_notification_admin($course_id, $student_full_name, $student_data['email'], $amount_paid);
			endif;
			$this->course_purchase_notification_instructor($course_id, $student_full_name, $student_data['email']);
			$this->course_purchase_notification_student($course_id, $student_id);
	    }
	}

	public function course_purchase_notification_admin($course_id = "", $student_full_name = "", $student_email = "", $amount = ""){
		$course_details = $this->crud_model->get_course_by_id($course_id)->row_array();
		$admin_details = $this->user_model->get_admin_details();
		$instructor_details = $this->user_model->get_all_user($course_details['user_id'])->row_array();
		$admin_msg = "<h2>".$course_details['title']."</h2>";
		$admin_msg .= "<h3><b><u><span style='color: #2ec75e;'>Course Price : ".currency($amount)."</span></u></b></h3>";
		$admin_msg .= "<p><b>Course owner:</b></p>";
		$admin_msg .= "<p>Name: <b>".$instructor_details['first_name']." ".$instructor_details['last_name']."</b></p>";
		$admin_msg .= "<p>Email: <b>".$instructor_details['email']."</b></p>";
		$admin_msg .= "<hr style='opacity: .4;'>";
		$admin_msg .= "<p><b>Bought the course:-</b></p>";
		$admin_msg .= "<p>Name: <b>".$student_full_name."</b></p>";
		$admin_msg .= "<p>Email: <b>".$student_email."</b></p>";


		$email_data['subject'] = 'The course has sold out';
		$email_data['from'] = get_settings('system_email');
		$email_data['to'] = $admin_details->row('email');
		$email_data['to_name'] = $admin_details->row('first_name').' '.$admin_details->row('last_name');
		$email_data['message'] = $admin_msg;
		$email_template = $this->load->view('email/common_template', $email_data, TRUE);
		$this->send_smtp_mail($email_template, $email_data['subject'], $email_data['to'], $email_data['from']);
	}

	public function course_purchase_notification_instructor($course_id = "",$student_full_name = "", $student_email = ""){
		$course_details = $this->crud_model->get_course_by_id($course_id)->row_array();
		$instructor_details = $this->user_model->get_all_user($course_details['user_id']);
		$instructor_msg = "<h2>".$course_details['title']."</h2>";
		$instructor_msg .= "<p>Congratulation!! Your <b>".$course_details['title']."</b> courses have been sold.</p>";
		$instructor_msg .= "<p><b>Bought the course:-</b></p>";
		$instructor_msg .= "<p>Name: <b>".$student_full_name."</b></p>";
		$instructor_msg .= "<p>Email: <b>".$student_email."</b></p>";

		$email_data['subject'] = 'The course has sold out';
		$email_data['from'] = get_settings('system_email');
		$email_data['to'] = $instructor_details->row('email');
		$email_data['to_name'] = $instructor_details->row('first_name').' '.$instructor_details->row('last_name');
		$email_data['message'] = $instructor_msg;
		$email_template = $this->load->view('email/common_template', $email_data, TRUE);
		$this->send_smtp_mail($email_template, $email_data['subject'], $email_data['to'], $email_data['from']);

	}

	public function course_purchase_notification_student($course_id = "", $student_id = ""){
		$course_details = $this->crud_model->get_course_by_id($course_id)->row_array();
		$student_details = $this->user_model->get_all_user($student_id);
		$instructor_details = $this->user_model->get_all_user($course_details['user_id'])->row_array();
		$student_msg = "<h2>".$course_details['title']."</h2>";
		$student_msg .= "<p><b>Congratulation!!</b> You have purchased a <b>".$course_details['title']."</b> course.</p>";
		$student_msg .= "<hr style='opacity: .4;'>";
		$student_msg .= "<p><b>Course owner:</b></p>";
		$student_msg .= "<p>Name: <b>".$instructor_details['first_name']." ".$instructor_details['last_name']."</b></p>";
		$student_msg .= "<p>Email: <b>".$instructor_details['email']."</b></p>";

		$email_data['subject'] = 'Course Purchase';
		$email_data['from'] = get_settings('system_email');
		$email_data['to'] = $student_details->row('email');
		$email_data['to_name'] = $student_details->row('first_name').' '.$student_details->row('last_name');
		$email_data['message'] = $student_msg;
		$email_template = $this->load->view('email/common_template', $email_data, TRUE);
		$this->send_smtp_mail($email_template, $email_data['subject'], $email_data['to'], $email_data['from']);
	}

	public function notify_on_certificate_generate($user_id = "", $course_id = "") {
		$checker = array(
			'course_id' => $course_id,
			'student_id' => $user_id
		);
		$result = $this->db->get_where('certificates', $checker)->row_array();
		$certificate_link = site_url('certificate/'.$result['shareable_url']);
		$course_details    = $this->crud_model->get_course_by_id($course_id)->row_array();
		$user_details = $this->user_model->get_all_user($user_id)->row_array();
		$email_msg	=	"<b>Congratulations!!</b> ". $user_details['first_name']." ".$user_details['last_name'].",";
		$email_msg	.=	"<p>You have successfully completed the course named, <b>".$course_details['title'].".</b></p>";
		$email_msg	.=	"<p>You can get your course completion certificate from here <b>".$certificate_link.".</b></p>";

		$email_data['subject'] = 'Course Completion Notification';
		$email_data['from'] = get_settings('system_email');
		$email_data['to'] = $user_details['email'];
		$email_data['to_name'] = $user_details['first_name'].' '.$user_details['last_name'];
		$email_data['message'] = $student_msg;
		$email_template = $this->load->view('email/common_template', $email_data, TRUE);
		$this->send_smtp_mail($email_template, $email_data['subject'], $email_data['to'], $email_data['from']);
	}

	public function suspended_offline_payment($user_id = ""){
		$user_details = $this->user_model->get_all_user($user_id);
		$email_msg  = "<p>Your offline payment has been <b style='color: red;'>suspended</b> !</p>";
		$email_msg .= "<p>Please provide a valid document of your payment.</p>";

		$email_data['subject'] = 'Suspended Offline Payment';
		$email_data['from'] = get_settings('system_email');
		$email_data['to'] = $user_details->row('email');
		$email_data['to_name'] = $user_details->row('first_name').' '.$user_details->row('last_name');
		$email_data['message'] = $email_msg;
		$email_template = $this->load->view('email/common_template', $email_data, TRUE);
		$this->send_smtp_mail($email_template, $email_data['subject'], $email_data['to'], $email_data['from']);
	}


	public function bundle_purchase_notification($student_id = "", $payment_method = "", $amount_paid = ""){
        $bundle_id = $this->session->userdata('checkout_bundle_id');
        $bundle_details = $this->course_bundle_model->get_bundle($bundle_id)->row_array();

        $admin_details = $this->user_model->get_admin_details()->row_array();
		$bundle_creator_details = $this->user_model->get_all_user($bundle_details['user_id'])->row_array();
		$student_details = $this->user_model->get_all_user($student_id)->row_array();

		if($admin_details['id'] != $bundle_creator_details['id']){
			$this->bundle_purchase_notification_admin($bundle_details, $admin_details, $bundle_creator_details, $student_details);
		}
		$this->bundle_purchase_notification_bundle_creator($bundle_details, $admin_details, $bundle_creator_details, $student_details);
		$this->bundle_purchase_notification_student($bundle_details, $admin_details, $bundle_creator_details, $student_details);
	}

	function bundle_purchase_notification_admin($bundle_details = "", $admin_details = "", $bundle_creator_details = "", $student_details = ""){
		$email_msg = "<h2>".$bundle_details['title']."</h2>";
		$email_msg .= "<h3><b><u><span style='color: #2ec75e;'>Bundle Price : ".currency($bundle_details['price'])."</span></u></b></h3>";
		$email_msg .= "<p><b>Bundle owner:</b></p>";
		$email_msg .= "<p>Name: <b>".$bundle_creator_details['first_name']." ".$bundle_creator_details['last_name']."</b></p>";
		$email_msg .= "<p>Email: <b>".$bundle_creator_details['email']."</b></p>";
		$email_msg .= "<hr style='opacity: .4;'>";
		$email_msg .= "<p><b>Bought the bundle:-</b></p>";
		$email_msg .= "<p>Name: <b>".$student_details['first_name']." ".$student_details['last_name']."</b></p>";
		$email_msg .= "<p>Email: <b>".$student_details['email']."</b></p>";

		$email_data['subject'] = 'The bundle has sold out';
		$email_data['from'] = get_settings('system_email');
		$email_data['to'] = $admin_details['email'];
		$email_data['to_name'] = $admin_details['first_name'].' '.$admin_details['last_name'];
		$email_data['message'] = $email_msg;
		$email_template = $this->load->view('email/common_template', $email_data, TRUE);
		$this->send_smtp_mail($email_template, $email_data['subject'], $email_data['to'], $email_data['from']);
	}

	function bundle_purchase_notification_bundle_creator($bundle_details = "", $admin_details = "", $bundle_creator_details = "", $student_details = ""){
		$email_msg = "<h2>".$bundle_details['title']."</h2>";
		$email_msg .= "<p>Congratulation!! Your <b>".$bundle_details['title']."</b> course bundle have been sold.</p>";
		$email_msg .= "<h3><b><u><span style='color: #2ec75e;'>Bundle Price : ".currency($bundle_details['price'])."</span></u></b></h3>";
		$email_msg .= "<p><b>Bought the bundle:-</b></p>";
		$email_msg .= "<p>Name: <b>".$student_details['first_name'].' '.$student_details['last_name']."</b></p>";
		$email_msg .= "<p>Email: <b>".$student_details['email']."</b></p>";

		$email_data['subject'] = 'The bundle has sold out';
		$email_data['from'] = get_settings('system_email');
		$email_data['to'] = $bundle_creator_details['email'];
		$email_data['to_name'] = $bundle_creator_details['first_name'].' '.$bundle_creator_details['last_name'];
		$email_data['message'] = $email_msg;
		$email_template = $this->load->view('email/common_template', $email_data, TRUE);
		$this->send_smtp_mail($email_template, $email_data['subject'], $email_data['to'], $email_data['from']);
	}

	function bundle_purchase_notification_student($bundle_details = "", $admin_details = "", $bundle_creator_details = "", $student_details = ""){
		$email_msg = "<h2>".$bundle_details['title']."</h2>";
		$email_msg .= "<p><b>Congratulation!!</b> You have purchased a <b>".$bundle_details['title']."</b> bundle.</p>";
		$email_msg .= "<h3><b><u><span style='color: #2ec75e;'>Bundle Price : ".currency($bundle_details['price'])."</span></u></b></h3>";
		$email_msg .= "<hr style='opacity: .4;'>";
		$email_msg .= "<p><b>Bundle owner:</b></p>";
		$email_msg .= "<p>Name: <b>".$bundle_creator_details['first_name']." ".$bundle_creator_details['last_name']."</b></p>";
		$email_msg .= "<p>Email: <b>".$bundle_creator_details['email']."</b></p>";

		$email_data['subject'] = 'Bundle Purchase';
		$email_data['from'] = get_settings('system_email');
		$email_data['to'] = $student_details['email'];
		$email_data['to_name'] = $student_details['first_name'].' '.$student_details['last_name'];
		$email_data['message'] = $email_msg;
		$email_template = $this->load->view('email/common_template', $email_data, TRUE);
		$this->send_smtp_mail($email_template, $email_data['subject'], $email_data['to'], $email_data['from']);
	}

	function send_notice($notice_id ="", $course_id = ""){
		$course_details = $this->crud_model->get_course_by_id($course_id)->row_array();
		$notice_details = $this->noticeboard_model->get_notices($notice_id)->row_array();
		$instructor_details = $this->user_model->get_all_user($course_details['user_id'])->row_array();

		$email_data['subject'] = htmlspecialchars_decode($notice_details['title']);
		$email_data['from'] = get_settings('system_email');
		$email_data['course_title'] = $course_details['title'];

		$enrolled_students = $this->crud_model->enrol_history($course_id)->result_array();
		foreach($enrolled_students as $enrolled_student):
			$student_details = $this->user_model->get_user($enrolled_student['user_id'])->row_array();
			$email_data['to'] = $student_details['email'];
			$email_data['to_name'] = $student_details['first_name'].' '.$student_details['last_name'];
			$email_data['message'] = htmlspecialchars_decode($notice_details['description']).'<hr style="border: 1px solid #efefef; margin-top: 50px;"> <small><b>'.get_phrase('course').':</b> '.$course_details['title'].'<br> <b>'.get_phrase('instructor').': </b> '.$instructor_details['first_name'].' '.$instructor_details['last_name'].'</small>';

			$email_template = $this->load->view('email/common_template', $email_data, TRUE);
			$this->send_smtp_mail($email_template, $email_data['subject'], $email_data['to'], $email_data['from']);
		endforeach;

		return 1;
	}

	public function send_smtp_mail($msg=NULL, $sub=NULL, $to=NULL, $from=NULL, $email_type=NULL, $verification_code = null) {
		//Load email library
		$this->load->library('email');

		if($from == NULL)
			$from		=	$this->db->get_where('settings' , array('key' => 'system_email'))->row()->value;

		//SMTP & mail configuration
		$config = array(
			'protocol'  => get_settings('protocol'),
			'smtp_host' => get_settings('smtp_host'),
			'smtp_port' => get_settings('smtp_port'),
			'smtp_user' => get_settings('smtp_user'),
			'smtp_pass' => get_settings('smtp_pass'),
			'mailtype'  => 'html',
			'charset'   => 'utf-8'
		);
		$this->email->set_header('MIME-Version', 1.0);
		$this->email->set_header('Content-type', 'text/html');
		$this->email->set_header('charset', 'UTF-8');
		
		$this->email->initialize($config);
		$this->email->set_mailtype("html");
		$this->email->set_newline("\r\n");

		$this->email->to($to);
		$this->email->from($from, get_settings('system_name'));
		$this->email->subject($sub);
		$this->email->message($msg);

		//Send email
		$this->email->send();
	}
}
