<?php

    enum Usergrade : int{
        case Admin=0;
        case First=1;
        case Second=2;
        case Normal=3;
        case Newbie=4;
    }
    enum RegisterResponse : int{
        case ID_ALREADY_EXIST=0;
        case EMAIL_ALREADY_EXIST=1;

    }
    class User{

        private $conn;
        private $table="tuser";

        /*private $_id;
        private $name;
        private $password;
        private $created_at;
        private $email;
        private $profile_id;
        private $grade;
        private $adress;
        private $phone_number;
        private $home_number;
        private $birthday;
        */

        public function __construct($db){
            $this->conn = $db;
        }

        //아이디로 유저 가져오기
        public function get_user_from_id($user_arr){
            $query = 'SELECT * FROM '.$this->table.' WHERE id = ?';
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(1,$user_arr['id']);
            $stmt->execute();
            return $stmt;
        }

        //이메일로 유저 가져오기
        public function get_user_from_email($user_arr){
            $query = 'SELECT * FROM '.$this->table.' WHERE email = ?';
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(1,$user_arr['email']);
            $stmt->execute();
            return $stmt;
        }

        //일반 유저생성
        public function create_user($user_arr){
            $user=$this->get_user_from_id($user_arr);
            $user_count=$user->rowCount();
            if($user_count>0){
                return false;
            }
            $euser=$this->get_user_from_email($user_arr);
            $euser_count=$euser->rowCount();
            if($euser_count>0){
                return false;
            }
            else{
                $query= 'INSERT INTO '.$this->table.'(id,birthday,address,created_at,grade,password,email,phone_number,home_number,name) VALUES(:id,:birthday,:address,:created_at,:grade,:password,:email,:phone_number,:home_number,:name)';
                $stmt = $this->conn->prepare($query);
                $stmt->bindValue(':id',$user_arr['id']);
                $stmt->bindValue(':name',$user_arr['name']);
                $stmt->bindValue(':password',password_hash($user_arr['password'],PASSWORD_DEFAULT));
                $stmt->bindValue(':created_at',time());
                $stmt->bindValue(':email',$user_arr['email']);
                $stmt->bindValue('grade',Usergrade::Newbie->value);
                $stmt->bindValue(':address',$user_arr['address']);
                $stmt->bindValue(':phone_number',$user_arr['phone_number']);
                $stmt->bindValue(':home_number',$user_arr['home_number']);
                $stmt->bindValue(':birthday',$user_arr['birthday']);
                $stmt->execute();
                return true;
            }
        }

        //관리자 생성
        public function create_admin($user_arr){
            $user=$this->get_user_from_id($user_arr);
            $user_count=$user->rowCount();
            if($user_count>0){
                return false;
            }
            $euser=$this->get_user_from_email($user_arr);
            $euser_count=$euser->rowCount();
            if($euser_count>0){
                return false;
            }
            else{
                $query= 'INSERT INTO '.$this->table.'(id,birthday,address,created_at,grade,password,email,phone_number,home_number,name) VALUES(:id,:birthday,:address,:created_at,:grade,:password,:email,:phone_number,:home_number,:name)';
                $stmt = $this->conn->prepare($query);
                $stmt->bindValue(':id',$user_arr['id']);
                $stmt->bindValue(':name',$user_arr['name']);
                $stmt->bindValue(':password',password_hash($user_arr['password'],PASSWORD_DEFAULT));
                $stmt->bindValue(':created_at',time());
                $stmt->bindValue(':email',$user_arr['email']);
                $stmt->bindValue('grade',Usergrade::Admin->value);
                $stmt->bindValue(':address',$user_arr['address']);
                $stmt->bindValue(':phone_number',$user_arr['phone_number']);
                $stmt->bindValue(':home_number',$user_arr['home_number']);
                $stmt->bindValue(':birthday',$user_arr['birthday']);
                $stmt->execute();
                return true;
            }
        }

        //유저 정보 수정 (비밀번호 포함)
        public function modify_user($user_arr){
            $user=$this->get_user_from_id($user_arr);
            $user_count=$user->rowCount();
            if($user_count<1){
                return false;
            }
            $query = 'UPDATE '.$this->table.' SET email = :email,address = :address,phone_number = :phone_number,home_number = :home_number WHERE id = :id';
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':id',$user_arr['id']);
            $stmt->bindValue(':email',$user_arr['email']);
            $stmt->bindValue(':address',$user_arr['address']);
            $stmt->bindValue(':phone_number',$user_arr['phone_number']);
            $stmt->bindValue(':home_number',$user_arr['home_number']);
            $stmt->execute();
            return True;
        }

        //유저 삭제 (회원 탈퇴?)
        public function delete_user($user_arr){
            $user=$this->get_user_from_id($user_arr);
            $user_count=$user->rowCount();
            if($user_count<1){
                return false;
            }
            $query = 'DELETE FROM '.$this->table.' WHERE id = :id';
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':id',$user_arr['id']);
            $stmt->execute();
            return True;
        }

        //권한 변경
        public function change_grade($grade,$user_arr){       
            $user=$this->get_user_from_id($user_arr);
            $user_count=$user->rowCount();
            if($user_count<1){
                return false;
            }
            $query = 'UPDATE '.$this->table.' SET grade = :grade WHERE id = :id';
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':grade',$grade);
            $stmt->bindValue(':id',$user_arr['id']);
            $stmt->execute();
        }

        //비밀번호 변경
        public function change_password($user_arr){
            $query = 'UPDATE '.$this->table.' SET password = :password WHERE id = :id';
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':password',password_hash($user_arr['password'],PASSWORD_DEFAULT));
            $stmt->bindValue(':id',$user_arr['id']);
            $stmt->execute();
            return true;
        }

    }