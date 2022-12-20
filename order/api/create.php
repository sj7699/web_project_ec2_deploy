<?php
    //디버그용 서비스시 반드시 삭제
    // error_reporting(E_ALL);

    // ini_set('display_errors', '1'); 
    //헤더 Cors json
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json');
    header('Access-Control-Allow-Methods: OPTION,POST,PUT');

    include_once '../../config/Database.php';
    include_once '../model/Order_User.php';
    include_once '../model/Order_Product.php';
    include_once '../../user/model/User.php';
    include_once '../../mail/MyMailer.php';
    include_once '../../user/model/Jwt.php';
    include_once '../../user/model/User.php';
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
    $__getData = array(json_decode($__rawBody,true))[0]; // 데이터를 변수에 넣고
    $data_arr=array();
    foreach($__getData as $k=>$v){
        $data_arr[$k]=$v;
    }

    //주문생성에 필요한 정보있는지 체크
    $user_need_info = array("JWT","ordererMessage","recipientAddr","recipientAddrDetail","product_list","recipientPhone","recipientPost","recipientName","recipientEmail");
    foreach($user_need_info as $arr_key){
        if(!array_key_exists($arr_key,$data_arr)){
            header("HTTP/1.1 400");
            echo(json_encode(array("message" => "no ".$arr_key)));
            exit;
        }
    }

    //주문 물건이 없으면
    if(count($data_arr["product_list"])==0){
        header("HTTP/1.1 400");
        echo(json_encode(array("message" => "Product required")));
        exit;
    }
    //url 파싱
    //$prev_url=$_SERVER['REQUEST_URI'];
    //$urlarr = explode('/',$prev_url);


    //쿠키 확인
    // if(!isset($_COOKIE["JWT"])){
    //     header("HTTP/1.1 401");
    //     echo(json_encode(array("message"=>"no jwt token")));
    //     exit;
    // }
    
    //jwt 토큰 서명 확인
    $cookie=$data_arr["JWT"];
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

    //유저정보받기
    while ( $row = $now_user->fetch( PDO::FETCH_ASSOC ) ){  
        $data_arr["user_id"]=$row["_id"];
        $data_arr["ordererEmail"]=$row["email"];
        $data_arr["ordererName"]=$row["name"];
        $data_arr["ordererPhone"]=$row["phone_number"];
    }

    //주문 금액 받기
    $order_total_price = 0;
    //주문 정보 생성
    $result = $Order_User->create($data_arr);
    $mail_subject = $data_arr["ordererName"]."님 베리굿 주문정보입니다 ^^";
    $mail_content = "주문번호 ".$result."<br><br>주문상품 목록<br><br>";
    foreach($data_arr["product_list"] as $i){
        $product_result = $product->getbyname($i["productName"]);
        while($row = $product_result->fetch(PDO::FETCH_ASSOC)){
            $Order_Product->create($result,$row["_id"],$i["productNum"]);
            // $mail_content.="제품명 ".$row["name"]."<br>가격 ".$row["price"]."원<br>";
            // $mail_content.="카테고리 ".$row["category"]."<br>중량 ".$row["weight"]."kg<br>";
            // $order_total_price+=$row["price"];
        }
    }
    // $mail_content.="<br>주문 총 금액 ".$order_total_price."원<br>";
    // $mail_content.="배송지 주소 ".$data_arr["address"]."<br>연락처 ".$data_arr["phone_number"]."<br>";
    // $mail_content.="수령인 ".$data_arr["recipient"]."<br>요청사항 ".$data_arr["detail"]."<br><br>";
    header("HTTP/1.1 201");
    echo(json_encode(array("message"=>"Order Accepted","order_id"=>$result)));
    #$mymailer->send($data_arr["email_address"],$mail_subject,$mail_content)

