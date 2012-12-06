<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Mission_model extends CI_Model {

	public $eval_error = false;

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
					'parameters'		=> $this->_parameter_decode($row->parameters),
				);
				
			}
			
		}
		
		#var_dump($missions);
	
		return $missions;
	
	}
	
	//for showing
	//exports as an array
	private function _parameter_decode($parameters){
	
		//this returns an associative array
		$parameters = json_decode($parameters, true);
		
		return $parameters;
		
	}
	
	//for storing
	//json string
	private function _parameter_encode($parameters){
	
		//$parameters comes as a completely array string without (;) at the end
		$parameters = @eval("
			\$param = $parameters;
			return \$param;
		");
		
		//if eval did not return properly
		if($parameters == false OR $parameters == null){
		
			if(error_get_last()){
				$this->eval_error = error_get_last();
			}
			
			return false;
			
		}
		
		$parameters = json_encode($parameters);
		return $parameters;
		
	}
	
	public function get_mission($id){
	
		$query = $this->db->get_where('missions', array('id'=>$id));
		
		$mission = false;
		
		if($query->num_rows() > 0){
		
			$row = $query->row();
			$mission = array(
				'id'				=> $row->id,
				'mission_number'	=> $row->mission_number,
				'title'				=> $row->title,
				'description'		=> $row->description,
				'parameters'		=> $this->_parameter_decode($row->parameters),
			);
		
		}
				
		return $mission;
	
	}
	
	public function update_mission($id, $updated_mission){
	
		//Update the mission based on id		
		$encoded_parameter = $this->_parameter_encode($updated_mission['parameters']);
		
		if($encoded_parameter){
		
			$updated_mission['parameters'] = $encoded_parameter;
			
			$this->db->where('id', $id);
			$this->db->update('missions', $updated_mission);
			
			if($this->db->affected_rows() > 0){
				return $this->db->affected_rows();
			}
			
		}
		
		return false;
	
	}
	
	//need to add in delete mission
	public function delete_mission($id){
	
	}
	
	public function add_mission($new_mission){
	
		$encoded_parameter = $this->_parameter_encode($new_mission['parameters']);
		
		if($encoded_parameter){
		
			$new_mission['parameters'] = $encoded_parameter;
			
			$query = $this->db->insert('missions', $new_mission);
			
			if($query){
				return $this->db->affected_rows();
			}
			
		}
		
		return false;
		
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