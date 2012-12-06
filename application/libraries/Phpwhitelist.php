<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
* Token sanitiser for php code
* DOES NOT SANITISE AGAINST CUSTOM NAME FUNCTIONS.. which is good
* DOES NOT SANITISE AGAINST CONTROL STRUCTURES AND LANGUAGE CONSTRUCTS
* THIS DOES NOT SANITISE CLASSES
*/
class Phpwhitelist{

	protected $_CI;
	//default whitelist of php CORE functions & classes(comma delimited)
	protected $_whitelist = 'true,false,function,abs,acos,acosh,addcslashes,addslashes,appenditerator,array,array_change_key_case,array_chunk,array_combine,array_count_values,array_diff,array_diff_assoc,array_diff_key,array_diff_uassoc,array_diff_ukey,array_fill,array_fill_keys,array_filter,array_flip,array_intersect,array_intersect_assoc,array_intersect_key,array_intersect_uassoc,array_intersect_ukey,array_key_exists,array_keys,array_map,array_merge,array_merge_recursive,array_multisort,array_pad,array_pop,array_product,array_push,array_rand,array_reduce,array_replace,array_replace_recursive,array_reverse,array_search,array_shift,array_slice,array_splice,array_sum,array_udiff,array_udiff_assoc,array_udiff_uassoc,array_uintersect,array_uintersect_assoc,array_uintersect_uassoc,array_unique,array_unshift,array_values,array_walk,array_walk_recursive,arrayaccess,arrayiterator,arrayobject,arsort,asin,asinh,asort,atan,atan2,atanh,autoload,badfunctioncallexception,badmethodcallexception,base64_decode,base64_encode,base_convert,basename,bin2hex,bind_textdomain_codeset,bindec,bindtextdomain,boolval,cachingiterator,cal_days_in_month,cal_from_jd,cal_info,cal_to_jd,calcul_hmac,calculhmac,call_user_func,call_user_func_array,call_user_method,call_user_method_array,callbackfilteriterator,ceil,checkdate,checkdnsrr,chop,chr,chunk_split,class_alias,class_exists,class_implements,class_parents,class_uses,clearstatcache,closelog,closure,collator,compact,cond,connection_aborted,connection_status,connection_timeout,constant,convert_cyr_string,convert_uudecode,convert_uuencode,copy,cos,cosh,count,count_chars,countable,crack_check,crack_closedict,crack_getlastmessage,crack_opendict,crc32,create_function,crypt,current,date,date_add,date_create,date_create_from_format,date_date_set,date_default_timezone_get,date_default_timezone_set,date_diff,date_format,date_get_last_errors,date_interval_create_from_date_string,date_interval_format,date_isodate_set,date_modify,date_offset_get,date_parse,date_parse_from_format,date_sub,date_sun_info,date_sunrise,date_sunset,date_time_set,date_timestamp_get,date_timestamp_set,date_timezone_get,date_timezone_set,dateinterval,dateperiod,datetime,datetimezone,dcgettext,dcngettext,deaggregate,debug_backtrace,debug_print_backtrace,debug_zval_dump,decbin,dechex,decoct,define,defined,deg2rad,dgettext,die,directory,directoryiterator,dirname,dngettext,dotnet,dotnet_load,doubleval,each,easter_date,easter_days,echo,empty,emptyiterator,end,ereg,ereg_replace,eregi,eregi_replace,error_get_last,errorexception,exception,exit,exp,expect_expectl,expect_popen,explode,expm1,extract,ezmlm_hash,fflush,filter_has_var,filter_id,filter_input,filter_input_array,filter_list,filter_var,filter_var_array,filteriterator,floatval,flock,floor,flush,fmod,fnmatch,forward_static_call,forward_static_call_array,frenchtojd,fribidi_log2vis,func_get_arg,func_get_args,func_name,func_num_args,function_exists,gender,geoip_continent_code_by_name,geoip_country_code3_by_name,geoip_country_code_by_name,geoip_country_name_by_name,geoip_database_info,geoip_db_avail,geoip_db_filename,geoip_db_get_all_info,geoip_id_by_name,geoip_isp_by_name,geoip_org_by_name,geoip_record_by_name,geoip_region_by_name,geoip_region_name_by_code,geoip_time_zone_by_country_and_region,get_browser,get_called_class,get_class,get_class_methods,get_class_vars,get_current_user,get_declared_classes,get_declared_interfaces,get_declared_traits,get_defined_constants,get_defined_functions,get_defined_vars,get_extension_funcs,get_headers,get_html_translation_table,get_meta_tags,get_object_vars,get_parent_class,get_required_files,get_resource_type,getallheaders,getdate,getenv,gethostbyaddr,gethostbyname,gethostbynamel,gethostname,getimagesize,getimagesizefromstring,getrandmax,getrusage,gettext,gettimeofday,gettype,gmdate,gmmktime,gmstrftime,gopher_parsedir,gregoriantojd,halt_compiler,hash,hash_algos,hash_copy,hash_file,hash_final,hash_hmac,hash_hmac_file,hash_init,hash_pbkdf2,hash_update,hash_update_file,hash_update_stream,header,header_register_callback,header_remove,headers_list,headers_sent,hebrev,hebrevc,hex2bin,hexdec,highlight_file,highlight_string,html_entity_decode,htmlentities,htmlspecialchars,htmlspecialchars_decode,hypot,iconv,iconv_get_encoding,iconv_mime_decode,iconv_mime_decode_headers,iconv_mime_encode,iconv_set_encoding,iconv_strlen,iconv_strpos,iconv_strrpos,iconv_substr,idate,idn_to_ascii,idn_to_unicode,idn_to_utf8,ignore_user_abort,implode,import_request_variables,in_array,inclued_get_data,infiniteiterator,interface_exists,intl_error_name,intl_get_error_code,intl_get_error_message,intl_is_failure,intldateformatter,intval,invalidargumentexception,ip2long,iptcembed,iptcparse,is_a,is_array,is_bool,is_callable,is_dir,is_double,is_executable,is_file,is_finite,is_float,is_infinite,is_int,is_integer,is_link,is_long,is_nan,is_null,is_numeric,is_object,is_readable,is_real,is_resource,is_scalar,is_soap_fault,is_string,is_subclass_of,is_tainted,is_uploaded_file,is_writable,is_writeable,isset,iterator,iterator_apply,iterator_count,iterator_to_array,iteratoraggregate,iteratoriterator,java_last_exception_clear,java_last_exception_get,join,json_decode,json_encode,json_last_error,jsonserializable,key,krsort,ksort,lapack,lapackexception,lcfirst,lcg_value,lengthexception,levenshtein,limititerator,linkinfo,list,locale,localeconv,localtime,log,log10,log1p,logicexception,long2ip,lstat,ltrim,max,mb_check_encoding,mb_convert_case,mb_convert_encoding,mb_convert_kana,mb_convert_variables,mb_decode_mimeheader,mb_decode_numericentity,mb_detect_encoding,mb_detect_order,mb_encode_mimeheader,mb_encode_numericentity,mb_encoding_aliases,mb_ereg,mb_ereg_match,mb_ereg_replace,mb_ereg_replace_callback,mb_ereg_search,mb_ereg_search_getpos,mb_ereg_search_getregs,mb_ereg_search_init,mb_ereg_search_pos,mb_ereg_search_regs,mb_ereg_search_setpos,mb_eregi,mb_eregi_replace,mb_get_info,mb_http_input,mb_http_output,mb_internal_encoding,mb_language,mb_list_encodings,mb_output_handler,mb_parse_str,mb_preferred_mime_name,mb_regex_encoding,mb_regex_set_options,mb_split,mb_strcut,mb_strimwidth,mb_stripos,mb_stristr,mb_strlen,mb_strpos,mb_strrchr,mb_strrichr,mb_strripos,mb_strrpos,mb_strstr,mb_strtolower,mb_strtoupper,mb_strwidth,mb_substitute_character,mb_substr,mb_substr_count,mcrypt_cbc,mcrypt_cfb,mcrypt_create_iv,mcrypt_decrypt,mcrypt_ecb,mcrypt_enc_get_algorithms_name,mcrypt_enc_get_block_size,mcrypt_enc_get_iv_size,mcrypt_enc_get_key_size,mcrypt_enc_get_modes_name,mcrypt_enc_get_supported_key_sizes,mcrypt_enc_is_block_algorithm,mcrypt_enc_is_block_algorithm_mode,mcrypt_enc_is_block_mode,mcrypt_enc_self_test,mcrypt_encrypt,mcrypt_generic,mcrypt_generic_deinit,mcrypt_generic_end,mcrypt_generic_init,mcrypt_get_block_size,mcrypt_get_cipher_name,mcrypt_get_iv_size,mcrypt_get_key_size,mcrypt_list_algorithms,mcrypt_list_modes,mcrypt_module_close,mcrypt_module_get_algo_block_size,mcrypt_module_get_algo_key_size,mcrypt_module_get_supported_key_sizes,mcrypt_module_is_block_algorithm,mcrypt_module_is_block_algorithm_mode,mcrypt_module_is_block_mode,mcrypt_module_open,mcrypt_module_self_test,mcrypt_ofb,md5,md5_file,mdecrypt_generic,messageformatter,metaphone,method_exists,mhash,mhash_count,mhash_get_block_size,mhash_get_hash_name,mhash_keygen_s2k,microtime,mime_content_type,min,mktime,money_format,mt_getrandmax,mt_rand,mt_srand,multipleiterator,mutex,natcasesort,natsort,next,ngettext,nl2br,nl_langinfo,normalizer,nsapi_request_headers,nsapi_response_headers,nsapi_virtual,nthmac,number_format,numberformatter,ob_clean,ob_deflatehandler,ob_end_clean,ob_end_flush,ob_etaghandler,ob_flush,ob_get_clean,ob_get_contents,ob_get_flush,ob_get_length,ob_get_level,ob_get_status,ob_gzhandler,ob_iconv_handler,ob_implicit_flush,ob_inflatehandler,ob_list_handlers,ob_start,ob_tidyhandler,octdec,opendir,openlog,ord,outeriterator,outofboundsexception,outofrangeexception,output_add_rewrite_var,output_reset_rewrite_vars,overflowexception,override_function,pack,parentiterator,passthru,password_get_info,password_hash,password_needs_rehash,password_verify,pathinfo,pi,pos,pow,preg_filter,preg_grep,preg_last_error,preg_match,preg_match_all,preg_quote,preg_replace,preg_replace_callback,preg_split,prev,print,print_r,printf,property_exists,putenv,quickhashinthash,quickhashintset,quickhashintstringhash,quickhashstringinthash,quoted_printable_decode,quoted_printable_encode,quotemeta,rad2deg,rand,range,rangeexception,rawurldecode,rawurlencode,read_exif_data,realpath,realpath_cache_get,realpath_cache_size,recode,recode_string,recursivearrayiterator,recursivecachingiterator,recursivecallbackfilteriterator,recursivedirectoryiterator,recursivefilteriterator,recursiveiterator,recursiveiteratoriterator,recursiveregexiterator,recursivetreeiterator,reflection,reflectionclass,reflectionexception,reflectionextension,reflectionfunction,reflectionfunctionabstract,reflectionmethod,reflectionobject,reflectionparameter,reflectionproperty,reflectionzendextension,reflector,regexiterator,reset,resourcebundle,return,round,rsort,rtrim,seekableiterator,serializable,serialize,set_error_handler,set_exception_handler,set_time_limit,settype,sha1,sha1_file,shuffle,signeurlpaiement,similar_text,sin,sinh,sizeof,sleep,sort,soundex,sphinxclient,spoofchecker,sprintf,sql_regcase,sqrt,srand,sscanf,ssdeep_fuzzy_compare,ssdeep_fuzzy_hash,ssdeep_fuzzy_hash_filename,stackable,str_getcsv,str_ireplace,str_pad,str_repeat,str_replace,str_rot13,str_shuffle,str_split,str_word_count,strcasecmp,strchr,strcmp,strcoll,strcspn,strftime,strip_tags,stripcslashes,stripos,stripslashes,stristr,strlen,strnatcasecmp,strnatcmp,strncasecmp,strncmp,strpbrk,strpos,strptime,strrchr,strrev,strripos,strrpos,strspn,strstr,strtok,strtolower,strtotime,strtoupper,strtr,strval,substr,substr_compare,substr_count,substr_replace,taint,textdomain,time,time_nanosleep,time_sleep_until,timezone_abbreviations_list,timezone_identifiers_list,timezone_location_get,timezone_name_from_abbr,timezone_name_get,timezone_offset_get,timezone_open,timezone_transitions_get,timezone_version_get,token_get_all,token_name,trait_exists,transliterator,traversable,trigger_error,trim,uasort,ucfirst,ucwords,uksort,umask,underflowexception,unexpectedvalueexception,uniqid,unixtojd,unpack,unserialize,unset,untaint,urldecode,urlencode,use_soap_error_handler,user_error,usleep,usort,utf8_decode,utf8_encode,var_dump,var_export,vfprintf,virtual,vprintf,vsprintf,wordwrap';
	protected $_errors = array();
	protected $_test_code = '';
	
