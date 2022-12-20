<?php
    //디버그용 서비스시 반드시 삭제
    error_reporting(E_ALL);

    ini_set('display_errors', '1'); 
    //헤더 Cors json
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json');

    include_once '../../../config/Database.php';
    include_once '../../models/Post.php';
    include_once '../../models/Comment.php';
    
    $database = new Database();
    $db = $database->connect();
    $method = $_SERVER["REQUEST_METHOD"];
    $post = new POST($db);
    $comment = new Comment($db);
    $result=null;
    if(isset($_GET["category"])){
        if(isset($_GET["page"])){
            $result = $post->readbycategory($_GET["category"],$_GET["page"]);
        }
        else{
            header("HTTP/1.1 400");
            echo(json_encode(array("message"=>"need querystring page")));
            exit;
        }
    }
    else{
        if(isset($_GET["page"])){
            $result = $post->read($_GET["page"]);
        }
        else{
            header("HTTP/1.1 400");
            echo(json_encode(array("message"=>"need querystring page")));
            exit;
        }
    }
    //url 파싱
    $prev_url=$_SERVER['REQUEST_URI'];
    $urlarr = explode('/',$prev_url);


    $num = $result->rowCount();
    if($num>0){
        $posts_arr = array();
        $posts_arr['data'] = array();
        while($row = $result->fetch(PDO::FETCH_ASSOC)){
            extract($row);
            $post_id_arr=array();
            $post_id_arr["post_id"]=$_id;
            $comment_stmt=$comment->read($post_id_arr);
            $answer=false;
            if($comment_stmt->rowCount()>0){
                $answer=true;
            }
            $post_item= array(
                'views' => $views,
                '_id' => $_id,
                'title' => $title,
                'author' => $id,
                'category' => $category,
                'created_at' => date("Y-m-d",$created_at),
                'iscomplete' => $answer
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