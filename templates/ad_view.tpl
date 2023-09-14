<?php
 include $config["template_path"] . "/lib/tfpdf.php";
 include $config["template_path"] . "/lib/qrlib.php";

          $Filters = new Filters();
          $dataAd =  $Filters->outProductPropArray($data["ad"]["ads_id"]);
$dataPdf =[
	"id" => $data["ad"]["ads_id"],
	"qr" => $config["template_path"] . '/images/tmp.png',
	"car" => [
	"mark" => $dataAd[1]['value'],
	"model" => $dataAd[2]['value'],
	"volume" => $dataAd[5]['value'],
	"year" => $dataAd[3]['value'],
	"type" => $ULang->t($dataAd[6]['value']),
	"mileage" => $dataAd[13]['value'],
	"price" => $data["ad"]['ads_price_usd']
	],
	"author" => [
		"name" => ( $data["ad"]['clients_name'] ) ? $data["ad"]['clients_name'].' '.$data["ad"]['clients_surname'] : '',
		"phone" => ( $data["ad"]['clients_phone'] ) ? '+'.preg_replace('/ /','-',$data["ad"]['clients_phone']) : ''
	]
];

if( $_POST['ID'] === $data["ad"]["ads_id"]): 

QRcode::png($_SERVER['HTTP_REFERER'],$dataPdf['qr'], 'H', 6, 2);
	$pdf = new tFPDF('L','pt',[446.25,631.5]);
	$pdf->SetAuthor($dataPdf['author']['name']); $pdf->AddFont('InterLightBetta','','Inter-LightBETA.ttf',true);$pdf->AddFont('InterMedium','','Inter-Medium.ttf',true);$pdf->AddFont('InterSemibold','B','Inter-SemiBold.ttf',true);
	$pdf->SetTitle($dataPdf['car']['mark'] .' '.$dataPdf['car']['model'] ); $pdf->SetLineWidth(0,75);$pdf->SetAutoPageBreak(1,0);
	$pdf->AddPage(); $pdf->SetDisplayMode('real');
	$pdf->Image($config["template_path"] . '/images/map-pin.png',42.75,36.97,18.64); $pdf->Image($config["template_path"] . '/images/logo.png',69.75,39,108.75);
	$pdf->SetFont('InterSemibold','B',40); $pdf->SetXY(42.75,88.5); $pdf->Write( 40, strtoupper($dataPdf['car']['mark'] .' '.$dataPdf['car']['model']));
	$pdf->SetFont('InterLightBetta','',42); $pdf->SetXY(45, 138); $pdf->Write( 42, $dataPdf['car']['year']);$pdf->cell(12.75); $pdf->SetFontSize(30); $pdf->Write( 42, $dataPdf['car']['volume'].'L'); $pdf->cell(12.75); $pdf->Write( 42, $dataPdf['car']['mileage']); $pdf->SetTextColor(131,128,128); $pdf->cell(4.5); $pdf->SetFontSize(24); $pdf->Write( 42,'km');
	$pdf->SetFillColor(0,0,0); $pdf->SetTextColor(255,255,255); $pdf->SetFont('InterSemibold','B',36); $pdf->Rect(36,240,205.5,99.75,'F'); $pdf->SetXY(36.75, 244); $pdf->cell(192.75, 61.5,$dataPdf['car']['price'].' $',0,1,'R',1); 
	$pdf->SetFont('InterLightBetta','',27); $pdf->SetXY(36.75, 297); $pdf->cell(192.75, 36.75,($dataPdf['car']['price']*2.63).' ₾',0,1,'R',1);
	$pdf->SetDrawColor(218); $pdf->Line(15.75,198,615.75,198);
	$pdf->SetXY(262.5, 253.5); $pdf->SetFillColor(255,255,255);$pdf->SetTextColor(0,0,0);$pdf->SetFont('InterMedium','',23);$pdf->cell(210, 36.75,$dataPdf['author']['phone'],0,1,'R',1); $pdf->SetFont('InterLightBetta','',23);$pdf->SetXY(262.5, 290.25);$pdf->cell(210, 36.75,$dataPdf['author']['name'],0,1,'R',1);
	$pdf->SetDrawColor(232); $pdf->Line(484.5,236.25,484.5, 345); $pdf->Image($dataPdf['qr'],488, 234, 114);
	$pdf->SetXY(500, 219); $pdf->SetFontSize(15);$pdf->cell(90,20,'more detailed',0,1,"C");$pdf->SetFillColor(0,0,0);$pdf->SetTextColor(255,255,255);$pdf->SetXY(500, 348);$pdf->cell(90,20,'SCAN ME',0,1,"C",1);
	$pdf->SetXY(0, 425); $pdf->SetFillColor(255,255,255);$pdf->SetTextColor(0,0,0);$pdf->SetFontSize(10.5);$pdf->cell(630, 11,'WWW.AUTOSPOT.GE - SELL, BUY, RENT CARS IN GEORGIA',0,1,"C",1);
	$pdf->Output('auto-'.$dataPdf['car']['mark'] .'-'.$dataPdf['car']['model'].'.pdf','D');
endif;?>

