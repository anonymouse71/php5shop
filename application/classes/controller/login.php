<?php defined('SYSPATH') or die('No direct script access.');
/**
 * php5shop - CMS интернет-магазина
 * Copyright (C) 2010-2012 phpdreamer
 * php5shop.com
 * email: phpdreamer@rambler.ru
 * Это программа является свободным программным обеспечением. Вы можете
 * распространять и/или модифицировать её согласно условиям Стандартной
 * Общественной Лицензии GNU, опубликованной Фондом Свободного Программного
 * Обеспечения, версии 3.
 * Эта программа распространяется в надежде, что она будет полезной, но БЕЗ
 * ВСЯКИХ ГАРАНТИЙ, в том числе подразумеваемых гарантий ТОВАРНОГО СОСТОЯНИЯ ПРИ
 * ПРОДАЖЕ и ГОДНОСТИ ДЛЯ ОПРЕДЕЛЁННОГО ПРИМЕНЕНИЯ. Смотрите Стандартную
 * Общественную Лицензию GNU для получения дополнительной информации.
 * Вы должны были получить копию Стандартной Общественной Лицензии GNU вместе
 * с программой. В случае её отсутствия, посмотрите http://www.gnu.org/licenses/.
 */

/**
 * Контроллер авторизации и регистрации пользователей.
 */

class Controller_Login extends Controller
{

    /**
     * Авторизация пользователей
     */
    public function action_index()
    {
        //checkLogin проводит проверки на то, включена ли ф-я авторизации и не
        //авторизован ли уже пользователь, есть ли POST массив
        $this->checkLogin();


        /*
        // С версии 1.5 отключена стандартная авторизация

        $antibrut = Model::factory('Antibrut');                                 //механизм защиты от перебора паролей
        if(!$antibrut->chk())                                                   //если попытки авторизаций закончились
            exit( Request::factory('error/loginlimit')->execute() );            //перенаправить пользователя на страницу ошибки.

        if (isset($_POST['login']) && isset($_POST['pass'])) //если логин и пароль переданы
        {
            if (!$this->auth->login($_POST['login'], $_POST['pass'], TRUE)) //попытка авторизации
            {
                //$antibrut->bad();
                Session::instance()->set(
                    'login_error', '1'
                ); //В случае ошибки, информация о ней записывается в COOKIES
            }
        }
        */

        if (isset($_POST['token']))
        {
            $s = file_get_contents(
                'http://ulogin.ru/token.php?token=' . $_POST['token'] . '&host=' . $_SERVER['HTTP_HOST']
            );
            $user = json_decode($s, true);

            //$user['network'] - соц. сеть, через которую авторизовался пользователь
            //$user['identity'] - уникальная строка определяющая конкретного пользователя соц. сети
            //$user['first_name'] - имя пользователя
            //$user['last_name'] - фамилия пользователя


        }


        //Перенаправление на страницу с которой пользователь пришел (если она на этом домене)
        $this->request->redirect(
            (
                isset($_SERVER['HTTP_REFERER'])
                    && strpos($_SERVER['HTTP_REFERER'], '://' . $_SERVER['HTTP_HOST'])
            )
                ?
                $_SERVER['HTTP_REFERER']
                :
                url::base()
        );
    }

