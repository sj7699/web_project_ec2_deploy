<?php
    class Comment{
        private $conn;
        public $post_table = 'tpost';
        public $table = 'tcomment';
        
        public $_id;
        public $created_at; 
        public $user_id;
        public $post_id;
        
        public function __construct($db){
            $this->conn = $db;
            $this->table='tcomment';
            $this->post_table='tpost';
            $this->user_table='tuser';
        }

        public function getcommentbyid($_id){
            $query = "SELECT * FROM ".$this->table." WHERE _id = :_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(":_id",$_id);
            $stmt->execute();
            return $stmt;
        }

        //게시물 전체 댓글 불러오기
        public function read($data_arr){
            $query = "SELECT ".$this->table."._id,".$this->table.".content,".$this->table.".created_at,".$this->user_table.".id FROM ".$this->table." INNER JOIN ".$this->user_table." ON ".$this->table.".user_id = ".$this->user_table."._id where post_id = ".$data_arr["post_id"]." order by ".$this->table.".created_at desc;";
            //$query = "Show tables;";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt;
        }

        #댓글생성
        public function create($data_arr){
            $query= 'INSERT INTO '.$this->table.'(post_id,content,user_id,created_at) VALUES(:post_id,:content,:user_id,:created_at)';
            $stmt = $this->conn->prepare($query); 
            $stmt->bindValue(':user_id',$data_arr["user_id"]);
            $stmt->bindValue(':content',$data_arr['content']);
            $stmt->bindValue(':created_at',time());
            $stmt->bindValue(':post_id',$data_arr['post_id']);
            $stmt->execute();
            return true;
        }
        
        #댓글 수정 (권한 쓴사람이어야 한다 -> 관리자에 대한 권한도..)
        public function modify($data_arr){ 
            $query= 'UPDATE '.$this->table.' SET content=:content where _id=:_id;';
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':_id',$data_arr["_id"]);
            $stmt->bindValue(':content',$data_arr['content']);
            $stmt->execute();
            return true;
        }

        #댓글 삭제 (권한 쓴사람이어야 한다 ->관리자에 대한 권한도)
        public function delete($data_arr){
            $query = 'DELETE FROM '.$this->table." WHERE _id = :_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(":_id",$data_arr["_id"]);
            $stmt->execute();
            return true;
        }
    }