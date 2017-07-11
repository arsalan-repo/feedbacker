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
			echo "<pre>";
			print_r($this->input->post());
			exit();
			$latitude = $this->input->post('latitude');
			$longitude = $this->input->post('longitude');			
			
			if(!empty($latitude) && !empty($longitude)){
				//Send request and receive json data by latitude and longitude
				$url = 'http://maps.googleapis.com/maps/api/geocode/json?latlng='.trim($latitude).','.trim($longitude).'&sensor=false';
				$json = @file_get_contents($url);
				$data = json_decode($json);
				$status = $data->status;
				if($status=="OK"){
					//Get address from json data
					$location = $data->results[0]->formatted_address;
				}else{
					$location =  '';
				}
				//Print address 
				echo json_encode(array("location" => $location));
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
