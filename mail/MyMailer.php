<?php

    //디버그용 서비스시 반드시 삭제
    error_reporting(E_ALL);

    ini_set('display_errors', '1'); 
    require("PHPMailer.php");  
    require("SMTP.php"); 
    require("Exception.php"); 
    use PHPMailer\PHPMailer\PHPMailer;     
    class MyMailer{
        private $phpmailer;
        private $SMTPAuth=true;
        private $SMTPSecure="tls";
        private $Host="smtp.gmail.com";
        private $port=587;
        private $Password = "hbxclkkylleylugj";
        private $SetFrom = "tkdwo7699@gmail.com";
        private $Username = "tkdwo7699@gmail.com";
        private $FromName="베리굿";

        public function __construct(){
            $this->phpmailer = new PHPMailer;
            $this->phpmailer->IsSMTP();
            $this->phpmailer->SMTPAuth=$this->SMTPAuth;
            $this->phpmailer->SMTPSecure=$this->SMTPSecure;
            $this->phpmailer->Host = $this->Host;
            $this->phpmailer->port = $this->port;
            $this->phpmailer->Username = $this->Username;
            $this->phpmailer->Password = $this->Password;
            $this->phpmailer->SetFrom = $this->SetFrom;
            $this->phpmailer->FromName = $this->FromName;
        }

        public function send($address,$subject,$body){
            $this->phpmailer->AddAddress($address);
            $this->phpmailer->Subject =$subject;
            $this->phpmailer->Body = $body;
            $this->phpmailer->IsHTML(true);
            if(!$this->phpmailer -> Send()){
                return False;
            }
            else{
                return True;
            }
        }
    }
?>