	public function __construct(){
	
		$this->_CI =& get_instance();
	
	}
	
	//whitelist can be an array or string (comma delimited)
	public function init_options($test_code, $whitelist = ''){
		
		if(!empty($whitelist)){
		
			if(is_array($whitelist)){
				$whitelist = implode(',', $whitelist);
			}
			
			$this->_whitelist = (empty($this->_whitelist))? $whitelist: $this->_whitelist . ',' . $whitelist;
			
		}
		
		$this->_test_code = $test_code;
		
		return true;
		
	}
	
	public function run_whitelist(){
	
		if(empty($this->_test_code) OR empty($this->_whitelist)){
			$this->_errors = array(
				'Please set up the options for whitelisting, we need testcode and whitelist.'
			);
			return false;
		}
		
		//get an array of all allowed functions
		$allowed_functions = explode(',', $this->_whitelist);
		//lets make this quicker (isset via keys)
		$allowed_functions = array_flip($allowed_functions);
		
		//setup the tokens
		$tokens = token_get_all($this->_test_code);
		
		#var_dump($tokens);
		
		//cycle through each token to do a check
		foreach($tokens as $token) {
		
		
			//not all tokens are arrays
			//the tokens that are arrays are the ones we can check against
			if(is_array($token)) {
				
				$function_id = $token[0];
				$function_name = $token[1];
				$line_number = $token[2];
				#var_dump($function_id);
				#var_dump($function_name);
				#var_dump($line_number);

				
				switch($function_id){
				
					//unwhitelisted custom functions will be caught here aswell
					case(T_CALLABLE):
					case(T_EVAL):
					case(T_FUNCTION):
					case(T_INCLUDE):
					case(T_INCLUDE_ONCE):
					case(T_REQUIRE):
					case(T_REQUIRE_ONCE):
					case(T_STRING):
					case(T_CLASS):
					{
						
						//if a particular function_name cannot be found within the allowed functions...
						if(!isset($allowed_functions[$function_name])){
							$this->_errors[] = 'Sorry this function call [' . $function_name . '] is not allowed on PHP Bounce, it is on line ' . $line_number;
						}
					
					}
					
					
				}
				
			}
		
		}
		
		return true;
	
	}
	
	public function get_errors(){
	
		if(empty($this->_errors)){
			return false;
		}
		
		return $this->_errors;
	
	}
	

}