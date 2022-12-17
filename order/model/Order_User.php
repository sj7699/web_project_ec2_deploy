<?php
    class Order_User{
        private $conn;
        public $table = 'torder_user';
        
        public $_id;
        public $user_id;
        public $created_at;
        public $detail;
        

        public function __construct($db){
            $this->conn = $db;
            $this->table='torder_user';
        }

        //주문번호로 주문번호 가져오기
        public function getbyorderid($orderid){
            $query = "SELECT * FROM ".$this->table." where order_id = :order_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(":order_id",$orderid);
            $stmt->exectue();
            return $stmt;
        }

        //주문 전체 조회 (관리자 권한)
        public function read($data_arr){
            $query =  "SELECT * FROM ".$this->table." order by created_at desc";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt;
        }

        //특정 주문 상세 보기
        public function read_specific($data_arr){
            $query = "SELECT * FROM ".$this->table." where created_at >= :start_time order by created_at desc";
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(":start_time",$data_arr["start_time"]);
            $stmt->execute();
            return $stmt;
        }

        //특정 주문 생성
        public function create($data_arr){
            $query = "INSERT INTO ".$this->table."(user_id,created_at,detail,address,phone_number,recipient) VALUES(:user_id,:created_at,:detail,:address,:phone_number,:recipient)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(":user_id",$data_arr["user_id"]);
            $stmt->bindValue(":created_at",time());
            $stmt->bindValue(":detail",$data_arr["detail"]);
            $stmt->bindValue(":address",$data_arr["address"]);
            $stmt->bindValue(":phone_number",$data_arr["phone_number"]);
            $stmt->bindValue(":recipient",$data_arr["recipient"]);
            $stmt->execute();
            return $this->conn->lastInsertId();
        }

        //배송상태 변경
        public function update_delivery_state($orderid,$delivery_state){
            $query = "UPDATE ".$this->table." SET delivery_state=:delivery_state where order_id = :order_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(":order_id",$orderid);
            $stmt->execute();
            return True;
        }

        //특정 주문 수정
        public function update($data_arr){
            $query = "UPDATE ".$this->table." SET user_id = :user_id created_at = :created_at detail = :detail WHERE _id = :_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(":_id",$data_arr["_id"]);
            $stmt->bindValue(":user_id",$data_arr["id"]);
            $stmt->bindValue(":created_at",time());
            $stmt->bindValue(":detail",$data_arr["detail"]);
            $stmt->execute();
            return True;
        }

        //특정 주문 취소
        //주문 - 제품 테이블은 모두 order_id에 delete cascade가 걸려있기에 여기서 지워야함
        public function delete($data_arr){
            $query = "DELETE FROM ".$this->table." WHERE _id = :_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(":_id",$data_arr["_id"]);
            $stmt->execute();
        }
    }