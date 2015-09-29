<html>
<head>
	<title>Checkout</title>
</head>
<body>

	<h1>Transaction action</h1>
	<form action="vt_transaction" method="POST">
	<input type="hidden" name="_token" value="{!! csrf_token() !!}">
			<p>
				<label>Order id</label>
				<input value="" size="20" type="text" name="order_id" autocomplete="off"/>
			</p>
			<p>
				<label>Action</label><br/>
				<input type="radio" name="action" value="status">Get Status<br>
				<input type="radio" name="action" value="approve">Approve<br>
				<input type="radio" name="action" value="cancel">Cancel<br>
				<input type="radio" name="action" value="expire">Expire<br>
			</p>
			<button class="submit-button" type="submit">Submit Payment</button>
	</form>
</body>
</html>
