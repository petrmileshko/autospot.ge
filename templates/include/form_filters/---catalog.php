<?php if( $getCategoryBoard["category_board_id"][ $data["category"]["category_board_id"] ]["category_board_booking"] ){ ?>

    <?php
    if( $getCategoryBoard["category_board_id"][ $data["category"]["category_board_id"] ]["category_board_booking_variant"] == 0 ){

?>
		
<div class=" toggle-list-options catalog-list-options-active" >
  <span class="catalog-list-options-name f w" >
    <?php echo $ULang->t("Даты"); ?>  
      <i class="las la-angle-down"></i>
  </span>
            
            <div class="catalog-list-options-content" >

                <div class="catalog-list-options-content" >
                    <div class="filter-input" >
                      <div><span><?php echo $ULang->t("с"); ?></span><input type="text" class="catalog-change-date-from" name="filter[date][start]" value="<?php if($data["param_filter"]["filter"]["date"]["start"]) echo date("d.m.Y", strtotime($data["param_filter"]["filter"]["date"]["start"])); ?>" /></div>
                      <div><span><?php echo $ULang->t("по"); ?></span><input type="text" class="catalog-change-date-to" name="filter[date][end]" value="<?php if($data["param_filter"]["filter"]["date"]["end"]) echo date("d.m.Y", strtotime($data["param_filter"]["filter"]["date"]["end"])); ?>" /></div>
                    </div>                    
                </div>

            </div>
</div>

<?php }elseif( $getCategoryBoard["category_board_id"][ $data["category"]["category_board_id"] ]["category_board_booking_variant"] == 1 ){ ?>

<div class="custom-control custom-checkbox">
    <input type="checkbox" class="custom-control-input" name="filter[booking]" <?php if($data["param_filter"]["filter"]["booking"]){ echo 'checked=""'; } ?> id="booking_variant" value="1" >
    <label class="custom-control-label" for="booking_variant"><?php echo $ULang->t("Онлайн-аренда"); ?></label>
</div>

<div class=" toggle-list-options catalog-list-options-active" >

            <span class="catalog-list-options-name f w">
            <?php echo $ULang->t("Даты"); ?>  
            <i class="las la-angle-down"></i>
            </span>
            
            <div class="catalog-list-options-content" >

                <div class="catalog-list-options-content" >
                    <div class="filter-input" >
                      <div><span><?php echo $ULang->t("с"); ?></span><input type="text" class="catalog-change-date-from" name="filter[date][start]" value="<?php if($data["param_filter"]["filter"]["date"]["start"]) echo date("d.m.Y", strtotime($data["param_filter"]["filter"]["date"]["start"])); ?>" /></div>
                      <div><span><?php echo $ULang->t("по"); ?></span><input type="text" class="catalog-change-date-to" name="filter[date][end]" value="<?php if($data["param_filter"]["filter"]["date"]["end"]) echo date("d.m.Y", strtotime($data["param_filter"]["filter"]["date"]["end"])); ?>" /></div>
                    </div>                    
				</div>

            </div>

</div>

<?php } ?>
<?php } ?>

<div class="catalog-more-filter s catalog-more-filter-action d-none">

	
<div class="catalog-list-options-name f w d" >
		<input type="text" class="form-controls modal-search-geo-input wb" style="padding:inherit;" value="<?php echo $ULang->t($Geo->change()["name"], [ "table"=>"geo", "field"=>"geo_name" ] ); ?>" placeholder="<?php echo $ULang->t("Введите название города"); ?>" > 
        <div class="modal-search-geo-results" style="display: none;"></div>
</div>

