<?php
    //디버그용 서비스시 반드시 삭제
    error_reporting(E_ALL);

    ini_set('display_errors', '1'); 
    //헤더 Cors json
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json');

    include_once '../../../config/Database.php';
    include_once '../../models/Post.php';
    
    $database = new Database();
    $db = $database->connect();
    $method = $_SERVER["REQUEST_METHOD"];
    $post = new POST($db);
    $result = $post->read();

    //url 파싱
    $prev_url=$_SERVER['REQUEST_URI'];
    $urlarr = explode('/',$prev_url);


    $num = $result->rowCount();

    if($num>0){
        $posts_arr = array();
        $posts_arr['data'] = array();
        while($row = $result->fetch(PDO::FETCH_ASSOC)){
            extract($row);
            $post_item= array(
                'views' => $views,
                '_id' => $_id,
                'title' => $title,
                'author' => $id,
                'category' => $category,
                'created_at' => $created_at
            );

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