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
	var button = $(event.relatedTarget);
	var target = button.data('button-target');
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
	}
	else if (winnerId == entrant2)
	{
		winner = robot2name + ' (' + team2 + ')';
	}
	else
	{
		winner = 'Unknown! winnerId = ' + winnerId
	}
	var modal = $(this);
	modal.find('.modal-title').text('Winner = ' + winner);
	modal.find('.modal-body #change-button').html('Change result');
	modal.find('.modal-body #change-fight').val(id);
	modal.find('.modal-body #change-target').val(target);
	modal.find('.modal-body #change-entrant1').val(entrant1);
	modal.find('.modal-body #change-entrant2').val(entrant2);
});

$('#change-button').click(function()
{
	target = $('#change-target').val();
	id = $('#change-fight').val();
	entrant = $('#change-entrant1').val();
	$('#change-result').modal('hide');
	$(location).attr('href',target + '/' + id + '?winner=' + entrant);
});
