<?php
session_start();

//linking required files
require_once "conn.php";
require_once "util.php";

//action for "cancel"
if(isset($_POST["cancel"]))  { 
    header("location:index.php"); 
}

//when user is not logged in
if (!isset($_SESSION['user_id'])){
    die("You need to login");
}

//Action for "Add"
if(isset($_POST["Add"]))  {
    if( empty(htmlentities($_POST["first_name"])) ||empty(htmlentities($_POST["last_name"])) || empty(htmlentities($_POST["headline"]))
		|| empty(htmlentities($_POST["email"])) || empty(htmlentities($_POST["summary"]))){
            $_SESSION['error'] = 'All values are required';
            header("Location: add.php");
            return;
        }
    elseif (strpos($_POST['email'], '@') === false) {
            $_SESSION['error'] =  "Email must contain @";
            header("Location: add.php");
            return;
    }
    if(! validatePos()){
        $_SESSION['error'] = validatePos();
        header("Location: add.php");
        return;
    }
    if(! validateEdu()){
        $_SESSION['error'] = validateEdu();
        header("Location: add.php");
        return;
    }
    // Insert to databse
			$fname=htmlentities($_POST["first_name"]);
			$lname=htmlentities($_POST["last_name"]);
			$headline=htmlentities($_POST["headline"]);
			$email=htmlentities($_POST["email"]);
			$summary=htmlentities($_POST["summary"]);
			$uid=$_SESSION['user_id'];
			$query = "INSERT INTO profile(user_id,first_name,last_name,email,headline,summary) 
			VALUES( :uid,:fname,:lname,:email,:headline,:summary )"; 
    try{
            $statement = $conn->prepare($query);  
            $statement->execute(array( 
                            ':uid'       =>     $uid,
                            ':fname'     =>     $fname,  
                            ':lname'     =>     $lname,
                            ':email'     =>     $email,
                            ':headline'  =>     $headline,
                            ':summary'   =>     $summary)); 
            $prof_id=$conn->lastInsertId();
            
            //insert position 
            $rank=1;
            for($i=1;$i<=9;$i++){
                    if( ! isset($_POST['year'.$i])) continue;
                    if( ! isset($_POST['desc'.$i])) continue;
                    if ( strlen($year) == 0 || strlen($desc) == 0 ) {
                        $_SESSION['error'] = "All fields are required !!!";
                        header("Location: add.php");
                        return;
                    }
            
                    elseif ( ! is_numeric($year) ) {
                        $_SESSION['error'] = "Position year must be numeric!!! !!!";
                        header("Location: add.php");
                        return;
                    }
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
            
            //insert education
            $edu_rank=1;
            for($i=1;$i<=9;$i++){
                    if( ! isset($_POST['edu_year'.$i])) continue;
                    if( ! isset($_POST['edu_school'.$i])) continue;
                    $edu_year=$_POST['edu_year'.$i];
                    $school=$_POST['edu_school'.$i];
                    if ( strlen($edu_year) == 0 || strlen($school) == 0 ) {
                        $_SESSION['error'] = "All fields are required !!!";
                        header("Location: add.php");
                        return;
                    }
                    
                    elseif ( !is_numeric($edu_year) ) {
                        $_SESSION['error'] = "Education year must be numeric!!! !!!";
                        header("Location: add.php");
                        return;
                    }

                    // looking for institution id
                    $stmt = $conn->prepare('SELECT institution_id FROM institution WHERE name= :name ');
                    $stmt->execute(array(
                        ':name' => $school
                        )
                    );
                    
                    $stmt =$stmt ->fetch();
                    $school_bool = false;
                    if ( $stmt !== false ){
                        $school_id = $stmt['institution_id'];
                        $school_bool = true;
                    }
                    if ($school_bool == false){
                        $statement = $conn->prepare('INSERT INTO institution(name) VALUES (:school)');
                        $statement->execute(array(
                            ':school' => $school)
                        );
                        $school_id = $conn->lastInsertId();
                    }
                    echo $school_id." **". $edu_year."** ". $edu_rank. "** ".$prof_id;
                    //insert education details to database
                $stmt = $conn->prepare('INSERT INTO Education
                    (profile_id, institution_id, year, rank)
                    VALUES ( :pid, :institution, :edu_year, :rank)');
                
                
                            $stmt->execute(array(
                                    ':pid' => $prof_id,
                                    ':institution' => $school_id,
                                    ':edu_year' => $edu_year,
                                    ':rank' => $edu_rank)
                            ); 
                    
                    echo "hii_evdeyane";
                    $edu_rank++;
            }

            $_SESSION['toast'] = "profile added*";
            header("location:index.php"); 
            return;
        }
        //exception handling
        catch(PDOException $error){
            $_SESSION['error']= "Unable to insert*";
            
        }
        
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Aleena C R</title>
<?php require_once "links.php"; ?>
</head>
<body>
<div class="container">
<?php
if(isset($_SESSION["user_id"]))  
	{
?>

<h1>Adding Profile for <?php echo $_SESSION["name"];?></h1>
<hr><div class="error_block">
<?php  
    flashmsg();
?>
<!--form -->
</div><hr>
<form method="POST" >
<label for="frist_name" >Frist Name :</label>
<input type="text" name="first_name" id="f_name" style="width:500px;" ><br/><br/>
<label for="last_name">Last Name :</label>
<input type="text" name="last_name" id="l_name" style="width:500px;" ><br/><br/>
<label for="email">Email :</label>
<input type="text" name="email" id="email" style="width:500px;" ><br/><br/>
<label for="headline">Headline :</label>
<input type="text" name="headline" id="headline" style="width:500px;" ><br/><br/>
<label for="summary">Summary :</label><br><br/>
<textarea  name="summary" id="summary" rows="10" cols="90">

</textarea><br/><br/>

<label for="position">Position :</label>
<input type="submit" name="addPos" id="addPos" value="+" >
<br><br/>
<div id="position_fields">
</div>
<label for="education">Education :</label>
<input type="submit" name="addEdu" id="addEdu" value="+" >
<br><br/>
<div id="edu_fields">
</div>
<input type="submit"  value="Add" name="Add" id="Add">
<input type="submit" name="cancel" value="Cancel">
</form>

<?php } ?>
</div>

<script>
countPos = 0;
countEdu =0 ;

$(document).ready(function(){
    window.console && console.log('Document ready called');

    //position feild
    $('#addPos').click(function(event){
        event.preventDefault();
        if ( countPos >= 9 ) {
            alert("Maximum of 9 position entries exceeded");
            return;
        }
        countPos++;
        window.console && console.log("Adding position "+countPos);
        $('#position_fields').append(
            '<div id="position'+countPos+'"> \
            <p>Year: <input type="text" name="year'+countPos+'" value="" style="width:500px;"/> \
            <input type="button" value="-" \
                onclick="$(\'#position'+countPos+'\').remove();return false;"/></p> \
            <textarea name="desc'+countPos+'" rows="8" cols="80"></textarea>\
            </div>');
    });


    //Education feild
    $('#addEdu').click(function(event){
        event.preventDefault();
        if ( countEdu >= 9 ) {
            alert("Maximum of 9 education entries exceeded");
            return;
        }
        countEdu++;
        window.console && console.log("Adding education "+countEdu);
        $('#edu_fields').append(
            '<div id="edu'+countEdu+'"> \
            <p>Year: <input type="text" name="edu_year'+countEdu+'" value="" style="width:500px;"/> \
            <input type="button" value="-" \
                onclick="$(\'#edu'+countEdu+'\').remove();return false;"/></p> \
            <p>School: <input type="text" size="80" name="edu_school' + countEdu + '" class="school" value="" />\
            </p></div>');

        $('.school').autocomplete({
                    source: "school.php"
        });

    });

});


</script>

</body>
</html>



