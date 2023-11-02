<!-- Updated head.tpl - Correction of meta tags - dev Petr Mileshko -->
<meta name="csrf-token" content="<?php echo csrf_token(); ?>">

 
 
 <!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-5498GRN3');</script>
<!-- End Google Tag Manager -->
 
<?php
// Correction by Petr Mileshko
// old code - echo $settings["header_meta"]; - This Code rendering meta tags with closing slash at the end ("/>") as it no more in use in HTML5 specification
// Code replaced with below to prevent code generation of tag <meta /> will be just <meta > instead as per new HTML5
// new code - echo preg_replace("/\/>/",">",$settings["header_meta"]); 
echo preg_replace("/\/>/",">",$settings["header_meta"]);
?>

<!-- 18-08-2023 Correction 42 Fonts transfered on server
//<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@200;300;400;500;600&display=swap" rel="stylesheet">
//<link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;600;700&family=Roboto:wght@100;300&display=swap" rel="stylesheet">
-->

<link rel="icon" href="https://autospot.ge/favicon.ico" type="image/x-icon">
<!-- Updated Correction of meta tags -->
 <?php
echo $Main->assets($config["css_styles"], 'css');
echo $Main->outFavicon(); 
?>
 
  <style>
		.css-234345 {
		width: 100%;
		height: 100%;
		position: relative;
		border-bottom: 10px solid #f2f2f2;
		border-top: 10px solid #f2f2f2;
		}
		
		.css-234345::after {
		content: "";
		
		top: -15px;
		left: -2px;
		right: -2px;
		bottom: -15px;
		border: 3px solid #ffffff;
		}
		
		.VIpgJd-yAWNEb-VIpgJd-fmcmS-sn54Q {
		background-color: #fff;
		box-shadow: none;
		box-sizing: border-box;
		-webkit-box-sizing: border-box;
		-moz-box-sizing: border-box;
		position: relative;
		}

		.slick-track {
         float:left;
        }

		.board-view-price {
         font-size:17px;
        }
	
		@media only screen and (min-width: 992px) {.item-grids {  max-width: 202px;}}
		.slick-track{float:left;margin-left: -10px;}.board-view-price{font-size:17px;} 

		.slick-track{float:left;}.board-view-price{font-size:17px;}

		@media only screen and (min-width: 992px) {.item-grids {  max-width: 202px;}}
		.slick-track{float:left;margin-left: -10px;}.board-view-price{font-size:17px;} 

		.selected-button {background-color: #C2272B;color: #ffffff;padding: 2px 10px;border-radius: 5px;}
        
        @media (max-width: 992px) {.btn-custom, .css-4che2j {width: 100%;}}
                     
		@media only screen and (min-width: 1024px) {.owl-carousel {width: auto;}.slick-slide {margin: 0 5px; width: 178.8px;}
div a{color: #8d8176;text-decoration: none !important;border: none !important;}.m-index-category:hover{background:#f7f8fa;}}
@media only screen and (max-width: 767px) {.owl-carousel {width: 104%;}.slick-slide {margin: 0 5px; width: 180px;}}
.cat-bg {border: 4px solid #fff;height: 119px;border-radius: 0px;display: block;border: 1px solid #f5f5f5; margin: 0px;}
.slick-track {margin-left: 0px;}.item-grids{margin-left: 0px;}
div a{color: #8d8176;text-decoration: none !important;
border: none !important;}.m-index-category:hover{background:#f7f8fa;}}

	</style>