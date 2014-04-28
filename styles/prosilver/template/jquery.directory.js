/**
* @package phpBB Directory
* @copyright (c) 2014 ErnadoO
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*/
var directory = {};
directory.requestSent = false;

(function ($) {  // Avoid conflicts with other libraries

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

})(jQuery); // Avoid conflicts with other libraries
