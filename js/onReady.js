$(document).ready(function() {
	setSizes($('.payment .bar'));
	
	$('form.ajax').submit(sendForm);
	
	$('.leave-group-form').submit(removeGroup);
	
	$('input[name=hasDeadline]').click(toggleDate);
	
	$('input[type=number].money').keyup(updateMoney);
});

function sendForm() {
	var $this = $(this);
	
	var url = $this.attr('action');
	var type = typeof $this.attr('method') !== 'undefined' ? $this.attr('method') : 'POST';
	
	if (url.includes('?')) {
		url += '&ajax=1';
	} else {
		url += '?ajax=1';
	}
	
	console.log(url);
	
	$.ajax(url, {
		type: type,
		data: $this.serializeArray()
		
	}).success(function(d, status, xhr) {
		console.log(d);
		var data = JSON.parse(d);
		
		console.log(data);
		
		if (typeof data.redirect !== 'undefined') {
			window.location.replace(data.redirect);
		}
		
		if (typeof data.response !== 'undefined') {
			for (var key in data.response) {
				$('#' + key).html(data.response[key]);
			}
		}
		
		if (typeof data.errors !== 'undefined') {
			for (var key in data.errors) {
				$('#' + key).html(data.errors[key]);
			}
		}
		
		if (typeof $this.data('success') !== 'undefined') {
			$this.data('success')();
		}
		
	}).fail(function(d, status, xhr){
		console.log(d, status, xhr);
	});
	
	return false;
}

function removeGroup() {
	$this = $(this);
	
	$this.parents('.group-square').remove();
}

function setSizes(bars) {
	for (var i=0; i < bars.length; i++) {
		var paid = bars.eq(i).data('paid');
		var total = bars.eq(i).data('total');
		
		if (total <= paid) bars.eq(i).addClass('full');
		bars.eq(i).css('width', 100 * paid / total + '%');
		console.log(bars.eq(i));
	}
}

function toggleDate() {
	$this = $(this);
	var deadlineElem = $this.siblings('input[name=deadline]');
	
	if (deadlineElem.attr('disabled') === 'disabled') {
		deadlineElem.attr('disabled','');
	} else {
		deadlineElem.attr('disabled','disabled');
	}
	
}

function updateMoney() {
	$this = $(this);
	var display = $this.siblings('.money-display');
	
	var point = $this.val().indexOf('.');
	if (point === -1) {
		message = '&pound;' + $this.val() + '.00';
	} else {
		var pounds = $this.val().substring(0, point);
		var pence = $this.val().substr(point, 3);
		if (pence.length === 2) pence + '0';
		var message = '&pound;' + $this.val().substring(0, point) + pence;
	}
	
	display.html(message);
}






















