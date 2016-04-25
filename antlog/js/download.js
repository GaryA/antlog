jQuery(window).load( function()
{
	setTimeout("Redirect()", 100);
});

function Redirect()
{
	// Redirect to a new URL, since the redirects to an SQL file it just causes a file
	// download, not a visible page redirect. The user appears to stay on the original page
	window.location = "../db/export";
}
