<div class="board-view-right" >

<div class="board-view-sidebar" >
    
   <!-- |.....| -->
<div class="board-view-price price-currency">		
	
<?php 
if ($data["ad"]["ads_price_usd"] != 0) {

    echo '<div style="float: left; margin-right: 10px;">';
    echo ($data["ad"]["ads_price_usd"] != 0) ? ($data["ad"]["ads_price_from"] == 1) ? $ULang->t("От") : "" : "";

    if ($data["ad"]["ads_price_usd"] > 100) {

        $formattedPrice = number_format($data["ad"]["ads_price_usd"], 0, ",", ",");
    } else {
        $formattedPrice = number_format($data["ad"]["ads_price_usd"], 2, ",", ",");
    }

    echo '<span class="box-stimulate-title" style="font-size: 22px;"> ' . $formattedPrice . " $" . '</span></br>';
    
    echo '</div>';
} else {
    
    echo $ULang->t("Договорная");
}
if ($data["ad"]["ads_price_usd"] != 0) {
echo ' <i class="las la-angle-down mt5"></i>';
}
?>
				<?php
					function getCurrencyRate($fromCurrency, $toCurrency) {
						$url = "https://api.exchangerate-api.com/v4/latest/$fromCurrency";
						$data = file_get_contents($url);
						$jsonData = json_decode($data, true);
						$exchangeRate = $jsonData['rates'][$toCurrency];
						return $exchangeRate;
					}

					$currencies = array('EUR' => '€', 'KZT' => '₸', 'UAH' => '₴', 'AMD' => '֏', 'KGS' => 'лв', 'TRY' => '₺', 'AZN' => '₼', 'UZS' => 'so\'m', 'RUB' => '₽');
					
					$dats = $data["ad"]["ads_price"];
					ob_start();
					foreach ($currencies as $currencyCode => $currencySymbol) {
						$exchangeRate = getCurrencyRate('GEL', $currencyCode);
						if ($exchangeRate !== false) {
							$convertedAmount = $exchangeRate * $dats;
							echo '<span class="box-stimulate-title">' . number_format($convertedAmount, 2, ".", ",") . " $currencySymbol</span></br>";
						}
					}
					$output = ob_get_clean();
				?>

				<div class="board-view-price-currency" style="display: none;">
					<span>
					
					<? echo $Ads->outAdViewPrice( ["data" => $data["ad"]] ) ?>
					
					<?php echo  $output; ?></span>
				</div>

				<?php echo $Ads->adSidebar( $data ); ?>
				
</div>		
<style>.board-view-price {font-size: 16px;}</style>	

<!-- |.....| -->

</div>

<div class="board-view-user" >

      <?php echo $Profile->cardUser($data); ?>

</div>

<div <?php echo $Main->modalAuth( ["attr"=>'class="complain-toggle init-complaint text-center open-modal mb20" data-id-modal="modal-complaint" data-id="'.$data["ad"]["ads_id"].'" data-action="ad"', "class"=>"mb20 complain-toggle text-center"] ); ?> > <span><?php echo $ULang->t("Пожаловаться"); ?></span> </div>

<div class="view-list-status-box" >
<?php
if($data["ad"]["ads_auction"]){
    ?>
    <div class="view-list-status-promo ad-view-promo-status-auction" >

          <h5><?php echo $ULang->t("Аукцион"); ?></h5>

          <?php echo $Ads->adAuctionSidebar( $data ); ?>

    </div>
    <?php
}

if($data["ad"]["ads_booking"]){
    ?>
    <div class="view-list-status-promo ad-view-promo-status-booking" >

        <div class="row" >
            <div class="col-lg-3 col-3" >
               
              <img src="/media/others/86155-play-video-rewind-repeat.svg" style=" width: 75px; "/>

            </div>
            <div class="col-lg-9 col-9" >
              
              <h5><?php echo $ULang->t("Онлайн-аренда"); ?></h5>

              <?php if($data["ad"]["category_board_booking_variant"] == 0){ ?>
           
              <?php }else{ ?>
                <p><?php echo $ULang->t("Можно взять в аренду онлайн"); ?></p>
              <?php } ?>

            </div>
        </div>

    </div>
    <?php
}

if($data["ad"]["ads_online_view"]){
    ?>
    <div class="view-list-status-promo ad-view-promo-status-online" >

        <div class="row" >
            <div class="col-lg-3 col-3" >
               
               <img src="/media/others/86155-play-video-rewind-repeat.svg" style="width: 75px;"/>

            </div>
            <div class="col-lg-9 col-9" >
              
              <h5><?php echo $ULang->t("Онлайн-показ"); ?></h5>

         

              <span class="view-list-status-promo-button open-modal" data-id-modal="modal-ad-online-view" ><?php echo $ULang->t("Подробнее"); ?> <i class="las la-arrow-right"></i></span>

            </div>
        </div>

    </div>
    <?php
}  

?>
</div>

<?php if( $data["ad"]["ads_status"] != 0 ){ ?>

    <?php if($_SESSION["profile"]["id"] == $data["ad"]["ads_id_user"]){ ?>
    <div class="board-view-sidebar-box-stimulate" >
     
     <p class="box-stimulate-title" ><?php echo $ULang->t("Кол-во показов"); ?></p>

     <p class="box-stimulate-count" ><?php echo $Ads->getDisplayView($data["ad"]["ads_id"], date("Y-m-d")); ?></p>
     
        <?php if( !$data["order_service_ids"] && $data["ad"]["ads_status"] == 1 && strtotime($data["ad"]["ads_period_publication"]) > time() ){ ?>
            <span class="btn-custom-mini btn-color-blue mt10 open-modal" data-id-modal="modal-top-views" ><?php echo $ULang->t("Как повысить?"); ?></span> 
        <?php } ?>

    </div>
    <?php } ?>

<?php } ?>

<?php
    echo $Banners->out( ["position_name"=>"ad_view_sidebar", "current_id_cat"=>$data["ad"]["category_board_id"], "categories"=>$getCategoryBoard] ); 
?>

</div>


