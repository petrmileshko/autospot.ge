
<div class="mb25 h3" > <strong><?php echo $ULang->t( "Сейчас смотрят" ); ?></strong></div>
<div id="slider" class="slick-slider row no-gutters gutters10" >
	<?php
		$conn = new mysqli($config["db"]["host"], $config["db"]["user"], $config["db"]["pass"], $config["db"]["database"]);
		$conn->query("SET NAMES 'utf8mb4'");
		
		// Получаем текущую дату и время
		// Получаем текущую дату и время
		$currentDateTime = date('Y-m-d H:i:s');
		
		// Определяем дату и время, за которое хотим получить объявления (последний час)
		$oneHourAgo = date('Y-m-d H:i:s', strtotime('-7 day', strtotime($currentDateTime)));
		
		$sql_select = "SELECT vp.*, uc.city_name, ua.ads_price AS ads_price_usd
		FROM (
		SELECT ads_id, MAX(visit_date) AS max_visit_date
		FROM uni_views_users
		GROUP BY ads_id
		) AS max_dates
		INNER JOIN uni_views_users AS vp ON vp.ads_id = max_dates.ads_id AND vp.visit_date = max_dates.max_visit_date 
		INNER JOIN uni_city uc ON vp.ads_geo = uc.city_id
		INNER JOIN uni_ads ua ON vp.ads_id = ua.ads_id
		WHERE ua.ads_status = 1
		ORDER BY vp.visit_date DESC
		LIMIT 6;
		";
		$result_select = $conn->query($sql_select);
		
		if ($result_select->num_rows > 0) {
			while ($row = $result_select->fetch_assoc()) {
				$ads_id = $row["ads_id"];
				$ads_img = $row["ads_img"];
				$ads_title = $row["ads_title"];
				$ads_price_usd = $row["ads_price"];
				$ads_geo = $row["ads_geo"];
				$city_name = $row["city_name"];
				$ads_url = $row["ads_url"];
				$ads_count = $row["ads_count"];
				$visit_date = $row["visit_date"];
				
				// Query to retrieve ads_price from uni_ads table based on ads_id
				$sql_price = "SELECT ads_price FROM uni_ads WHERE ads_id = $ads_id";
				$result_price = $conn->query($sql_price);
				
				if ($result_price->num_rows > 0) {
					$row_price = $result_price->fetch_assoc();
					$ads_price = $row_price["ads_price"];
					} else {
					$ads_price = "N/A"; // Default value if price is not found
				}
			?>
			<div class='item-grid item-grids' title="<?php echo $ads_title; ?>">
				<a href="<?php echo $ads_url; ?>" title="<?php echo $ads_title; ?>">	
					<div class="item-grid-img">
						
						<div class="item-labels"></div>
						<img src="<?php echo $ads_img; ?>" class="image-autofocus ad-gallery-hover-slider-image lazyload" data-src="<?php echo $ads_title; ?>" data-key="0" alt="<?php echo $ads_title; ?>">
						<span style="top: auto;" class="item-grid-count-photo"><i class="las la-camera"></i><?php echo $ads_count; ?></span>
						
					</div>
					<div class="item-grid-info">	
						<div class="item-grid-price-now">
							<?php
										$language = getLang();
										if (strpos($ads_price_usd, 'Цена договорная') !== false) {
											if ($language === getLang()) {
												echo $ULang->t("Цена договорная");
												} else {
												echo "Price not specified";
											}
											} else {
										?>
								<div class="item-grid-price">
									<!-- Блок с ценой в USD -->
									<div id="usdPriceBlock_<?php echo $ads_id ?>7">
										<?php echo $ads_price_usd; ?>
									</div>
									
									<!-- Блок с ценой в LARI (скрыт по умолчанию) -->
									<div id="lariPriceBlock_<?php echo $ads_id ?>7" style="display: none;">
										<?php echo ($ads_price == 0) ? "".$ULang->t( "Договорная" )."" : number_format($ads_price, 0, '', ',') . " ₾"; ?>
									</div>
								</div>
								
								<script>
									$(document).ready(function () {
										// Получить значение выбранной кнопки из Local Storage
										var selectedButton = localStorage.getItem('selectedButton');
										
										// При загрузке страницы восстановить состояние кнопок
										if (selectedButton === 'lari') {
											$("#usdPriceBlock_<?php echo $ads_id ?>7").hide();
											$("#lariPriceBlock_<?php echo $ads_id ?>7").show();
											} else {
											$("#usdPriceBlock_<?php echo $ads_id ?>7").show();
											$("#lariPriceBlock_<?php echo $ads_id ?>7").hide();
										}
										
										// При нажатии на кнопку "USD"
										$("#btnUSD").on("click", function () {
											// Показать блок с ценой в USD
											$("#usdPriceBlock_<?php echo $ads_id ?>7").show();
											// Скрыть блок с ценой в LARI
											$("#lariPriceBlock_<?php echo $ads_id ?>7").hide();
											// Сохранить выбранную кнопку в Local Storage
											localStorage.setItem('selectedButton', 'usd');
										});
										
										// При нажатии на кнопку "LARI"
										$("#btnLARI").on("click", function () {
											// Скрыть блок с ценой в USD
											$("#usdPriceBlock_<?php echo $ads_id ?>7").hide();
											// Показать блок с ценой в LARI
											$("#lariPriceBlock_<?php echo $ads_id ?>7").show();
											// Сохранить выбранную кнопку в Local Storage
											localStorage.setItem('selectedButton', 'lari');
										});
										// При нажатии на кнопку "USD"
										$("#btnUSD2").on("click", function () {
											// Показать блок с ценой в USD
											$("#usdPriceBlock_<?php echo $ads_id ?>7").show();
											// Скрыть блок с ценой в LARI
											$("#lariPriceBlock_<?php echo $ads_id ?>7").hide();
											// Сохранить выбранную кнопку в Local Storage
											localStorage.setItem('selectedButton', 'usd');
										});
										
										// При нажатии на кнопку "LARI"
										$("#btnLARI2").on("click", function () {
											// Скрыть блок с ценой в USD
											$("#usdPriceBlock_<?php echo $ads_id ?>7").hide();
											// Показать блок с ценой в LARI
											$("#lariPriceBlock_<?php echo $ads_id ?>7").show();
											// Сохранить выбранную кнопку в Local Storage
											localStorage.setItem('selectedButton', 'lari');
										});
									});
								</script>
								
								<?php
								}
							?>
						</div>
						<!-- 16-08-2023 Correction 22 Replaced <a></a> inside other link by span -->
						<span><?php echo custom_substr($ads_title, 35, "..."); ?></span>
						<span class="item-grid-city"><?php echo $ULang->t($value["city_name"], ["table" => "geo", "field" => "geo_name"]); ?></span>
					</div>
					</a>	<!-- 16-08-2023 Correction 23 closing tag </a> was after parent container tag </div> -->
				</div>
			<?php
			}
		}
	?>
	
</div>
		<!-- 16-08-2023
		Correction 24  Element style not allowed as child of element div
		transferring to index.tpl in <head></head> section

		<style> 
	
	@media only screen and (min-width: 992px) {.item-grids {  max-width: 202px;}}
	.slick-track{float:left;margin-left: -10px;}.board-view-price{font-size:17px;} 
</style>
-->
