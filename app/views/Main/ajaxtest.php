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
