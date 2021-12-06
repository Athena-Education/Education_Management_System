<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Lazyloaddata_model extends CI_Model
{

  // constructor
  function __construct()
  {
    parent::__construct();
  }

  // Servre side testing
  function courses($limit, $start, $col, $dir, $filter_data)
  {
    // MULTI INSTRUCTOR COURSE IDS
    $multi_instructor_course_ids = array();
    if ($filter_data['selected_instructor_id'] != "all") {
      $multi_instructor_course_ids = $this->crud_model->multi_instructor_course_ids_for_an_instructor($filter_data['selected_instructor_id']);
    }

    $this->db->limit($limit, $start);
    $this->db->order_by($col, $dir);
    // apply the filter data
    // check if the user is admin. Admin can not see the draft courses
    if (strtolower($this->session->userdata('role')) == 'admin') {
      $this->db->where("status !=", 'draft');
    }
    if ($filter_data['selected_category_id'] != 'all') {
      $this->db->where('sub_category_id', $filter_data['selected_category_id']);
    }
    if ($filter_data['selected_instructor_id'] != "all") {
      $this->db->where('user_id', $filter_data['selected_instructor_id']);
      if ($multi_instructor_course_ids && count($multi_instructor_course_ids)) {
        $this->db->or_where_in('id', $multi_instructor_course_ids);
      }
    }
    if ($filter_data['selected_price'] != "all") {
      if ($filter_data['selected_price'] == "paid") {
        $this->db->where('is_free_course', null);
      } elseif ($filter_data['selected_price'] == "free") {
        $this->db->where('is_free_course', 1);
      }
    }
    if ($filter_data['selected_status'] != "all") {
      $this->db->where('status', $filter_data['selected_status']);
    }
    $query = $this->db->get('course');
    if ($query->num_rows() > 0)
      return $query->result();
    else
      return null;
  }

  function course_search($limit, $start, $search, $col, $dir, $filter_data)
  {
    // MULTI INSTRUCTOR COURSE IDS
    $multi_instructor_course_ids = array();
    if ($filter_data['selected_instructor_id'] != "all") {
      $multi_instructor_course_ids = $this->crud_model->multi_instructor_course_ids_for_an_instructor($filter_data['selected_instructor_id']);
    }

    $this->db->like('title', $search);
    $this->db->limit($limit, $start);
    $this->db->order_by($col, $dir);
    // apply the filter data
    // check if the user is admin. Admin can not see the draft courses
    if (strtolower($this->session->userdata('role')) == 'admin') {
      $this->db->where("status !=", 'draft');
    }
    if ($filter_data['selected_category_id'] != 'all') {
      $this->db->where('sub_category_id', $filter_data['selected_category_id']);
    }
    if ($filter_data['selected_instructor_id'] != "all") {
      $this->db->where('user_id', $filter_data['selected_instructor_id']);
      if ($multi_instructor_course_ids && count($multi_instructor_course_ids)) {
        $this->db->or_where_in('id', $multi_instructor_course_ids);
      }
    }
    if ($filter_data['selected_price'] != "all") {
      if ($filter_data['selected_price'] == "paid") {
        $this->db->where('is_free_course', null);
      } elseif ($filter_data['selected_price'] == "free") {
        $this->db->where('is_free_course', 1);
      }
    }
    if ($filter_data['selected_status'] != "all") {
      $this->db->where('status', $filter_data['selected_status']);
    }

    $query = $this->db->get('course');
    if ($query->num_rows() > 0)
      return $query->result();
    else
      return null;
  }

  function course_search_count($search)
  {
    $query = $this
      ->db
      ->like('title', $search)
      ->get('course');

    return $query->num_rows();
  }

  function count_all_courses($filter_data = array())
  {
    // MULTI INSTRUCTOR COURSE IDS
    $multi_instructor_course_ids = array();
    if ($filter_data['selected_instructor_id'] != "all") {
      $multi_instructor_course_ids = $this->crud_model->multi_instructor_course_ids_for_an_instructor($filter_data['selected_instructor_id']);
    }

    // apply the filter data
    // check if the user is admin. Admin can not see the draft courses
    if (strtolower($this->session->userdata('role')) == 'admin') {
      $this->db->where("status !=", 'draft');
    }
    if ($filter_data['selected_category_id'] != 'all') {
      $this->db->where('sub_category_id', $filter_data['selected_category_id']);
    }

    if ($filter_data['selected_instructor_id'] != "all") {
      $this->db->where('user_id', $filter_data['selected_instructor_id']);
      if ($multi_instructor_course_ids && count($multi_instructor_course_ids)) {
        $this->db->or_where_in('id', $multi_instructor_course_ids);
      }
    }
    if ($filter_data['selected_price'] != "all") {
      if ($filter_data['selected_price'] == "paid") {
        $this->db->where('is_free_course', null);
      } elseif ($filter_data['selected_price'] == "free") {
        $this->db->where('is_free_course', 1);
      }
    }
    if ($filter_data['selected_status'] != "all") {
      $this->db->where('status', $filter_data['selected_status']);
    }
    $query = $this->db->get('course');
    return $query->num_rows();
  }
}
