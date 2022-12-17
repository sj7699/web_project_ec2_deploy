<?php
    //디버그용 서비스시 반드시 삭제
    error_reporting(E_ALL);

    ini_set('display_errors', '1'); 
    //헤더 Cors json
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json');
    header('Access-Control-Allow-Methods: POST');

    include_once '../../../config/Database.php';
    include_once '../../models/Comment.php';
    include_once '../../../user/model/Jwt.php';
    include_once '../../../user/model/User.php';
    
    $database = new Database();
    $db = $database->connect();

    //$method = $_SERVER["REQUEST_METHOD"];
    $jwt = new Jwt();
    $comment = new Comment($db);
    $user = new User($db);

    //post json형태로 받아서 php array로 만들기
    $__rawBody = file_get_contents("php://input"); // json 본문을 불러옴
    $__getData = array(json_decode($__rawBody))[0]; // 데이터를 변수에 넣고
    $data_arr=array();
    foreach($__getData as $k=>$v){
        $data_arr[$k]=$v;
    }

    //댓글 작성 필요한 정보있는지 체크
    $user_need_info = array("post_id","content");
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
    if(!isset($_COOKIE["JWT"])){
        header("HTTP/1.1 401");
        echo(json_encode(array("message"=>count($_COOKIE))));
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
    
    $data_arr["id"]=$Token["id"];

    //아이디 존재 확인
    $now_user=$user->get_user_from_id($data_arr);
    if($now_user->rowCount()!=1){       
        header("HTTP/1.1 400");
        echo(json_encode(array("message" => "no user")));
        exit;
    }

    while ( $row = $now_user->fetch( PDO::FETCH_ASSOC ) ){  
        $data_arr["user_id"]=$row["_id"];
     }  
    //댓글 생성
    if($comment->create($data_arr)){
        header("HTTP/1.1 201");
        echo(json_encode(array("message"=> "comment created")));
    }else{
        header("HTTP/1.1 400");
        echo(json_encode(array("message"=>"no user")));
    }
