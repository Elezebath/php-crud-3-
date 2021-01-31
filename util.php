<?php

function flashmsg(){
    if(isset($_SESSION['toast']))  
    {  
        echo '<label class="toast">'.$_SESSION['toast'].'</label>'; 
		unset($_SESSION['toast']);
    }else if(isset($_SESSION['error']))  
    {  
        echo '<label class="error">'.$_SESSION['error'].'</label>';
		unset($_SESSION['error']);
    } 
}

function validateProf(){
    
	if( empty(htmlentities($_POST["first_name"])) ||empty(htmlentities($_POST["last_name"])) || empty(htmlentities($_POST["headline"]))
		|| empty(htmlentities($_POST["email"])) || empty(htmlentities($_POST["summary"])))
			{
				return  "All values are required";
            }
    if (strpos($_POST['email'], '@') === false) {
                return "Email must contain @";
            }
     return true;
}

function validatePos() {
    for($i=1; $i<=9; $i++) {
      if ( ! isset($_POST['year'.$i]) ) continue;
      if ( ! isset($_POST['desc'.$i]) ) continue;
  
      $year = $_POST['year'.$i];
      $desc = $_POST['desc'.$i];
  
      if ( strlen($year) == 0 || strlen($desc) == 0 ) {
        return "All fields are required";
      }
  
      if ( ! is_numeric($year) ) {
        return "Position year must be numeric";
      }
    }
    return true;
}


function validateEdu() {
    for($i=1; $i<=9; $i++) {
      if ( ! isset($_POST['edu_year'.$i]) ) continue;
      if ( ! isset($_POST['school'.$i]) ) continue;
  
      $year = $_POST['edu_year'.$i];
      $school = $_POST['school'.$i];
  
      if ( strlen($edu_year) == 0 || strlen($school) == 0 ) {
        return "All fields are required";
      }
  
      if ( ! is_numeric($edu_year) ) {
        return "Education year must be numeric";
      }
    }
    return true;
}

function loadProf($conn){
    $sql = "SELECT first_name,last_name,email,headline,summary FROM profile WHERE profile_id= :u_id";
	$statement = $conn->prepare($sql);  
    $statement->execute(array(  'u_id'     =>   $_SESSION['view_id'] )); 
    $row = $statement->fetch(PDO::FETCH_ASSOC);
    return $row;
}

function loadPosition($conn, $pro_id ){
    $quer = "SELECT year as yr,description as des FROM position WHERE profile_id= :pro_id order by rank";
	$stmt=$conn->prepare($quer);  
	$stmt->execute(array('pro_id'     =>   $pro_id )); 
    $positions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $positions;
}

function loadEdu($conn, $pro_id ){
    $query = "SELECT year,name FROM education,institution 
    WHERE education.institution_id = institution.institution_id AND education.profile_id= :prof";
    $stmt=$conn->prepare($query);  
	$stmt->execute(array('prof'     =>   $pro_id )); 
    $positions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $positions;

}


function insertPos($conn,$pro_id){
    $rank=1;
    for($i=1;$i<=9;$i++){
            if( ! isset($_POST['year'.$i])) continue;
            if( ! isset($_POST['desc'.$i])) continue;
            $year=$_POST['year'.$i];
            $desc=$_POST['desc'.$i];
            $stmt = $conn->prepare('INSERT INTO Position
            (profile_id, rank, year, description) 
            VALUES ( :pid, :rank, :year, :desc)');
            $stmt->execute(array(
                ':pid' => $prof_id,
                ':rank' => $rank,
                ':year' => $year,
                ':desc' => $desc)
            );
        $rank++;
    }    
}

function insertEdu($conn,$pro_id){
    $edu_rank=1;
    for($i=1;$i<=9;$i++){
            if( ! isset($_POST['edu_year'.$i])) continue;
            if( ! isset($_POST['school'.$i])) continue;
            $edu_year=$_POST['edu_year'.$i];
            $school=$_POST['school'.$i];

            // looking for institution id
            $stmt = $conn->prepare('SELECT institution_id FROM institution WHERE name= :name ');
            $stmt->execute(array(
                ':name' => $school
                )
            );
           
            $stmt =$stmt ->fetch();
            $school_id = false;
            if ( $stmt !== false ){
                $school_id = $stmt['institution_id'];
            }
            if ($school_id == false){
                $statement = $conn->prepare('INSERT INTO institution(name) VALUES (:school)');
                $statement->execute(array(
                    ':school' => $school)
                );
                $school_id = $conn->lastInsertId();
            }

            echo $school_id;
            //insert education details to database
            $stmt = $conn->prepare('INSERT INTO education( profile_id,institution_id ,rank,year ) VALUES ( :pro_id, :inst_id, :rank, :school)');
            $stmt->execute(array(
                ':pid' => $prof_id,
                ':school_id' => $school_id,
                ':rank' => $edu_rank,
                ':year' => $edu_year)
            );
        $edu_rank++;
    }
}