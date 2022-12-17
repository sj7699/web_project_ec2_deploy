<?php
    class log_login{
        private $conn;
        public $table = 'tlog_login';
        public $user_table='tuser';
        public $_id;
        public $user_id;
        public $accessed_at;
        public $access_IP;
        public $user_agent;
        
        public function __construct($db){
            $this->conn = $db;
            $this->table='tlog_login';
        }

        //로그인 로그 생성
        public function create($data_arr){
            $query= 'INSERT INTO '.$this->table.'(user_id,accessed_at,access_IP,user_agent,is_fail) VALUES(:user_id,:accessed_at,:access_IP,:user_agent,:is_fail)';
            $stmt = $this->conn->prepare($query); 
            $stmt->bindValue(':user_id',$data_arr["user_id"]);
            $stmt->bindValue(':accessed_at',time());
            $stmt->bindValue(':access_IP',$data_arr['access_IP']);
            $stmt->bindValue(':user_agent',$data_arr['user_agent']);
            $stmt->bindValue(':is_fail',$data_arr['is_fail']);
            $stmt->execute();
            return true;
        }

        //로그인 로그 전체 읽기
        public function read($data_arr){
            $query = "SELECT ".$this->table."._id,".$this->table.".user_agent,".$this->table.".accessed_at,".$this->table.".access_IP,".$this->user_table.".id FROM ".$this->table." INNER JOIN ".$this->user_table." ON ".$this->table.".user_id = ".$this->user_table."._id where post_id = ".$data_arr["post_id"]." order by ".$this->table.".created_at desc;";
            //$query = "Show tables;";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt;
        }
        
        //로그인 로그 특정 기준으로 읽기
        //예)기간 특정id user-agent 특정ip
        public function read_specific($data_arr){
            $query = "SELECT ".$this->table."._id,".$this->table.".user_agent,".$this->table.".accessed_at,".$this->table.".access_IP,".$this->user_table.".id FROM ".$this->table." INNER JOIN ".$this->user_table." ON ".$this->table.".user_id = ".$this->user_table."._id where ".$data_arr["search_by_id"].$data_arr["search_by_ip"].$data_arr["search_by_user_agent"]." order by ".$this->table.".created_at desc;";
            //$query = "Show tables;";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt;
        }
    }