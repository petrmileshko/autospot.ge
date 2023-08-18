<?php 
	$images = $Ads->getImages($value["ads_images"]);
	$getShop = $Shop->getUserShop( $value["ads_id_user"] );
	
?>

<div class="item-grid" title="<?php echo $value["ads_title"]; ?>" >
   
   
	<div class="item-grid-img thumbs" >
		<div class="caption">
			<span  style="margin-top: -180px;font-size: 12px; background: #000;"><?php 
				echo $Ads->outAdAddressArea($value);
			?></span>
		</div>
		<a href="<?php echo $Ads->alias($value); ?>" title="<?php echo $value["ads_title"]; ?>" target="_blank" >
			
			<div class="item-labels" >
				<?php echo $Ads->outStatus([], $value); ?>
			</div>
			
			<?php echo $Ads->CatalogOutAdGallery($images, $value); ?>
			
		</a>
		<a href="<?php echo $Ads->alias($value); ?>" target="_blank" style="color: #fff;">
			<?php echo $Ads->adActionFavorite($value, "catalog", "item-grid-favorite"); ?>
			<div class="caption">
				<span class="title"><?php echo custom_substr($value["ads_title"], 35, "..."); ?></span>
				<div class="info info__div"> 
					
					<div class="item-grid-price">
						<!-- Блок с ценой в USD -->
						<div id="usdPriceBlock_<?php echo $value['ads_id']; ?>1">
							<?php
								echo $Ads->outPrice( [ "data"=>$value,"class_price"=>"item-grid-price-now pri","class_price_old"=>"item-grid-price-old", "shop"=>$getShop, "abbreviation_million" => true ] );
							?> 
						</div>
						
						<!-- Блок с ценой в LARI (скрыт по умолчанию) -->
						<div class="item-grid-price-now" id="lariPriceBlock_<?php echo $value['ads_id']; ?>1" style="display: none;color:#fff;">
							<?php echo ($value["ads_price"] == 0 || $param["data"]["ads_currency"] == '₾') ? "". $ULang->t("Договорная") ."" : "{$Main->price($value['ads_price'], $param['data']['ads_currency'])} {$param['data']['ads_currency']}";
							?>
						</div>
					</div>
				</div>
			</div>
		</a>
	</div>  <!-- Подключаем jQuery -->

	<script>
		$(document).ready(function () {
			// Получить значение выбранной кнопки из Local Storage
			var selectedButton = localStorage.getItem('selectedButton');
			
			// При загрузке страницы восстановить состояние кнопок
			if (selectedButton === 'lari') {
				$("#usdPriceBlock_<?php echo $value['ads_id']; ?>1").hide();
				$("#lariPriceBlock_<?php echo $value['ads_id']; ?>1").show();
				} else {
				$("#usdPriceBlock_<?php echo $value['ads_id']; ?>1").show();
				$("#lariPriceBlock_<?php echo $value['ads_id']; ?>1").hide();
			}
			
			// При нажатии на кнопку "USD"
			$("#btnUSD").on("click", function () {
				// Показать блок с ценой в USD
				$("#usdPriceBlock_<?php echo $value['ads_id']; ?>1").show();
				// Скрыть блок с ценой в LARI
				$("#lariPriceBlock_<?php echo $value['ads_id']; ?>1").hide();
				// Сохранить выбранную кнопку в Local Storage
				localStorage.setItem('selectedButton', 'usd');
			});
			
			// При нажатии на кнопку "LARI"
			$("#btnLARI").on("click", function () {
				// Скрыть блок с ценой в USD
				$("#usdPriceBlock_<?php echo $value['ads_id']; ?>1").hide();
				// Показать блок с ценой в LARI
				$("#lariPriceBlock_<?php echo $value['ads_id']; ?>1").show();
				// Сохранить выбранную кнопку в Local Storage
				localStorage.setItem('selectedButton', 'lari');
			});
			// При нажатии на кнопку "USD"
			$("#btnUSD2").on("click", function () {
				// Показать блок с ценой в USD
				$("#usdPriceBlock_<?php echo $value['ads_id']; ?>1").show();
				// Скрыть блок с ценой в LARI
				$("#lariPriceBlock_<?php echo $value['ads_id']; ?>1").hide();
				// Сохранить выбранную кнопку в Local Storage
				localStorage.setItem('selectedButton', 'usd');
			});
			
			// При нажатии на кнопку "LARI"
			$("#btnLARI2").on("click", function () {
				// Скрыть блок с ценой в USD
				$("#usdPriceBlock_<?php echo $value['ads_id']; ?>1").hide();
				// Показать блок с ценой в LARI
				$("#lariPriceBlock_<?php echo $value['ads_id']; ?>1").show();
				// Сохранить выбранную кнопку в Local Storage
				localStorage.setItem('selectedButton', 'lari');
			});
		});
	</script>
</div>