<?php 
if($data["category"]["category_board_id"]){
    if( $getCategoryBoard["category_board_id"][ $data["category"]["category_board_id"] ]["category_board_display_price"] ){ 
    ?>
    <div class=" toggle-list-options catalog-list-options-active d" >

        <div class="catalog-list-options-content" >
        <div class="filter-input" >
          <div class="wb"><span></span><input type="text" placeholder="<?php echo $ULang->t("Цена от"); ?>" class="inputNumber" name="filter[price][from]" value="<?php if($data["param_filter"]["filter"]["price"]["from"]) echo round($data["param_filter"]["filter"]["price"]["from"],2); ?>" /></div>
          <div class="wb"><span></span><input type="text" placeholder="<?php echo $ULang->t("до"); ?>" class="inputNumber" name="filter[price][to]" value="<?php if($data["param_filter"]["filter"]["price"]["to"]) echo round($data["param_filter"]["filter"]["price"]["to"],2); ?>" /></div>
        </div>
        </div>

    </div>
    <?php 
    } 
}else{
    ?>
    <div class=" toggle-list-options  catalog-list-options-active" >

        <span class="catalog-list-options-name f w" >
        <?php echo $ULang->t("Цена"); ?>  
        <i class="las la-angle-down"></i>
        </span>
        
        <div class="catalog-list-options-content" >
        <div class="filter-input" >
          <div class="wb"><span><?php echo $ULang->t("от"); ?></span><input type="text" class="inputNumber" name="filter[price][from]" value="<?php if($data["param_filter"]["filter"]["price"]["from"]) echo round($data["param_filter"]["filter"]["price"]["from"],2); ?>" /></div>
          <div class="wb"><span><?php echo $ULang->t("до"); ?></span><input type="text" class="inputNumber" name="filter[price][to]" value="<?php if($data["param_filter"]["filter"]["price"]["to"]) echo round($data["param_filter"]["filter"]["price"]["to"],2); ?>" /></div>
        </div>
        </div>

    </div>    
    <?php
}

?>

<div class="toggle-list-optionst d filter-items <?php if($data["param_filter"]["filter"]["period"]){ echo 'catalog-list-options-active'; } ?>">
	<div class="uni-select" data-status="0">
			<div class="uni-select-name catalog-list-options-name f w filter-select" style="height:45px;" 
			<span class="catalog-list-options-name f w" ><?php echo $ULang->t("Тип размещения"); ?> <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="1em" height="1em" class="css-mf5jvh"><path fill="currentColor" fill-rule="evenodd" d="M2.001 6.5h1.414l1.27 1.271 7.316 7.315 7.315-7.315L20.587 6.5h1.414v1.414l-1.27 1.27-7.316 7.316-1 1h-.827l-3.942-3.942-4.374-4.374-1.27-1.27z"></path></svg></span>
			
			</div>
			<div class="uni-select-list  css-dqw345 " style="background-color: #fff;border: 0px solid #d8d8d8; box-shadow: 0 0 2px 0 rgb(0 0 0 / 15%);">
			
			
    <span class="catalog-list-options-single " >
        
        <?php if( $getCategoryBoard["category_board_id"][ $data["category"]["category_board_id"] ]["category_board_secure"] && $settings["secure_status"] ){ ?>
        <div class="custom-control custom-checkbox">
            <input type="checkbox" class="custom-control-input" name="filter[secure]" <?php if($data["param_filter"]["filter"]["secure"]){ echo 'checked=""'; } ?> id="flsecure" value="1" >
            <label class="custom-control-label" for="flsecure"><?php echo $ULang->t("Безопасная сделка"); ?></label>
        </div>
        <?php } ?>
        
        <?php if( $getCategoryBoard["category_board_id"][ $data["category"]["category_board_id"] ]["category_board_auction"] || isset($data["param_filter"]["filter"]["auction"]) ){ ?>
        <div class="custom-control custom-checkbox">
            <input type="checkbox" class="custom-control-input" name="filter[auction]" <?php if($data["param_filter"]["filter"]["auction"]){ echo 'checked=""'; } ?> id="flauction" value="1" >
            <label class="custom-control-label" for="flauction"><?php echo $ULang->t("Аукцион"); ?></label>
        </div>
        <?php } ?>
        
        <?php if( $getCategoryBoard["category_board_id"][ $data["category"]["category_board_id"] ]["category_board_online_view"] ){ ?>
        <div class="custom-control custom-checkbox">
            <input type="checkbox" class="custom-control-input" name="filter[online_view]" <?php if($data["param_filter"]["filter"]["online_view"]){ echo 'checked=""'; } ?> id="online_view" value="1" >
            <label class="custom-control-label" for="online_view"><?php echo $ULang->t("Онлайн-показ"); ?></label>
        </div>
        <?php } ?>

        <div class="custom-control custom-checkbox">
            <input type="checkbox" class="custom-control-input" name="filter[vip]" <?php if($data["param_filter"]["filter"]["vip"]){ echo 'checked=""'; } ?> id="flvip" value="1" >
            <label class="custom-control-label" for="flvip"><?php echo $ULang->t("VIP"); ?></label>
        </div>

        <?php if( $getCategoryBoard["category_board_id"][ $data["category"]["category_board_id"] ]["category_board_booking"] ){ ?>

            <?php
            if( $getCategoryBoard["category_board_id"][ $data["category"]["category_board_id"] ]["category_board_booking_variant"] == 0 ){
            ?>

                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input" name="filter[booking]" <?php if($data["param_filter"]["filter"]["booking"]){ echo 'checked=""'; } ?> id="booking_variant" value="1" >
                    <label class="custom-control-label" for="booking_variant"><?php echo $ULang->t("Онлайн-бронирование"); ?></label>
                </div>

            <?php }elseif( $getCategoryBoard["category_board_id"][ $data["category"]["category_board_id"] ]["category_board_booking_variant"] == 1 ){ ?>

                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input" name="filter[booking]" <?php if($data["param_filter"]["filter"]["booking"]){ echo 'checked=""'; } ?> id="booking_variant" value="1" >
                    <label class="custom-control-label" for="booking_variant"><?php echo $ULang->t("Онлайн-аренда"); ?></label>
                </div>

            <?php } ?>

        <?php } ?>
        
    </span>

