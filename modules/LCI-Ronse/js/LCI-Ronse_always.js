$(document).ready(function() {
	//$('#gibbonFinanceFeeCategoryID1').prop( "readonly", null );
	$('select').not($("select[name^='gibbonFinanceFeeCategoryID']")).select2({ width: '100%' });
});
