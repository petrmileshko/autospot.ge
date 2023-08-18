<?php 
$images = $Ads->getImages($value["ads_images"]);
$service = $Ads->adServices($value["ads_id"]);
$getShop = $Shop->getUserShop( $value["ads_id_user"] );

if($value["ads_auction"]){
  $auction_rate = (int)getOne("select count(*) as total from uni_ads_auction where ads_auction_id_ad=?", [ $value["ads_id"] ])["total"];
}
?>
<div class="col-lg-12" >
  <div class="item-list <?php echo isset($service[2]) || isset($service[3]) ? "ads-highlight" : ""; ?>" title="<?php echo $value["ads_title"]; ?>" target="_blank" >

     <div class="row no-gutters" >
       <div class="col-lg-3 col-12 col-md-3 col-sm-12" >
         
         <div class="item-list-img" >
          
           <a href="<?php echo $Ads->alias($value); ?>" title="<?php echo $value["ads_title"]; ?>" target="_blank" >

             <div class="item-labels" >
                <?php echo $Ads->outStatus($service, $value); ?>
             </div>

             <?php echo $Ads->CatalogOutAdGallery($images, $value); ?>

           </a>
           <?php echo $Ads->adActionFavorite($value, "catalog", "item-grid-favorite"); ?>

         </div>

       </div>
       <div class="col-lg-9 col-sm-12 col-md-9 col-12" >
          <div class="item-list-content" >
            <div class="row" >
               <div class="col-lg-8 col-8" >
                  <a  class="item-list-title word-break" href="<?php echo $Ads->alias($value); ?>" target="_blank" ><?php echo $value["ads_title"]; ?></a>
                  <?php if($settings["main_type_products"] == 'physical'){ ?>
                  <span class="item-list-city-name" ><i class="las la-map-marker"></i>
                     <?php 
                         echo $Ads->outAdAddressArea($value);
                     ?>
                  </span>
                  <?php } ?>                 
                  <span class="item-list-date" ><i class="las la-clock"></i> <?php echo datetime_format($value["ads_datetime_add"],false); ?></span>  
                  <?php if($value["ads_auction"]){ ?>               
                  <span class="item-list-rate" ><i class="las la-gavel"></i> <?php echo $ULang->t("Ставок:"); ?> <?php echo $auction_rate; ?></span>
                  <?php } ?>                 
               </div>
               <div class="col-lg-4 col-4 text-right" >

                  <div class="item-list-price" >
                      
					  
					   
            <!-- Блок с ценой в USD -->
			<div id="usdPriceBlock_<?php echo $value['ads_id']; ?>">
				<?php
                            echo $Ads->outPrice( ["data"=>$value,"class_price"=>"item-list-price-now","class_price_old"=>"item-list-price-old", "shop"=>$getShop, "abbreviation_million" => true] );
                ?>
			</div>
			
			<!-- Блок с ценой в LARI (скрыт по умолчанию) -->
			<div class="item-grid-price-now" id="lariPriceBlock_<?php echo $value['ads_id']; ?>" style="display: none;">
				<?php echo ($value["ads_price"] == 0 || $param["data"]["ads_currency"] == '₾') ? "". $ULang->t("Договорная") ."" : "{$Main->price($value['ads_price'], $param['data']['ads_currency'])} {$param['data']['ads_currency']}";
				?>
			</div>      
  
                  </div> 
              
               </div>
            </div>

            <?php if($value["ads_auction"]){ ?>
            <p class="item-list-duration" ><?php echo $ULang->t("Завершается через:"); ?> <span <?php echo $Ads->auctionTimeCompletion( $value["ads_auction_duration"], "pulse-time" ); ?>  ><?php echo $Ads->auctionTime( $value["ads_auction_duration"] ); ?></span></p>
            <div class="item-list-auction-rate" >
              <a href="<?php echo $Ads->alias($value); ?>" class="mt15" > <span><?php echo $ULang->t("Сделать ставку"); ?> <i class="las la-arrow-right"></i></span> </a>
            </div>
            <?php }else{ ?>
            <p class="item-list-content-text word-break" ><?php echo custom_substr($value["ads_text"], 200, "..."); ?></p>
            <?php } ?>
            
          </div>
       </div>
     </div>


  </div>
</div>

<div class="col-lg-12" >
<?php 
echo $Banners->results( ["position_name"=>"result", "current_id_cat"=>isset($data["category"]["category_board_id"]) ? $data["category"]["category_board_id"] : 0,"categories"=>isset($getCategoryBoard) ? $getCategoryBoard : [], "index"=>$key] );
?>
</div>

<script>
	$(document).ready(function () {
		// Получить значение выбранной кнопки из Local Storage
		var selectedButton = localStorage.getItem('selectedButton');
		
		// При загрузке страницы восстановить состояние кнопок
		if (selectedButton === 'lari') {
			$("#usdPriceBlock_<?php echo $value['ads_id']; ?>").hide();
			$("#lariPriceBlock_<?php echo $value['ads_id']; ?>").show();
			} else {
			$("#usdPriceBlock_<?php echo $value['ads_id']; ?>").show();
			$("#lariPriceBlock_<?php echo $value['ads_id']; ?>").hide();
		}
		
		// При нажатии на кнопку "USD"
		$("#btnUSD").on("click", function () {
			// Показать блок с ценой в USD
			$("#usdPriceBlock_<?php echo $value['ads_id']; ?>").show();
			// Скрыть блок с ценой в LARI
			$("#lariPriceBlock_<?php echo $value['ads_id']; ?>").hide();
			// Сохранить выбранную кнопку в Local Storage
			localStorage.setItem('selectedButton', 'usd');
		});
		
		// При нажатии на кнопку "LARI"
		$("#btnLARI").on("click", function () {
			// Скрыть блок с ценой в USD
			$("#usdPriceBlock_<?php echo $value['ads_id']; ?>").hide();
			// Показать блок с ценой в LARI
			$("#lariPriceBlock_<?php echo $value['ads_id']; ?>").show();
			// Сохранить выбранную кнопку в Local Storage
			localStorage.setItem('selectedButton', 'lari');
		});
		// При нажатии на кнопку "USD"
		$("#btnUSD2").on("click", function () {
			// Показать блок с ценой в USD
			$("#usdPriceBlock_<?php echo $value['ads_id']; ?>").show();
			// Скрыть блок с ценой в LARI
			$("#lariPriceBlock_<?php echo $value['ads_id']; ?>").hide();
			// Сохранить выбранную кнопку в Local Storage
			localStorage.setItem('selectedButton', 'usd');
		});
		
		// При нажатии на кнопку "LARI"
		$("#btnLARI2").on("click", function () {
			// Скрыть блок с ценой в USD
			$("#usdPriceBlock_<?php echo $value['ads_id']; ?>").hide();
			// Показать блок с ценой в LARI
			$("#lariPriceBlock_<?php echo $value['ads_id']; ?>").show();
			// Сохранить выбранную кнопку в Local Storage
			localStorage.setItem('selectedButton', 'lari');
		});
	});
</script>
