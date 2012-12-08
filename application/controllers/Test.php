<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Test extends CI_Controller {

	private $_view_data = array();

	public function __construct(){
	
		parent::__construct();
		$this->_view_data = $this->config->item('php_bounce');
		
	}
	
	public function index(){	
		
$test_code = '<?php
$y = str_replace(\'z\', \'e\', \'zxzc\');
$y("malicious code");
$my_chinese_surname = \'Qiu\';
';
		//init the parser
		$php_parser = new PHPParser_Parser(new PHPParser_Lexer);
		
		//resolve namespaces (using visitor pattern)
		$visitor_pattern = new PHPParser_NodeTraverser;
		$visitor_pattern->addVisitor(new PHPParser_NodeVisitor_NameResolver);
		
		//init the seraliser so we can query it later
		$php_xml_serialiser = new PHPParser_Serializer_XML;
		
		//produce a graph for analysis		
		$php_parser_error = false;
		try{
		
			$mission_graph = $php_parser->parse($test_code);
			$mission_graph = $visitor_pattern->traverse($mission_graph);
			$mission_graph = $php_xml_serialiser->serialize($mission_graph);
			
		}catch(PHPParser_Error $e){
		
			//unlikely to happen if lint has passed and whitelist passed...
			//there will be only 1 parse error, so just declare the array here
			$php_parser_error = $e->getMessage();
					
		}
		
		$mission_parameters2 = array(
			'variable_declaration'	=> array(
				'paths'	=> array(
					0 => array(
						'//node:Expr_Assign' => array(
							'subNode:var/node:Expr_Variable' => array(
								'subNode:name' => array(
									'scalar:string'
								),
							),
							'subNode:expr/node:Scalar_String/subNode:value/scalar:string',
						),
					),
					1 => array(
							'//node:Expr_Assign/subNode:var/node:Expr_Variable/subNode:name/scalar:string',
					),
				),
				'tests'	=> array(
					'Error, you need to make sure to declare a variable called [[my_chinese_surname]] with the value [[Qiu]]' => array(
						'my_chinese_surname',
						'Qiu'
					),
					'Error, you need to make sure to declare a variable called [[my_chinese_surname]]' => array(
						'my_chinese_surname',
					),
				),
			),
		);
		
		$this->missionchecker->init_options($mission_graph, $mission_parameters2);
		$this->missionchecker->graph_check();
		$mission_errors = $this->missionchecker->get_error_messages();
		
		echo '<pre><h2>MISSION ERRORS</h2>';
		var_dump($mission_errors);
		echo '</pre>';
	
	}
	
}