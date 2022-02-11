<?php
	include "../config/hotel_config.php";
    if(admin_login != "true" && $page != "index") {
        echo "<script type=\"text/javascript\">location.href='".base_admin."';</script>";
    }
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Hotel Admin</title>
    <!-- plugins:css -->
    <link rel="stylesheet" href="<?=base_admin?>/assets/vendors/mdi/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="<?=base_admin?>/assets/vendors/css/vendor.bundle.base.css">
    <!-- endinject -->
    <!-- Plugin css for this page -->
    <link rel="stylesheet" href="<?=base_admin?>/assets/vendors/select2/select2.min.css">
    <link rel="stylesheet" href="<?=base_admin?>/assets/vendors/select2-bootstrap-theme/select2-bootstrap.min.css">
    
    <link rel="stylesheet" href="<?=base_admin?>/assets/vendors/jvectormap/jquery-jvectormap.css">
    <link rel="stylesheet" href="<?=base_admin?>/assets/vendors/flag-icon-css/css/flag-icon.min.css">
    <link rel="stylesheet" href="<?=base_admin?>/assets/vendors/owl-carousel-2/owl.carousel.min.css">
    <link rel="stylesheet" href="<?=base_admin?>/assets/vendors/owl-carousel-2/owl.theme.default.min.css">
    <!-- End plugin css for this page -->
    <!-- inject:css -->
    <!-- endinject -->
    <!-- Layout styles -->
    <link rel="stylesheet" href="<?=base_admin?>/assets/css/style.css">
    <link rel="stylesheet" href="<?=base_url?>/css/font-awesome.min.css">

    <!-- End layout styles -->
    <link rel="shortcut icon" href="<?=base_admin?>/assets/images/favicon.png" />
  </head>
  <body>