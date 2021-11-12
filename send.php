<?

// mail
$to = 'order@salesgenerator.pro';
$subject = 'Сообщение из формы для тестового задания';
$headers = "From: no-reply@ditrim.ru\r\n";
$headers .= 'Content-type: text/html; charset=utf-8\r\n';


$message = '<p>Сообщение из формы для тестового задания</p><p><b>email</b>: '.$_POST['email'].'<br><b>Телефон</b>:'.$_POST['phone'].'</p>';

mail($to, $subject, $message, $headers);

///amoCRM

$refresh_token = file_get_contents('./t/ref-token.txt');


$client_id = "c29680d0-280f-40f4-b605-25437bae1f15";
$client_secret = "3mEmPEpS8Qz3YOuOHPDFSXnWi4kE6VYpc1gNWuD5TzVSSUMgF2pbayMePMXhZvBR";
$grant_type_access =  "refresh_token";





$subdomain = "https://ditrim.ru/test-unit/"; //Поддомен нужного аккаунта
$link = 'https://1982dmitrii19821982.amocrm.ru/oauth2/access_token'; //Формируем URL для запроса

/** Соберем данные для запроса */
$data = [
	'client_id' => $client_id,
	'client_secret' => $client_secret,
	'grant_type' => $grant_type_access,
	'refresh_token' => $refresh_token,
	'redirect_uri' => $subdomain,
];

$curl = curl_init();
curl_setopt($curl,CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl,CURLOPT_USERAGENT,'amoCRM-oAuth-client/1.0');
curl_setopt($curl,CURLOPT_URL, $link);
curl_setopt($curl,CURLOPT_HTTPHEADER,['Content-Type:application/json']);
curl_setopt($curl,CURLOPT_HEADER, false);
curl_setopt($curl,CURLOPT_CUSTOMREQUEST, 'POST');
curl_setopt($curl,CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($curl,CURLOPT_SSL_VERIFYPEER, 1);
curl_setopt($curl,CURLOPT_SSL_VERIFYHOST, 2);
$out = curl_exec($curl); 
$code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
curl_close($curl);

$code = (int)$code;

if ($code < 200 || $code > 204) {
    http_response_code(400);
}

$response = json_decode($out, true);

$access_token = $response['access_token']; //Access токен
$refresh_token = $response['refresh_token']; //Refresh токен
$token_type = $response['token_type']; //Тип токена
$expires_in = $response['expires_in']; //Через сколько действие токена истекает

file_put_contents('./t/ref-token.txt', $refresh_token);


//push contact

$headers = [
	'Authorization: Bearer ' . $access_token,
    'Content-Type: application/json'
];

$query = [
    [
        "name"=> "Заявка Шакуло",
        "custom_fields_values" => [
            [
                "field_id"=> 308655,
                "values"=> [
                    [
                        "value" => $_POST['phone']
                    ]
                ]
            ],
            [
                "field_id" => 308657,
                "values"=> [
                    [
                        "value"=> $_POST['email']
                    ]
                ]
            ]
        ]
    ]
];


$link = 'https://1982dmitrii19821982.amocrm.ru/api/v4/contacts';

$curl = curl_init();
curl_setopt($curl,CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl,CURLOPT_USERAGENT,'amoCRM-oAuth-client/1.0');
curl_setopt($curl,CURLOPT_URL, $link);
curl_setopt($curl,CURLOPT_HTTPHEADER,$headers);
curl_setopt($curl,CURLOPT_HEADER, false);
curl_setopt($curl,CURLOPT_CUSTOMREQUEST, 'POST');
curl_setopt($curl,CURLOPT_POSTFIELDS, json_encode($query));
curl_setopt($curl,CURLOPT_SSL_VERIFYPEER, 1);
curl_setopt($curl,CURLOPT_SSL_VERIFYHOST, 2);
$out = curl_exec($curl); 
$code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
curl_close($curl);

$response = json_decode($out, true);

$code = (int)$code;

if ($code < 200 || $code > 204) {
    http_response_code(400);
} else {
    http_response_code(200);
}


?>