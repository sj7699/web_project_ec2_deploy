<?php
    //디버그용 서비스시 반드시 삭제
    error_reporting(E_ALL);

    ini_set('display_errors', '1'); 
    //헤더 Cors json
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json');
    header('Access-Control-Allow-Methods: GET');

    include_once '../../config/Database.php';
    include_once '../model/log_login.php';
    include_once '../../user/model/User.php';
    include_once '../../user/model/Jwt.php';
    
    $database = new Database();
    $db = $database->connect();

    //$method = $_SERVER["REQUEST_METHOD"];
    $log_login = new log_login($db);
    $jwt = new Jwt();
    $user = new User($db);

    //post json형태로 받아서 php array로 만들기
    $__rawBody = file_get_contents("php://input"); // json 본문을 불러옴
    // $__getData = array(json_decode($__rawBody))[0]; // 데이터를 변수에 넣고

    // //post data -> php array
    // $data_arr=array();
    // foreach($__getData as $k=>$v){
    //     $data_arr[$k]=$v;
    // }

    //상품 조회에 필요한 정보 확인
    $user_need_info = array();
    foreach($user_need_info as $arr_key){
        if(!array_key_exists($arr_key,$data_arr)){
            header("HTTP/1.1 400");
            echo(json_encode(array("message" => "no ".$arr_key)));
            exit;
        }
    }
    
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
        exit;
    }
    //관리자 권한인지 확인
    if($Token["grade"] != Usergrade::Admin->value){
        header("HTTP/1.1 401");
        echo(json_encode(array("message" => "need admin authority")));
        exit;
    }
    $data_arr["id"]=$Token["id"];

    //아이디 존재 확인
    $now_user=$user->get_user_from_id($data_arr);
    if($now_user->rowCount()!=1){       
        header("HTTP/1.1 400");
        echo(json_encode(array("message" => "no user")));
        exit;
    }
    //url 파싱
    //$prev_url=$_SERVER['REQUEST_URI'];
    //$urlarr = explode('/',$prev_url);

    $result = $log_login->read($data_arr);
    $num=$result->rowCount();
    //응답 json변환
    if($num>0){
        header("HTTP/1.1 200");
        $posts_arr = array();
        $posts_arr['data'] = array();
        while($row = $result->fetch(PDO::FETCH_ASSOC)){
            extract($row);
            $post_item= array(
                'id' => $id,
                'accessed_at' => $accessed_at,
                'access_IP' => $access_IP,
                'user_agent'=>$user_agent,

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