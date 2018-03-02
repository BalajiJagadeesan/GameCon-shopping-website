<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="x-ua-compatible" content="ie=edge">

    <link rel="stylesheet" href="<?=URL_CSS?>style.css" type="text/css" />
    <link rel="shortcut icon" type="image/x-icon" href="<?=URL_BASE?>/assests/img/favicon.ico" />

    <title>Project-1<?=($title)?" | ".($title):""?></title>
</head>
<body>
<div id="wrapper">
<div id="header">
<?
$pageName = explode("/",$_SERVER["SCRIPT_NAME"]);
$previousPage = explode("/",$_SERVER['HTTP_REFERER']);
include_once PATH_TEMPLATES."navbar.php";
?>
</div>
<div id="body">
<script src="<?= URL_JS ?>jquery-v2.min.js" type="text/javascript"></script>
<script src="<?= URL_JS ?>tether.min.js" type="text/javascript"></script>
<script src="<?= URL_JS ?>bootstrap.min.js" type="text/javascript"></script>
<script src="<?= URL_JS ?>mdb.min.js" type="text/javascript"></script>
<script src="<?= URL_JS ?>main.js" type="text/javascript"></script>
