<?php
echo "<h1>Profile information</h1>";
// Database connection
session_start();
//linking required files
require_once "conn.php";
require_once "util.php";


	$_SESSION['view_id'] =$_GET['id'];  // get id through query string
	$id = $_SESSION['user_id'];
	$sql = "SELECT first_name,last_name,email,headline,summary FROM profile WHERE profile_id= :u_id";
	try{
	$statement = $conn->prepare($sql);  
                $statement->execute(array(  
                          'u_id'     =>   $_SESSION['view_id'] )); 
				$row = $statement->fetch(PDO::FETCH_ASSOC);
	if ( $row !== false ) {
					echo "<p>
							First Name :".htmlspecialchars($row['first_name']). "<br><br>
							Last Name :".htmlspecialchars($row['last_name'])."<br><br>
							Email :".htmlspecialchars($row['email'])."<br><br>
							Headline :<br>
							".htmlspecialchars($row['headline'])."	<br><br>
							Summary :<br>
							".htmlspecialchars($row['summary'])."	<br><br>
						";
						//position fields
						$quer = "SELECT year as yr,description as des FROM position WHERE profile_id= :u_id order by rank";
						$stmt=$conn->prepare($quer);  
						$stmt->execute(array(  
								  'u_id'     =>   $_SESSION['view_id'] )); 
						$positions = $stmt->fetchAll(PDO::FETCH_ASSOC);
						
						if ( $positions >0 ){
							echo "Position : <br><ul>";
							foreach($positions as $pos){
								echo "<li>".htmlspecialchars($pos['yr']).":".htmlspecialchars($pos['des'])."</li>";

							}
							echo "</ul>";
						}
						else{
							echo "None";
						}

						//education fields
						$schools = loadEdu( $conn , $_SESSION['view_id']);
						if ($schools > 0){
							echo "Education : <br><ul>";
							foreach($schools as $school){
								echo "<li>".htmlspecialchars($school['year']).":".htmlspecialchars($school['name'])."</li>";

							}
							echo "</ul>";
						}
						echo "</ul></p><a href='./'>Done</a>
						";
					 }  
    else  
                {  
					 $message = '<label>No Data Found</label>'; 
					 echo "<a href='./'>Done</a>"; 
                }
	}
catch(PDOException $error)  
 {  
      $message = $error->getMessage();  
 } 
 
 ?>