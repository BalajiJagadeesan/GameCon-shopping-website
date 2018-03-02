<?php

/**
 * To load necessary class files
 * @param $classname - name of the class
 */

function __autoload($classname){
    require_once PATH_CLASS.$classname.".class.php";
}

/**
 * To Hash the password using passwordhash class
 * @param $pass - password to be hashed
 * @return string - the hashed result
 */
function _hashPassword($pass){
    require_once PATH_LIB."PasswordHash.php";
    $hasher = new PasswordHash(8,FALSE);
    $hash = $hasher->HashPassword($pass);
    return $hash;
}

/**
 * To check the given by user matches the one in database
 * @param $pass - password
 * @param $hash - hashed password from database
 * @return bool - result of the check
 */
function _hashCheck($pass,$hash){
    require_once PATH_LIB."PasswordHash.php";
    $hasher = new PasswordHash(8,FALSE);
    //Inbuild function to check the hash
    return $hasher->CheckPassword($pass, $hash);
}

/**
 * To get details of the user
 * @param $email - the email id of the user
 * @return array - the data about the user from the table
 */
function _getUser($email){
    $db=DB::getInstance();
    $query = "SELECT * from customer WHERE customer_email=?";
    $db->do_query($query,array($email),array("s"));
    if(!$db->get_error()){
        return $db->fetch_all_array();
    }
}

/**
 * To check whether the email already exists in database
 * @param $email - email
 * @return bool - the result of the check
 */
function _checkUser($email){
    $result = _getUser($email);
    if(sizeof($result)!=0){
        return true;
    }
    return false;
}

/**
 * To print the error
 * @param $errors - array of errors
 */
function _printErrors($errors)
{
    if (isset($errors)) {
        echo "<div class='text-center'>";
        foreach ($errors as $error) {
            if (is_array($error)) {
                _printErrors($error);
            } else {
                echo "<h6 style='color:red'>*{$error}</h6>";
            }
        }
        echo "</div>";
    }
}

/**
 * To check whether the user is legit
 * @param $email - email id
 * @param $password - password provided by user
 * @return array - error message
 */
function _legitPassword($email,$password){
    $errors=array();
    $result= _getUser($email);
    if(empty($result)){
        $errors[]="No user exists";
    }
    if(sizeof($result)>1){
        $errors[] = "Duplicate rows returned,problem with database";
    }
    if(!_hashCheck($password,$result[0]['customer_password'])){
        $errors[]="Password is incorrect";
    }
    return $errors;
}

/**
 * To store the session in database
 * @param $sid - The session id
 * @param $id - the customer_id of the user
 * @return bool - the result of the query ( true or false )
 */
function _storesession($sid,$id){
    $db=DB::getInstance();
    $query = "INSERT INTO session_table(sessionID,customer_id) VALUES (?,?)";
    $db->do_query($query,array($sid,$id),array("s","i"));
    if(!$db->get_error()){
        return true;
    }
    return false;
}

/**
 * Function to create cookie that is particular to the application directory
 * @param $name - Name of the cookie
 * @param $value - Value of the cookie
 */
function set_cookies($name,$value){
    $expire = time() + 1000 * 60;
    $path = "/~bxj9142/commerce/"; //Path as specific as possible -Path to homepage
    $domain = "kelvin.ist.rit.edu";
    $secure = false;//accessible on HTTP (not just HTTPS)
    $http_only = false; //accessible to JS
    setcookie($name,$value, $expire, $path, $domain, $secure, $http_only);

}


/**
 * To check if the session exists
 * @param $sid - the session id
 */
function _checkSession($sid){
    $db=DB::getInstance();
    $query = "SELECT * from session_table s JOIN customer c on s.customer_id=c.customer_id WHERE sessionID=?";
    $db->do_query($query,array($sid),array("s"));
    $result = $db->fetch_all_array();

    if(!$db->get_error() && sizeof($result)==1){
        $_SESSION["loggedIn"] = true;
        set_cookies("loggedIn", date("F j,Y h:i A",strtotime($result[0]['time'])));
        set_cookies("user",$result[0]['customer_name']);
        set_cookies("email",$result[0]['customer_email']);
        switch($result[0]['flag']){
            case 1:
                header("Location:".URL_BASE."admin.php");
                break;
            case 0:
                header("Location:".URL_BASE."product.php");
                break;
        }
    }
}

/**
 * Check whether the user is admin or not
 * @return bool - the result of the check
 */
function _checkAdmin(){
    $db=DB::getInstance();
    $query = "SELECT * from customer WHERE customer_email=?";
    $db->do_query($query,array($_COOKIE['email']),array("s"));
    $result = $db->fetch_all_array();
    if(!$db->get_error()){
        switch($result[0]['flag']){
            case 1:
                return true;
            default:
                return false;
        }
    }
    return false;
}

/**
 * Get all the products from the product table
 * @return array - array of product details
 */
function _getAllProducts(){
    $db=DB::getInstance();
    $query = "SELECT * from product";
    $db->do_query($query,array(),array());
    return $db->fetch_all_array();
}

/**
 * Get discount/notdiscounted/size of notdiscounted / limited records from notdiscounted products from product table
 * @param $bool - whether to get discounted/not discounted product ( true- discounted / false -- not discounted)
 * @param int $firstElement - The starting index of record to return
 * @param int $elements - The number of records to return
 * @return array - the result set of query
 */

