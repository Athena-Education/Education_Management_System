<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Api_model extends CI_Model
{

	// constructor
	function __construct()
	{
		parent::__construct();
		/*cache control*/
		$this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
		$this->output->set_header('Pragma: no-cache');
	}

	// get the top courses
	public function top_courses_get($top_course_id = "")
	{
		if ($top_course_id != "") {
			$this->db->where('id', $top_course_id);
		}
		$this->db->where('is_top_course', 1);
		$this->db->where('status', 'active');
		$top_courses = $this->db->get('course')->result_array();
		// This block of codes return the required data of courses
		$result = array();
		$result = $this->course_data($top_courses);
		return $result;
	}

	// Get categories
	public function categories_get($category_id)
	{
		if ($category_id != "") {
			$this->db->where('id', $category_id);
		}
		$this->db->where('parent', 0);
		$categories = $this->db->get('category')->result_array();
		foreach ($categories as $key => $category) {
			$categories[$key]['thumbnail'] = $this->get_image('category_thumbnail', $category['thumbnail']);
			$categories[$key]['number_of_courses'] = $this->crud_model->get_category_wise_courses($category['id'])->num_rows();
		}
		return $categories;
	}

	// Get category wise courses
	public function category_wise_course_get($category_id)
	{
		$category_details = $this->crud_model->get_category_details_by_id($category_id)->row_array();

		if ($category_details['parent'] > 0) {
			$this->db->where('sub_category_id', $category_id);
		} else {
			$this->db->where('category_id', $category_id);
		}
		$this->db->where('status', 'active');
		$courses = $this->db->get('course')->result_array();

		// This block of codes return the required data of courses
		$result = array();
		$result = $this->course_data($courses);
		return $result;
	}

	// Filter courses
	function filter_course()
	{
		$selected_category = $_GET['selected_category'];
		$selected_price = $_GET['selected_price'];
		$selected_level = $_GET['selected_level'];
		$selected_language = $_GET['selected_language'];
		$selected_rating = $_GET['selected_rating'];
		$selected_search_string = ltrim(rtrim($_GET['selected_search_string']));

		$course_ids = array();

		if ($selected_search_string != "" && $selected_search_string != "null") {
			$this->db->like('title', $selected_search_string);
		}
		if ($selected_category != "all") {
			$category_details = $this->crud_model->get_category_details_by_id($selected_category)->row_array();

			if ($category_details['parent'] > 0) {
				$this->db->where('sub_category_id', $selected_category);
			} else {
				$this->db->where('category_id', $selected_category);
			}
		}

		if ($selected_price != "all") {
			if ($selected_price == "paid") {
				$this->db->where('is_free_course', null);
			} elseif ($selected_price == "free") {
				$this->db->where('is_free_course', 1);
			}
		}

		if ($selected_level != "all") {
			$this->db->where('level', $selected_level);
		}

		if ($selected_language != "all") {
			$this->db->where('language', $selected_language);
		}

		$this->db->where('status', 'active');
		$courses = $this->db->get('course')->result_array();

		foreach ($courses as $course) {
			if ($selected_rating != "all") {
				$total_rating =  $this->crud_model->get_ratings('course', $course['id'], true)->row()->rating;
				$number_of_ratings = $this->crud_model->get_ratings('course', $course['id'])->num_rows();
				if ($number_of_ratings > 0) {
					$average_ceil_rating = ceil($total_rating / $number_of_ratings);
					if ($average_ceil_rating == $selected_rating) {
						array_push($course_ids, $course['id']);
					}
				}
			} else {
				array_push($course_ids, $course['id']);
			}
		}

		if (count($course_ids) > 0) {
			$this->db->where_in('id', $course_ids);
			$courses = $this->db->get('course')->result_array();
		} else {
			return array();
		}

		// This block of codes return the required data of courses
		$result = array();
		$result = $this->course_data($courses);
		return $result;
	}

	public function courses_by_search_string_get($search_string)
	{
		$this->db->like('title', $search_string);
		$this->db->where('status', 'active');
		$courses = $this->db->get('course')->result_array();
		return $this->course_data($courses);
	}

	// Return require course data
	public function course_data($courses = array())
	{
		foreach ($courses as $key => $course) {
			$courses[$key]['requirements'] = json_decode($course['requirements']);
			$courses[$key]['outcomes'] = json_decode($course['outcomes']);
			$courses[$key]['thumbnail'] = $this->get_image('course_thumbnail', $course['id']);
			if ($course['is_free_course'] == 1) {
				$courses[$key]['price'] = get_phrase('free');
			} else {
				if ($course['discount_flag'] == 1) {
					$courses[$key]['price'] = currency($course['discounted_price']);
				} else {
					$courses[$key]['price'] = currency($course['price']);
				}
			}
			$total_rating =  $this->crud_model->get_ratings('course', $course['id'], true)->row()->rating;
			$number_of_ratings = $this->crud_model->get_ratings('course', $course['id'])->num_rows();
			if ($number_of_ratings > 0) {
				$courses[$key]['rating'] = ceil($total_rating / $number_of_ratings);
			} else {
				$courses[$key]['rating'] = 0;
			}
			$courses[$key]['number_of_ratings'] = $number_of_ratings;
			$instructor_details = $this->user_model->get_all_user($course['user_id'])->row_array();
			$courses[$key]['instructor_name'] = $instructor_details['first_name'] . ' ' . $instructor_details['last_name'];
			$courses[$key]['total_enrollment'] = $this->crud_model->enrol_history($course['id'])->num_rows();
			$courses[$key]['shareable_link'] = site_url('home/course/' . slugify($course['title']) . '/' . $course['id']);
		}

		return $courses;
	}
	// Get all the language of system
	public function languages_get()
	{
		$language_files = array();
		$all_files = $this->crud_model->get_list_of_language_files();
		$counter = 0;
		foreach ($all_files as $file) {
			$info = pathinfo($file);
			if (isset($info['extension']) && strtolower($info['extension']) == 'json') {
				$inner_array = array();
				$file_name = explode('.json', $info['basename']);
				$inner_array = array(
					'id' => $counter++,
					'value' => $file_name[0],
					'displayedValue' => ucfirst($file_name[0])
				);

				array_push($language_files, $inner_array);
			}
		}
		return $language_files;
	}

	// Get image for course, categories or user image
	public function get_image($type, $identifier)
	{ // type is the flag to realize whether it is course, category or user image. For course, user image Identifier is id but for category Identifier is image name
		if ($type == 'user_image') {
			// code...
		} elseif ($type == 'course_thumbnail') {
			$course_media_placeholders = themeConfiguration(get_frontend_settings('theme'), 'course_media_placeholders');
			if (file_exists('uploads/thumbnails/course_thumbnails/course_thumbnail_' . get_frontend_settings('theme') . '_' . $identifier . '.jpg')) {
				return base_url() . 'uploads/thumbnails/course_thumbnails/course_thumbnail_' . get_frontend_settings('theme') . '_' . $identifier . '.jpg';
			} else {
				return base_url() . $course_media_placeholders['course_thumbnail_placeholder'];
			}
		} elseif ($type == 'category_thumbnail') {
			if (file_exists('uploads/thumbnails/category_thumbnails/' . $identifier) && $identifier != "") {
				return base_url() . 'uploads/thumbnails/category_thumbnails/' . $identifier;
			} else {
				return base_url() . 'uploads/thumbnails/category_thumbnails/category-thumbnail.png';
			}
		}
	}

	public function system_settings_get_older()
	{
		$query = $this->db->get('settings')->result_array();
		$system_settings_data = array();
		foreach ($query as $row) {
			if ($row['key'] == 'paypal' || $row['key'] == 'stripe_keys') {
				$system_settings_data[$row['key']] = json_decode($row['value'], true);
			} else {
				$system_settings_data[$row['key']] = $row['value'];
			}
		}
		$system_settings_data['thumbnail'] = base_url() . 'uploads/system/'.get_frontend_settings('dark_logo');
		return $system_settings_data;
	}

	public function system_settings_get()
	{
		$query = $this->db->get('settings')->result_array();
		$system_settings_data = array();
		foreach ($query as $row) {
			if (
				$row['key'] == "language" ||
				$row['key'] == "system_name" ||
				$row['key'] == "system_title" ||
				$row['key'] == "system_email" ||
				$row['key'] == "address" ||
				$row['key'] == "phone" ||
				$row['key'] == "slogan" ||
				$row['key'] == "version" ||
				$row['key'] == "youtube_api_key" ||
				$row['key'] == "vimeo_api_key"
			) {

				$system_settings_data[$row['key']] = $row['value'];
			}
		}
		$system_settings_data['thumbnail'] = base_url() . 'uploads/system/'.get_frontend_settings('dark_logo');
		$system_settings_data['favicon'] = base_url() . 'uploads/system/'.get_frontend_settings('favicon');
		return $system_settings_data;
	}

	// Login mechanism
	public function login_get()
	{
		$userdata = array();
		$credential = array('email' => $_GET['email'], 'password' => sha1($_GET['password']), 'status' => 1);
		$query = $this->db->get_where('users', $credential);
		if ($query->num_rows() > 0) {
			$row = $query->row_array();
			$userdata['user_id'] = $row['id'];
			$userdata['first_name'] = $row['first_name'];
			$userdata['last_name'] = $row['last_name'];
			$userdata['email'] = $row['email'];
			$userdata['role'] = strtolower(get_user_role('user_role', $row['id']));
			$userdata['validity'] = 1;
		} else {
			$userdata['validity'] = 0;
		}
		return $userdata;
	}

	// My Courses
	public function my_courses_get($user_id = "")
	{
		$my_courses = array();
		$my_courses_ids = $this->user_model->my_courses($user_id)->result_array();
		foreach ($my_courses_ids as $my_courses_id) {
			$course_details = $this->crud_model->get_course_by_id($my_courses_id['course_id'])->row_array();
			array_push($my_courses, $course_details);
		}
		$my_courses = $this->course_data($my_courses);
		foreach ($my_courses as $key => $my_course) {
			if (isset($my_course['id']) && $my_course['id'] > 0) {
				$my_courses[$key]['completion'] = round(course_progress($my_course['id'], $user_id));
				$my_courses[$key]['total_number_of_lessons'] = $this->crud_model->get_lessons('course', $my_course['id'])->num_rows();
				$my_courses[$key]['total_number_of_completed_lessons'] = $this->get_completed_number_of_lesson($user_id, 'course', $my_course['id']);
			}
		}
		return $my_courses;
	}

	// My Wishlists
	public function my_wishlist_get($user_id = "")
	{
		$wishlists = $this->crud_model->getWishLists($user_id);
		if (sizeof($wishlists) > 0) {
			$this->db->where_in('id', $wishlists);
			$courses = $this->db->get('course')->result_array();
			return $this->course_data($courses);
		} else {
			return array();
		}
	}

	// Remove from wishlist
	public function toggle_wishlist_items_get($user_id = "")
	{
		$status = "";
		$course_id = $_GET['course_id'];
		$wishlists = array();
		$user_details = $this->user_model->get_user($user_id)->row_array();
		$wishlists = json_decode($user_details['wishlist']);
		if (in_array($course_id, $wishlists)) {
			$container = array();
			foreach ($wishlists as $key) {
				if ($key != $course_id) {
					array_push($container, $key);
				}
			}
			$wishlists = $container;
			$status = "removed";
		} else {
			array_push($wishlists, $course_id);
			$status = "added";
		}
		$updater['wishlist'] = json_encode($wishlists);
		$this->db->where('id', $user_id);
		$this->db->update('users', $updater);
		$this->my_wishlist_get($user_id);
		return $status;
	}

	//get all sections
	public function sections_get($course_id = "", $user_id = "")
	{
		$lesson_counter_starts = 0;
		$lesson_counter_ends   = 0;
		$sections = $this->crud_model->get_section('course', $course_id)->result_array();
		foreach ($sections as $key => $section) {
			$sections[$key]['lessons'] = $this->section_wise_lessons($section['id'], $user_id);
			$sections[$key]['total_duration'] = str_replace(' ' . get_phrase('hours'), "", $this->crud_model->get_total_duration_of_lesson_by_section_id($section['id']));
			if ($key == 0) {
				$lesson_counter_starts = 1;
				$lesson_counter_ends = count($sections[$key]['lessons']);
			} else {
				$lesson_counter_starts = $lesson_counter_ends + 1;
				$lesson_counter_ends = $lesson_counter_starts + count($sections[$key]['lessons']);
			}
			$sections[$key]['lesson_counter_starts'] = $lesson_counter_starts;
			$sections[$key]['lesson_counter_ends'] = $lesson_counter_ends;
			if ($user_id > 0) {
				$sections[$key]['completed_lesson_number'] = $this->get_completed_number_of_lesson($user_id, 'section', $section['id']);
			} else {
				$sections[$key]['completed_lesson_number'] = 0;
			}
		}
		$response = $this->add_user_validity($sections);
		return $response;
	}

	public function section_wise_lessons($section_id = "", $user_id = "")
	{
		$response = array();
		$lessons = $this->crud_model->get_lessons('section', $section_id)->result_array();
		foreach ($lessons as $key => $lesson) {
			$response[$key]['id'] = $lesson['id'];
			$response[$key]['title'] = $lesson['title'];
			$response[$key]['duration'] = readable_time_for_humans($lesson['duration_for_mobile_application']);
			$response[$key]['course_id'] = $lesson['course_id'];
			$response[$key]['section_id'] = $lesson['section_id'];
			$response[$key]['video_type'] = ($lesson['video_type_for_mobile_application'] == "" ? "" : $lesson['video_type_for_mobile_application']);
			$response[$key]['video_url'] = ($lesson['video_url_for_mobile_application'] == "" ? "" : $lesson['video_url_for_mobile_application']);;
			$response[$key]['video_url_web'] = $lesson['video_url'];
			$response[$key]['video_type_web'] = $lesson['video_type'];
			$response[$key]['lesson_type'] = $lesson['lesson_type'];
			$response[$key]['attachment'] = $lesson['attachment'];
			$response[$key]['attachment_url'] = base_url() . 'uploads/lesson_files/' . $lesson['attachment'];
			$response[$key]['attachment_type'] = $lesson['attachment_type'];
			$response[$key]['summary'] = $lesson['summary'];
			if ($user_id > 0) {
				$response[$key]['is_completed'] = lesson_progress($lesson['id'], $user_id);
			} else {
				$response[$key]['is_completed'] = 0;
			}
		}
		$response = $this->add_user_validity($response);
		return $response;
	}

	public function add_user_validity($responses = array())
	{
		foreach ($responses as $key => $response) {
			$responses[$key]['user_validity'] = true;
		}
		return $responses;
	}

	public function get_completed_number_of_lesson($user_id = "", $type = "", $id = "")
	{
		$counter = 0;
		if ($type == 'section') {
			$lessons = $this->crud_model->get_lessons('section', $id)->result_array();
		} else {
			$lessons = $this->crud_model->get_lessons('course', $id)->result_array();
		}
		foreach ($lessons as $key => $lesson) {
			if (lesson_progress($lesson['id'], $user_id)) {
				$counter = $counter + 1;
			}
		}
		return $counter;
	}

	public function lesson_details_get($user_id = "", $lession_id = "")
	{
		$lesson_details = $this->crud_model->get_lessons('lesson', $lession_id)->result_array();
		foreach ($lesson_details as $key => $lesson_detail) {
			$lesson_details[$key]['duration'] = readable_time_for_humans($lesson_detail['duration']);
		}
		return $this->add_user_validity($lesson_details);
	}

	// Get course details by id
	public function course_details_by_id_get($user_id = "", $course_id = "")
	{
		$course_details = $this->crud_model->get_course_by_id($course_id)->result_array();
		$response = $this->course_data($course_details);
		foreach ($response as $key => $resp) {
			$response[$key]['sections'] = $this->sections_get($course_id);
			$response[$key]['is_wishlisted'] = $this->is_added_to_wishlist($user_id, $course_id);
			$response[$key]['is_purchased'] = $this->is_purchased($user_id, $course_id);
			$response[$key]['includes'] = array(
				$this->crud_model->get_total_duration_of_lesson_by_course_id($course_id) . ' ' . get_phrase('on_demand_videos'),
				$this->crud_model->get_lessons('course', $course_id)->num_rows() . ' ' . get_phrase('lessons'),
				get_phrase('high_quality_videos'),
				get_phrase('life_time_access'),
			);
		}
		return $response;
	}

	public function is_added_to_wishlist($user_id = 0, $course_id = "")
	{
		if ($user_id > 0) {
			$wishlists = array();
			$user_details = $this->user_model->get_all_user($user_id)->row_array();
			$wishlists = json_decode($user_details['wishlist']);
			if (in_array($course_id, $wishlists)) {
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	public function is_purchased($user_id = 0, $course_id = "")
	{
		// 0 represents Not purchased, 1 represents Purchased, 2 represents Pending
		if ($user_id > 0) {
			$check_purchased_course = $this->db->get_where('enrol', array('user_id' => $user_id, 'course_id' => $course_id))->num_rows();
			if ($check_purchased_course > 0) {
				return 1;
			} else {
				if (addon_status('offline_payment')) {
					$this->load->model('addons/Offline_payment_model', 'offline_payment_model');
					$response = $this->offline_payment_model->get_course_status($user_id, $course_id);
					if ($response) {
						return $response == "approved" ? 1 : 2; // 2 represents Pending status
					}
				}
				return 0;
			}
		} else {
			return 0;
		}
	}

	// This function returns a single object of course by its id
	public function course_object_by_id_get()
	{
		$course_id = $_GET['course_id'];
		$course = $this->crud_model->get_course_by_id($course_id)->row_array();
		$course['requirements'] = json_decode($course['requirements']);
		$course['outcomes'] = json_decode($course['outcomes']);
		$course['thumbnail'] = $this->get_image('course_thumbnail', $course['id']);
		if ($course['is_free_course'] == 1) {
			$course['price'] = get_phrase('free');
		} else {
			if ($course['discount_flag'] == 1) {
				$course['price'] = currency($course['discounted_price']);
			} else {
				$course['price'] = currency($course['price']);
			}
		}
		$total_rating =  $this->crud_model->get_ratings('course', $course['id'], true)->row()->rating;
		$number_of_ratings = $this->crud_model->get_ratings('course', $course['id'])->num_rows();
		if ($number_of_ratings > 0) {
			$course['rating'] = ceil($total_rating / $number_of_ratings);
		} else {
			$course['rating'] = 0;
		}
		$course['number_of_ratings'] = $number_of_ratings;
		$instructor_details = $this->user_model->get_all_user($course['user_id'])->row_array();
		$course['instructor_name'] = $instructor_details['first_name'] . ' ' . $instructor_details['last_name'];
		$course['total_enrollment'] = $this->crud_model->enrol_history($course['id'])->num_rows();
		$course['shareable_link'] = site_url('home/course/' . slugify($course['title']) . '/' . $course['id']);
		return $course;
	}

	// save lesson completion status
	// code of mark this lesson as completed
	function save_course_progress_get($user_id = "")
	{
		$lesson_id = $_GET['lesson_id'];
		$progress = $_GET['progress'];
		$user_details  = $this->user_model->get_all_user($user_id)->row_array();
		$watch_history = $user_details['watch_history'];
		$watch_history_array = array();
		if ($watch_history == '') {
			array_push($watch_history_array, array('lesson_id' => $lesson_id, 'progress' => $progress));
		} else {
			$founder = false;
			$watch_history_array = json_decode($watch_history, true);
			for ($i = 0; $i < count($watch_history_array); $i++) {
				$watch_history_for_each_lesson = $watch_history_array[$i];
				if ($watch_history_for_each_lesson['lesson_id'] == $lesson_id) {
					$watch_history_for_each_lesson['progress'] = $progress;
					$watch_history_array[$i]['progress'] = $progress;
					$founder = true;
				}
			}
			if (!$founder) {
				array_push($watch_history_array, array('lesson_id' => $lesson_id, 'progress' => $progress));
			}
		}
		$data['watch_history'] = json_encode($watch_history_array);
		$this->db->where('id', $user_id);
		$this->db->update('users', $data);

		// CHECK IF THE USER IS ELIGIBLE FOR CERTIFICATE
		if (addon_status('certificate')) {
			$this->load->model('addons/Certificate_model', 'certificate_model');
			$this->certificate_model->check_certificate_eligibility("lesson", $lesson_id, $user_id);
		}

		$lesson_details = $this->crud_model->get_lessons('lesson', $lesson_id)->row_array();
		return $this->course_completion_data($lesson_details['course_id'], $user_id);
	}

	private function course_completion_data($course_id = "", $user_id = "")
	{
		$response = array();
		$course = $this->crud_model->get_course_by_id($course_id)->row_array();
		$response['course_id'] = $course['id'];
		$response['number_of_lessons'] = $this->crud_model->get_lessons('course', $course_id)->num_rows();
		$response['number_of_completed_lessons'] = $this->get_completed_number_of_lesson($user_id, 'course', $course_id);
		$response['course_progress'] = round(course_progress($course_id, $user_id));
		return $response;
	}

	public function userdata_get($user_id = "")
	{
		$user_details = $this->user_model->get_all_user($user_id)->row_array();
		$response['id'] = $user_details['id'];
		$response['first_name'] = $user_details['first_name'];
		$response['last_name'] = $user_details['last_name'];
		$response['email'] = $user_details['email'];
		$social_links = json_decode($user_details['social_links'], true);
		$response['facebook'] = $social_links['facebook'];
		$response['twitter'] = $social_links['twitter'];
		$response['linkedin'] = $social_links['linkedin'];
		$response['biography'] = $user_details['biography'];
		$response['image'] = $this->user_model->get_user_image_url($user_details['id']);
		return $response;
	}

	public function update_userdata_post($user_id = "")
	{
		$response = array();
		$validity = $this->user_model->check_duplication('on_update', $this->input->post('email'), $user_id);
		if ($validity) {
			if (html_escape($this->input->post('first_name')) != "") {
				$data['first_name'] = html_escape($this->input->post('first_name'));
			} else {
				$response['status'] = 'failed';
				$response['error_reason'] = get_phrase('first_name_can_not_be_empty');
				return $response;
			}
			if (html_escape($this->input->post('last_name')) != "") {
				$data['last_name'] = html_escape($this->input->post('last_name'));
			} else {
				$response['status'] = 'failed';
				$response['error_reason'] = get_phrase('last_name_can_not_be_empty');
				return $response;
			}
			if (isset($_POST['email']) && html_escape($this->input->post('email')) != "") {
				$data['email'] = html_escape($this->input->post('email'));
			} else {
				$response['status'] = 'failed';
				$response['error_reason'] = get_phrase('email_can_not_be_empty');
				return $response;
			}

			$social_link['facebook'] = html_escape($this->input->post('facebook_link'));
			$social_link['twitter'] = html_escape($this->input->post('twitter_link'));
			$social_link['linkedin'] = html_escape($this->input->post('linkedin_link'));
			$data['social_links'] = json_encode($social_link);
			$data['biography'] = $this->input->post('biography');
			$this->db->where('id', $user_id);
			$this->db->update('users', $data);
			$response = $this->userdata_get($user_id);
			$response['status'] = 'success';
			$response['error_reason'] = get_phrase('none');
		} else {
			$response['status'] = 'failed';
			$response['error_reason'] = get_phrase('email_duplication');
		}
		return $response;
	}

	public function update_password_post($user_id = "")
	{
		$response = array();
		if (!empty($_POST['current_password']) && !empty($_POST['new_password']) && !empty($_POST['confirm_password'])) {
			$user_details = $this->user_model->get_user($user_id)->row_array();
			$current_password = $this->input->post('current_password');
			$new_password = $this->input->post('new_password');
			$confirm_password = $this->input->post('confirm_password');
			if ($user_details['password'] == sha1($current_password) && $new_password == $confirm_password) {
				$data['password'] = sha1($new_password);
				$this->db->where('id', $user_id);
				$this->db->update('users', $data);
				$response['status'] = 'success';
			} else {
				$response['status'] = 'failed';
			}
		} else {
			$response['status'] = 'failed';
		}

		return $response;
	}

	public function certificate_addon_get($user_id = "", $course_id = "")
	{
		$response = array();
		if (addon_status('certificate')) {
			$response['addon_status'] = 'success';

			$this->load->model('addons/Certificate_model', 'certificate_model');
			$course_progress = course_progress($course_id, $user_id);
			if ($course_progress == 100) {
				$checker = array(
					'course_id' => $course_id,
					'student_id' => $user_id
				);
				$previous_data = $this->db->get_where('certificates', $checker);
				if ($previous_data->num_rows() == 0) {

					$certificate_identifier = substr(sha1($user_id . '-' . $course_id . '-' . date('d-M-Y')), 0, 10);
					$certificate_link = base_url('uploads/certificates/' . $certificate_identifier . '.jpg');
					$insert_data = array(
						'course_id' => $course_id,
						'student_id' => $user_id,
						'shareable_url' => $certificate_identifier . '.jpg'
					);
					$this->db->insert('certificates', $insert_data);
					$this->certificate_model->create_certificate($user_id, $course_id, $certificate_identifier);
					$this->email_model->notify_on_certificate_generate($user_id, $course_id);
					$certificate_link = base_url('uploads/certificates/' . $certificate_identifier . '.jpg');
				} else {
					$previous_data = $previous_data->row_array();
					$certificate_link = base_url('uploads/certificates/' . $previous_data['shareable_url']);
				}

				$response['is_completed'] = 1;
				$response['certificate_shareable_url'] = $certificate_link;
			} else {
				$response['is_completed'] = 0;
				$response['certificate_shareable_url'] = "";
			}
		} else {
			$response['addon_status'] = 'failed';
			$response['is_completed'] = 0;
			$response['certificate_shareable_url'] = "";
		}

		return $response;
	}
}
