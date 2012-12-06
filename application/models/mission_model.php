<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Mission_model extends CI_Model {

	public function __construct(){
	
		parent::__construct();
		
	}
	
	//get all missions
	public function get_all_missions(){
	
		//need to sort by asc
		$this->db->order_by('id', 'asc'); 
		$query = $this->db->get('missions');
		
		$missions = false;
		if($query->num_rows() > 0){
		
			foreach($query->result() as $row){
			
				$missions[] = array(
					'id'				=> $row->id,
					'mission_number'	=> $row->mission_number,
					'title'				=> $row->title,
					'description'		=> $row->description,
					'parameters'		=> stripslashes(var_export(unserialize(base64_decode($row->parameters)), true)),
				);
				
			}
			
		}
	
		return $missions;
	
	}
	
	public function get_parameters($id){
	
	}
	
	public function update_parameters($id){
	
	}
	
	public function get_description($id){
	
	}
	
	public function update_description($id){
	
	}
	
	public function delete_mission($id){
	
	}
	
	public function add_mission($new_mission){
	
		$new_mission['parameters'] = base64_encode(serialize($new_mission));
	
		$query = $this->db->insert('missions', $new_mission);
		
		if($query){
			return $this->db->affected_rows();
		}else{
			return false;
		}
	
	}
	
	public function mission_number_check($num){
	
		//true or false?
		$query = $this->db->get_where('missions', array('mission_number'=>$num));
		if($query->num_rows() > 0){
			return $query->num_rows();
		}else{
			return false;
		}
		
	}
	
}