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
    include_once '../../user/model/Jwt.php';
    
    $database = new Database();
    $db = $database->connect();

    //$method = $_SERVER["REQUEST_METHOD"];
    
    $jwt = new Jwt();
    $product = new Product($db);

    //post json형태로 받아서 php array로 만들기
    $__rawBody = file_get_contents("php://input"); // json 본문을 불러옴
    $__getData = array(json_decode($__rawBody))[0]; // 데이터를 변수에 넣고
    $data_arr=array();
    foreach($__getData as $k=>$v){
        $data_arr[$k]=$v;
    }

    //게시물조회에 필요한 정보있는지 체크
    $user_need_info = array("keyword","search_by");
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

    /*
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
    */
    $result=null;
    if($data_arr["search_by"]=="name"){
        $result = $product->search_by_name($data_arr);
    }
    else if($data_arr["search_by"]=="category"){
        $result = $product->search_by_category($data_arr);
    }
    else if($data_arr["search_by"]=="namecategory"){
        if(!isset($data_arr["keyword2"])){
            header("HTTP/1.1 400");
            echo(json_encode(array("message"=>"need category keyword")));
            exit;
        }
        $result = $product->search_by_name_and_category($data_arr);
    }
    else{
        header("HTTP/1.1 400");
        echo(json_encode(array("message"=>"search_by name or category")));
        exit;
    }

    $num=$result->rowCount();
    if($num>0){
        $products_arr = array();
        $products_arr['data'] = array();
        while($row = $result->fetch(PDO::FETCH_ASSOC)){
            extract($row);
            $product_item= array(
                'name' => $name,
                '_id' => $_id,
                'price' => $price,
                'category' => $category,
                'price' => $price,
                'weight' => $weight
            );
            array_push($products_arr['data'],$product_item);
        }
        echo(json_encode($products_arr));
    }
    else{
        header("HTTP/1.1 404");
        echo json_encode(
            array('message' => "no product")
        );
    }