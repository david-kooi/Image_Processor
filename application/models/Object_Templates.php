<?

class Object_Templates extends CI_Model{
	
	function getResponseObject(){

		$response = array(
						 'header' => '',
						 'data' => array()
		);
		return $response;
	}

	function getCompanyObject(){
		$company = array(
					     'id'=>null,
					     'name'=>null
					     );
		return $company;
	}

	function getRatioObject(){
		$ratio = array(
					   'id'=>null,
					   'name'=>null,
					   'value'=>null,
					   'fk_comp'=>null,
					   'checked'=>False
					);
		return $ratio;
	}

}