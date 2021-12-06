<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Addon_model extends CI_Model
{

	function __construct()
	{
		parent::__construct();
	}

	public function install_addon($param1 = "") {

		// CHECK ADDON PURCHASE CODE ONLY FOR INSTALLING
		if ($param1 == 'install'){
			if($_SERVER['SERVER_NAME'] == 'localhost' || $_SERVER['SERVER_NAME'] == '127.0.0.1'){
				//Local server
				$purchase_code = null;
			}else{
				$purchase_code = $this->input->post('purchase_code');

				// $addon_exits = $this->get_addon_by_purchase_code($purchase_code);
				// if($addon_exits->num_rows() > 0){
				// 	$this->session->set_flashdata('error_message', get_phrase('you_have_already_used_this_purchase_code'));
				// 	redirect(site_url('admin/addon'), 'refresh');
				// }

				$status_response = $this->crud_model->curl_request($purchase_code);
				if(!$status_response){
					$this->session->set_flashdata('error_message', get_phrase('purchase_code_is_wrong').'. '.get_phrase('please_enter_your_valid_purchase_code'));
					redirect(site_url('admin/addon'), 'refresh');
				}
			}
		}

		// CHECK IF THE ADDON FOLDER INSIDE CONTROLLERS EXISTS
		if (!is_dir('application/controllers/addons')){
			mkdir("application/controllers/addons", 0777, true);
		}

		// CHECK IF THE ADDON FOLDER INSIDE MODELS EXISTS
		if (!is_dir('application/models/addons')){
			mkdir("application/models/addons", 0777, true);
		}

		$zipped_file_name = $_FILES['addon_zip']['name'];

		if (!empty($zipped_file_name)) {
			// Create update directory.
			$dir = 'uploads/addons';
			if (!is_dir($dir))
			mkdir($dir, 0777, true);

			$path = "uploads/addons/".$zipped_file_name;
			if (class_exists('ZipArchive')) {
				move_uploaded_file($_FILES['addon_zip']['tmp_name'], $path);
				//Unzip uploaded update file and remove zip file.
				$zip = new ZipArchive;
				$zip->open($path);
				$zip->extractTo('uploads/addons');
				$zip->close();
				unlink($path);
			}else{
				$this->session->set_flashdata('error_message', get_phrase('your_server_is_unable_to_extract_the_zip_file').'. '.get_phrase('please_enable_the_zip_extension_on_your_server').', '.get_phrase('then_try_again'));
				redirect(site_url('admin/addon'), 'refresh');
			}

			$unzipped_file_name = substr($zipped_file_name, 0, -4);
			$config_str = file_get_contents('uploads/addons/' . $unzipped_file_name . '/config.json');
			$config = json_decode($config_str, true);

			// CREATE DIRECTORIES
			if (!empty($config['directories'])) {
				foreach ($config['directories'] as $directory) {
					if (!is_dir($directory['name'])){
						mkdir($directory['name'], 0777, true);
					}
				}
			}

			// CREATE OR REPLACE NEW FILES
			if (!empty($config['files'])) {
				foreach ($config['files'] as $file){
					copy($file['root_directory'], $file['update_directory']);
				}
			}

			// CREATE OR REPLACE NEW LIBRARIES
			if (!empty($config['libraries'])) {
				foreach ($config['libraries'] as $libraries){
					copy($libraries['root_directory'], $libraries['update_directory']);

					//Unzip zip file and remove zip file.
					$library_path = $libraries['update_directory'];

					// PATH OF EXTRACTING LIBRARY FILE
					$library_path_array = explode('/', $library_path);
					array_pop($library_path_array);
					$extract_to = implode('/', $library_path_array);
					$library_zip = new ZipArchive;
					$library_result = $library_zip->open($library_path);
					$library_zip->extractTo($extract_to);
					$library_zip->close();
					unlink($library_path);
				}
			}

			// EXECUTE THE SQL FILE
			if (!empty($config['sql_file'])) {
				require './uploads/addons/'.$unzipped_file_name.'/sql/'.$config['sql_file'];
			}

			// INSERT OR UPDATE AN ENTRY ON DATABASE

			$data['name'] = $config['name'];
			$data['unique_identifier'] = $config['unique_identifier'];
			$data['version'] = $config['version'];
			$data['about'] = $config['about'];
			$data['status'] = 1;

			//CHECK IF THE ADDON IS ALREADY INSTALLED OR NOT BY UNIQUE IDENTIFIER.
			$addon_details = $this->db->get_where('addons', array('unique_identifier' => $data['unique_identifier']));

			if ($addon_details->num_rows() > 0) {
				$data['updated_at'] = strtotime(date('d-m-y'));
				$this->db->where('unique_identifier', $data['unique_identifier']);
				$this->db->update('addons', $data);

			}else{
				$data['purchase_code'] = $purchase_code;
				$data['created_at'] = strtotime(date('d-m-y'));
				$this->db->insert('addons', $data);
			}

			$this->remove_from_uploads($unzipped_file_name);

			if ($param1 == 'install') {
				$this->session->set_flashdata('flash_message', get_phrase('addon_installed_successfully'));
			}else{
				$this->session->set_flashdata('flash_message', get_phrase('addon_updated_successfully'));
			}
			redirect(site_url('admin/addon'), 'refresh');
		}else{
			$this->session->set_flashdata('error_message', get_phrase('no_addon_found'));
			redirect(site_url('admin/addon'), 'refresh');
		}

		return json_encode($response);
	}

	public function remove_from_uploads($folder_name) {
		$dir = 'uploads/addons/'.$folder_name;
		$it = new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS);
		$files = new RecursiveIteratorIterator($it,
		RecursiveIteratorIterator::CHILD_FIRST);
		foreach($files as $file) {
			if ($file->isDir()){
				rmdir($file->getRealPath());
			} else {
				unlink($file->getRealPath());
			}
		}
		rmdir($dir);
	}

	public function addon_activate($addon_id = ""){
		$check_addon_status = $this->db->get_where('addons', array('id' => $addon_id))->row('status');
		if($check_addon_status == 0):
			$data['status'] = 1;
			$this->db->where('id', $addon_id);
			$this->db->update('addons', $data);
			return "addon_is_activated_successfully";
		else:
			return "addon_is_already_activated";
		endif;
	}

	public function addon_deactivate($addon_id = ""){
		$check_addon_status = $this->db->get_where('addons', array('id' => $addon_id))->row('status');
		if($check_addon_status == 1):
			$data['status'] = 0;
			$this->db->where('id', $addon_id);
			$this->db->update('addons', $data);
			return "addon_is_deactivated_successfully";
		else:
			return "addon_is_already_deactivated";
		endif;
	}

	public function addon_delete($addon_id = ""){
		$this->db->where('id', $addon_id);
		$this->db->delete('addons');
	}

	public function addon_list($unique_identifier = ""){
		if($unique_identifier != ""){
			$this->db->where('unique_identifier', $unique_identifier);
		}
		return $this->db->get('addons');
	}

	public function get_addon_by_purchase_code($purchase_code = ""){
		if($purchase_code != ""){
			$this->db->where('purchase_code', $purchase_code);
		}
		return $this->db->get('addons');
	}

}
