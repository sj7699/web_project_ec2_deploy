<?php
    //디버그용 서비스시 반드시 삭제
    // error_reporting(E_ALL);

    // ini_set('display_errors', '1'); 
    //헤더 Cors json
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json');
    header('Access-Control-Allow-Methods: POST');

    include_once '../../../config/Database.php';
    include_once '../../models/Post.php';
    include_once '../../../user/model/Jwt.php';
    include_once '../../models/Comment.php';
    
    $database = new Database();
    $db = $database->connect();

    //$method = $_SERVER["REQUEST_METHOD"];
    $jwt = new Jwt();
    $post = new Post($db);
    $comment = new Comment($db);

    //post json형태로 받아서 php array로 만들기
    $__rawBody = file_get_contents("php://input"); // json 본문을 불러옴
    $__getData = array(json_decode($__rawBody))[0]; // 데이터를 변수에 넣고
    if(!isset($_GET["post_id"])){
        header("HTTP/1.1 400");
        echo(json_encode(array("message" => "no query string post_id")));
        exit;
    }
    $data_arr["post_id"]=$_GET["post_id"];
    //게시물조회에 필요한 정보있는지 체크
    $user_need_info = array("post_id");
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


    $result = $comment->read($data_arr);
    $num=$result->rowCount();
    if($num>0){
        $comments_arr = array();
        while($row = $result->fetch(PDO::FETCH_ASSOC)){
            extract($row);
            $comment_item= array(
                '_id' => $_id,
                'author' => $id,
                'content' => html_entity_decode($content),
                'created_at' => date("Y-m-d",$created_at)
            );

            array_push($comments_arr,$comment_item);
        }
        echo json_encode($comments_arr);
    }
    else{
        header("HTTP/1.1 404");
        echo json_encode(
            array('message' => "no comment")
        );
    }