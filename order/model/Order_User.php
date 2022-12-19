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
            $query = "SELECT * FROM ".$this->table." where _id = :order_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(":order_id",$orderid);
            $stmt->execute();
            return $stmt;
        }

        //주문 전체 조회 (관리자 권한)
        public function read($data_arr){
            $query =  "SELECT * FROM ".$this->table." order by created_at desc";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt;
        }

        //자신의 주문 보기
        public function read_customer($user_id){
            $query = "SELECT * FROM ".$this->table." where user_id = :user_id order by created_at desc";
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(":user_id",$user_id);
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
            $query = "INSERT INTO ".$this->table."(user_id,created_at,recipientAddr,ordererMessage,recipientPhone,delivery_state,recipientName,recipientAddrDetail,recipientPost,recipientEmail) VALUES(:user_id,:created_at,:recipientAddr,:ordererMessage,:recipientPhone,:delivery_state,:recipientName,:recipientAddrDetail,:recipientPost,:recipientEmail)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(":user_id",$data_arr["user_id"]);
            $stmt->bindValue(":created_at",time());
            $stmt->bindValue(":recipientAddr",$data_arr["recipientAddr"]);
            $stmt->bindValue(":ordererMessage",$data_arr["ordererMessage"]);
            $stmt->bindValue(":recipientPhone",$data_arr["recipientPhone"]);
            $stmt->bindValue(":delivery_state","입금대기");
            $stmt->bindValue(":recipientName",$data_arr["recipientName"]);
            $stmt->bindValue(":recipientAddrDetail",$data_arr["recipientAddrDetail"]);
            $stmt->bindValue(":recipientPost",$data_arr["recipientPost"]);
            $stmt->bindValue(":recipientEmail",$data_arr["recipientEmail"]);
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