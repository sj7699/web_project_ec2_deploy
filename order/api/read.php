<?php
    //디버그용 서비스시 반드시 삭제
    // error_reporting(E_ALL);

    // ini_set('display_errors', '1'); 
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

    //주문조회에 필요한 정보있는지 체크
    $user_need_info = array("JWT");
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
        exit;
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
    $result = array();
    if($Token["grade"]==Usergrade::Admin){
        $result = $Order_User->read($data_arr);
    }
    else{
        $result = $Order_User->read_customer($data_arr["user_id"]);
    }

    $num = $result->rowCount();

    if($num>0){
        $posts_arr = array();
        $posts_arr['data'] = array();
        while($row = $result->fetch(PDO::FETCH_ASSOC)){
            $post_item= array(
                'orderNumber'=>$row["_id"],
                'created_at'=>date("Y-m-d",$row["created_at"]),
                'ordererMessage'=>$row["ordererMessage"],
                'recipientAddr'=>$row["recipientAddr"],
                'recipientPhone'=>$row["recipientPhone"],
                'recipientName'=>$row["recipientName"],
                "delivery_state"=>$row["delivery_state"],
                "recipientPost"=>$row["recipientPost"],
                "recipientEmail"=>$row["recipientEmail"]

            );
            $post_item["total_price"]=0;
            $product_result = $Order_Product->getproductbyorderid($row["_id"]);
            while($row2 = $product_result->fetch(PDO::FETCH_ASSOC)){
                $post_item["total_price"]+=$row2["price"];
            }
            array_push($posts_arr['data'],$post_item);
        }
        echo json_encode($posts_arr);
    }
    else{
        header("HTTP/1.1 404");
        echo json_encode(
            array('message' => "no post")
        );
    }