</div> 
</div>
</div>

<div class="toggle-list-optionst d filter-items <?php if($data["param_filter"]["filter"]["period"]){ echo 'catalog-list-options-active'; } ?>">	
			<div class="uni-select" data-status="0">
			<div class="uni-select-name catalog-list-options-name f w filter-select" style="height:45px;" 
			<span class="catalog-list-options-name f w" ><?php echo $ULang->t("Срок размещения"); ?> <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="1em" height="1em" class="css-mf5jvh"><path fill="currentColor" fill-rule="evenodd" d="M2.001 6.5h1.414l1.27 1.271 7.316 7.315 7.315-7.315L20.587 6.5h1.414v1.414l-1.27 1.27-7.316 7.316-1 1h-.827l-3.942-3.942-4.374-4.374-1.27-1.27z"></path></svg></span>
			
			</div>
			<div class="uni-select-list  css-dqw345 " style="background-color: #fff;border: 0px solid #d8d8d8; box-shadow: 0 0 2px 0 rgb(0 0 0 / 15%);">
			
			
			
			
			<div class="custom-control custom-radio" onclick="toggleContainer(this)">
        <input type="radio" class="custom-control-input" name="filter[period]" <?php if($data["param_filter"]["filter"]["period"] == 1){ echo 'checked=""'; } ?> id="flPeriod1" value="1" >
        <label class="custom-control-label" for="flPeriod1"><?php echo $ULang->t("За 24 часа"); ?></label>
    </div>                        

    <div class="custom-control custom-radio" onclick="toggleContainer(this)">
        <input type="radio" class="custom-control-input" name="filter[period]" <?php if($data["param_filter"]["filter"]["period"] == 7){ echo 'checked=""'; } ?> id="flPeriod2" value="7" >
        <label class="custom-control-label" for="flPeriod2"><?php echo $ULang->t("За 7 дней"); ?></label>
    </div>

    <div class="custom-control custom-radio" onclick="toggleContainer(this)">
        <input type="radio" class="custom-control-input" name="filter[period]" <?php if(!$data["param_filter"]["filter"]["period"]){ echo 'checked=""'; } ?> id="flPeriod3" value="" >
        <label class="custom-control-label" for="flPeriod3"><?php echo $ULang->t("За все время"); ?></label>
    </div>
