<?php

class Delivery{
    
    function boxberryCreateParcel($params=[]){

          global $settings;

          $ch = curl_init();
          curl_setopt($ch, CURLOPT_URL, 'http://api.boxberry.ru/json.php');
          curl_setopt($ch, CURLOPT_POST, true);
          curl_setopt($ch, CURLOPT_POSTFIELDS, array(
              'token'=>decrypt($settings['delivery_api_key']),
              'method'=>'ParselCreate',
              'sdata'=>json_encode($params)
          ));
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
          $data = json_decode(curl_exec($ch),1);
          if($data['err'] or count($data)<=0)
          {
              return ['errors'=>$data['err']];
          }
          else
          {
              $getInvoice = json_decode(file_get_contents('http://api.boxberry.ru/json.php?token='.decrypt($settings['delivery_api_key']).'&method=ParselSend&ImIds='.$data['track']), true);
              if(!$getInvoice['err']){
                 return ['invoice_number'=>$getInvoice['id'], 'track_number'=>$data['track']];
              }else{
                 return ['errors'=>$getInvoice['err']];
              }
          }

    }
     
}


?>