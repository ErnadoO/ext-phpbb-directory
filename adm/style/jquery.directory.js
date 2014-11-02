/**
* @package phpBB Directory
* @copyright (c) 2014 ErnadoO
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*/

function update_image(newimage) {
	document.getElementById('image').src = (newimage) ? dir_icon_path + encodeURI(newimage) : "./images/spacer.gif";
}

(function ($) {  // Avoid conflicts with other libraries

	$('#parent').change(function() {
		var value = $(this).val();

		if (value == 0) {
			phpbb.toggleDisplay('cat_display_parent', -1);
		} else {
			phpbb.toggleDisplay('cat_display_parent', 1);
		}
	});

	$('#cat_icon').change(function() {
		var value = $(this).val();
		var newimage = (value) ? dir_icon_path + encodeURI(value) : "./images/spacer.gif";

		$('#cat_image').attr('src', newimage);
	});

	$('#cron_every').keyup(function() {
		var day = $(this).val();

		var date = new Date();
		var timestamp = date.setTime((date.getTime()/1000) + day * 86400);

		$.ajax({
			url: dir_url_ajax,
			type: 'GET',
			data: 'timestamp='+timestamp
		})
		.done(function( data ) {
			$("#next_check").html(data.DATE);
		});
	})

})(jQuery); // Avoid conflicts with other libraries