<!doctype html>
<html lang="<?php echo getLang(); ?>">
  <head>
    <meta charset="utf-8">
    	<meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="description" content="<?php echo $Seo->out(["page" => "ad", "field" => "meta_desc"], $data); ?>">

    <meta property="og:image" content="<?php echo Exists($config["media"]["big_image_ads"],$data["ad"]["ads_images"][0],$config["media"]["no_image"]); ?>">
    <meta property="og:title" content="<?php echo $Seo->out(["page" => "ad", "field" => "meta_title"], $data); ?>">
    <meta property="og:description" content="<?php echo $Seo->out(["page" => "ad", "field" => "meta_desc"], $data); ?>">

    <title><?php echo $Seo->out(["page" => "ad", "field" => "meta_title"], $data); ?></title>

    <?php include $config["template_path"] . "/head.tpl"; ?>
  </head>

  <body data-prefix="<?php echo $config["urlPrefix"]; ?>" data-header-sticky="true" data-id-ad="<?php echo $data["ad"]["ads_id"]; ?>" data-id-cat="<?php echo $data["ad"]["category_board_id"]; ?>" data-template="<?php echo $config["template_folder"]; ?>" >
	<form action="" method="post" id="pdf">
		<input type="hidden" name="ID" value="<?=$data['ad']['ads_id']?>" >
	</form>
    <?php include $config["template_path"] . "/header.tpl"; ?>
     
    <div class="container" >

       <?php echo $Banners->out( ["position_name"=>"ad_view_top", "current_id_cat"=>$data["ad"]["category_board_id"], "categories"=>$getCategoryBoard] ); ?>

<?php

$conn = new mysqli($config["db"]["host"], $config["db"]["user"], $config["db"]["pass"], $config["db"]["database"]);

$ip = $_SERVER['REMOTE_ADDR'];
$ads_id = $data["ad"]["ads_id"];


$sql_check = "SELECT * FROM uni_views_users WHERE ads_id = '$ads_id' AND ip = '$ip'";
$result_check = $conn->query($sql_check);

if ($result_check->num_rows == 0) {
    $ads_geo = $data["ad"]["ads_city_id"];
	$ads_url = $current_url = $_SERVER['REQUEST_URI'];
    $ads_title = $data["ad"]["ads_title"];
    $ads_count = count($data["ad"]["ads_images"]);
    $ads_price = $Ads->outAdViewPrice(["data" => $data["ad"]]);
    $ads_img = Exists($config["media"]["big_image_ads"], $data["ad"]["ads_images"][0], $config["media"]["no_image"]);
    $sql_insert = "INSERT INTO uni_views_users (ip, ads_id, ads_img, ads_title, ads_price, ads_geo, ads_url, ads_count) VALUES ('$ip', '$ads_id', '$ads_img', '$ads_title', '$ads_price', '$ads_geo', '$ads_url', '$ads_count')";
    

    $conn->query($sql_insert);
}

?>


<?php

$conn = new mysqli($config["db"]["host"], $config["db"]["user"], $config["db"]["pass"], $config["db"]["database"]);

// Получение IP-адреса пользователя
$ip = $_SERVER['REMOTE_ADDR'];
$ads_id = $data["ad"]["ads_id"];


$sql_check = "SELECT * FROM uni_views_popular WHERE ads_id = '$ads_id' AND ip = '$ip'";
$result_check = $conn->query($sql_check);

if ($result_check->num_rows == 0) {
    $ads_geo = $data["ad"]["ads_city_id"];
	$ads_url = $current_url = $_SERVER['REQUEST_URI'];
    $ads_title = $data["ad"]["ads_title"];
    $ads_count = count($data["ad"]["ads_images"]);
    $ads_price = $Ads->outAdViewPrice(["data" => $data["ad"]]);
    $ads_img = Exists($config["media"]["big_image_ads"], $data["ad"]["ads_images"][0], $config["media"]["no_image"]);
    $sql_insert = "INSERT INTO uni_views_popular (ip, ads_id, ads_img, ads_title, ads_price, ads_geo, ads_url, ads_count) VALUES ('$ip', '$ads_id', '$ads_img', '$ads_title', '$ads_price', '$ads_geo', '$ads_url', '$ads_count')";
    

    $conn->query($sql_insert);
}

