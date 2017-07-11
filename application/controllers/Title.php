<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Title extends CI_Controller {
	
	public $data;

    public function __construct() {
        parent::__construct();

        // Prevent access without login
		if(!isset($this->session->userdata['mec_user'])){
			redirect();
		}
		
		// Load library
		$this->load->library('template');

        $this->data['title'] = "Title | Feedbacker ";

        // Load Login Model
        $this->load->model('common');
		
		// Load Language File
		$this->lang->load('message','english');

        //remove catch so after logout cannot view last visited page if that page is this
        $this->output->set_header('Last-Modified:' . gmdate('D, d M Y H:i:s') . 'GMT');
        $this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate');
        $this->output->set_header('Cache-Control: post-check=0, pre-check=0', false);
        $this->output->set_header('Pragma: no-cache');
    }

	public function index() {
		/* Load Template */
		$this->template->front_render('user/dashboard');
	}
	
	public function follow() {
		if ($this->input->is_ajax_request()) {
			$user_id = $this->input->post('user_id');
			$title_id = $this->input->post('title_id');
			
			$condition_array = array('user_id' => $user_id, 'title_id' => $title_id);
			$followings = $this->common->select_data_by_condition('followings', $condition_array, $data = '*', $short_by = '', $order_by = '', $limit = '1', $offset = '', $join_str = array(), $group_by = '');
			
			if(count($followings) > 0) {
				// Unfollow Title
				$this->common->delete_data('followings', 'follow_id', $followings[0]['follow_id']);
				echo json_encode(array('is_followed' => 0, 'message' => $this->lang->line('success_unfollow_title'), 'status' => 1));
				die();
			} else {
				// Follow Title
				$insert_array['user_id'] = $user_id;
				$insert_array['title_id'] = $title_id;
				
				$insert_result = $this->common->insert_data($insert_array, $tablename = 'followings');
				echo json_encode(array('is_followed' => 1, 'message' => $this->lang->line('success_follow_title'), 'status' => 1));
				die();
			}
		}
	}
	
	public function create() {
		//check post and save data
        if ($this->input->is_ajax_request() && $this->input->post('btn_save')) {
			$this->form_validation->set_rules('email', 'Email', 'trim|valid_email|required');
			
			if ($this->form_validation->run() == FALSE) {
				$this->session->set_flashdata('error', validation_errors());
				redirect('user/profile');
			}
		}
		
		$this->data['module_name'] = 'Post';
        $this->data['section_title'] = 'Create';
		
		/* Load Template */
		$this->template->front_render('post/create', $this->data);
	}
	
}
