<?php
/**
 * Created by PhpStorm.
 * User: Balaji Jagadeesan
 * Date: 03/13/2017
 * Time: 6:37 PM
 */

//Signup Page
$title = "Signup";


require_once "./assests/inc/page_start.inc.php";
require_once PATH_INC."header.inc.php";

//Validator class for validation
$validate = new Validator();
$validate = new Validator();
//DB class intialisation for DB connectivity
$db = DB::getInstance();

//Check if form is submitted
if(!empty($_POST)){
    //Get the input variables and sanitize the input
    $name = _cleanInput(ucfirst($_POST['f-name'])." ".ucfirst($_POST['l-name']));
    $email = _cleanInput($_POST['user-id']);
    $password = _cleanInput($_POST['password']);

    //Validate the input using validator class
    if (empty($name) || !$validate::_nameOfPerson($name)  ) {
        $errors[] = "The name has invalid characters";
    }
    if(empty($email) || !$validate::_emailMatch($email)){
        $errors[] = "E-mail ID is invalid";
    }
    if (empty($password) || !$validate::_passwordStrength($password)) {
        $errors[] = "Password must be at least 4 characters, no more than 8 characters, and must include at least one upper case letter, one lower case letter, and one numeric digit.";
    }

    //Check if email already exists
    if(_checkUser($email)){
        $errors[] = "User already Exists.Try another username";
    }

    //print error message if any
    if (isset($errors)) {
        _printErrors($errors);
    }
    //If no error insert the user in database and hash the password using PasswordHash class
    if (!isset($errors)) {
        $queryUser = "INSERT INTO customer(customer_name,customer_email,customer_password) VALUES (?,?,?)";
        $inputPeople = array($name,$email,_hashPassword($password));
        $datatypePeople = array("s","s","s");
        $db->do_query($queryUser,$inputPeople,$datatypePeople);
        echo $db->get_insert_id();

        //If success print the customer id
        if($db->get_error()){
            echo "ERROR in query processing.Please Try again!!!";
        }else{
            echo "<h4 class='text-center'>Customer ID is ".($db->get_insert_id())." is Successfully Added!!Please Login with your credentials</h4>";
        }
    }
}
?>
<div class="container">
    <div class="row">
        <div class="col-md-4 offset-md-4">
            <div id="pad" class="card">
                <div class="card-block">
                    <form method="post" target="_self">
                        <div class="form-header orange lighten-2">
                            <h3><i class="fa fa-sign-in"></i> SignUp:</h3>
                        </div>

                        <div class="md-form">
                            <input type="text" id="f-name" name="f-name" class="form-control">
                            <label for="f-name">First Name</label>
                        </div>

                        <div class="md-form">
                            <input type="text" id="l-name" name="l-name" class="form-control">
                            <label for="l-name">Last Name</label>
                        </div>
                        <div class="md-form">
                            <input type="email" id="user-id" name="user-id" class="form-control">
                            <label for="user-id">Your email</label>
                        </div>

                        <div class="md-form">
                            <input type="password" id="password" name="password" class="form-control">
                            <label for="password">Your password</label>
                        </div>

                        <div class="text-center">
                            <button class="btn orange lighten-2" name="signup">Signup</button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?
require_once PATH_INC."footer.inc.php";
?>

