<!DOCTYPE html>
<html dir="ltr" lang="en">
<head>
	<meta charset="utf-8">
	<meta content="IE=edge" http-equiv="X-UA-Compatible">
	<meta name="viewport" content="width=device-width, initial-scale=1">
<?php
if (isset($page_title)) {
?>
	<title><?php echo $page_title; ?></title>
<?php
} else {
?>
	<title>Tactician</title>
<?php
}
?>
    <link type="text/css" rel="stylesheet" href="<?php echo base_url("css/core.css"); ?>">
	<link type="image/png" rel="shortcut icon" href="<?php echo base_url("img/icon_32.png"); ?>">
    <link type="image/png" rel="apple-touch-icon" href="<?php echo base_url("img/icon_32.png"); ?>">
    <link sizes="72x72" type="image/png" rel="apple-touch-icon" href="<?php echo base_url("img/icon_72.png"); ?>">
    <link sizes="114x114" type="image/png" rel="apple-touch-icon" href="<?php echo base_url("img/icon_144.png"); ?>">
</head>
<?php

$about_class = "";
$register_class = "";
$profile_class = "";
$signout_class = "";
$cases_class = "";

if (isset($menu_item) && $menu_item > 0) {
    switch($menu_item) {
        case 1: $about_class = "class=\"active\""; break;
        case 2: $register_class = "class=\"active\""; break;
        case 3: $profile_class = "class=\"active\""; break;
        case 4: $signout_class = "class=\"active\""; break;
        case 5: $cases_class = "class=\"active\""; break;
    }
}

?>
<body>
    <nav class="navbar navbar-default navbar-fixed-top">
      <div class="container">
        <div class="navbar-header">
<?php
$email_session = $this->session->userdata('email');
if (isset($email_session) && strlen($email_session) > 0) {
?>
            <a class="navbar-brand" href="<?php echo site_url("mycases"); ?>" title="Tactician"><img alt="Tactician" src="<?php echo base_url("img/icon_72.png");?>" style="width:54px;height:54px;"></a>
<?php
}else{
?>
            <a class="navbar-brand" href="<?php echo site_url(""); ?>" title="Tactician"><img alt="Tactician" src="<?php echo base_url("img/icon_72.png");?>" style="width:54px;height:54px;"></a>
<?php
}
?>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
<?php
if (isset($email_session) && strlen($email_session) > 0) {
?>
            <!--
                <form class="navbar-form navbar-left" role="search">
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="Search for...">
                        <span class="input-group-btn">
                            <button class="btn btn-default" type="button"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>
                        </span>
                    </div>
                </form>
            -->
<?php
$name = $this->session->userdata('name');
$image = $this->session->userdata('image');
$is_admin = $this->session->userdata('is_admin');
?>
                <ul class="nav navbar-nav navbar-right">
                    <li <?php echo $cases_class; ?>><a href="<?php echo site_url("mycases"); ?>" title="My Cases">Cases</a></li>          
<?php
$image_data = "<img src=\"".base_url('img/user/profile.png')."\" style=\"width:36px;height:36px;vertical-align:middle;\" id=\"img-prof-thumb\" class=\"img-thumbnail\">";
if (isset($image) && strlen($image) > 0 && $image !== 'NULL') {
    $image_data = "<img src=\"".base_url('img/user')."/".$image."\" style=\"width:36px;height:36px;vertical-align:middle;\" id=\"img-prof-thumb\" class=\"img-thumbnail\">";
}
?>
                    <li <?php echo $profile_class; ?>><a href="<?php echo site_url("account"); ?>" title="<?php echo $name; ?>" id="ancProfile"><?php echo $image_data ?>&nbsp;&nbsp;<span id="spnProfile"><?php echo $name; ?></span></a></li>
                    <li <?php echo $signout_class; ?>><a href="<?php echo site_url("account/logout"); ?>" title="Log Out">Log Out</a></li>
                </ul>
<?php
}else{
?>
                <ul class="nav navbar-nav navbar-right">

                    <li <?php echo $about_class; ?>><a href="<?php echo site_url("about"); ?>" title="About Tactician">About</a></li>
                    <li <?php echo $register_class; ?>><a href="<?php echo site_url("account/register"); ?>" title="Register">Register</a></li>
                </ul>
<?php
}
?>
            </div>
        </div>
    </nav>
    <div id="page" class="container">
