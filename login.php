<?php 
session_start();
$server = "localhost";
$username = "root";
$password = "";
$dbname = "misc";
$message="";
if(isset($_POST["cancel"]))  
      { 
		header("location:index.php"); 
	  }
try {
	$conn = new PDO("mysql:host=$server; dbname=$dbname", $username, $password);  
	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);  
     if(isset($_POST["Login"]))  
      { 	
			$email=htmlentities($_POST["email"]);
			$pass=htmlentities($_POST["pass"]);
			$query = "SELECT user_id, name FROM users WHERE email = :email AND password = :password"; 
			$salt = 'XyZzy12*_';
				$check = hash('md5', $salt.htmlentities($_POST["pass"]));
                $statement = $conn->prepare($query);  
                $statement->execute(array(  
                          'email'     =>     htmlentities($_POST["email"]),  
                          'password'  =>      $check)); 
				$row = $statement->fetch(PDO::FETCH_ASSOC);
                if ( $row !== false ) {
					$_SESSION['name'] = $row['name'];
					$_SESSION['user_id'] = $row['user_id'];
					// Redirect the browser to index.php
                    header("location:index.php");  }  
                else  
                {  
                     $message = '<label>Invalid Data</label>';  
                } 
	  }
	}
catch(PDOException $error)  
 {  
      $message = $error->getMessage();  
 } 
 
?>


<!DOCTYPE html>
<html>
<head>
<title>Aleena C R</title>
<!-- bootstrap.php - this is HTML -->

<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" 
    href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" 
    integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" 
    crossorigin="anonymous">

<!-- Optional theme -->
<link rel="stylesheet" 
    href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" 
    integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" 
    crossorigin="anonymous">

</head>
<body>
<div class="container">
<h1>Please Log In</h1>
<?php  
    if(isset($message))  
    {  
        echo '<label class="message">'.$message.'</label>';  
    }  
?> 
<form method="POST" action="login.php">
<label for="email">Email</label>
<input type="text" name="email" id="email"><br/>
<label for="id_1723">Password</label>
<input type="password" name="pass" id="id_1723"><br/>
<input type="submit" onclick="return doValidate();" value="Log In" name="Login">
<input type="submit" name="cancel" value="cancel">
</form>

<script>
function doValidate() {
    console.log('Validating...');
    try {
        addr = document.getElementById('email').value;
        pw = document.getElementById('id_1723').value;
        console.log("Validating addr="+addr+" pw="+pw);
        if (addr == null || addr == "" || pw == null || pw == "") {
            alert("Both fields must be filled out");
            return false;
        }
        if ( addr.indexOf('@') == -1 ) {
            alert("Invalid email address");
            return false;
        }
        return true;
    } catch(e) {
        return false;
    }
    return false;
}
</script>

</div>
</body>
</html>
