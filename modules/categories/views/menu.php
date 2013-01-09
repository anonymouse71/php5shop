<?php defined('SYSPATH') or die('No direct script access.'); ?>
<!-- 
menu 2
-->
<script type="text/javascript" src="js/jquery.cookie.js"></script>
<script type="text/javascript">
$(document).ready(function() {
	$('ul#my-menu ul').each(function(i) { // Check each submenu:
		if ($.cookie('submenuMark-' + i)) {  // If index of submenu is marked in cookies:
			$(this).show().prev().removeClass('collapsed').addClass('expanded'); // Show it (add apropriate classes)
		}else {
			$(this).hide().prev().removeClass('expanded').addClass('collapsed'); // Hide it
		}
		$(this).prev().addClass('collapsible').click(function() { // Attach an event listener
			var this_i = $('ul#my-menu ul').index($(this).next()); // The index of the submenu of the clicked link
			if ($(this).next().css('display') == 'none') {
				$(this).next().slideDown(200, function () { // Show submenu:
					$(this).prev().removeClass('collapsed').addClass('expanded');
					cookieSet(this_i);
				});
			}else {
				$(this).next().slideUp(200, function () { // Hide submenu:
					$(this).prev().removeClass('expanded').addClass('collapsed');
					cookieDel(this_i);
					$(this).find('ul').each(function() {
						$(this).hide(0, cookieDel($('ul#my-menu ul').index($(this)))).prev().removeClass('expanded').addClass('collapsed');
					});
				});
			}
                //docoment.location.href = this.href;
		return false; // Prohibit the browser to follow the link address
		});
	});
});
function cookieSet(index) {
	$.cookie('submenuMark-' + index, 'opened', {expires: null, path: '/'}); // Set mark to cookie (submenu is shown):
}
function cookieDel(index) {
	$.cookie('submenuMark-' + index, null, {expires: null, path: '/'}); // Delete mark from cookie (submenu is hidden):
}
</script>
<style type="text/css">
	ul.cat-menu li { padding:2px 0;margin:0;list-style:none; }
	ul.cat-menu li ul { padding:0;margin:0 0 0 10px; }
	ul#my-menu a { padding-left:8px; }
	ul#my-menu a.collapsed { background:url('images/collapsed.gif') left 6px no-repeat; }
	ul#my-menu a.expanded { background:url('images/expanded.gif') left 6px no-repeat; }
</style>

<div id="menu1">
    <?php echo $menu; ?>
</div>
<!-- 
//menu 2
-->