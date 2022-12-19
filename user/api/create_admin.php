<?php
    //디버그용 서비스시 반드시 삭제
    // error_reporting(E_ALL);

    // ini_set('display_errors', '1'); 
    //헤더 Cors json
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json');
    header('Access-Control-Allow-Methods: POST');

    include_once '../../config/Database.php';
    include_once '../model/User.php';
    include_once '../model/Jwt.php';
    
    $database = new Database();
    $db = $database->connect();

    //$method = $_SERVER["REQUEST_METHOD"];
    $jwt = new Jwt();
    $user = new User($db);

    //post json형태로 받아서 php array로 만들기
    $__rawBody = file_get_contents("php://input"); // json 본문을 불러옴
    $__getData = array(json_decode($__rawBody))[0]; // 데이터를 변수에 넣고
    $data_arr=array();
    foreach($__getData as $k=>$v){
        $data_arr[$k]=$v;
    }

    //회원가입에 필요한 정보있는지 체크
    $user_need_info = array("id","password","name","email","address","phone_number","home_number","birthday");
    foreach($user_need_info as $arr_key){
        if(!array_key_exists($arr_key,$data_arr)){
            header("HTTP/1.1 400");
            echo(json_encode(array("message" => "no ".$arr_key)));
            exit;
        }
    }
    if(!isset($data_arr["admin_password"])){
        header("HTTP/1.1 401");
        echo(json_encode(array("message" => "no authority")));
        exit;
    }
    if($data_arr["admin_password"]!="berrygood_jeju"){
        header("HTTP/1.1 401");
        echo(json_encode(array("message" => "no authority")));
        exit;
    }
    //url 파싱
    //$prev_url=$_SERVER['REQUEST_URI'];
    //$urlarr = explode('/',$prev_url);

    //유저 정보 생성    
    if($user->create_admin($data_arr)){
        header("HTTP/1.1 201");
        echo(json_encode(array("message"=> "admin created")));
    }else{
        header("HTTP/1.1 400");
        echo(json_encode(array("message"=>"id or email already exist")));
    }
