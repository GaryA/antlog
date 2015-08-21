jQuery("form#form2").submit( function(event)
{
	var target = $(this).data('target');
	event.preventDefault();
	event.stopImmediatePropagation();
	$.ajax(
	{
		type: "post",
		dataType: 'json',
		url: target,
		data: $('#form2').serializeArray()
	})
	.done(function(response)
	{
		open_progress_bar(false);
		return true;
	})
	.fail(function (jqXHr, textStatus, errorThrown)
	{
		console.debug(jqXHr.responseText);
		console.log(textStatus);
		console.log(errorThrown);
	});
});

jQuery("form#upload_button_form").submit( function(event)
{
	event.preventDefault();
	event.stopImmediatePropagation();
	
	var target = $(this).data('target');
	var formData = new FormData($(this)[0]);
	$.ajax(
	{
		type: "post",
		dataType: 'json',
		url: target,
		data: formData,
		async: false,
		cache: false,
		contentType: false,
		processData: false,
	})
	.done(function(response)
	{
		if (response.status == 'OK')
		{
			$("#filename").val(response.fileName);
			$("form#form2").submit();
			return false;
		}
		else
		{
			alert('Failed to upload file\n' + response.status);
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
