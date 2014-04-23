/**
* @package phpBB Directory
* @copyright (c) 2014 ErnadoO
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*/
var directory = {};
directory.requestSent = false;

(function ($) {  // Avoid conflicts with other libraries

	phpbb.addAjaxCallback('phpbbdirctory.add_vote', function(data) {

		var link_id = data.LINK_ID;
		$('#l' + link_id + ' #note').html(data.NOTE);
		$('#l' + link_id + ' #vote').html(data.NB_VOTE);
		$(this).text('');

		phpbb.closeDarkenWrapper(3000);
	});

})(jQuery); // Avoid conflicts with other libraries
