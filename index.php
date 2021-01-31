<!DOCTYPE html>
<html><head>
<title>Aleena C R</title>
<link rel="stylesheet" 
    href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" 
    integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" 
    crossorigin="anonymous">

<link rel="stylesheet" 
    href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" 
    integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" 
    crossorigin="anonymous">

<link rel="stylesheet" 
    href="https://code.jquery.com/ui/1.12.1/themes/ui-lightness/jquery-ui.css">
<script
  src="https://code.jquery.com/jquery-3.2.1.js"
  integrity="sha256-DZAnKJ/6XZ9si04Hgrsxu/8s717jcIzLy3oi35EouyE="
  crossorigin="anonymous"></script>
<script
  src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"
  integrity="sha256-T0Vest3yCU7pafRw9r+settMBX6JkKN06dqBnpQ8d30="
  crossorigin="anonymous"></script>
<style>
table, td, th {
  border: 1px solid black;
  height:auto;
}

table {
  width: 25%;
  border-collapse: collapse;
}
.toast{
	color:green;
}
.error{
	color:red;
}
</style>
</head>
<body><div class='container' >
<h1>Aleena's Resume Registry</h1>

<?php
session_start();
 if(isset($_SESSION['toast']))  
    {  
        echo '<label class="toast">'.$_SESSION['toast'].'</label>'; 
		unset($_SESSION['toast']);
    }else if(isset($_SESSION['error']))  
    {  
        echo '<label class="error">'.$_SESSION['error'].'</label>';
			unset($_SESSION['error']);
    } 
$server = "localhost";
$username = "root";
$password = "";
$dbname = "misc";
$message='';
try {
	$conn = new PDO("mysql:host=$server; dbname=$dbname", $username, $password);  
	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 
	
	
//login view
	if(isset($_SESSION["user_id"]))  
	{
    echo "[". $_SESSION['name'].$_SESSION['user_id']."]"; 
	echo "<h5><a href='logout.php'> Logout</a></h5>";
    $id = $_SESSION['user_id'];
   	$sql = "SELECT profile_id,first_name,last_name,headline FROM profile WHERE user_id='$id'";
	$q = $conn->query($sql);
    $q->setFetchMode(PDO::FETCH_ASSOC);
    $rows = $q -> fetchAll(PDO::FETCH_ASSOC);
    if ($rows >0){
	            echo " <table class='logout_table' >
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Headline</th>
						<th>Action</th>
                    </tr>
                </thead>
                <tbody>";
                
                
                foreach ($rows as $row)
                        echo "  
                        <tr>
                                <td><a href='view.php?id=".htmlspecialchars($row['profile_id'])."'>".htmlspecialchars($row['first_name']) ." ".htmlspecialchars($row['last_name'])."</a></td>
                                <td>".htmlspecialchars($row['headline'])."</td>
                                <td><a href='edit.php?id=".htmlspecialchars($row['profile_id'])."'>Edit</a>
                                &nbsp;<a href='delete.php?id=".htmlspecialchars($row['profile_id'])."'>Delete</a></td>
                            </tr>";
                
                    echo "</tbody>";
    }
    else{
        echo "<p style='color : green;'>No Record Found </p>";
    }
    echo "<h5><a href='add.php'>Add New Entry</a></h5>
	</div>";
	}
	

	
//logout view
else {
	echo "<h5><a href='login.php'> Please log in</a></h5>";
	$sql = 'SELECT first_name,last_name,headline FROM profile';
    $q = $conn->query($sql);
    $q->setFetchMode(PDO::FETCH_ASSOC);
    if ($q->fetchAll(PDO::FETCH_ASSOC) > 0){
	    echo " <table class='logout_table'>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Headline</th>
                    </tr>
                </thead>
				<tbody>";
                while ($row = $q->fetchAll(PDO::FETCH_ASSOC)): 
                    echo "    <tr>
                            <td>".htmlspecialchars($row['first_name']) ." ".htmlspecialchars($row['last_name'])."</td>
                            <td>".htmlspecialchars($row['headline'])."</td>
                        </tr>";
                endwhile;
                echo "</tbody></div>";
        }
    else{
        echo "<p style='color :green;'>No Record Found</p>";
    }
	
}

}
catch(PDOException $error)  
 {  
      echo ($error->getMessage());  
 } ?>

</body>
</html>
