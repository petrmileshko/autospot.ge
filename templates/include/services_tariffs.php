<div class="ads-services-tariffs css-dsfgt47 col-lg-4 col-md-4 col-sm-12 col-12 item-grid "  data-id="<?php echo $value["services_ads_uid"]; ?>" data-id="3">
	<?php
		$conn = new mysqli($config["db"]["host"], $config["db"]["user"], $config["db"]["pass"], $config["db"]["database"]);
		$conn->set_charset("utf8");
		$sql = "SELECT sign FROM uni_currency WHERE main = 1";
		$result = $conn->query($sql);
	?>   
    <?php if($value["services_ads_recommended"]){ ?>
		<span class="ads-services-tariffs-discount" ><?php echo $ULang->t("Рекомендуем"); ?></span>
	<?php } ?>
	
	<div class="ads-services-tariffs-icon" >
		<span> <img src="<?php echo Exists($config["media"]["other"],$value["services_ads_image"],$config["media"]["no_image"]); ?>" height="55" > </span>
	</div>
	<p><strong><?php echo $ULang->t( $value["services_ads_name"], [ "table"=>"uni_services_ads", "field"=>"services_ads_name" ] ); ?></strong></p>
    <?php if($value["services_ads_variant"] == 1){ ?>
		<p><?php echo $ULang->t("Действует"); ?> <?php echo $value["services_ads_count_day"]; ?> <?php echo $ULang->t("дней"); ?></p>
		<?php }else{ ?>
		<div class="input-group input-group-sm">
			<input type="number" class="form-control" name="service[<?php echo $value["services_ads_uid"]; ?>]" id="services-ads-count-<?php echo $value["services_ads_uid"]; ?>" value="1">
			<div class="input-group-append">
				<span class="input-group-text"><?php echo $ULang->t("дней"); ?></span>
			</div>
		</div>                              
	<?php } ?>
    <p><?php echo $ULang->t( $value["services_ads_text"], [ "table"=>"uni_services_ads", "field"=>"services_ads_text" ] ); ?></p>
    <strong>
		<p class="ads-services-tariffs-price-now"><span id="total-price-<?php echo $value["services_ads_uid"]; ?>"><?php echo $value["services_ads_new_price"] ? $value["services_ads_new_price"] : $value["services_ads_price"]; ?></span> <?php if ($result->num_rows > 0) {
			while($row = $result->fetch_assoc()) {
				echo "" . $row["sign"];
			}} ?>
		</p>
	</strong>
	
    <?php echo $Main->outPrices( array("new_price"=> array("price"=>$value["services_ads_new_price"], "tpl"=>'<p class="ads-services-tariffs-price-now" > <strong></strong> </p>'), "price"=>array("price"=>$value["services_ads_price"], "tpl"=>'<p class="ads-services-tariffs-price-old" >'.$ULang->t("Цена без скидки").' <span>{price}</span></p>') ) ); ?>
	
<style>
	@media only screen and (min-width: 768px) {
  /* Стили для компьютера */
  .css-dsfgt47 {
    max-width: 325px;
  }
}
	
</style>
	

	<script>
		document.getElementById('services-ads-count-<?php echo $value["services_ads_uid"]; ?>').addEventListener('change', function() {
			var count = parseInt(this.value);
			var price = <?php echo $value["services_ads_price"]; ?>;
			var totalPrice = price * count;
			document.getElementById('total-price-<?php echo $value["services_ads_uid"]; ?>').textContent = totalPrice;
		});
	</script>
	<script>
		// Получаем все элементы с классом "ads-services-tariffs"
		var elements = document.getElementsByClassName("ads-services-tariffs");
		
		// Перебираем элементы и проверяем значение атрибута "data-id"
		for (var i = 0; i < elements.length; i++) {
			var element = elements[i];
			var dataId = element.getAttribute("data-id");
			
			// Проверяем, равно ли значение "data-id" значению, которое хотим сделать активным по умолчанию (в данном случае 3)
			if (dataId === "3") {
				// Применяем необходимые стили или действия к элементу
				element.classList.add("active"); // Например, добавляем класс "active"
			}
		}
	</script>
</div>
