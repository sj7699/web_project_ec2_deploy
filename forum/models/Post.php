<?php
    class Post{
        private $conn;
        public $table = 'tpost';
        public $user_table = 'tuser';
        
        public $_id;
        public $category;
        public $created_at;
        public $title;
        public $content;
        public $author_id;
        
        public function __construct($db){
            $this->conn = $db;
            $this->table='tpost';
            $this->user_table='tuser';
        }

        //게시글 카테고리
        public function readbycategory($category,$page_number){
            $page_str=strval(((int)$page_number-1)*5);
            $query = "SELECT ".$this->table."._id,".$this->table.".title,".$this->table.".category,".$this->table.".created_at,".$this->user_table.".id,".$this->table.".views FROM ".$this->table." INNER JOIN ".$this->user_table." ON ".$this->table.".user_id = ".$this->user_table."._id where category = :category order by created_at desc LIMIT ".$page_str.",5";
            //$query = "Show tables;";
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(":category",$category);
            $stmt->execute();
            return $stmt;
        }

        //조회수 안올라가는 게시물 가져오기
        public function getpostbyid($post_id){
            $query = "SELECT ".$this->user_table."._id "."FROM ".$this->table." INNER JOIN ".$this->user_table." ON ".$this->table.".user_id = ".$this->user_table."._id where ".$this->table."._id = :post_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(":post_id",$post_id);
            $stmt->execute();
            return $stmt;
        }
        //게시글 전체 조회
        public function read($page_number){
            $page_str=strval(((int)$page_number-1)*5);
            $query = "SELECT ".$this->table."._id,".$this->table.".title,".$this->table.".category,".$this->table.".created_at,".$this->user_table.".id,".$this->table.".views FROM ".$this->table." INNER JOIN ".$this->user_table." ON ".$this->table.".user_id = ".$this->user_table."._id order by created_at desc LIMIT ".$page_str.",5";
            //$query = "Show tables;";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt;
        }

        #게시물 제목 검색
        public function search_by_title($data_arr){  
            $query = "SELECT ".$this->table."._id,".$this->table.".title,".$this->table.".category,".$this->table.".created_at,".$this->user_table.".id,".$this->table.".views FROM ".$this->table." INNER JOIN ".$this->user_table." ON ".$this->table.".user_id = ".$this->user_table."._id where ".$this->table.".title like '%".$data_arr["keyword"]."%' and ".$this->table.".category like '%".$data_arr["category"]."%' order by created_at desc;";
            //$query = "Show tables;";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt;
        }

        #게시물 내용 검색
        public function search_by_content($data_arr){         
            $query = "SELECT ".$this->table."._id,".$this->table.".title,".$this->table.".category,".$this->table.".created_at,".$this->user_table.".id,".$this->table.".views FROM ".$this->table." INNER JOIN ".$this->user_table." ON ".$this->table.".user_id = ".$this->user_table."._id where ".$this->table.".content like '%".$data_arr["keyword"]."%' and ".$this->table.".category like '%".$data_arr["category"]."%' order by created_at desc;";
            //$query = "Show tables;";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt;
        }

        #게시물 제목+내용 검색
        public function search_by_title_or_content($data_arr){         
            $query = "SELECT ".$this->table."._id,".$this->table.".title,".$this->table.".category,".$this->table.".created_at,".$this->user_table.".id,".$this->table.".views FROM ".$this->table." INNER JOIN ".$this->user_table." ON ".$this->table.".user_id = ".$this->user_table."._id where (".$this->table.".content like '%".$data_arr["keyword"]."%' or ".$this->table.".title like '%".$data_arr["keyword"]."%') and ".$this->table.".category like '%".$data_arr["category"]."%' order by created_at desc;";
            //$query = "Show tables;";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt;
        }

        #게시물 클릭시 게시물에 대한 정보(비밀글 기능도 토큰대조? 비밀번호?) 
        public function read_specific($now_id){
            $query = "SELECT ".$this->table."._id,".$this->table.".title,".$this->table.".category,".$this->table.".content,".$this->table.".created_at,".$this->user_table.".id,".$this->table.".views FROM ".$this->table." INNER JOIN ".$this->user_table." ON ".$this->table.".user_id = ".$this->user_table."._id where ".$this->table."._id = :post_id";
            $query2 = "UPDATE ".$this->table." set views=views+1 where _id = :post_id";
            $stmt2 = $this->conn->prepare($query2);
            $stmt2->bindValue(':post_id',$now_id);
            $stmt2->execute();
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':post_id',$now_id);
            $stmt->execute();
            return $stmt;
        }

        #게시물생성
        public function create($data_arr){
            $query= 'INSERT INTO '.$this->table.'(title,category,content,user_id,created_at) VALUES(:title,:category,:content,:user_id,:created_at)';
            $stmt = $this->conn->prepare($query); 
            $stmt->bindValue(':user_id',$data_arr["user_id"]);
            $stmt->bindValue(':title',$data_arr['title']);
            $stmt->bindValue(':category',$data_arr['category']);
            $stmt->bindValue(':content',$data_arr['content']);
            $stmt->bindValue(':created_at',time());
            $stmt->execute();
            return true;
        }
        
        #특정 게시물 수정 (권한 쓴사람이어야 한다 -> 토큰을 받아야하나? 관리자에 대한 권한도..)
        public function modify($data_arr){ 
            $query= 'UPDATE '.$this->table.' SET title=:title,content=:content where _id=:_id;';
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':_id',$data_arr["_id"]);
            $stmt->bindValue(':title',$data_arr['title']);
            $stmt->bindValue(':content',$data_arr['content']);
            $stmt->execute();
            return true;
        }
        #특정 게시물 삭제 (권한 쓴사람이어야 한다 -> 토큰을 받아야하나? 관리자에 대한 권한도)
        public function delete($data_arr){
            $query = 'DELETE FROM '.$this->table." WHERE _id = :_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(":_id",$data_arr["_id"]);
            $stmt->execute();
            return true;
        }
    }