    /**
     * Регистрация.
     */
    public function action_register()
    {
        //checkLogin проводит проверки на то, включена ли ф-я авторизации и не
        //авторизован ли уже пользователь, есть ли POST массив
        $this->checkLogin();

        /*
        $post = $_POST; //сохраняем копию массива POST
        $group
            = isset($_POST['finvite']) && strlen($_POST['finvite']) ? Model_Invite::checkInvite($_POST['finvite']) : 0;

        if (isset($_POST['captcha']) && Captcha::valid($_POST['captcha'])) //если проверочное изображение введено верно,
        {
            $register = Model::factory('user')->register(
                $_POST, TRUE, $group
            ); //пользователь добавляется, а массив ошибок или TRUE записывается в переменную $register
            if ($register === TRUE && isset($post['finvite']))
            {
                Model_Invite::deleteInvite($post['finvite']);
            }
        }
        else //если картинка была введенв неверно,
        { //происходит проверка остальных полей
            $register = Model::factory('user')->register(
                $_POST, FALSE, $group
            ); //без занесения инфрмации в БД, даже если все остальные поля введены верно
            if ($register === TRUE) //если остальные поля верны, создаем массив ошибок с 1 элементом
            {
                $register = array('' => 'Проверочное изображение введено неверно');
            }
            else //иначе добавляем к массиву ошибок еще 1
            {
                $register = array_merge($register, array('' => ' Проверочное изображение введено неверно'));
            }
        }
        if ($register === TRUE) //если пользователь успешно зарегистрирован
        {

            if (Session::instance()->get('referral')) //проверка участия в партнерской программе
            { //сохранение:
                $uid = Model::factory('user')->find_id($_POST['username']);
                $ref = ORM::factory('referral');
                $ref->id = Session::instance()->get('referral');
                $ref->ref = $uid;
                $ref->save();
                Session::instance()->delete('referral'); //очищаем переменную сессии
            }

            if (EMAIL_VERIFY)
            {
                if (!isset($uid))
                {
                    $uid = Model::factory('user')->find_id($_POST['username']);
                }

                Session::instance()->set('needEmail', 1);
                $mail = Model::factory('PHPMailer'); //объект модели PHPMailer
                $name = Model::factory('html')->getblock('shopName'); //получение название магазина
                $email = ORM::factory('mail', 3)->__get('value'); //и email администратора
                $mail->AddReplyTo($email, $name); //подстановка переменных в атрибуты объекта класса
                $mail->From = $email;
                $mail->FromName = $name;
                $mail->AddAddress($_POST['email']);
                $mail->Subject = 'Завершение регистрации';
                $body = new View('EMAILregistration');
                $body->url = 'http://' . $_SERVER['HTTP_HOST'] . url::base()
                    . 'email/' . md5($email . $_SERVER['HTTP_HOST'] . $_POST['email'])
                    . $uid . '_' . base64_encode($_POST['email']);
                $mail->MsgHTML($body);
                $mail->WordWrap = 80;
                $mail->Send();
                //die($body);
            }
            else
            {
                $this->auth->login($_POST['username'], $_POST['password'], TRUE);
            } //авторизуем

            $this->request->redirect(url::base()); //и отправляем на главную страницу
        }
        else //иначе
        {
            Session::instance()->set('register_errors', $register); //записываем ошибки в COOKIES
            foreach ($post as $postN => $postV) //защищаемся от XSS
            {
                $post[$postN] = htmlspecialchars($postV);
            }
            Session::instance()->set('register_post', $post); //записываем POST в COOKIES
            $this->request->redirect(url::base() . 'shop/register'); //и отправляем на страницу регистрации
        }
        */
    }

    /**
     * Выход. Завершение сессии по собственному желанию.
     */
    public function action_exit()
    {//если пользователь вошел и попал на страницу нажатием на кнопку "Выход"
        if (isset($_POST['exit']) && Auth::instance()->logged_in())
        {
            Auth::instance()->logout(TRUE); //сессия уничтожается
        }

        //в любом случае на этой странице делать больше нечего - перенаправим на главную
        $this->request->redirect(url::base());
    }



    /**
     * Проверка: включена ли ф-я авторизации, не авторизован ли уже пользователь, есть ли POST массив
     * Перенаправляет пользователей, которые зашли не туда
     */
    private function checkLogin()
    {
        /*
        $boolConfigs = Model::factory('config')->getbool(); //получение пользовательских настроек.
        //если функция авторизации отключена или нет POST данных
        if (!$boolConfigs['LoginOn'])
        {
            exit(Request::factory('error/404')->execute());
        } //завершаем выполнение ошибкой 404 (Not found).
        */

        $this->auth = Auth::instance(); //инициализация механизма авторизации
        if ($this->auth->logged_in()) //если пользователь уже авторизован,
        {
            exit($this->request->redirect(url::base()));
        } //перенаправим на главную страницу.

    }