function _getDiscountedProduct($bool,$firstElement=0,$elements=5){
    $db=DB::getInstance();
    if($bool==false && $firstElement==0 && $elements==0){
        $query="SELECT * FROM product WHERE product_discount = ?";
        $db->do_query($query,array(0),array("i"));
        return $db->fetch_all_array();
    }else{
        if($bool==true){
            $query="SELECT * FROM product WHERE product_discount > ? LIMIT ?,?;";
        }else{
            $query="SELECT * FROM product WHERE product_discount = ? LIMIT ?,?;";
        }
        $db->do_query($query,array(0,$firstElement,$elements),array("i","i","i"));
        return $db->fetch_all_array();
    }
}

/**
 * To check if the product can be added as a discount
 * MAX 5 products can be discounted
 * @param $pid - the product id
 * @return bool - the result of the check
 */

function _checkDiscountedItem($pid){
    $products = _getDiscountedProduct(true);
    $flag=0;
    foreach ($products as $val ){
        if($pid==$val['product_id']){
            $flag=1;
            break;
        }
    }
    if($flag==1){
        return true;
    }else if($flag==0 && sizeof($products)<5){
        return true;
    }
    return false;
}

/**
 * Get selected product from the product table
 * @param $pid - product id
 * @return array - array of product details
 */

function _getSelectedProduct($pid){
    $db=DB::getInstance();
    $query = "SELECT * from product WHERE product_id=?";
    $db->do_query($query,array($pid),array("i"));
    return $db->fetch_all_array();
}

/**
 * To get the price of the product
 * @param $price - price of the product
 * @param $discount - discount on the product
 * @return string - the sale price of the product
 */

function _getPrice($price,$discount){
    if($discount==0){
        return number_format((float)$price,2,'.','');
    }
    $discount = $discount/100;
    $reduce = $price * $discount;
    return number_format((float)$price-$reduce,2,'.','');
}

/**
 * To get the total price of the products in cart
 * @param $total - the array of price bought by the user
 * @return string - the total price of the cart
 */

function _getTotalPrice($total){
    $temp=0;
    foreach ($total as $item){
        $temp = $temp+$item;
    }
    return number_format((float)$temp,2,'.','');;
}

/**
 * Decrease or increase the quantity of the product
 * @param $value - the value to be increased or decreased
 * @param $pid - the product id
 * @param $bool - (true - decrease / false-increase)
 * @return bool - (true-if query is success / false-if query fails)
 */

function _decreaseQuantity($value,$pid,$bool){
    $db=DB::getInstance();
    if($bool==true) {
        $query = "UPDATE product SET product_quantity=product_quantity-? WHERE product_id=? ";
    }else{
        $query = "UPDATE product SET product_quantity=product_quantity+? WHERE product_id=? ";
    }
    $db->do_query($query,array($value,$pid),array("i","i"));
    if(!$db->get_error()){
        return true;
    }
    return false;
}

/**
 * Delete the cart item
 * @param $cid - customer id
 * @param $pid - product id
 * @return bool - return whether the query is executed correctly or not
 */

function _delCartItem($cid,$pid){
    $db=DB::getInstance();
    $query = "DELETE FROM cart WHERE customer_id=? AND product_id=? ";
    $db->do_query($query,array($cid,$pid),array("i","i"));
    if(!$db->get_error()){
        return true;
    }
    return false;
}

/**
 * Insert an item in cart table or update exisiting cart item
 * @param $cid - customer id
 * @param $pid - product id
 * @return bool - return whether the query is executed correctly or not
 */

function _insertCart($cid,$pid){
    $db=DB::getInstance();
    $query = "INSERT INTO cart(customer_id,product_id,quantity) VALUES(?,?,?) ON DUPLICATE KEY UPDATE quantity = quantity + 1;";
    $db->do_query($query,array($cid,$pid,1),array("i","i","i"));
    if(!$db->get_error()){
        return true;
    }
    return false;
}

/**
 * Get cart item that corresponds to the user
 * @param $cid - customer id
 * @return array|string - the item list /error if error in processing query
 */

function _getCartItems($cid){
    $db=DB::getInstance();
    $query = "SELECT * FROM cart WHERE customer_id = ?";
    $db->do_query($query,array($cid),array("i"));
    if(!$db->get_error()){
        return $db->fetch_all_array();
    }
    return $db->get_error();
}

/**
 * Delete an item in cart
 * @param $item - a string made up of customer id ,product id and quanity from cart table
 * @return bool - the result of query processing
 */
function _deleteItemInCart($item){
    list($cid,$pid,$quantity)=explode("_",$item);
    if(_delCartItem($cid,$pid)) {
        if(_decreaseQuantity($quantity, $pid, false)){
            return true;
        }
    }
    return false;
}

/**
 * To sanitise the inputs
 * @param $data - the input data
 * @return mixed|string - the sanitised data
 */
function _cleanInput($data) {
    $data = trim($data);
    $data = str_replace(PHP_EOL,' ', $data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
/**
 * Delete all cookies used while logging out
 */
function _sessionDestroy()
{
//    echo session_id();
    $db=DB::getInstance();
    $query = "DELETE FROM session_table WHERE sessionID=?";
    $db->do_query($query,array(session_id()),array("s"));

    $expire = time() - 1000;
    $path = "/~bxj9142/commerce/"; //Path as specific as possible -Path to homepage
    $domain = "kelvin.ist.rit.edu";
    $secure = false;//accessible on HTTP (not just HTTPS)
    $http_only = false; //accessible to JS
    if (isset($_SERVER['HTTP_COOKIE'])) {
        $cookies = explode(';', $_SERVER['HTTP_COOKIE']);
        foreach ($cookies as $cookie) {
            $parts = explode('=', $cookie);
            $name = trim($parts[0]);
            setcookie($name, '', $expire, $path, $domain, $secure, $http_only);
            setcookie($name, '', $expire, '/');
        }
    }
    $_SESSION=array();
    session_destroy();
    session_unset();
}
?>