<?php defined('SYSPATH') OR die('No direct access allowed.');?>
<style type="text/css">a{color: #000;}</style>
<h2>Добавление записи в блог</h2>

<form action="" method="post">
    Заголовок <input type="text" name="title" size="35" value="<?php if(isset($post['title'])) echo htmlspecialchars($post['title']); ?>"><br>
    <p>
        Основной текст новости
        <textarea cols="80" id="editor1" name="editor" rows="10"><?php if(isset($post['code'])) echo htmlspecialchars($post['code']); ?></textarea>
        <script type="text/javascript" src="<?php echo url::base();?>js/ckedit/inc.js"></script>
    </p>
    <p>
        Сокращенный вариант для предпросмотра
        <textarea cols="80" id="editor2" name="editor2" rows="10"><?php if(isset($post['code2'])) echo htmlspecialchars($post['code2']); ?></textarea>
        <script type="text/javascript" src="<?php echo url::base();?>js/ckedit/inc2.js"></script>
    </p>
    <p>
        <input type="submit" value="Сохранить" />
    </p>
</form>
<?php echo $errors;?>