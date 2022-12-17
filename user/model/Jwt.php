<?php

    function base64UrlEncode(string $data): string
    {
        $base64Url = strtr(base64_encode($data), '+/', '-_');

        return rtrim($base64Url, '=');
    }

    function base64UrlDecode(string $base64Url): string
    {
        return base64_decode(strtr($base64Url, '-_', '+/'));
    }

    enum Tokentype : int{
        case Refresh=0;
        case Access=1;
    }

    enum TokenResponse : int{
        case Token_Expire=0;
        case Token_Nomatch=1;
        case Token_Match=2;
    }
    class Jwt{
        private $alg;
        private $secret_key;

        public function __construct(){
            $this->alg="sha256";
            $this->secret_key="berrygood123";
        }

        public function hashing(array $data){
            $header = base64UrlEncode(
                json_encode(array(
                    'alg'=>$this->alg,
                    'typ'=>'JWT'
            )));

            $payload = base64UrlEncode(json_encode($data));

            $signature = hash_hmac($this->alg,$header.".".$payload,$this->secret_key);
            return $header.".".$payload.".".$signature;
        }   

        public function dehashing($token){

            //토큰 파싱 header.payload.signature
            $token_base64=explode('.',$token);  
            
            //받은 시그니처
            $signature=$token_base64[2];

            //받은 헤더.페이로드,시크릿키 = 만든 시그니쳐
            $header_payload=hash_hmac($this->alg,$token_base64[0].".".$token_base64[1],$this->secret_key);
            //base64타입 토큰 -> json타입 토큰-> payload를 associative array로 디코드
            $token_payload_json=json_decode(base64UrlDecode($token_base64[1]));
            //받은 시그니처를 통해 헤더 페이로드의 무결성 검증
            $token_payload_arr=array();
            foreach($token_payload_json as $k => $v){
                $token_payload_arr[$k]=$v;
            }
            if($signature != $header_payload){
                return TokenResponse::Token_Nomatch->value;
            }
            else{
                //만료시간 검사 (추후에 refresh 토큰 도입시 변경)
                //토큰 만료시 로그인페이지로 리다이렉트?
                $now_time=time();
                if($now_time>$token_payload_arr['exp']){
                    return TokenResponse::Token_Expire->value;
                }
            }

            return $token_payload_arr;
        }
     }