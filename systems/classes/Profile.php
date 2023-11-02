<?php
	
	class Profile{
		
		function oneUser($query = "", $param = []){
			
			return getOne("SELECT * FROM uni_clients LEFT JOIN `uni_city` ON `uni_city`.city_id = `uni_clients`.clients_city_id $query ",$param);
			
		}
		
		function userLink($data = [], $shopConditions = true){
			
			$Shop = new Shop();
			
			if($shopConditions){
				$getShop = $Shop->getShop(['user_id'=>$data["clients_id"],'conditions'=>true]);
				
				if($getShop){
					return $Shop->linkShop($getShop["clients_shops_id_hash"]);
				}
			}
			
			return _link("user/" . $data["clients_id_hash"]);
			
		}
		
		function getMessage($user_id=0,$id_hash=""){
			
			$results = [];
			$results['total'] = 0;
			
			if(!$id_hash){
				$groupBy = [];
				$getAll = getAll("select * from uni_chat_users where chat_users_id_user=?", array($user_id));
				if(count($getAll)){
					
					foreach ($getAll as $key => $value) {
						
						if($value["chat_users_id_interlocutor"]){
							$get = findOne("uni_clients", "clients_id=?", [$value["chat_users_id_interlocutor"]]);
							if( $get ){
								$groupBy[ $value["chat_users_id_hash"] ] = $value["chat_users_id_hash"];
							}
							}else{
							$groupBy[ $value["chat_users_id_hash"] ] = $value["chat_users_id_hash"];
						}
						
					}
					
					if( count($groupBy) ){
						foreach ($groupBy as $id_hash) {
							
							$count = (int)getOne("select count(*) as total from uni_chat_messages where chat_messages_id_hash=? and chat_messages_status=? and chat_messages_id_user!=?", array($id_hash,0,$user_id) )["total"];
							
							if($count){
								$results['hash_counts'][$id_hash] = $count;
							}
							
							$results['total'] += $count;
							
						}
					}
					
				}
				return $results;
				}else{
				$results['total'] = (int)getOne("select count(*) as total from uni_chat_messages where chat_messages_id_hash=? and chat_messages_status=? and chat_messages_id_user!=?", array($id_hash,0,$user_id) )["total"];
				return $results;
			}
			
		}
		
		function activation(){
			global $config,$settings;
			
			$Subscription = new Subscription();
			
			if(!empty($_GET["activation_hash"])){
				$get = findOne("uni_clients_hash_email","clients_hash_email_hash=?",[clear($_GET['activation_hash'])]);
				if($get){
					
					$getUser = findOne("uni_clients", "clients_id=?", [$get["clients_hash_email_id_user"]]);
					
					if(!$getUser["clients_email"]){
						
						if($settings["bonus_program"]["email"]["status"] && $settings["bonus_program"]["email"]["price"]){
							$this->actionBalance(array("id_user"=>$get["clients_hash_email_id_user"],"summa"=>$settings["bonus_program"]["email"]["price"],"title"=>$settings["bonus_program"]["email"]["name"],"id_order"=>generateOrderId(),"email" => $get["clients_hash_email_email"],"name" => $getUser->clients_name, "note" => $settings["bonus_program"]["email"]["name"]),"+");           	
						}
						
					}
					
					update("UPDATE uni_clients SET clients_email=? WHERE clients_id=?", [$get["clients_hash_email_email"],$get["clients_hash_email_id_user"]]);
					$Subscription->add(array("email"=>$get["clients_hash_email_email"],"user_id"=>$get["clients_hash_email_id_user"],"name"=>$getUser["clients_name"],"status" => 1));
					
					update("delete from uni_clients_hash_email where clients_hash_email_hash=?", [clear($_GET['activation_hash'])]);
					
				}
			}
			
		}
		
		function checkAuth(){
			
			$Shop = new Shop();
			
			if(intval($_SESSION['profile']['id'])){
				$get = findOne("uni_clients", "clients_id=?", [$_SESSION['profile']['id']]);
				}elseif($_COOKIE["tokenAuth"]){
				$get = findOne("uni_clients", "clients_cookie_token=?", [clear($_COOKIE["tokenAuth"])]);
			}
			
			if($get){
				
				$_SESSION['profile']['id'] = $get->clients_id;
				
				if($get->clients_status == 2 || $get->clients_status == 3){
					update('update uni_clients set clients_cookie_token=? where clients_id=?',['',$get->clients_id]);
					unset($_SESSION['profile']); 
					setcookie("tokenAuth", "", time() - 2592000);
					}else{
					$_SESSION["profile"]["data"] = $get;
					$_SESSION["profile"]["tariff"] = $this->getOrderTariff($get->clients_id);
					$getShop = $Shop->getShop(['user_id'=>$get->clients_id,'conditions'=>false]);
					if($getShop){
						$_SESSION["profile"]['shop'] = $getShop;
						}else{
						$_SESSION["profile"]['shop'] = [];
					}
					unset($_SESSION["profile"]["data"]["clients_pass"]);
					unset($_SESSION["profile"]["data"]["clients_cookie_token"]);
				}
				
			}
			
		}
		
		function chatDialog($id_hash = 0, $support = 0){
			global $config, $settings;
			
			$Ads = new Ads();
			$Profile = new Profile();
			$Main = new Main();
			$ULang = new ULang();
			$Admin = new Admin();
			
			if($id_hash){
				
				if(!$support){
					
					$getChatUser = getOne("select * from uni_chat_users where chat_users_id_hash=? and chat_users_id_user=?", array($id_hash,intval($_SESSION['profile']['id'])) );
					
					if($getChatUser["chat_users_id_ad"]){
						
						$getAd = $Ads->get("ads_id=?", [$getChatUser["chat_users_id_ad"]]);
						
						$getAd["ads_images"] = $Ads->getImages($getAd["ads_images"]);
						
						if( $id_hash == md5( $getChatUser["chat_users_id_ad"] . $getChatUser["chat_users_id_interlocutor"] ) || $id_hash == md5( $getChatUser["chat_users_id_ad"] . $getChatUser["chat_users_id_user"] ) ){
							
							update("update uni_chat_messages set chat_messages_status=? where chat_messages_id_hash=? and chat_messages_id_user!=?", array(1,$id_hash,$_SESSION['profile']['id']));
							
							$getDialog = getAll("select * from uni_chat_messages where chat_messages_id_hash=? order by chat_messages_date asc", array($id_hash) );
							
							$getLocked = findOne( "uni_clients_blacklist", "clients_blacklist_user_id=? and clients_blacklist_user_id_locked=?", array(intval($_SESSION['profile']['id']),$getChatUser["chat_users_id_interlocutor"]) );
							
							$getMyLocked = findOne( "uni_clients_blacklist", "clients_blacklist_user_id=? and clients_blacklist_user_id_locked=?", array( $getChatUser["chat_users_id_interlocutor"],intval($_SESSION['profile']['id'])) );
							
							ob_start();
							require $config["template_path"] . "/include/chat_dialog.php";
							$list_dialog = ob_get_clean();
							
							return $list_dialog;
							
						}
						
						}else{
						
						if( $id_hash == md5( $getChatUser["chat_users_id_user"] . $getChatUser["chat_users_id_interlocutor"] ) || $id_hash == md5( $getChatUser["chat_users_id_interlocutor"] . $getChatUser["chat_users_id_user"] ) ){
							
							update("update uni_chat_messages set chat_messages_status=? where chat_messages_id_hash=? and chat_messages_id_user!=?", array(1,$id_hash,$_SESSION['profile']['id']));
							
							$getDialog = getAll("select * from uni_chat_messages where chat_messages_id_hash=? order by chat_messages_date asc", array($id_hash) );
							
							$getLocked = findOne( "uni_clients_blacklist", "clients_blacklist_user_id=? and clients_blacklist_user_id_locked=?", array(intval($_SESSION['profile']['id']),$getChatUser["chat_users_id_interlocutor"]) );
							
							$getMyLocked = findOne( "uni_clients_blacklist", "clients_blacklist_user_id=? and clients_blacklist_user_id_locked=?", array( $getChatUser["chat_users_id_interlocutor"],intval($_SESSION['profile']['id'])) );
							
							$getUser = findOne("uni_clients", "clients_id=?", [$getChatUser["chat_users_id_interlocutor"]]);
							
							ob_start();
							require $config["template_path"] . "/include/chat_dialog_users.php";
							$list_dialog = ob_get_clean();
							
							return $list_dialog;
							
						}                
						
					}
					
					}else{
					
					$getChatUser = getOne("select * from uni_chat_users where chat_users_id_hash=? and chat_users_id_user=?", array($id_hash,intval($_SESSION['profile']['id'])) );
					
					if($id_hash == md5('support' . $_SESSION['profile']['id'])){
						
						update("update uni_chat_messages set chat_messages_status=? where chat_messages_id_hash=? and chat_messages_id_user!=?", array(1,$id_hash,$_SESSION['profile']['id']));
						
						$getDialog = getAll("select * from uni_chat_messages where chat_messages_id_hash=? order by chat_messages_date asc", array($id_hash) );
						
						$getMyLocked = findOne( "uni_clients_blacklist", "clients_blacklist_user_id=? and clients_blacklist_user_id_locked=?", array( $getChatUser["chat_users_id_interlocutor"],intval($_SESSION['profile']['id'])) );
						
						ob_start();
						require $config["template_path"] . "/include/chat_dialog_support.php";
						$list_dialog = ob_get_clean();
						
						return $list_dialog;
						
					}
					
				}
				
				}else{
				
				$get = getOne("select count(*) as total from uni_chat_users where chat_users_id_user=? group by chat_users_id_hash", array(intval($_SESSION['profile']['id'])) );
				
				if($get["total"]){
					return '
					<div class="chat-dialog-empty" >
					<div>
					<svg width="184" height="136" viewBox="0 0 184 136" ><defs><linearGradient id="dialog-stub_svg__a" x1="100%" x2="0%" y1="0%" y2="100%"><stop offset="0%" stop-color="#BAF8FF"></stop><stop offset="100%" stop-color="#D2D4FF"></stop></linearGradient><linearGradient id="dialog-stub_svg__b" x1="0%" x2="100%" y1="100%" y2="0%"><stop offset="0%" stop-color="#B7F2FF"></stop><stop offset="100%" stop-color="#C1FFE5"></stop></linearGradient><linearGradient id="dialog-stub_svg__c" x1="100%" x2="0%" y1="0%" y2="100%"><stop offset="0%" stop-color="#FFF0BF"></stop><stop offset="100%" stop-color="#FFE0D4"></stop></linearGradient></defs><g fill="none" fill-rule="evenodd"><path fill="#FFF" d="M-88-141h360v592H-88z"></path><g transform="translate(12 8)"><path fill="#FFF" d="M0 3.993A4 4 0 0 1 3.995 0h152.01A3.996 3.996 0 0 1 160 3.993v112.014a4 4 0 0 1-3.995 3.993H3.995A3.996 3.996 0 0 1 0 116.007V3.993z"></path><rect width="24" height="24" x="12" y="8" fill="url(#dialog-stub_svg__a)" rx="4"></rect><path fill="#F5F5F5" d="M71 13H44v6h27zm77 0h-17v6h17zm-35.5 10H44v6h68.5z"></path><circle cx="35" cy="11" r="6" fill="#E6EDFF" stroke="#FFF" stroke-width="2"></circle><rect width="24" height="24" x="12" y="47" fill="url(#dialog-stub_svg__b)" rx="4"></rect><path fill="#F5F5F5" d="M71 52H44v6h27zm77 0h-17v6h17zm-35.5 10H44v6h68.5z"></path><circle cx="35" cy="50" r="6" fill="#E6EDFF" stroke="#FFF" stroke-width="2"></circle><rect width="24" height="24" x="12" y="86" fill="url(#dialog-stub_svg__c)" rx="4"></rect><path fill="#F5F5F5" d="M71 91H44v6h27zm77 0h-17v6h17zm-35.5 10H44v6h68.5z"></path><circle cx="35" cy="89" r="6" fill="#E6EDFF" stroke="#FFF" stroke-width="2"></circle></g></g></svg>
					<p>'.$ULang->t("Выберите чат для общения").'</p>
					</div>
					</div>
					';
					}else{
					return '
					<div class="chat-dialog-empty" >
					<div>
					<svg width="184" height="136" viewBox="0 0 184 136" ><defs><linearGradient id="dialog-stub_svg__a" x1="100%" x2="0%" y1="0%" y2="100%"><stop offset="0%" stop-color="#BAF8FF"></stop><stop offset="100%" stop-color="#D2D4FF"></stop></linearGradient><linearGradient id="dialog-stub_svg__b" x1="0%" x2="100%" y1="100%" y2="0%"><stop offset="0%" stop-color="#B7F2FF"></stop><stop offset="100%" stop-color="#C1FFE5"></stop></linearGradient><linearGradient id="dialog-stub_svg__c" x1="100%" x2="0%" y1="0%" y2="100%"><stop offset="0%" stop-color="#FFF0BF"></stop><stop offset="100%" stop-color="#FFE0D4"></stop></linearGradient></defs><g fill="none" fill-rule="evenodd"><path fill="#FFF" d="M-88-141h360v592H-88z"></path><g transform="translate(12 8)"><path fill="#FFF" d="M0 3.993A4 4 0 0 1 3.995 0h152.01A3.996 3.996 0 0 1 160 3.993v112.014a4 4 0 0 1-3.995 3.993H3.995A3.996 3.996 0 0 1 0 116.007V3.993z"></path><rect width="24" height="24" x="12" y="8" fill="url(#dialog-stub_svg__a)" rx="4"></rect><path fill="#F5F5F5" d="M71 13H44v6h27zm77 0h-17v6h17zm-35.5 10H44v6h68.5z"></path><circle cx="35" cy="11" r="6" fill="#E6EDFF" stroke="#FFF" stroke-width="2"></circle><rect width="24" height="24" x="12" y="47" fill="url(#dialog-stub_svg__b)" rx="4"></rect><path fill="#F5F5F5" d="M71 52H44v6h27zm77 0h-17v6h17zm-35.5 10H44v6h68.5z"></path><circle cx="35" cy="50" r="6" fill="#E6EDFF" stroke="#FFF" stroke-width="2"></circle><rect width="24" height="24" x="12" y="86" fill="url(#dialog-stub_svg__c)" rx="4"></rect><path fill="#F5F5F5" d="M71 91H44v6h27zm77 0h-17v6h17zm-35.5 10H44v6h68.5z"></path><circle cx="35" cy="89" r="6" fill="#E6EDFF" stroke="#FFF" stroke-width="2"></circle></g></g></svg>
					<p>'.$ULang->t("У вас пока нет диалогов").'</p>
					</div>
					</div>
					';
				}
				
				
			}      
			
		}
		
		function getUserLocked( $user_id=0, $locked_id=0 ){
			return findOne( "uni_clients_blacklist", "clients_blacklist_user_id=? and clients_blacklist_user_id_locked=?", array($user_id,$locked_id) );
		}
		
		function getCountFavorites($id_ad=0){
			return (int)getOne( "select count(*) as total from uni_favorites where favorites_id_ad=?", array($id_ad) )["total"];
		}
		
		function sendChat($param = array()){
			
			global $config, $settings;
			
			$Ads = new Ads();
			
			$messages = [];
			$attach = [];
			
			$getUserLocked = $this->getUserLocked($param["user_to"],$param["user_from"]);
			
			if($getUserLocked) return false;
			
			if($param["attach"]){
				
				foreach ($param["attach"] as $name) {
					if(file_exists($config["basePath"] . "/" . $config["media"]["temp_images"] . "/" . $name)){
						@copy( $config["basePath"] . "/" . $config["media"]["temp_images"] . "/" . $name , $config["basePath"] . "/" . $config["media"]["attach"] . "/" . $name );
						$attach['images'][] = $name;
					}
				}
				
			}
			
			if($param["voice"]){
				$attach['voice'] = $param["voice"];
				$attach['duration'] = $param["duration"];
			}
			
			if($param["text"] || $attach || $param["action"]){
				
				if($param["text"]) $encrypt_text = encrypt($param["text"]);
				
				if(!$param["support"]){
					
					if($param["id_ad"]){
						
						$getAd = $Ads->get("ads_id=".intval($param["id_ad"]) );
						
						if(!$param["id_hash"]){
							
							$param["id_hash"] = md5( $param["id_ad"] . $param["user_from"] );
							
							}else{
							
							if( $param["id_hash"] != md5( $param["id_ad"] . $param["user_from"] ) && $param["id_hash"] != md5( $param["id_ad"] . $param["user_to"] ) ){
								exit;
							}
							
						}
						
						}else{
						
						if(!$param["id_hash"]){
							
							$param["id_hash"] = md5( $param["user_to"] . $param["user_from"] );
							
							}else{
							
							if( $param["id_hash"] != md5( $param["user_to"] . $param["user_from"] ) && $param["id_hash"] != md5( $param["user_from"] . $param["user_to"] ) ){
								exit;
							}
							
						}
						
					}
					
					}else{
					
					if( $param["id_hash"] != md5( 'support' . $param["user_from"] ) && $param["id_hash"] != md5( 'support' . $param["user_to"] ) ){
						exit;
					}
					
				}
				
				
				if(!$param["action"]){
					insert("INSERT INTO uni_chat_users(chat_users_id_ad,chat_users_id_user,chat_users_id_hash,chat_users_id_interlocutor,chat_users_id_responder)VALUES(?,?,?,?,?)", array(intval($param["id_ad"]),$param["user_from"], $param["id_hash"], $param["user_to"],intval($param["id_responder"])));
				}
				
				insert("INSERT INTO uni_chat_users(chat_users_id_ad,chat_users_id_user,chat_users_id_hash,chat_users_id_interlocutor,chat_users_id_responder)VALUES(?,?,?,?,?)", array(intval($param["id_ad"]),$param["user_to"], $param["id_hash"], $param["user_from"], intval($param["id_responder"])));
				
				insert("INSERT INTO uni_chat_messages(chat_messages_text,chat_messages_date,chat_messages_id_hash,chat_messages_id_user,chat_messages_action,chat_messages_attach,chat_messages_id_responder)VALUES(?,?,?,?,?,?,?)", array($encrypt_text, date("Y-m-d H:i:s"),$param["id_hash"],$param["user_from"],intval($param["action"]),json_encode($attach),intval($param["id_responder"])));
				
				
				if($param["firebase"] && $param["action"] == 0){
					if(!$param["support"]){
						$getUserFrom = findOne('uni_clients','clients_id=?', [$param["user_from"]]);  
						$getMessageToken = findOne('uni_clients_fcm_tokens','user_id=?', [$param["user_to"]]);  
						if($getMessageToken) $messages[$getMessageToken['token']] = ['title'=>$getUserFrom['clients_name'], 'text'=>custom_substr($param["text"], 255, "..."), 'screen'=>'chat', 'id_hash'=>$param["id_hash"], 'only_notification'=>false, 'support'=>$param["support"]]; 
						}else{
						$getMessageToken = findOne('uni_clients_fcm_tokens','user_id=?', [$param["user_to"]]);  
						if($getMessageToken) $messages[$getMessageToken['token']] = ['title'=>$settings['site_name'], 'text'=>custom_substr($param["text"], 255, "..."), 'screen'=>'chat', 'id_hash'=>$param["id_hash"], 'only_notification'=>false, 'support'=>$param["support"]];
					}
					
					if($messages) sendMessageFirebase($messages);
				}
				
			}
			
		}
		
		function setMode(){
			if($_SESSION['profile']['id']){
				update("UPDATE uni_clients SET clients_datetime_view=NOW() WHERE clients_id=?", array( intval($_SESSION['profile']['id']) ));
			}  
		}
		
		function chatUsers( $chat_users_id_hash = "", $newMessage = false ){
			global $config;
			
			$Ads = new Ads();
			$ULang = new ULang();
			
			$listUsers = [];
			
			$get = getAll("select * from uni_chat_users where chat_users_id_user=? order by chat_users_id desc",[intval($_SESSION['profile']['id'])]);
			
			if( count($get) ){
				foreach ($get as $key => $value) {
					$listUsers[ $value["chat_users_id_hash"] ] = $value;
				}
			}
			
			if(count($listUsers)){
				foreach ($listUsers as $key => $value) {
					
					$getMsg = getOne("select * from uni_chat_messages where chat_messages_id_hash=? order by chat_messages_date desc", array($value["chat_users_id_hash"]));
					
					if($getMsg || $newMessage){
						
						$getMsg["chat_messages_text"] = decrypt($getMsg["chat_messages_text"]);
						
						if( $chat_users_id_hash == $value["chat_users_id_hash"] ){
							$active_user = 'class="active"';
							}else{
							$active_user = '';
						}
						
						$getAd = $Ads->get("ads_id=?",[$value["chat_users_id_ad"]]);
						
						if($getAd){
							
							$getAd["ads_images"] = $Ads->getImages($getAd["ads_images"]);
							
							if($value["chat_users_id_interlocutor"] == $_SESSION['profile']['id']){
								
							?>
							<div data-id="<?php echo $value["chat_users_id_hash"]; ?>" <?php echo $active_user; ?> >
								
								<div class="module-chat-users-img" >
									<img src="<?php echo Exists($config["media"]["small_image_ads"],$getAd["ads_images"][0],$config["media"]["no_image"]); ?>" >
								</div>
								<div class="module-chat-users-info" >
									<span class="module-chat-users-info-date" ><?php echo datetime_format($getMsg["chat_messages_date"], false); ?></span>
									<p class="module-chat-users-info-client" ><?php echo custom_substr($this->name($getAd),10, "..."); ?></p>
									<p class="module-chat-users-info-title" ><?php echo custom_substr($getAd["ads_title"],20, "..."); ?></p>
									<p class="module-chat-users-info-msg" >
										<?php 
											
											if($getMsg["chat_messages_action"] == 0){
												
												if($getMsg["chat_messages_id_user"] == $value["chat_users_id_user"]){ echo $ULang->t("Вы:").' '; }
												
												if($getMsg["chat_messages_text"]){
													echo custom_substr($getMsg["chat_messages_text"], 20, "...");
													}else{
													
													if($getMsg["chat_messages_attach"]){
														$attach = json_decode($getMsg["chat_messages_attach"], true);
														if($attach['voice']){
															echo custom_substr($ULang->t("Голосовое"), 20, "...");
															}elseif($attach['images']){
															echo custom_substr($ULang->t("Фото"), 20, "...");
														}
													}
													
												}
												
												}elseif($getMsg["chat_messages_action"] == 1 && intval($_SESSION['profile']['id']) != $getMsg["chat_messages_id_user"]){
												
												echo custom_substr($ULang->t("Покупатель добавил объявление в избранное"), 20, "...");
												
												}elseif($getMsg["chat_messages_action"] == 2 && intval($_SESSION['profile']['id']) != $getMsg["chat_messages_id_user"]){
												
												echo custom_substr($ULang->t("Ваш номер просмотрели"), 20, "...");
												
												}elseif($getMsg["chat_messages_action"] == 3 && intval($_SESSION['profile']['id']) != $getMsg["chat_messages_id_user"]){
												
												echo custom_substr($ULang->t("Оформление заказа"), 20, "...");
												
												}elseif($getMsg["chat_messages_action"] == 4 && intval($_SESSION['profile']['id']) != $getMsg["chat_messages_id_user"]){
												
												echo custom_substr($ULang->t("У вас новый отзыв"), 20, "...");
												
												}elseif($getMsg["chat_messages_action"] == 5 && intval($_SESSION['profile']['id']) != $getMsg["chat_messages_id_user"]){
												
												echo custom_substr($ULang->t("Вы победили в аукционе"), 20, "...");
												
												}elseif($getMsg["chat_messages_action"] == 6 && intval($_SESSION['profile']['id']) != $getMsg["chat_messages_id_user"]){
												
												echo custom_substr($ULang->t("Ваша ставка перебита"), 20, "...");
												
											}
											
										?>
									</p>
									
									<span class="module-chat-users-info-view" >
										<div class="chat-users-info-read" <?php if($getMsg["chat_messages_status"] && intval($_SESSION['profile']['id']) == $getMsg["chat_messages_id_user"]){ echo 'style="display: block"'; } ?> ><svg width="16" height="16" viewBox="0 0 16 16"><path fill="#77c226" fill-rule="evenodd" d="M11.226 3.5l.748.664-6.924 8.164L-.022 8.27l.644-.82 4.328 3.486L11.226 3.5zm4 0l.748.664-6.924 8.164-.776-.643.676-.749L15.226 3.5z"></path></svg></div>
										<div class="chat-users-info-notread" <?php if(!$getMsg["chat_messages_status"] && intval($_SESSION['profile']['id']) == $getMsg["chat_messages_id_user"]){ echo 'style="display: block"'; } ?> ><svg width="16" height="16" viewBox="0 0 16 16"><path fill="#77c226" fill-rule="evenodd" d="M13.248 3.5l.748.664-6.924 8.164L2 8.215l.644-.765 4.328 3.486z"></path></svg></div>
									</span>
									
									<?php echo $this->countChatMessages($value["chat_users_id_hash"],$_SESSION['profile']['id']); ?>
									
								</div>
								
								<div class="clr" ></div>
								
							</div>
							<?php
								
								}else{
								
								$get = findOne("uni_clients", "clients_id=?", [$value["chat_users_id_interlocutor"]]);
								
								if($get){
									
								?>
								<div data-id="<?php echo $value["chat_users_id_hash"]; ?>" <?php echo $active_user; ?> >
									
									<div class="module-chat-users-img" >
										<img src="<?php echo Exists($config["media"]["small_image_ads"],$getAd["ads_images"][0],$config["media"]["no_image"]); ?>" >
									</div>
									<div class="module-chat-users-info" >
										<span class="module-chat-users-info-date" ><?php echo datetime_format($getMsg["chat_messages_date"], false); ?></span>
										<p class="module-chat-users-info-client" ><?php echo custom_substr($this->name($get),10, "..."); ?></p>
										<p class="module-chat-users-info-title" ><?php echo custom_substr($getAd["ads_title"],20, "..."); ?></p>
										<p class="module-chat-users-info-msg" >
											<?php 
												
												if($getMsg["chat_messages_action"] == 0){
													
													if($getMsg["chat_messages_id_user"] == $value["chat_users_id_user"]){ echo 'Вы: '; }
													
													if($getMsg["chat_messages_text"]){
														echo custom_substr($getMsg["chat_messages_text"], 20, "...");
														}else{
														
														if($getMsg["chat_messages_attach"]){
															$attach = json_decode($getMsg["chat_messages_attach"], true);
															if($attach['voice']){
																echo custom_substr($ULang->t("Голосовое"), 20, "...");
																}elseif($attach['images']){
																echo custom_substr($ULang->t("Фото"), 20, "...");
															}
														}
														
													}
													
													}elseif($getMsg["chat_messages_action"] == 1 && intval($_SESSION['profile']['id']) != $getMsg["chat_messages_id_user"]){
													
													echo custom_substr($ULang->t("Покупатель добавил объявление в избранное"), 20, "...");
													
													}elseif($getMsg["chat_messages_action"] == 2 && intval($_SESSION['profile']['id']) != $getMsg["chat_messages_id_user"]){
													
													echo custom_substr($ULang->t("Ваш номер просмотрели"), 20, "...");
													
													}elseif($getMsg["chat_messages_action"] == 3 && intval($_SESSION['profile']['id']) != $getMsg["chat_messages_id_user"]){
													
													echo custom_substr($ULang->t("Оформление заказа"), 20, "...");
													
													}elseif($getMsg["chat_messages_action"] == 4 && intval($_SESSION['profile']['id']) != $getMsg["chat_messages_id_user"]){
													
													echo custom_substr($ULang->t("У вас новый отзыв"), 20, "...");
													
													}elseif($getMsg["chat_messages_action"] == 5 && intval($_SESSION['profile']['id']) != $getMsg["chat_messages_id_user"]){
													
													echo custom_substr($ULang->t("Вы победили в аукционе"), 20, "...");
													
													}elseif($getMsg["chat_messages_action"] == 6 && intval($_SESSION['profile']['id']) != $getMsg["chat_messages_id_user"]){
													
													echo custom_substr($ULang->t("Ваша ставка перебита"), 20, "...");
													
												}
												
											?>
										</p>
										
										<span class="module-chat-users-info-view" >
											<div class="chat-users-info-read" <?php if($getMsg["chat_messages_status"] && intval($_SESSION['profile']['id']) == $getMsg["chat_messages_id_user"]){ echo 'style="display: block"'; } ?> ><svg width="16" height="16" viewBox="0 0 16 16"><path fill="#77c226" fill-rule="evenodd" d="M11.226 3.5l.748.664-6.924 8.164L-.022 8.27l.644-.82 4.328 3.486L11.226 3.5zm4 0l.748.664-6.924 8.164-.776-.643.676-.749L15.226 3.5z"></path></svg></div>
											<div class="chat-users-info-notread" <?php if(!$getMsg["chat_messages_status"] && intval($_SESSION['profile']['id']) == $getMsg["chat_messages_id_user"]){ echo 'style="display: block"'; } ?> ><svg width="16" height="16" viewBox="0 0 16 16"><path fill="#77c226" fill-rule="evenodd" d="M13.248 3.5l.748.664-6.924 8.164L2 8.215l.644-.765 4.328 3.486z"></path></svg></div>
										</span>
										
										<?php echo $this->countChatMessages($value["chat_users_id_hash"],$_SESSION['profile']['id']); ?>
										
									</div>
									
									<div class="clr" ></div>
									
								</div>
								<?php 
								}             
								
							}
							
							}else{
							
							if($value["chat_users_id_interlocutor"] == $_SESSION['profile']['id']){
								
								$get = findOne("uni_clients", "clients_id=?", [$value["chat_users_id_user"]]);
								
								if($get){
									
								?>
								<div data-id="<?php echo $value["chat_users_id_hash"]; ?>" <?php echo $active_user; ?> >
									
									<div class="module-chat-users-img" >
										<img src="<?php echo $this->userAvatar($get); ?>" >
									</div>
									<div class="module-chat-users-info" >
										<span class="module-chat-users-info-date" ><?php echo datetime_format($getMsg["chat_messages_date"], false); ?></span>
										<p class="module-chat-users-info-client" ><?php echo custom_substr($this->name($get),10, "..."); ?></p>
										<p class="module-chat-users-info-msg" >
											<?php
												if($getMsg["chat_messages_id_user"] == $value["chat_users_id_user"]){ echo 'Вы: '; }
												
												if($getMsg["chat_messages_text"]){
													echo custom_substr($getMsg["chat_messages_text"], 20, "...");
													}else{
													
													if($getMsg["chat_messages_attach"]){
														$attach = json_decode($getMsg["chat_messages_attach"], true);
														if($attach['voice']){
															echo custom_substr($ULang->t("Голосовое"), 20, "...");
															}elseif($attach['images']){
															echo custom_substr($ULang->t("Фото"), 20, "...");
														}
													}
													
												}
											?>
										</p>
										
										<span class="module-chat-users-info-view" >
											<div class="chat-users-info-read" <?php if($getMsg["chat_messages_status"] && intval($_SESSION['profile']['id']) == $getMsg["chat_messages_id_user"]){ echo 'style="display: block"'; } ?> ><svg width="16" height="16" viewBox="0 0 16 16"><path fill="#77c226" fill-rule="evenodd" d="M11.226 3.5l.748.664-6.924 8.164L-.022 8.27l.644-.82 4.328 3.486L11.226 3.5zm4 0l.748.664-6.924 8.164-.776-.643.676-.749L15.226 3.5z"></path></svg></div>
											<div class="chat-users-info-notread" <?php if(!$getMsg["chat_messages_status"] && intval($_SESSION['profile']['id']) == $getMsg["chat_messages_id_user"]){ echo 'style="display: block"'; } ?> ><svg width="16" height="16" viewBox="0 0 16 16"><path fill="#77c226" fill-rule="evenodd" d="M13.248 3.5l.748.664-6.924 8.164L2 8.215l.644-.765 4.328 3.486z"></path></svg></div>
										</span>
										
										<?php echo $this->countChatMessages($value["chat_users_id_hash"],$_SESSION['profile']['id']); ?>
										
									</div>
									
									<div class="clr" ></div>
									
								</div>
								<?php
								}
								
								}else{
								
								$get = findOne("uni_clients", "clients_id=?", [$value["chat_users_id_interlocutor"]]);
								
								if($get){
									
								?>
								<div data-id="<?php echo $value["chat_users_id_hash"]; ?>" <?php echo $active_user; ?> >
									
									<div class="module-chat-users-img" >
										<img src="<?php echo $this->userAvatar($get); ?>" >
									</div>
									<div class="module-chat-users-info" >
										<span class="module-chat-users-info-date" ><?php echo datetime_format($getMsg["chat_messages_date"], false); ?></span>
										<p class="module-chat-users-info-client" ><?php echo custom_substr($this->name($get),10, "..."); ?></p>
										<p class="module-chat-users-info-msg" >
											<?php
												if($getMsg["chat_messages_id_user"] == $value["chat_users_id_user"]){ echo 'Вы: '; }
												
												if(isset($getMsg["chat_messages_text"])){
													echo custom_substr($getMsg["chat_messages_text"], 20, "...");
													}else{
													
													if($getMsg["chat_messages_attach"]){
														$attach = json_decode($getMsg["chat_messages_attach"], true);
														if(isset($attach['voice'])){
															echo custom_substr($ULang->t("Голосовое"), 20, "...");
															}elseif($attach['images']){
															echo custom_substr($ULang->t("Фото"), 20, "...");
														}
													}
													
												}
											?>                                 
										</p>
										
										<span class="module-chat-users-info-view" >
											<div class="chat-users-info-read" <?php if($getMsg["chat_messages_status"] && intval($_SESSION['profile']['id']) == $getMsg["chat_messages_id_user"]){ echo 'style="display: block"'; } ?> ><svg width="16" height="16" viewBox="0 0 16 16"><path fill="#77c226" fill-rule="evenodd" d="M11.226 3.5l.748.664-6.924 8.164L-.022 8.27l.644-.82 4.328 3.486L11.226 3.5zm4 0l.748.664-6.924 8.164-.776-.643.676-.749L15.226 3.5z"></path></svg></div>
											<div class="chat-users-info-notread" <?php if(!$getMsg["chat_messages_status"] && intval($_SESSION['profile']['id']) == $getMsg["chat_messages_id_user"]){ echo 'style="display: block"'; } ?> ><svg width="16" height="16" viewBox="0 0 16 16"><path fill="#77c226" fill-rule="evenodd" d="M13.248 3.5l.748.664-6.924 8.164L2 8.215l.644-.765 4.328 3.486z"></path></svg></div>
										</span>
										
										<?php echo $this->countChatMessages($value["chat_users_id_hash"],$_SESSION['profile']['id']); ?>
										
									</div>
									
									<div class="clr" ></div>
									
								</div>
								<?php 
								}             
								
							}
							
						}
						
					}
					
				}
			}
			
		}
		
		
		function countChatMessages($id_hash = "", $id_user=0, $html = true){
			
			$countMessage = (int)getOne("select count(*) as total from uni_chat_messages where chat_messages_id_hash=? and chat_messages_status=? and chat_messages_id_user!=?", array($id_hash,0,$id_user) )["total"];
			$display = $countMessage ? '' : 'style="display:none"';
			
			if($html){
				return '<span class="module-chat-users-count-msg label-count" '.$display.' >'.$countMessage.'</span>';
				}else{
				return $countMessage;
			}
			
		}
		
		
		function auth_reg($array=array()){
			global $settings,$config;
			
			$Admin = new Admin();
			$Subscription = new Subscription();
			$ULang = new ULang();
			
			if($array["method"] == 1){
				
				$getUser = findOne("uni_clients", "clients_phone=?", [$array["phone"]]);
				
				if($getUser){
					
					if($getUser->clients_status == 2 || $getUser->clients_status == 3){
						
						return array( "status" => false, "status_user" => $getUser->clients_status );
						
						}else{
						
						$_SESSION['profile']['id'] = $getUser->clients_id;
						
						return array( "status" => true, "reg" => 1, "data" => $getUser );
						
					}
					
				}
				
				}elseif($array["method"] == 2 || $array["method"] == 3){
				
				if($array["email"] && $array["phone"]){
					$getUser = findOne("uni_clients", "clients_email=? or clients_phone=?", [$array["email"],$array["phone"]]);
					}else{
					$getUser = findOne("uni_clients", "clients_email=?", [$array["email"]]);
				}
				
				if($getUser){
					
					if($getUser->clients_status == 2 || $getUser->clients_status == 3){
						
						return array( "status" => false, "status_user" => $getUser->clients_status );
						
						}else{
						
						if($array["network"]){
							
							$_SESSION['profile']['id'] = $getUser->clients_id;
							
							return array( "status" => true, "reg" => 1, "data" => $getUser );
							
							}else{
							
							if (password_verify($array["pass"].$config["private_hash"], $getUser->clients_pass)) {  
								
								$_SESSION['profile']['id'] = $getUser->clients_id;
								
								return array( "status" => true, "reg" => 1, "data" => $getUser );
								
								}else{
								
								return array( "status" => false, "answer" => $ULang->t("Неверный логин и(или) пароль!") );
								
							}
							
						}
						
					}           
					
				}
				
			}
			
			$notifications = '{"messages":"1","answer_comments":"1","services":"1","answer_ad":"1"}';
			
			if(empty($array["pass"])){ $pass = generatePass(10); }else{ $pass = $array["pass"]; }
			$password_hash =  password_hash($pass.$config["private_hash"], PASSWORD_DEFAULT);
			
			if(!$array["name"]){
				if($array["email"]){
					$array["name"] = explode("@", $array["email"])[0];
					}else{
					$array["name"] = $array["phone"];
				}
			}
			
			$clients_id_hash = md5($array["email"] ? $array["email"] : $array["phone"]);
			
			$insert_id = insert("INSERT INTO uni_clients(clients_pass,clients_email,clients_phone,clients_name,clients_surname,clients_ip,clients_id_hash,clients_status,clients_datetime_add,clients_notifications,clients_social_identity,clients_avatar,clients_ref_id)VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?)", array($password_hash,$array["email"],$array["phone"],$array["name"],$array["surname"],clear($_SERVER["REMOTE_ADDR"]),$clients_id_hash,intval($array["activation"]), date("Y-m-d H:i:s"), $notifications,$array["social_link"],$array["avatar"],genRefId())); 
			
			$_SESSION['profile']['id'] = $insert_id;
			
			if(isset($_COOKIE["ref_id"])){
				$ref_id = intval($_COOKIE["ref_id"]);
				}elseif($_SESSION['ref_id']){
				$ref_id = intval($_SESSION['ref_id']);
				}else{
				$getReferrer = findOne('uni_clients_ref_transitions', 'ip=?', [$_SERVER["REMOTE_ADDR"]]);
				if($getReferrer){
					$ref_id = $getReferrer['id_user_referrer'];
				}
			}
			
			if($ref_id){
				smart_insert('uni_clients_ref', [
				'timestamp' => date("Y-m-d H:i:s"),
				'id_user_referral' => $insert_id,
				'id_user_referrer' => $ref_id,
				]);   
			}
			
			if($settings["bonus_program"]["register"]["status"] && $settings["bonus_program"]["register"]["price"]){
				$this->actionBalance(array("id_user"=>$insert_id,"summa"=>$settings["bonus_program"]["register"]["price"],"title"=>$settings["bonus_program"]["register"]["name"],"id_order"=>generateOrderId(),"email" => $array["email"],"name" => $array["name"], "note" => $settings["bonus_program"]["register"]["name"]),"+");             
			}
			
			notifications("user", array("user_name" => $array["name"], "user_email" => $array["email"], "user_phone" => $array["phone"]));
			$Admin->addNotification("user");   
			
			$Subscription->add(array("email"=>$array["email"],"user_id"=>$insert_id,"name"=>$array["name"],"status" => 1));
			
			return array( "status" => true, "data" => findOne("uni_clients", "clients_id=?", [$insert_id]) );    
			
		}
		
		
		function actionBalance($array=array(),$action=""){
			global $settings;   
			
			$Main = new Main();
			if(!$array["id_order"]) $array["id_order"] = generateOrderId();
			
			if($array["note"]){
				$note = '<p>'.$array["note"].'</p>';
			}
			
			if(!empty($array["id_user"])){
				if($action == "+"){
					$check = findOne("uni_history_balance","id_order=? AND id_user=?", [$array["id_order"],$array["id_user"]]);  
					if(!$check){
						
						update("UPDATE uni_clients SET clients_balance=clients_balance+{$array["summa"]} WHERE clients_id=?", [$array["id_user"]]); 
						
						$this->profileAddHistoryBalance($array,"+");
						
						$param      = array("{USER_NAME}"=>$array["name"],
						"{USER_EMAIL}"=>$array["email"],
						"{SUMMA}"=>$Main->price($array["summa"]),
						"{NOTE}"=>$note,
						"{UNSUBCRIBE}"=>"",
						"{EMAIL_TO}"=>$array["email"]); 
						
						email_notification( array( "variable" => $param, "code" => "BALANCE" ) );
						
						return true;
						
					}else{ return false; }   
					}else{
					
					update("UPDATE uni_clients SET clients_balance=clients_balance-{$array["summa"]} WHERE clients_id=?",[$array["id_user"]]); 
					
					$this->profileAddHistoryBalance($array,"-");
					
					return true;
					
				} 
			}else{ return false; }  
			
		}
		
		function profileAddHistoryBalance($array=array(),$action=""){
			insert("INSERT INTO uni_history_balance(id_user,summa,method,name,action,datetime)VALUES(?,?,?,?,?,?)", array($array["id_user"],$array["summa"],$array["method"],$array["title"],$action,date("Y-m-d H:i:s"))); 
		}
		
		function name($data = [], $shopConditions = true){
			
			$Shop = new Shop();
			
			if($shopConditions && isset($data["clients_id"])){
				$getShop = $Shop->getShop(['user_id'=>$data["clients_id"],'conditions'=>true]);
				
				if($getShop){
					return $getShop["clients_shops_title"];
				}
			}
			
			if($data["clients_surname"]!=""){
				$clients_surname = mb_strtoupper(mb_substr( $data["clients_surname"] , 0,1, "UTF-8" ) ,"UTF-8") . ".";
				return $data["clients_name"] . ' ' . $clients_surname;
				}else{
				return $data["clients_name"];
			}
			
		}    
		
		function userAvatar($data = [], $shopConditions = true){
			global $config;  
			
			$Shop = new Shop();
			
			if($shopConditions && isset($data["clients_id"])){
				$getShop = $Shop->getShop(['user_id'=>$data["clients_id"],'conditions'=>true]);
				if($getShop){
					if($getShop["clients_shops_logo"]){
						return Exists($config["media"]["other"], $getShop["clients_shops_logo"], $config["media"]["no_avatar"]);
					}
				}
			}
			
			if(preg_match('/^(http|https|ftp):[\/]{2}/i', urldecode($data["clients_avatar"]))){
				return urldecode($data["clients_avatar"]);       
				}else{
				return Exists($config["media"]["avatar"],$data["clients_avatar"],$config["media"]["no_avatar"]);
			}
			
		}
		
		function downAvatar($link = "", $id = 0){
			global $config;
			$path = $config["basePath"] . "/" . $config["media"]["avatar"];
			if($link && $id){
				$link = file_get_contents_curl(urldecode($link));
				if($link){
					file_put_contents($path . "/" . md5($id) . ".jpg", $link);
					update("UPDATE uni_clients SET clients_avatar=? WHERE clients_id=?", array( md5($id) . ".jpg" ,$id));
				}
			}
		}
		
		function outRating($id_user=0,$rating=0){
			
			if($id_user) $rating = $this->ratingBalls($id_user);
			
			if($rating == 1){
				return '
				<span class="ion-ios-star" ></span>
				<span class="ion-ios-star-outline" ></span>
				<span class="ion-ios-star-outline" ></span>
				<span class="ion-ios-star-outline" ></span>
				<span class="ion-ios-star-outline" ></span>
				';
				}elseif($rating == 2){
				return '
				<span class="ion-ios-star" ></span>
				<span class="ion-ios-star" ></span>
				<span class="ion-ios-star-outline" ></span>
				<span class="ion-ios-star-outline" ></span>
				<span class="ion-ios-star-outline" ></span>
				';
				}elseif($rating == 3){
				return '
				<span class="ion-ios-star" ></span>
				<span class="ion-ios-star" ></span>
				<span class="ion-ios-star" ></span>
				<span class="ion-ios-star-outline" ></span>
				<span class="ion-ios-star-outline" ></span>
				';
				}elseif($rating == 4){
				return '
				<span class="ion-ios-star" ></span>
				<span class="ion-ios-star" ></span>
				<span class="ion-ios-star" ></span>
				<span class="ion-ios-star" ></span>
				<span class="ion-ios-star-outline" ></span>
				';            
				}elseif($rating == 5){
				return '
				<span class="ion-ios-star" ></span>
				<span class="ion-ios-star" ></span>
				<span class="ion-ios-star" ></span>
				<span class="ion-ios-star" ></span>
				<span class="ion-ios-star" ></span>
				';            
				}else{
				return '
				<span class="ion-ios-star-outline" ></span>
				<span class="ion-ios-star-outline" ></span>
				<span class="ion-ios-star-outline" ></span>
				<span class="ion-ios-star-outline" ></span>
				<span class="ion-ios-star-outline" ></span>
				';
			}
			
		}
		
		function ratingBalls($id_user){
			
			$array = [];
			
			$getReviews = getAll("select * from uni_clients_reviews where clients_reviews_status = ? and clients_reviews_id_user = ?", [1,intval($id_user)]);
			
			if(count($getReviews)){
				foreach ($getReviews as $key => $value) {
					
					if(isset($array["total_rating"])){
						$array["total_rating"] += $value["clients_reviews_rating"];
						}else{
						$array["total_rating"] = 1;
					}
					
					if(isset($array["rating_".$value["clients_reviews_rating"]])){
						$array["rating_".$value["clients_reviews_rating"]] += $value["clients_reviews_rating"];
						}else{
						$array["rating_".$value["clients_reviews_rating"]] = 1;
					}
					
				}
			}
			
			$array["rating_1"] = isset($array["rating_1"]) ? $array["rating_1"] : 0;
			$array["rating_2"] = isset($array["rating_2"]) ? $array["rating_2"] : 0;
			$array["rating_3"] = isset($array["rating_3"]) ? $array["rating_3"] : 0;
			$array["rating_4"] = isset($array["rating_4"]) ? $array["rating_4"] : 0;
			$array["rating_5"] = isset($array["rating_5"]) ? $array["rating_5"] : 0;
			
			if($array["total_rating"]){
				$result = ($array["rating_1"]*1+$array["rating_2"]*2+$array["rating_3"]*3+$array["rating_4"]*4+$array["rating_5"]*5)/$array["total_rating"];
				}else{
				$result = 0;
			}
			
			if($result <= 5){
				return number_format($result, 0, '.', '');
				}else{
				return number_format(5, 0, '.', '');
			}
			
			
		}
		
		
		function arrayMenu(){
			global $settings;
			
			$ULang = new ULang();
			$menu = [];
			
			$menu['balance'] = [ "link"=>_link( "user/" . $_SESSION["profile"]["data"]["clients_id_hash"] . "/balance" ), "name"=> $ULang->t("Кошелек"), "icon" => '<svg width="24" height="24" viewBox="0 0 24 24"><path fill="#333" fill-rule="evenodd" d="M21 19.5a1.5 1.5 0 0 1-1.5 1.5H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h12.5A1.5 1.5 0 0 1 19 4.5V6h.5A1.5 1.5 0 0 1 21 7.5v12zM19.5 7H4v12a1 1 0 0 0 1 1h14.5a.5.5 0 0 0 .5-.5V16h-3.5a2.5 2.5 0 1 1 0-5H20V7.5a.5.5 0 0 0-.5-.5zm.5 5h-3.5a1.5 1.5 0 0 0 0 3H20v-3zm-2.5-8H5a1 1 0 1 0 0 2h13V4.5a.5.5 0 0 0-.5-.5z"></path></svg>' ];
			
			$nested["tariff"] = ["link"=>_link("user/" . $_SESSION["profile"]["data"]["clients_id_hash"] . "/tariff"),"icon" => '<svg width="24" height="24" ></svg>', "name" => $ULang->t("Тариф")];
			
			if($settings["user_shop_status"]){
				$nested[$settings['user_shop_alias_url_page']] = ["link"=>_link( "user/" . $_SESSION["profile"]["data"]["clients_id_hash"]."/".$settings['user_shop_alias_url_page'] ),"icon" => '<svg width="24" height="24" ></svg>', "name" => $ULang->t("Магазин")];
			}
			
			$nested["statistics"] = ["link"=>_link( "user/" . $_SESSION["profile"]["data"]["clients_id_hash"] . "/statistics" ),"icon" => '<svg width="24" height="24" ></svg>', "name" => $ULang->t("Статистика")];
			$nested["scheduler"] = ["link"=>_link( "user/" . $_SESSION["profile"]["data"]["clients_id_hash"] . "/scheduler" ),"icon" => '<svg width="24" height="24" ></svg>', "name" => $ULang->t("Планировщик задач")];
			
			if($settings["functionality"]["booking"] && $settings["booking_status"]){
				$nested["booking_calendar"] = ["link"=>_link( "user/" . $_SESSION["profile"]["data"]["clients_id_hash"] . "/booking-calendar" ),"icon" => '<svg width="24" height="24" ></svg>', "name" => $ULang->t("Календарь")];
			}
			
			$menu['services'] = ["name"=>$ULang->t("Бизнес-услуги"), "icon" => '<svg width="24" height="24" viewBox="0 0 24 24" class="sc-biOYSp jfKzIP"><path fill="#333" fill-rule="evenodd" d="M12 2c5.523 0 10 4.477 10 10s-4.477 10-10 10S2 17.523 2 12 6.477 2 12 2zm0 1a9 9 0 1 0 .001 18.001A9 9 0 0 0 12 3zm.732 3.667l.326.007c.426.02.822.08 1.186.18a3.26 3.26 0 0 1 1.17.569c.326.255.58.577.765.967.184.39.276.857.276 1.399s-.097 1.013-.292 1.414a2.842 2.842 0 0 1-.797 1 3.389 3.389 0 0 1-1.187.594c-.455.13-.949.195-1.48.195h-1.642v1.642h2.618v1.073h-2.618V18h-1.3v-2.293H8.601v-1.073h1.154v-1.642H8.602v-1.138h1.154V6.667h2.976zm0 1.138h-1.675v4.049h1.675c.748 0 1.336-.171 1.764-.513.428-.341.642-.859.642-1.552 0-.694-.214-1.198-.642-1.513-.428-.314-1.016-.471-1.764-.471z"></path></svg>', "nested" => $nested];
			
			$menu['ad'] = [ "link"=>_link( "user/" . $_SESSION["profile"]["data"]["clients_id_hash"] . "/ad" ), "name" => $ULang->t("Объявления"), "icon" => '<svg width="24" height="24" viewBox="0 0 24 24"><path fill="#333" fill-rule="evenodd" d="M6.077 14c.669 0 .911.07 1.156.2.244.131.436.323.567.567.13.245.2.487.2 1.156v1.154c0 .669-.07.911-.2 1.156a1.371 1.371 0 0 1-.567.567c-.245.13-.487.2-1.156.2H4.923c-.669 0-.911-.07-1.156-.2a1.363 1.363 0 0 1-.567-.567c-.13-.245-.2-.487-.2-1.156v-1.154c0-.669.07-.911.2-1.156.131-.244.323-.436.567-.567.245-.13.487-.2 1.156-.2h1.154zm.14 1H4.924c-.459 0-.57.022-.684.082a.364.364 0 0 0-.157.157c-.054.1-.077.2-.081.543L4 17.077c0 .459.022.57.082.684.038.07.087.12.157.157.113.06.225.082.684.082h1.154c.459 0 .57-.022.684-.082a.364.364 0 0 0 .157-.157c.06-.113.082-.225.082-.684v-1.154c0-.459-.022-.57-.082-.684a.364.364 0 0 0-.157-.157c-.1-.054-.2-.077-.543-.081zM21 16v1H10v-1h11zM6.077 5c.669 0 .911.07 1.156.2.244.131.436.323.567.567.13.245.2.487.2 1.156v1.154c0 .669-.07.911-.2 1.156a1.371 1.371 0 0 1-.567.567c-.245.13-.487.2-1.156.2H4.923c-.669 0-.911-.07-1.156-.2a1.363 1.363 0 0 1-.567-.567c-.13-.245-.2-.487-.2-1.156V6.923c0-.669.07-.911.2-1.156.131-.244.323-.436.567-.567.245-.13.487-.2 1.156-.2h1.154zm.14 1H4.924c-.459 0-.57.022-.684.082a.364.364 0 0 0-.157.157c-.054.1-.077.2-.081.543L4 8.077c0 .459.022.57.082.684.038.07.087.12.157.157.113.06.225.082.684.082h1.154c.459 0 .57-.022.684-.082a.364.364 0 0 0 .157-.157c.06-.113.082-.225.082-.684V6.923c0-.459-.022-.57-.082-.684a.364.364 0 0 0-.157-.157c-.1-.054-.2-.077-.543-.081zM21 7v1H10V7h11z"></path></svg>' ];
			
			if($settings["functionality"]["booking"] && $settings["booking_status"]){
				$menu['orders'] = ["name"=>$ULang->t("Заказы"), "icon" => '<svg width="24" height="24" viewBox="0 0 24 24"><path fill="#333" fill-rule="evenodd" d="M9.5 19a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3zm7 0a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3zm-7 1a.5.5 0 1 0 0 1 .5.5 0 0 0 0-1zm7 0a.5.5 0 1 0 0 1 .5.5 0 0 0 0-1zm-9.87-5.27A1.5 1.5 0 0 0 8.113 16H20v1H8.113a2.5 2.5 0 0 1-2.47-2.115L4.033 4.558l-2.06-1.78.653-.756L4.917 4H20.18a1.5 1.5 0 0 1 1.471 1.794l-1.24 6.196a2.5 2.5 0 0 1-2.45 2.01H8v-1h9.96a1.5 1.5 0 0 0 1.471-1.206l1.24-6.196A.5.5 0 0 0 20.18 5H5.113l1.516 9.73z"></path></svg>', "nested" => [
				
				"orders"=>["link"=>_link("user/" . $_SESSION["profile"]["data"]["clients_id_hash"] . "/orders"),"icon" => '<svg width="24" height="24" ></svg>', "name" => $ULang->t("Все заказы")],
				"booking"=>["link"=>_link( "user/" . $_SESSION["profile"]["data"]["clients_id_hash"] . "/booking" ),"icon" => '<svg width="24" height="24" ></svg>', "name" => $ULang->t("Бронирования")],
				
				]];
				}else{
				$menu['orders'] = ["link"=>_link( "user/" . $_SESSION["profile"]["data"]["clients_id_hash"] . "/orders" ), "name" => $ULang->t("Заказы"), "icon" => '<svg width="24" height="24" viewBox="0 0 24 24"><path fill="#333" fill-rule="evenodd" d="M9.5 19a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3zm7 0a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3zm-7 1a.5.5 0 1 0 0 1 .5.5 0 0 0 0-1zm7 0a.5.5 0 1 0 0 1 .5.5 0 0 0 0-1zm-9.87-5.27A1.5 1.5 0 0 0 8.113 16H20v1H8.113a2.5 2.5 0 0 1-2.47-2.115L4.033 4.558l-2.06-1.78.653-.756L4.917 4H20.18a1.5 1.5 0 0 1 1.471 1.794l-1.24 6.196a2.5 2.5 0 0 1-2.45 2.01H8v-1h9.96a1.5 1.5 0 0 0 1.471-1.206l1.24-6.196A.5.5 0 0 0 20.18 5H5.113l1.516 9.73z"></path></svg>'];
			}
			
			$menu['chat'] = ["modal_id"=>"modal-chat-user", "name" => $ULang->t("Сообщения").'<span class="chat-message-counter label-count" ></span>', "icon" => '<svg width="24" height="24" viewBox="0 0 24 24"><path fill="#333" fill-rule="evenodd" d="M18.936 4c.888 0 1.324.084 1.777.326.413.221.74.548.96.961.243.453.327.889.327 1.777v9.872c0 .888-.084 1.324-.326 1.777a2.31 2.31 0 0 1-.961.96c-.453.243-.889.327-1.777.327H5.064c-.888 0-1.324-.084-1.777-.326a2.317 2.317 0 0 1-.96-.961C2.083 18.26 2 17.824 2 16.936V7.064c0-.888.084-1.324.326-1.777a2.31 2.31 0 0 1 .961-.96C3.74 4.083 4.176 4 5.064 4h13.872zm0 1H5.064c-.737 0-1.017.054-1.305.208a1.317 1.317 0 0 0-.551.551C3.054 6.047 3 6.327 3 7.064v9.872c0 .737.054 1.017.208 1.305.128.239.312.423.551.551.288.154.568.208 1.305.208h13.872c.737 0 1.017-.054 1.305-.208.239-.128.423-.312.551-.551.154-.288.208-.568.208-1.305V7.064c0-.737-.054-1.017-.208-1.305a1.317 1.317 0 0 0-.551-.551C19.953 5.054 19.673 5 18.936 5zm-.535 2l.598.8L12 13.025 5.001 7.8l.598-.802L12 11.776 18.401 7z"></path></svg>'];
			
			$menu['favorites'] = ["link"=>_link( "user/" . $_SESSION["profile"]["data"]["clients_id_hash"] . "/favorites" ), "name" => $ULang->t("Избранное"), "icon" => '<svg width="24" height="24" viewBox="0 0 24 24"><path fill="#333" fill-rule="evenodd" d="M12.333 5.673L12 5.97l-.333-.298C10.487 4.618 9.229 4 8 4 4.962 4 3 5.962 3 9c0 4.01 3.244 7.656 8.842 10.975a.4.4 0 0 0 .326-.004C17.772 16.615 21 12.996 21 9c0-3.038-1.962-5-5-5-1.229 0-2.488.618-3.667 1.673zM16 3c3.59 0 6 2.41 6 6 0 4.452-3.44 8.308-9.311 11.824-.394.246-.98.246-1.366.006C5.46 17.353 2 13.466 2 9c0-3.59 2.41-6 6-6 1.39 0 2.746.61 4 1.641C13.254 3.61 14.61 3 16 3z"></path></svg>'];
			
			$menu['settings'] = ["link"=>_link( "user/" . $_SESSION["profile"]["data"]["clients_id_hash"] . "/settings" ), "name" => $ULang->t("Настройки"), "icon" => '<svg width="24" height="24" viewBox="0 0 24 24"><path fill="#333" fill-rule="evenodd" d="M14.841 5.975l-.063-.17-.125-.334-.276-.738a48.538 48.538 0 0 0-.4-1.049c-.16-.38-.63-.684-1.081-.684h-1.792c-.45 0-.918.305-1.1.726a475.491 475.491 0 0 0-.652 1.746l-.121.332-.062.168-1.018.597-.18-.03-.35-.06-.776-.133a51.697 51.697 0 0 0-1.156-.189c-.418-.054-.92.197-1.144.58L3.65 8.268c-.222.38-.188.927.09 1.291.017.025.308.374.7.838l.508.603.228.27.127.15-.009.195-.005.148-.003.086a5.479 5.479 0 0 0 .006.516l.017.207-.134.158-.231.274c-.173.205-.347.41-.508.603-.403.48-.688.821-.724.87-.253.327-.284.874-.062 1.255l.896 1.531c.224.383.726.63 1.19.575.03-.003.482-.079 1.085-.182l.785-.135.343-.06.176-.03 1.032.58.064.172a421.646 421.646 0 0 0 .402 1.079c.22.586.376.999.402 1.054.16.38.63.684 1.08.684h1.792c.45 0 .918-.305 1.1-.726.012-.028.171-.448.382-1.01l.277-.741.123-.332.062-.168 1.015-.59.178.03a17398.007 17398.007 0 0 0 1.93.322l.342.057c.424.055.926-.195 1.15-.579l.896-1.531c.222-.38.188-.927-.09-1.291a69.29 69.29 0 0 0-.699-.833l-.508-.6-.23-.27-.126-.149.008-.196.006-.151.003-.088a5.786 5.786 0 0 0-.001-.45 2.582 2.582 0 0 0-.004-.077l-.014-.203.131-.156.231-.273c.173-.205.347-.41.508-.603.403-.479.687-.82.723-.87.253-.326.284-.873.062-1.254l-.896-1.531c-.224-.383-.726-.63-1.19-.575-.031.003-.483.078-1.088.18l-.786.135c-.276.047-.276.047-.344.06l-.178.03-1.028-.592zm1.214-.455a10307.99 10307.99 0 0 1 .931-.16 44.378 44.378 0 0 1 1.174-.192c.843-.102 1.732.335 2.158 1.064l.896 1.531c.427.73.371 1.72-.124 2.358-.049.068-.3.37-.752.907l-.516.612-.106.126a11.75 11.75 0 0 1-.002.417.409.409 0 0 0 .102.177l.504.595c.47.554.711.843.746.892.514.674.575 1.659.148 2.39l-.896 1.531c-.426.729-1.317 1.172-2.126 1.068l-.393-.065-.786-.131-.967-.162-.393.229-.061.165-.273.73a42.104 42.104 0 0 1-.412 1.081c-.333.775-1.162 1.317-2.011 1.317h-1.792c-.848 0-1.684-.54-1.995-1.28-.035-.076-.173-.44-.418-1.093l-.283-.758-.062-.166-.402-.226a10061.036 10061.036 0 0 1-.931.16 41.73 41.73 0 0 1-1.173.195c-.843.102-1.732-.336-2.158-1.064l-.896-1.531c-.427-.73-.371-1.72.124-2.358.05-.068.3-.37.753-.908l.516-.612.106-.125a10.03 10.03 0 0 1 0-.413c-.024-.083-.058-.123-.102-.176l-.504-.597c-.468-.555-.71-.846-.745-.894a2.203 2.203 0 0 1-.148-2.39l.896-1.532c.425-.727 1.316-1.172 2.117-1.068.085.007.48.072 1.184.191l.806.138.17.029.395-.232.06-.164c.092-.248.185-.497.27-.73a40.21 40.21 0 0 1 .408-1.079C9.426 2.542 10.255 2 11.104 2h1.792c.848 0 1.684.54 1.995 1.28.035.076.173.438.417 1.087l.282.754.062.167.403.232zM12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm0 1a4 4 0 1 1 0-8 4 4 0 0 1 0 8z"></path></svg>'];
			
			$menu['subscriptions'] = ["link"=>_link( "user/" . $_SESSION["profile"]["data"]["clients_id_hash"] . "/subscriptions" ), "name" => $ULang->t("Подписки"), "icon" => '<svg width="24" height="24" viewBox="0 0 24 24"><path d="M17.876 13.145c.948.431 1.69.952 2.221 1.566.55.637.854 1.353.903 2.167v2.352l-.212.15c-2.424 1.706-5.358 2.557-8.788 2.557-3.42 0-6.354-.851-8.787-2.557L3 19.23v-.26l.001-2.127c.05-.72.329-1.382.828-1.976.475-.564 1.199-1.127 2.174-1.698l.506.862c-.885.519-1.524 1.015-1.915 1.48-.366.435-.56.898-.594 1.367v1.829c2.216 1.486 4.879 2.23 8 2.23 3.13 0 5.793-.744 8-2.23v-1.799c-.033-.558-.25-1.068-.66-1.543-.428-.496-1.053-.935-1.878-1.31l.414-.91zM12.05 2c1 0 1.89.258 2.637.755l-.554.833C13.554 3.202 12.856 3 12.05 3 9.316 3 7.768 5.441 8.158 8.945 8.56 12.577 10.095 15 12.05 15c1.363 0 2.522-1.146 3.255-3.187l.941.337c-.86 2.396-2.33 3.85-4.196 3.85-2.617 0-4.433-2.869-4.886-6.945C6.715 5.021 8.63 2 12.05 2zM17 3v3h3v1h-3v3h-1V7h-3V6h3V3h1z" fill="#333" fill-rule="evenodd"></path></svg>'];
			
			$menu['subscriptions'] = ["link"=>_link( "user/" . $_SESSION["profile"]["data"]["clients_id_hash"] . "/subscriptions" ), "name" => $ULang->t("Подписки"), "icon" => '<svg width="24" height="24" viewBox="0 0 24 24"><path d="M17.876 13.145c.948.431 1.69.952 2.221 1.566.55.637.854 1.353.903 2.167v2.352l-.212.15c-2.424 1.706-5.358 2.557-8.788 2.557-3.42 0-6.354-.851-8.787-2.557L3 19.23v-.26l.001-2.127c.05-.72.329-1.382.828-1.976.475-.564 1.199-1.127 2.174-1.698l.506.862c-.885.519-1.524 1.015-1.915 1.48-.366.435-.56.898-.594 1.367v1.829c2.216 1.486 4.879 2.23 8 2.23 3.13 0 5.793-.744 8-2.23v-1.799c-.033-.558-.25-1.068-.66-1.543-.428-.496-1.053-.935-1.878-1.31l.414-.91zM12.05 2c1 0 1.89.258 2.637.755l-.554.833C13.554 3.202 12.856 3 12.05 3 9.316 3 7.768 5.441 8.158 8.945 8.56 12.577 10.095 15 12.05 15c1.363 0 2.522-1.146 3.255-3.187l.941.337c-.86 2.396-2.33 3.85-4.196 3.85-2.617 0-4.433-2.869-4.886-6.945C6.715 5.021 8.63 2 12.05 2zM17 3v3h3v1h-3v3h-1V7h-3V6h3V3h1z" fill="#333" fill-rule="evenodd"></path></svg>'];
			
			if($settings["referral_program_status"]){
				$menu['referral'] = ["link"=>_link( "user/" . $_SESSION["profile"]["data"]["clients_id_hash"] . "/ref" ), "name" => $ULang->t("Реф. программа"), "icon" => '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="19px" height="19px" viewBox="0 0 19 19" version="1.1"><g id="surface1"><path style=" stroke:none;fill-rule:nonzero;fill:rgb(0%,0%,0%);fill-opacity:1;" d="M 17.589844 8.148438 L 14.566406 11.175781 C 14.367188 11.375 14.066406 11.375 13.867188 11.175781 C 13.664062 10.976562 13.664062 10.675781 13.867188 10.476562 L 16.890625 7.449219 C 18.367188 5.976562 18.367188 3.574219 16.890625 2.101562 C 16.164062 1.375 15.242188 1 14.214844 1 C 13.214844 1 12.265625 1.398438 11.539062 2.101562 L 8.515625 5.125 C 7.039062 6.601562 7.039062 9 8.515625 10.476562 C 8.867188 10.824219 9.289062 11.125 9.765625 11.300781 C 10.015625 11.398438 10.140625 11.699219 10.039062 11.949219 C 9.816406 12.476562 9.242188 12.175781 9.390625 12.226562 C 8.789062 11.976562 8.265625 11.625 7.816406 11.175781 C 5.941406 9.324219 5.941406 6.273438 7.816406 4.425781 L 10.839844 1.398438 C 11.742188 0.5 12.941406 0 14.214844 0 C 15.492188 0 16.691406 0.5 17.589844 1.398438 C 19.464844 3.25 19.464844 6.300781 17.589844 8.148438 Z M 9.589844 6.773438 C 9.339844 6.675781 9.039062 6.800781 8.941406 7.050781 C 8.839844 7.300781 8.964844 7.601562 9.214844 7.699219 C 9.691406 7.898438 10.117188 8.175781 10.464844 8.523438 C 11.941406 10 11.941406 12.398438 10.464844 13.875 L 7.441406 16.898438 C 6.714844 17.625 5.789062 18 4.765625 18 C 3.765625 18 2.816406 17.601562 2.089844 16.898438 C 0.617188 15.425781 0.617188 13.023438 2.089844 11.550781 L 5.117188 8.523438 C 5.316406 8.324219 5.316406 8.023438 5.117188 7.824219 C 4.914062 7.625 4.617188 7.625 4.414062 7.824219 L 1.390625 10.851562 C -0.460938 12.699219 -0.460938 15.75 1.390625 17.601562 C 2.289062 18.5 3.492188 19 4.765625 19 C 6.039062 19 7.242188 18.5 8.140625 17.601562 L 11.164062 14.574219 C 13.039062 12.726562 13.039062 9.675781 11.164062 7.824219 C 10.714844 7.375 10.191406 7.023438 9.589844 6.773438 Z M 9.589844 6.773438 "/></g></svg>'];
			}
			
			$menu['logout'] = ["link"=>_link( "user/" . $_SESSION["profile"]["data"]["clients_id_hash"] . "/?logout=1" ), "name" => $ULang->t("Выход"), "icon" => '<svg width="24" height="24" viewBox="0 0 24 24"><path fill="#333" d="M14.936 2c.888 0 1.324.084 1.777.326.413.221.74.548.96.961.243.453.327.889.327 1.777V8h-1V5.064c0-.737-.054-1.017-.208-1.305a1.319 1.319 0 0 0-.551-.551C15.953 3.054 15.673 3 14.936 3l-6.375-.001 1.773 1.007c.558.317.8.495 1.036.753.223.245.384.522.487.837.109.332.143.631.143 1.273v11.13l2.936.001c.737 0 1.017-.054 1.305-.208.239-.128.423-.312.551-.551.154-.288.208-.568.208-1.305V13h1v2.936c0 .888-.084 1.324-.326 1.777a2.31 2.31 0 0 1-.961.96c-.453.243-.889.327-1.777.327L12 18.999v.484a2.5 2.5 0 0 1-3.735 2.173L5.666 20.18c-.558-.317-.8-.495-1.036-.753a2.276 2.276 0 0 1-.487-.837C4.034 18.258 4 17.959 4 17.317V4.703c0-.126.01-.25.027-.371.043-.438.135-.738.3-1.045.22-.413.547-.74.96-.96C5.74 2.083 6.176 2 7.064 2h7.872zM6.5 3.203c-.75 0-1.373.552-1.483 1.272-.011.158-.017.35-.017.589v10.872h-.001L5 17.317c0 .546.023.747.093.962.06.181.149.334.277.475.152.167.316.287.79.556l2.599 1.477A1.5 1.5 0 0 0 11 19.483V6.869c0-.546-.023-.747-.093-.962a1.292 1.292 0 0 0-.277-.475c-.152-.167-.316-.287-.79-.556L7.241 3.399a1.503 1.503 0 0 0-.741-.196zM20 8.493l1.3 1.3.707.707L20 12.507l-.707-.707.798-.8H14v-1h6.093l-.8-.8.707-.707z"></path></svg>'];
			
			return $menu;
			
		}
		
		function menuPageName( $action = "" ){
			global $settings;
			
			$ULang = new ULang();
			
			if($action == "ad"){
				return $ULang->t("Объявления");
				}elseif($action == "orders"){
				return $ULang->t("Заказы");
				}elseif($action == "favorites"){
				return $ULang->t("Избранное");
				}elseif($action == "settings"){
				return $ULang->t("Настройки");
				}elseif($action == "balance"){
				return $ULang->t("Кошелек");
				}elseif($action == "history"){
				return $ULang->t("История платежей");
				}elseif($action == "reviews"){
				return $ULang->t("Отзывы");
				}elseif($action == "subscriptions"){
				return $ULang->t("Подписки");
				}elseif($action == $settings['user_shop_alias_url_page']){
				return $ULang->t("Магазин");
				}elseif($action == "statistics"){
				return $ULang->t("Статистика");
				}elseif($action == "scheduler"){
				return $ULang->t("Планировщик задач");
				}elseif($action == "tariff"){
				return $ULang->t("Тариф");
				}elseif($action == "booking"){
				return $ULang->t("Бронирования");
				}elseif($action == "ref"){
				return $ULang->t("Реферальная программа");
				}else{
				return $ULang->t("Объявления");
			}
			
		}
		
		function headerUserMenu( $name = true ){
			
			$ULang = new ULang();
			$Main = new Main();
			$menu = $this->arrayMenu();
			
			if(isset($_SESSION["profile"]["id"])){
				
				foreach ($menu as $page => $value) {
					
					if(isset($value['nested'])){
						
						$links .= '<div class="dropdown-box-list-nested-toggle" ><a href="#" >'.$value['icon'].' '.$value["name"].' <i class="las la-angle-down"></i></a><div class="dropdown-box-list-nested" >';
						
						foreach ($value['nested'] as $page_nested => $nested){
							
							if($nested["link"]){
								$links .= '<a href="'.$nested["link"].'" >'.$nested["icon"].' '.$nested["name"].'</a>';
								}else{
								$links .= '<a href="#" class="open-modal" data-id-modal="'.$nested["modal_id"].'" >'.$nested["icon"].' '.$nested["name"].'</a>';
							}
							
						}
						
						$links .= '</div></div>';
						
						}else{
						
						if($page == "balance"){
							$links .= '<a href="'.$value["link"].'" >'.$value["icon"].' '.$value["name"].' '.$Main->price($_SESSION["profile"]["data"]["clients_balance"]).'</a>';
							}else{
							if($value["link"]){
								$links .= '<a href="'.$value["link"].'" >'.$value["icon"].' '.$value["name"].'</a>';
								}else{
								$links .= '<a href="#" class="open-modal" data-id-modal="'.$value["modal_id"].'" >'.$value["icon"].' '.$value["name"].'</a>';
							}
						}
						
					}
					
				}
				
				if($name){
					$user_name = '<span class="mini-avatar-name" >'.$this->name($_SESSION["profile"]["data"], false).'</span>';
				}
				
				return '
				<div class="toolbar-dropdown dropdown-click">
				<span> <span class="mini-avatar" > <span class="mini-avatar-img" ><img src="'. $this->userAvatar($_SESSION["profile"]["data"], false).'" /></span> </span> '.$user_name.'</span>
				<div class="toolbar-dropdown-box toolbar-dropdown-js width-300 right-0 no-padding" >
				
				<div class="dropdown-box-list-link" >
				'.$links.'
				</div>
				
				</div>
				</div>
				';
				}else{
				return '
				<a class="toolbar-link-icon" href="'._link("auth").'" title="'.$ULang->t("Войти в личный кабинет").'" ><i class="las la-sign-in-alt"></i></a>
				';
			}
		}
		
		function outUserMenu($data=[],$balance=0){
			
			$Main = new Main();
			
			if($data["menu_links"]){
				
				foreach ($data["menu_links"] as $page => $value) {
					
					if($value['nested']){
						
						$links .= '<div class="dropdown-box-list-nested-toggle" ><a href="#" >'.$value['icon'].' '.$value["name"].' <i class="las la-angle-down"></i></a><div class="dropdown-box-list-nested" >';
						
						foreach ($value['nested'] as $page_nested => $nested){
							
							if($nested["link"]){
								$links .= '<a href="'.$nested["link"].'" >'.$nested["icon"].' '.$nested["name"].'</a>';
								}else{
								$links .= '<a href="#" class="open-modal" data-id-modal="'.$nested["modal_id"].'" >'.$nested["icon"].' '.$nested["name"].'</a>';
							}
							
						}
						
						$links .= '</div></div>';
						
						}else{
						
						if($page == "balance"){
							$links .= '<a href="'.$value["link"].'" >'.$value["icon"].' '.$value["name"].' '.$Main->price($balance).'</a>';
							}else{
							if($value["link"]){
								$links .= '<a href="'.$value["link"].'" >'.$value["icon"].' '.$value["name"].'</a>';
								}else{
								$links .= '<a href="#" class="open-modal" data-id-modal="'.$value["modal_id"].'" >'.$value["icon"].' '.$value["name"].'</a>';
							}
						}
						
					}
					
				}
				
				return $links;
			}
			
		}
		
		function sessionsFavorites(){
			if($_SESSION['profile']['id']){
				$get = getAll("select * from uni_favorites where favorites_from_id_user=?", array($_SESSION['profile']['id']));
				if(count($get)){
					foreach ($get as $key => $value) {
						$_SESSION['profile']["favorite"][$value["favorites_id_ad"]] = $value["favorites_id_ad"];
					}
				}
			}
		}
		
		function cardUser($data = array()){
			global $settings, $config;
			
			$ULang = new ULang();
			$Shop = new Shop();
			
			if((strtotime($data["ad"]["clients_datetime_view"]) + 180) > time()){
				$status = '<span class="online badge-pulse-green-small" data-tippy-placement="top" title="'.$ULang->t("В сети").'"  ></span>';
				}else{
				$status = '<span class="online badge-pulse-red-small" data-tippy-placement="top" title="'.$ULang->t("Был(а) в сети:").' '.datetime_format($data["ad"]["clients_datetime_view"]).'" ></span>';
			}
			
			$countReviews = (int)getOne("select count(*) as total from uni_clients_reviews where clients_reviews_id_user=? and clients_reviews_status=?", [$data["ad"]["clients_id"],1])["total"];
			
			$getShop = $Shop->getShop(['user_id'=>$data["ad"]["clients_id"],'conditions'=>true]);
			
			if( $getShop ){
				$avatar = '<img src="'.Exists($config["media"]["other"], $getShop["clients_shops_logo"], $config["media"]["no_avatar"]).'">';
				$link = '<div class="board-view-user-label-shop" ></div> <a href="'.$Shop->linkShop($getShop["clients_shops_id_hash"]).'"  >'.$getShop["clients_shops_title"].'</a>';
				}else{
				$avatar = '<img src="'.$this->userAvatar($data["ad"]).'">';
				$link = '<a href="'._link( "user/" . $data["ad"]["clients_id_hash"] ).'"  >'.$this->name($data["ad"]).'</a>';
			}
			
			return '
			
			<div class="board-view-user-left" >
			'.$status.'
			<div class="board-view-user-avatar" >
			'.$avatar.'
			</div>
			</div>
			
			<div class="board-view-user-right" >
			
			'.$link.'
			
			<span class="board-view-user-date" >'.$ULang->t("На").' '.$settings["site_name"].' '.$ULang->t("с").' '. date("d.m.Y", strtotime($data["ad"]["clients_datetime_add"])).'</span>
			
            <div class="board-view-stars">
			
			<!--'.$data["ratings"].' <a href="'._link( "user/" . $data["ad"]["clients_id_hash"] . "/reviews" ).'" >('.$countReviews.')</a>-->
            <div class="clr"></div>   
			
            </div>
			
			</div>
			
			<div class="clr" ></div>
			<br>
			
			<span class="ml70 mt10" >' . ($countReviews != 0 ? $data["ratings"] . '</span> <a href="' . _link("user/" . $data["ad"]["clients_id_hash"] . "/reviews") . '">(' . $countReviews . ')</a>  ' : '
			<!--<div class="css-ds523g create-info" style="padding: 5px;">
			<div class="css-fasd5d mt5 mr5" style=" float: left;">
			<svg width="34px" height="34px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M10.75 2.44995C11.44 1.85995 12.57 1.85995 13.27 2.44995L14.85 3.81005C15.15 4.07005 15.71 4.28002 16.11 4.28002H17.81C18.87 4.28002 19.74 5.14996 19.74 6.20996V7.91003C19.74 8.30003 19.95 8.87004 20.21 9.17004L21.57 10.75C22.16 11.44 22.16 12.57 21.57 13.27L20.21 14.85C19.95 15.15 19.74 15.71 19.74 16.11V17.8101C19.74 18.8701 18.87 19.74 17.81 19.74H16.11C15.72 19.74 15.15 19.95 14.85 20.21L13.27 21.5699C12.58 22.1599 11.45 22.1599 10.75 21.5699L9.17002 20.21C8.87002 19.95 8.31001 19.74 7.91001 19.74H6.18C5.12 19.74 4.25 18.8701 4.25 17.8101V16.1C4.25 15.71 4.04001 15.15 3.79001 14.85L2.44 13.26C1.86 12.57 1.86 11.45 2.44 10.76L3.79001 9.17004C4.04001 8.87004 4.25 8.31004 4.25 7.92004V6.20996C4.25 5.14996 5.12 4.28002 6.18 4.28002H7.91001C8.30001 4.28002 8.87002 4.07005 9.17002 3.81005L10.75 2.44995Z" stroke="#292D32" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path> <path d="M9.89001 14.82L11.34 15.94C11.53 16.13 11.95 16.22 12.23 16.22H14C14.56 16.22 15.17 15.8 15.31 15.24L16.43 11.82C16.66 11.17 16.24 10.6 15.54 10.6H13.67C13.39 10.6 13.16 10.3701 13.2 10.0401L13.43 8.54008C13.52 8.12008 13.24 7.65005 12.82 7.51005C12.45 7.37005 11.98 7.56 11.79 7.84L9.87 10.69" stroke="#292D32" stroke-width="1.5" stroke-miterlimit="10"></path> <path d="M7.5 14.8199V10.24C7.5 9.57999 7.78 9.34998 8.44 9.34998H8.91C9.56 9.34998 9.85001 9.57999 9.85001 10.24V14.8199C9.85001 15.4699 9.57 15.71 8.91 15.71H8.44C7.78 15.71 7.5 15.4799 7.5 14.8199Z" stroke="#292D32" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path> </g></svg></div>
			<div>'.$ULang->t("Этот автор пока не получил ни одного отзыва").'</div>
			
			</div>-->') . '
			
			
			';
			
		}
		
		function cardUserAd($data = array()){
			global $settings, $config;
			
			$ULang = new ULang();
			$Shop = new Shop();
			
			if( (strtotime($data["ad"]["clients_datetime_view"]) + 180) > time() ){
				$status = '<span class="online badge-pulse-green-small" data-tippy-placement="top" title="'.$ULang->t("В сети").'"  ></span>';
				}else{
				$status = '<span class="online badge-pulse-red-small" data-tippy-placement="top" title="'.$ULang->t("Был(а) в сети:").' '.datetime_format($data["ad"]["clients_datetime_view"]).'" ></span>';
			}
			
			$countReviews = (int)getOne("select count(*) as total from uni_clients_reviews where clients_reviews_id_user=? and clients_reviews_status=?", [$data["ad"]["clients_id"],1])["total"];
			
			$getShop = $Shop->getShop(['user_id'=>$data["ad"]["clients_id"],'conditions'=>true]);
			
			if( $getShop ){
				$avatar = '<img src="'.Exists($config["media"]["other"], $getShop["clients_shops_logo"], $config["media"]["no_avatar"]).'">';
				$link = '<div class="board-view-user-label-shop" ></div><a class="ad-view-card-user-link-profile" href="'.$Shop->linkShop($getShop["clients_shops_id_hash"]).'"  >'.$getShop["clients_shops_title"].'</a>';
				}else{
				$avatar = '<img src="'.$this->userAvatar($data["ad"]).'">';
				$link = '<a class="ad-view-card-user-link-profile" href="'._link( "user/" . $data["ad"]["clients_id_hash"] ).'"  >'.$this->name($data["ad"]).'</a>';
			}
			
			return '
			
			<div class="row" >
			<div class="col-lg-7 col-7" >
			
			<div class="board-view-user-left" >
			'.$status.'
			<div class="board-view-user-avatar" >
			'.$avatar.'
			</div>
			</div>
			
			<div class="board-view-user-right" >
			'.$link.'
			<span class="board-view-user-date" >'.$ULang->t("На").' '.$settings["site_name"].' '.$ULang->t("с").' '. date("d.m.Y", strtotime($data["ad"]["clients_datetime_add"])).'</span>
			</div>
			
			</div>
			<div class="col-lg-5 col-5 text-center" >
			
			<a href="'._link( "user/" . $data["ad"]["clients_id_hash"] . "/reviews" ).'" class="btn-custom-mini btn-color-light" >'.$ULang->t("Отзывы").'</a>
			
			</div>
			</div>
			
			<div class="clr" ></div>
			
			<br>
			<!-- Phone_rating -->
			<span class="ml70 mt10" >' . ($countReviews != 0 ? $data["ratings"] . '</span> <a href="' . _link("user/" . $data["ad"]["clients_id_hash"] . "/reviews") . '">(' . $countReviews . ')</a>  ' : '
			<!--<div class="css-ds523g create-info" style="padding: 5px;min-height: 55px;">
			<div class="css-fasd5d mt5 mr5" style=" float: left;">
			<svg width="34px" height="34px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M10.75 2.44995C11.44 1.85995 12.57 1.85995 13.27 2.44995L14.85 3.81005C15.15 4.07005 15.71 4.28002 16.11 4.28002H17.81C18.87 4.28002 19.74 5.14996 19.74 6.20996V7.91003C19.74 8.30003 19.95 8.87004 20.21 9.17004L21.57 10.75C22.16 11.44 22.16 12.57 21.57 13.27L20.21 14.85C19.95 15.15 19.74 15.71 19.74 16.11V17.8101C19.74 18.8701 18.87 19.74 17.81 19.74H16.11C15.72 19.74 15.15 19.95 14.85 20.21L13.27 21.5699C12.58 22.1599 11.45 22.1599 10.75 21.5699L9.17002 20.21C8.87002 19.95 8.31001 19.74 7.91001 19.74H6.18C5.12 19.74 4.25 18.8701 4.25 17.8101V16.1C4.25 15.71 4.04001 15.15 3.79001 14.85L2.44 13.26C1.86 12.57 1.86 11.45 2.44 10.76L3.79001 9.17004C4.04001 8.87004 4.25 8.31004 4.25 7.92004V6.20996C4.25 5.14996 5.12 4.28002 6.18 4.28002H7.91001C8.30001 4.28002 8.87002 4.07005 9.17002 3.81005L10.75 2.44995Z" stroke="#292D32" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path> <path d="M9.89001 14.82L11.34 15.94C11.53 16.13 11.95 16.22 12.23 16.22H14C14.56 16.22 15.17 15.8 15.31 15.24L16.43 11.82C16.66 11.17 16.24 10.6 15.54 10.6H13.67C13.39 10.6 13.16 10.3701 13.2 10.0401L13.43 8.54008C13.52 8.12008 13.24 7.65005 12.82 7.51005C12.45 7.37005 11.98 7.56 11.79 7.84L9.87 10.69" stroke="#292D32" stroke-width="1.5" stroke-miterlimit="10"></path> <path d="M7.5 14.8199V10.24C7.5 9.57999 7.78 9.34998 8.44 9.34998H8.91C9.56 9.34998 9.85001 9.57999 9.85001 10.24V14.8199C9.85001 15.4699 9.57 15.71 8.91 15.71H8.44C7.78 15.71 7.5 15.4799 7.5 14.8199Z" stroke="#292D32" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path> </g></svg></div>
			<div class="some-class">'.$ULang->t("Этот автор пока не получил ни одного отзыва").'</div>
			</div>-->') . '
			<style>@media (min-width: 417px) {.some-class {padding: 12px;}}</style>';
		}
		
		
		function cardUserOrder($data = array()){
			global $settings, $config;
			
			$ULang = new ULang();
			$Shop = new Shop();
			
			$getShop = $Shop->getShop(['user_id'=>$data["user"]["clients_id"],'conditions'=>true]);
			
			if( (strtotime($data["user"]["clients_datetime_view"]) + 180) > time() ){
				$status = '<span class="online badge-pulse-green-small" data-tippy-placement="top" title="'.$ULang->t("В сети").'"  ></span>';
				}else{
				$status = '<span class="online badge-pulse-red-small" data-tippy-placement="top" title="'.$ULang->t("Был в сети:").' '.datetime_format($data["user"]["clients_datetime_view"]).'" ></span>';
			}
			
			if( $getShop ){
				$avatar = '<img src="'.Exists($config["media"]["other"], $getShop["clients_shops_logo"], $config["media"]["no_avatar"]).'">';
				$link = '<a href="'.$Shop->linkShop($getShop["clients_shops_id_hash"]).'"  >'.$getShop["clients_shops_title"].'</a>';
				}else{
				$avatar = '<img src="'.$this->userAvatar($data["user"]).'">';
				$link = '<a href="'._link( "user/" . $data["user"]["clients_id_hash"] ).'"  >'.$this->name($data["user"]).'</a>';
			
            }
			
			return '
			<div class="board-view-user-left" >
			'.$status.'
			<div class="board-view-user-avatar" >
            '.$avatar.'
			</div>
			</div>
			
			<div class="board-view-user-right" >
			
			'.$link.'
			
			<span class="board-view-user-date" >'.$ULang->t("На").' '.$settings["site_name"].' '.$ULang->t("с").' '. date("d.m.Y", strtotime($data["user"]["clients_datetime_add"])).'</span>
			
			<div class="board-view-stars">
			
			'.$data["ratings"].'
			<div class="clr"></div>   
			
			</div>
			
			</div>
			
			<div class="clr" ></div>
		
		';
		
		}
		
		
		function payMethod($payment="", $paramForm = array()){
		global $config;
		$param = paymentParams($payment);
		
		if(file_exists( $config["basePath"] . "/systems/payment/".$payment."/form.php" )){
		
		insert("INSERT INTO uni_orders_parameters(orders_parameters_param,orders_parameters_id_uniq,orders_parameters_date)VALUES(?,?,?)", [json_encode($paramForm), $paramForm["id_order"], date("Y-m-d H:i:s")]);
		
		return include $config["basePath"] . "/systems/payment/".$payment."/form.php";
		
		}
		
		}
		
		function payCallBack( $id_uniq = "" ){
		global $settings,$config;
		
		$static_msg = require $config["basePath"] . "/static/msg.php";
		
		$Main = new Main();
		$Ads = new Ads();
		$Delivery = new Delivery();
		
		$getOrderParam = findOne("uni_orders_parameters","orders_parameters_id_uniq=? and orders_parameters_status=?", [$id_uniq,0]);
		
		if($getOrderParam){
		
		update("update uni_orders_parameters set orders_parameters_status=? where orders_parameters_id_uniq=?", [ 1 , $id_uniq ]);
		
		$output_param = json_decode($getOrderParam["orders_parameters_param"], true);
		
		$user = findOne("uni_clients", "clients_id=?", [ intval($output_param["id_user"]) ]);
		
		if($output_param["action"] == "balance"){
		
		$this->actionBalance(array("id_user"=>$output_param["id_user"],"summa"=>$output_param["amount"],"title"=>$output_param["title"],"id_order"=>generateOrderId(),"email" => $user["clients_email"],"name" => $user["clients_name"], "note" => $output_param["title"]),"+");
		
		if($settings["bonus_program"]["balance"]["status"] && $settings["bonus_program"]["balance"]["price"]){
		$bonus = $this->calcBonus($output_param["amount"]);
		$this->actionBalance(array("id_user"=>$output_param["id_user"],"summa"=>$bonus,"title"=>$settings["bonus_program"]["balance"]["name"],"id_order"=>generateOrderId(),"email" => $user["clients_email"],"name" => $user["clients_name"], "note" => $settings["bonus_program"]["balance"]["name"]),"+");             
		}
		
		$Main->addOrder(["id_order" => $id_uniq,"id_ad"=>0,"price"=>$output_param["amount"],"title"=>$output_param["title"],"id_user"=>$output_param["id_user"],"status_pay"=>1, "user_name" => $user["clients_name"], "id_hash_user" => $user["clients_id_hash"], 'action_name' => $output_param["action"]]);
		
		if($settings["referral_program_status"] && $settings["referral_program_award_percent"]){
		$bonus = $this->calcReferralAward($output_param["amount"]);
		$getRef = findOne('uni_clients_ref', 'id_user_referral=?', [$output_param["id_user"]]);
		if($getRef){
		$getUser = findOne('uni_clients', 'clients_id=?', [$getRef['id_user_referrer']]);
		if($getUser){
		
		smart_insert('uni_clients_ref_award', [
		'amount' => $bonus,
		'id_user_referral' => $output_param["id_user"],
		'id_user_referrer' => $getRef['id_user_referrer'],
		'timestamp' => date('Y-m-d H:i:s'),
		]);
		
		$this->actionBalance(array("id_user"=>$getRef['id_user_referrer'],"summa"=>$bonus,"title"=>$static_msg[58],"id_order"=>generateOrderId(),"email" => $getUser["clients_email"],"name" => $getUser["clients_name"], "note" => $static_msg[58]),"+");
		
		$getRefUser = findOne('uni_clients', 'clients_id=?', [$output_param["id_user"]]);
		
		$param      = array("{USER_NAME}"=>$getUser["clients_name"],
		"{REF_USER_NAME}"=>$getRefUser["clients_name"].' '.$getRefUser["clients_surname"],
		"{SUMMA}"=>$Main->price($bonus),
		"{UNSUBCRIBE}"=>"",
		"{EMAIL_TO}"=>$getUser["clients_email"]); 
		
		$this->userNotification( [ "mail"=>["params"=>$param, "code"=>"REF_AWARD", "email"=>$getUser["clients_email"]],"method"=>1 ] );
		
		}
		}             
		}
		
		}elseif($output_param["action"] == "secure"){
		
		if($output_param["auction"] == 1){
		insert("INSERT INTO uni_ads_auction(ads_auction_id_ad,ads_auction_price,ads_auction_id_user)VALUES(?,?,?)", [$output_param["id_ad"], $output_param["ad_price"], $output_param["id_user"]]);
		update("update uni_ads set ads_price=? where ads_id=?", [ $output_param["ad_price"] , $output_param["id_ad"] ], true);
		}
		
		$findAd = $Ads->get("ads_id=?", [$output_param["id_ad"]]);
		
		if($Ads->getStatusDelivery($findAd) && $output_param["delivery"]["type"] != 'self' && $settings["main_type_products"] == 'physical'){
		
		$deliveryParams['items'][] = [
		'id'=>$findAd['ads_id'],
		'name'=>$findAd['ads_title'],
		'nds'=>0,
		'price'=>$findAd['ads_price'],
		'quantity'=>1
		];   
		
		$deliveryParams['weights']['weight'] = $findAd['ads_delivery_weight']; 
		
		$getUser = findOne('uni_clients', 'clients_id=?', [$output_param["id_user"]]);
		
		$deliveryParams['order_id'] = (string)$output_param["id_order"];
		$deliveryParams['vid'] = 1;
		$deliveryParams['shop'] = [
		'name'=>$output_param["delivery"]["delivery_id_point"],
		'name1'=>$getUser['clients_delivery_id_point_send']
		];
		$deliveryParams['customer'] = [
		'fio'=>$output_param["delivery"]["delivery_surname"].' '.$output_param["delivery"]["delivery_name"].' '.$output_param["delivery"]["delivery_patronymic"],
		'phone'=>$output_param["delivery"]["delivery_phone"],
		];
		
		$deliveryResults = $Delivery->boxberryCreateParcel($deliveryParams);  
		
		update("update uni_secure set secure_status=?,secure_delivery_type=?,secure_delivery_invoice_number=?,secure_delivery_track_number=?,secure_delivery_errors=? where secure_id_order=?", [ $deliveryResults['invoice_number'] ? 2 : 1, $output_param["delivery"]["type"], $deliveryResults['invoice_number'], $deliveryResults['track_number'], $deliveryResults['errors'], $output_param["id_order"] ]);
		
		}else{
		update("update uni_secure set secure_status=?,secure_delivery_type=? where secure_id_order=?", [ 1, $output_param["delivery"]["type"], $output_param["id_order"] ]);
		}
		
		
		if($findAd["category_board_marketplace"] && !$findAd['ads_available_unlimitedly'] && $findAd["ads_available"]){
		update("update uni_ads set ads_available=ads_available-1 where ads_id=?", [$value['ad']['ads_id']]);
		}
		
		$Ads->addSecurePayments( ["id_order"=>$output_param["id_order"], "amount"=>$output_param["amount"], "id_user"=>$output_param["id_user"], "status_pay"=>1, "status"=>0, "amount_percent"=>$output_param["amount"]] );
		
		$getAd = $Ads->get("ads_id=?", [ $output_param["id_ad"] ]);
		
		$param      = array("{USER_NAME}"=>$getAd["clients_name"],
		"{USER_EMAIL}"=>$getAd["clients_email"],
		"{ADS_TITLE}"=>$getAd["ads_title"],
		"{ADS_LINK}"=>$Ads->alias($getAd),
		"{PROFILE_LINK_ORDER}"=>$output_param["link_success"],
		"{UNSUBCRIBE}"=>"",
		"{EMAIL_TO}"=>$getAd["clients_email"]); 
		
		$this->userNotification( [ "mail"=>["params"=>$param, "code"=>"USER_NEW_BUY", "email"=>$getAd["clients_email"]],"method"=>1 ] );
		
		}elseif($output_param["action"] == "booking"){
		
		update("update uni_ads_booking set ads_booking_status_pay=? where ads_booking_id_order=?", [ 1, $output_param["id_order"] ]);
		
		insert("INSERT INTO uni_ads_booking_prepayments(ads_booking_prepayments_id_order,ads_booking_prepayments_amount)VALUES(?,?)", array($output_param["id_order"], $output_param["amount"]));
		
		$Main->addActionStatistics(['ad_id'=>$output_param["id_ad"],'from_user_id'=>$output_param["from_user_id"],'to_user_id'=>$output_param["to_user_id"]],"booking");
		
		$getAd = $Ads->get("ads_id=?", [ $output_param["id_ad"] ]);
		
		$param      = array("{USER_NAME}"=>$getAd["clients_name"],
		"{USER_EMAIL}"=>$getAd["clients_email"],
		"{ADS_TITLE}"=>$getAd["ads_title"],
		"{ADS_LINK}"=>$Ads->alias($getAd),
		"{PROFILE_LINK_ORDER}"=>$output_param["link_order"],
		"{UNSUBCRIBE}"=>"",
		"{EMAIL_TO}"=>$getAd["clients_email"]); 
		
		if($getAd['category_board_booking_variant'] == 0){
		$this->userNotification( [ "mail"=>["params"=>$param, "code"=>"USER_NEW_ORDER_BOOKING", "email"=>$getAd["clients_email"]],"method"=>1 ] );
		}else{
		$this->userNotification( [ "mail"=>["params"=>$param, "code"=>"USER_NEW_ORDER_RENT", "email"=>$getAd["clients_email"]],"method"=>1 ] );
		}
		
		}elseif($output_param["action"] == "marketplace"){
		
		foreach ($output_param["cart"] as $id => $value) {
		$data_order[$value['ad']['ads_id_user']][] = $value;
		}
		
		foreach ($data_order as $id_user => $array) {
		
		$total_price = 0;
		$deliveryParams = [];
		$order_id = generateOrderId();
		
		foreach ($array as $value) {
		
		$total_price += $value['ad']['ads_price'] * $value['count'];
		
		smart_insert('uni_secure_ads', ['secure_ads_ad_id'=>$value['ad']['ads_id'],'secure_ads_count'=>$value['count'],'secure_ads_total'=>$value['ad']['ads_price'] * $value['count'],'secure_ads_order_id'=>$order_id,'secure_ads_user_id'=>$value['ad']['ads_id_user']]);
		
		if(!$value['ad']['ads_available_unlimitedly']){
		update("update uni_ads set ads_available=ads_available-".$value['count']." where ads_id=?", [$value['ad']['ads_id']]);
		}
		
		if($Ads->getStatusDelivery($value['ad'])){
		$deliveryParams['items'][] = [
		'id'=>$value['ad']['ads_id'],
		'name'=>$value['ad']['ads_title'],
		'nds'=>0,
		'price'=>$value['ad']['ads_price'],
		'quantity'=>$value['count']
		];   
		$deliveryParams['weights']['weight'] += $value['ad']['ads_delivery_weight'];                                             
		}
		
		$this->sendChat( array("id_ad" => $value['ad']['ads_id'], "action" => 3, "user_from" => $output_param["id_user"], "user_to" => $value['ad']["ads_id_user"] ) );
		
		}
		
		if($deliveryParams['items'] && $output_param["delivery"]["type"] != 'self' && $settings["main_type_products"] == 'physical'){
		
		$getUser = findOne('uni_clients', 'clients_id=?', [$id_user]);
		
		$deliveryParams['order_id'] = (string)$order_id;
		$deliveryParams['vid'] = 1;
		$deliveryParams['shop'] = [
		'name'=>$output_param["delivery"]["delivery_id_point"],
		'name1'=>$getUser['clients_delivery_id_point_send']
		];
		$deliveryParams['customer'] = [
		'fio'=>$output_param["delivery"]["delivery_surname"].' '.$output_param["delivery"]["delivery_name"].' '.$output_param["delivery"]["delivery_patronymic"],
		'phone'=>$output_param["delivery"]["delivery_phone"],
		];
		
		$deliveryResults = $Delivery->boxberryCreateParcel($deliveryParams);
		
		smart_insert('uni_secure', ['secure_date'=>date("Y-m-d H:i:s"),'secure_id_user_buyer'=>$output_param["id_user"],'secure_id_user_seller'=>$id_user,'secure_id_order'=>$order_id,'secure_price'=>$total_price,'secure_status'=>$deliveryResults['invoice_number'] ? 2 : 1,'secure_delivery_type'=>$output_param["delivery"]["type"],'secure_delivery_invoice_number'=>$deliveryResults['invoice_number'],'secure_delivery_track_number'=>$deliveryResults['track_number'],'secure_delivery_errors'=>$deliveryResults['errors'],'secure_delivery_name'=>$output_param["delivery"]['delivery_name'],'secure_delivery_surname'=>$output_param["delivery"]['delivery_surname'],'secure_delivery_patronymic'=>$output_param["delivery"]['delivery_patronymic'],'secure_delivery_phone'=>$output_param["delivery"]['delivery_phone'],'secure_delivery_id_point'=>$output_param["delivery"]['delivery_id_point'],'secure_marketplace'=>1]);
		
		}else{
		smart_insert('uni_secure', ['secure_date'=>date("Y-m-d H:i:s"),'secure_id_user_buyer'=>$output_param["id_user"],'secure_id_user_seller'=>$id_user,'secure_id_order'=>$order_id,'secure_price'=>$total_price,'secure_status'=>1,'secure_delivery_type'=>$output_param["delivery"]["type"],'secure_marketplace'=>1]);
		}
		
		smart_insert('uni_clients_orders', ['clients_orders_from_user_id'=>$output_param["id_user"],'clients_orders_uniq_id'=>$order_id,'clients_orders_date'=>date('Y-m-d H:i:s'),'clients_orders_to_user_id'=>$id_user,'clients_orders_secure'=>1,'clients_orders_status'=>1]);
		$Ads->addSecurePayments( ["id_order"=>$order_id, "amount"=>$total_price, "id_user"=>$output_param["id_user"], "status_pay"=>1, "status"=>0, "amount_percent"=>$total_price] );
		
		}
		
		}
		
		}
		
		}
		
		function countOnline(){
		return (int)getOne("select count(*) as total from uni_clients where unix_timestamp(clients_datetime_view)+3*60 > unix_timestamp(NOW())")["total"];
		}
		
		function calcBonus($price){
		global $settings;
		return (($price / 100) * $settings["bonus_program"]["balance"]["price"]);
		}
		
		function calcReferralAward($price){
		global $settings;
		return (($price / 100) * $settings["referral_program_award_percent"]);
		}
		
		function paramNotifications($json=""){
		
		if($json){
		return json_decode($json, true);
		}else{
		return [];
		}
		
		}
		
		function userNotification( $data = [] ){
		
		if($data["method"] == 1){   
		if($data["mail"]["email"]){
		email_notification( array( "variable" => $data["mail"]["params"], "code" => $data["mail"]["code"] ) );
		}
		}elseif($data["method"] == 2){   
		if($data["mail"]["email"]){
		email_notification( array( "variable" => $data["mail"]["params"], "code" => $data["mail"]["code"] ) );
		}elseif($data["sms"]["phone"]){
		sms($data["sms"]["phone"], $data["sms"]["text"],'sms');
		}
		}
		
		}
		
		function authLink($name=""){
		global $settings, $config;
		
		$social_auth_params = json_decode(decrypt($settings["social_auth_params"]), true);
		
		if($name == "vk"){
		
		$params = array(
		'client_id'     => $social_auth_params["vk"]["id_client"],
		'redirect_uri'  => $config["urlPath"] . "/systems/ajax/oauth.php?network=vk",
		'scope'         => 'email',
		'response_type' => 'code',
		'state'         => $config["urlPath"] . "/auth",
		);
		
		return 'https://oauth.vk.com/authorize?' . urldecode(http_build_query($params));
		
		}elseif($name == "google"){
		
		$params = array(
		'client_id'     => $social_auth_params["google"]["id_client"],
		'redirect_uri'  => $config["urlPath"] . "/systems/ajax/oauth.php?network=google",
		'response_type' => 'code',
		'scope'         => 'https://www.googleapis.com/auth/userinfo.email https://www.googleapis.com/auth/userinfo.profile',
		'state'         => '123'
		);
		
		return 'https://accounts.google.com/o/oauth2/auth?' . urldecode(http_build_query($params));
		
		}elseif($name == "fb"){
		
		$params = array(
		'client_id'     => $social_auth_params["fb"]["id_app"],
		'scope'         => 'email',
		'redirect_uri'  => $config["urlPath"] . "/systems/ajax/oauth.php?network=fb",
		'response_type' => 'code',
		);
		
		return 'https://www.facebook.com/dialog/oauth?' . urldecode(http_build_query($params));
		
		}elseif($name == "yandex"){
		
		$params = array(
		'client_id'     => $social_auth_params["yandex"]["id_app"],
		'redirect_uri'  => $config["urlPath"] . "/systems/ajax/oauth.php?network=yandex",
		'response_type' => 'code',
		'state'         => '123'
		);
		
		return 'https://oauth.yandex.ru/authorize?' . urldecode(http_build_query($params));
		
		}
		
		}
		
		function socialAuth(){
		global $settings;
		
		$authorization_social_list = explode(",", $settings["authorization_social"]);
		
		if( in_array( "yandex" , $authorization_social_list ) ){
		?>
		<a class="auth-yandex" href="<?php echo $this->authLink("yandex"); ?>" >
		<img src="<?php echo $settings["path_other"].'/media_social_yandex_61627.svg'; ?>">
		</a>                           
		<?php  
		}
		if( in_array( "vk" , $authorization_social_list ) ){
		?>
		<a class="css-i2yj1g" href="<?php echo $this->authLink("vk"); ?>"  style="color:#000;">
		<i class="lab la-vk"></i> ВКонтакте
		</a>                          
		<?php
		}
		if( in_array( "google" , $authorization_social_list ) ){
		?>
		<a class="css-i2yj1g" href="<?php echo $this->authLink("google"); ?>" style="color:#000;">
		<svg fill="#000000" width="24px" height="24px" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M21.35 11.1H12.18V13.83H18.69C18.36 17.64 15.19 19.27 12.19 19.27C8.36003 19.27 5.00003 16.25 5.00003 12C5.00003 7.9 8.20003 4.73 12.2 4.73C15.29 4.73 17.1 6.7 17.1 6.7L19 4.72C19 4.72 16.56 2 12.1 2C6.42003 2 2.03003 6.8 2.03003 12C2.03003 17.05 6.16003 22 12.25 22C17.6 22 21.5 18.33 21.5 12.91C21.5 11.76 21.35 11.1 21.35 11.1Z"></path> </g></svg>&nbsp; Google
		</a>                          
		<?php  
		}                                               
		if( in_array( "fb" , $authorization_social_list ) ){
		?>
		<a class="css-i2yj1g" href="<?php echo $this->authLink("fb"); ?>" style="color:#000000;">
		<svg fill="#000000" width="24px" height="24px" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" data-name="Layer 1"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"><path d="M15.12,5.32H17V2.14A26.11,26.11,0,0,0,14.26,2C11.54,2,9.68,3.66,9.68,6.7V9.32H6.61v3.56H9.68V22h3.68V12.88h3.06l.46-3.56H13.36V7.05C13.36,6,13.64,5.32,15.12,5.32Z"></path></g></svg> 
		FaceBook
		</a>                          
		<?php  
		}                          
		}
		
		function getMessagesOrder($order_id=0){
		global $config;
		$ULang = new ULang();
		$getMessages = getAll('select * from uni_clients_orders_messages where clients_orders_messages_id_order=? order by clients_orders_messages_date asc', [$order_id]);
		if(count($getMessages)){
		foreach ($getMessages as $key => $value) {
		
		$attach = '';
		
		if($_SESSION['profile']['id'] == $value['clients_orders_messages_from_id_user']){
		$getUser = findOne('uni_clients', 'clients_id=?', [$value['clients_orders_messages_to_id_user']]);
		$name = '<a href="'._link( "user/" . $getUser["clients_id_hash"] ).'" class="order-messages-item-name" >'.$ULang->t('Вы:').'</a>';
		$class = 'order-messages-from';
		}else{
		$getUser = findOne('uni_clients', 'clients_id=?', [$value['clients_orders_messages_from_id_user']]);
		$name = '<a href="'._link( "user/" . $getUser["clients_id_hash"] ).'" class="order-messages-item-name" >'.$getUser['clients_name'].':</a>';
		$class = 'order-messages-to';
		}
		
		if($value['clients_orders_messages_files']){
		$attach = '<div class="order-message-item-attach lightgallery" >';
		foreach (explode(',', $value['clients_orders_messages_files']) as $file_name) {
		if( file_exists( $config["basePath"] . "/" . $config["media"]["users"] . "/" . $file_name ) ){
		$attach .= '<a href="'.$config["urlPath"] . "/" . $config["media"]["users"] . "/" . $file_name.'"><img class="image-autofocus" src="'.$config["urlPath"] . "/" . $config["media"]["users"] . "/" . $file_name.'" ></a>';
		}                        
		}
		$attach .= '</div>';
		}
		
		$items .= '
		<div>
		<div class="order-messages-item '.$class.'" >
		<div>
		'.$name.'
		<span class="order-messages-item-date" >'.datetime_format($value['clients_orders_messages_date']).'</span>
		<div class="clr" ></div>
		</div>
		<span class="order-messages-item-message" >'.nl2br(decrypt($value['clients_orders_messages_message'])).'</span>
		'.$attach.'
		</div>
		</div>
		';
		
		}
		return $items;
		}
		}
		
		function getTariff($tariff_id=0){
		$results = [];
		if($tariff_id){
		$results['tariff'] = findOne('uni_services_tariffs', 'services_tariffs_id=?', [$tariff_id]);
		if($results['tariff']['services_tariffs_services']){
		$results['tariff']['services_tariffs_services'] = json_decode($results['tariff']['services_tariffs_services'], true);
		foreach ($results['tariff']['services_tariffs_services'] as $id) {
		$getChecklist = findOne('uni_services_tariffs_checklist', 'services_tariffs_checklist_id=?', [$id]);
		if($getChecklist) $results['services'][$getChecklist['services_tariffs_checklist_uid']] = $getChecklist;
		}
		}
		}
		return $results;
		}
		
		function calcPriceTariff($getTariff=[],$getTariffOrder=[]){
		
		$price_tariff = $getTariff['tariff']['services_tariffs_new_price'] ?: $getTariff['tariff']['services_tariffs_price'];
		$price_current = $getTariffOrder['services_tariffs_orders_price'];
		
		if($price_tariff > $price_current){
		
		$day = difference_days($getTariffOrder['services_tariffs_orders_date_completion'],date('Y-m-d H:i:s'))+1;
		if($day){
		$total = ($price_current / $getTariffOrder['services_tariffs_orders_days']) * $day;
		return $price_tariff - $total;
		}else{
		return $price_tariff;
		}
		
		}else{
		return $price_tariff; 
		}
		
		}
		
		function getOrderTariff($user_id=0){
		$results = [];
		$results = getOne('SELECT * FROM `uni_services_tariffs_orders` INNER JOIN `uni_services_tariffs` ON `uni_services_tariffs`.services_tariffs_id = `uni_services_tariffs_orders`.services_tariffs_orders_id_tariff WHERE services_tariffs_orders_id_user=?', [$user_id]);
		if($results['services_tariffs_services'] && (strtotime($results['services_tariffs_orders_date_completion']) > time() || !$results['services_tariffs_orders_date_completion'])){
		$results['services_tariffs_services'] = json_decode($results['services_tariffs_services'], true);
		foreach ($results['services_tariffs_services'] as $id) {
		$get = findOne('uni_services_tariffs_checklist', 'services_tariffs_checklist_id=?', [$id]);
		$results['services'][$get['services_tariffs_checklist_uid']] = $get;
		}
		}
		return $results;
		}
		
		function buttonPayTariff(){
		
		$ULang = new ULang();
		$Main = new Main();
		
		$getOnetime = findOne('uni_services_tariffs_onetime', 'services_tariffs_onetime_user_id=? and services_tariffs_onetime_tariff_id=?', [$_SESSION["profile"]["id"],$_SESSION["profile"]["tariff"]["services_tariffs_orders_id_tariff"]]);
		
		if($_SESSION["profile"]["tariff"]["services_tariffs_orders_days"] && strtotime($_SESSION["profile"]["tariff"]["services_tariffs_orders_date_completion"]) <= time() && $_SESSION["profile"]["tariff"]["services_tariffs_price"] && !$getOnetime){
		return '<span class="btn-custom-mini btn-color-danger profile-tariff-activate" data-id="'.$_SESSION["profile"]["tariff"]["services_tariffs_orders_id_tariff"].'" >'.$ULang->t("Оплатить").' '.$Main->price($_SESSION["profile"]["tariff"]["services_tariffs_new_price"] ?: $_SESSION["profile"]["tariff"]["services_tariffs_price"]).'</span>';
		}
		
		}
		
		function dataActionStatistics($action=''){
		
		$data = [];
		$days = [];
		$months = [];
		$years = [];
		$format = 'Y-m-d';
		
		$ad_id = (int)$_GET['ad'];
		$date_start = $_GET['date_start'];
		$date_end = $_GET['date_end'];
		
		if($date_start && $date_end){
		
		if(strtotime($date_end) > strtotime($date_start)){
		
		if(date("Y-m", strtotime($date_start)) == date("Y-m", strtotime($date_end))){
		
		$difference = difference_days($date_end,$date_start);
		
		$days[ date($format, strtotime($date_start)) ] = date($format, strtotime($date_start));
		
		$x=0;
		while ($x++<$difference){
		$days[ date($format, strtotime("+".$x." day", strtotime($date_start))) ] = date($format, strtotime("+".$x." day", strtotime($date_start)));
		}
		
		ksort($days);
		
		}elseif(date("Y", strtotime($date_start)) == date("Y", strtotime($date_end))){
		
		$months[ date("Y-m", strtotime($date_start)) ] = date("Y-m", strtotime($date_start));
		
		$new_m = (int)date("m", strtotime($date_end)) - (int)date("m", strtotime($date_start));
		
		$x=0;
		while ($x++<$new_m){
		$months[ date("Y-m", strtotime("+".$x." month", strtotime($date_start))) ] = date("Y-m", strtotime("+".$x." month", strtotime($date_start)));
		}   
		
		}else{
		
		$years[ date("Y", strtotime($date_start)) ] = date("Y", strtotime($date_start));
		
		$new_y = (int)date("Y", strtotime($date_end)) - (int)date("Y", strtotime($date_start));
		
		$x=0;
		while ($x++<$new_y){
		$years[ date("Y", strtotime("+".$x." year", strtotime($date_start))) ] = date("Y", strtotime("+".$x." year", strtotime($date_start)));
		} 
		
		}
		
		}
		
		}elseif($date_start){
		
		$days[ date($format, strtotime($date_start)) ] = date($format, strtotime($date_start));
		
		}
		
		
		if(!$days && !$months && !$years){
		$x=0;
		while ($x++<30){
		$days[ date($format, strtotime("-".$x." day")) ] = date($format, strtotime("-".$x." day"));
		}
		
		$days[ date($format) ] = date($format);
		
		ksort($days);
		}
		
		if($action == 'display'){
		
		if($ad_id){
		if($days){
		foreach ($days as $value) {
		$data[] = (int)getOne("select sum(ads_views_display_count) as total from uni_ads_views_display where ads_views_display_id_user=? and date(ads_views_display_date)=? and ads_views_display_id_ad=?", [$_SESSION["profile"]["id"],$value,$ad_id])["total"];
		}
		}elseif($months){
		foreach ($months as $value) {
		$explode = explode('-', $value);
		$data[] = (int)getOne("select sum(ads_views_display_count) as total from uni_ads_views_display where ads_views_display_id_user=? and YEAR(ads_views_display_date)=? and MONTH(ads_views_display_date)=? and ads_views_display_id_ad=?", [$_SESSION["profile"]["id"],$explode[0],$explode[1],$ad_id])["total"];
		}
		}elseif($years){
		foreach ($years as $value) {
		$data[] = (int)getOne("select sum(ads_views_display_count) as total from uni_ads_views_display where ads_views_display_id_user=? and YEAR(ads_views_display_date)=? and ads_views_display_id_ad=?", [$_SESSION["profile"]["id"],$value,$ad_id])["total"];
		}
		}
		}else{
		if($days){
		foreach ($days as $value) {
		$data[] = (int)getOne("select sum(ads_views_display_count) as total from uni_ads_views_display where ads_views_display_id_user=? and date(ads_views_display_date)=?", [$_SESSION["profile"]["id"],$value])["total"];
		}
		}elseif($months){
		foreach ($months as $value) {
		$explode = explode('-', $value);
		$data[] = (int)getOne("select sum(ads_views_display_count) as total from uni_ads_views_display where ads_views_display_id_user=? and YEAR(ads_views_display_date)=? and MONTH(ads_views_display_date)=?", [$_SESSION["profile"]["id"],$explode[0],$explode[1]])["total"];
		}
		}elseif($years){
		foreach ($years as $value) {
		$data[] = (int)getOne("select sum(ads_views_display_count) as total from uni_ads_views_display where ads_views_display_id_user=? and YEAR(ads_views_display_date)=?", [$_SESSION["profile"]["id"],$value])["total"];
		}
		}                
		}
		
		return implode(',', $data);
		
		}elseif($action == 'view'){
		
		if($ad_id){
		if($days){
		foreach ($days as $value) {
		$data[] = (int)getOne("select count(*) as total from uni_ads_views where ads_views_id_user=? and date(ads_views_date)=? and ads_views_id_ad=?", [$_SESSION["profile"]["id"],$value,$ad_id])["total"];
		}
		}elseif($months){
		foreach ($months as $value) {
		$explode = explode('-', $value);
		$data[] = (int)getOne("select count(*) as total from uni_ads_views where ads_views_id_user=? and YEAR(ads_views_date)=? and MONTH(ads_views_date)=? and ads_views_id_ad=?", [$_SESSION["profile"]["id"],$explode[0],$explode[1],$ad_id])["total"];
		}
		}elseif($years){
		foreach ($years as $value) {
		$data[] = (int)getOne("select count(*) as total from uni_ads_views where ads_views_id_user=? and YEAR(ads_views_date)=? and ads_views_id_ad=?", [$_SESSION["profile"]["id"],$value,$ad_id])["total"];
		}
		}
		}else{
		if($days){
		foreach ($days as $value) {
		$data[] = (int)getOne("select count(*) as total from uni_ads_views where ads_views_id_user=? and date(ads_views_date)=?", [$_SESSION["profile"]["id"],$value])["total"];
		}
		}elseif($months){
		foreach ($months as $value) {
		$explode = explode('-', $value);
		$data[] = (int)getOne("select count(*) as total from uni_ads_views where ads_views_id_user=? and YEAR(ads_views_date)=? and MONTH(ads_views_date)=?", [$_SESSION["profile"]["id"],$explode[0],$explode[1]])["total"];
		}
		}elseif($years){
		foreach ($years as $value) {
		$data[] = (int)getOne("select count(*) as total from uni_ads_views where ads_views_id_user=? and YEAR(ads_views_date)=?", [$_SESSION["profile"]["id"],$value])["total"];
		}
		}                
		}
		
		return implode(',', $data);
		
		}elseif($action == 'favorites'){
		
		return $this->getCountActionStatistics($ad_id,$days,$months,$years,'favorite');
		
		}elseif($action == 'show_phone'){
		
		return $this->getCountActionStatistics($ad_id,$days,$months,$years,'show_phone');
		
		}elseif($action == 'ad_sell'){
		
		return $this->getCountActionStatistics($ad_id,$days,$months,$years,'ad_sell');
		
		}elseif($action == 'cart'){
		
		return $this->getCountActionStatistics($ad_id,$days,$months,$years,'add_to_cart');
		
		}elseif($action == 'booking'){
		
		return $this->getCountActionStatistics($ad_id,$days,$months,$years,'booking');
		
		}elseif($action == 'date'){
		
		if($days){
		foreach ($days as $value) {
		$quotation_month[$value] = '"'.$value.'"';
		}
		}elseif($months){
		foreach ($months as $value) {
		$quotation_month[$value] = '"'.$value.'"';
		}
		}elseif($years){
		foreach ($years as $value) {
		$quotation_month[$value] = '"'.$value.'"';
		}
		}
		
		return implode(',',$quotation_month);
		
		}
		}
		
		function getCountActionStatistics($ad_id=0,$days=[],$months=[],$years=[],$action=''){
		
		$data = [];
		
		if($ad_id){
		if($days){
		foreach ($days as $value) {
		$data[] = (int)getOne("select count(*) as total from uni_action_statistics where action_statistics_to_user_id=? and date(action_statistics_date)=? and action_statistics_ad_id=? and action_statistics_action=?", [$_SESSION["profile"]["id"],$value,$ad_id,$action])["total"];
		}
		}elseif($months){
		foreach ($months as $value) {
		$explode = explode('-', $value);
		$data[] = (int)getOne("select count(*) as total from uni_action_statistics where action_statistics_to_user_id=? and YEAR(action_statistics_date)=? and MONTH(action_statistics_date)=? and action_statistics_ad_id=? and action_statistics_action=?", [$_SESSION["profile"]["id"],$explode[0],$explode[1],$ad_id,$action])["total"];
		}
		}elseif($years){
		foreach ($years as $value) {
		$data[] = (int)getOne("select count(*) as total from uni_action_statistics where action_statistics_to_user_id=? and YEAR(action_statistics_date)=? and action_statistics_ad_id=? and action_statistics_action=?", [$_SESSION["profile"]["id"],$value,$ad_id,$action])["total"];
		}
		}
		}else{
		if($days){
		foreach ($days as $value) {
		$data[] = (int)getOne("select count(*) as total from uni_action_statistics where action_statistics_to_user_id=? and date(action_statistics_date)=? and action_statistics_action=?", [$_SESSION["profile"]["id"],$value,$action])["total"];
		}
		}elseif($months){
		foreach ($months as $value) {
		$explode = explode('-', $value);
		$data[] = (int)getOne("select count(*) as total from uni_action_statistics where action_statistics_to_user_id=? and YEAR(action_statistics_date)=? and MONTH(action_statistics_date)=? and action_statistics_action=?", [$_SESSION["profile"]["id"],$explode[0],$explode[1],$action])["total"];
		}
		}elseif($years){
		foreach ($years as $value) {
		$data[] = (int)getOne("select count(*) as total from uni_action_statistics where action_statistics_to_user_id=? and YEAR(action_statistics_date)=? and action_statistics_action=?", [$_SESSION["profile"]["id"],$value,$action])["total"];
		}
		}                
		}
		
		return implode(',', $data);
		
		}
		
		function usersActionStatistics(){
		$ad_id = (int)$_GET['ad'];
		$data = [];
		
		if($ad_id){
		$get = getAll('select * from uni_action_statistics where action_statistics_to_user_id=? and action_statistics_ad_id=?', [$_SESSION["profile"]["id"],$ad_id]);
		}else{
		$get = getAll('select * from uni_action_statistics where action_statistics_to_user_id=?', [$_SESSION["profile"]["id"]]);
		}
		
		if(count($get)){
		foreach ($get as $value) {
		$getUser = findOne("uni_clients", "clients_id=?", [$value["action_statistics_from_user_id"]]);
		if($getUser) $data[$value['action_statistics_from_user_id']] = $getUser;
		}
		}
		return $data;
		}
		
		function refAlias($ref_id){
		return _link('ref/'.$ref_id);
		}
		
		function getActiveChatDialogs($userId=0){
		$groupByUsers = [];
		$getUsers = getAll("select * from uni_chat_users where chat_users_id_user=? order by chat_users_id desc", array($userId));
		if(count($getUsers)){
		
		foreach ($getUsers as $value) {
		
		$get = findOne("uni_clients", "clients_id=?", [$value["chat_users_id_interlocutor"]]);
		if($get) $groupByUsers[ $value["chat_users_id_hash"] ] = $value;
		
		}
		
		}
		return $groupByUsers;        
		}
		
		function getUserStories(){
		$results = [];
		$getUserStories = getAll("select * from uni_clients_stories order by clients_stories_timestamp desc");
		if($getUserStories){
		foreach ($getUserStories as $key => $value) {
		
		$getUser = findOne('uni_clients', 'clients_id=?', [$value['clients_stories_user_id']]);
		
		if($value['clients_stories_user_id'] == $_SESSION['profile']['id']){
		$getLastStory = findOne('uni_clients_stories_media', 'clients_stories_media_user_id=? and clients_stories_media_loaded=? order by clients_stories_media_timestamp desc', [$value['clients_stories_user_id'],1]);
		}else{
		$getLastStory = findOne('uni_clients_stories_media', 'clients_stories_media_user_id=? and clients_stories_media_loaded=? and clients_stories_media_status=? order by clients_stories_media_timestamp desc', [$value['clients_stories_user_id'],1,1]);
		}
		
		if($getLastStory && $getUser){
		$results[] = [
		"id" => $value['clients_stories_id'],
		"user_id" => $value['clients_stories_user_id'],
		"user_name" => $this->name($getUser),
		"user_avatar" => $this->userAvatar($getUser),
		"timestamp" => $value['clients_stories_timestamp'],
		];
		}
		
		}
		}
		return $results;
		}
		
		function countViewStories($storyId=0){
		if($storyId){
		return (int)getOne('select count(*) as total from uni_clients_stories_view where story_id=?', [$storyId])['total'];
		}
		return 0;
		}
		
		function addSendPulseBook($email, $phone, $name, $lang){
		
		$dataReg['emails'][0] = [
		'email' => $email,
		'variables' =>[
		"Имя" => $name,
		"Phone" => $phone,
		"Update" => date("d M Y"),
		"Язык" => $lang,
		],
		];
		
		$jsonData = json_encode($dataReg);
		$curl = curl_init();
		curl_setopt_array($curl, array(
		CURLOPT_URL => 'https://api.sendpulse.com/addressbooks/319646/emails',
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => '',
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 0,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => 'POST',
		CURLOPT_POSTFIELDS => $jsonData,
		CURLOPT_HTTPHEADER => array(
		'Content-Type: application/json',
		'Authorization: Bearer '.$this->getTokenSendPulse().''
		),
		));
		
		$response = curl_exec($curl);
		curl_close($curl);
		
		if($response){
		return true;
		}else return false;
		
		}
		
		function getTokenSendPulse(){
		
		$curl = curl_init();
		curl_setopt_array($curl, array(
		CURLOPT_URL => 'https://api.sendpulse.com/oauth/access_token',
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => '',
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 0,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => 'POST',
		CURLOPT_POSTFIELDS => '{
		"grant_type":"client_credentials",
		"client_id":"5fb7693b3b70ca80d30d12b708fd3ebb",
		"client_secret":"20b426c4437433dff55a8a8b46c47a01"
		}',
		CURLOPT_HTTPHEADER => array(
		'Content-Type: application/json'
		),
		));
		$response = curl_exec($curl);
		curl_close($curl);
		
		$response = json_decode($response);
		$token = $response->access_token;
		
		return $token;
		}
		
		
		
		}
		?>				