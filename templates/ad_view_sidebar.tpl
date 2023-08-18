<div class="board-view-right" >

<div class="board-view-sidebar" >

<?php

function getCurrencyRate($fromCurrency, $toCurrency) {
    $cacheDir = "./cache";
    if (!is_dir($cacheDir)) {
        mkdir($cacheDir, 0755, true);
    }

    $cacheFile = "$cacheDir/$fromCurrency-$toCurrency.json";
    $cacheTime = 3600; // 1 час

    if (file_exists($cacheFile) && (time() - filemtime($cacheFile) < $cacheTime)) {
        $data = file_get_contents($cacheFile);
    } else {
        $url = "https://api.exchangerate-api.com/v4/latest/$fromCurrency";
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $data = curl_exec($curl);
        curl_close($curl);

        if ($data) {
            file_put_contents($cacheFile, $data);
        }
    }

    $exchangeRate = 1.0; // Default value in case of an error or missing data
    $rates = json_decode($data, true);
    if (isset($rates['rates'][$toCurrency])) {
        $exchangeRate = $rates['rates'][$toCurrency];
    }

    return $exchangeRate;
}

$eurToLari = getCurrencyRate('EUR', 'GEL');
$uahToLari = getCurrencyRate('UAH', 'GEL');
$kztToLari = getCurrencyRate('KZT', 'GEL');
$rubToLari = getCurrencyRate('RUB', 'GEL');
$amdToLari = getCurrencyRate('AMD', 'GEL');
$tryToLari = getCurrencyRate('TRY', 'GEL');
$aznToLari = getCurrencyRate('AZN', 'GEL');
$kgsToLari = getCurrencyRate('KGS', 'GEL');
$uzsToLari = getCurrencyRate('UZS', 'GEL');
?>


<?php
echo $Ads->outAdViewPrice(["data" => $data["ad"]]);
?>


<div class="board-view-price price-currency">        
<div style="float: left; margin-right: 10px;"></div> 
<i id="angle-down-icon" class="las la-angle-down"></i>
              
<div class="board-view-price-currency" style="display: none;">


<h5 class="ad-view-subtitle-bold" style="font-size: 17px;">
<div class="mt5"> <?php echo  $Main->adPrefixPrice($Main->price($data["ad"]["ads_price"],$param["data"]["ads_currency"]),$param["data"])?>
<div class="mt5"> <?php echo number_format($data["ad"]["ads_price"] / $eurToLari, 0, '.', ',') . ' €'; ?></div>
<div class="mt5"> <?php echo number_format($data["ad"]["ads_price"] / $kztToLari, 0, '.', ',') . ' ₸'; ?></div>
<div class="mt5"> <?php echo number_format($data["ad"]["ads_price"] / $uahToLari, 0, '.', ',') . ' ₴'; ?></div>
<div class="mt5"> <?php echo number_format($data["ad"]["ads_price"] / $rubToLari, 0, '.', ',') . ' ₽'; ?></div>
<div class="mt5"> <?php echo number_format($data["ad"]["ads_price"] / $amdToLari, 0, '.', ',') . ' ֏'; ?></div>
<div class="mt5"> <?php echo number_format($data["ad"]["ads_price"] / $tryToLari, 0, '.', ',') . ' ₺'; ?></div>
<div class="mt5"> <?php echo number_format($data["ad"]["ads_price"] / $aznToLari, 0, '.', ',') . ' ₼'; ?></div>
<div class="mt5"> <?php echo number_format($data["ad"]["ads_price"] / $kgsToLari, 0, '.', ',') . ' лв'; ?></div>
<div class="mt5"> <?php echo number_format($data["ad"]["ads_price"] / $uzsToLari, 0, '.', ',') . ' soʻm'; ?></div>
</h5>

<script>

  var adsBargain = <?php echo $data["ad"]["ads_bargain"]; ?>; // Если значение получено из PHP
  var adsPriceFree = <?php echo $data["ad"]["ads_price_free"]; ?>; // Если значение получено из PHP

  if (adsBargain === 1 || adsPriceFree === 1) {
    document.getElementById("angle-down-icon").style.display = "none";
  }
</script>

</div>     
</div>

<?php
echo $Ads->adSidebar($data);
?>
		
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