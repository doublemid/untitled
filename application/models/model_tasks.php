<?php
/*class Model {
    public $pdo;
    public $data;
    function __construct(){
        $host = '127.0.0.1';
        $db   = 'tasks';
        $user = 'root';
        $pass = '';
        $charset = 'utf8';

        $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
        $opt = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        $this->pdo = new PDO($dsn, $user, $pass, $opt);
    }
}*/

class Model_Tasks extends Model{
    protected $num=3;



    public function getCountPages(){
        $stmt = $this->pdo->query("SELECT COUNT(*) FROM listing_task")->fetch(PDO::FETCH_NUM);
        $result= $stmt[0];
        $pages=0;
            for($i = 0; $i < $result; $i+=$this->num){
                $pages++;
            }
        return $pages;
    }

    public function getPagination($my_page=1,$sort='',$order='DESC'){
        $num=$this->num;
        strtoupper($order);
        $pages=$this->getCountPages();
            if(empty($my_page) or $my_page < 0) $my_page = 1;
            if($my_page > $pages) $my_page = $pages;
        $start=(($my_page-1)*$num);
        $queryOrder='';

       if(!empty($sort)&&!empty($order)){
           $order === 'ASC' ? 'ASC ' : 'DESC ';
           $sort ==='name'?'name':'false';
           $sort ==='email'?'email':'false';
           $sort ==='status'?'status':'false';
           $sort ==='id'?'id':'false';
                 if($order&&$sort){
                    $queryOrder='ORDER BY '.$sort." ".$order;
                 }
        }
        $sql='SELECT * FROM listing_task '.$queryOrder." LIMIT :start, :num";
            try {
                $result =$this->pdo->prepare($sql);
                $result->execute(array('start'=>$start,'num'=>$num));
                $response= $result->fetchAll();
            } catch(PDOException $e) {
                echo $e->getMessage();
                exit;
            }

             return $response;
    }


    public function newTask($data){
        $sql = "INSERT INTO listing_task (name, text, email) VALUES (:name, :text, :email)";
        $stmt= $this->pdo->prepare($sql);
        $stmt->execute($data);
        $name = $stmt->fetch();
        return $name;
    }

    public function editTask($data){
        $id=$data['id'];
        $edittxt =$this->pdo->prepare('SELECT text FROM listing_task WHERE id=:id');
        $edittxt->execute(array('id'=>$id));
        $result = $edittxt->fetch();
        $sql = "UPDATE  listing_task SET status=:status, text=:text  WHERE id=:id";
        if($data['text']!=$result['text']) {
            $data['edit'] = '1';
            $sql = "UPDATE  listing_task SET status=:status, text=:text, edit=:edit WHERE id=:id";
        }
        try{
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute($data);
                $response =$this->pdo->prepare('SELECT * FROM listing_task WHERE id=:id');
                $response->execute(array('id'=>$id));
                $result=$response->fetchAll();
            }catch(PDOException $e) {
                echo $e->getMessage();
                exit;
            }
        return $result;
    }
}
/*$data['id'] = 3;
$data['status'] = 0;
$data['text'] ='fsdfsdf';
print_r( $data);
$fffd = new Model_Tasks();
$result = $fffd->editTask($data);
print_r($result);*/