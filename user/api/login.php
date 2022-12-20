<?php


    function getRealClientIp() {
        $ipaddress = '';
        if (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        } else if(isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else if(isset($_SERVER['HTTP_X_FORWARDED'])) {
            $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        } else if(isset($_SERVER['HTTP_FORWARDED_FOR'])) {
            $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        } else if(isset($_SERVER['HTTP_FORWARDED'])) {
            $ipaddress = $_SERVER['HTTP_FORWARDED'];
        } else if(isset($_SERVER['REMOTE_ADDR'])) {
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        } else {
            $ipaddress = 'unknown';
        }
        return $ipaddress;
    }
    //디버그용 서비스시 반드시 삭제
    error_reporting(E_ALL);

    ini_set('display_errors', '1'); 
    //헤더 Cors json
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json');
    header('Access-Control-Allow-Methods: POST');

    include_once '../../config/Database.php';
    include_once '../model/User.php';
    include_once '../model/Jwt.php';
    include_once '../../log/model/log_login.php';//로그인 로그 기록 모델
    
    $database = new Database();
    $db = $database->connect();

    //$method = $_SERVER["REQUEST_METHOD"];
    $jwt = new Jwt();
    $user = new User($db);
    $log_login = new log_login($db);

    //post json형태로 받아서 php array로 만들기
    $__rawBody = file_get_contents("php://input"); // json 본문을 불러옴
    $__getData = array(json_decode($__rawBody))[0]; // 데이터를 변수에 넣고
    $data_arr=array();
    foreach($__getData as $k=>$v){
        $data_arr[$k]=$v;
    }

    //로그인에 필요한 정보있는지 체크
    $user_need_info = array("id","password");
    foreach($user_need_info as $arr_key){
        if(!array_key_exists($arr_key,$data_arr)){
            header("HTTP/1.1 400");
            echo(json_encode(array("message" => "no ".$arr_key)));
            exit;
        }
    }
    //아이디 존재 확인
    $now_user=$user->get_user_from_id($data_arr);
    if($now_user->rowCount()!=1){       
        header("HTTP/1.1 400");
        echo(json_encode(array("message" => "wrong id or password")));
        exit;
    }

    //클라이언트 ip 가져오기

    $access_Ip = getRealClientIp();
    
    //user-agent 가져오기

    $user_agent = $_SERVER["HTTP_USER_AGENT"];

    //비밀번호 일치 확인
    $is_login_success=True;
    $now_user_arr=null;
    while ( $row = $now_user->fetch( PDO::FETCH_ASSOC ) ){  
        $now_pw=$row["password"];
        $now_user_arr=$row;
        if(!password_verify($data_arr["password"],$now_pw)){
            $is_login_success=False;
        }
     }  

     //로그인 로그 기록
     $log_data_arr=array(
        "user_id"=>$now_user_arr["_id"],
        "access_IP"=>$access_Ip,
        "user_agent"=>$user_agent,
        "is_fail"=>(int)!$is_login_success
    );
     $log_login->create($log_data_arr);
     if($is_login_success){      
        //로그인시 토큰 발행
        $Token = $jwt->hashing(array("id"=>$data_arr["id"],"exp"=>time()+90000,"grade"=>$now_user_arr["grade"]));
        setcookie("JWT",$Token,['expires'=>time()+90000,'path'=>'/','samesite' => 'None']);
        header("HTTP/1.1 200");
        echo(json_encode(array("message"=> "login success","JWT"=>$Token)));
     }
     else{
        header("HTTP/1.1 400");
        echo(json_encode(array("message" => "wrong id or password")));
        exit;
     }
     
