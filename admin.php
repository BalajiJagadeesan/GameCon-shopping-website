<?php
$title = "Admin";
require_once "./assests/inc/page_start.inc.php";
require_once PATH_INC."header.inc.php";

$product_name=$product_description=$product_price=$product_quantity=$product_discount=$admin_password=$pid='';
if(!isset($_SESSION) || $_SESSION["loggedIn"] == false || !isset($_COOKIE['loggedIn']) || !isset($_COOKIE['user']) || !isset($_COOKIE['email']) || !_checkadmin()){
    header("Location:".URL_BASE."login.php");
}
$validate = new Validator();
$db = DB::getInstance();

//If form is to update product get the details of the product and fill it in add a product form
if(!empty($_GET)){
    $pid=$_GET['product-select'];
    $result = _getSelectedProduct($pid);
    $product_name = $result[0]['product_name'];
    $product_description = $result[0]['product_description'];
    $product_quantity = $result[0]['product_quantity'];
    $product_price = $result[0]['product_price'];
    $product_discount = $result[0]['product_discount'];
    unset($_GET);

}
//If form is to add a product
if(!empty($_POST)){
    //Clean the input
    $product_name=_cleanInput($_POST['product-name']);
    $product_description = _cleanInput($_POST['product-description']);
    $product_price = $_POST['product-price'];
    $product_quantity = $_POST['product-quantity'];
    $product_discount =$_POST['product-discount'];
    $admin_password = $_POST['password'];
    $pid = $_POST['add-update'];
    //validate the input
    if(empty($product_name)){
        $errors[] = "Product name is either empty or contains special characters";
        $product_name='';
    }

    if(empty($product_description)){
        $errors[] ="Please enter product description";
        $product_description='';
    }

    if (empty($admin_password) || !$validate::_passwordStrength($admin_password)) {
        $errors[] = "Password must be at least 4 characters, no more than 8 characters, and must include at least one upper case letter, one lower case letter, and one numeric digit.";
        $admin_password='';
    }

    //If discount items are more than 5 ,print error
    if($product_discount!=0){
        if(!_checkDiscountedItem($pid)){
            $errors[] = "Only 5 products can be in discount at a time.Please set the discount value to 0.";
        }
    }

    //Check the admin password
    $errors[] = _legitPassword($_COOKIE['email'],$admin_password);

    //print errors
    if (!empty($errors[0])) {
        _printErrors($errors);
    }

    if (empty($errors[0])) {
        //Add the product to product table or update if product already exists
        $query = "INSERT into product(product_id,product_name,product_description,product_quantity,product_price,product_discount) ".
            "VALUES (?,?,?,?,?,?) ON DUPLICATE KEY UPDATE product_name = VALUES(product_name),".
            "product_description = VALUES(product_description),product_quantity = VALUES(product_quantity),".
            "product_price = VALUES(product_price),product_discount = VALUES(product_discount);";
        $input = array($pid,$product_name,$product_description,$product_quantity,$product_price,$product_discount);
        $dataType = array("i","s","s","i","d","i");
        $db->do_query($query,$input,$dataType);
        if(!$db->get_error()) {
            $pid = $db->get_insert_id();
        }else{
            echo $db->get_error();
        }

        //If user uploaded a product image,move the file to local directory and insert the URL to the database
        if($_FILES['file-img']['name']!='') {
            $info = pathinfo($_FILES['file-img']['name']);
            $ext = $info['extension']; // get the extension of the file
            $img_name = $pid .".". $ext;
            $target = PATH_PRODUCT_IMG . $img_name;
            $targetURL = URL_PRODUCT_IMG . $img_name;
            move_uploaded_file($_FILES['file-img']['tmp_name'], $target);
            $q = "UPDATE product SET product_image=? WHERE product_id=? ";
            $db->do_query($q,array($targetURL,$pid),array("s","i"));
            if(!$db->get_error()){
                echo "<p class='text-center'>Added product to the table </p>";
            }
        }

        echo "<h3 class='text-center'>Content Added to the table </h3>";

        $product_name=$pid=$product_description=$product_price=$product_quantity=$product_discount='';

        unset($_POST);
    }
}
?>
<div class="container">
    <div class="row">
        <div class="col-md-4">
            <div id="pad" class="card">
                <div class="card-block">
                    <form id="product-update-form" method="get" target="_self">
                        <div class="form-header orange lighten-2">
                            <h3><i class="fa fa-exclamation-circle"></i> Update a Product:</h3>
                        </div>
                        <div class="form-group">
                            <label for="product-select">Product Name</label>
                            <select class="form-control" id="product-select" name="product-select">
                                <option <?php if ($pid == '' ) echo 'selected' ; ?> value="">Please choose a product to update</option>
                                <?
                                //Get all the product and list it in the select menu
                                $product = _getAllProducts();
                                foreach($product as $key => $value):
                                    $selected = ($pid == $value['product_id']) ? "selected='selected'" : '';
                                    echo "<option value='".$value['product_id']."' ".$selected.">".$value['product_name']."</option>";
                                endforeach;
                                ?>
                            </select>
                        </div>
                        <div class="text-center">
                            <button class="btn orange lighten-2" type="reset" value="Reset">Reset</button>
                            <button class="btn orange lighten-2" name="select-btn" >Select Product</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div id="pad" class="card">
                <div class="card-block">
                    <form id="product-form" method="post" target="_self" enctype="multipart/form-data">

                        <div class="form-header orange lighten-2">
                            <h3><i class="fa fa-plus"></i> Add a Product:</h3>
                        </div>

                        <div class="md-form">
                            <input type="text" id="product-name" name="product-name" class="form-control" value="<?=$product_name?$product_name:''?>">
                            <label for="product-name">Product Name</label>
                        </div>

                        <div class="md-form">
                            <textarea type="text" id="product-description" name="product-description" class="md-textarea"><?=$product_description?$product_description:''?></textarea>
                            <label for="product-description">Product Description</label>
                        </div>

                        <div class="md-form">
                            <input type="number" min="1" max="999" maxlength="6" step="any"  id="product-price" name="product-price" class="form-control" value="<?=$product_price?round($product_price,2):''?>">
                            <label for="product-price">Product Price($)</label>
                        </div>

                        <div class="md-form">
                            <input type="number"  min="1" step="1"  id="product-quantity" name="product-quantity" class="form-control" value="<?=$product_quantity?$product_quantity:''?>">
                            <label for="product-quantity">Product Quantity</label>
                        </div>

                        <div class="md-form">
                            <input type="number" min="0" max="30" step="1"  id="product-discount" name="product-discount" class="form-control" value="<?=$product_discount?$product_discount:''?>">
                            <label for="product-discount">Product Discount(%)</label>
                        </div>

                        <div class="form-group">
                            <label class="btn btn-default btn-file " for="file-img">Choose an img (.jpg/.png)</label>
                            <input type="file" hidden onchange="$('#fileHelp').text(this.value);" id="file-img" name="file-img" accept="image/*" aria-describedby="fileHelp">
                            <small id="fileHelp" class="form-text text-muted">Upload a image for the product</small>
                        </div>

                        <div class="md-form">
                            <input type="password" id="password" name="password" class="form-control" placeholder="Admin's Password" value="<?=$admin_password?$admin_password:''?>">
                            <label for="password">Your password</label>
                        </div>

                        <div class="text-center">
                            <button class="btn orange lighten-2" type="reset" value="Reset">Reset</button>
                            <button class="btn orange lighten-2" name="add-update" value="<?=$pid?$pid:''?>" >Add a Product</button>
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
