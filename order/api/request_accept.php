<?php
    //디버그용 서비스시 반드시 삭제
    error_reporting(E_ALL);

    ini_set('display_errors', '1'); 
    //헤더 Cors json
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json');
    header('Access-Control-Allow-Methods: PUT');

    include_once '../../config/Database.php';
    include_once '../model/Order_User.php';
    include_once '../model/Order_Product.php';
    include_once '../../user/model/User.php';
    include_once '../../mail/MyMailer.php';
    include_once '../../user/model/Jwt.php';
    include_once '../../product/model/Product.php';
    
    $database = new Database();
    $db = $database->connect();

    //$method = $_SERVER["REQUEST_METHOD"];
    $Order_User = new Order_User($db);
    $Order_Product = new Order_Product($db);
    $mymailer = new MyMailer();
    $jwt = new Jwt();
    $user = new User($db);
    $product = new Product($db);

    //post json형태로 받아서 php array로 만들기
    $__rawBody = file_get_contents("php://input"); // json 본문을 불러옴
    $__getData = array(json_decode($__rawBody))[0]; // 데이터를 변수에 넣고
    $data_arr=array();
    foreach($__getData as $k=>$v){
        $data_arr[$k]=$v;
    }

    //주문 취소/교환/환불 요청 승인에 필요한 정보가 있는지
    $user_need_info = array("order_id");
    foreach($user_need_info as $arr_key){
        if(!array_key_exists($arr_key,$data_arr)){
            header("HTTP/1.1 400");
            echo(json_encode(array("message" => "no ".$arr_key)));
            exit;
        }
    }
    
    //url 파싱
    //$prev_url=$_SERVER['REQUEST_URI'];
    //$urlarr = explode('/',$prev_url);


    //쿠키 확인
    if(!isset($_COOKIE["JWT"])){
        header("HTTP/1.1 401");
        echo(json_encode(array("message"=>"no jwt token")));
        exit;
    }
    
    //jwt 토큰 서명 확인

    $cookie=$_COOKIE["JWT"];
    $Token = $jwt->dehashing($cookie);

    //토큰 서명 불일치
    if($Token == 1){
        header("HTTP/1.1 401");
        echo(json_encode(array("message"=>"invalid jwt token")));
        exit;
    }

    //토큰 만료
    if($Token == 2){
        header("HTTP/1.1 401");
        //추후에 url정해지면 변경
        echo(json_encode(array("message"=>"token expired login needed")));
    }

    //아이디 존재 확인
    $now_user=$user->get_user_from_id($Token);
    if($now_user->rowCount()!=1){       
        header("HTTP/1.1 400");
        echo(json_encode(array("message" => "no user")));
        exit;
    }

    //관리자 권한인지 확인
    if($Token["grade"] != Usergrade::Admin->value){
        header("HTTP/1.1 401");
        echo(json_encode(array("message" => "need admin authority")));
        exit;
    }

    //유저정보받기
    while ( $row = $now_user->fetch( PDO::FETCH_ASSOC ) ){  
        $data_arr["user_id"]=$row["_id"];
        $data_arr["email_address"]=$row["email"];
        $data_arr["name"]=$row["name"];
    }   
    
    //취소/교환/환불 주문정보 가져오기
    while ( $row = $now_user->fetch( PDO::FETCH_ASSOC ) ){  
        $data_arr["request_reason"]=$row["request_reason"];
        $data_arr["request_type"]=$row["request_type"];
        $data_arr["name"]=$row["name"];
    }
    
    
    //주문 취소/교환/환불 처리 완료로 상태변경



    //메일 본문
    $mail_subject = $data_arr["name"]."님 주문이 ".$data_arr["request_type"]."되었습니다.";
    $mail_content = $data_arr.["request_type"]."된 주문번호 ".$data_arr["order_id"]."<br><br>주문상품 목록<br><br>";
    foreach($data_arr["product_list"] as $i){
        $product_result = $product->getbyid($i);
        while($row = $product_result->fetch(PDO::FETCH_ASSOC)){
            $Order_Product->create($result,$i);
            $mail_content.="제품명 ".$row["name"]."<br>가격 ".$row["price"]."<br>";
            $mail_content.="카테고리 ".$row["category"]."<br>중량 ".$row["weight"]."kg<br><br>";
        }
    }
    header("HTTP/1.1 200");
    echo(json_encode(array("message"=>"Request Accepted")));
    $mymailer->send($data_arr["email_address"],$mail_subject,$mail_content);

