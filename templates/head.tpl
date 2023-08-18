<!-- Updated head.tpl - Correction of meta tags - dev Petr Mileshko -->
<meta name="csrf-token" content="<?php echo csrf_token(); ?>">

<?php
// Correction by Petr Mileshko
// old code - echo $settings["header_meta"]; - This Code rendering meta tags with closing slash at the end ("/>") as it no more in use in HTML5 specification
// Code replaced with below to prevent code generation of tag <meta /> will be just <meta > instead as per new HTML5
// new code - echo preg_replace("/\/>/",">",$settings["header_meta"]); 
echo preg_replace("/\/>/",">",$settings["header_meta"]); 
?>

<!-- 18-08-2023 Correction 42 Fonts transfered on server
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@200;300;400;500;600&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;600;700&family=Roboto:wght@100;300&display=swap" rel="stylesheet">
-->

<link rel="icon" href="https://autospot.ge/favicon.ico" type="image/x-icon">
<!-- Updated Correction of meta tags -->
 <?php
echo $Main->assets($config["css_styles"], 'css');
echo $Main->outFavicon(); 
?>