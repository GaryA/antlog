jQuery("#do_draw").on('click', function()
{
	$.ajax(
	{
		type: "post",
		dataType: 'json',
		url: 'index.php?r=event/draw&eventId=' + $('#event_id').val(),
		data: $('#event_button_form').serializeArray()
	})
	.done(function(response)
	{
		//$('#progress_key').val(uniqid());
		open_progress_bar();
		return true;
	})
	.fail(function (jqXHr, textStatus, errorThrown)
	{
		console.debug(jqXHr.responseText);
		console.log(textStatus);
		console.log(errorThrown);
	});
});
