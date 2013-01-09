<div style="padding:10px;">
    <p class="post pagination" style="float:left; ">

	<?php if ($first_page !== FALSE): ?>
		<a href="<?php echo $page->url($first_page) ?>"><?php echo __('Первая') ?></a>
	<?php else: ?>
		<?php echo __('Первая') ?>
	<?php endif ?>

	<?php if ($previous_page !== FALSE): ?>
		<a href="<?php echo $page->url($previous_page) ?>"><?php echo __('Предыдущая') ?></a>
	<?php else: ?>
		<?php echo __('Предыдущая') ?>
	<?php endif ?>

	<?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <?php if (abs($i - $current_page) <= 5 ): //добавлено 18.09.10 ?>
		<?php if ($i == $current_page): ?>
			<strong>[<?php echo $i ?>]</strong>
		<?php else: ?>
			<a href="<?php echo $page->url($i) ?>"><?php echo $i ?></a>
		<?php endif ?>
            <?php endif ?>
	<?php endfor ?>

	<?php if ($next_page !== FALSE): ?>
		<a href="<?php echo $page->url($next_page) ?>"><?php echo __('Следующая') ?></a>
	<?php else: ?>
		<?php echo __('Следующая') ?>
	<?php endif ?>

	<?php if ($last_page !== FALSE): ?>
		<a href="<?php echo $page->url($last_page) ?>"><?php echo __('Последняя ('.$i.')') ?></a> 
	<?php else: ?>
		<?php echo __('Последняя') ?>
	<?php endif ?>

    </p><!-- .pagination -->
</div>