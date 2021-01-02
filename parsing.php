<?php
set_time_limit(0);
$data = file("cluster_report_engine_init_20210102.txt",FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES); //read the entire file to array by ignoring new lines and spaces


$final_array = array(); // create an empty array

foreach ($data as $key=> $dat){ // iterate over file() generated array

    $final_data = explode('	',$dat); //explode the data with space

    //now assign the value to corresponding indexes

    $final_array[$key]['fileContent']   = stripslashes(urldecode(substr($final_data[4],12)));

}

//print_r($final_array); // print the final output


// print_r($final_array);

// $x= 0;
foreach($final_array as $dat){

    // if($x == "3"){
    //     break;
    // }

    $dataArr = json_decode(json_encode($dat['fileContent']),true);

    $data = json_decode($dataArr,true);

    $param = array(
        "vm_code" => $data['vmCode'],
        "slot" => $data['slot'],
        "redeem_status" => $data['redeemStatus'],
        "timestamp" => $data['timestamp'],
        "error_code" => $data['errorCode'],
        "user_identity" => $data['userIdentity'],
        "pin" => $data['pin'],
        "meta_code" => $data['metaCode'],
        "transactionId" => $data['transactionId'],
        "payment_method" => isset($data['paymentMethod']) ?  $data['paymentMethod']:'',
        "payment_source" => isset($data['paymentSource']) ?  $data['paymentSource']:'',
        "promo_used" => isset($data['promoUsed']) ? $data['promoUsed']:'' 

    );

   
    $req_url ="http://mobisuite.andalabs.com/vm-cms/stock/redeem/v2/".urlencode(json_encode($param));
    //$req_url ="http://andalabs.net:8067/vm-cms/stock/redeem/v2/".urlencode(json_encode($param));

    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => $req_url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => array(
        "Accept: /",
        "Cache-Control: no-cache",
        "Connection: keep-alive",
        "accept-encoding: gzip, deflate",
        "cache-control: no-cache"
        ),
    ));

    $res = curl_exec($curl);
    $info	= curl_getinfo($curl);
    echo json_encode($res);

	$log  =		    "trx_code=".$data['transactionId']. "\t".
					"slot=".$data['slot']. "\t".
                    "redeem_status=".$data['redeemStatus']. "\t".
                    "response_redeem=".json_encode($res). "\t".
                    "timestamp=".$data['timestamp']. "\n"
			;
	file_put_contents('log_tanggal_2.log', $log, FILE_APPEND);
    
    sleep(3);
    // $x++;
 
    // echo $dat['fileContent'];
    // echo "<br>";
 
}