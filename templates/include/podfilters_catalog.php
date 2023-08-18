

<?php
	if($parent_value["ads_filters_type"] == "select" || $parent_value["ads_filters_type"] == "input_btn" || $parent_value["ads_filters_type"] == "select_multi"){
		
		$findParent = findOne("uni_ads_filters", "ads_filters_id_parent=?", [$parent_value["ads_filters_id"]]);
		
		if($findParent){
			
			if(count($getItems) > 0){
				
				foreach ($getItems as $item_key => $item_value) {
					
					$active = "";
					$checked = "";
					
					if($this->checkSelected($parent_value["ads_filters_id"],$item_value["ads_filters_items_id"],$param) == true){
						$checked = 'checked=""';
						$active = 'class="uni-select-item-active"';
					}
					
					$items .= ' 
					<label '.$active.' > <input type="radio" '.$checked.' name="filter['.$parent_value["ads_filters_id"].'][]" value="'.$item_value["ads_filters_items_id"].'" > <span>'.$ULang->t( $item_value["ads_filters_items_value"] , [ "table" => "uni_ads_filters", "field" => "ads_filters_items_value" ] ).'</span> <i class="la la-check"></i> </label>                       
					';
					
				}
			}
			
				$return .= ' 
			
			<div class="catalog-list-options toggle-list-options filter-items d" id-filter="'.$parent_value["ads_filters_id"].'" main-id-filter="'.$id_filter.'"  data-ids="'.$this->idsBuild($parent_value["ads_filters_id"],$getFilters).'" >
       
			<div class="uni-select" data-status="0">
			<div class="uni-select-name catalog-list-options-name f w filter-select" style="height:45px;" data-name="'.$ULang->t("Все объявления").'"> <span>'.$ULang->t( $parent_value["ads_filters_name"] , [ "table" => "uni_ads_filters", "field" => "ads_filters_name" ] ).'</span> <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="1em" height="1em" class="css-mf5jvh"><path fill="currentColor" fill-rule="evenodd" d="M2.001 6.5h1.414l1.27 1.271 7.316 7.315 7.315-7.315L20.587 6.5h1.414v1.414l-1.27 1.27-7.316 7.316-1 1h-.827l-3.942-3.942-4.374-4.374-1.27-1.27z"></path></svg></div>
			<div class="uni-select-list " style="background-color: #fff;border: 0px solid #d8d8d8; box-shadow: 0 0 2px 0 rgb(0 0 0 / 15%);">
			'.$items.'
			</div> 
			</div>
			</div>
            '.$this->load_podfilters_catalog($parent_value["ads_filters_id"],$param["filter"][$parent_value["ads_filters_id"]][0],$param,$tpl).'
			
			';
			
			}else{
			
			if(count($getItems) > 0){
				
				foreach ($getItems as $item_key => $item_value) {
					
					$active = "";
					$checked = "";
					
					if($this->checkSelected($parent_value["ads_filters_id"],$item_value["ads_filters_items_id"],$param) == true){
						$checked = 'checked=""';
						$active = 'class="uni-select-item-active"';
					}
					
					$items .= '       
					<label '.$active.' > <input type="checkbox" '.$checked.' name="filter['.$parent_value["ads_filters_id"].'][]" value="'.$item_value["ads_filters_items_id"].'" > <span>'.$ULang->t( $item_value["ads_filters_items_value"] , [ "table" => "uni_ads_filters", "field" => "ads_filters_items_value" ] ).'</span> <i class="la la-check"></i> </label>                
					';
					
				}
			}
			
			$return .= ' 
			
			<div class="catalog-list-options toggle-list-options filter-items d" id-filter="'.$parent_value["ads_filters_id"].'" main-id-filter="'.$id_filter.'"  data-ids="'.$this->idsBuild($parent_value["ads_filters_id"],$getFilters).'" >
			<div class="uni-select" data-status="0">
			<div class="uni-select-name catalog-list-options-name f w filter-select" style="height:45px;" data-name="'.$ULang->t("Все объявления").'"> <span>'.$ULang->t( $parent_value["ads_filters_name"] , [ "table" => "uni_ads_filters", "field" => "ads_filters_name" ] ).'</span> <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="1em" height="1em" class="css-mf5jvh"><path fill="currentColor" fill-rule="evenodd" d="M2.001 6.5h1.414l1.27 1.271 7.316 7.315 7.315-7.315L20.587 6.5h1.414v1.414l-1.27 1.27-7.316 7.316-1 1h-.827l-3.942-3.942-4.374-4.374-1.27-1.27z"></path></svg></div>
			
			<div class="uni-select-list " style="background-color: #fff;border: 0px solid #d8d8d8; box-shadow: 0 0 2px 0 rgb(0 0 0 / 15%);">
			'.$items.'
			</div> 
			</div>
			</div>
			
			';
			
		}
		
		
		}elseif($parent_value["ads_filters_type"] == "input"){
		
		if(count($getItems) > 0){
			
			if(isset($param["filter"][$parent_value["ads_filters_id"]])){
				$slideStart = $param["filter"][$parent_value["ads_filters_id"]]["from"];
				$slideEnd = $param["filter"][$parent_value["ads_filters_id"]]["to"];
				}else{
				$slideStart = $getItems[0]["ads_filters_items_value"];
				$slideEnd = $getItems[1]["ads_filters_items_value"];                           
			}
			
			
		}
		
		$return .= '
<!--		
		<div class="filter-items filter-items-spacing" id-filter="'.$parent_value["ads_filters_id"].'" main-id-filter="'.$id_filter.'" data-ids="" >
		
		<div class="col-lg-4" >
		<label>'.$ULang->t( $parent_value["ads_filters_name"] , [ "table" => "uni_ads_filters", "field" => "ads_filters_name" ] ).'</label>
		</div>
		<div class="col-lg-5" >
		
		<div class="filter-input" >
		<div><span>'.$ULang->t("от").'</span><input type="text" name="filter['.$parent_value["ads_filters_id"].'][from]" value="'.$param["filter"][$parent_value["ads_filters_id"]]["from"].'" /></div>
		<div><span>'.$ULang->t("до").'</span><input type="text" name="filter['.$parent_value["ads_filters_id"].'][to]" value="'.$param["filter"][$parent_value["ads_filters_id"]]["to"].'" /></div>
		</div>
		
		</div>
		
		</div>
-->		
		';
		
		
	}
?>










