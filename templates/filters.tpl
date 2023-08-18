 <!--      <form class="modal-form-filter" >

          <div class="row" >
             <div class="col-lg-4" >
               <label>
                  <?php echo $ULang->t("Город или регион"); ?>                             
               </label>
             </div>
             <div class="col-lg-5" >

                <div class="modal-search-geo-container" >
                <input type="text" class="form-control modal-search-geo-input" value="<?php echo $ULang->t($Geo->change()["name"], [ "table"=>"geo", "field"=>"geo_name" ] ); ?>" > 
                <div class="modal-search-geo-results" style="display: none;"></div>
                </div>

                <div class="select-box-city-options" >

                    <?php

                        if(isset($_SESSION["geo"]["data"]["city_id"])){
                            $city_areas = getAll("select * from uni_city_area where city_area_id_city=? order by city_area_name asc", [ intval($_SESSION["geo"]["data"]["city_id"]) ]);
                        }

                        if(isset($_SESSION["geo"]["data"]["city_id"])){
                            $city_metro = getAll("select * from uni_metro where city_id=? and parent_id!=0 Order by name ASC", [ intval($_SESSION["geo"]["data"]["city_id"]) ]);
                        }
                      
                       
                      if(isset($city_areas)){
                      ?>

                          <div class="uni-select" data-status="0" >

                               <div class="uni-select-name" data-name="<?php echo $ULang->t("Район"); ?>" > <span><?php echo $ULang->t("Район"); ?></span> <i class="la la-angle-down"></i> </div>
                               <div class="uni-select-list" >
                                   <?php
                                   foreach ($city_areas as $value) {

                                      if( isset($_GET['filter']['area']) ){

                                          if( in_array($value["city_area_id"], $_GET['filter']['area'] ) ){
                                                  $active = 'class="uni-select-item-active"'; $checked = 'checked=""';      
                                          }else{
                                                  $active = ''; $checked = '';
                                          }

                                      }

                                      ?>
                                      <label <?php echo $active; ?> > <input type="checkbox" <?php echo $checked; ?> name="filter[area][]" value="<?php echo $value["city_area_id"]; ?>" > <span><?php echo $ULang->t( $value["city_area_name"], [ "table" => "uni_city_area", "field" => "city_area_name" ] ); ?></span> <i class="la la-check"></i> </label>
                                      <?php
                                   }
                                   ?>
                               </div>
                          
                          </div>

                      <?php
                      }

                      if(isset($city_metro)){
                      ?>

                          <div class="container-custom-search">
                            <input type="text" class="ads-create-input action-input-search-metro" placeholder="<?php echo $ULang->t("Поиск станций метро"); ?>">
                            <div class="custom-results SearchMetroResults" style="display: none;"></div>
                          </div>

                          <div class="ads-container-metro-station">
                            <?php
                                if( isset($_GET['filter']['metro']) ){
                                    $getMetro = getAll("select * from uni_metro where id IN(".implode(',',$_GET['filter']['metro']).")");

                                    if(count($getMetro)){
                                      foreach ($getMetro as $key => $value) {
                                        $main = findOne("uni_metro", "id=?", [$value["parent_id"]]);
                                        if($main){
                                          echo '
                                                 <span><i style="background-color:'.$main["color"].';"></i>'.$value["name"].' <i class="las la-times ads-metro-delete"></i><input type="hidden" value="'.$value["id"].'" name="filter[metro][]"></span>
                                          ';
                                          }
                                      }
                                    }
                                }
                            ?>
                          </div>

                      <?php
                      }
                    ?>

                </div> 

                <input type="hidden" name="city_id" value="<?php echo isset($_SESSION["geo"]["data"]["city_id"]) ? $_SESSION["geo"]["data"]["city_id"] : 0; ?>" >

                <div class="mb10" ></div>                      
               
             </div>
          </div>

          <?php if(count($getCategoryBoard["category_board_id_parent"][0])){ ?>
          <div class="row" >
             <div class="col-lg-4" >
               <label>
                  <?php echo $ULang->t("Категория"); ?>                             
               </label>
             </div>
             <div class="col-lg-5" >

                <?php
                $main_id_c = 0;
                if($data["category"]["category_board_id"]){
                   $ids_cat = $CategoryBoard->reverseId($getCategoryBoard,$data["category"]["category_board_id"]);
                   $ids_cat = explode(',', $ids_cat);
                   $main_id_c = $ids_cat[0];

                   foreach ($ids_cat as $key => $value) {
                       $array_cats[$value] = $ids_cat[ $key + 1 ];
                   }
                }
                ?>
               
                <div class="uni-select" data-status="0" >

                     <div class="uni-select-name" data-name="<?php echo $ULang->t("Не выбрано"); ?>" > <span><?php echo $ULang->t("Не выбрано"); ?></span> <i class="la la-angle-down"></i> </div>
                     <div class="uni-select-list" >
                         <label> <input type="radio" class="modal-filter-select-category" value="0" > <span><?php echo $ULang->t("Все категории"); ?></span> <i class="la la-check"></i> </label>
                         <?php
                         foreach ($getCategoryBoard["category_board_id_parent"][0] as $value) {
                            ?>
                            <label <?php if($value["category_board_id"] == $main_id_c){ echo 'class="uni-select-item-active"'; } ?> > <input type="radio" class="modal-filter-select-category" value="<?php echo $value["category_board_id"]; ?>" > <span><?php echo $ULang->t( $value["category_board_name"], [ "table" => "uni_category_board", "field" => "category_board_name" ] ); ?></span> <i class="la la-check"></i> </label>
                            <?php
                         }
                         ?>
                     </div>
                
                </div> 

                <div class="select-box-subcategory" >

                    <?php

                      if($array_cats){

                         foreach ($array_cats as $id_main_cat => $id_sub_cat) {

                               if($getCategoryBoard["category_board_id_parent"][$id_main_cat]){
                               ?>

                                <div class="uni-select" data-status="0" >

                                     <div class="uni-select-name" data-name="<?php echo $ULang->t("Не выбрано"); ?>" > <span><?php echo $ULang->t("Не выбрано"); ?></span> <i class="la la-angle-down"></i> </div>
                                     <div class="uni-select-list" >
                                         <label> <input type="radio" class="modal-filter-select-category" value="<?php echo $id_main_cat; ?>" > <span><?php echo $ULang->t("Все категории"); ?></span> <i class="la la-check"></i> </label>
                                         <?php
                                         foreach ($getCategoryBoard["category_board_id_parent"][$id_main_cat] as $value) {
                                            ?>
                                            <label <?php if($value["category_board_id"] == $id_sub_cat){ echo 'class="uni-select-item-active"'; } ?> > <input type="radio" class="modal-filter-select-category" value="<?php echo $value["category_board_id"]; ?>" > <span><?php echo $ULang->t( $value["category_board_name"], [ "table" => "uni_category_board", "field" => "category_board_name" ] ); ?></span> <i class="la la-check"></i> </label>
                                            <?php
                                         }
                                         ?>
                                     </div>
                                
                                </div>

                               <?php
                               }

                         }

                      }

                    ?>

                </div> 

                <div class="mb15" ></div>                      
               
             </div>
          </div>
          <?php } ?>

          <div class="select-box-filters" >
            
              <?php 
              if($data["category"]["category_board_id"]){
                  if( $getCategoryBoard["category_board_id"][ $data["category"]["category_board_id"] ]["category_board_display_price"] ){ 
                  ?>
                  <div class="row" >
                     <div class="col-lg-4" >
                       <label>
                          <?php 
                          if( $getCategoryBoard["category_board_id"][ $data["category"]["category_board_id"] ]["category_board_variant_price"] == 1 ){
                            echo $ULang->t('Зарплата'); 
                          }else{ 
                            echo $ULang->t('Цена'); 
                          }
                          ?>                             
                       </label>
                     </div>
                     <div class="col-lg-5" >
                       
                        <div class="filter-input" >
                          <div><span><?php echo $ULang->t("от"); ?></span><input type="text" class="inputNumber" name="filter[price][from]" value="<?php if(isset($data["param_filter"]["filter"]["price"]["from"])) echo $data["param_filter"]["filter"]["price"]["from"]; ?>" /></div>
                          <div><span><?php echo $ULang->t("до"); ?></span><input type="text" class="inputNumber" name="filter[price][to]" value="<?php if(isset($data["param_filter"]["filter"]["price"]["to"])) echo $data["param_filter"]["filter"]["price"]["to"]; ?>" /></div>
                        </div>

                     </div>
                  </div>
                  <?php 
                  } 
              }else{
                  ?>
                  <div class="row" >
                     <div class="col-lg-4" >
                       <label>
                          <?php echo $ULang->t("Цена"); ?>                             
                       </label>
                     </div>
                     <div class="col-lg-5" >
                       
                        <div class="filter-input" >
                          <div><span><?php echo $ULang->t("от"); ?></span><input type="text" class="inputNumber" name="filter[price][from]" value="<?php if(isset($data["param_filter"]["filter"]["price"]["from"])) echo $data["param_filter"]["filter"]["price"]["from"]; ?>" /></div>
                          <div><span><?php echo $ULang->t("до"); ?></span><input type="text" class="inputNumber" name="filter[price][to]" value="<?php if(isset($data["param_filter"]["filter"]["price"]["to"])) echo $data["param_filter"]["filter"]["price"]["to"]; ?>" /></div>
                        </div>
                       
                     </div>
                  </div>  
                  <?php
              }

              ?>

              <div class="row mt15" >
                 <div class="col-lg-4" >
                   <label>
                      <?php echo $ULang->t("Статус"); ?>                             
                   </label>
                 </div>
                 <div class="col-lg-8" >
                    
                    <div class="filter-items-spacing" >

                      <?php if( $getCategoryBoard["category_board_id"][ $data["category"]["category_board_id"] ]["category_board_secure"] && $settings["secure_status"] ){ ?>
                      <div class="custom-control custom-checkbox" >
                          <input type="checkbox" class="custom-control-input" name="filter[secure]" <?php if($data["param_filter"]["filter"]["secure"]){ echo 'checked=""'; } ?> id="mobileflsecure" value="1" >
                          <label class="custom-control-label" for="mobileflsecure"><?php echo $ULang->t("Безопасная сделка"); ?></label>
                      </div>
                      <?php } ?>
                      
                      <?php if( $getCategoryBoard["category_board_id"][ $data["category"]["category_board_id"] ]["category_board_auction"] ){ ?>
                      <div class="custom-control custom-checkbox">
                          <input type="checkbox" class="custom-control-input" name="filter[auction]" <?php if($data["param_filter"]["filter"]["auction"]){ echo 'checked=""'; } ?> id="mobileflauction" value="1" >
                          <label class="custom-control-label" for="mobileflauction"><?php echo $ULang->t("Аукцион"); ?></label>
                      </div>
                      <?php } ?>
                      
                      <?php if( $getCategoryBoard["category_board_id"][ $data["category"]["category_board_id"] ]["category_board_online_view"] ){ ?>
                      <div class="custom-control custom-checkbox">
                          <input type="checkbox" class="custom-control-input" name="filter[online_view]" <?php if($data["param_filter"]["filter"]["online_view"]){ echo 'checked=""'; } ?> id="mobileonline_view" value="1" >
                          <label class="custom-control-label" for="mobileonline_view"><?php echo $ULang->t("Онлайн-показ"); ?></label>
                      </div>
                      <?php } ?>
                      
                      <div class="custom-control custom-checkbox">
                          <input type="checkbox" class="custom-control-input" name="filter[vip]" <?php if($data["param_filter"]["filter"]["vip"]){ echo 'checked=""'; } ?> id="mobileflvip" value="1" >
                          <label class="custom-control-label" for="mobileflvip"><?php echo $ULang->t("VIP объявление"); ?></label>
                      </div>

                        <?php if( $getCategoryBoard["category_board_id"][ $data["category"]["category_board_id"] ]["category_board_booking"] ){ ?>

                            <?php
                            if( $getCategoryBoard["category_board_id"][ $data["category"]["category_board_id"] ]["category_board_booking_variant"] == 0 ){
                            ?>

                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" name="filter[booking]" <?php if($data["param_filter"]["filter"]["booking"]){ echo 'checked=""'; } ?> id="mobileflbookingVariant" value="1" >
                                    <label class="custom-control-label" for="mobileflbookingVariant"><?php echo $ULang->t("Онлайн-бронирование"); ?></label>
                                </div>

                            <?php }elseif( $getCategoryBoard["category_board_id"][ $data["category"]["category_board_id"] ]["category_board_booking_variant"] == 1 ){ ?>

                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" name="filter[booking]" <?php if($data["param_filter"]["filter"]["booking"]){ echo 'checked=""'; } ?> id="mobileflbookingVariant" value="1" >
                                    <label class="custom-control-label" for="mobileflbookingVariant"><?php echo $ULang->t("Онлайн-аренда"); ?></label>
                                </div>

                            <?php } ?>

                        <?php } ?>

                    </div>
                   
                 </div>
              </div>
              
                <?php if( $getCategoryBoard["category_board_id"][ $data["category"]["category_board_id"] ]["category_board_booking"] ){ ?>

                    <?php
                    if( $getCategoryBoard["category_board_id"][ $data["category"]["category_board_id"] ]["category_board_booking_variant"] == 0 ){

                    ?>

                      <div class="row mt15 mb15" >
                         <div class="col-lg-4" >
                           <label>
                              <?php echo $ULang->t("Даты"); ?>                             
                           </label>
                         </div>
                         <div class="col-lg-8" >
                            
                               <div class="filter-input" >
                                  <div><span><?php echo $ULang->t("с"); ?></span><input type="text" class="catalog-change-date-from" name="filter[date][start]" value="<?php if($data["param_filter"]["filter"]["date"]["start"]) echo date("d.m.Y", strtotime($data["param_filter"]["filter"]["date"]["start"])); ?>" /></div>
                                  <div><span><?php echo $ULang->t("по"); ?></span><input type="text" class="catalog-change-date-to" name="filter[date][end]" value="<?php if($data["param_filter"]["filter"]["date"]["end"]) echo date("d.m.Y", strtotime($data["param_filter"]["filter"]["date"]["end"])); ?>" /></div>
                                </div>
                           
                         </div>
                      </div>

                    <?php }elseif( $getCategoryBoard["category_board_id"][ $data["category"]["category_board_id"] ]["category_board_booking_variant"] == 1 ){ ?>

                      <div class="row mt15 mb15" >
                         <div class="col-lg-4" >
                           <label>
                              <?php echo $ULang->t("Даты"); ?>                             
                           </label>
                         </div>
                         <div class="col-lg-8" >
                            
                               <div class="filter-input" >
                                  <div><span><?php echo $ULang->t("с"); ?></span><input type="text" class="catalog-change-date-from" name="filter[date][start]" value="<?php if($data["param_filter"]["filter"]["date"]["start"]) echo date("d.m.Y", strtotime($data["param_filter"]["filter"]["date"]["start"])); ?>" /></div>
                                  <div><span><?php echo $ULang->t("по"); ?></span><input type="text" class="catalog-change-date-to" name="filter[date][end]" value="<?php if($data["param_filter"]["filter"]["date"]["end"]) echo date("d.m.Y", strtotime($data["param_filter"]["filter"]["date"]["end"])); ?>" /></div>
                                </div>
                           
                         </div>
                      </div>

                    <?php } ?>

                <?php } ?>

              <?php if($route_name == 'catalog'){ ?>
              <?php echo $Filters->load_filters_catalog( $data["category"]["category_board_id"] , $data["param_filter"], "filters_modal" ); ?>
              <?php } ?>

          </div>

          <div class="row mt15" >
             <div class="col-lg-4" >
               <label>
                  <?php echo $ULang->t("Срок размещения"); ?>                             
               </label>
             </div>
             <div class="col-lg-8" >
                
                  <div class="custom-control custom-radio">
                      <input type="radio" class="custom-control-input" name="filter[period]" <?php if($data["param_filter"]["filter"]["period"] == 1){ echo 'checked=""'; } ?> id="mobileflPeriod1" value="1" >
                      <label class="custom-control-label" for="mobileflPeriod1"><?php echo $ULang->t("За 24 часа"); ?></label>
                  </div>                        

                  <div class="custom-control custom-radio">
                      <input type="radio" class="custom-control-input" name="filter[period]" <?php if($data["param_filter"]["filter"]["period"] == 7){ echo 'checked=""'; } ?> id="mobileflPeriod2" value="7" >
                      <label class="custom-control-label" for="mobileflPeriod2"><?php echo $ULang->t("За 7 дней"); ?></label>
                  </div>

                  <div class="custom-control custom-radio">
                      <input type="radio" class="custom-control-input" name="filter[period]" <?php if(!isset($data["param_filter"]["filter"]["period"])){ echo 'checked=""'; } ?> id="mobileflPeriod3" value="" >
                      <label class="custom-control-label" for="mobileflPeriod3"><?php echo $ULang->t("За все время"); ?></label>
                  </div>
               
             </div>
          </div>

          <input type="hidden" name="id_c" value="<?php echo $data["category"]["category_board_id"]; ?>" >

          <div class="mt30 mb30" >

                <div><button class="btn-custom btn-color-blue width100" > <?php echo $ULang->t("Применить"); ?> </button></div>

                <?php if($data["param_filter"]["filter"] && !$data["filter"]){ ?>
                <div><button class="btn-custom btn-color-light action-clear-filter mt5 width100" > <?php echo $ULang->t("Сбросить"); ?> </button></div>
                <?php } ?>

          </div>

        </form>-->