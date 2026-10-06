<?php
// Copyright (C) 2002-2012  Paul Yasi (paul at citrusdb.org)
// read the README file for more information

/*----------------------------------------------------------------------------*/
// Check for authorized accesss
/*----------------------------------------------------------------------------*/
if(constant("INDEX_CITRUS") <> 1){
	echo "You must be logged in to run this.  Goodbye.";
	exit;	
}

if (!defined("INDEX_CITRUS")) {
	echo "You must be logged in to run this.  Goodbye.";
        exit;
}

echo "<h3>$l_pending $l_refund</h3>
<SCRIPT LANGUAGE=\"JavaScript\" SRC=\"include/CalendarPopup.js\"></SCRIPT>
	<SCRIPT LANGUAGE=\"JavaScript\">
	var cal = new CalendarPopup();
	</SCRIPT>
";

// make sure the user is in a group that is allowed to run this

	/*--------------------------------------------------------------------*/
	// Show the refund data that would be processed if sent
	/*--------------------------------------------------------------------*/

	// query from billing_details ALL the refunds that are pending
	$query = "SELECT ROUND(SUM(bd.refund_amount),2) AS RefundTotal, 
			b.id b_id, b.name b_name, b.company b_company, 
			b.street b_street, b.city b_city, 
			b.state b_state, b.zip b_zip, 
			b.account_number b_acctnum, 
			b.from_date b_from_date, 
			b.to_date b_to_date, 
			b.payment_due_date b_payment_due_date,  
			bd.invoice_number bd_invoice_number, 
			bd.batch bd_batch, g.org_name   
			FROM billing_details bd
			LEFT JOIN billing b ON bd.billing_id = b.id 
			LEFT JOIN billing_types bt ON bt.id = b.billing_type 
			LEFT JOIN general g ON g.id = b.organization_id 
			WHERE bd.refunded <> 'y' AND bd.refund_amount > 0 
			AND bt.method = 'creditcard' 
			GROUP BY b.id";
	$DB->SetFetchMode(ADODB_FETCH_ASSOC);
	$result = $DB->Execute($query) 
		or die ("$l_queryfailed");

	echo "<table>";

	while ($myresult = $result->FetchRow()) {
		$batchid = $myresult['bd_batch'];
		$invoice_number = $myresult['bd_invoice_number'];
		$user = "refund";
		$mydate = $today;
		$org_name = $myresult['org_name'];
		$mybilling_id = $myresult['b_id'];
		$billing_name = $myresult['b_name'];
		$billing_company = $myresult['b_company'];
		$billing_street =  $myresult['b_street'];
		$billing_city = $myresult['b_city'];
		$billing_state = $myresult['b_state'];
		$billing_zip = $myresult['b_zip'];
		$billing_acctnum = $myresult['b_acctnum'];
		$billing_fromdate = $myresult['b_from_date'];
		$billing_todate = $myresult['b_to_date'];
		$billing_payment_due_date = $myresult['b_payment_due_date'];
		$precisetotal = $myresult['RefundTotal'];

		echo "<td>$org_name</td><td>$mybilling_id</td><td>$billing_name</td><td>$billing_company</td><td>$precisetotal</td></tr>";

	} // end while

echo "</table>";
	

?>

