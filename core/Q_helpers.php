<?php 

class Q_helpers {

	public static $quinn = 'quinn';

	public static function hello(){
		echo self::$quinn;
	}

	public static function debug($var){
		echo '<pre>';
		print_r($var);
		echo '</pre>';
	}

	public static function flatten_array($input_arr,$what)
	{

		$flattened = array();

		foreach($input_arr as $key => $val)
		{
			$flattened[] = $val[$what];
		}

		return $flattened;

	}

	public static function remove($array, $unset)
	{

	}

	public static function pluck($array, $pluck)
	{
		$return_array = array();

		foreach($array as $k => $v)
		{
			$return_array[] = $v['Field'];
		}

		return $return_array;
	}

	public static function merge_array($array1, $array2 = null)
	{
		$merged_array = array(); 

		foreach($array1 as $k => $v)
		{
			$array1[$k]['value'] = $array2[$v['Field']];
		}

		//self::debug($array1);

		return $array1;
	}

	public static function loopCreate($retrieve_data, $ignore, $result = NULL){

		if($result != NULL)
		{
			$retrieve_data = self::merge_array($retrieve_data, $result);
		}
		//self::debug($retrieve_data);
		
		for($i=0; $i < count($retrieve_data); $i++){
			echo '<tr>';
			if(!in_array($retrieve_data[$i]['Field'], $ignore)):
				if($retrieve_data[$i]['Type'] == 'text')
				{
						echo '<td>';
						echo 	'<label>' . ucfirst(str_replace("q_","",$retrieve_data[$i]['Field'])) . '</label>';
						echo '</td>';
						echo '<td>';
						echo 	'<textarea class="qcrm__textarea" name="'.$retrieve_data[$i]['Field'].'" id="'.$retrieve_data[$i]['Field'].'">';
						if(isset($retrieve_data[$i]['value'])):
							echo $retrieve_data[$i]['value'];
						endif;
						echo '</textarea>';
						echo '</td>';
				}
				else
				{
						echo '<td>';
						echo 	'<label>' . ucfirst(str_replace("q_","",$retrieve_data[$i]['Field'])) . '</label>';
						echo '</td>';
						echo '<td>';
						echo 	'<input type="text" class="qcrm__input" name="'.$retrieve_data[$i]['Field'].'" id="'.$retrieve_data[$i]['Field'].'" value="';
						if(isset($retrieve_data[$i]['value'])):
							echo $retrieve_data[$i]['value'];
						endif;
						echo '" />';
						echo '</td>';
				}
				/*switch($retrieve_data[$i]['Type']){
					case 'text':
						echo '<td>';
						echo 	'<label>' . ucfirst(str_replace("q_","",$retrieve_data[$i]['Field'])) . '</label>';
						echo '</td>';
						echo '<td>';
						echo 	'<textarea name="'.$retrieve_data[$i]['Field'].'" id="'.$retrieve_data[$i]['Field'].'">';
						if(isset($retrieve_data[$i]['value'])):
							echo $retrieve_data[$i]['value'];
						endif;
						echo '</textarea>';
						echo '</td>';
					default:
						echo '<td>';
						echo 	'<label>' . ucfirst(str_replace("q_","",$retrieve_data[$i]['Field'])) . '</label>';
						echo '</td>';
						echo '<td>';
						echo 	'<input type="text" name="'.$retrieve_data[$i]['Field'].'" id="'.$retrieve_data[$i]['Field'].'" value="';
						if(isset($retrieve_data[$i]['value'])):
							echo $retrieve_data[$i]['value'];
						endif;
						echo '" />';
						echo '</td>';
				}*/
			endif;
			echo '</tr>';
		}
	}

}

