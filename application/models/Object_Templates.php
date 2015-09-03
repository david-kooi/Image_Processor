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
					   'checked'=>False,
					   'emptyOption'=>False
					);
		return $ratio;
	}

	function getOptionObject(){
		$option = array(
						'id'=>null,
						'ratio_id'=>null,
						'comp_id'=>null,
						'x_small'=>null,
						'y_small'=>null,
						'x_med'=>null,
						'y_med'=>null,
						'x_large'=>null,
						'y_large'=>null);
		return $option;
	}

	function getOptionResponse($optionsList, $ratioList){
		$response = array('optionsList'=>$optionsList,
						  'ratioList'=>$ratioList,
						  );
		return $response;
	}

	function getProcessResponse(){
		$response = array(
					'status'=>null,
					'companyPath'=>null,
					'ratioUsed'=>null,
					'x_small'=>null,
					'x_med'=>null,
					'x_large'=>null,
					'imagesProcessed'=>null
			);
		return $response;
	}

}