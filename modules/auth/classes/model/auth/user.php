<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Auth_User extends ORM
{

    // Relationships
    protected $_has_many
        = array
        (
            'user_tokens' => array('model' => 'user_token'),
            'roles'       => array('model' => 'role', 'through' => 'roles_users'),
        );

    // Rules
    protected $_rules
        = array
        (
            'username' => array
            (
                'not_empty'  => NULL,
                'min_length' => array(4),
                'max_length' => array(32),
                'regex'      => array('/^[\s_a-zа-яё0-9]+$/uiD'),
            ),
            /*
            'password'			=> array
            (
                'not_empty'		=> NULL,
                'min_length'		=> array(5),
                'max_length'		=> array(42),
            ),
            'password_confirm'	=> array
            (
                'matches'		=> array('password'),
            ),*/
            'email'    => array
            (
                'not_empty'       => NULL,
                'min_length'      => array(4),
                'max_length'      => array(127),
                'validate::email' => NULL,
            ), /*    добавлено */
            'phone'    => array
            (
                'not_empty'       => NULL,
                'max_length'      => array(12),
                'validate::phone' => NULL
            ),
            'address'  => array
            (
                'max_length' => array(200)
            )/*     /добавлено */
        );

    protected $_callbacks
        = array
        (
            'username' => array('username_available'),
            'email'    => array('email_available'),
        );

    // Columns to ignore
    protected $_ignored_columns = array('password_confirm');

    /**
     * Validates login information from an array, and optionally redirects
     * after a successful login.
     *
     * @param  array    values to check
     * @param  string   URI or URL to redirect to
     *
     * @return boolean
     */
    public function login(array & $array, $redirect = FALSE)
    {
        $array = Validate::factory($array)
            ->filter(TRUE, 'trim')
            ->rules('username', $this->_rules['username'])
            ->rules('password', $this->_rules['password']);

        // Login starts out invalid
        $status = FALSE;

        if ($array->check())
        {
            // Attempt to load the user
            $this->where('username', '=', $array['username'])->find();

            if ($this->loaded() AND Auth::instance()->login($this, $array['password']))
            {
                if (is_string($redirect))
                {
                    // Redirect after a successful login
                    Request::instance()->redirect($redirect);
                }

                // Login is successful
                $status = TRUE;
            }
            else
            {
                $array->error('username', 'invalid');
            }
        }

        return $status;
    }

    /**
     * Проверяет правильность входящих данных и сохраняет нового пользователя
     * Возвращает TRUE или массив ошибок
     *
     * @param array  массив входящих данных
     * @param bool   Сохранять после проверки или нет
     * @param int    В какую группу пользователей регистрация

    public function register(array & $array, $save = TRUE, $group = 0)
    {
        $errors = array();

        $fieldsAdd = array(); //массив для полей, которые будут сохранены
        if (ORM::factory('field')->count_all()) //если есть дополнительные поля
        {
            foreach (ORM::factory('field')->find_all() as $field)
            {
                if (isset($array['f' . $field->id]) && !empty($array['f' . $field->id]))
                {
                    if (!ORM::factory('field')->validate($array['f' . $field->id], $field->type))
                    {
                        $errors[' ' . $field->name . ': '] = 'поле введено в неправильном формате';
                    }
                    else
                    {
                        $fieldsAdd[$field->id] = $array['f' . $field->id];
                    }
                }
                else
                {
                    if (!$field->empty)
                    {
                        $errors[' ' . $field->name . ': '] = 'поле не должно быть пустым';
                    }
                    else
                    {
                        $fieldsAdd[$field->id] = '';
                    }
                }
            }
        }

        $array = Validate::factory($array)
            ->filter(TRUE, 'trim')
            ->rules('username', $this->_rules['username'])
            ->rules('password', $this->_rules['password'])
            ->rules('password_confirm', $this->_rules['password_confirm'])
            ->rules('email', $this->_rules['email'])
            ->rules('phone', $this->_rules['phone'])
            ->rules('address', $this->_rules['address']);

        $array->check();
        $this->username_available($array, 'username');
        $this->email_available($array, 'email');
        $errors += $array->errors('validate');
        $group = (int)$group;
        if ($group === -1)
        {
            $errors += array('Код приглашения' => ': не действителен. ');
        }

        if (count($errors))
        {
            return $errors;
        }

        if (!$save)
        {
            return TRUE;
        }

        $user = new Model_User();
        $post = $array->as_array();
        $user->username = $post['username'];
        $user->password = $post['password'];
        $user->email = $post['email'];
        $user->phone = (string)$post['phone'];
        $user->address = isset($post['address']) ? $post['address'] : '';
        $user->save();
        $uid = $user->__get('id');
        if ($group)
        {
            DB::insert('groups_users')->values(array($uid, $group))->execute();
        }

        if (!EMAIL_VERIFY)
        {
            DB::insert('roles_users')->values(array($uid, 1))->execute();
        }

        foreach ($fieldsAdd as $id => $val)
        {
            Model::factory('field_value')->set($id, $uid, $val);
        }

        return TRUE;
    }
     */

    /**
     * Validates an array for a matching password and password_confirm field.
     *
     * @param  array    values to check
     * @param  string   save the user if
     *
     * @return boolean
     */
    public function change_password(array & $array, $save = FALSE)
    {
        $array = Validate::factory($array)
            ->filter(TRUE, 'trim')
            ->rules('password', $this->_rules['password'])
            ->rules('password_confirm', $this->_rules['password_confirm']);
        $status = $array->check();
        if ($status)
        {
            // Change the password
            $this->password = $array['password'];

            if ($save !== FALSE AND $status = $this->save())
            {
                if (is_string($save))
                {
                    // Redirect to the success page
                    Request::instance()->redirect($save);
                }
            }
        }

        return $status;
    }

    /**
     * Does the reverse of unique_key_exists() by triggering error if username exists
     * Validation Rule
     *
     * @param    Validate  $array   validate object
     * @param    string    $field   field name
     * @param    array     $errors  current validation errors
     *
     * @return   array
     */
    public function username_available(Validate $array, $field)
    {
        if ($this->unique_key_exists($array[$field]))
        {
            $array->error($field, 'username_available', array($array[$field]));
        }
    }

    /**
     * Does the reverse of unique_key_exists() by triggering error if email exists
     * Validation Rule
     *
     * @param    Validate  $array   validate object
     * @param    string    $field   field name
     * @param    array     $errors  current validation errors
     *
     * @return   array
     */
    public function email_available(Validate $array, $field)
    {
        if ($this->unique_key_exists($array[$field]))
        {
            $array->error($field, 'email_available', array($array[$field]));
        }
    }

    /**
     * Tests if a unique key value exists in the database
     *
     * @param   mixed        value  the value to test
     *
     * @return  boolean
     */
    public function unique_key_exists($value)
    {
        return (bool)DB::select(array('COUNT("*")', 'total_count'))
            ->from($this->_table_name)
            ->where($this->unique_key($value), '=', $value)
            ->execute($this->_db)
            ->get('total_count');
    }

    /**
     * Allows a model use both email and username as unique identifiers for login
     *
     * @param  string    $value   unique value
     *
     * @return string             field name
     */
    public function unique_key($value)
    {
        return Validate::email($value) ? 'email' : 'username';
    }

    /**
     * Saves the current object. Will hash password if it was changed
     *
     * @chainable
     * @return  $this
     */
    public function save()
    {
        if (array_key_exists('password', $this->_changed))
        {
            $this->_object['password'] = Auth::instance()->hash_password($this->_object['password']);
        }

        return parent::save();
    }

    /**
     * Проверяет наличие email в БД. Предварительно проводит валидацию.
     * Возвращает:
     * -1 если email не прошел валидацию,
     *  1 если есть в БД,
     *  0 если нету в БД
     *
     * @param string $email
     *
     * @return int
     */
    public function check_email_in_db($email)
    {
        $validate = Validate::factory(array('email' => $email)) //валидация email
            ->rules('email', $this->_rules['email']);
        if (!$validate->check()) //если email не прошел валидацию
        {
            return -1;
        }
        //проверка на наличие email в БД
        if (!count(DB::select()->from('users')->where('email', '=', $email)->limit(1)->execute()->as_array()))
        {
            return 0;
        }

        return 1;
    }

    public function is_username_available($username)
    {
        return self::unique_key_exists($username) ? TRUE : FALSE;
    }

    /**
     * Проводит валидацию пароля и устанавливает его для пользователя с передаваемым id или email
     *
     * @param string $email_or_id
     * @param string $password
     * @param string $password_confirm - не обязательное поле, только для валидации
     *
     * @return bool

    public function set_password($email_or_id, $password, $password_confirm = FALSE)
    {
        if ($password_confirm === FALSE)
        {
            $password_confirm = $password;
        }
        $arr = array('password' => $password, 'password_confirm' => $password_confirm);
        if (!$this->change_password($arr))
        {
            return FALSE;
        }

        $field = ((string)$email_or_id == (string)((int)$email_or_id)) ? 'id' : 'email';

        return DB::update('users')
            ->value('password', Auth::instance()->hash_password($password))
            ->where($field, '=', $email_or_id)
            ->limit(1)
            ->execute();

    }*/

    /**
     * Проводит валидацию телефона согласно правилам class Model_Auth_User
     * и сохраняет в БД новый телефон
     *
     * @param int    $id
     * @param string $phone
     *
     * @return string
     */
    public function set_phone($id, $phone)
    {
        $validate = Validate::factory(array('phone' => $phone))->rules('phone', $this->_rules['phone']);
        if (!$validate->check()) //если не прошел валидацию
        {
            $errors = $validate->errors('validate');
            return 'Телефон' . $errors['phone'] . '. ';
        }

        $user = ORM::factory('user', $id);
        $user->__set('phone', $phone);
        $user->save();

        return 'Телефон сохранен. ';
    }

    /**
     * сохраняет в БД новый адрес
     * @param int    $id
     * @param string $addres
     *
     * @return string
     */
    public function set_address($id, $addres)
    {
        $validate = Validate::factory(array('addres' => $addres))->rules('addres', $this->_rules['address']);
        if (!$validate->check()) //если не прошел валидацию
        {
            $errors = $validate->errors('validate');
            return 'Адрес' . $errors['addres'] . '. ';
        }
        $user = ORM::factory('user', $id);
        $user->__set('address', $addres);
        $user->save();

        return 'Адрес сохранен. ';
    }

    /**
     * сохраняет в БД новый email
     * @param int    $id
     * @param string $email
     *
     * @return string
     */
    public function set_email($id, $email)
    {
        $validate = Validate::factory(array('email' => $email))->rules('email', $this->_rules['email']);
        if (!$validate->check()) //если не прошел валидацию
        {
            $errors = $validate->errors('validate');
            return ' Email' . $errors['email'] . '. ';
        }
        $user = ORM::factory('user', $id);
        $user->__set('email', $email);
        $user->save();

        return ' Email сохранен. ';
    }

    /**
     * сохраняет username
     * @param int    $id
     * @param string $username
     *
     * @return string
     */
    public function set_username($id, $username)
    {
        $validate = Validate::factory(array('username' => $username))
            ->rules('username', $this->_rules['username']);
        if (!$validate->check()) //если не прошел валидацию
        {
            $errors = $validate->errors('validate');
            if($errors['username'] == ': поле не отвечает формату')
                return ' Укажите Ваши настоящие имя и фамилию.';
            return ' Имя' . $errors['username'] . '. ';
        }
        $user = ORM::factory('user', $id);
        $user->__set('username', $username);
        $user->save();

        return ' Имя сохранено. ';
    }

    /**
     * Проверяет является ли пользователь администратором.
     * Если не передать id, то использует id объекта класса
     *
     * @param int $id
     *
     * @return bool
     */
    public function is_admin($id = null)
    {
        if (!$id)
        {
            $id = $this->id;
        }
        $result = DB::select()->from('roles_users')
            ->where('user_id', '=', $id)
            ->and_where('role_id', '=', 2)
            ->limit(1)
            ->execute()
            ->as_array('role_id');
        return isset($result[2]);
    }

    /**
     * Делает пользователя администратором
     * @param int $id
     *
     * @return bool
     */
    public function make_admin($id = null)
    {
        if (!$id)
        {
            $id = $this->id;
        }
        return DB::insert('roles_users')
            ->values(array($id, 2))
            ->execute();
    }

    /**
     * Снимает администраторские привилегии
     * @param int $id
     *
     * @return bool
     */
    public function make_not_admin($id = null)
    {
        if (!$id)
        {
            $id = $this->id;
        }
        return DB::delete('roles_users')
            ->where('user_id', '=', $id)
            ->and_where('role_id', '=', 2)
            ->execute();
    }

    /**
     * Находит id пользователя по имени
     *
     * @param string $username
     *
     * @return int
     *
     */
    public function find_id($username)
    {
        $q = DB::select('id')->from('users')
            ->where('username', '=', $username)
            ->execute()
            ->as_array();

        if (isset($q[0]['id']))
        {
            return $q[0]['id'];
        }
        else
        {
            return 0;
        }
    }

    /**
     * Обновление информации о пользователе
     * @param int $id
     * @param array $post
     * @param bool $is_admin
     * @param bool $quiet
     * @param bool $readonly
     * @return string $messages
     */
    public function updateUser($id, $post, $is_admin = false, $quiet = false, $readonly = false)
    {
        $user = Model::factory('user', $id);

        $messages = '';

        if (ORM::factory('field')->count_all()) //если есть дополнительные поля
            foreach (ORM::factory('field')->find_all() as $field)
                if (isset($post['f' . $field->id]) && !empty($post['f' . $field->id]))
                {
                    if (ORM::factory('field')->validate($post['f' . $field->id], $field->type))
                        Model::factory('field_value')->set($field->id, $id, $post['f' . $field->id]);
                    else
                        $messages .= htmlspecialchars($field->name) . ': поле введено в неправильном формате. ';
                }
                else
                {
                    if ($field->empty)
                        Model::factory('field_value')->set($field->id, $id, '');
                    else
                        $messages .=  htmlspecialchars($field->name) . ': поле не должно быть пустым. ';
                }

        $fields = array(
            'username' => array(
                'ok' => ' Имя сохранено. ',
                'notSet' => 'Имя не указано. ',
                'err' => 'Имя'
            ),
            'email' => array(
                'ok' => ' Email сохранен. ',
                'notSet' => ' Email не указан. ',
                'err' => ' Email'
            ),
            'phone' => array(
                'ok' => 'Телефон сохранен. ',
                'notSet' => 'Телефон не указан. ',
                'err' => 'Телефон'
            ),
            'address' => array(
                'ok' => ' Адрес сохранен. ',
                'notSet' => ' Адрес не указан. ',
                'err' => 'Адрес'
            ),
        );

        if(!$is_admin)
            unset($fields['username']);

        foreach($fields as $key => $fieldMessages)
            if ($post[$key]) // введен?
            {
                $validate = Validate::factory(array($key => $post[$key]))->rules($key, $this->_rules[$key]);
                if (!$validate->check()) //если не прошел валидацию
                {
                    $errors = $validate->errors('validate');
                    if($key == 'username' && $errors['username'] == ': поле не отвечает формату')
                        $messages .=  ' Укажите Ваши настоящие имя и фамилию.';
                    else
                        $messages .=  $fieldMessages['err'] . $errors[$key] . '. ';
                }
                else
                {
                    $user->$key = $post[$key];
                    if(!$quiet)
                        $messages .=  $fieldMessages['ok'];
                }
            }
            else
                $messages .=  $fieldMessages['notSet'];

        if (!$readonly &&$is_admin)
        {
            if (isset($post['gid'])) //редактирование группы
            {
                $gid = (int)$post['gid'];
                $gr = ORM::factory('groups_user', $post['id']);
                if ($gid > 0 && $gid < 99)
                {
                    if (!$gr->id)
                        $gr->id = $post['id'];
                    $gr->__set('gid', (int)$post['gid']);
                    $gr->save();
                    $messages .=  'Группа сохранена. ';
                }
                else
                {
                    $gr->delete();
                    $messages .=  'Не состоит в группах. ';
                }
            }

            if (isset($post['is_admin']))
            {
                if ($post['is_admin'] == 1 && !$user->is_admin())
                    $user->make_admin();
                if ($post['is_admin'] == 2)
                    $user->make_not_admin();
            }
        }

        if(!$readonly)
            $user->save();
        return $messages;
    }

} // End Auth User Model