$('#run-fight-modal').on('show.bs.modal', function (event) {
	var button = $(event.relatedTarget);
	var target = button.data('button-target');
	var id = button.data('id');
	var team1 = button.data('team1');
	var robot1name = button.data('robot1name');
	var entrant1 = button.data('entrant1');
	var team2 = button.data('team2');
	var robot2name = button.data('robot2name');
	var entrant2 = button.data('entrant2');

	var button1text = '<span style="font-size:smaller">' + team1 + '</span><br>' + robot1name;
	//button1text += '<br><span style="font-size:small">' + target + "/" + id + "?winner=" + entrant1 + '</span>';
	var button2text = '<span style="font-size:smaller">' + team2 + '</span><br>' + robot2name;
	//button2text += '<br><span style="font-size:small">' + target + "/" + id + "?winner=" + entrant2 + '</span>';
	 
	var modal = $(this);
	modal.find('.modal-title').text('Current Fight = ' + id);
	modal.find('.modal-body #button1').html(button1text);
	modal.find('.modal-body #button2').html(button2text);
	modal.find('.modal-body #fight').val(id);
	modal.find('.modal-body #target').val(target);
	modal.find('.modal-body #entrant1').val(entrant1);
	modal.find('.modal-body #entrant2').val(entrant2);
});

$('#button1').click(function()
{
	target = $('#target').val();
	id = $('#fight').val();
	entrant = $('#entrant1').val();
	$('#run-fight-modal').modal('hide');
	$(location).attr('href',target + '/' + id + '?winner=' + entrant);
});

$('#button2').click(function()
{
	target = $('#target').val();
	id = $('#fight').val();
	entrant = $('#entrant2').val();
	$('#run-fight-modal').modal('hide');
	$(location).attr('href',target + '/' + id + '?winner=' + entrant);
});

$('#change-result').on('show.bs.modal', function (event) {
	var winner;
	var loserId;
	var button = $(event.relatedTarget);
	var target = button.data('button-target');
	var update = button.data('button-update');
	var id = button.data('id');
	var team1 = button.data('team1');
	var robot1name = button.data('robot1name');
	var entrant1 = button.data('entrant1');
	var team2 = button.data('team2');
	var robot2name = button.data('robot2name');
	var entrant2 = button.data('entrant2');
	var winnerId = button.data('winner-id');
	if (winnerId == entrant1)
	{
		winner = robot1name + ' (' + team1 + ')';
		loserId = entrant2;
	}
	else if (winnerId == entrant2)
	{
		winner = robot2name + ' (' + team2 + ')';
		loserId = entrant1;
	}
	else
	{
		winner = 'Unknown! winnerId = ' + winnerId
	}
	var modal = $(this);
	modal.find('.modal-title').text('Winner = ' + winner);

	$.ajax(
	{
		type: "post",
		dataType: 'json',
		url: target + '?id=' + id,
	})
	.done(function(response)
	{
		if (response.status == 'false')
		{
			modal.find('.modal-body #change-button').html('Cannot change result');
			modal.find('.modal-body #change-button').removeClass('btn-success').addClass('btn-danger disabled');
		}
		else if (response.status == 'true')
		{
			modal.find('.modal-body #change-button').html('Change result');
			modal.find('.modal-body #change-button').removeClass('btn-danger disabled').addClass('btn-success');
		}
		modal.find('.modal-body #change-fight').val(id);
		modal.find('.modal-body #change-target').val(update);
		modal.find('.modal-body #change-entrant1').val(winnerId);
		modal.find('.modal-body #change-entrant2').val(loserId);
	})
	.fail(function (jqXHr, textStatus, errorThrown)
	{
		console.debug(jqXHr.responseText);
		console.log(textStatus);
		console.log(errorThrown);
	});
});

$('#change-button').click(function()
{
	target = $('#change-target').val();
	id = $('#change-fight').val();
	winner = $('#change-entrant1').val();
	replacement = $('#change-entrant2').val();
	$('#change-result').modal('hide');
	$(location).attr('href',target + '?id=' + id + '&winner=' + winner + '&change=true&replacement=' + replacement);
});
