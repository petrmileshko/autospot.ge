<?php
	include $config["template_path"] . "/extra/fn.php";
?>
<!doctype html>
<html lang="<?php echo getLang(); ?>">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		
		<meta name="description" content="<?php echo $data["meta_desc"]; ?>">
		
		<title><?php echo $data["meta_title"]; ?></title>
		
		<link rel="canonical" href="<?php echo _link( explode("?", $_SERVER['REQUEST_URI'])[0] ); ?>"/>
		
		<?php include $config["template_path"] . "/head.tpl"; ?>
		
	</head>
	
	<body data-prefix="<?php echo $config["urlPrefix"]; ?>" data-template="<?php echo $config["template_folder"]; ?>" data-header-sticky="true" data-type-loading="<?php echo $settings["type_content_loading"]; ?>" data-page-name="<?php echo $route_name; ?>" >
		
		<?php include $config["template_path"] . "/header.tpl"; ?>

		<div class="col-lg-12 d-none d-lg-block" style="padding: 0px;">
			
			<?php include $config["template_path"] . "/catalog_sidebar.tpl"; ?>
			
		</div>
		
		<div class="container" >
			
			<?php echo $Banners->out( ["position_name"=>"catalog_top", "current_id_cat"=>$data["category"]["category_board_id"], "categories"=>$getCategoryBoard] ); ?>

			<div class="row" >
				
				<div class="col-lg-12 min-height-600" >

					<?php
						$outParent = $CategoryBoard->outParent($getCategoryBoard, [ "tpl_parent" => '<div class="col-lg-3 col-12 col-md-4 col-sm-4" ><a {ACTIVE} href="{PARENT_LINK}">{PARENT_NAME} {COUNT_AD}</a></div>', "tpl" => '{PARENT_CATEGORY}', "category" => $data["category"]]);
					?>
					
					<?php if( $outParent ){ ?>
						<div class="catalog-subcategory mb15 mt45" >
							
							<div class="row" >
								<?php 
									echo $outParent; 
								?>
							</div>
							
						</div>
					       <?php } ?>
					
					<?php if($data["seo_alias_category"]){ ?>
					<div class="slider-list-seo-filters list-seo-filters" >
						<?php echo $data["seo_alias_category"]; ?>
						</div> 
					<?php } ?>        

					<div class="row" >

						<div class="col-lg-12" >
							
							<?php if( $data["vip"]["count"] && !$data["param_filter"]["filter"]["vip"] ){ ?>
								<div class="mb20" >
									<div class="mb25 title-and-link h4 mt50" > <strong><?php echo $ULang->t( "VIP объявления" ); ?></strong> <a href="<?php echo $data["vip_link"]; ?>" ><?php echo $ULang->t( "Больше объявлений" ); ?> <i class="las la-arrow-right"></i></a> 
										    <span class="d-none d-md-block"  style="float: right;font-size:16px; margin-top: 5px;">
						                         <img src="/media/others/hot.svg" style="width:22px;margin-top: -5px;">
						                      <a style="color:#000; font-size:15px;" href="/promo/hot-<?php $lang = getLang(); if ($lang != "geo" && $lang != "rus") { $lang = "geo";} echo $lang; ?>" ><?php echo $ULang->t( "Разместить сюда" ); ?></a>
						                    </span>
										</div>
										<div class="d-block d-lg-none mb20">
						        <img src="/media/others/hot.svg" style="width:22px;margin-top: -5px;">
						        <a style="color:#000; font-size:15px;" href="/promo/hot-<?php $lang = getLang(); if ($lang != "geo" && $lang != "rus") { $lang = "geo";} echo $lang; ?>" ><?php echo $ULang->t( "Разместить сюда" ); ?></a>
						</div>
						
									<div class="slider-item-grid">
										<div class="owl-carousel init-slider-grid gutters10" style="padding: 10px;" data-slick='{"infinite": true}'>
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
								</div>
								<?php
								} 
							?>
							<div class="row mt15" style="float: left;margin-left: 0px;">
							     <h1 class="title-and-link h4" ><strong><?php echo $data["h1"]; ?> </strong> </h1>

					        </div>
							<div class="catalog-sort text-right mt30 mb10" >

                                
								<div>
									<?php echo $Ads->outSorting(); ?>
								</div>

								<?php if($settings["main_type_products"] == 'physical'){ ?>

								<?php } ?>
								
								<div>
									
									<span class="catalog-sort-link-button catalog-ads-subscriptions-add" data-tippy-placement="bottom" title="<?php echo $ULang->t("Подписка на поиск"); ?>" ><i class="las la-bell"></i></span>
									
								</div>

								<div data-view="grid" class="catalog-ad-view <?php if($_SESSION["catalog_ad_view"] == "grid" || !$_SESSION["catalog_ad_view"]){ echo 'active'; } ?>" > <i class="las la-border-all"></i> </div>
								<div data-view="list" class="catalog-ad-view <?php if($_SESSION["catalog_ad_view"] == "list"){ echo 'active'; } ?>" > <i class="las la-list"></i> </div>
								
							</div>
							
							<div class="catalog-results" >
								
								<div class="preload" >
									
									<img src="/media/others/animation_lnj7ji3l_small.gif"/>
									
								</div>
								
							</div>
							
						</div>
						
						
					</div>
					
					
				</div>

				<?php if($data["seo_text"]){ ?> <div class="mt35 schema-text" > <?php echo $data["seo_text"]; ?> </div> <?php } ?>
				
				<div class="mt50" ></div>
				
				<?php echo $Banners->out( ["position_name"=>"catalog_bottom", "current_id_cat"=>$data["category"]["category_board_id"], "categories"=>$getCategoryBoard] ); ?>
				
			</div>
			
			<noindex>
				
				<div class="modal-custom-bg bg-click-close" id="modal-ads-subscriptions" style="display: none;" >
					<div class="modal-custom" style="max-width: 500px;" >
						
						<span class="modal-custom-close" ><i class="las la-times"></i></span>
						
						<div class="modal-ads-subscriptions-block-1" >
							
							<h4> <strong><?php echo $ULang->t("Подписка на объявления"); ?></strong> </h4>
							
							<p><?php echo $ULang->t("Новые объявления будут приходить на электронную почту"); ?></p>
							
							<?php if( !$_SESSION["profile"]["id"] ){ ?>
								<div class="create-info" >
									<?php echo $ULang->t("Для удобного управления подписками"); ?> - <a href="<?php echo _link("auth"); ?>"><?php echo $ULang->t("войдите в личный кабинет"); ?></a>
								</div>
							<?php } ?>
							
							<form class="modal-ads-subscriptions-form mt20" >
								
								<label><?php echo $ULang->t("Ваш e-mail"); ?></label>
								
								<input type="text" name="email" class="form-control" value="<?php echo $_SESSION["profile"]["data"]["clients_email"]; ?>" >
								
								<label class="mt15" ><?php echo $ULang->t("Частота уведомлений"); ?></label>
								
								<select name="period" class="form-control" >
									<option value="1" selected="" ><?php echo $ULang->t("Раз в день"); ?></option>
									<option value="2" ><?php echo $ULang->t("Сразу при публикации"); ?></option>
								</select>
								
								<input type="hidden" name="url" value="<?php echo $Ads->buildUrlCatalog( $data ); ?>" >
								
							</form>
							
							<div class="mt30" >
								<button class="btn-custom btn-color-blue width100 modal-ads-subscriptions-add mb5" ><?php echo $ULang->t("Подписаться"); ?></button>
							</div>
							
							<p style="font-size: 13px; color: #7a7a7a;" class="mt15" ><?php echo $ULang->t("При подписке вы принимаете условия"); ?> <a href="<?php echo _link("polzovatelskoe-soglashenie"); ?>"><?php echo $ULang->t("Пользовательского соглашения"); ?></a> <?php echo $ULang->t("и"); ?> <a href="<?php echo _link("privacy-policy"); ?>"><?php echo $ULang->t("Политики конфиденциальности"); ?></a></p>
							
						</div>
						
						<div class="modal-ads-subscriptions-block-2" style="text-align: center;" >
							
							<i class="las la-check checkSuccess"></i>
							
							<h3> <strong><?php echo $ULang->t("Подписка оформлена"); ?></strong> </h3>
							
							<p><?php echo $ULang->t("Если вы захотите отписаться от рассылки - просто нажмите на соответствующую кнопку в тексте письма, либо перейдите в раздел"); ?> <a href="<?php if($_SESSION["profile"]["id"]){ echo _link( "user/" . $_SESSION["profile"]["data"]["clients_id_hash"] . "/subscriptions" ); }else{ echo _link( "auth" ); } ?>"><?php echo $ULang->t("управления подписками"); ?></a></p>
							
						</div>
						
					</div>
				</div>
				
			</noindex>
			
			<div class="mt20">
				<nav aria-label="breadcrumb">
					
					<ol class="breadcrumb" itemscope="" itemtype="http://schema.org/BreadcrumbList">
						
						<li class="breadcrumb-item" itemprop="itemListElement" itemscope="" itemtype="http://schema.org/ListItem">
							<a itemprop="item" href="<?php echo $config["urlPath"]; ?>">
							<span itemprop="name"><?php echo $ULang->t("Главная"); ?></span></a>
							<meta itemprop="position" content="1">
						</li>
						
						<?php echo $data["breadcrumb"]; ?>
						
					</ol>
					
				</nav>
			</div>
			<div class="mt35" ></div>
			
			<?php include $config["template_path"] . "/footer.tpl"; ?>
			
		</body>
		</html>
<style>
@media only screen and (min-width: 1024px) {.owl-carousel {width: auto;}.slick-slide {margin: 0 5px; width: 178.8px;}
 div a{color: #8d8176; text-decoration: none !important; border: none !important;}.m-index-category:hover{background:#f7f8fa;}}
@media only screen and (max-width: 767px) {.owl-carousel {width: 104%;}.slick-slide {margin: 0 5px; width: 180px;}}
.cat-bg {border: 4px solid #fff;height: 119px;border-radius: 0px;display: block;border: 1px solid #f5f5f5; margin: 0px;}
.slick-track {margin-left: 0px;}.item-grids{margin-left: 0px;}
div a{color: #8d8176; text-decoration: none !important; border: none !important;}.m-index-category:hover{background:#f7f8fa;}}
</style>