?>



       <div class="mt15" ></div>

       <?php if( $data["activity_ad"] ){ ?>
    
       <div class="board-view-container" >

          <div class="d-none d-xl-block" >

          <nav aria-label="breadcrumb" >
     
              <ol class="breadcrumb" itemscope="" itemtype="http://schema.org/BreadcrumbList">

                <li class="breadcrumb-item" itemprop="itemListElement" itemscope="" itemtype="http://schema.org/ListItem">
                  <a itemprop="item" href="<?php echo _link(); ?>">
                  <span itemprop="name"><?php echo $ULang->t("Главная"); ?></span></a>
                  <meta itemprop="position" content="1">
                </li>

                <?php
                  echo $data["breadcrumb"];
                ?>                 
              </ol>

          </nav>

          <?php if( $data["ad"]["ads_status"] == 4 || $data["ad"]["ads_status"] == 5 || $data["ad"]["ads_status"] == 2 ){ ?>
            
            <div class="view-list-status mb10" >
              <span class="ad-status-label-<?php echo $data["ad"]["ads_status"]; ?>" ><?php echo $Ads->status( $data["ad"]["ads_status"] ); ?></span>
            </div>

          <?php } ?>

          <div class="row" >
             <div class="col-lg-10 col-12" >
                 <h1 class="h1title word-break" ><?php echo $data["ad"]["ads_title"]; ?></h1>
             </div>
             <div class="col-lg-2 col-12 text-right" >

						 <div class="d-none d-lg-block d-lg-block--pdf" >
                 <button class="print-pdf-btn" type="submit" form="pdf">pdf</button>
                <span <?php echo $Main->modalAuth( ["attr"=>'class="ad-view-title-favorite toggle-favorite-ad" data-id="'.$data["ad"]["ads_id"].'"', "class"=>"ad-view-title-favorite"] ); ?> >

                    <div class="ad-view-title-favorite-icon favorite-ad-icon-box" >
                    <?php if( !isset($_SESSION['profile']["favorite"][$data["ad"]["ads_id"]]) ){ ?>
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M6.026 4.133C4.398 4.578 3 6.147 3 8.537c0 3.51 2.228 6.371 4.648 8.432A23.633 23.633 0 0012 19.885a23.63 23.63 0 004.352-2.916C18.772 14.909 21 12.046 21 8.537c0-2.39-1.398-3.959-3.026-4.404-1.594-.436-3.657.148-5.11 2.642a1 1 0 01-1.728 0C9.683 4.281 7.62 3.697 6.026 4.133zM12 21l-.416.91-.003-.002-.008-.004-.027-.012a15.504 15.504 0 01-.433-.214 25.638 25.638 0 01-4.762-3.187C3.773 16.297 1 12.927 1 8.538 1 5.297 2.952 2.9 5.499 2.204c2.208-.604 4.677.114 6.501 2.32 1.824-2.206 4.293-2.924 6.501-2.32C21.048 2.9 23 5.297 23 8.537c0 4.39-2.772 7.758-5.352 9.955a25.642 25.642 0 01-4.762 3.186 15.504 15.504 0 01-.432.214l-.027.012-.008.004-.003.001L12 21zm0 0l.416.91c-.264.12-.568.12-.832 0L12 21z" fill="currentColor"></path></svg>
                    <?php }else{ ?>
                        <svg width="24" height="24" fill="none" xmlns="http://www.w3.org/2000/svg" class="favorite-icon-active" ><path d="M12.39 20.87a.696.696 0 01-.78 0C9.764 19.637 2 14.15 2 8.973c0-6.68 7.85-7.75 10-3.25 2.15-4.5 10-3.43 10 3.25 0 5.178-7.764 10.664-9.61 11.895z" fill="currentColor"></path></svg>
                    <?php } ?>
                    </div>
                    <?php echo $ULang->t("В избранное"); ?>
                </span>
                </div>
                
             </div>
          </div>

          <div class="d-block d-lg-none" >
            <?php echo $Ads->outAdViewPrice( ["data" => $data["ad"]] ); ?>
          </div>

          <div class="ad-view-title-info" >

             <span class="ad-view-title-info-label" ><?php echo $ULang->t("Размещено:"); ?> <?php echo datetime_format($data["ad"]["ads_datetime_add"]); ?></span>

             <span class="ad-view-title-info-label-link open-modal" data-id-modal="modal-ad-share" ><i class="las la-share"></i> <?php echo $ULang->t("Поделиться"); ?></span>

          </div>
          
          <div class="mt30" ></div>

          </div>

          <div class="row" >

              <div class="col-lg-9" >

                   <div class="ads-view-photo" >

                      <div class="ads-view-photo-nav-left" ><i class="las la-arrow-left"></i></div>
                      <div class="ads-view-photo-nav-right" ><i class="las la-arrow-right"></i></div>
                    
                      <?php
                        if($data["ad"]["ads_images"]){

                            ?>
                            <div class="ads-view-photo-label-count" ><span><?php echo count($data["ad"]["ads_images"]); ?> <?php echo ending(count($data["ad"]["ads_images"]), $ULang->t("фото"),$ULang->t("фото"),$ULang->t("фотографий")); ?></span></div>
                            <?php

                            if(count($data["ad"]["ads_images"]) > 1){ 
                                ?>
                                <div class="ads-view-photo-slider lightgallery" >
                                <?php
                                foreach ($data["ad"]["ads_images"] as $key => $value) {
                                   ?>

                                   <a class="ads-view-photo-slider-item"  data-thumb="<?php echo Exists($config["media"]["big_image_ads"],$value,$config["media"]["no_image"]); ?>" href="<?php echo Exists($config["media"]["big_image_ads"],$value,$config["media"]["no_image"]); ?>" >
                                     <div><img src="<?php echo Exists($config["media"]["big_image_ads"],$value,$config["media"]["no_image"]); ?>"></div>
                                   </a>

                                   <?php
                                }
                                if($data["ad"]["ads_video"]){
                                  ?>

                                  <a class="ads-view-photo-slider-item"  data-thumb="<?php echo $settings["path_tpl_image"] . '/youtube.png'; ?>" href="<?php echo clear($data["ad"]["ads_video"]); ?>"  data-type="video" >
                                    <div><iframe src="<?php echo clear($data["ad"]["ads_video"]); ?>" frameborder="0" allowfullscreen></iframe></div>
                                  </a>

                                  <?php
                                }
                                ?>
                                </div>
                                <?php
                            }else{

                                if(!$data["ad"]["ads_video"]){
                                  ?>
                                  <div class="lightgallery ads-view-photo-one ads-view-photo-bg-gray" >

                                   <a data-thumb="<?php echo Exists($config["media"]["big_image_ads"],$data["ad"]["ads_images"][0],$config["media"]["no_image"]); ?>" href="<?php echo Exists($config["media"]["big_image_ads"],$data["ad"]["ads_images"][0],$config["media"]["no_image"]); ?>" >
                                       <img src="<?php echo Exists($config["media"]["big_image_ads"],$data["ad"]["ads_images"][0],$config["media"]["no_image"]); ?>">
                                   </a>

                                  </div>
                                  <?php   
                                }else{
                                  ?>
                                  <div class="ads-view-photo-slider lightgallery" >

                                    <a data-thumb="<?php echo Exists($config["media"]["big_image_ads"],$data["ad"]["ads_images"][0],$config["media"]["no_image"]); ?>" class="ads-view-photo-slider-item" href="<?php echo Exists($config["media"]["big_image_ads"],$data["ad"]["ads_images"][0],$config["media"]["no_image"]); ?>" >
                                        <div><img src="<?php echo Exists($config["media"]["big_image_ads"],$data["ad"]["ads_images"][0],$config["media"]["no_image"]); ?>"></div>
                                    </a>

                                    <a class="ads-view-photo-slider-item" data-thumb="<?php echo $settings["path_tpl_image"] . '/youtube.png'; ?>" href="<?php echo clear($data["ad"]["ads_video"]); ?>"  data-type="video" >
                                        <div><iframe src="<?php echo clear($data["ad"]["ads_video"]); ?>" frameborder="0" allowfullscreen></iframe></div>
                                    </a>

                                  </div>
                                  <?php
                                }

                            }

                        }elseif($data["ad"]["ads_video"]){
                           ?>
                              <a href="<?php echo clear($data["ad"]["ads_video"]); ?>"  data-type="video" >
                              <iframe src="<?php echo clear($data["ad"]["ads_video"]); ?>" frameborder="0" allowfullscreen></iframe></a>                          
                           <?php
                        }
                      ?>

                   </div>

                   <div class="ads-view-photo-mini-gallery d-none d-lg-block" ></div>

                   <div class="d-block d-lg-none" >

                    <div class="view-list-status-box" >
                    
                    </div>

                      <?php if( $data["ad"]["ads_status"] == 4 || $data["ad"]["ads_status"] == 5 || $data["ad"]["ads_status"] == 2 ){ ?>
                        
                        <div class="view-list-status mb10" >
                          <span class="ad-status-label-<?php echo $data["ad"]["ads_status"]; ?>" ><?php echo $Ads->status( $data["ad"]["ads_status"] ); ?></span>
                        </div>

                      <?php } ?>

                      <h1 class="h1title word-break" ><?php echo $data["ad"]["ads_title"]; ?></h1>

                      <?php echo $Ads->outAdViewPrice( ["data" => $data["ad"]] ); ?>

                      <?php echo $Ads->adSidebar($data); ?>
                      
                     <div class="board-view-user-mobile mt20" >

                          <?php echo $Profile->cardUserAd($data); ?>

                     </div>

                   </div>


                   <div class="mt30" ></div>                

                   <?php if($data["ad"]["ads_text"]){ ?>

                     <h5 class="ad-view-subtitle-bold" ><?php echo $ULang->t("Описание"); ?></h5>

                     <div class="word-break mb20" ><?php echo nl2br($data["ad"]["ads_text"]); ?> </div>

                   <?php } ?>

                   <?php if($data["properties"]){ ?>

                     <h5 class="ad-view-subtitle-bold" ><?php echo $ULang->t("Характеристики"); ?></h5>

                     <div class="list-properties mb20" >
                       <div class="list-properties-display" >
                         <?php echo $data["properties"]; ?>
                       </div>
                     </div>

                   <?php } ?>
				   
				   
<div class="d-block d-lg-none">			   
<?php

                    if($data["ad"]["ads_auction"]){
                        ?>
                        <div class="view-list-status-promo ad-view-promo-status-auction mt30" >

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
                                   
                                   <img src="/media/others/86155-play-video-rewind-repeat.svg" style="width: 60px;"/>

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
                                   
                                  <img src="/media/others/86155-play-video-rewind-repeat.svg" style="width: 60px;"/>

                                </div>
                                <div class="col-lg-9 col-9" >
                                  
                                  <h5><?php echo $ULang->t("Онлайн-показ"); ?></h5>

                                  <span style="margin-top: -8px;" class="view-list-status-promo-button open-modal" data-id-modal="modal-ad-online-view" ><?php echo $ULang->t("Подробнее"); ?> <i class="las la-arrow-right"></i></span>

                                </div>
                            </div>

                        </div>
                        <?php
                    }                            
                    ?>				   
				   
		   


 	</div>	 
	<?php if($settings["main_type_products"] == 'physical'){ ?>

                   <h5 class="ad-view-subtitle-bold" ><?php echo $ULang->t("Местоположение"); ?></h5>

                  <?php echo $ULang->t( $address[] = $data["ad"]["region_name"], [ "table" => "geo", "field" => "geo_name" ] ) ?>, <?php echo $ULang->t( $address[] = $data["ad"]["city_name"], [ "table" => "geo", "field" => "geo_name" ] ) ?>
                   
                   <?php if($data["metro"]){ ?>
                    <div class="ads-view-metro" >
                       <?php
                          foreach ($data["metro"] as $key => $value) {

                             ?>

                              <div  title="<?php echo $value["metro_name"]; ?>" >
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" width="17" height="17"><path fill="<?php echo $value["metro_color"]; ?>" fill-rule="evenodd" d="M11.154 4L8 9.53 4.845 4 1.1 13.466H0v1.428h5.657v-1.428H4.81l.824-2.36L8 15l2.365-3.893.824 2.36h-.85v1.427H16v-1.428h-1.1z"></path></svg>
                                <?php echo $value["station"]; ?>
                              </div>

                             <?php
                             
                          }
                       ?>
                    </div>
                   <?php } ?>

                   <?php if($data["ad"]["ads_latitude"] && $data["ad"]["ads_longitude"]){ ?>

                   <div class="ads-view-map">
                       <div id="mapAd" ></div>
                   </div>

                   <?php } ?>

                   <?php } ?>

                   <div class="d-block d-lg-none" >
                       
                        <?php if( $data["ad"]["ads_status"] != 0 ){ ?>

                            <?php if($_SESSION["profile"]["id"] == $data["ad"]["ads_id_user"]){ ?>
                            <div class="board-view-sidebar-box-stimulate" >
                             
                             <p class="box-stimulate-title" ><?php echo $ULang->t("Кол-во показов"); ?></p>

                             <p class="box-stimulate-count" ><?php echo $Ads->getDisplayView($data["ad"]["ads_id"], date("Y-m-d")); ?></p>
                             
                                <?php if( !$data["order_service_ids"] && $data["ad"]["ads_status"] == 1 && strtotime($data["ad"]["ads_period_publication"]) > time() ){ ?>
                                    <span class="btn-custom btn-color-blue mt10 open-modal" data-id-modal="modal-top-views" ><?php echo $ULang->t("Как повысить?"); ?></span> 
                                <?php } ?>

                            </div>
                            <?php } ?>

                        <?php } ?>

                        <div class="text-center mt15" >

                        <hr>

                        <span class="ad-view-title-info-label" ><?php echo $ULang->t("Размещено:"); ?> <?php echo datetime_format($data["ad"]["ads_datetime_add"]); ?></span>

                        <div data-id-modal="modal-complaint" class="complain-toggle open-modal text-center mt10 init-complaint" data-id="<?php echo $data["ad"]["ads_id"]; ?>" data-action="ad" > <span><?php echo $ULang->t("Пожаловаться"); ?></span> </div>

                        </div>

                   </div>

                   <?php if($data["ad"]["ads_status"] && $settings["ads_comments"] && $data["ad"]["clients_comments"]){ ?>
                   
                   <noindex>
                    
                   <h4 class="mb30 mt30" > <strong> <?php echo $ULang->t("Комментарии"); ?> </strong> </h4>
                   
                   <?php 

                   if($_SESSION['profile']['id']){ 
                     
                     if( !$data["locked"] ){
                        ?>
                        <div class="module-comments-form-otvet mb25" >
                          <form class="module-comments-form" >
                          <textarea name="text" placeholder="<?php echo $ULang->t("Ваш комментарий ..."); ?>" ></textarea>
                            <button class="module-comments-form-send" ><i class="las la-arrow-right"></i></button>
                            <input type="hidden" name="id_ad" value="<?php echo $data["ad"]["ads_id"]; ?>" >
                          </form>
                        </div>
                        <?php
                     }else{
                        ?>
                        <div class="alert alert-primary mb25" role="alert">
                          <?php echo $ULang->t("Вы не можете оставить комментарий к данному объявлению"); ?>
                        </div>                        
                        <?php
                     } 
                     
                     }else{ ?>
                     <div class="alert alert-primary mb25" role="alert">
                      <?php echo $ULang->t("Добавлять комментарии могут только авторизованные пользователи"); ?>
                     </div>                    
                   <?php } ?>


                   <div class="module-comments" >
                      
                      <?php
                      echo $Ads->outComments(0, $Ads->getComments($data["ad"]["ads_id"]));
                      ?>
                      
                   </div>  
                   
                   </noindex>

                   <?php } ?>               

              </div>
              <div class="col-lg-3 d-none d-lg-block" >

                 <?php include $config["template_path"] . "/ad_view_sidebar.tpl"; ?>

              </div>
          </div>

       </div>

    <?php }else{

       if($data["ad"]["ads_status"] == 8 || $data["ad"]["clients_status"] == 3){
          ?>
          <div class="row" >
            <div class="col-lg-12" >
               <div class="ads-status-block mt100" >
                 <div class="status-block-icon" >
                    <div><i class="las la-lock"></i></div>
                 </div>
                 <h5><strong><?php echo $ULang->t("Объявление удалено"); ?></strong></h5>
               </div>
            </div>
          </div>
          <?php        
        }elseif( $data["ad"]["ads_status"] == 3 || $data["ad"]["clients_status"] == 2 ){
          ?>
          <div class="row" >
            <div class="col-lg-12" >
               <div class="ads-status-block mt100" >
                 <div class="status-block-icon" >
                    <div><i class="las la-lock"></i></div>
                 </div>
                 <h5><strong><?php echo $ULang->t("Объявление заблокировано"); ?></strong></h5>
               </div>
            </div>
          </div>
          <?php        
        }else{
            ?>
            <div class="row" >
              <div class="col-lg-12" >
                 <div class="ads-status-block mt100" >
                   <div class="status-block-icon" >
                      <div><i class="las la-lock"></i></div>
                   </div>
                   <h5><strong><?php echo $ULang->t("Объявление неактивно"); ?></strong></h5>
                 </div>
              </div>
            </div>
            <?php
        }


    } ?>
	

    </div>

    <div class="mt30" ></div>

    <div class="ajax-container-similar" >
        <div class="container" >
           <h1 class="h1title mb15 mt35" ><?php if($data["tariff"]['services']['hiding_competitors_ads']){ echo $ULang->t("Другие объявления продавца"); }else{ echo $ULang->t("Объявления из той же категории"); } ?></h1>
           <div class="row no-gutters gutters10 ajax-container-similar-content" ></div>            
        </div>
    </div>

    <div class="container" >

    <?php echo $Banners->out( ["position_name"=>"ad_view_bottom", "current_id_cat"=>$data["ad"]["category_board_id"], "categories"=>$getCategoryBoard] ); ?>

    </div>
	
	<div class="col-lg-9 mt20 mb20 d-block d-lg-none">
        <nav aria-label="breadcrumb" >
              <ol class="breadcrumb" itemscope="" itemtype="http://schema.org/BreadcrumbList">
                <li class="breadcrumb-item" itemprop="itemListElement" itemscope="" itemtype="http://schema.org/ListItem">
                  <a itemprop="item" href="<?php echo _link(); ?>">
                  <span itemprop="name"><?php echo $ULang->t("Главная"); ?></span></a>
                  <meta itemprop="position" content="1">
                </li>
                <?php
                  echo $data["breadcrumb"];
                ?>                 
              </ol>
        </nav>
    </div>

    <?php include $config["template_path"] . "/footer.tpl"; ?>

    <?php echo $Geo->vendorMap($data["ad"]["ads_latitude"],$data["ad"]["ads_longitude"]); ?>
    
    <noindex>
    
    <div class="modal-custom-bg bg-click-close" style="display: none;" id="modal-view-phone" >
        <div class="modal-custom animation-modal" style="max-width: 400px;" >

          <span class="modal-custom-close" ><i class="las la-times"></i></span>

               <div class="user-avatar" >

                   <div class="user-avatar-img" >
                      <img src="<?php echo $Profile->userAvatar($data["ad"]); ?>" />
                   </div>  
                   <h4> <?php echo $Profile->name($data["ad"]); ?> </h4>  
                   <p>На <?php echo $settings["site_name"]; ?> с <?php echo date("d.m.Y", strtotime($data["ad"]["clients_datetime_add"])); ?></p>  

                   <div class="board-view-stars">
                       
                     <?php echo $data["ratings"]; ?>
                     <div class="clr"></div>   

                   </div>

               </div>

               <hr>
               
               <div class="modal-view-phone-display" ></div>

               <p class="mt10 text-center" ><?php echo $ULang->t("Скажите, что Вы нашли объявление на"); ?> <?php echo $settings["site_name"]; ?></p>

        </div>
    </div>

    <div class="modal-custom-bg bg-click-close"  id="modal-order-service" style="display: none;"  >
        <div class="modal-custom animation-modal" style="max-width: 1128px;" >

          <span class="modal-custom-close" ><i class="las la-times"></i></span>

          <h4> <strong><div class="text-center"><?php echo $ULang->t("Подключите услуги, чтобы продать свой товар быстрее"); ?></div></strong> </h4>

          <div class="mt40" ></div>
          
          <form method="post" class="form-ads-services" >

          <div class="row no-gutters gutters10" style="justify-content: center; ">

              <?php echo $list_services; ?>

          </div>
		  
          <input type="hidden" name="id_s" value="3" >
          <input type="hidden" name="id_ad" value="<?php echo $data["ad"]["ads_id"]; ?>" >
          
         
             <div class="row text-center">
               
			   <div class="col-lg-5 text-right" >
			   <button class="btn-custom btn-color-green mt15" ><?php echo $ULang->t("Подключить"); ?></button>
               </div>
			   <div class="col-lg-7 text-left" >
			   <div class="css-4che2j header-button-menu btn-custom mt15" > <a href="" style="color: black; text-decoration: none;"><?php echo $ULang->t("Опубликовать без рекламы"); ?></a></div>
               </div>   
             
			 
			    
          </div>

          </form>


        </div>
    </div>


    <div class="modal-custom-bg bg-click-close" style="display: none;" id="modal-remove-publication" >
        <div class="modal-custom animation-modal" style="max-width: 450px" >

          <span class="modal-custom-close" ><i class="las la-times"></i></span>
          
          <div class="modal-confirm-content" >
              <h4><?php echo $ULang->t("Снять с публикации"); ?></h4>   
              <p><?php echo $ULang->t("Выберите причину"); ?></p>         
          </div>

          <div class="mt30" ></div>

          <div class="modal-custom-button-list" >
            <button class="button-style-custom schema-color-button color-blue ads-status-sell" data-id="<?php echo $data["ad"]["ads_id"]; ?>" ><?php echo $ULang->t("Я продал на"); ?> <?php echo $settings["site_name"]; ?></button>
            <button class="button-style-custom color-light ads-remove-publication mt5" data-id="<?php echo $data["ad"]["ads_id"]; ?>" ><?php echo $ULang->t("Другая причина"); ?></button>
          </div>

        </div>
    </div>

    <div class="modal-custom-bg bg-click-close" style="display: none;" id="modal-delete-ads" >
        <div class="modal-custom animation-modal" style="max-width: 400px" >

          <span class="modal-custom-close" ><i class="las la-times"></i></span>
          
          <div class="modal-confirm-content" >
              <h4><?php echo $ULang->t("Вы действительно хотите удалить объявление?"); ?></h4> 
              <p><?php echo $ULang->t("Ваше объявление будет безвозвратно удалено"); ?></p>           
          </div>

          <div class="modal-custom-button" >
             <div>
               <button class="button-style-custom btn-color-danger ads-delete" data-id="<?php echo $data["ad"]["ads_id"]; ?>" ><?php echo $ULang->t("Удалить"); ?></button>
             </div> 
             <div>
               <button class="button-style-custom color-light button-click-close" ><?php echo $ULang->t("Отменить"); ?></button>
             </div>                                       
          </div>

        </div>
    </div>

    <div class="modal-custom-bg bg-click-close"  id="modal-top-views" style="display: none;" >
        <div class="modal-custom animation-modal no-padding" style="max-width: 500px;" >

          <span class="modal-custom-close" ><i class="las la-times"></i></span>

          <div class="modal-top-views-content" >

             <div class="modal-top-views-content-title" >
               <h4> <strong><?php echo $ULang->t("Поднятие объявления в ленте"); ?></strong> </h4>
               <p><?php echo $ULang->t("Воспользуйтесь услугой - поднятие объявление в ленте и ваше объявление будет на много чаще показываться в каталоге чем у остальных!"); ?></p>
             </div>
            
            <div class="modal-custom-button" >
               <div>
                 <button class="button-style-custom schema-color-button color-green mb25 top-views-up" data-id="<?php echo $data["ad"]["ads_id"]; ?>" ><?php echo $Ads->buttonViewsUp(); ?></button>
               </div> 
               <div>
                 <button class="button-style-custom color-light mb25 open-modal" data-id-modal="modal-order-service" ><?php echo $ULang->t("Выбрать другую услугу"); ?></button>
               </div>                                       
            </div>

          </div>


        </div>
    </div>

    <div class="modal-custom-bg"  id="modal-auction" style="display: none;" >
        <div class="modal-custom animation-modal" style="max-width: 400px;" >

          <span class="modal-custom-close" ><i class="las la-times"></i></span>

          <div class="modal-auction-content" >

            <h4> <strong><?php echo $ULang->t("Укажите ставку"); ?></strong> </h4>

            <input type="number" name="rate" class="form-control" >

            <p><i class="las la-exclamation-circle"></i> <?php echo $ULang->t("Сумма не должна быть меньше"); ?> <?php echo $Main->price($data["ad"]["ads_price"]); ?></p>
            
            <div class="modal-custom-button mt25" >
               <div>
                 <button class="button-style-custom schema-color-button color-green mb5 action-auction-rate" data-id="<?php echo $data["ad"]["ads_id"]; ?>" ><?php echo $ULang->t("Сделать ставку"); ?></button>
               </div> 
               <div>
                 <button class="button-style-custom color-light mb5 button-click-close" ><?php echo $ULang->t("Закрыть"); ?></button>
               </div>                                       
            </div>

          </div>


        </div>
    </div>

    <div class="modal-custom-bg"  id="modal-auction-success" style="display: none;" >
        <div class="modal-custom animation-modal" style="max-width: 450px;" >

          <span class="modal-custom-close" ><i class="las la-times"></i></span>

          <h4> <strong><?php echo $ULang->t("Ваша ставка принята!"); ?></strong> </h4>

          <p class="mt15" ><?php echo $ULang->t("Если ставка будет перебита, вы получите E-mail уведомление. Новую ставку вы можете сделать в любое время!"); ?></p>

          <div class="mt30" ></div>

          <div class="row" >
             <div class="col-lg-3" ></div>
             <div class="col-lg-6" >
               <button class="button-style-custom color-light button-click-close" ><?php echo $ULang->t("Закрыть"); ?></button>
             </div>
             <div class="col-lg-3" ></div>            
          </div>

        </div>
    </div>

    <div class="modal-custom-bg"  id="modal-auction-users" style="display: none;" >
        <div class="modal-custom animation-modal" style="max-width: 550px;" >

          <span class="modal-custom-close" ><i class="las la-times"></i></span>

          <div class="modal-auction-users-content" >

            <h4> <strong><?php echo $ULang->t("Список ставок"); ?></strong> </h4>

            <div class="mt30" ></div>

               <?php
                 if(count($data["auction_users"])){
                   ?>
                   <div class="row no-gutters gutters10 mb25" >

                      <?php
                      foreach ($data["auction_users"] as $key => $value) {
                          ?>
                          <div class="col-lg-2 col-4 col-md-3 col-sm-3" >
                             <div class="auction-user-item" >
                                <div class="auction-user-item-avatar" >
                                   <img src="<?php echo $Profile->userAvatar($value); ?>" title="<?php echo $Profile->name($value); ?>" >
                                </div>
                                <div class="auction-user-item-price" > <?php echo $Main->price($value["ads_auction_price"]); ?> </div>
                             </div>
                          </div>
                          <?php
                      }
                      ?>
                     
                   </div>
                   <?php
                 }else{
                   ?>
                    <p><?php echo $ULang->t("Ставок пока нет"); ?></p>
                    <?php if($_SESSION["profile"]["id"] != $data["ad"]["ads_id_user"]){ ?>
                    <div class="row mt30 mb5" >
                       <div class="col-lg-3" ></div>
                       <div class="col-lg-6" >
                         <button class="button-style-custom schema-color-button color-green open-modal" data-id-modal="modal-auction" ><?php echo $ULang->t("Сделать ставку"); ?></button>
                       </div>
                       <div class="col-lg-3" ></div>            
                    </div>                   
                   <?php
                    }
                 }
               ?>

          </div>


        </div>
    </div>

    <div class="modal-custom-bg"  id="modal-auction-cancel" style="display: none;" >
        <div class="modal-custom animation-modal" style="max-width: 400px;" >

          <span class="modal-custom-close" ><i class="las la-times"></i></span>

            <h4> <strong><?php echo $ULang->t("Отказ от покупки"); ?></strong> </h4>

            <p class="mt15" ><?php echo $ULang->t("Если победитель аукциона отказался по каким то причинам от покупки товара, то вы можете удалить его ставку и исключить из аукциона. Новый победитель будет выбран тот который шел за этим участником!"); ?></p>
            
            <div class="modal-custom-button mt25" >
               <div>
                 <button class="button-style-custom schema-color-button color-green mb5 action-auction-cancel-rate" data-id="<?php echo $data["ad"]["ads_id"]; ?>" ><?php echo $ULang->t("Удалить ставку"); ?></button>
               </div> 
               <div>
                 <button class="button-style-custom color-light mb5 button-click-close" ><?php echo $ULang->t("Отменить"); ?></button>
               </div>                                       
            </div>

        </div>
    </div>

    <div class="modal-custom-bg"  id="modal-confirm-buy" style="display: none;" >
        <div class="modal-custom animation-modal" style="max-width: 500px;" >

          <span class="modal-custom-close" ><i class="las la-times"></i></span>

            <h4> <strong><?php echo $ULang->t("Подтверждение заказа"); ?></strong> </h4>

            <p class="mt15" ><?php echo $ULang->t("После подтверждения заказ объявление будет зарезервировано за Вами, договоритесь с продавцом в чате или по телефону о способе передачи и оплате товара."); ?></p>
            
            <div class="modal-custom-button mt25" >
               <div>
                 <button class="button-style-custom schema-color-button color-green mb5 action-accept-auction-order-reservation" data-id="<?php echo $data["ad"]["ads_id"]; ?>" ><?php echo $ULang->t("Подтверждаю"); ?></button>
               </div> 
               <div>
                 <button class="button-style-custom color-light mb5 button-click-close" ><?php echo $ULang->t("Отменить"); ?></button>
               </div>                                       
            </div>

        </div>
    </div>

    <div class="modal-custom-bg"  id="modal-ad-share" style="display: none;" >
        <div class="modal-custom animation-modal" style="max-width: 400px;" >

          <span class="modal-custom-close" ><i class="las la-times"></i></span>

            <h4 class="text-center" > <strong><?php echo $ULang->t("Поделиться"); ?></strong> </h4>

                            <div class="text-center mt15">
          <div><label><?php echo $ULang->t("Соц.сети"); ?></label></div>
            <?php echo $data["share"]; ?>
                <div class="mt15"><label><?php echo $ULang->t("Ссылка"); ?></label></div>
            <div class="input-group">
                <input id="myInput" type="text" class="form-control" value="">
            <div class="input-group-append">
                <button class="btn btn-dark" onclick="copyToClipboard()"><i class="las la-copy"></i></button>
            </div>
           </div>
        <div id="copyMessage" class="mt15" style="display: none;"><?php echo $ULang->t("Ссылка скопирована"); ?></div>
        </div>

            <div class="mt25" > 

               <button class="button-style-custom color-light mb5 button-click-close" ><?php echo $ULang->t("Закрыть"); ?></button>                                       
            </div>

        </div>
    </div>

    <div class="modal-custom-bg"  id="modal-ad-online-view" style="display: none;" >
        <div class="modal-custom animation-modal" style="max-width: 500px;" >

          <span class="modal-custom-close" ><i class="las la-times"></i></span>

            <h4> <strong><?php echo $ULang->t("Как проходит онлайн-показ"); ?></strong> </h4>

            <p class="mt15" ><?php echo $ULang->t("Продавец проведёт показ по видеосвязи: покажет все детали и ответит на вопросы. Договоритесь о времени и приложении, в котором будет удобно пообщаться."); ?></p>

            <div class="mt25" > 

               <button class="button-style-custom color-light mb5 button-click-close" ><?php echo $ULang->t("Закрыть"); ?></button>                                       
            </div>

        </div>
    </div>

    <div class="modal-custom-bg" id="modal-booking" style="display: none;" >
        <div class="modal-custom animation-modal" style="max-width: 500px;" >

          <span class="modal-custom-close" ><i class="las la-times"></i></span>

          <form class="modal-booking-form" ></form>

        </div>
    </div>

  </noindex>

