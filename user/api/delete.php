<?php
    //디버그용 서비스시 반드시 삭제
    error_reporting(E_ALL);

    ini_set('display_errors', '1'); 
    //헤더 Cors json
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json');
    header('Access-Control-Allow-Methods: PUT');

    include_once '../../config/Database.php';
    include_once '../model/User.php';
    include_once '../model/Jwt.php';
    
    $database = new Database();
    $db = $database->connect();

    //$method = $_SERVER["REQUEST_METHOD"];
    $jwt = new Jwt();
    $user = new User($db);

    //post json형태로 받아서 php array로 만들기
    $__rawBody = file_get_contents("php://input"); // json 본문을 불러옴
    $__getData = array(json_decode($__rawBody))[0]; // 데이터를 변수에 넣고
    $data_arr=array();
    foreach($__getData as $k=>$v){
        $data_arr[$k]=$v;
    }

    //회원탈퇴에 필요한 정보있는지 체크
    $user_need_info = array("JWT");
    foreach($user_need_info as $arr_key){
        if(!array_key_exists($arr_key,$data_arr)){
            header("HTTP/1.1 400");
            echo(json_encode(array("message" => "no ".$arr_key)));
            exit;
        }
    }

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
    }

    //아이디 존재 확인
    $now_user=$user->get_user_from_id($Token);
    if($now_user->rowCount()!=1){       
        header("HTTP/1.1 400");
        echo(json_encode(array("message" => "no user")));
        exit;
    }

    //유저 정보 수정
    if($user->delete_user($Token)){
        header("HTTP/1.1 200");
        echo(json_encode(array("message"=> "account deleted")));
    }else{
        header("HTTP/1.1 400");
        echo(json_encode(array("message"=>"no user")));
    }
