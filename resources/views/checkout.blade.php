<html>
<head>
	<title>Checkout</title>
	<!-- Include PaymentAPI  -->
	<link href="{{ URL::to('css/jquery.fancybox.css') }}" rel="stylesheet"> 
</head>
<body>
	<script type="text/javascript" src="https://api.sandbox.veritrans.co.id/v2/assets/js/veritrans.min.js"></script>
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>	
	<script type="text/javascript" src="{{ URL::to('js/jquery.fancybox.pack.js') }}"></script>


	<h1>Checkout</h1>
	<form action="vtdirect" method="POST" id="payment-form">
	<input type="hidden" name="_token" value="{!! csrf_token() !!}">
		<fieldset>
			<legend>Checkout</legend>
			<p>
				<label>Card Number</label>
				<input class="card-number" value="4011111111111112" size="20" type="text" autocomplete="off"/>
			</p>
			<p>
				<label>Expiration (MM/YYYY)</label>
				<input class="card-expiry-month" value="12" placeholder="MM" size="2" type="text" />
		    	<span> / </span>
		    	<input class="card-expiry-year" value="2018" placeholder="YYYY" size="4" type="text" />
			</p>
			<p>
		    	<label>CVV</label>
		    	<input class="card-cvv" value="123" size="4" type="password" autocomplete="off"/>
			</p>

			<p>
		    	<label>Save credit card</label>
		    	<input type="checkbox" name="save_cc" value="true">
			</p>

			<input id="token_id" name="token_id" type="hidden" />
			<button class="submit-button" type="submit">Submit Payment</button>
		</fieldset>
	</form>

	<!-- Javascript for token generation -->
	<script type="text/javascript">
	$(function(){
		// Sandbox URL
		Veritrans.url = "https://api.sandbox.veritrans.co.id/v2/token";
		// TODO: Change with your client key.
		Veritrans.client_key = "VT-client-tsQabcFjwuwUuN7a";
		//Veritrans.client_key = "VT-client-h7ubdjqpcsLAQnjY";
		
		//Veritrans.client_key = "d4b273bc-201c-42ae-8a35-c9bf48c1152b";
		var card = function(){
			return { 	'card_number'		: $(".card-number").val(),
						'card_exp_month'	: $(".card-expiry-month").val(),
						'card_exp_year'		: $(".card-expiry-year").val(),
						'card_cvv'			: $(".card-cvv").val(),
						'secure'			: false,
						'bank'				: 'bni',
						'gross_amount'		: 10000
						 }
		};

		function callback(response) {
			if (response.redirect_url) {
				// 3dsecure transaction, please open this popup
				openDialog(response.redirect_url);

			} else if (response.status_code == '200') {
				// success 3d secure or success normal
				closeDialog();
				// submit form
				$(".submit-button").attr("disabled", "disabled"); 
				$("#token_id").val(response.token_id);
				$("#payment-form").submit();
			} else {
				// failed request token
				console.log('Close Dialog - failed');
				//closeDialog();
				//$('#purchase').removeAttr('disabled');
				// $('#message').show(FADE_DELAY);
				// $('#message').text(response.status_message);
				//alert(response.status_message);
			}
		}

		function openDialog(url) {
			$.fancybox.open({
		        href: url,
		        type: 'iframe',
		        autoSize: false,
		        width: 700,
		        height: 500,
		        closeBtn: false,
		        modal: true
		    });
		}

		function closeDialog() {
			$.fancybox.close();
		}

		$('.submit-button').click(function(event){
			event.preventDefault();
			//$(this).attr("disabled", "disabled"); 
			Veritrans.token(card, callback);
			return false;
		});
	});

	</script>
</body>
</html>
