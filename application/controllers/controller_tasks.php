<?php
class Controller_Tasks extends Controller
{
public $result;
    function __construct()
    {
        $this->model = new Model_Tasks();
        $this->view = new View();
    }

    function action_index(){
        $this->view->data['TaskList'] =  $this->TaskList($page, $sort='',$order='DESC');
        $this->view->data['TaskPages'] =  $this->TaskPages();
        $this->view->data['login']=$this->authCheck();
        $this->view->generate([ 'template_view.html','main_view.html','footer.html']);
    }


    function action_list(){
        $page = isset($_GET['page']) ? $_GET['page'] : 1;
        $order = $_GET['order'];
        $sort = $_GET['sort'];
        $task=$this->TaskList($page, $sort, $order);
        echo $task;
    }

    function action_edittask(){
        session_start();
        if(isset($_SESSION["login"])){
            $data['id'] = (isset($_POST['id']) ? $_POST['id'] : null);
            $data['status'] = (isset($_POST['status']) ? $_POST['status'] : "0");
            intval($data['status']);
            $data['text'] =(isset($_POST['text']) ? $_POST['text'] : null);
            if (empty($data['text']) && empty( $data['status'])) {
                echo json_encode(array(
                    'success' => 0,
                    'msg' =>"Не заполнены поля!",
                ));
            exit;
        }
        else {
            $result=$this->model->editTask($data);
            echo json_encode(array(
                'success' => 1,
                'msg' =>"Успешно отредактировано!",
                'data' =>json_encode($result),
            ));
        }}
        else{
            echo json_encode(array(
                'success' => 0,
                'msg' =>"Вы не админ!",
            ));
            exit;
        }
    }

    function action_newtask(){
        $data['name'] = (isset($_POST['name']) ? $_POST['name'] : null);
        $data['email'] = (isset($_POST['email']) ? $_POST['email'] : null);
        $data['text'] =(isset($_POST['text']) ? $_POST['text'] : null);
        if (empty($data['text']) || empty( $data['email'])|| empty( $data['name'])) {
            echo json_encode(array(
                'success' => 0,
                'msg' => 'Поля обязательны для заполнения',
            ));
            exit;
        }
        if(!filter_var( $data['email'], FILTER_VALIDATE_EMAIL)) {
            echo json_encode(array(
                'success' => 0,
                'msg' => 'Неверный формат email',
            ));
            exit;
        }

        else {
            $this->model->newTask($data);
            echo json_encode(array(
                'success' => 1,
                'msg' => 'Новая задача успешно создана!'
            ));
        }
    }

    public function action_taskpages(){
       echo $this->TaskPages();
    }
    
    private function TaskPages(){
        $pages='';
        for($i = 1; $i<=$this->model->getCountPages(); $i++){
            $pages.="<li class='page-item'><a class='page-link'>".$i.'</a></li>';
        }
        return $pages;
    }

    private function authCheck(){
        $buffer = '';
        session_start();
        if(!isset($_SESSION['login'])){
            $buffer = file_get_contents('application/views/auth.html');
            return $buffer;
            session_destroy();
        }
        else{
            $buffer = file_get_contents('application/views/login.html');
            return $buffer;
        }
    }
    
    private function TaskList($page, $sort='',$order='')
    {
        session_start();
        $data = $this->model->getPagination($page,$sort,$order);
        $buffer = '';
        foreach($data as $data){
            $buffer .= file_get_contents('application/views/list_task.html');
            foreach($data as $key=>$value) {
                $buffer = str_replace('{'.$key.'}', $value, $buffer);
                if($data['status']==1){
                    $buffer = str_replace('{statustext}', 'Завершено', $buffer);
                }

                if($data['status']==0){
                    $buffer = str_replace('{statustext}', 'Не завершено', $buffer);
                }
                if(!isset($_SESSION['login'])){
                    $buffer = str_replace('{hidden}', 'hidden', $buffer);
                }
                else{ $buffer = str_replace('{hidden}', '', $buffer);}
            }
        }
        return $buffer;
    }
}