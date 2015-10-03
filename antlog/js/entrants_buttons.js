jQuery(".enter-btn").on('click', function(event)
{
	event.preventDefault();
	var target = $(this).data('target');
	var cell = $(this).parent();
	$.ajax(
	{
		type: "get",
		dataType: 'json',
		url: target,
	})
	.done(function(response)
	{
		if (response.status == 'Error')
		{
			$(location).attr('href', ''); // redirect to current page to show flash set by controller
		}
		else if (response.status == 'OK')
		{
			$(cell).html(response.newhtml);
			return false;
		}
	})
	.fail(function (jqXHr, textStatus, errorThrown)
	{
		console.debug(jqXHr.responseText);
		console.log(textStatus);
		console.log(errorThrown);
	});
});

jQuery(".delete-btn").on('click', function()
{
	var target = $(this).data('target');
	var row = $(this).parent().parent();
	if (confirm('Are you sure you want to delete this entry?'))
	{
		$.ajax(
		{
			type: "post",
			dataType: 'json',
			url: target,
		})
		.done(function(response)
		{
			if (response.status == 'Error')
			{
				$(location).attr('href', ''); // redirect to current page to show flash set by controller
			}
			else if (response.status == 'OK')
			{
				$(row).remove();
				return true;
			}
		})
		.fail(function (jqXHr, textStatus, errorThrown)
		{
			console.debug(jqXHr.responseText);
			console.log(textStatus);
			console.log(errorThrown);
		});
	}
});
