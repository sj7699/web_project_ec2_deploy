<?php
    //디버그용 서비스시 반드시 삭제
    //error_reporting(E_ALL);

    //ini_set('display_errors', '1'); 
    //헤더 Cors json
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json');
    header('Access-Control-Allow-Methods: POST');

    include_once '../../../config/Database.php';
    include_once '../../models/Post.php';
    include_once '../../../user/model/Jwt.php';
    
    $database = new Database();
    $db = $database->connect();

    //$method = $_SERVER["REQUEST_METHOD"];
    $jwt = new Jwt();
    $post = new Post($db);

    //post json형태로 받아서 php array로 만들기
    $__rawBody = file_get_contents("php://input"); // json 본문을 불러옴
    $__getData = array(json_decode($__rawBody))[0]; // 데이터를 변수에 넣고
    $data_arr=array();
    if(isset($_GET["post_id"])){
        $data_arr["id"]=$_GET["post_id"];
    }
    else{
        header("HTTP/1.1 400");
        echo(json_encode(array("message" => "need post_id in query string")));
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
    $result = $post->read_specific($data_arr["id"]);
    $num=$result->rowCount();
    if($num>0){
        while($row = $result->fetch(PDO::FETCH_ASSOC)){
            extract($row);
            $post_item= array(
                'views' => $views,
                '_id' => $_id,
                'title' => $title,
                'author' => $id,
                'content' => html_entity_decode($content),
                'category' => $category,
                'created_at' => date("Y-m-d",$created_at)
            );
            echo json_encode($post_item);
            exit;
        }
    }
    else{
        header("HTTP/1.1 404");
        echo json_encode(
            array('message' => "no post")
        );
    }