<?php 
$images = $Ads->getImages($value["ads_images"]);
$service = $Ads->adServices($value["ads_id"]);
$getShop = $Shop->getUserShop($value["ads_id_user"]);
?>
<div class="<?php if($settings["home_sidebar_status"]){ echo 'col-lg-3 col-md-3 col-sm-6 col-6'; }else{ echo 'col-lg-2 col-md-3 col-sm-3 col-6'; } ?>" >
  <div class="item-grid <?php echo isset($service[2]) || isset($service[3]) ? "ads-highlight" : ""; ?>" title="<?php echo $value["ads_title"]; ?>" >
     <div class="item-grid-img" >
     <a href="<?php echo $Ads->alias($value); ?>" title="<?php echo $value["ads_title"]; ?>" target="_blank" >

       <div class="item-labels" >
          <?php echo $Ads->outStatus($service, $value); ?>
       </div>

       <?php echo $Ads->CatalogOutAdGallery($images, $value); ?>

     </a>
     <?php echo $Ads->adActionFavorite($value, "catalog", "item-grid-favorite"); ?>
     </div>
     <div class="item-grid-info" >

        <div class="item-grid-price" >
         <!-- Блок с ценой в USD -->
			<div id="usdPriceBlock_<?php echo $value['ads_id']; ?>3">
				<?php
                            echo $Ads->outPrice( ["data"=>$value,"class_price"=>"item-list-price-now","class_price_old"=>"item-list-price-old", "shop"=>$getShop, "abbreviation_million" => true] );
                ?>
			</div>
			
			<!-- Блок с ценой в LARI (скрыт по умолчанию) -->
			<div class="item-grid-price-now" id="lariPriceBlock_<?php echo $value['ads_id']; ?>3" style="display: none;">
				<?php echo ($value["ads_price"] == 0 || $param["data"]["ads_currency"] == '₾') ? "". $ULang->t("Договорная") ."" : "{$Main->price($value['ads_price'], $param['data']['ads_currency'])} {$param['data']['ads_currency']}";
				?>
			</div>      
        </div>
        <a href="<?php echo $Ads->alias($value); ?>" target="_blank" ><?php echo custom_substr($value["ads_title"], 35, "..."); ?></a>

     </div>
  </div>
  <script>
	$(document).ready(function () {
		// Получить значение выбранной кнопки из Local Storage
		var selectedButton = localStorage.getItem('selectedButton');
		
		// При загрузке страницы восстановить состояние кнопок
		if (selectedButton === 'lari') {
			$("#usdPriceBlock_<?php echo $value['ads_id']; ?>3").hide();
			$("#lariPriceBlock_<?php echo $value['ads_id']; ?>3").show();
			} else {
			$("#usdPriceBlock_<?php echo $value['ads_id']; ?>3").show();
			$("#lariPriceBlock_<?php echo $value['ads_id']; ?>3").hide();
		}
		
		// При нажатии на кнопку "USD"
		$("#btnUSD").on("click", function () {
			// Показать блок с ценой в USD
			$("#usdPriceBlock_<?php echo $value['ads_id']; ?>3").show();
			// Скрыть блок с ценой в LARI
			$("#lariPriceBlock_<?php echo $value['ads_id']; ?>3").hide();
			// Сохранить выбранную кнопку в Local Storage
			localStorage.setItem('selectedButton', 'usd');
		});
		
		// При нажатии на кнопку "LARI"
		$("#btnLARI").on("click", function () {
			// Скрыть блок с ценой в USD
			$("#usdPriceBlock_<?php echo $value['ads_id']; ?>3").hide();
			// Показать блок с ценой в LARI
			$("#lariPriceBlock_<?php echo $value['ads_id']; ?>3").show();
			// Сохранить выбранную кнопку в Local Storage
			localStorage.setItem('selectedButton', 'lari');
		});
		
		// При нажатии на кнопку "USD"
		$("#btnUSD2").on("click", function () {
			// Показать блок с ценой в USD
			$("#usdPriceBlock_<?php echo $value['ads_id']; ?>3").show();
			// Скрыть блок с ценой в LARI
			$("#lariPriceBlock_<?php echo $value['ads_id']; ?>3").hide();
			// Сохранить выбранную кнопку в Local Storage
			localStorage.setItem('selectedButton', 'usd');
		});
		
		// При нажатии на кнопку "LARI"
		$("#btnLARI2").on("click", function () {
			// Скрыть блок с ценой в USD
			$("#usdPriceBlock_<?php echo $value['ads_id']; ?>3").hide();
			// Показать блок с ценой в LARI
			$("#lariPriceBlock_<?php echo $value['ads_id']; ?>3").show();
			// Сохранить выбранную кнопку в Local Storage
			localStorage.setItem('selectedButton', 'lari');
		});
		
	});
</script>

</div>


<?php 
echo $Banners->results( ["position_name"=>"result", "current_id_cat"=>isset($data["category"]["category_board_id"]) ? $data["category"]["category_board_id"] : 0,"categories"=>isset($getCategoryBoard) ? $getCategoryBoard : [], "index"=>$key] );
?>