// handle change of selection in robot dropdown box
$('#entrant-robotid').change(function(event)
{
	var className = ["", "Nanoweight", "Fleaweight", "Antweight"];
	var eventClass = $('#event-classid').val();
	var robotClass = $('#entrant-robotid').find(":selected").data('class');
	if (eventClass != robotClass)
	{
		// popup modal dialog if robot class doesn't match event class
		$('#check-class-modal').find('.modal-body #event-class').html(className[eventClass]);
		$('#check-class-modal').find('.modal-body #robot-class').html(className[robotClass]);
		$('#check-class-modal').modal('show');
	}
});

// handle Yes button click
$('#button1').click(function()
{
	$('#check-class-modal').modal('hide');
});

// handle No button click
$('#button2').click(function()
{
	// clear robot selection
	$('select#entrant-robotid option').each(function() { this.selected = (this.val == '0'); });
	$('#check-class-modal').modal('hide');
});
