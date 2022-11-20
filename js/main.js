$(document).ready(function(){
});

function getData(url, fail_message, callback) {
	$.ajax(url, {
		dataType: 'json', // type of response data
		timeout: 3000, // timeout milliseconds
		success: function (data, status, xhr) { // success callback function
			if (status == 'success') {
				console.log('get data success', data);
				callback(data);
			} else {
				console.log('post data fail');
				if (fail_message) {
					console.log(fail_message);
				}
			}
		},
		error: function (jqXhr, textStatus, errorMessage) { // error callback
			console.log('post data error', errorMessage, 'textStatus', textStatus);
			if (fail_message) {
				console.log(fail_message);
			}
		}
	});
}