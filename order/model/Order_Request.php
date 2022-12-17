<?php
    class Order_Request{
        private $conn;
        public $table = 'torder_request';
        
        public $_id;
        public $order_id;
        public $user_id;
        public $created_at;
        public $request_type;
        public $request_reason;
        

        public function __construct($db){
            $this->conn = $db;
            $this->table='torder_request';
        }

        //order_id로 취소/교환/환불 주문요청 가져오기
        public function getbyorderid($orderid){
            $query = "SELECT * FROM ".$this->table." where = order_id = :order_id";
            $stmt = $this->conn->prepare($query);
            $stmt.bindValue(":order_id",$orderid);
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

        //특정 주문 상세 보기
        public function read_specific($data_arr){
            $query = "SELECT * FROM ".$this->table." where created_at >= :start_time order by created_at desc";
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(":start_time",$data_arr["start_time"]);
            $stmt->execute();
            return $stmt;
        }

        //특정 주문-제품 생성
        public function create($order_id,$product_id){
            $query = "INSERT INTO ".$this->table."(order_id,product_id) VALUES(:order_id,:product_id)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(":order_id",$order_id);
            $stmt->bindValue(":product_id",$product_id);
            $stmt->execute();
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
    }