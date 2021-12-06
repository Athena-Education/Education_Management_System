<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Api_instructor_model extends CI_Model
{

	// constructor
	function __construct()
	{
		parent::__construct();
		/*cache control*/
		$this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
		$this->output->set_header('Pragma: no-cache');
	}

	// Login mechanism
	public function login_post()
	{
		$response = array();
		$credential = array('email' => $_POST['email'], 'password' => sha1($_POST['password']), 'is_instructor' => 1, 'role_id' => 2, 'status' => 1);
		$query = $this->db->get_where('users', $credential);
		if ($query->num_rows() > 0) {
			$row = $query->row_array();
			$response['user_id'] = $row['id'];
			$response['first_name'] = $row['first_name'];
			$response['last_name'] = $row['last_name'];
			$response['email'] = $row['email'];
			$response['role'] = strtolower(get_user_role('user_role', $row['id']));
			$response['image'] = $this->user_model->get_user_image_url($row['id']);
			$response['validity'] = 1;
		} else {
			$response['status'] = 403;
			$response['validity'] = 0;
			$response['message'] = get_phrase('user_not_found');
		}
		return $response;
	}

	public function change_profile_photo_post($user_id = "")
	{
		$query = $this->user_model->get_all_user($user_id);

		if ($query->num_rows() > 0) {
			if (isset($_FILES['user_image']['name']) && !empty($_FILES['user_image']['name'])) {
				$data['image'] = md5(rand(10000, 99999));

				if (file_exists('uploads/user_image/' . $query->row('image') . 'jpg')) {
					unlink('uploads/user_image/' . $query->row('image') . 'jpg');
				}
				move_uploaded_file($_FILES['user_image']['tmp_name'], 'uploads/user_image/' . $data['image'] . '.jpg');

				$this->db->where('id', $user_id);
				$this->db->update('users', $data);

				$response['message'] = get_phrase('photo_uploaded_successfully');
				$response['status'] = 200;
				$response['validity'] = true;
			} else {
				$response['status'] = 403;
				$response['validity'] = false;
				$response['message'] = get_phrase('first_select_your_image');
			}
		} else {
			$response['status'] = 403;
			$response['validity'] = false;
			$response['message'] = get_phrase('user_not_found');
		}

		return $response;
	}

	public function change_password_post($user_id = "")
	{
		$response = array();

		$query = $this->user_model->get_user($user_id);
		if ($query->num_rows() > 0) {
			$user_details = $query->row_array();
			$current_password = $this->input->post('current_password');
			$new_password = $this->input->post('new_password');
			$confirm_password = $this->input->post('confirm_password');
			if ($user_details['password'] == sha1($current_password) && $new_password == $confirm_password) {
				$data['password'] = sha1($new_password);
				$this->db->where('id', $user_id);
				$this->db->update('users', $data);

				$response['status'] = 200;
				$response['validity'] = true;
				$response['message'] = get_phrase('password_changed');
			} else {
				$response['status'] = 403;
				$response['validity'] = false;
				$response['message'] = get_phrase('password_is_not_matching');
			}
		} else {
			$response['status'] = 403;
			$response['validity'] = false;
			$response['message'] = get_phrase('user_not_found');
		}

		return $response;
	}

	public function forgot_password_post($new_password = "")
	{
		$query = $this->db->get_where('users', array('email' => $_POST['email']));
		if ($query->num_rows() > 0) {
			$this->db->where('id', $query->row('id'));
			$this->db->update('users', array('password' => sha1($new_password)));
			return true;
		} else {
			return false;
		}
	}

	public function userdata_get($user_id = "")
	{
		$response = array();
		$credential = array('id' => $user_id, 'role_id' => '2', 'status' => 1);
		$query = $this->db->get_where('users', $credential);
		if ($query->num_rows() > 0) {
			$user_details = $query->row_array();
			$response['user_id'] = $user_details['id'];
			$response['first_name'] = $user_details['first_name'];
			$response['last_name'] = $user_details['last_name'];
			$response['email'] = $user_details['email'];
			$social_links = json_decode($user_details['social_links'], true);
			$response['facebook'] = $social_links['facebook'];
			$response['twitter'] = $social_links['twitter'];
			$response['linkedin'] = $social_links['linkedin'];
			$response['biography'] = strip_tags($user_details['biography']);
			$response['image'] = $this->user_model->get_user_image_url($user_details['id']);

			$response['message'] = get_phrase('userdata_successfully');
			$response['status'] = 200;
			$response['validity'] = 1;
		} else {
			$response['status'] = 403;
			$response['validity'] = 0;
			$response['message'] = get_phrase('user_not_found');
		}
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
				$response['status'] = 403;
				$response['validity'] = 0;
				$response['message'] = get_phrase('first_name_can_not_be_empty');
				return $response;
			}
			if (html_escape($this->input->post('last_name')) != "") {
				$data['last_name'] = html_escape($this->input->post('last_name'));
			} else {
				$response['status'] = 403;
				$response['validity'] = 0;
				$response['message'] = get_phrase('last_name_can_not_be_empty');
				return $response;
			}
			if (isset($_POST['email']) && html_escape($this->input->post('email')) != "") {
				$data['email'] = html_escape($this->input->post('email'));
			} else {
				$response['status'] = 403;
				$response['validity'] = 0;
				$response['message'] = get_phrase('email_can_not_be_empty');
				return $response;
			}

			$social_link['facebook'] = html_escape($this->input->post('facebook_link'));
			$social_link['twitter'] = html_escape($this->input->post('twitter_link'));
			$social_link['linkedin'] = html_escape($this->input->post('linkedin_link'));
			$data['social_links'] = json_encode($social_link);
			$data['biography'] = $this->input->post('biography');
			$this->db->where('id', $user_id);
			$this->db->update('users', $data);

			$response['status'] = 200;
			$response['validity'] = 1;
			$response['message'] = get_phrase('updated_successfully');
		} else {
			$response['status'] = 403;
			$response['validity'] = 0;
			$response['message'] = get_phrase('already_exist_this_email');
		}
		return $response;
	}


	public function courses_get($user_id = "")
	{
		$response = array();
		$multi_instructor_course_ids = $this->crud_model->multi_instructor_course_ids_for_an_instructor($user_id);

		$this->db->select('id');
		$this->db->select('title');
		$this->db->select('user_id');
		$this->db->select('status');

		$this->db->where('user_id', $user_id);
		if ($multi_instructor_course_ids && count($multi_instructor_course_ids)) {
			$this->db->or_where_in('id', $multi_instructor_course_ids);
		}

		$query = $this->db->get('course');
		$total_course_number = $query->num_rows();
		if ($total_course_number > 0) {
			$response['courses'] = $query->result_array();
			$response['total_courses'] = $total_course_number;
			$response['message'] = get_phrase('course_list');
			$response['status'] = 200;
			$response['validity'] = 1;
		} else {
			$response['message'] = get_phrase('course_not_found');
			$response['status'] = 403;
			$response['validity'] = 0;
		}

		return $response;
	}


	public function add_course_form_get()
	{
		$response = array();
		$all_categories = array();
		$all_languages = array();

		$languages = $this->crud_model->get_all_languages();
		foreach ($languages as $key => $language) {
			$language_arr['name'] = $languages[$key];
			$all_languages[$key] = $language_arr;
		}

		$this->db->select('id');
		$this->db->select('name');
		$this->db->where('parent >', 0);
		$all_categories = $this->db->get_where('category')->result_array();

		$response['categories'] = $all_categories;
		$response['languages'] = $all_languages;
		$response['status'] = 200;
		$response['validity'] = 1;
		$response['message'] = get_phrase('course_form_data');

		return $response;
	}

	public function add_course_post($user_id = "")
	{
		$response = array();

		$data['course_type'] = 'general';
		$data['title'] = html_escape($this->input->post('title'));
		$data['outcomes'] = '[]';
		$data['language'] = $this->input->post('language');
		$data['sub_category_id'] = $this->input->post('sub_category_id');
		$category_details = $this->crud_model->get_category_details_by_id($this->input->post('sub_category_id'))->row_array();
		$data['category_id'] = $category_details['parent'];

		$data['requirements'] = '[]';
		$data['price'] = $this->input->post('price');
		$data['discount_flag'] = $this->input->post('discount_flag');
		$data['discounted_price'] = $this->input->post('discounted_price');
		$data['level'] = $this->input->post('level');
		$data['is_top_course'] = $this->input->post('is_top_course');
		$data['is_free_course'] = $this->input->post('is_free_course');

		$data['date_added'] = strtotime(date('D, d-M-Y'));
		$data['section'] = json_encode(array());

		$data['user_id'] = $user_id;

		$data['is_admin'] = 0;

		$data['status'] = 'pending';

		if ($data['is_free_course'] == 1 || $data['is_free_course'] != 1 && $data['price'] > 0 && $data['discount_flag'] != 1 || $data['discount_flag'] == 1 && $data['discounted_price'] > 0) {
			$this->db->insert('course', $data);

			$response['message'] = get_phrase('course_has_been_added_successfully');
			$response['status'] = 200;
			$response['validity'] = 1;
		} else {
			$response['message'] = get_phrase('please_fill_up_the_price_field');
			$response['status'] = 403;
			$response['validity'] = 0;
		}

		return $response;
	}

	public function edit_course_form_get($course_id = "", $user_id = "")
	{
		$response = array();
		$all_categories = array();
		$all_languages = array();
		$course_details = $this->db->get_where('course', array('id' => $course_id))->row_array();

		$languages = $this->crud_model->get_all_languages();
		foreach ($languages as $key => $language) {
			$language_arr['name'] = $languages[$key];
			$all_languages[$key] = $language_arr;
		}

		$this->db->select('id');
		$this->db->select('name');
		$this->db->where('parent >', 0);
		$all_categories = $this->db->get_where('category')->result_array();
		$response['categories'] = $all_categories;
		$response['languages'] = $all_languages;

		$response['title'] = $course_details['title'];
		$response['short_description'] = $course_details['short_description'];
		$response['description'] = $course_details['description'];
		$response['sub_category_id'] = $course_details['sub_category_id'];
		$response['level'] = $course_details['level'];
		$response['language'] = $course_details['language'];
		$response['course_overview_provider'] = $course_details['course_overview_provider'];
		$response['video_url'] = $course_details['video_url'];
		$response['is_top_course'] = $course_details['is_top_course'];

		//Media section
		$course_media_images = array();
		$course_media_files = themeConfiguration(get_frontend_settings('theme'), 'course_media_files');
		foreach ($course_media_files as $course_media => $size) {
			$course_media_images[$course_media . '_size'] = $size;
			$course_media_images[$course_media] = $this->crud_model->get_course_thumbnail_url($course_details['id'], $course_media);
		}

		$response['theme'] = get_frontend_settings('theme');
		$response['course_media_images'] = $course_media_images;
		// End media section

		$response['meta_keywords'] = $course_details['meta_keywords'];
		$response['meta_description'] = $course_details['meta_description'];

		$response['status'] = 200;
		$response['validity'] = 1;
		$response['message'] = get_phrase('course_form_data');

		return $response;
	}

	public function update_course_post($course_id = "", $user_id = "")
	{
		$response = array();

		$course_details = $this->crud_model->get_course_by_id($course_id)->row_array();

		$data['title'] = $this->input->post('title');
		$data['short_description'] = html_escape($this->input->post('short_description'));
		$data['description'] = $this->input->post('description');
		$data['language'] = $this->input->post('language');
		$data['sub_category_id'] = $this->input->post('sub_category_id');
		$category_details = $this->crud_model->get_category_details_by_id($this->input->post('sub_category_id'))->row_array();
		$data['category_id'] = $category_details['parent'];
		$data['is_free_course'] = $this->input->post('is_free_course');
		$data['level'] = $this->input->post('level');
		$data['video_url'] = $this->input->post('course_overview_url');

		if ($this->input->post('course_overview_url') != "") {
			$data['course_overview_provider'] = html_escape($this->input->post('course_overview_provider'));
		} else {
			$data['course_overview_provider'] = "";
		}

		$data['meta_description'] = $this->input->post('meta_description');
		$data['meta_keywords'] = $this->input->post('meta_keywords');
		$data['last_modified'] = strtotime(date('D, d-M-Y'));

		if ($this->input->post('is_top_course') != 1) {
			$data['is_top_course'] = 0;
		} else {
			$data['is_top_course'] = 1;
		}


		$this->db->where('id', $course_id);
		$this->db->update('course', $data);

		// Upload different number of images according to activated theme. Data is taking from the config.json file
		$course_media_files = themeConfiguration(get_frontend_settings('theme'), 'course_media_files');
		foreach ($course_media_files as $course_media => $size) {
			if ($_FILES[$course_media]['name'] != "") {
				move_uploaded_file($_FILES[$course_media]['tmp_name'], 'uploads/thumbnails/course_thumbnails/' . $course_media . '_' . get_frontend_settings('theme') . '_' . $course_id . '.jpg');
			}
		}

		$response['message'] = get_phrase('course_updated_successfully');
		$response['status'] = 200;
		$response['validity'] = 1;

		return $response;
	}

	public function update_course_status_get($course_id = "", $status = "", $user_id = "")
	{
		if ($status == 'active') {
			$status = 'pending';
		}

		$updater = array('status' => $status);
		$this->db->where('id', $course_id);
		$this->db->update('course', $updater);

		$response['message'] = get_phrase('status_updated');
		$response['status'] = 200;
		$response['validity'] = 1;

		return $response;
	}

	public function edit_course_requirements_get($course_id = "", $user_id = "")
	{
		$this->db->where('id', $course_id);
		$requirements = $this->db->get('course')->row('requirements');

		$response['total_requirements'] = count(json_decode($requirements));
		$response['requirements'] = json_decode($requirements);
		$response['message'] = get_phrase('course_requirements');
		$response['status'] = 200;
		$response['validity'] = 1;

		return $response;
	}

	public function update_course_requirements_post($course_id = "", $user_id = "")
	{
		$requirements = json_decode($_POST['requirements'], true);
		$data['requirements'] = $this->crud_model->trim_and_return_json($requirements);
		$this->db->where('id', $course_id);
		$this->db->update('course', $data);

		$response['message'] = get_phrase('requirements_updated');
		$response['status'] = 200;
		$response['validity'] = 1;

		return $response;
	}

	public function edit_course_outcomes_get($course_id = "", $user_id = "")
	{
		$this->db->where('id', $course_id);
		$outcomes = $this->db->get('course')->row('outcomes');

		$response['total_outcomes'] = count(json_decode($outcomes));
		$response['outcomes'] = json_decode($outcomes);
		$response['message'] = get_phrase('course_outcomes');
		$response['status'] = 200;
		$response['validity'] = 1;

		return $response;
	}

	public function update_course_outcomes_post($course_id = "", $user_id = "")
	{
		$outcomes = json_decode($_POST['outcomes'], true);
		$data['outcomes'] = $this->crud_model->trim_and_return_json($outcomes);
		$this->db->where('id', $course_id);
		$this->db->update('course', $data);

		$response['message'] = get_phrase('outcomes_updated');
		$response['status'] = 200;
		$response['validity'] = 1;

		return $response;
	}

	public function delete_course_get($course_id = "", $user_id = "")
	{
		$response = array();
		$multi_instructor_course_ids = $this->crud_model->multi_instructor_course_ids_for_an_instructor($user_id);

		$this->db->where('id', $course_id);
		$this->db->where('user_id', $user_id);

		if ($multi_instructor_course_ids && count($multi_instructor_course_ids)) {
			$this->db->or_where_in('id', $multi_instructor_course_ids);
		}
		
		$query = $this->db->get('course');
		if ($query->num_rows() > 0) {
			$this->crud_model->delete_course($course_id);
			$response['message'] = get_phrase('course_deleted_successfully');
			$response['status'] = 200;
			$response['validity'] = 1;
		} else {
			$response['message'] = get_phrase('course_not_found');
			$response['status'] = 403;
			$response['validity'] = 0;
		}

		return $response;
	}

	public function section_and_lesson_get($course_id = "", $user_id = "")
	{
		$response = array();
		$section_and_lesson = array();

		$this->db->order_by('order', 'asc');
		$this->db->where('course_id', $course_id);
		$query = $this->db->get('section');
		if ($query->num_rows() > 0) {
			foreach ($query->result_array() as $key => $section) {
				$sections['id'] = $section['id'];
				$sections['title'] = $section['title'];

				$this->db->select('id');
				$this->db->select('video_type');
				$this->db->select('lesson_type');
				$this->db->select('attachment_type');
				$this->db->select('title');
				$this->db->order_by('order', 'asc');
				$this->db->where('section_id', $section['id']);
				$lessons = $this->db->get('lesson')->result_array();

				$section_and_lesson[$key] = $sections;
				$section_and_lesson[$key]['lessons'] = $lessons;
			}


			$response['section_and_lesson'] = $section_and_lesson;
			$response['course_id'] = $course_id;
			$response['message'] = get_phrase('course_sections');
			$response['status'] = 200;
			$response['validity'] = 1;
		} else {
			$response['message'] = get_phrase('section_not_found');
			$response['status'] = 403;
			$response['validity'] = 0;
		}

		return $response;
	}

	public function sections_get($course_id = "", $user_id = "")
	{
		$response = array();
		$sections = array();

		$this->db->order_by('order', 'asc');
		$this->db->where('course_id', $course_id);
		$query = $this->db->get('section');
		if ($query->num_rows() > 0) {
			$response['sections'] = $query->result_array();
			$response['message'] = get_phrase('course_sections');
			$response['status'] = 200;
			$response['validity'] = 1;
		} else {
			$response['message'] = get_phrase('section_not_found');
			$response['status'] = 403;
			$response['validity'] = 0;
		}

		return $response;
	}


	public function add_section_post($course_id = "", $user_id = "")
	{
		$response = array();

		$data['title'] = html_escape($this->input->post('title'));
		$data['course_id'] = $course_id;
		$this->db->insert('section', $data);
		$section_id = $this->db->insert_id();

		if ($section_id > 0) {
			$course_details = $this->crud_model->get_course_by_id($course_id)->row_array();
			$previous_sections = json_decode($course_details['section']);

			if (sizeof($previous_sections) > 0) {
				array_push($previous_sections, $section_id);
				$updater['section'] = json_encode($previous_sections);
				$this->db->where('id', $course_id);
				$this->db->update('course', $updater);
			} else {
				$previous_sections = array();
				array_push($previous_sections, $section_id);
				$updater['section'] = json_encode($previous_sections);
				$this->db->where('id', $course_id);
				$this->db->update('course', $updater);
			}

			$response['message'] = get_phrase('section_added');
			$response['status'] = 200;
			$response['validity'] = 1;
		} else {
			$response['message'] = get_phrase('section_not_added');
			$response['status'] = 403;
			$response['validity'] = 0;
		}

		return $response;
	}

	public function update_section_post($section_id = "", $user_id = "")
	{
		$response = array();

		if ($section_id > 0) {
			$data['title'] = $this->input->post('title');
			$this->db->where('id', $section_id);
			$this->db->update('section', $data);

			$response['message'] = get_phrase('section_updated');
			$response['status'] = 200;
			$response['validity'] = 1;
		} else {
			$response['message'] = get_phrase('section_not_updated');
			$response['status'] = 403;
			$response['validity'] = 0;
		}

		return $response;
	}

	public function delete_section_post($section_id = "", $course_id = "", $user_id = "")
	{
		$response = array();

		$this->db->where('id', $section_id);
		$this->db->delete('section');

		$this->db->where('section_id', $section_id);
		$this->db->delete('lesson');



		$course_details = $this->crud_model->get_course_by_id($course_id)->row_array();
		$previous_sections = json_decode($course_details['section']);

		if (sizeof($previous_sections) > 0) {
			$new_section = array();
			for ($i = 0; $i < sizeof($previous_sections); $i++) {
				if ($previous_sections[$i] != $section_id) {
					array_push($new_section, $previous_sections[$i]);
				}
			}
			$updater['section'] = json_encode($new_section);
			$this->db->where('id', $course_id);
			$this->db->update('course', $updater);
		}

		$response['message'] = get_phrase('section_deleted');
		$response['status'] = 200;
		$response['validity'] = 1;

		return $response;
	}

	//Start add lesson
	public function add_lesson_post($user_id = "")
	{
		$response = array();

		$data['course_id'] = html_escape($this->input->post('course_id'));
		$data['title'] = html_escape($this->input->post('title'));
		$data['section_id'] = html_escape($this->input->post('section_id'));

		$lesson_type_array = explode('-', $this->input->post('lesson_type'));
		$lesson_type = $lesson_type_array[0];

		$attachment_type = $lesson_type_array[1];
		$data['attachment_type'] = $attachment_type;
		$data['lesson_type'] = $lesson_type;

		if ($lesson_type == 'video') {
			// This portion is for web application's video lesson
			$lesson_provider = $this->input->post('lesson_provider');
			if ($lesson_provider == 'youtube' || $lesson_provider == 'vimeo') {
				$video_details = $this->video_model->getVideoDetails($_POST['video_url']);
				$duration_formatter = explode(':', $video_details['duration']);
				$hour = sprintf('%02d', $duration_formatter[0]);
				$min = sprintf('%02d', $duration_formatter[1]);
				$sec = sprintf('%02d', $duration_formatter[2]);
				$data['duration'] = $hour . ':' . $min . ':' . $sec;

				if ($this->input->post('video_url') == "" || $hour <= 0 && $min <= 0 && $sec <= 0) {
					$response['message'] = get_phrase('invalid_lesson_url');
					$response['status'] = 403;
					$response['validity'] = 0;
					return $response;
					die();
				}
				$data['video_url'] = html_escape($this->input->post('video_url'));
				$data['video_type'] = $video_details['provider'];
			} elseif ($lesson_provider == 'html5') {
				if ($this->input->post('html5_video_url') == "" || $this->input->post('html5_duration') == "") {
					$response['message'] = get_phrase('invalid_lesson_url');
					$response['status'] = 403;
					$response['validity'] = 0;
					return $response;
					die();
				}
				$data['video_url'] = html_escape($this->input->post('html5_video_url'));
				$duration_formatter = explode(':', $this->input->post('html5_duration'));
				$hour = sprintf('%02d', $duration_formatter[0]);
				$min = sprintf('%02d', $duration_formatter[1]);
				$sec = sprintf('%02d', $duration_formatter[2]);
				$data['duration'] = $hour . ':' . $min . ':' . $sec;
				$data['video_type'] = 'html5';
			} else {
				$response['message'] = get_phrase('invalid_lesson_provider');
				$response['status'] = 403;
				$response['validity'] = 0;
				return $response;
				die();
			}

			// // This portion is for mobile application video lessons
			// if ($this->input->post('html5_video_url_for_mobile_application') == "" || $this->input->post('html5_duration_for_mobile_application') == "") {
			//     $mobile_app_lesson_url = "https://www.html5rocks.com/en/tutorials/video/basics/devstories.webm";
			//     $mobile_app_lesson_duration = "00:01:10";
			// }else{
			//     $mobile_app_lesson_url = $this->input->post('html5_video_url_for_mobile_application');
			//     $mobile_app_lesson_duration = $this->input->post('html5_duration_for_mobile_application');
			// }
			// $duration_for_mobile_application_formatter = explode(':', $mobile_app_lesson_duration);
			// $hour = sprintf('%02d', $duration_for_mobile_application_formatter[0]);
			// $min  = sprintf('%02d', $duration_for_mobile_application_formatter[1]);
			// $sec  = sprintf('%02d', $duration_for_mobile_application_formatter[2]);
			// $data['duration_for_mobile_application'] = $hour . ':' . $min . ':' . $sec;
			// $data['video_type_for_mobile_application'] = 'html5';
			// $data['video_url_for_mobile_application'] = $mobile_app_lesson_url;
		} elseif ($lesson_type == "s3") {
			// SET MAXIMUM EXECUTION TIME 600
			ini_set('max_execution_time', '600');

			$fileName           = $_FILES['video_file_for_amazon_s3']['name'];
			$tmp                = explode('.', $fileName);
			$fileExtension      = strtoupper(end($tmp));

			$video_extensions = ['WEBM', 'MP4'];
			if (!in_array($fileExtension, $video_extensions)) {
				$response['message'] = get_phrase('please_select_valid_video_file');
				$response['status'] = 403;
				$response['validity'] = 0;
				return $response;
				die();
			}

			if ($this->input->post('amazon_s3_duration') == "") {
				$response['message'] = get_phrase('invalid_lesson_duration');
				$response['status'] = 403;
				$response['validity'] = 0;
				return $response;
				die();
			}

			$upload_loaction = get_settings('video_upload_location');
			$access_key = get_settings('amazon_s3_access_key');
			$secret_key = get_settings('amazon_s3_secret_key');
			$bucket = get_settings('amazon_s3_bucket_name');
			$region = get_settings('amazon_s3_region_name');

			$s3config = array(
				'region'  => $region,
				'version' => 'latest',
				'credentials' => [
					'key'    => $access_key, //Put key here
					'secret' => $secret_key // Put Secret here
				]
			);


			$tmpfile = $_FILES['video_file_for_amazon_s3'];

			$s3 = new Aws\S3\S3Client($s3config);
			$key = str_replace(".", "-" . rand(1, 9999) . ".", $tmpfile['name']);

			$result = $s3->putObject([
				'Bucket' => $bucket,
				'Key'    => $key,
				'SourceFile' => $tmpfile['tmp_name'],
				'ACL'   => 'public-read'
			]);

			$data['video_url'] = $result['ObjectURL'];
			$data['video_type'] = 'amazon';
			$data['lesson_type'] = 'video';
			$data['attachment_type'] = 'file';

			$duration_formatter = explode(':', $this->input->post('amazon_s3_duration'));
			$hour = sprintf('%02d', $duration_formatter[0]);
			$min = sprintf('%02d', $duration_formatter[1]);
			$sec = sprintf('%02d', $duration_formatter[2]);
			$data['duration'] = $hour . ':' . $min . ':' . $sec;

			$data['duration_for_mobile_application'] = $hour . ':' . $min . ':' . $sec;
			$data['video_type_for_mobile_application'] = "html5";
			$data['video_url_for_mobile_application'] = $result['ObjectURL'];
		} elseif ($lesson_type == "system") {
			// SET MAXIMUM EXECUTION TIME 600
			ini_set('max_execution_time', '600');

			$fileName           = $_FILES['system_video_file']['name'];

			// CHECKING IF THE FILE IS AVAILABLE AND FILE SIZE IS VALID
			if (array_key_exists('system_video_file', $_FILES)) {
				if ($_FILES['system_video_file']['error'] !== UPLOAD_ERR_OK) {
					$error_code = $_FILES['system_video_file']['error'];
					$response['message'] = phpFileUploadErrors($error_code);
					$response['status'] = 403;
					$response['validity'] = 0;
					return $response;
					die();
				}
			} else {
				$response['message'] = get_phrase('please_select_valid_video_file');
				$response['status'] = 403;
				$response['validity'] = 0;
				return $response;
				die();
			};

			$tmp                = explode('.', $fileName);
			$fileExtension      = strtoupper(end($tmp));

			$video_extensions = ['WEBM', 'MP4'];

			if (!in_array($fileExtension, $video_extensions)) {
				$response['message'] = get_phrase('please_select_valid_video_file');
				$response['status'] = 403;
				$response['validity'] = 0;
				return $response;
				die();
			}

			// custom random name of the video file
			$uploadable_video_file    =  md5(uniqid(rand(), true)) . '.' . strtolower($fileExtension);

			if ($this->input->post('system_video_file_duration') == "") {
				$response['message'] = get_phrase('please_select_valid_video_file');
				$response['status'] = 403;
				$response['validity'] = 0;
				return $response;
				die();
			}



			$tmp_video_file = $_FILES['system_video_file']['tmp_name'];

			if (!file_exists('uploads/lesson_files/videos')) {
				mkdir('uploads/lesson_files/videos', 0777, true);
			}
			$video_file_path = 'uploads/lesson_files/videos/' . $uploadable_video_file;
			move_uploaded_file($tmp_video_file, $video_file_path);
			$data['video_url'] = site_url($video_file_path);
			$data['video_type'] = 'system';
			$data['lesson_type'] = 'video';
			$data['attachment_type'] = 'file';

			$duration_formatter = explode(':', $this->input->post('system_video_file_duration'));
			$hour = sprintf('%02d', $duration_formatter[0]);
			$min = sprintf('%02d', $duration_formatter[1]);
			$sec = sprintf('%02d', $duration_formatter[2]);
			$data['duration'] = $hour . ':' . $min . ':' . $sec;

			$data['duration_for_mobile_application'] = $hour . ':' . $min . ':' . $sec;
			$data['video_type_for_mobile_application'] = "html5";
			$data['video_url_for_mobile_application'] = site_url($video_file_path);
		} else {
			if ($attachment_type == 'iframe') {
				if (empty($this->input->post('iframe_source'))) {
					$response['message'] = get_phrase('invalid_source');
					$response['status'] = 403;
					$response['validity'] = 0;
					return $response;
					die();
				}
				$data['attachment'] = $this->input->post('iframe_source');
			} else {
				if ($_FILES['attachment']['name'] == "") {
					$response['message'] = get_phrase('invalid_attachment');
					$response['status'] = 403;
					$response['validity'] = 0;
					return $response;
					die();
				} else {
					$fileName           = $_FILES['attachment']['name'];
					$tmp                = explode('.', $fileName);
					$fileExtension      = end($tmp);
					$uploadable_file    =  md5(uniqid(rand(), true)) . '.' . $fileExtension;
					$data['attachment'] = $uploadable_file;

					if (!file_exists('uploads/lesson_files')) {
						mkdir('uploads/lesson_files', 0777, true);
					}
					move_uploaded_file($_FILES['attachment']['tmp_name'], 'uploads/lesson_files/' . $uploadable_file);
				}
			}
		}

		$data['date_added'] = strtotime(date('D, d-M-Y'));
		$data['summary'] = $this->input->post('summary');

		$this->db->insert('lesson', $data);
		$inserted_id = $this->db->insert_id();

		if (isset($_FILES['thumbnail']['name']) && $_FILES['thumbnail']['name'] != "") {
			if (!file_exists('uploads/thumbnails/lesson_thumbnails')) {
				mkdir('uploads/thumbnails/lesson_thumbnails', 0777, true);
			}
			move_uploaded_file($_FILES['thumbnail']['tmp_name'], 'uploads/thumbnails/lesson_thumbnails/' . $inserted_id . '.jpg');
		}

		$response['message'] = get_phrase('lesson_added');
		$response['status'] = 200;
		$response['validity'] = 1;
		return $response;
	}
	//End add lesson

	public function lesson_all_data_get($lesson_id = "", $user_id = "")
	{
		$response = array();
		$lesson_id = $_GET['lesson_id'];
		$response['lesson_details'] = $this->db->get_where('lesson', array('id' => $lesson_id))->row_array();
		$response['message'] = get_phrase('lesson_data');
		$response['status'] = 200;
		$response['validity'] = 1;
		return $response;
	}

	public function update_lesson_post($lesson_id = "", $user_id = "")
	{

		$response = array();

		$data['title'] = html_escape($this->input->post('title'));
		$data['section_id'] = html_escape($this->input->post('section_id'));

		$lesson_type_array = explode('-', $this->input->post('lesson_type'));
		$lesson_type = $lesson_type_array[0];

		$attachment_type = $lesson_type_array[1];
		$data['attachment_type'] = $attachment_type;
		$data['lesson_type'] = $lesson_type;

		if ($lesson_type == 'video') {
			// This portion is for web application's video lesson
			$lesson_provider = $this->input->post('lesson_provider');
			if ($lesson_provider == 'youtube' || $lesson_provider == 'vimeo') {
				$video_details = $this->video_model->getVideoDetails($_POST['video_url']);
				$duration_formatter = explode(':', $video_details['duration']);
				$hour = sprintf('%02d', $duration_formatter[0]);
				$min = sprintf('%02d', $duration_formatter[1]);
				$sec = sprintf('%02d', $duration_formatter[2]);
				$data['duration'] = $hour . ':' . $min . ':' . $sec;

				if ($this->input->post('video_url') == "" || $hour <= 0 && $min <= 0 && $sec <= 0) {
					$response['message'] = get_phrase('invalid_lesson_url');
					$response['status'] = 403;
					$response['validity'] = 0;
					return $response;
					die();
				}
				$data['video_url'] = html_escape($this->input->post('video_url'));
				$data['video_type'] = $video_details['provider'];
			} elseif ($lesson_provider == 'html5') {
				if ($this->input->post('html5_video_url') == "" || $this->input->post('html5_duration') == "") {
					$response['message'] = get_phrase('invalid_lesson_url');
					$response['status'] = 403;
					$response['validity'] = 0;
					return $response;
					die();
				}
				$data['video_url'] = html_escape($this->input->post('html5_video_url'));
				$duration_formatter = explode(':', $this->input->post('html5_duration'));
				$hour = sprintf('%02d', $duration_formatter[0]);
				$min = sprintf('%02d', $duration_formatter[1]);
				$sec = sprintf('%02d', $duration_formatter[2]);
				$data['duration'] = $hour . ':' . $min . ':' . $sec;
				$data['video_type'] = 'html5';
			} else {
				$response['message'] = get_phrase('invalid_lesson_provider');
				$response['status'] = 403;
				$response['validity'] = 0;
				return $response;
				die();
			}

			// // This portion is for mobile application video lessons
			// if ($this->input->post('html5_video_url_for_mobile_application') == "" || $this->input->post('html5_duration_for_mobile_application') == "") {
			//     $mobile_app_lesson_url = "https://www.html5rocks.com/en/tutorials/video/basics/devstories.webm";
			//     $mobile_app_lesson_duration = "00:01:10";
			// }else{
			//     $mobile_app_lesson_url = $this->input->post('html5_video_url_for_mobile_application');
			//     $mobile_app_lesson_duration = $this->input->post('html5_duration_for_mobile_application');
			// }
			// $duration_for_mobile_application_formatter = explode(':', $mobile_app_lesson_duration);
			// $hour = sprintf('%02d', $duration_for_mobile_application_formatter[0]);
			// $min  = sprintf('%02d', $duration_for_mobile_application_formatter[1]);
			// $sec  = sprintf('%02d', $duration_for_mobile_application_formatter[2]);
			// $data['duration_for_mobile_application'] = $hour . ':' . $min . ':' . $sec;
			// $data['video_type_for_mobile_application'] = 'html5';
			// $data['video_url_for_mobile_application'] = $mobile_app_lesson_url;
		} elseif ($lesson_type == "s3") {
			// SET MAXIMUM EXECUTION TIME 600
			ini_set('max_execution_time', '600');

			$fileName           = $_FILES['video_file_for_amazon_s3']['name'];
			$tmp                = explode('.', $fileName);
			$fileExtension      = strtoupper(end($tmp));

			$video_extensions = ['WEBM', 'MP4'];
			if (!in_array($fileExtension, $video_extensions)) {
				$response['message'] = get_phrase('please_select_valid_video_file');
				$response['status'] = 403;
				$response['validity'] = 0;
				return $response;
				die();
			}

			if ($this->input->post('amazon_s3_duration') == "") {
				$response['message'] = get_phrase('invalid_lesson_duration');
				$response['status'] = 403;
				$response['validity'] = 0;
				return $response;
				die();
			}

			$upload_loaction = get_settings('video_upload_location');
			$access_key = get_settings('amazon_s3_access_key');
			$secret_key = get_settings('amazon_s3_secret_key');
			$bucket = get_settings('amazon_s3_bucket_name');
			$region = get_settings('amazon_s3_region_name');

			$s3config = array(
				'region'  => $region,
				'version' => 'latest',
				'credentials' => [
					'key'    => $access_key, //Put key here
					'secret' => $secret_key // Put Secret here
				]
			);


			$tmpfile = $_FILES['video_file_for_amazon_s3'];

			$s3 = new Aws\S3\S3Client($s3config);
			$key = str_replace(".", "-" . rand(1, 9999) . ".", $tmpfile['name']);

			$result = $s3->putObject([
				'Bucket' => $bucket,
				'Key'    => $key,
				'SourceFile' => $tmpfile['tmp_name'],
				'ACL'   => 'public-read'
			]);

			$data['video_url'] = $result['ObjectURL'];
			$data['video_type'] = 'amazon';
			$data['lesson_type'] = 'video';
			$data['attachment_type'] = 'file';

			$duration_formatter = explode(':', $this->input->post('amazon_s3_duration'));
			$hour = sprintf('%02d', $duration_formatter[0]);
			$min = sprintf('%02d', $duration_formatter[1]);
			$sec = sprintf('%02d', $duration_formatter[2]);
			$data['duration'] = $hour . ':' . $min . ':' . $sec;

			$data['duration_for_mobile_application'] = $hour . ':' . $min . ':' . $sec;
			$data['video_type_for_mobile_application'] = "html5";
			$data['video_url_for_mobile_application'] = $result['ObjectURL'];
		} elseif ($lesson_type == "system") {
			// SET MAXIMUM EXECUTION TIME 600
			ini_set('max_execution_time', '600');

			$fileName           = $_FILES['system_video_file']['name'];

			// CHECKING IF THE FILE IS AVAILABLE AND FILE SIZE IS VALID
			if (array_key_exists('system_video_file', $_FILES)) {
				if ($_FILES['system_video_file']['error'] !== UPLOAD_ERR_OK) {
					$error_code = $_FILES['system_video_file']['error'];
					$response['message'] = phpFileUploadErrors($error_code);
					$response['status'] = 403;
					$response['validity'] = 0;
					return $response;
					die();
				}
			}


			if ($fileName != "") {
				// custom random name of the video file
				$tmp                = explode('.', $fileName);
				$fileExtension      = strtoupper(end($tmp));
				$uploadable_video_file    =  md5(uniqid(rand(), true)) . '.' . strtolower($fileExtension);
				$video_extensions = ['WEBM', 'MP4'];

				if (!in_array($fileExtension, $video_extensions)) {
					$response['message'] = get_phrase('please_select_valid_video_file');
					$response['status'] = 403;
					$response['validity'] = 0;
					return $response;
					die();
				}

				$tmp_video_file = $_FILES['system_video_file']['tmp_name'];

				if (!file_exists('uploads/lesson_files/videos')) {
					mkdir('uploads/lesson_files/videos', 0777, true);
				}
				$video_file_path = 'uploads/lesson_files/videos/' . $uploadable_video_file;
				move_uploaded_file($tmp_video_file, $video_file_path);
				$data['video_url'] = site_url($video_file_path);
			}

			if ($this->input->post('system_video_file_duration') == "") {
				$response['message'] = get_phrase('please_enter_video_duration');
				$response['status'] = 403;
				$response['validity'] = 0;
				return $response;
				die();
			}

			$data['video_type'] = 'system';
			$data['lesson_type'] = 'video';
			$data['attachment_type'] = 'file';

			$duration_formatter = explode(':', $this->input->post('system_video_file_duration'));
			$hour = sprintf('%02d', $duration_formatter[0]);
			$min = sprintf('%02d', $duration_formatter[1]);
			$sec = sprintf('%02d', $duration_formatter[2]);
			$data['duration'] = $hour . ':' . $min . ':' . $sec;

			$data['duration_for_mobile_application'] = $hour . ':' . $min . ':' . $sec;
			$data['video_type_for_mobile_application'] = "html5";
			$data['video_url_for_mobile_application'] = site_url($video_file_path);
		} else {
			if ($attachment_type == 'iframe') {
				if (empty($this->input->post('iframe_source'))) {
					$response['message'] = get_phrase('invalid_source');
					$response['status'] = 403;
					$response['validity'] = 0;
					return $response;
					die();
				}
				$data['attachment'] = $this->input->post('iframe_source');
			} else {
				if ($_FILES['attachment']['name'] != "") {
					$fileName           = $_FILES['attachment']['name'];
					$tmp                = explode('.', $fileName);
					$fileExtension      = end($tmp);
					$uploadable_file    =  md5(uniqid(rand(), true)) . '.' . $fileExtension;
					$data['attachment'] = $uploadable_file;

					if (!file_exists('uploads/lesson_files')) {
						mkdir('uploads/lesson_files', 0777, true);
					}
					move_uploaded_file($_FILES['attachment']['tmp_name'], 'uploads/lesson_files/' . $uploadable_file);
				}
			}
		}

		$data['date_added'] = strtotime(date('D, d-M-Y'));
		$data['summary'] = $this->input->post('summary');

		$this->db->where('id', $lesson_id);
		$this->db->update('lesson', $data);

		if (isset($_FILES['thumbnail']['name']) && $_FILES['thumbnail']['name'] != "") {
			if (!file_exists('uploads/thumbnails/lesson_thumbnails')) {
				mkdir('uploads/thumbnails/lesson_thumbnails', 0777, true);
			}
			move_uploaded_file($_FILES['thumbnail']['tmp_name'], 'uploads/thumbnails/lesson_thumbnails/' . $inserted_id . '.jpg');
		}

		$response['message'] = get_phrase('lesson_updated');
		$response['status'] = 200;
		$response['validity'] = 1;
		return $response;
	}

	public function delete_lesson_get($lesson_id = "", $user_id = "")
	{
		$response = array();

		$this->db->where('id', $lesson_id);
		$this->db->delete('lesson');
		$response['message'] = get_phrase('lesson_deleted');
		$response['status'] = 200;
		$response['validity'] = 1;
		return $response;
	}

	public function sort_post($user_id = "", $type = "")
	{
		$response = array();
		$item_json = $this->input->post('item_json');
		if ($type == "section") {
			$this->crud_model->sort_section($item_json);
		} elseif ($type == "lesson") {
			$this->crud_model->sort_lesson($item_json);
		}

		$response['message'] = get_phrase('item_sorted');
		$response['status'] = 200;
		$response['validity'] = 1;
		return $response;
	}

	//End  lesson


	public function course_pricing_form_get($user_id = "", $course_id = "")
	{
		$response = array();
		$this->db->where('id', $course_id);
		$course_details = $this->db->get('course')->row_array();

		// $course_pricing['id'] = $course_details['id'];
		// $course_pricing['price'] = $course_details['price'];
		// if($course_details['discount_flag'] == null){
		// 	$course_pricing['discount_flag'] = "0";
		// }else{
		// 	$course_pricing['discount_flag'] = $course_details['discount_flag'];
		// }
		// $course_pricing['discounted_price'] = $course_details['discounted_price'];

		// if($course_pricing['is_free_course'] == null){
		// 	$course_pricing['is_free_course'] = "0";
		// }else{
		// 	$course_pricing['is_free_course'] = $course_details['is_free_course'];
		// }

		$course_pricing['id'] = $course_details['id'];
		$course_pricing['price'] = $course_details['price'];
		$course_pricing['discount_flag'] = $course_details['discount_flag'];
		$course_pricing['discounted_price'] = $course_details['discounted_price'];
		$course_pricing['is_free_course'] = $course_details['is_free_course'];


		$response['course_details'] = $course_pricing;
		$response['message'] = get_phrase('course_pricing');
		$response['status'] = 200;
		$response['validity'] = 1;
		return $response;
	}

	public function update_course_price_post($user_id = "", $course_id = "")
	{
		$response = array();

		$data['price'] = $_POST['price'];
		$data['discount_flag'] = $_POST['discount_flag'];
		$data['discounted_price'] = $_POST['discounted_price'];
		$data['is_free_course'] = $_POST['is_free_course'];

		$this->db->where('id', $course_id);
		$this->db->update('course', $data);

		$response['message'] = get_phrase('course_pricing_updated');
		$response['status'] = 200;
		$response['validity'] = 1;
		return $response;
	}

	public function sales_report_get($user_id = "")
	{
		$response = array();

		if (isset($_GET['date_range']) && !empty($_GET['date_range'])) {
			$date_range = explode('_', $_GET['date_range']);
			$start_date = strtotime($date_range[0]);
			$end_date = strtotime($date_range[1] . ' 23:59:59');
		} else {
			$start_date = strtotime(date('d M Y'));
			$end_date = strtotime(date('d M Y 23:59:59'));
		}

		$sales_report = $this->crud_model->get_instructor_revenue($user_id, $start_date, $end_date);

		$response['sales_report'] = $sales_report;
		$response['message'] = get_phrase('sales_reports');
		$response['status'] = 200;
		$response['validity'] = 1;
		return $response;
	}

	public function details_of_sales_report_get($payment_id = "")
	{
		$response = array();

		$this->db->where('id', $payment_id);
		$payment_details = $this->db->get('payment')->row_array();

		$student_details = $this->db->get_where('users', array('id' => $payment_details['user_id']))->row_array();

		$response['payment_details'] = $payment_details;
		$response['payment_date'] = date('d M Y', $payment_details['date_added']);
		$response['course_title'] = $this->db->get_where('course', array('id' => $payment_details['course_id']))->row('title');
		$response['enrolled_student'] = $student_details['first_name'] . ' ' . $student_details['last_name'];
		$response['message'] = get_phrase('sales_reports');
		$response['status'] = 200;
		$response['validity'] = 1;
		return $response;
	}

	public function payout_report_get($user_id = "")
	{
		$response = array();

		$response['payouts'] = $this->crud_model->get_payouts($user_id, 'user')->result_array();
		$response['total_pending_amoun'] = $this->crud_model->get_total_pending_amount($user_id);
		$response['total_payout_amount'] = $this->crud_model->get_total_payout_amount($user_id);
		$response['requested_withdrawal_amount'] = strval($this->crud_model->get_requested_withdrawal_amount($user_id));

		$response['message'] = get_phrase('payout_reports');
		$response['status'] = 200;
		$response['validity'] = 1;
		return $response;
	}

	public function add_withdrawal_request_post($user_id = "")
	{
		$response = array();

		$total_pending_amount = $this->crud_model->get_total_pending_amount($user_id);
		$requested_withdrawal_amount = $this->input->post('withdrawal_amount');

		if ($total_pending_amount > 0 && $total_pending_amount >= $requested_withdrawal_amount) {
			$data['amount']     = $requested_withdrawal_amount;
			$data['user_id']    = $user_id;
			$data['date_added'] = strtotime(date('D, d M Y'));
			$data['status']     = 0;
			$this->db->insert('payout', $data);

			$response['message'] = get_phrase('withdrawal_requested');
			$response['status'] = 200;
			$response['validity'] = 1;
		} else {
			$response['message'] = get_phrase('invalid_withdrawal_amount');
			$response['status'] = 403;
			$response['validity'] = 0;
		}

		return $response;
	}

	public function delete_withdrawal_request_get($user_id = "")
	{
		$response = array();

		$checker = array(
			'user_id' => $user_id,
			'status' => 0
		);
		$requested_withdrawal = $this->db->get_where('payout', $checker);
		if ($requested_withdrawal->num_rows() > 0) {
			$this->db->where($checker);
			$this->db->delete('payout');

			$response['message'] = get_phrase('withdrawal_deleted');
			$response['status'] = 200;
			$response['validity'] = 1;
		} else {
			$response['message'] = get_phrase('withdrawal_not_found');
			$response['status'] = 403;
			$response['validity'] = 0;
		}

		return $response;
	}
}