    /**
     * Отправка нового пароля на Email (функция "Забыли пароль?")
     * /
    public function action_emailpass()
    {
        $this->checkLogin();

        if ($this->request->param('id') > 0) //если пользователь на странице /login/emailpass/num,
        { //где num > 0
            if ( //но если num не записан в сессии
                Session::instance()->get('emailpass_id') != $this->request->param('id')
                || //или
                !$userEmail = Session::instance()->get('email') //email не записан в сессии
            )
            { //в сессию записывается информация об ошибке и происходит перенаправление
                Session::instance()->set(
                    'emailpass_errors',
                    'Вы зашли с другого браузера или сессия закончилась. Попробуйте отправить новый запрос на смену пароля.'
                );
                exit($this->request->redirect(url::base() . 'shop/forgotpassword'));
            }

            $password = text::random('alnum', 12); //генерируется случайный пароль [0-9a-Z]{12}
            //установка нового пароля(в качестве идентификатора служит email из сессии)
            Model::factory('user')->set_password($userEmail, $password);
            //отправляется письмо с новым паролем на email из сессии:
            $mail = Model::factory('PHPMailer'); //объект модели PHPMailer
            $name = Model::factory('html')->getblock('shopName'); //получение название магазина
            $email = ORM::factory('mail', 3)->__get('value'); //и email администратора
            $mail->AddReplyTo($email, $name); //подстановка переменных в атрибуты объекта класса
            $mail->From = $email;
            $mail->FromName = $name;
            $mail->AddAddress($userEmail);
            $mail->Subject = 'Изменен пароль';
            $body = new View('EMAILnewpassword');
            $body->pass = $password;
            $mail->AltBody = 'Ваш новый пароль';
            $mail->MsgHTML($body);
            $mail->WordWrap = 80;
            $mail->Send(); //непосредственно отправка
            Session::instance()->regenerate(); //разрушение сессии и создание новой (для чистки)
            Session::instance()->set(
                'password_changed', 1
            ); //установка в сессию переменной, индикатора завершения смены пароля
            Model::factory('antibrut')->unlock(); //разблокировка
            exit($this->request->redirect(url::base())); //перенаправление на главную страницу
        }

        if ( //если пользователь на странице /login/emailpass
            !isset ($_POST['captcha']) //без отправки captcha
            || //или
            !Captcha::valid($_POST['captcha']) //с неправильной captcha
            || //или
            !isset ($_POST['email']) //без отправки email ($_POST['email'])
        )
        { //в сессию записывается информация об ошибке и
            Session::instance()->set('emailpass_errors', 'Проверочное изображение введено неверно');
            exit($this->request->redirect(url::base() . 'shop/forgotpassword')); //происходит перенаправление
        }

        $chkEmail = Model::factory('user')->check_email_in_db($_POST['email']); //проверка на наличие email в БД
        if ($chkEmail == -1) //Если email не прошел валидацию,
        {
            Session::instance()->set(
                'emailpass_errors', 'Email не настоящий'
            ); //в сессию записывается информация об ошибке и
            exit($this->request->redirect(url::base() . 'shop/forgotpassword')); //происходит перенаправление
        }
        else
        {
            if (!$chkEmail) //нет пользователя с таким email
            {
                Session::instance()->set('emailpass_errors', 'Email не зарегистрирован на сайте');
                exit($this->request->redirect(url::base() . 'shop/forgotpassword'));
            }
        }

        $id = mt_rand(10000, mt_getrandmax()); //генерация произвольного числа от 10000 до максимума ф-и mt_rand
        Session::instance()->regenerate(
        ); //пересоздание сессии чтобы у пользователя на активацию было ровно столько времени, сколько живет сессия
        Session::instance()->set('emailpass_id', $id); //установка в сессию данных о номере для активации
        Session::instance()->set('email', $_POST['email']); //и email

        $mail = Model::factory('PHPMailer'); //отправка письма на email

        $name = Model::factory('html')->getblock('shopName'); //получение название магазина
        $email = ORM::factory('mail', 3)->__get('value');
        $mail->AddReplyTo($email, $name); //подстановка переменных в атрибуты объекта класса
        $mail->From = $email;
        $mail->FromName = $name;
        $mail->AddAddress($_POST['email']);
        $mail->Subject = 'Восстановление пароля';
        $body = new View('EMAILforgotpassword');
        $body->id = $id;
        $mail->AltBody = 'Был произведен запрос на смену пароля';
        $mail->MsgHTML($body);
        $mail->WordWrap = 80;
        $mail->Send();
        Session::instance()->set('emailpass_errors', TRUE);
        $this->request->redirect(url::base() . 'shop/forgotpassword'); //перенаправление
    }

    /**
     * Подтверждение email
     * /
    public function action_email($id = '')
    {
        Session::instance()->set('okEmail', 2);
        if (preg_match('|^([0-9a-z]{32})([0-9]+)_(.+)$|', $id, $base))
        {
            $email = base64_decode($base[3]);
            $md = md5(ORM::factory('mail', 3)->__get('value') . $_SERVER['HTTP_HOST'] . $email);

            if ($md == $base[1])
            {
                Session::instance()->set('okEmail', 1);

                $login = DB::select()->from('roles_users')
                    ->where('user_id', '=', $base[2])
                    ->and_where('role_id', '=', 1)->execute()
                    ->as_array();

                if (!count($login))
                {
                    DB::insert('roles_users')
                        ->values(array($base[2], 1))
                        ->execute();

                    //Auth::instance()->force_login($email);
                }
            }
        }
        $this->request->redirect(url::base());
    }

    */
}