</div> 
	</div>
</div>

</div>
</div>
<div class="catalog-more-filter s <?php if($data["count_filters"] > 4){ ?>catalog-more-filter-action <?php } ?>" >
   
   <?php 
if($data["category"]["category_board_id"]){
    if( $getCategoryBoard["category_board_id"][ $data["category"]["category_board_id"] ]["category_board_display_price"] ){ 
    ?>
    <div class=" toggle-list-options catalog-list-options-active d catalog-list-options toggle-list-options  filter-items"  style="padding: 0px;
    ">

<div id="usdPriceBlock_f">
    <div class="filter-input">
        <div class="wb">
            <span></span>
            <input type="text" placeholder=" <?php echo $ULang->t("Цена от"); ?> $"  name="filter[price_usd][from]" value="<?php if($data["param_filter"]["filter"]["price_usd"]["from"]) echo round($data["param_filter"]["filter"]["price_usd"]["from"],2); ?>" />
        </div>
        <div class="wb">
            <span></span>
            <input type="text" placeholder=" <?php echo $ULang->t("до"); ?> $" name="filter[price_usd][to]" value="<?php if($data["param_filter"]["filter"]["price_usd"]["to"]) echo round($data["param_filter"]["filter"]["price_usd"]["to"],2); ?>" />
            <span class="text-right action-clear-filter b-currency" style="left: -14px;color: #fff;"  id="btnLARI_f">₾</span>
        </div>
    </div>
</div>

<div class="item-grid-price-now catalog-list-options toggle-list-options d filter-items" id="lariPriceBlock_f" style="display: none;">
    <div class="filter-input">
        <div class="wb">
            <span></span>
            <input type="text" placeholder=" <?php echo $ULang->t("Цена от"); ?> ₾" name="filter[price][from]" value="<?php if($data["param_filter"]["filter"]["price"]["from"]) echo round($data["param_filter"]["filter"]["price"]["from"],2); ?>" />
        </div>
        <div class="wb">
            <span></span>
            <input type="text" placeholder=" <?php echo $ULang->t("до"); ?> ₾" name="filter[price][to]" value="<?php if($data["param_filter"]["filter"]["price"]["to"]) echo round($data["param_filter"]["filter"]["price"]["to"],2); ?>" />
            <span class="text-right action-clear-filter b-currency" style="left: -14px;color: #fff;"  id="btnUSD_f">$</span>
        </div>
    </div>
</div>
<div class="col-2 text-right">
</div>
</div>



    <?php 
    } 
}else{
    ?>
    <div class=" toggle-list-options d  catalog-list-options-active" style="">

        <div class="catalog-list-options-content" >
        <div class="filter-input" >
          <div class="wb"><span><?php echo $ULang->t("от"); ?></span><input type="text" class="inputNumber" name="filter[price][from]" value="<?php if($data["param_filter"]["filter"]["price"]["from"]) echo round($data["param_filter"]["filter"]["price"]["from"],2); ?>" /></div>
          <div class="wb"><span><?php echo $ULang->t("до"); ?></span><input type="text" class="inputNumber" name="filter[price][to]" value="<?php if($data["param_filter"]["filter"]["price"]["to"]) echo round($data["param_filter"]["filter"]["price"]["to"],2); ?>" /></div>
        </div>
        </div>

    </div>    
    <?php
}

