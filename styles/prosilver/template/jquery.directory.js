/**
* @package phpBB Directory
* @copyright (c) 2014 ErnadoO
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*/
var directory = {};
directory.requestSent = false;

(function ($) {  // Avoid conflicts with other libraries

	$('#dir_flag').change(function()
	{
		var src_image = dir_flag_path + encodeURI($(this).val());

		$("#flag_image").attr("src",src_image);
	});

	phpbb.addAjaxCallback('phpbbdirectory.add_vote', function(data) {

		var link_id = data.LINK_ID;
		$('#l' + link_id + ' #note').html(data.NOTE);
		$('#l' + link_id + ' #vote').html(data.NB_VOTE);
		$(this).text('');

		phpbb.closeDarkenWrapper(3000);
	});

	phpbb.addAjaxCallback('phpbbdirectory.delete_site', function(data) {

		var link_id = data.LINK_ID;
		$('#l' + link_id).remove();
		$('.dir_total_links').html(data.TOTAL_LINKS);

		phpbb.closeDarkenWrapper(3000);
	});

	phpbb.addAjaxCallback('phpbbdirectory.delete_comment', function(data) {

		var comment_id = data.COMMENT_ID;
		$('#p' + comment_id).remove();
		$('.dir_total_comments').html(data.TOTAL_COMMENTS);

		phpbb.closeDarkenWrapper(3000);
	});

})(jQuery); // Avoid conflicts with other libraries
