<?php
    //디버그용 서비스시 반드시 삭제
    // error_reporting(E_ALL);

    // ini_set('display_errors', '1'); 
    //헤더 Cors json
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json');
    header('Access-Control-Allow-Methods: POST');

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
    foreach($__getData as $k=>$v){
        $data_arr[$k]=$v;
    }

    if(!isset($_GET["product_id"])){
        header("HTTP/1.1 400");
        echo(json_encode(array("message"=>"need query_string product_id")));
        exit;
    }
    else{
        $data_arr["id"]=$_GET["product_id"];
    }
    //url 파싱
    //$prev_url=$_SERVER['REQUEST_URI'];
    //$urlarr = explode('/',$prev_url);

    $result = $product->read_specific($data_arr["id"]);
    $num=$result->rowCount();

    if($num>0){
        header("HTTP/1.1 200");
        while($row = $result->fetch(PDO::FETCH_ASSOC)){
            extract($row);
            $post_item= array(
                'name' => $name,
                'price' => $price,
                'weight' => $weight,
                'image' => $image,
                'category' => $category,
                'created_at' => $created_at,
                'detail' => html_entity_decode($detail)
            );
            echo json_encode($post_item);
            exit;
        }
    }
    else{
        header("HTTP/1.1 404");
        echo json_encode(
            array('message' => "no product")
        );
    }