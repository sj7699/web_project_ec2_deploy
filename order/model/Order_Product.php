<?php
    class Order_Product{
        private $conn;
        public $table = 'torder_product';
        public $ptable = 'tproduct';
        
        public $_id;
        public $user_id;
        public $created_at;
        public $detail;
        

        public function __construct($db){
            $this->conn = $db;
            $this->table='torder_product';
        }

        //특정 주문-제품 생성
        public function create($order_id,$product_id,$num){
            $query = "INSERT INTO ".$this->table."(order_id,product_id,num) VALUES(:order_id,:product_id,:num)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(":order_id",$order_id);
            $stmt->bindValue(":product_id",$product_id);
            $stmt->bindValue(":num",$num);
            $stmt->execute();
        }

        //주문번호 - 물건뱉기
        public function getproductbyorderid($order_id){
            $query = "SELECT * FROM ".$this->table." AS a INNER JOIN ".$this->ptable." AS b ON a.product_id = b._id WHERE a.order_id = ".$order_id;
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt;
        }

        //주문번호 - 가격뱉기
        public function getpricebyorderid($order_id){
            $query = "SELECT SUM(b.price) FROM ".$this->table." AS a INNER JOIN ".$this->ptable." AS b ON a.product_id = b._id WHERE a.order_id = ".$order_id;
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt;
        }
    }