?>

   <?php echo $Filters->load_filters_catalog( $data["category"]["category_board_id"] , $data["param_filter"], "filters_catalog" ); ?>

    <?php if($data["count_filters"] > 4){ ?>
    <div class="catalog-more-filter-show d f css-rw3255" > 
		<svg width="20px" height="20px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M19 22V11" stroke="#292D32" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"></path> <path d="M19 7V2" stroke="#292D32" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"></path> <path d="M12 22V17" stroke="#292D32" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"></path> <path d="M12 13V2" stroke="#292D32" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"></path> <path d="M5 22V11" stroke="#292D32" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"></path> <path d="M5 7V2" stroke="#292D32" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"></path> <path d="M3 11H7" stroke="#292D32" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"></path> <path d="M17 11H21" stroke="#292D32" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"></path> <path d="M10 13H14" stroke="#292D32" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"></path> </g></svg>
		
		<?php echo $ULang->t("Расширенный поиск"); ?> 
		
		<i class="las la-angle-down"></i> </div>

		<div class=" toggle-list-optionst d filter-items catalog-more-filter-hide " style="padding: 0;">	
	<div class="uni-select-name catalog-list-options-name f w filter-select css-rw3255"> <span><?php echo $ULang->t("Скрыть"); ?><i class="las la-angle-up"></i>  </span>			
        </div>
</div>
			
			
    <?php } ?>
		
<div class="form-filter-submit" style="margin-top: 3px;z-index: 0;">
    <button class="btn btn-dark submit-filter-form" style="padding: 10px;z-index:0;"> <?php echo $ULang->t("Поиск"); ?> </button>
    
</div>

<div class="form-filter-submit" style="margin-top: 3px;z-index: 0;">
	<?php if($data["param_filter"]["filter"] && !$data["filter"]){ ?>
    <button class="action-clear-filter btn btn-danger" style="padding: 10px;z-index:0;"> <?php echo $ULang->t("Сбросить"); ?> </button>
<?php } ?>
</div>

<input type="hidden" name="id_c" value="<?php echo $data["category"]["category_board_id"]; ?>" >
<input type="hidden" name="filter[sort]" value="<?php echo clear($data["param_filter"]["filter"]["sort"]); ?>" >

<?php if($data["param_filter"]["search"]){ ?>
    <input type="hidden" name="search" value="<?php echo clear($data["param_filter"]["search"]); ?>" >
<?php } ?>

<script src="/templates/js/filters_show.js?=v1"></script>

<script>
    // Получить значение выбранной кнопки из Local Storage
    var selectedButton = localStorage.getItem('selectedButton');
    
    // Восстановить состояние кнопок в зависимости от выбранной кнопки
    if (selectedButton === 'lari') {
        document.addEventListener("DOMContentLoaded", function() {
            $("#usdPriceBlock_f").hide();
            $("#lariPriceBlock_f").show();
        });
    } else {
        document.addEventListener("DOMContentLoaded", function() {
            $("#usdPriceBlock_f").show();
            $("#lariPriceBlock_f").hide();
        });
    }

    // При загрузке DOM
    document.addEventListener("DOMContentLoaded", function() {
        // При нажатии на кнопку "USD"
        $("#btnUSD_f").on("click", function() {
            // Показать блок с ценой в USD
            $("#usdPriceBlock_f").show();
            // Скрыть блок с ценой в LARI
            $("#lariPriceBlock_f").hide();
            // Сохранить выбранную кнопку в Local Storage
            localStorage.setItem('selectedButton', 'usd');
        });
        
        // При нажатии на кнопку "LARI"
        $("#btnLARI_f").on("click", function() {
            // Скрыть блок с ценой в USD
            $("#usdPriceBlock_f").hide();
            // Показать блок с ценой в LARI
            $("#lariPriceBlock_f").show();
            // Сохранить выбранную кнопку в Local Storage
            localStorage.setItem('selectedButton', 'lari');
        });
    });
</script>

<style>
.css-rw3255{background: #ebebeb;}	
.f{	padding: 12px; height: 45px;}
.catalog-list-options-content {margin-bottom: 0px; margin-top: 0px;}
.b-currency{cursor: pointer; padding: 4px;background: #212529; border-radius: 20px;}
</style>

