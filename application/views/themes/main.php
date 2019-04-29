<!DOCTYPE html>
<html lang="en">
<head>
	<title>Aside - Free HTML5 Bootstrap 4 Template by uicookies.com</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta name="description" content="Free HTML5 Website Template by uicookies.com" />
	<meta name="keywords" content="free bootstrap 4, free bootstrap 4 template, free website templates, free html5, free template, free website template, html5, css3, mobile first, responsive" />
	<meta name="author" content="uicookies.com" />

	<link href="https://fonts.googleapis.com/css?family=Work+Sans" rel="stylesheet">

	<link rel="stylesheet" href="<?php echo base_url()."/assets/aside/css/bootstrap.min.css" ?>">
	<link rel="stylesheet" href="<?php echo base_url()."/assets/aside/css/open-iconic-bootstrap.min.css" ?>">

	<link rel="stylesheet" href="<?php echo base_url()."/assets/aside/css/owl.carousel.min.css" ?>">
	<link rel="stylesheet" href="<?php echo base_url()."/assets/aside/css/owl.theme.default.min.css" ?>">

	<link rel="stylesheet" href="<?php echo base_url()."/assets/aside/css/icomoon.css" ?>">
	<link rel="stylesheet" href="<?php echo base_url()."/assets/aside/css/animate.css" ?>">
	<link rel="stylesheet" href="<?php echo base_url()."/assets/aside/css/style.css" ?>">
</head>
<body>
<?php 
echo $this->load->get_section('aside');
echo $output;
echo $this->load->get_section('footer');
?>
</body>
</html>