<!-- 16-08-2023 Correction 36 (validator W3)  Element style not allowed as child of element div
	transferring to index.tpl in <head></head> section
<style>@media (max-width: 992px) {.btn-custom, .css-4che2j {width: 100%;}}</style>
-->





<script>
window.addEventListener('load', function() {
  var urlParams = new URLSearchParams(window.location.search);
  var currentPage = window.location.href;

  // Проверяем, была ли функция уже запущена на этой странице
  var isFunctionExecuted = localStorage.getItem('functionExecuted');
  if (isFunctionExecuted && currentPage === isFunctionExecuted) {
    return; // Если функция уже запущена на этой странице, выходим из обработчика
  }

  if (urlParams.has('modal') && urlParams.get('modal') === 'new_ad') {
    var modal = document.querySelector('[data-id-modal="modal-order-service"]');
    if (modal) {
      modal.click(); // Имитируем щелчок на элементе, чтобы открыть модальное окно
      localStorage.setItem('functionExecuted', currentPage); // Сохраняем информацию о выполнении функции для данной страницы
    }
  }
});

</script>

<script>
function copyToClipboard() {
  var copyText = document.getElementById("myInput");
  copyText.select();
  document.execCommand("copy");
  copyText.setSelectionRange(0, 0); // Снимаем выделение со ссылки
  copyText.blur(); // Убираем фокус с поля ввода
  
  var copyMessage = document.getElementById("copyMessage");
  copyMessage.style.display = "block";
  setTimeout(function() {
    copyMessage.style.display = "none";
  }, 2000); // Скрыть сообщение через 2 секунды
}
</script>



  </body>
</html>