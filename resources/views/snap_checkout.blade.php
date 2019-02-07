<html>
<title>Checkout</title>
  <head>
    <script type="text/javascript"
            src="https://app.sandbox.midtrans.com/snap/snap.js"
            data-client-key="<CLIENT-KEY>"></script>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
  </head>
  <body>

    
    <form id="payment-form" method="post">
      @csrf
      <input type="hidden" name="result_type" id="result-type" value="">
      <input type="hidden" name="result_data" id="result-data" value="">
    </form>
    
    <button id="pay-button">Pay!</button>
    <script type="text/javascript">
  
    $('#pay-button').click(function (event) {
         $.ajaxSetup({
        		headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
      		});
      event.preventDefault();
      $(this).attr("disabled", "disabled");
    
    $.ajax({
      
      url: './snaptoken', //url provids here there is no need to action attribute in form html tag
      cache: false,
      dataType: "json",
			type: "POST",
      data: {
					'_token': $('input[name=_token]').val(),
			},
      success: function(data) {
        //location = data;

        console.log('token = '+data);
        
        var resultType = document.getElementById('result-type');
        var resultData = document.getElementById('result-data');

        function changeResult(type,data){
          $("#result-type").val(type);
          $("#result-data").val(JSON.stringify(data));
          //resultType.innerHTML = type;
          //resultData.innerHTML = JSON.stringify(data);
        }

        snap.pay(data, {
          
          onSuccess: function(result){
            changeResult('success', result);
            console.log(result.status_message);
            console.log(result);
            $("#payment-form").submit();
          },
          onPending: function(result){
            changeResult('pending', result);
            console.log(result.status_message);
            $("#payment-form").submit();
          },
          onError: function(result){
            changeResult('error', result);
            console.log(result.status_message);
            $("#payment-form").submit();
          }
        });
      }
    });
  });

  </script>


</body>
</html>
