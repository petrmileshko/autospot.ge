<?php
	if($value["ads_filters_type"] == "select" || $value["ads_filters_type"] == "input_btn" || $value["ads_filters_type"] == "input_multi_btn" ||   $value["ads_filters_type"] == "input_btn_color" || $value["ads_filters_type"] == "input_checkbox" || $value["ads_filters_type"] == "switch_btn" || $value["ads_filters_type"] == "select_multi"){
		$findParent = findOne("uni_ads_filters", "ads_filters_id_parent=?", [$value["ads_filters_id"]]);
		if( $findParent ){
			if(count($getItems) > 0){
				foreach ($getItems as $item_key => $item_value) {	
					$checked = ""; $active = "";
					
					if($this->checkSelected($value["ads_filters_id"],$item_value["ads_filters_items_id"],$param) == true){
						$checked = 'checked=""';
						$active = 'class="uni-select-item-active"';
					}
					
					
					
					$items .= ' 
					<label '.$active.' > <input type="radio" '.$checked.' name="filter['.$value["ads_filters_id"].'][]" value="'.$item_value["ads_filters_items_id"].'"> <span>'.$ULang->t( $item_value["ads_filters_items_value"] , [ "table" => "uni_ads_filters", "field" => "ads_filters_items_value" ] ).'</span> <i class="la la-check"></i> </label>                     
					';
					
				}
			}
			
			if(count($getItems) > 10){
				$search_items = '<div class="catalog-list-options-search"> <input class="form-control" type="text" placeholder="'.$ULang->t("Поиск").'" /> </div>';
			}else{ $search_items = ''; }

			$return .= '

			<div class="catalog-list-options toggle-list-optionst d filter-items  " id-filter="'.$value["ads_filters_id"].'" main-id-filter="0" data-ids="'.$this->idsBuild($value["ads_filters_id"],$getFilters).'">	
		
			<div class="uni-select" data-status="0">
			<div class="uni-select-name catalog-list-options-name f w filter-select" style="height:45px;" data-name="'.$ULang->t("Все объявления").'"> <span>'.$ULang->t( $value["ads_filters_name"] , [ "table" => "uni_ads_filters", "field" => "ads_filters_name" ] ).'</span> <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="1em" height="1em" class="css-mf5jvh"><path fill="currentColor" fill-rule="evenodd" d="M2.001 6.5h1.414l1.27 1.271 7.316 7.315 7.315-7.315L20.587 6.5h1.414v1.414l-1.27 1.27-7.316 7.316-1 1h-.827l-3.942-3.942-4.374-4.374-1.27-1.27z"></path></svg></div>
			<div class="uni-select-list " style="background-color: #fff;border: 0px solid #d8d8d8; box-shadow: 0 0 2px 0 rgb(0 0 0 / 15%);">
			'.$items.'
			</div> 
			</div>
            </div>
			
            '.$this->load_podfilters_catalog($value["ads_filters_id"],$param["filter"][$value["ads_filters_id"]][0],$param,"podfilters_catalog").'
			
			';
			
			}else{
			
			if(count($getItems) > 0){
				foreach ($getItems as $item_key => $item_value) {
					
					$checked = ""; $active = "";
					
					if($this->checkSelected($value["ads_filters_id"],$item_value["ads_filters_items_id"],$param) == true){
						$checked = 'checked=""';
						$active = 'class="uni-select-item-active"';
					}
					
					$items .= ' 
					<label '.$active.' > <input type="checkbox" '.$checked.' name="filter['.$value["ads_filters_id"].'][]" value="'.$item_value["ads_filters_items_id"].'"> <span>'.$ULang->t( $item_value["ads_filters_items_value"] , [ "table" => "uni_ads_filters", "field" => "ads_filters_items_value" ] ).'</span> <i class="la la-check"></i> </label>                     
					';
					
				}
			}
			if(count($getItems) > 10){
				$search_items = '<div class="catalog-list-options-search"> <input class="form-control" type="text" placeholder="'.$ULang->t("Поиск").'" /> </div>';
			}else{ $search_items = ''; }
			
			$return .= '
			<div class="toggle-list-optionst d filter-items  " id-filter="'.$value["ads_filters_id"].'" main-id-filter="0" data-ids="'.$this->idsBuild($value["ads_filters_id"],$getFilters).'">	
			
			<div class="uni-select" data-status="0">
			<div class="uni-select-name catalog-list-options-name f w filter-select" style="height:45px;" data-name="'.$ULang->t("Все объявления").'"> <span>'.$ULang->t( $value["ads_filters_name"] , [ "table" => "uni_ads_filters", "field" => "ads_filters_name" ] ).'</span> <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="1em" height="1em" class="css-mf5jvh"><path fill="currentColor" fill-rule="evenodd" d="M2.001 6.5h1.414l1.27 1.271 7.316 7.315 7.315-7.315L20.587 6.5h1.414v1.414l-1.27 1.27-7.316 7.316-1 1h-.827l-3.942-3.942-4.374-4.374-1.27-1.27z"></path></svg></div>
			<div class="uni-select-list " style="background-color: #fff;border: 0px solid #d8d8d8; box-shadow: 0 0 2px 0 rgb(0 0 0 / 15%);">
			'.$items.'
			</div> 
			</div>
            </div>
			';
		}
		}elseif($value["ads_filters_type"] == "input"){
		if(count($getItems) > 0){	
			if(isset($param["filter"][$value["ads_filters_id"]])){
				$slideStart = $param["filter"][$value["ads_filters_id"]]["from"];
				$slideEnd = $param["filter"][$value["ads_filters_id"]]["to"];
				}else{
				$slideStart = $getItems[0]["ads_filters_items_value"];
				$slideEnd = $getItems[1]["ads_filters_items_value"];                           
			}
			
		}
      $return .= '<div class="catalog-list-options toggle-list-options d filter-items"  id-filter="'.$value["ads_filters_id"].'" main-id-filter="0" data-ids="">
	
		
		<div class="filter-input">
		<div class="wb" style="border: 0px solid #d8d8d8;border-radius: 4px;"><span></span><input type="text" placeholder="'.$ULang->t( $value["ads_filters_name"] , [ "table" => "uni_ads_filters", "field" => "ads_filters_name" ] ).'" name="filter['.$value["ads_filters_id"].'][from]" value="'.$param["filter"][$value["ads_filters_id"]]["from"].'" /></div>
		<div class="wb" style="border: 0px solid #d8d8d8;border-radius: 4px;"><span></span><input type="text" placeholder="'.$ULang->t("до").'" name="filter['.$value["ads_filters_id"].'][to]" value="'.$param["filter"][$value["ads_filters_id"]]["to"].'" /></div>
		</div>

        </div>
		
		
		
		';
	}

?>