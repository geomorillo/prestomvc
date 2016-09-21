<html>
<head>
<title>jQuery Hello World</title>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
</head>

<body>

<script type="text/javascript">

$(document).ready(function(){
    
  $.ajax({url: "ajaxcall",  data: { name: "Geo", location: "Colombia" }, success: function(result){
        console.log(result);
    }});
});

</script>

This is Hello World by HTML

<div id="msgid">
</div>

</body>
</html>