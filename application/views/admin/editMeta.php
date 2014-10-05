<?php defined('SYSPATH') or die('No direct script access.'); ?>

<style>
    .modal-dialog {
        width: 720px;
    }
    #meta_path, #meta_title, #meta_description, #meta_keywords{
        max-width:600px;
    }
</style>


<div class="modal fade" id="modal_meta" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Редактирование meta</h4>
            </div>
            <div class="modal-body">
                <form action="" method="post" class="meta_edit_form">
                    <fieldset>
                        <label for="meta_path">Страница:</label><br>
                        <input readonly id="meta_path" name="path" maxlength="255" size="100">

                        <br>
                        <label for="meta_title">Заголовок страницы (title)</label><br>
                        <input id="meta_title" name="title" maxlength="300" size="100"><br>

                        <label for="meta_description">meta description</label><br>
                        <textarea id="meta_description" name="description" maxlength="300" rows="3"
                                  cols="100"></textarea><br>

                        <label for="meta_keywords">meta keywords</label><br>
                        <textarea id="meta_keywords" name="keywords" maxlength="300" rows="3" cols="100"></textarea><br>

                    </fieldset>
                </form>
            </div>
            <div class="modal-footer">
                <input type="submit" value="Сохранить" id="submit_edit_meta">
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть окно</button>
            </div>
        </div>
    </div>
</div>


<script type="text/javascript">

    /**
     * Показывает окно редактирования метаданных
     * @param old_data - старые значения
     * @param call - функция, которая вызывается после успешного сохранения.
     *                  Получает параметр - новые значения метаданных
     */
    function edit_meta_modal(old_data, call){

        var modal_meta = $('#modal_meta');
        var fields = ['path', 'title', 'description', 'keywords'];
        $.each(fields, function (i, v) {
            modal_meta.find('[name=' + v + ']').val(old_data[v]);
        });
        modal_meta.find('form').submit(function () {
            return false;
        });

        $('#submit_edit_meta')
            .unbind('click')
            .attr('type', 'button')
            .attr("data-dismiss", "modal")
            .bind('click', function () {
                var post_data = {id: old_data['id'], update: true};
                $.each(fields, function (i, v) {
                    post_data[v] = modal_meta.find('[name=' + v + ']').val();
                });
                $.post("<?php echo url::base() ?>admin/meta", post_data, function (answer) {
                    if (answer != 'ok') {
                        alert('Произошла ошибка. Страница будет обновлена. ');
                        document.location.href += '';
                        return false;
                    }
                    call(post_data);
                }, 'text');

            });
        modal_meta.modal({});
    }

    function edit_meta_by_path(path){
        $.post('<?php echo url::base() ?>ajax/meta_load', {path: path},
            function (d) {edit_meta_modal(d, function (n) {})}, 'json')
    }

</script>