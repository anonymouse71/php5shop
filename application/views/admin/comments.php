<?php defined('SYSPATH') or die('No direct script access.'); ?>
<h2>Комментарии</h2>
<?php if (is_array($data)) : ?>
<form method="post" action="">
    <table>
        <tr>
            <th><!-- № --></th>
            <th>Комментарий к странице</th>
            <th>Оценка (из 5)</th>
            <th>От пользователя</th>
            <th>Подписан как</th>
            <th>Текст</th>
        </tr>
        <?php foreach ($data as $item): ?>
            <tr>
                <td><input type="checkbox" name="delete<?php echo $item->id ?>" ></td>
                <td><?php echo $item->object ?></td>
                <td><?php echo htmlspecialchars($item->rate) ?></td>
                <td></td>
                <td><?php echo htmlspecialchars($item->username) ?></td>
                <td><?php echo htmlspecialchars($item->text) ?></td>
            </tr>
        <?php endforeach; ?>
        <tr>
            <td colspan="6">
                <input type="submit" class="bnt" name="delete" value="Удалить выбранные">
            </td>
        </tr>
    </table>
</form>

<?php
    echo $pagination;
else: ?>
    Комментариев еще нет.
<?php endif;