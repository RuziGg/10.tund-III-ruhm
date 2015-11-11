<?php
class InterestsManager{
	
	// InterestsManager.class.php
	
	private $connection;
	private $user_id;
	
	//kui tekitan new, siis kaivitakse see funktsioon
	function __construct($mysqli, $user_id_from_session){
		
		//selle klassi muutuja
		$this->connection = $mysqli;
		$this->user_id = $user_id_from_session;
		
		echo "Huvialade haldus kaivitatud, kasutaja=".$this->user_id;
		
	}
	
	
	function addInterest($new_interest){
		
		// teen objekti 
		// seal on error, ->id ja ->message
		// voi success ja sellel on ->message
		$response = new StdClass();
		
		//kas selline huviala on juba olemas
		$stmt = $this->connection->prepare("SELECT id FROM interests WHERE name=?");
		$stmt->bind_param("s", $new_interest);
		$stmt->bind_result($name);
		$stmt->execute();
		
		// kas sain rea andmeid
		if($stmt->fetch()){
			
			// annan errori, et selline huviala olemas
			$error = new StdClass();
			$error->id = 0;
			$error->message = "Huviala <strong>".$new_interest."</strong> on juba olemas!";
			
			$response->error = $error;
			
			// koik mis on parast returni enam ei kaivitata
			return $response;
			
		}
		
		// panen eelmise paringu kinni
		$stmt->close();
		
		$stmt = $this->connection->prepare("INSERT INTO interests (name) VALUES (?)");
		$stmt->bind_param("s", $new_interest);
		
		// sai edukalt salvestatud
		if($stmt->execute()){
			
			$success = new StdClass();
			$success->message = "Huviala on lisatud!";
			
			$response->success = $success;
			
		}else{
			
			// midagi laks katki
			$error = new StdClass();
			$error->id = 1;
			$error->message = "Midagi laks katki!";
			
			$response->error = $error;
			
		}
		
		$stmt->close();
		
		return $response;
	}
		
	function createDropdown(){
		
		$html = '';
		
		$html .= '<select name="new_dd_selection">';
		
		//$html .= '<option>1</option>';
		
		//$html .= '<option>2</option>';
		
		//$html .= '<option>3</option>';
		
		$stmt = $this->connection->prepare("Select id, name FROM interests");
		$stmt->bind_result($id, $name);
		$stmt->execute();
		
		//iga rea kohta
		while($stmt->fetch()){
			
			$html .= '<option value="'.$id.'">'.$name.'</option>';
			
		}
		
		$html .= '</select>';
		return $html;
		
	}
	
	function addUserInterest($new_interest_id){
		
		// teen objekti 
		// seal on error, ->id ja ->message
		// voi success ja sellel on ->message
		$response = new StdClass();
		
		//kas sellel kasutajal on see huviala
		$stmt = $this->connection->prepare("SELECT id FROM user_interests WHERE user_id=? AND interests_id=?");
		$stmt->bind_param("ii", $this->user_id, $new_interest_id);
		$stmt->bind_result($interests_id);
		$stmt->execute();
		
		// kas sain rea andmeid
		if($stmt->fetch()){
			
			// annan errori, et selline huviala olemas
			$error = new StdClass();
			$error->id = 0;
			$error->message = "Huviala on sinul juba olemas!";
			
			$response->error = $error;
			
			// koik mis on parast returni enam ei kaivitata
			return $response;
			
		}
		
		// panen eelmise paringu kinni
		$stmt->close();
		
		$stmt = $this->connection->prepare("INSERT INTO user_interests (user_id, interests_id) VALUES (?,?)");
		$stmt->bind_param("ii", $this->user_id, $new_interest_id);
		
		// sai edukalt salvestatud
		if($stmt->execute()){
			
			$success = new StdClass();
			$success->message = "Huviala on lisatud!";
			
			$response->success = $success;
			
		}else{
			
			// midagi laks katki
			$error = new StdClass();
			$error->id = 1;
			$error->message = "Midagi laks katki!";
			
			$response->error = $error;
			
		}
		
		$stmt->close();
		
		return $response;
		
	}
	
	
	
}?>