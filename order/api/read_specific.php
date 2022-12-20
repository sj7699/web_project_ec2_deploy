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
    if(!isset($_GET["order_id"])){
        header("HTTP/1.1 400");
        echo(json_encode(array("message"=>"need query string order_id")));
        exit;
    }
    $data_arr["order_id"]=$_GET["order_id"];
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
        $data_arr["email_address"]=$row["email"];
        $data_arr["name"]=$row["name"];
    }

    //주문 금액 받기
    $result = $Order_User->getbyorderid($data_arr["order_id"]);
    $result_arr=$result->fetchall();
    if(count($result_arr)==0){
        header("HTTP/1.1 400");
        echo(json_encode(array("message"=>"No Order")));
        exit;
    }
    if($Token["grade"]!=Usergrade::Admin){
        if($result_arr[0]["user_id"]!=$data_arr["user_id"]){
            header("HTTP/1.1 401");
            echo(json_encode(array("message"=>"other's order")));
            exit;
        }
    }

    $num = $result->rowCount();

    if($num>0){
        $posts_arr = array();
        $posts_arr['data'] = array();
        foreach($result_arr as $row){
            $post_item= array(
                'orderNumber'=>$row["_id"],
                'created_at'=>date("Y-m-d",$row["created_at"]),
                'ordererMessage'=>$row["ordererMessage"],
                'recipientAddr'=>$row["recipientAddr"],
                'recipientPhone'=>$row["recipientPhone"],
                'recipientName'=>$row["recipientName"],
                "delivery_state"=>$row["delivery_state"],
                "recipientPost"=>$row["recipientPost"],
                "recipientEmail"=>$row["recipientEmail"],
                "product_list"=>array()
            );
            $post_item["total_price"]=0;
            $product_result = $Order_Product->getproductbyorderid($row["_id"]);
            while($row2 = $product_result->fetch(PDO::FETCH_ASSOC)){
                $post_item["total_price"]+=$row2["price"]*$row2["num"];
                array_push($post_item["product_list"],array("productName"=>$row2["name"],"productPrice"=>$row2["price"],"img"=>$row2["image"],"productNum"=>$row2["num"]));
            }

            array_push($posts_arr['data'],$post_item);
        }
        echo json_encode($posts_arr);
    }
    else{
        header("HTTP/1.1 404");
        echo json_encode(
            array('message' => "no order")
        );
    }