<?php

// Unique error identifier
$error_id = uniqid('error');

?>
<style type="text/css">
#error { background: #F3F8F0; font-size: 1em; font-family:sans-serif; text-align: left; color: #111; }
#error h1,
#error h2 { margin: 0; padding: 1em; font-size: 1em; font-weight: normal; background: #119963; color: #fff; }
#error h1 a,
#error h2 a { color: #fff; }
#error h2 { background: #222; }
#error h3 { margin: 0; padding: 0.4em 0 0; font-size: 1em; font-weight: normal; }
#error p { margin: 0; padding: 0.2em 0; }
#error a { color: #1b323b; }
#error pre { overflow: auto; white-space: pre-wrap; }
#error table { width: 100%; display: block; margin: 0 0 0.4em; padding: 0; border-collapse: collapse; background: #fff; }
#error table td { border: solid 1px #ddd; text-align: left; vertical-align: top; padding: 0.4em; }
#error div.content { padding: 0.4em 1em 1em; overflow: hidden; }
#error pre.source { margin: 0 0 1em; padding: 0.4em; background: #fff; border: dotted 1px #b7c680; line-height: 1.2em; }
#error pre.source span.line { display: block; }
#error pre.source span.highlight { background: #C8FAC6; }
#error pre.source span.line span.number { color: #666; }
#error ol.trace { display: block; margin: 0 0 0 2em; padding: 0; list-style: decimal; }
#error ol.trace li { margin: 0; padding: 0; }
.js .collapsed { display: none; }
</style>
<script type="text/javascript">
document.documentElement.className = 'js';
function koggle(elem)
{
	elem = document.getElementById(elem);

	if (elem.style && elem.style['display'])
		// Only works with the "style" attr
		var disp = elem.style['display'];
	else if (elem.currentStyle)
		// For MSIE, naturally
		var disp = elem.currentStyle['display'];
	else if (window.getComputedStyle)
		// For most other browsers
		var disp = document.defaultView.getComputedStyle(elem, null).getPropertyValue('display');

	// Toggle the state of the "display" style
	elem.style.display = disp == 'block' ? 'none' : 'block';
	return false;
}
</script>
<div id="error">
	<h1><span class="type"><?php echo $type ?> [ <?php echo $code ?> ]:</span> <span class="message"><?php echo $message ?></span></h1>
	<div id="<?php echo $error_id ?>" class="content">
		<p><span class="file"><?php echo Kohana::debug_path($file) ?> [ <?php echo $line ?> ]</span></p>
		<?php echo Kohana::debug_source($file, $line) ?>
		<ol class="trace">
		<?php foreach (Kohana::trace($trace) as $i => $step): ?>
			<li>
				<p>
					<span class="file">
						<?php if ($step['file']): $source_id = $error_id.'source'.$i; ?>
							<a href="#<?php echo $source_id ?>" onclick="return koggle('<?php echo $source_id ?>')"><?php echo Kohana::debug_path($step['file']) ?> [ <?php echo $step['line'] ?> ]</a>
						<?php else: ?>
							{<?php echo __('PHP internal call') ?>}
						<?php endif ?>
					</span>
					&raquo;
					<?php echo $step['function'] ?>(<?php if ($step['args']): $args_id = $error_id.'args'.$i; ?><a href="#<?php echo $args_id ?>" onclick="return koggle('<?php echo $args_id ?>')"><?php echo __('arguments') ?></a><?php endif ?>)
				</p>
				<?php if (isset($args_id)): ?>
				<div id="<?php echo $args_id ?>" class="collapsed">
					<table cellspacing="0">
					<?php foreach ($step['args'] as $name => $arg): ?>
						<tr>
							<td><code><?php echo $name ?></code></td>
							<td><pre><?php echo Kohana::dump($arg) ?></pre></td>
						</tr>
					<?php endforeach ?>
					</table>
				</div>
				<?php endif ?>
				<?php if (isset($source_id)): ?>
					<pre id="<?php echo $source_id ?>" class="source collapsed"><code><?php echo $step['source'] ?></code></pre>
				<?php endif ?>
			</li>
			<?php unset($args_id, $source_id); ?>
		<?php endforeach ?>
		</ol>
	</div>
</div>
