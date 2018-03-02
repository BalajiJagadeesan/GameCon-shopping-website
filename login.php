<?php
/**
 * Created by PhpStorm.
 * User: Balaji Jagadeesan
 * Date: 03/13/2017
 * Time: 6:37 PM
 */

//Login Page
$title = "Login";
require_once "./assests/inc/page_start.inc.php";
require_once PATH_INC."header.inc.php";

$email=$password='';
//Check whether there is a session exisiting in DB ,if so rebuild the cookie and set session variables
_checkSession(session_id());

//Validator class for validation
$validate = new Validator();
//DB class intialisation for DB connectivity
$db = DB::getInstance();

//Check if form is submitted
if(!empty($_POST)){
    //Get the input variables and sanitize the input
    $email = _cleanInput($_POST['user-id']);
    $password = _cleanInput($_POST['password']);

    //Validate the input using validator class
    if(empty($email) || !$validate::_emailMatch($email)){
        $errors[] = "E-mail ID is invalid";
    }
    if (empty($password) || !$validate::_passwordStrength($password)) {
        $errors[] = "Password must be at least 4 characters, no more than 8 characters, and must include at least one upper case letter, one lower case letter, and one numeric digit.";
    }

    //Check user and password
    $result = _getUser($email);
    $errors[] = _legitPassword($email,$password);

    //If error print it
    if (!empty($errors[0])) {
        _printErrors($errors);
    }

    //Else regenerate the session id and store it in the session table and redirect
    if (empty($errors[0])) {
        session_regenerate_id(true);
        $_SESSION["loggedIn"] = true;
        set_cookies("loggedIn", date("F j,Y h:i A"));
        set_cookies("user",$result[0]['customer_name']);
        set_cookies("email",$result[0]['customer_email']);
        if(_storesession(session_id(),$result[0]['customer_id'])){
            if($result[0]['flag']==1) {
                header("Location:".URL_BASE."admin.php");
            }else{
                header("Location:".URL_BASE."product.php");
            }
        }else{
            echo "<p>Some problem in adding session to db</p>";
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
                        <!--Form header-->
                        <div class="form-header orange lighten-2">
                            <h3><i class="fa fa-lock"></i> Login:</h3>
                        </div>

                        <!--Form Body-->
                        <div class="md-form">
                            <i class="fa fa-envelope prefix"></i>
                            <input type="email" id="user-id" name="user-id" class="form-control" value="<?=$email?$email:'';?>">
                            <label for="user-id">Your email</label>
                        </div>

                        <div class="md-form">
                            <i class="fa fa-lock prefix"></i>
                            <input type="password" id="password" name="password" class="form-control" value="<?=$password?$password:'';?>">
                            <label for="password">Your password</label>
                        </div>

                        <div class="text-center">
                            <button class="btn orange lighten-2" name="login">Login</button>
                        </div>
                    </form>
                </div>
                <!--Form Fotter-->
                <div class="modal-footer">
                    <div class="options">
                        <p>Not a member? <a href="<?=URL_BASE?>signup.php">Sign Up</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?
require_once PATH_INC."footer.inc.php";
?>

