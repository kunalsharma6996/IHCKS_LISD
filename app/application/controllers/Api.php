<?php
defined('BASEPATH') OR exit('No direct script access allowed');

	/**
	* 
	*/
	class Api extends CI_Controller
	{
		
		function __construct()
		{
			parent::__construct();
			$this->load->model('lisd_model');
		}

		function index(){
			ini_set('memory_limit', '1024M');
			echo "api call";
		}

		function validate_user(){
			//var_dump($_POST);
			$username=$this->input->get('username');
			$password=$this->input->get('password');
			$data['user_name']=$username;
			$data['user_password']=$password;
			$res=$this->lisd_model->validate_user($data);

			if(sizeof($res)>0)
			{
				foreach ($res as $item ) {
					$ret['sessionId']=$item['user_id'];
					$ret['user_firstname']=$item['user_firstname'];
					$ret['user_lastname']=$item['user_lastname'];
					$ret['user_contact']=$item['user_contact'];
					$ret['user_email']=$item['user_email'];
					$ret['user_password']=$item['user_password'];
					$ret['user_name']=$item['user_name'];
					$ret['status']=1;
					$desc['description']="login successful";
					$ret['message']=$desc;
					echo json_encode($ret);
				}
			
			}
			else{
			$desc['description']="login unsuccessful";
			$ret['status']=0;
			$ret['message']=$desc;
			echo json_encode($ret);
			}

		}


		function fetch_user_vehicle(){

			$user=$this->input->get('_USER_NAME');
			$data['user_name']=$user;
			$res=$this->lisd_model->fetch_by_username($data);
			//echo $res->user_id; 
			$id=$res->user_id;
			$inp['user_id']=$id;
			$vehicles=$this->lisd_model->fetch_user_vehicles($inp);

			$out['vehicles']=$vehicles;

			echo json_encode($out);

		}
		function fetch_user_vehicle_id(){

			$user=$this->input->get('user_id');
			
			$inp['user_id']=$user;
			$vehicles=$this->lisd_model->fetch_user_vehicles($inp);

			$out['vehicles']=$vehicles;

			echo json_encode($out);

		}

		function insert_user(){
			//print_r($array);
			$username=$this->input->get('first_name');
			$lastname=$this->input->get('last_name');
			$password=$this->input->get('password');
			$confpass=$this->input->get('confirm_password');
			$user_name=$this->input->get('user_name');
			$email=$this->input->get('email_id');
			$contact=$this->input->get('contact_no');

			$usercheck['user_name']=$user_name;
			if($this->lisd_model->check_username($usercheck)){

				if($confpass==$password){
				$info['user_name']=$user_name;
				$info['user_firstname']=$username;
				$info['user_lastname']=$lastname;
				$info['user_password']=$password;
				$info['user_email']=$email;
				$info['user_contact']=$contact;
				
				$res=$this->lisd_model->insert_user($info);
				}
				if($res==true){
					$ret['status']=1;
					$ret['message']="registration successful";
					echo json_encode($ret);
				}
				else
				{
					$ret['message']="registration unsuccessful";
					$ret['status']=1;
					echo json_encode($ret);
				}	
			}
			else {
					$ret['status']=3;
					$ret['message']="username already exists";
					
					echo json_encode($ret);
			}

			
		}




		function insert_vehicle(){
			/*$stream_clean = $this->security->xss_clean($this->input->raw_input_stream);
			$request = json_decode($stream_clean);
			$json=json_decode($stream_clean);
			//print_r($json);
			$form=$json->form;
			$array = json_decode(json_encode($form), True);
			//print_r($array);
			$vehicle_type=$array['vehicle_type'];
			$vehicle_count=$array['vehicle_count'];*/

			$vehicle_type=$this->input->get('vehicle_type');
			$vehicle_count=$this->input->get('vehicle_count');
			
			$info['vehicle_type']=$vehicle_type;
			$info['vehicle_count']=$vehicle_count;
			$info['immobilize']='false';
			
			$res=$this->lisd_model->insert_vehicle($info);
			if($res==true){
				$desc['description']="registration successful";
				$ret['message']=$desc;
				echo json_encode($ret);
			}
			else
			{
				$desc['description']="registration unsuccessful";
				$ret['message']=$desc;
				echo json_encode($ret);
			}

		}


		function insert_trip(){
			//reading data
			$user_id=$this->input->get('user_id');
			$vehicle_id=$this->input->get('vehicle_id');
			$latitude=$this->input->get('latitude');
			$longitude=$this->input->get('longitude');
			$timestamp=$this->input->get('timestamp');

			$info['user_id']=$user_id;
			$info['vehicle_id']=$vehicle_id;
			$info['latitude']=$latitude;
			$info['longitude']=$longitude;
			$info['timestamp']=$timestamp;

			$res=$this->lisd_model->insert_gps_start($info);
			if($res>0){
				$ret['trip_id']=$res;
				echo json_encode($ret);
			}
			else{
				$desc['description']="insert failed";
					$ret['message']=$desc;
					echo json_encode($ret);
			}


		}

		function update_trip(){

			$trip_id=$this->input->get('trip_id');
			$user_id=$this->input->get('user_id');
			$vehicle_id=$this->input->get('vehicle_id');
			$latitude=$this->input->get('latitude');
			$longitude=$this->input->get('longitude');
			$timestamp=$this->input->get('timestamp');
			$is_trip_live=$this->input->get('is_trip_live');

			$info['trip_id']=$trip_id;
			$info['user_id']=$user_id;
			$info['vehicle_id']=$vehicle_id;
			$info['latitude']=$latitude;
			$info['longitude']=$longitude;
			$info['timestamp']=$timestamp;
			$info['is_trip_live']=$is_trip_live;

			$res=$this->lisd_model->insert_gps_details($info);
			if($res>0){
				$ret['message']="successful insert";
				echo json_encode($ret);
			}
			else{
				$desc['description']="insert failed";
					$ret['message']=$desc;
					echo json_encode($ret);
			}


		}

		function get_flag_by_id(){
			$vehicle_id=$this->input->get('vehicle_id');
			$info['vehicle_id']=$vehicle_id;
			$res=$this->lisd_model->fetch_flag_by_id($info);
			$ret['immobilize']=$res->immobilize;
			echo json_encode($ret);
		}

		function set_flag_by_id(){
			$vehicle_id=$this->input->get('vehicle_id');
			$info['vehicle_id']=$vehicle_id;
			$res=$this->lisd_model->set_flag_by_id($info);
			$ret['message']=$res;
			echo json_encode($ret);
		}

		function carbon_footprint_generate(){

			$ch = curl_init();
			$vehicle_id=$this->input->get('vehicle_id');
			$info['vehicle_id']=$vehicle_id;

			$ret=$this->lisd_model->get_latlong($info);

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, "http://api.commutegreener.com/api/co2/emissions?startLat=".$ret['startlat']."&startLng=".$ret['startlong']."&endLat=".$ret['endlat']."&endLng=".$ret['endlong']."&format=json");
			// define options
			

			// apply those options
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

			// execute request and get response
			$output = curl_exec($ch);
			// also get the error and response code
			curl_close($ch);
			echo($output);
		}

		
		function fetch_gps_user_id(){
			$final=[];
			$user=$this->input->get('user_id');
			$inp['user_id']=$user;
			$trips=$this->lisd_model->fetch_gps_user($inp);
			foreach ($trips as $trip ) {
				$trip_details=$this->lisd_model->fetch_trip_by_trip_id($trip);
				$out=[];
				foreach($trip_details as $element) {

					$check['trip_id']=$element['trip_id'];
					$check['is_trip_live']='false';

					$out['trip_id']=$element['trip_id'];
					$out['user_id']=$element['user_id'];
					$out['vehicle_id']=$element['vehicle_id'];
        			$out['longitude'][] = $element['longitude'];
        			$out['latitude'][] = $element['latitude'];
        			$out['timestamp'][] = $element['timestamp'];
        			$out['islive']=$this->lisd_model->is_live($check);
				}
				array_push($final, $out);
			}
			$output['trips']=$final;
			echo json_encode($output);

		}

		function toogle_button(){

			$vehicle_id=$this->input->get('vehicle_id');
			$value=$this->input->get('value');

			if($value==0){
				$info['immobilize']='true';
			}
			else{
			$info['immobilize']='false';	
			}
			$out['status'] = $this->lisd_model->toggle_button($vehicle_id,$info['immobilize']); 
			echo json_encode($out);

		}

		function add_user_vehicle(){
			$vehicle_id=$this->input->get('vehicle_id');
			$user_id=$this->input->get('user_id');

			$data['vehicle_id']=$vehicle_id;
			$data['user_id']=$user_id;

			$res=$this->lisd_model->add_user_vehicle($data);
			if($res==true){
				$desc['description']="registration successful";
				$ret['message']=$desc;
				echo json_encode($ret);
			}
			else
			{
				$desc['description']="registration unsuccessful";
				$ret['message']=$desc;
				echo json_encode($ret);
			}
		}


	}

 ?>