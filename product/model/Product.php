<?php
    class Product{
        private $conn;
        public $table = 'tproduct';
        
        public $_id;
        public $name;
        public $price;
        public $category;
        public $created_at;
        public $image;
        public $weight;
        public $detail;

        public function __construct($db){
            $this->conn = $db;
            $this->table='tproduct';
        }      
        
        //제품 전체 조회
        public function getbyid($id){
            $query =  "SELECT * FROM ".$this->table." where _id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(":id",$id);
            $stmt->execute();
            return $stmt;
        }      
        
        //제품 전체 조회
        public function getbyname($name){
            $query =  "SELECT * FROM ".$this->table." where name = :name";
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(":name",$name);
            $stmt->execute();
            return $stmt;
        }

        //제품 전체 조회
        public function read($data_arr){
            $query =  "SELECT _id,name,price,created_at,image,weight,category FROM ".$this->table;
            //$query = "Show tables;";
            if(isset($data_arr["order_by"])){
                $query.=" order by ".$data_arr["order_by"];
            }
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt;
        }

        #제품 이름 검색
        public function search_by_name($data_arr){  
            $query = "SELECT _id,name,price,created_at,image,weight,category FROM ".$this->table." where name like '%".$data_arr["keyword"]."%'";
            //$query = "Show tables;";
            if(isset($data_arr["order_by"])){
                $query.=" order by ".$data_arr["order_by"];
            }
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt;
        }

        #제품 카테고리 검색
        public function search_by_category($data_arr){   
            $query = "SELECT _id,name,price,created_at,image,weight,category FROM ".$this->table." where category like '%".$data_arr["keyword"]."%'";
            //$query = "Show tables;";
            if(isset($data_arr["order_by"])){
                $query.=" order by ".$data_arr["order_by"];
            }
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt;
        }       
        
        #제품 카테고리 와 이름으로 검색
        public function search_by_name_and_category($data_arr){   
            $query = "SELECT _id,name,price,created_at,image,weight,category FROM ".$this->table." where category like '%".$data_arr["keyword2"]."%' and name like '%".$data_arr["keyword"]."%'";
            //$query = "Show tables;";
            if(isset($data_arr["order_by"])){
                $query.=" order by ".$data_arr["order_by"];
            }
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt;
        }

        #제품 클릭시 제품 대한 자세한 정보
        public function read_specific($now_id){
            $query =  "SELECT * FROM ".$this->table." where _id = :id";
            //$query = "Show tables;";
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':id',$now_id);
            $stmt->execute();
            return $stmt;
        }

        #제품생성(관리자 권한)
        public function create($data_arr){
            $query= 'INSERT INTO '.$this->table.'(name,category,created_at,detail,weight,price,image) VALUES(:name,:category,:created_at,:detail,:weight,:price,:image)';
            $stmt = $this->conn->prepare($query); 
            $stmt->bindValue(':name',$data_arr["name"]);
            $stmt->bindValue(':price',$data_arr['price']);
            $stmt->bindValue(':category',$data_arr['category']);
            $stmt->bindValue(':detail',$data_arr['detail']);
            $stmt->bindValue(':weight',$data_arr['weight']);
            $stmt->bindValue(':price',$data_arr['price']);
            $stmt->bindValue(':image',$data_arr['image']);
            $stmt->bindValue(':created_at',time());
            $stmt->execute();
            return true;
        }

        #제품 수정 (권한 쓴사람이어야 한다 -> 토큰을 받아야하나? 관리자에 대한 권한도..)
        public function modify($data_arr){ 
            $query= 'UPDATE '.$this->table.' SET name=:name,category=:category,price=:price,weight=:weight,detail=:detail where _id=:_id;';
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':name',$data_arr["name"]);
            $stmt->bindValue(':price',$data_arr['price']);
            $stmt->bindValue(':category',$data_arr['category']);
            $stmt->bindValue(':detail',$data_arr['detail']);
            $stmt->bindValue(':weight',$data_arr['weight']);
            $stmt->bindValue(':price',$data_arr['price']);
            $stmt->bindValue(':_id',$data_arr['_id']);
            $stmt->execute();
            return true;
        }
        #특정 제품 삭제 (권한 쓴사람이어야 한다 -> 토큰을 받아야하나? 관리자에 대한 권한도)
        public function delete($data_arr){
            $query = 'DELETE FROM '.$this->table." WHERE _id = :_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(":_id",$data_arr["_id"]);
            $stmt->execute();
            return true;
        }
    }