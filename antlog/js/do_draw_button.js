jQuery(".do_draw").on('click', function()
{
	var target = $(this).data('target');
	$.ajax(
	{
		type: "post",
		dataType: 'json',
		url: target + '?eventId=' + $('#event_id').val(),
		data: $('#event_button_form').serializeArray()
	})
	.done(function(response)
	{
		//$('#progress_key').val(uniqid());
		open_progress_bar(true);
		return true;
	})
	.fail(function (jqXHr, textStatus, errorThrown)
	{
		console.debug(jqXHr.responseText);
		console.log(textStatus);
		console.log(errorThrown);
	});
});
