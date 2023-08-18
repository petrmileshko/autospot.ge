<!doctype html>
<html lang="<?php echo getLang(); ?>">
  <head>
	<meta charset="utf-8">
     <!-- 18-08-2023 Correction 38 of meta tag ( old code <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1"> ) -->
	<meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="<?php echo $Seo->out(array("page" => "index", "field" => "meta_desc")); ?>">
    <?php include $config["template_path"] . "/head.tpl"; ?>
    <title><?php echo $Seo->out(array("page" => "index", "field" => "meta_title")); ?></title>
    <!-- Correction of meta tags (duplication excluded) -->
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
 </head>
  <body data-prefix="<?php echo $config["urlPrefix"]; ?>" data-template="<?php echo $config["template_folder"]; ?>" data-header-sticky="true" data-type-loading="<?php echo $settings["type_content_loading"]; ?>" data-page-name="<?php echo $route_name; ?>" >

    <?php include $config["template_path"] . "/header.tpl"; ?>
    <?php include $config["template_path"] . "/filters.tpl"; ?>

    <div class="container mt15" >

       <div class="row" >
          <?php if($settings["home_sidebar_status"]){ ?>
          <div class="col-lg-2 d-none d-lg-block" >

             <?php include $config["template_path"] . "/index_sidebar.tpl"; ?>

          </div>
          <?php } ?>
          <div class="<?php if($settings["home_sidebar_status"]){ echo 'col-lg-10 col-12'; }else{ echo 'col-lg-12'; } ?>" >

          <?php if($route_name == "index" ){ ?>
		
		  <div class="d-none d-lg-block mb30">
			<div class="row no-gutters gutters12 col-lg-12 " style="padding-right: 0px; padding-left: 0px;">
				<?php
					if(count($getCategoryBoard["category_board_id_parent"][0])){
						foreach ($getCategoryBoard["category_board_id_parent"][0] as $value) {
						?>
						<div class="col-sm-2 col-3 cat-bg m-index-category"  style="border: 1px solid #f5f5f5; height: 120px;">
							<a href="<?php echo $CategoryBoard->alias($value["category_board_chain"]); ?>"  >
								<?php if( $value["category_board_image"] ){ ?>
									<div class="header-wow-mobile-category-name"  style="text-align: center; height: 65px;">
                                     
                                       <!-- 15-08-2023 Correction 12 (validator W3) <img> element must have an alt attribute. and
                                       	  18-08-2023 Correction 37 added Text to alt attribute to avoid redundant.
                                         old code - <img class="m-cat-img" src="<?php echo Exists($config["media"]["other"],$value["category_board_image"],$config["media"]["no_image"]); ?>">
                                       -->
										<img class="m-cat-img" src="<?php echo Exists($config["media"]["other"],$value["category_board_image"],$config["media"]["no_image"]); ?>" alt='Category icon - <?php echo $ULang->t( $value["category_board_name"], [ "table" => "uni_category_board", "field" => "category_board_name" ] ); ?>'>
									</div>
									<div class="main-category-list-name" style="text-align: center;font-size: 14px;font-weight: 500;">
									<?php echo $ULang->t( $value["category_board_name"], [ "table" => "uni_category_board", "field" => "category_board_name" ] ); ?></div>
								<?php } ?>
							</a>
						</div>
						
						
						<?php
							
						}
					}
				?>
			</div>
		   </div>

	       <?php } ?>

           <?php
              foreach ($settings["home_widget_sorting"] as $widgetName) {

                if($widgetName == "category_slider" && $settings["home_category_slider_status"]){

                   ?>
  
				   
                   <?php

                }elseif($widgetName == "stories" && $settings["home_stories_status"] && $settings["user_stories_status"]){

                    $data["stories"] = $Profile->getUserStories();

                    if($_SESSION['profile']['id'] || $data["stories"]){
                    ?>
                    <div class="slider-user-stories mb25" >

                    <?php if($_SESSION['profile']['id']){ ?>
                    <div>
                        <div class="slider-user-stories-add open-modal" data-id-modal="modal-user-story-add" >
                            <div class="slider-user-stories-item-user" >
                                <div class="slider-user-stories-item-avatar" ><img src="<?php echo $settings["path_tpl_image"]; ?>/plus-icon.png" /></div>
                                <div class="slider-user-stories-item-name" ><?php echo $ULang->t( "Добавить" ); ?></div>
                            </div>
                        </div>
                    </div>                    
                    <?php
                    }

                    if($data["stories"]){

                        foreach ($data["stories"] as $key => $value) {

                            if($value['user_id'] == $_SESSION['profile']['id']){
                                $getLastStory = findOne('uni_clients_stories_media', 'clients_stories_media_user_id=? and clients_stories_media_loaded=? order by clients_stories_media_timestamp desc', [$value['user_id'],1]);
                            }else{
                                $getLastStory = findOne('uni_clients_stories_media', 'clients_stories_media_user_id=? and clients_stories_media_loaded=? and clients_stories_media_status=? order by clients_stories_media_timestamp desc', [$value['user_id'],1,1]);
                            }

                            if($getLastStory){
                                
                                if($getLastStory['clients_stories_media_type'] == 'image'){
                                    if(file_exists($config['basePath'].'/'.$config['media']['user_stories'].'/'.$getLastStory['clients_stories_media_name'])){
                                        $imageStory = $config['urlPath'].'/'.$config['media']['user_stories'].'/'.$getLastStory['clients_stories_media_name'];
                                    }
                                }else{
                                    if(file_exists($config['basePath'].'/'.$config['media']['user_stories'].'/'.$getLastStory['clients_stories_media_preview'])){
                                        $imageStory = $config['urlPath'].'/'.$config['media']['user_stories'].'/'.$getLastStory['clients_stories_media_preview'];
                                    }
                                }

                                if(isset($_COOKIE['viewStory'.$value['user_id']])){ 
                                    if(strtotime($value["timestamp"]) > $_COOKIE['viewStory'.$value['user_id']]){
                                        $statusViewed = "stories-item-no-viewed";
                                    }else{
                                        $statusViewed = "stories-item-viewed";
                                    }
                                }else{
                                    $statusViewed = "stories-item-no-viewed";
                                }

                                if($imageStory){
                                    ?>
                                    <div>
                                        <div class="slider-user-stories-item" data-index="<?php echo $key; ?>" data-id="<?php echo $value["id"]; ?>" style="background-image: linear-gradient(rgba(0, 0, 0, 0) 50%, rgba(0, 0, 0, 0.24) 75%, rgba(0, 0, 0, 0.64)), url(<?php echo $imageStory; ?>); background-position: center center; background-size: cover;" >
                                            
                                            <div class="slider-user-stories-item-user" >
                                                <div class="slider-user-stories-item-avatar <?php echo $statusViewed; ?>" >
                                                 
                                                  <!-- Correction 13 (validator W3) <img> element must have an alt attribute -->
                                                 <img src="<?php echo $value["user_avatar"]; ?>" class="image-autofocus" alt="<?php echo $value['user_name']; ?>">
                                                 </div>
                                                <div class="slider-user-stories-item-name"  ><?php echo $value["user_name"]; ?></div>
                                            </div>
                                        </div>
                                    </div>                
                                    <?
                                }

                            }

                        }

                    }

                    ?>
                    </div>
                    <?php
                    }

                }elseif($widgetName == "shop" && $settings["home_shop_status"]){

                    $data["shops"] = getAll("select * from uni_clients_shops INNER JOIN `uni_clients` ON `uni_clients`.clients_id = `uni_clients_shops`.clients_shops_id_user where (clients_shops_time_validity > now() or clients_shops_time_validity IS NULL) and clients_shops_status=1 and clients_status IN(0,1) order by rand() limit ?", [$settings["index_out_count_shops"] ?: 16]);

                    if($data["shops"]){ ?>
                    <div class="mb25 title-and-link h3" > <strong><?php echo $ULang->t( "Магазины" ); ?></strong> <a href="<?php echo $Shop->linkShops(); ?>"><?php echo $ULang->t( "Все магазины" ); ?> <i class="las la-arrow-right"></i> </a> </div>
                      <div class="row no-gutters gutters10 mb25" >
                          <?php 
                          
                             foreach ($data["shops"] as $key => $value) {
                                 include $config["template_path"] . "/include/shop_grid.php";
                             }
                          
                          ?>
                      </div>
                    <?php
                    }
					
                }elseif($widgetName == "promo" && $settings["home_promo_status"]){
                    
                    $data["sliders"] = getAll("select * from uni_sliders where sliders_visible=? order by sliders_sort asc", [1]);

                    if($data["sliders"]){
                        ?>
                        <div class="load-sliders-wide mb25 " >
                        <div class="owl-carousel sliders-wide" data-show-slider="<?php echo $settings["media_slider_count_show"]; ?>" data-autoplay="<?php echo $settings["media_slider_autoplay"]; ?>" data-arrows="<?php echo $settings["media_slider_arrows"]; ?>" >
                           
                           <?php
                           foreach ($data["sliders"] as $key => $value) {
                               ?>
                                 <div class="sliders-wide-item" data-id="<?php echo $value["sliders_id"]; ?>" >

                                      <a title="<?php echo $ULang->t( $value["sliders_title1"] , [ "table"=>"uni_sliders", "field"=>"sliders_title1" ] ); ?>. <?php echo $ULang->t( $value["sliders_title2"] , [ "table"=>"uni_sliders", "field"=>"sliders_title2" ] ); ?>" style="
                                        <?php if($value["sliders_image"]){ ?>
                                        background: url(<?php echo Exists($config["media"]["other"],$value["sliders_image"],$config["media"]["no_image"]); ?>);
                                        background-position: right;
                                        background-size: contain;
                                        background-repeat: no-repeat;
                                        <?php } ?>
                                        background-color: <?php echo $value["sliders_color_bg"]; ?>;
                                        display: block;
                                        border-radius: 10px;
                                        height: <?php echo $settings["media_slider_height"]; ?>px;
                                        " target="_blank"  href="<?php echo $Main->sliderLink( $value["sliders_link"] ); ?>">
                                        
                                        <span class="sliders-wide-title">
                                          <span class="sliders-wide-title1"><?php echo $ULang->t( $value["sliders_title1"] , [ "table"=>"uni_sliders", "field"=>"sliders_title1" ] ); ?></span>
                                          <span class="sliders-wide-title2"><?php echo $ULang->t( $value["sliders_title2"] , [ "table"=>"uni_sliders", "field"=>"sliders_title2" ] ); ?></span>
                                        </span>

                                      </a>

                                </div>               
                               <?php
                           }
                           ?>
                        </div>
                        </div>
                     <?php
                    }

                }elseif($widgetName == "vip" && $settings["home_vip_status"]){
                    
                    if($settings["main_type_products"] == 'physical'){
                       $geo = $Ads->queryGeo() ? " and " . $Ads->queryGeo() : "";
                    }
                    
                    if($settings["index_out_content_method"] == 0){
                      $data["vip"] = $Ads->getAll( ["query"=>"ads_status='1' and clients_status IN(0,1) and ads_period_publication > now() and ads_vip='1' order by rand() limit 30", "param_search" => $param_search, "output" => 16 ] );
                    }else{
                      $data["vip"] = $Ads->getAll( ["query"=>"ads_status='1' and clients_status IN(0,1) and ads_period_publication > now() and ads_vip='1' $geo order by rand() limit 30", "param_search" => $param_search, "output" => 16 ] );
                    }

                    if($settings["main_type_products"] == 'physical'){
                        if($_SESSION["geo"]["alias"]){
                          $data["vip_link"] = _link($_SESSION["geo"]["alias"]."/vip");
                        }else{
                          $data["vip_link"] = _link($settings["country_default"]."/vip"); 
                        }
                    }else{
                        $data["vip_link"] = _link("vip");
                    }

                    if($data["vip"]["count"]){
                        ?>
                        
                        <div class="mb25 title-and-link h3" > <strong><?php echo $ULang->t( "Горячие предложения" ); ?></strong>  <span class="d-none d-md-block"  style="float: right;font-size:16px; margin-top: 5px;">
						   
						   <img src="/media/others/hot.svg" style="width:22px;margin-top: -5px;">
						  
						     <a style="color:#000; font-size:15px;" href="/promo/ads-hot/" ><?php echo $ULang->t( "Разместить сюда" ); ?></a>
						  
						    </span>
						</div>
						
                       <div class="slider-item-grid">
                            <div class="owl-carousel init-slider-grid gutters10" data-slick='{"infinite": true, "rows": 3}'>
                            <?php
                             $itemsPerPage = 100;
                             $counter = 0;
                             $dataCount = count($data["vip"]["all"]);
                             $startIndex = 0;

                            if (isset($_GET['start'])) {
                             $startIndex = (int)$_GET['start'];
                            }

                            for ($i = $startIndex; $i < $dataCount; $i++) {
                            $value = $data["vip"]["all"][$i];

                            if (!empty($value)) {
                             include $config["template_path"] . "/include/vip_ad_grid.php";
                            $counter++;
                            }

                            if ($counter >= $itemsPerPage) {
                            break;
                           }
                          }

                          if ($counter < $itemsPerPage && $startIndex > 0) {
                            $remainingSlots = $itemsPerPage - $counter;
                            for ($i = 0; $i < $remainingSlots; $i++) {
                               $value = $data["vip"]["all"][$i];

                            if (!empty($value)) {
                              include $config["template_path"] . "/include/vip_ad_grid.php";
                              $counter++;
                            }

                         if ($counter >= $itemsPerPage) {
                        break;
                        }
                      }
                    }
                ?>
                </div>
                </div>
                <div class="text-right mt20">
						 <a class="css-qwrg77" style="color:#000;" href="<?php echo $data["vip_link"]; ?>" ><?php echo $ULang->t( "Больше объявлений" ); ?> <i class="las la-arrow-right"></i></a> 
						  </div>
                          <div class="text-center mb50">
                              <a href="ad/create"/><button class="button-style-custom css-i2yj1r width250 action-forgot mt10"><svg width="20px" height="20px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M12 21C10.22 21 8.47991 20.4722 6.99987 19.4832C5.51983 18.4943 4.36628 17.0887 3.68509 15.4442C3.0039 13.7996 2.82567 11.99 3.17294 10.2442C3.5202 8.49836 4.37737 6.89472 5.63604 5.63604C6.89472 4.37737 8.49836 3.5202 10.2442 3.17294C11.99 2.82567 13.7996 3.0039 15.4442 3.68509C17.0887 4.36628 18.4943 5.51983 19.4832 6.99987C20.4722 8.47991 21 10.22 21 12C21 14.387 20.0518 16.6761 18.364 18.364C16.6761 20.0518 14.387 21 12 21ZM12 4.5C10.5166 4.5 9.0666 4.93987 7.83323 5.76398C6.59986 6.58809 5.63856 7.75943 5.07091 9.12988C4.50325 10.5003 4.35473 12.0083 4.64411 13.4632C4.9335 14.918 5.64781 16.2544 6.6967 17.3033C7.7456 18.3522 9.08197 19.0665 10.5368 19.3559C11.9917 19.6453 13.4997 19.4968 14.8701 18.9291C16.2406 18.3614 17.4119 17.4001 18.236 16.1668C19.0601 14.9334 19.5 13.4834 19.5 12C19.5 10.0109 18.7098 8.10323 17.3033 6.6967C15.8968 5.29018 13.9891 4.5 12 4.5Z" fill="#000000"></path> <path d="M12 16.75C11.8019 16.7474 11.6126 16.6676 11.4725 16.5275C11.3324 16.3874 11.2526 16.1981 11.25 16V8C11.25 7.80109 11.329 7.61032 11.4697 7.46967C11.6103 7.32902 11.8011 7.25 12 7.25C12.1989 7.25 12.3897 7.32902 12.5303 7.46967C12.671 7.61032 12.75 7.80109 12.75 8V16C12.7474 16.1981 12.6676 16.3874 12.5275 16.5275C12.3874 16.6676 12.1981 16.7474 12 16.75Z" fill="#000000"></path> <path d="M16 12.75H8C7.80109 12.75 7.61032 12.671 7.46967 12.5303C7.32902 12.3897 7.25 12.1989 7.25 12C7.25 11.8011 7.32902 11.6103 7.46967 11.4697C7.61032 11.329 7.80109 11.25 8 11.25H16C16.1989 11.25 16.3897 11.329 16.5303 11.4697C16.671 11.6103 16.75 11.8011 16.75 12C16.75 12.1989 16.671 12.3897 16.5303 12.5303C16.3897 12.671 16.1989 12.75 16 12.75Z" fill="#000000"></path> </g></svg>   <?php echo $ULang->t( "Разместить бесплатно" ); ?></button></a>
                          </div>
                        <?php
                     }                 
                        }elseif($widgetName == "blog" && $settings["home_blog_status"]){

                    $data["articles"] = $Blog->getAll( ["query"=>"blog_articles_visible=1", "sort"=>"order by rand() limit 9"] );

                    if($data["articles"]["count"]){
                        ?>
                          <div class="mb25 title-and-link h3" > <strong><?php echo $ULang->t( "Блог" ); ?></strong> <a href="<?php echo _link("blog"); ?>" ><?php echo $ULang->t( "Наш блог" ); ?> <i class="las la-arrow-right"></i></a> </div>
                          <div class="slider-item-grid init-slider-grid mb25" >
                              <?php 
                              
                                 foreach ( $data["articles"]["all"] as $key => $value) {
                                     include $config["template_path"] . "/include/slider_articles_blog.php";
                                 }
                              
                              ?>
                          </div>                    
                        <?php
                    }
                    
                }elseif($widgetName == "category_ads" && $settings["home_category_ads_status"]){

                    $data["slider_ad_category"] = $Main->outSlideAdCategory(16);

                    if($data["slider_ad_category"]){
                        foreach ($data["slider_ad_category"] as $id_category => $nested) {
                            ?>
                              <div class="mb25 title-and-link h3" > <strong><?php echo $ULang->t( $getCategoryBoard["category_board_id"][$id_category]["category_board_name"], [ "table" => "uni_category_board", "field" => "category_board_name" ] ); ?></strong> 
                              <a class="css-qwrg77" href="<?php echo $CategoryBoard->alias($getCategoryBoard["category_board_id"][$id_category]["category_board_chain"]); ?>" >
                                    <?php echo $ULang->t( "Больше объявлений" ); ?> <i class="las la-arrow-right"></i>
                              </a>                            
                              </div>
                              <div class="slider-item-grid init-slider-grid mb25" >
                                  <?php 
                                  
                                    foreach ($nested as $key => $value) {
                                        include $config["template_path"] . "/include/slider_ad_grid.php";
                                    }
                                  
                                  ?>
                              </div>
                            
                        
                            
                            <?php

                        }
                    }                
                    
                }elseif($widgetName == "auction" && $settings["home_auction_status"]){
                    
                    if($settings["main_type_products"] == 'physical'){
                       $geo = $Ads->queryGeo() ? " and " . $Ads->queryGeo() : "";
                    }
                    
                    if($settings["index_out_content_method"] == 0){
                      $data["auction"] = $Ads->getAll( ["query"=>"ads_status='1' and clients_status IN(0,1) and ads_period_publication > now() and ads_auction='1' order by rand() limit 16", "output" => 16 ] );
                    }else{
                      $data["auction"] = $Ads->getAll( ["query"=>"ads_status='1' and clients_status IN(0,1) and ads_period_publication > now() and ads_auction='1' $geo order by rand() limit 16", "output" => 16 ] );
                    }

                    if($settings["main_type_products"] == 'physical'){
                        if($_SESSION["geo"]["alias"]){
                          $data["auction_link"] = _link($_SESSION["geo"]["alias"]."/auction");
                        }else{
                          $data["auction_link"] = _link($settings["country_default"]."/auction"); 
                        }
                    }else{
                        $data["auction_link"] = _link("auction");
                    }

                    if($data["auction"]["count"]){
                        ?>
                        <div class="auction-block mb25" >
                          <div class="css-qwrg77" class="mb25 title-and-link h3" > <strong><?php echo $ULang->t( "Аукционы" ); ?></strong> <a href="<?php echo $data["auction_link"]; ?>" ><?php echo $ULang->t( "Больше объявлений" ); ?> <i class="las la-arrow-right"></i></a> </div>
                          <div class="slider-item-grid init-slider-grid" >
                              <?php 
                              
                                 foreach ( $data["auction"]["all"] as $key => $value) {
                                     include $config["template_path"] . "/include/auction_ad_grid.php";
                                 }
                              
                              ?>
                          </div>
                        </div>                    
                        <?php
                    }                 

                }
            }
           ?>
		   
		   <div class="d-none d-xl-block">

		                 <!-- Corrections at popular_ad_grid.php replaced tag <a> inside of <a> by tag span insted -->
						  <?php  include $config["template_path"] . "/include/popular_ad_grid.php";  ?> 
			              </div>

						  <div class="d-xl-none">
                           <!-- Corrections at popular_ad_grid_phone.php replaced tag <a> inside of <a> by tag span insted -->
						  <?php include $config["template_path"] . "/include/popular_ad_grid_phone.php"; ?>
		                  </div>

                          <div class="d-none d-xl-block">
                          <!-- Corrections at users_ad_grid_views.php replaced tag <a> inside of <a> by tag span insted -->                 
						  <?php  include $config["template_path"] . "/include/users_ad_grid_views.php";  ?> 
   
			              </div>

						  <div class="d-xl-none">
                           <!-- Corrections at users_ad_grid_views_phone.php replaced tag <a> inside of <a> by tag span insted -->  
						  <?php include $config["template_path"] . "/include/users_ad_grid_views_phone.php"; ?>
		                  </div> 

                        <div class="mb25 h3" > <strong><?php echo $ULang->t( "Рекомендации для вас" ); ?></strong> </div>

                <div class="catalog-results" >
          
              <div class="preload" >

                  <div class="spinner-grow mt35 preload-spinner" role="status">
                    <span class="sr-only"></span>
                  </div>

              </div>

          </div>

          <h1 style="font-size: 1.75rem;" class="mb25 mt35" > <strong><?php echo $data["h1"]; ?></strong> </h1>

          <div class="schema-text" >
             <?php if($data["seo_text"]){ ?> <div class="mt35" > <?php echo $data["seo_text"]; ?> </div> <?php } ?>
          </div>

          <?php echo $Banners->out( ["position_name"=>"index_bottom"] ); ?>
          </div>
       </div>

    </div>

    <div class="mt35" ></div>
    <?php include $config["template_path"] . "/footer.tpl"; ?>

  </body>
</html>

<!-- 16-08-2023 Correction 36 (validator W3) Element style not allowed as child of element div
		transferring to index.tpl in <head></head> section
<style>
@media only screen and (min-width: 1024px) {.owl-carousel {width: auto;}.slick-slide {margin: 0 5px; width: 178.8px;}
div a{color: #8d8176;text-decoration: none !important;border: none !important;}.m-index-category:hover{background:#f7f8fa;}}
@media only screen and (max-width: 767px) {.owl-carousel {width: 104%;}.slick-slide {margin: 0 5px; width: 180px;}}
.cat-bg {border: 4px solid #fff;height: 119px;border-radius: 0px;display: block;border: 1px solid #f5f5f5; margin: 0px;}
.slick-track {margin-left: 0px;}.item-grids{margin-left: 0px;}
div a{color: #8d8176;text-decoration: none !important;
border: none !important;}.m-index-category:hover{background:#f7f8fa;}}
</style>
-->