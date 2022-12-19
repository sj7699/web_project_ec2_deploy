<?php
    //디버그용 서비스시 반드시 삭제
    //error_reporting(E_ALL);

    //ini_set('display_errors', '1'); 
    //헤더 Cors json
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json');
    header('Access-Control-Allow-Methods: GET');

    include_once '../../config/Database.php';
    include_once '../model/Product.php';
    
    $database = new Database();
    $db = $database->connect();

    //$method = $_SERVER["REQUEST_METHOD"];
    $product = new Product($db);

    //post json형태로 받아서 php array로 만들기
    $__rawBody = file_get_contents("php://input"); // json 본문을 불러옴
    $__getData = array(json_decode($__rawBody))[0]; // 데이터를 변수에 넣고
    $data_arr=array();
    if(isset($_GET["order_by"])){
       $data_arr["order_by"]=$_GET["order_by"]; 
    }
    //상품 조회에 필요한 정보 확인
    $user_need_info = array();
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

    $result = $product->read($data_arr);
    $num=$result->rowCount();

    //응답 json변환
    if($num>0){
        header("HTTP/1.1 200");
        $posts_arr = array();
        $posts_arr['data'] = array();
        while($row = $result->fetch(PDO::FETCH_ASSOC)){
            extract($row);
            $post_item= array(
                'name' => $name,
                '_id' => $_id,
                'price' => $price,
                'weight' => $weight,
                'category' => $category,
                'created_at' => $created_at,
                'image' => $image
            );

            array_push($posts_arr['data'],$post_item);
        }
        echo json_encode($posts_arr);
    }
    else{
        header("HTTP/1.1 404");
        echo json_encode(
            array('message' => "no product")
        );
    }