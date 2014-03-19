function initCap() {
	$(this).val($(this).val().replace(/[^a-zA-Z]+/g, '').replace(/^[a-z]/g, function(match){
		return match.toUpperCase();
	}));
	$('#tableName').val($(this).val().replace(/[A-Z]/g, function(match, pos){
		return (pos > 0) ? '_' + match.toLowerCase() : match.toLowerCase();
	}));
}

function justComma(){
	$(this).val($(this).val().replace(/^[(\s),]/,function(match, pos){
		return (pos === 0) ? '' : ', ';
	}).replace(/[^a-zA-Z0-9,_]+/g, ''));
}