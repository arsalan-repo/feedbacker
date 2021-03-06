<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require 'vendor/autoload.php';
use Elasticsearch\ClientBuilder;

class Post extends CI_Controller {
	
	public $data;
	
	public $user;
	
	private $perPage = 10;
	
	private $aws_client;

    public function __construct() {
        parent::__construct();

        // Prevent access without login
		if(!isset($this->session->userdata['mec_user'])){
			redirect();
		}
		
		// Load library
		$this->load->library('s3');
		$this->load->library('template');
		
		$this->aws_client = ClientBuilder::create()->setHosts(["search-feedbacker-q3gdcfwrt27ulaeee5gz3zbezm.eu-west-1.es.amazonaws.com:80"])->build();

        $this->data['title'] = "Post | Feedbacker ";

        // Load Login Model
        $this->load->model('common');
		
		// Session data
		$this->user = $this->session->userdata['mec_user'];
		$this->data['user_info'] = $this->user;
		
		// Load Language File		
		if ($this->user['language'] == 'ar') {
			$this->lang->load('message','arabic');
			$this->lang->load('label','arabic');
		} else {
			$this->lang->load('message','english');
			$this->lang->load('label','english');
		}

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
	
	public function get_location() {
		if ($this->input->is_ajax_request()) {
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
	
	// Like / Unlike Feedback
    function like() {
		if ($this->input->is_ajax_request()) {
			$feedback_id = $this->input->post('feedback_id');
			$totl_likes = $this->input->post('totl_likes');
			
			$condition_array = array('user_id' => $this->user['id'], 'feedback_id' => $feedback_id);
			$likes = $this->common->select_data_by_condition('feedback_likes', $condition_array, $data = '*', $short_by = '', $order_by = '', $limit = '1', $offset = '', $join_str = array(), $group_by = '');
			
			if(count($likes) > 0) {
				// Unlike Feedback
				$this->common->delete_data('feedback_likes', 'like_id', $likes[0]['like_id']);
	
				// Check / Add Notification for users
				$this->common->notification('', $this->user['id'], $title_id = '', $feedback_id, $replied_to = '', 3);
	
				echo json_encode(array('is_liked' => 0, 'likes' => $totl_likes, 'message' => $this->lang->line('success_unlike_feedback'), 'status' => 1));
				die();
			} else {
				// Like Feedback
				$insert_array['user_id'] = $this->user['id'];
				$insert_array['feedback_id'] = $feedback_id;
				
				$insert_result = $this->common->insert_data($insert_array, $tablename = 'feedback_likes');
	
				// Check / Add Notification for users
				$this->common->notification('', $this->user['id'], $title_id = '', $feedback_id, $replied_to = '', 3);
	
				echo json_encode(array('is_liked' => 1, 'likes' => $totl_likes, 'message' => $this->lang->line('success_like_feedback'), 'status' => 1));
				die();
			}
		}
    }
	
	public function create() {
		//check post and save data
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
			$this->form_validation->set_rules('title', 'Title', 'trim|required');
			$this->form_validation->set_rules('feedback_cont', 'Feedback', 'trim|required');
			
			if ($this->form_validation->run() == FALSE) {
				$this->session->set_flashdata('error', validation_errors());
				redirect('post/create');
			}
			
            $feedback_img = '';
            $feedback_thumb = '';
            $feedback_video = '';
            
            // Image Upload Start
            if (isset($_FILES['feedback_img']['name']) && $_FILES['feedback_img']['name'] != '') {
                $config['upload_path'] = $this->config->item('feedback_main_upload_path');
                $config['thumb_upload_path'] = $this->config->item('feedback_thumb_upload_path');
                $config['allowed_types'] = $this->config->item('feedback_allowed_types');
                $config['max_size'] = $this->config->item('feedback_main_max_size');
                $config['max_width'] = $this->config->item('feedback_main_max_width');
                $config['max_height'] = $this->config->item('feedback_main_max_height');
                $config['file_name'] = time();
    
                $this->load->library('upload', $config);
    
                // Uploading Image
                if (!$this->upload->do_upload('feedback_img')) {
                    $error = array('error' => $this->upload->display_errors());
					$this->session->set_flashdata('error', $error);
					redirect('post/create');
                } else {
                    // Getting Uploaded Image File Data
                    $imgdata = $this->upload->data();                   
                    $feedback_img = $imgdata['file_name'];

                    // Configuring Thumbnail 
                    $config_thumb['image_library'] = 'gd2';
                    $config_thumb['source_image'] = $config['upload_path'] . $imgdata['file_name'];
                    $config_thumb['new_image'] = $config['thumb_upload_path'] . $imgdata['file_name'];
                    $config_thumb['create_thumb'] = TRUE;
                    $config_thumb['maintain_ratio'] = TRUE;
                    $config_thumb['thumb_marker'] = '_thumb';
                    $config_thumb['width'] = $this->config->item('feedback_thumb_width');
                    $config_thumb['height'] = $this->config->item('feedback_thumb_height');

                    // Loading Image Library
                    $this->load->library('image_lib', $config_thumb);

                    // Creating Thumbnail
                    if(!$this->image_lib->resize()) {
                        $error = array('error' => $this->image_lib->display_errors());
                        $this->session->set_flashdata('error', $error);
						redirect('post/create');
                    } else {
                        $feedback_thumb = $imgdata['raw_name'].'_thumb'.$imgdata['file_ext'];
                    }
                    
                    // AWS S3 Upload
                    $thumb_file_path = str_replace("main", "thumbs", $imgdata['file_path']);
                    $thumb_file_name = $config['thumb_upload_path'] . $imgdata['raw_name'].'_thumb'.$imgdata['file_ext'];
                    
                    $this->s3->putObjectFile($imgdata['full_path'], S3_BUCKET, $config_thumb['source_image'], S3::ACL_PUBLIC_READ);
                    $this->s3->putObjectFile($thumb_file_path.$feedback_thumb, S3_BUCKET, $thumb_file_name, S3::ACL_PUBLIC_READ);

                    // Remove File from Local Storage
                    unlink($config_thumb['source_image']);
                    unlink($thumb_file_name);
                }
            }
            // Image Upload End
            
            // Video Upload Start
            if (isset($_FILES['feedback_video']['name']) && $_FILES['feedback_video']['name'] != '') {
                $config_video['upload_path'] = $this->config->item('feedback_video_upload_path');
                $config_video['thumb_upload_path'] = $this->config->item('feedback_thumb_upload_path');
                $config_video['max_size'] = $this->config->item('feedback_video_max_size');
                $config_video['allowed_types'] = $this->config->item('feedback_allowed_video_types');
                $config_video['overwrite'] = FALSE;
                $config_video['remove_spaces'] = TRUE;
                $config_video['file_name'] = time();    
                    
                $this->load->library('upload', $config_video);
                $this->upload->initialize($config_video);
                
                if (!$this->upload->do_upload('feedback_video')) {
                    $error = $this->upload->display_errors();
					$this->session->set_flashdata('error', strip_tags($error));
					redirect('post/create');
                } else {
                    $video_details = $this->upload->data();
					$feedback_video = $video_details['file_name'];
        
/*                  if($video_details['file_ext'] == ".mov" || $video_details['file_ext'] == ".MOV") {
                        // ffmpeg command to convert video
                        shell_exec("ffmpeg -i ".$video_details['full_path']." ".$video_details['file_path'].$video_details['raw_name'].".mp4");
                    
                        /// In the end update video name in DB
                        $feedback_video = $video_details['raw_name'].'.'.'mp4';
                    }*/
                    
                    // Generate video thumbnail
                    $video_path = $video_details['full_path'];
                    $thumb_name = $video_details['raw_name']."_video.jpg";
                    $thumb_path = $config_video['thumb_upload_path'].$thumb_name;

                    shell_exec("ffmpeg -itsoffset -3 -i ".$video_path."  -y -an -f image2 -s 400x270 ".$thumb_path."");
                    $feedback_thumb = $thumb_name;
                    
                    // AWS S3 Upload
                    $thumb_file_path = str_replace("video", "thumbs", $video_details['file_path']);
                    
                    $this->s3->putObjectFile($video_details['full_path'], S3_BUCKET, $config_video['upload_path'].$video_details['file_name'], S3::ACL_PUBLIC_READ);
                    $this->s3->putObjectFile($thumb_file_path.$feedback_thumb, S3_BUCKET, $thumb_path, S3::ACL_PUBLIC_READ);

                    // Remove File from Local Storage
                    unlink($config_video['upload_path'].$video_details['file_name']);
                    unlink($thumb_path);
                }
            }
            // Video Upload End

            // Check / Add Title
			$title = trim($this->input->post('title'));
			
			$params = ['index' => 'title'];
			$response = $this->aws_client->indices()->exists($params);
	
			if(!$response){
				$indexParams = [
					'index' => 'title',
					'body' => [
						'settings' => [
							'number_of_shards' => 5,
							'number_of_replicas' => 1
						]
					]
				];
	
				$response = $this->aws_client->indices()->create($indexParams);
			}
			
			// Check If title exists
			$contition_array = array('title' => $title);
			$check_title = $this->common->select_data_by_condition('titles', $contition_array, $data = '*', $sortby = '', $orderby = '', $limit = '', $offset = '', $join_str = array(), $group_by='');
			
			if(count($check_title) > 0) {
				// Restore If deleted
				$update_data = array('deleted' => 0);
				$update_result = $this->common->update_data($update_data, 'titles', 'title_id', $check_title[0]['title_id']);
	
				$insert_array['title_id'] = $check_title[0]['title_id'];
			} else {
				$insert_result = $this->common->insert_data_getid(array('title' => $title), $tablename = 'titles');
		
				$docParams = [
					'index' => 'title',
					'type' => 'title_type',
					'id' => $insert_result,
					'body' => ['title' => $title,'title_id' => $insert_result]
				]; 
		
				$response = $this->aws_client->index($docParams);
				
				// Auto Follow Title
				$follow_array['user_id'] = $this->user['id'];
				$follow_array['title_id'] = $insert_result;
				
				$auto_follow = $this->common->insert_data($follow_array, $tablename = 'followings');
		
				$insert_array['title_id'] = $insert_result;
			}
            
			$insert_array['user_id'] = $this->user['id'];
            $insert_array['feedback_cont'] = $this->input->post('feedback_cont');
            if($feedback_img != '') {
                $insert_array['feedback_img'] = $feedback_img;
            }
            if($feedback_thumb != '') {
                $insert_array['feedback_thumb'] = $feedback_thumb;
            }
            if($feedback_video != '') {
                $insert_array['feedback_video'] = $feedback_video;
            }
            $insert_array['latitude'] = $this->input->post('latitude');
            $insert_array['longitude'] = $this->input->post('longitude');
            $insert_array['location'] = $this->input->post('location');
            $insert_array['country'] = $this->user['country'];
            $insert_array['datetime'] = date('Y-m-d H:i:s');

            $insert_result = $this->common->insert_data_getid($insert_array, $tablename = 'feedback');

            // AWS Elastic Search
            $params = ['index' => 'feedback'];
            $response = $this->aws_client->indices()->exists($params);

            if(!$response){
                $indexParams = [
                    'index' => 'feedback',
                    'body' => [
                        'settings' => [
                            'number_of_shards' => 5,
                            'number_of_replicas' => 1
                        ]
                    ]
                ];

                $response = $this->aws_client->indices()->create($indexParams);
            } 
            $insert_array['feedback_id'] = $insert_result;

            $docParams = [
                'index' => 'feedback',
                'type' => 'feedback_type',
                'id' => $insert_result,
                'body' => $insert_array
            ]; 

            $response = $this->aws_client->index($docParams);

            if ($insert_result) {
                // Check / Add Notification for users
                $this->common->notification('', $user_id, $title_id, $insert_result, $replied_to = '', 2);

                $this->session->set_flashdata('success', '<p>'.$this->lang->line('success_feedback_submit').'</p>');
				redirect('user/dashboard');
            } else {
				$this->session->set_flashdata('error', '<p>'.$this->lang->line('error_feedback_submit').'</p>');
				redirect('post/create');
            }
			//
		}
		
		$this->data['module_name'] = 'Post';
        $this->data['section_title'] = 'Create Post';
		
		/* Load Template */
		$this->template->front_render('post/create', $this->data);
	}
	
	public function reply() {
		//check post and save data
		$replied_to = $this->input->post('id');
		
		if ($replied_to == '') {
			redirect('post/create');
        } else {
			$this->form_validation->set_rules('feedback_cont', 'Feedback', 'trim|required');
			
			if ($this->form_validation->run() == FALSE) {
				$this->session->set_flashdata('error', validation_errors());
				redirect('post/detail/'.$title_id);
			}
			
            $feedback_img = '';
            $feedback_thumb = '';
            $feedback_video = '';
            
            // Image Upload Start
            if (isset($_FILES['feedback_img']['name']) && $_FILES['feedback_img']['name'] != '') {
                $config['upload_path'] = $this->config->item('feedback_main_upload_path');
                $config['thumb_upload_path'] = $this->config->item('feedback_thumb_upload_path');
                $config['allowed_types'] = $this->config->item('feedback_allowed_types');
                $config['max_size'] = $this->config->item('feedback_main_max_size');
                $config['max_width'] = $this->config->item('feedback_main_max_width');
                $config['max_height'] = $this->config->item('feedback_main_max_height');
                $config['file_name'] = time();
    
                $this->load->library('upload', $config);
    
                // Uploading Image
                if (!$this->upload->do_upload('feedback_img')) {
                    $error = array('error' => $this->upload->display_errors());
					$this->session->set_flashdata('error', $error);
					redirect('post/create');
                } else {
                    // Getting Uploaded Image File Data
                    $imgdata = $this->upload->data();                   
                    $feedback_img = $imgdata['file_name'];

                    // Configuring Thumbnail 
                    $config_thumb['image_library'] = 'gd2';
                    $config_thumb['source_image'] = $config['upload_path'] . $imgdata['file_name'];
                    $config_thumb['new_image'] = $config['thumb_upload_path'] . $imgdata['file_name'];
                    $config_thumb['create_thumb'] = TRUE;
                    $config_thumb['maintain_ratio'] = TRUE;
                    $config_thumb['thumb_marker'] = '_thumb';
                    $config_thumb['width'] = $this->config->item('feedback_thumb_width');
                    $config_thumb['height'] = $this->config->item('feedback_thumb_height');

                    // Loading Image Library
                    $this->load->library('image_lib', $config_thumb);

                    // Creating Thumbnail
                    if(!$this->image_lib->resize()) {
                        $error = array('error' => $this->image_lib->display_errors());
                        $this->session->set_flashdata('error', $error);
						redirect('post/create');
                    } else {
                        $feedback_thumb = $imgdata['raw_name'].'_thumb'.$imgdata['file_ext'];
                    }
                    
                    // AWS S3 Upload
                    $thumb_file_path = str_replace("main", "thumbs", $imgdata['file_path']);
                    $thumb_file_name = $config['thumb_upload_path'] . $imgdata['raw_name'].'_thumb'.$imgdata['file_ext'];
                    
                    $this->s3->putObjectFile($imgdata['full_path'], S3_BUCKET, $config_thumb['source_image'], S3::ACL_PUBLIC_READ);
                    $this->s3->putObjectFile($thumb_file_path.$feedback_thumb, S3_BUCKET, $thumb_file_name, S3::ACL_PUBLIC_READ);

                    // Remove File from Local Storage
                    unlink($config_thumb['source_image']);
                    unlink($thumb_file_name);
                }
            }
            // Image Upload End
            
            // Video Upload Start
            if (isset($_FILES['feedback_video']['name']) && $_FILES['feedback_video']['name'] != '') {
                $config_video['upload_path'] = $this->config->item('feedback_video_upload_path');
                $config_video['thumb_upload_path'] = $this->config->item('feedback_thumb_upload_path');
                $config_video['max_size'] = $this->config->item('feedback_video_max_size');
                $config_video['allowed_types'] = $this->config->item('feedback_allowed_video_types');
                $config_video['overwrite'] = FALSE;
                $config_video['remove_spaces'] = TRUE;
                $config_video['file_name'] = time();    
                    
                $this->load->library('upload', $config_video);
                $this->upload->initialize($config_video);
                
                if (!$this->upload->do_upload('feedback_video')) {
                    $error = $this->upload->display_errors();
					$this->session->set_flashdata('error', strip_tags($error));
					redirect('post/create');
                } else {
                    $video_details = $this->upload->data();
					$feedback_video = $video_details['file_name'];
        
/*                  if($video_details['file_ext'] == ".mov" || $video_details['file_ext'] == ".MOV") {
                        // ffmpeg command to convert video
                        shell_exec("ffmpeg -i ".$video_details['full_path']." ".$video_details['file_path'].$video_details['raw_name'].".mp4");
                    
                        /// In the end update video name in DB
                        $feedback_video = $video_details['raw_name'].'.'.'mp4';
                    }*/
                    
                    // Generate video thumbnail
                    $video_path = $video_details['full_path'];
                    $thumb_name = $video_details['raw_name']."_video.jpg";
                    $thumb_path = $config_video['thumb_upload_path'].$thumb_name;

                    shell_exec("ffmpeg -itsoffset -3 -i ".$video_path."  -y -an -f image2 -s 400x270 ".$thumb_path."");
                    $feedback_thumb = $thumb_name;
                    
                    // AWS S3 Upload
                    $thumb_file_path = str_replace("video", "thumbs", $video_details['file_path']);
                    
                    $this->s3->putObjectFile($video_details['full_path'], S3_BUCKET, $config_video['upload_path'].$video_details['file_name'], S3::ACL_PUBLIC_READ);
                    $this->s3->putObjectFile($thumb_file_path.$feedback_thumb, S3_BUCKET, $thumb_path, S3::ACL_PUBLIC_READ);

                    // Remove File from Local Storage
                    unlink($config_video['upload_path'].$video_details['file_name']);
                    unlink($thumb_path);
                }
            }
            // Video Upload End
            
			$gettitle = $this->common->select_data_by_id('feedback', 'feedback_id', $replied_to, 'title_id', '');
            if (count($gettitle) > 0) {
                $insert_array['title_id'] = $gettitle[0]['title_id'];
            }
			
			$insert_array['user_id'] = $this->user['id'];
            $insert_array['feedback_cont'] = $this->input->post('feedback_cont');
            if($feedback_img != '') {
                $insert_array['feedback_img'] = $feedback_img;
            }
            if($feedback_thumb != '') {
                $insert_array['feedback_thumb'] = $feedback_thumb;
            }
            if($feedback_video != '') {
                $insert_array['feedback_video'] = $feedback_video;
            }
            $insert_array['latitude'] = $this->input->post('latitude');
            $insert_array['longitude'] = $this->input->post('longitude');
            $insert_array['location'] = $this->input->post('location');
			$insert_array['replied_to'] = $replied_to;
            $insert_array['country'] = $this->user['country'];
            $insert_array['datetime'] = date('Y-m-d H:i:s');

            $insert_result = $this->common->insert_data_getid($insert_array, $tablename = 'feedback');

            // AWS Elastic Search
            $params = ['index' => 'feedback'];
            $response = $this->aws_client->indices()->exists($params);

            if(!$response){
                $indexParams = [
                    'index' => 'feedback',
                    'body' => [
                        'settings' => [
                            'number_of_shards' => 5,
                            'number_of_replicas' => 1
                        ]
                    ]
                ];

                $response = $this->aws_client->indices()->create($indexParams);
            } 
            $insert_array['feedback_id'] = $insert_result;

            $docParams = [
                'index' => 'feedback',
                'type' => 'feedback_type',
                'id' => $insert_result,
                'body' => $insert_array
            ]; 

            $response = $this->aws_client->index($docParams);

            if ($insert_result) {
				// Check / Add Notification for users
                $this->common->notification('', $this->user['id'], $title_id = '', $insert_result, $replied_to, 4);

                $this->session->set_flashdata('success', '<p>'.$this->lang->line('success_reply_submit').'</p>');
				redirect('post/detail/'.$replied_to);
            } else {
				$this->session->set_flashdata('error', '<p>'.$this->lang->line('error_reply_submit').'</p>');
				redirect('post/detail/'.$replied_to);
            }
			//
		}
	}
	
	public function title($id) {		
		// Trends
		$this->data['trends'] = $this->common->getTrends($this->user['country']);
		
		// What to Follow
		$this->data['to_follow'] = $this->common->whatToFollow($this->user['id'], $this->user['country']);
		
		// Get Feedbacks From Title ID
		$join_str = array(
			array(
				'table' => 'users',
				'join_table_id' => 'users.id',
				'from_table_id' => 'feedback.user_id',
				'join_type' => 'left'
			),
			array(
				'table' => 'titles',
				'join_table_id' => 'titles.title_id',
				'from_table_id' => 'feedback.title_id',
				'join_type' => 'left'
			)
		);
			
		$contition_array = array('feedback.title_id' => $id, 'feedback.replied_to' => NULL, 'feedback.deleted' => 0, 'feedback.status' => 1);
		
		$data = 'feedback_id, feedback.title_id, title, users.id as user_id, name, photo, feedback_cont, feedback_img, feedback_thumb, feedback_video, replied_to, location, feedback.datetime as time';
		
		if (!empty($this->input->get("page"))) {
			$page = ceil($this->input->get("page") - 1);
			$start = ceil($page * $this->perPage);
			
			$feedback = $this->common->select_data_by_condition('feedback', $contition_array, $data, $sortby = 'feedback.datetime', $orderby = 'DESC', $this->perPage, $start, $join_str, $group_by = '');
		
			if(count($feedback) > 0) {
				// Get Likes, Followings and Other details
				$result = $this->common->getFeedbacks($feedback, $this->user['id']);
				
				// Append Ad Banners
				$return_array = $this->common->adBanners($result, $this->user['country'], 'title', $this->input->get("page"), $id);
				
				$this->data['module_name'] = 'Post';
				$this->data['section_title'] = $feedback[0]['title'];
				
				$this->data['feedbacks'] = $return_array;
			} else {
				$this->data['feedbacks'] = array();
				$this->data['no_record_found'] = $this->lang->line('no_results');
			}
			
			$response = $this->load->view('post/ajax', $this->data);
			echo json_encode($response);
		} else {
			$feedback = $this->common->select_data_by_condition('feedback', $contition_array, $data, $sortby = 'feedback.datetime', $orderby = 'DESC', $this->perPage, 0, $join_str, $group_by = '');
			
			if(count($feedback) > 0) {
				// Get Likes, Followings and Other details
				$result = $this->common->getFeedbacks($feedback, $this->user['id']);
				
				// Append Ad Banners
				$return_array = $this->common->adBanners($result, $this->user['country'], 'title', '', $id);
				
				$this->data['feedbacks'] = $return_array;
			} else {
				$this->data['feedbacks'] = array();
				$this->data['no_record_found'] = $this->lang->line('no_results');
			}
			
			$this->data['module_name'] = 'Post';
			$this->data['section_title'] = $feedback[0]['title'];
			
			/* Load Template */
			$this->template->front_render('post/title', $this->data);
		}
	}
	
	public function detail($id) {
		$this->data['module_name'] = 'Post';
        $this->data['section_title'] = 'Detail';
		
		// Get Feedback Details
        $return_array = $this->common->getFeedbackDetail($this->user['id'], $id);

        // Get all replies for this feedback
        $contition_array = array('replied_to' => $id, 'feedback.deleted' => 0, 'feedback.status' => 1);
        $replies = $this->common->select_data_by_condition('feedback', $contition_array, 'feedback_id', $sortby = 'feedback.datetime', $orderby = 'DESC', $limit = '', $offset = '', $join_str = array(), $group_by = '');
        
        $return_array['replies'] = array();
        foreach($replies as $reply) {
            $feedback = $this->common->getFeedbackDetail($id, $reply['feedback_id']);
            array_push($return_array['replies'], $feedback);
        }
		
		$this->data['feedback'] = $return_array;
		
		// Get feedbacks from same User
		$others_array = array();
		
		$join_str = array(
			array(
				'table' => 'users',
				'join_table_id' => 'users.id',
				'from_table_id' => 'feedback.user_id',
				'join_type' => 'left'
			),
			array(
				'table' => 'titles',
				'join_table_id' => 'titles.title_id',
				'from_table_id' => 'feedback.title_id',
				'join_type' => 'left'
			)
		);
		
		$data = 'feedback_id, feedback.title_id, title, name, photo, feedback_cont, feedback_img, feedback_thumb, feedback_video, replied_to, location, feedback.datetime as time';
		
		$search_condition = "feedback_id != ".$id." AND users.id = ".$return_array['user_id']." AND replied_to IS NULL AND feedback.deleted = 0 AND feedback.status = 1";
        $others = $this->common->select_data_by_search('feedback', $search_condition, $condition_array = array(), $data, $sortby = 'feedback.datetime', $orderby = 'DESC', $limit = '2', $offset = '', $join_str);
		
		if(count($others) > 0) {
			foreach ($others as $item) {
				$return = array();
				$return['id'] = $item['feedback_id'];
				$return['title_id'] = $item['title_id'];                
				$return['title'] = $item['title'];
				
				// Get likes for this feedback
				$contition_array_lk = array('feedback_id' => $item['feedback_id']);
				$flikes = $this->common->select_data_by_condition('feedback_likes', $contition_array_lk, $data = '*', $short_by = '', $order_by = '', $limit = '', $offset = '', $join_str = array(), $group_by = '');
				
				$return['likes'] = "";
				
				if(count($flikes) > 1000) {
					$return['likes'] = (count($flikes)/1000)."k";
				} else {
					$return['likes'] = count($flikes);
				}
				
				// Get followers for this title
				$contition_array_fo = array('title_id' => $item['title_id']);
				$followings = $this->common->select_data_by_condition('followings', $contition_array_fo, $data = '*', $short_by = '', $order_by = '', $limit = '', $offset = '', $join_str = array(), $group_by = '');
				
				$return['followers'] = "";
				
				if(count($followings) > 1000) {
					$return['followers'] = (count($followings)/1000)."k";
				} else {
					$return['followers'] = count($followings);
				}
				
				// Check If user liked this feedback
				$contition_array_li = array('feedback_id' => $item['feedback_id'], 'user_id' =>$this->user['id']);
				$likes = $this->common->select_data_by_condition('feedback_likes', $contition_array_li, $data = '*', $short_by = '', $order_by = '', $limit = '', $offset = '', $join_str = array(), $group_by = '');
							
				if(count($likes) > 0) {
					$return['is_liked'] = TRUE;
				} else {
					$return['is_liked'] = FALSE;
				}
				
				// Check If user followed this title
				$contition_array_ti = array('title_id' => $item['title_id'], 'user_id' =>$this->user['id']);
				$followtitles = $this->common->select_data_by_condition('followings', $contition_array_ti, $data = '*', $short_by = '', $order_by = '', $limit = '', $offset = '', $join_str = array(), $group_by = '');
							
				if(count($followtitles) > 0) {
					$return['is_followed'] = TRUE;
				} else {
					$return['is_followed'] = FALSE;
				}
				
				$return['name'] = $item['name'];
				
				if(isset($item['photo'])) {
					$return['user_avatar'] = S3_CDN . 'uploads/user/thumbs/' . $item['photo'];
				} else {
					$return['user_avatar'] = ASSETS_URL . 'images/user-avatar.png';
				}
				
				if($item['feedback_img'] !== "") {
					$return['feedback_img'] = S3_CDN . 'uploads/feedback/main/' . $item['feedback_img'];
				} else {
					$return['feedback_img'] = "";
				}

				if($item['feedback_thumb'] !== "") {
					$return['feedback_thumb'] = S3_CDN . 'uploads/feedback/thumbs/' . $item['feedback_thumb'];
				} elseif($item['feedback_img'] !== "") {
					$return['feedback_thumb'] = S3_CDN . 'uploads/feedback/main/' . $item['feedback_img'];
				} else {
					$return['feedback_thumb'] = "";
				}
				
				if($item['feedback_video'] !== "") {
					$return['feedback_video'] = S3_CDN . 'uploads/feedback/video/' . $item['feedback_video'];
					//$return['feedback_thumb'] = S3_CDN . 'uploads/feedback/thumbs/video_thumbnail.png';
				} else {
					$return['feedback_video'] = "";
				}

				$return['location'] = $item['location'];
				$return['feedback'] = $item['feedback_cont'];
				$return['time'] = $this->common->timeAgo($item['time']);

				array_push($others_array, $return);
			}
			
			$this->data['others'] = $others_array;
			
		} else {
			$this->data['others'] = array();
			$this->data['no_record_found'] = $this->lang->line('no_record_found');
		}
		
		$this->data['module_name'] = 'Post';
		$this->data['section_title'] = $return_array['title'];
		
		/* Load Template */
		$this->template->front_render('post/detail', $this->data);
	}
	
}
