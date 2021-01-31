<html>
<head>
<title>Delete</title>
<link rel='stylesheet' 
    href='https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css' 
    integrity='sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7' 
    crossorigin='anonymous'>
<link rel='stylesheet' 
    href='https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css' 
    integrity='sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r' 
    crossorigin='anonymous'>
</head>
<body>
<?php
// Database connection
session_start();
$server = "localhost";
$username = "root";
$password = "";
$dbname = "misc";
$message="";
$conn = new PDO("mysql:host=$server; dbname=$dbname", $username, $password);  
	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);  
	$_SESSION['delete_id'] =$_GET['id'];  // get id through query string
	$id = $_SESSION['user_id'];
	$sql = "SELECT first_name,last_name FROM profile WHERE profile_id= :u_id";
	try{
	$statement = $conn->prepare($sql);  
                $statement->execute(array(  
                          'u_id'     =>   $_SESSION['delete_id'] )); 
				$row = $statement->fetch(PDO::FETCH_ASSOC);
	if ( $row !== false ) {
					echo "<p>
							First Name :".htmlspecialchars($row['first_name']). "<br><br>
							Last Name :".htmlspecialchars($row['last_name'])."<br><br>
						</p>";
					 }  
                else  
                {  
                     $message = '<label>No Data</label>';  
                }
	}
catch(PDOException $error)  
 {  
      $message = $error->getMessage();  
 } 
 if(isset($_POST["Cancel"])) {
	header("location:index.php"); 
 }
 if(isset($_POST["Delete"]))  {
		
		$query = "DELETE FROM profile WHERE profile_id = :id";
		 
		try{
		$statement = $conn->prepare($query);  
        $statement->execute(array( 
						'id'        =>     $_SESSION['delete_id'])); 
		$statement=null;
		$_SESSION['toast']='profile deleted';
		header("location:index.php"); 
		}
		catch(PDOException $error){
			$_SESSION['error']='Could not connect to the profile';	
		}
		

}
 
 ?>
 <form id="delete" method="post" >
 <br>
 <input type="submit" name="Delete" value="Delete">
 <input type="submit" name="Cancel" value="Cancel">
 <br>
 </form>
</body>
</html>