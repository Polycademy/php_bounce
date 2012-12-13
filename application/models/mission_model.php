<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Mission_model extends CI_Model {

	public $eval_error = false;

	public function __construct(){
	
		parent::__construct();
		
	}
	
	//get all missions
	public function get_all_missions(){
	
		//need to sort by asc
		$this->db->order_by('mission_number', 'asc'); 
		$query = $this->db->get('missions');
		
		$missions = false;
		if($query->num_rows() > 0){
		
			foreach($query->result() as $row){
			
				$missions[] = array(
					'id'				=> $row->id,
					'mission_number'	=> $row->mission_number,
					'title'				=> $row->title,
					'description'		=> $row->description,
					'output'			=> $row->output,
					'whitelist'			=> $row->whitelist,
					'parameters'		=> (!empty($row->parameters)) ? $this->_parameter_decode($row->parameters) : $row->parameters,
					'default'			=> $row->default,
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
	
	//for encoding html between <code> </code>
	//won't work if there is <code> inside <code></code>
	private function _htmlcode_encode($data){
	
		$data = preg_replace_callback(
			'/(<code[^>]*>)([\s\S]*?)(<\/code>)/m',
			function($matches){
				return $matches[1] . htmlspecialchars($matches[2], ENT_COMPAT|ENT_HTML5, 'UTF-8', false) . $matches[3];
			},
			$data
		);
		
		return $data;
		
	}
	
	//this should be based on number
	public function get_mission($num){
	
		$query = $this->db->get_where('missions', array('mission_number'=>$num));
		
		$mission = false;
		
		if($query->num_rows() > 0){
		
			$row = $query->row();
			
			#var_dump($row->description);
			
			$mission = array(
				'id'				=> $row->id,
				'mission_number'	=> $row->mission_number,
				'title'				=> $row->title,
				'description'		=> $row->description,
				'output'			=> $row->output,
				'parameters'		=> (!empty($row->parameters)) ? $this->_parameter_decode($row->parameters) : $row->parameters,
				'whitelist'			=> $row->whitelist,
				'default'			=> $row->default,
			);
		
		}
				
		return $mission;
	
	}
	
	public function update_mission($num, $updated_mission){
	
		if(!empty($updated_mission['parameters'])){
			
			$updated_mission['parameters'] = $this->_parameter_encode($updated_mission['parameters']);
			if(!$updated_mission['parameters']){
				return false;
			}
			
		}
		
		$updated_mission['description'] = $this->_htmlcode_encode($updated_mission['description']);
		
		$this->db->where('mission_number', $num);
		$this->db->update('missions', $updated_mission);
		
		if($this->db->affected_rows() > 0){
			return $this->db->affected_rows();
		}
		
		return false;
		
	}
	
	//need to add in delete mission
	public function delete_mission($id){
	
	}
	
	public function add_mission($new_mission){
	
		if(!empty($new_mission['parameters'])){
			
			$new_mission['parameters'] = $this->_parameter_encode($new_mission['parameters']);
			if(!$new_mission['parameters']){
				return false;
			}
			
		}
		
		$new_mission['description'] = $this->_htmlcode_encode($new_mission['description']);
		
		$query = $this->db->insert('missions', $new_mission);
		
		if($query){
			return $this->db->affected_rows();
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