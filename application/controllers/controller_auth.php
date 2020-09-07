<?php
class Controller_Auth extends Controller
{
    function __construct()
    {
        $this->model = new Model_Auth();
    }

    function action_index()
    {
        $login = (isset($_POST['login']) ? $_POST['login'] : null);
        $password = (isset($_POST['password']) ? $_POST['password'] : null);

        if (empty($login) || empty($password)) {

            echo json_encode(array(
                'success' => 0,
                'msg' => 'Поля обязательны для заполнения!',
            ));
            exit;
        }

        $data = $this->model->authorize($login, $password);
        if ($data) {
            $buffer = file_get_contents('application/views/login.html');
            echo json_encode(array(
                'success' => 1,
                'data' => $buffer,
                'msg' => 'Успешная авторизация!',
            ));
            exit;
        }
        else{
            echo json_encode(array(
                'success' => 0,
                'msg' => 'Неверные данные!',
            ));
            exit;
        }

    }


    public function action_logout()
    {
        session_start();
        session_unset();
        session_destroy();
        echo $buffer = file_get_contents('application/views/auth.html');
    }
}