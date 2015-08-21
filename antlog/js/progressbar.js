function open_progress_bar(dynamic)
{
	$("#progress-wrapper").css('display', 'block');
	show_progress(dynamic);
}

function show_progress(dynamic)
{
	var url = '../event/get-progress-bar-data';
	var progress_key = $('#progress_key').val();
	$.getJSON(url + "?key=" + progress_key, function(data)
	{
		var done = parseInt(data.done);
		var total = parseInt(data.total);
		var percentage = Math.floor(100 * done / total);
		if (percentage > 100)
		{
			percentage = 100;
		}
		if (dynamic == true)
		{
			$('.progress-bar').css('width', percentage + '%').attr('aria-valuenow', percentage).html(percentage + '% Complete');			
		}
		else
		{
			$('.progress-bar').css('width', 100 + '%').attr('aria-valuenow', 100).html('');
			$('span.sr-only').html('');
		}
		if (parseInt(data.error) == 1)
		{
			// redirect via an error handler to provide an error message to the user
			var message = encodeURIComponent(data.errorMessage);
			var protocol = $(location).attr('protocol');
			var hostname = $(location).attr('hostname');
			var pathname = $(location).attr('pathname'); 
			var search = $(location).attr('search');
			search = search.replace('view', 'error');
			$(location).attr('href', protocol + "//" + hostname + pathname + search + "&message=" + message);
		}
		else if (percentage == 100)
		{
			$(location).attr('href', data.redirect); // redirect to target page
		}
		else
		{
			setTimeout(function(){show_progress(dynamic)}, 500); // update twice per second
		}
	});
}

function uniqid (prefix, more_entropy)
{
	// +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
	// +	revised by: Kankrelune (http://www.webfaktory.info/)
	// %		note 1: Uses an internal counter (in php_js global) to avoid collision
	// *	 example 1: uniqid();
	// *	 returns 1: 'a30285b160c14'
	// *	 example 2: uniqid('foo');
	// *	 returns 2: 'fooa30285b1cd361'
	// *	 example 3: uniqid('bar', true);
	// *	 returns 3: 'bara20285b23dfd1.31879087'

	if (typeof prefix === 'undefined')
	{
		prefix = "";
	}

	var retId;
	var formatSeed = function (seed, reqWidth)
		{
			seed = parseInt(seed, 10).toString(16); // to hex str
			if (reqWidth < seed.length)
			{ // so long we split
				return seed.slice(seed.length - reqWidth);
			}
			if (reqWidth > seed.length)
			{ // so short we pad
				return Array(1 + (reqWidth - seed.length)).join('0') + seed;
			}
			return seed;
		};

	// BEGIN REDUNDANT
	if (!this.php_js)
	{
		this.php_js = {};
	}
	// END REDUNDANT
	if (!this.php_js.uniqidSeed)
	{
		// init seed with big random int
		this.php_js.uniqidSeed = Math.floor(Math.random() * 0x75bcd15);
	}
	this.php_js.uniqidSeed++;

	retId = prefix; // start with prefix, add current milliseconds hex string
	retId += formatSeed(parseInt(new Date().getTime() / 1000, 10), 8);
	retId += formatSeed(this.php_js.uniqidSeed, 5); // add seed hex string
	if (more_entropy)
	{
		// for more entropy we add a float lower to 10
		retId += (Math.random() * 10).toFixed(8).toString();
	}

	return retId;
}
