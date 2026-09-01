 
document.getElementById("primartom").addEventListener("click", function(event){
  event.preventDefault()
});
$("#primartom").click(function(){
var username=$("#username").val();
var password=$("#password").val();
var tahun=$("#myselects_log").val();

if($("#username").val()=="")
{
	//alert ("username belum diisi");
	alert("Oops! Username belum diisi!", "warning");
	//$("#username").focus();
	return false;
}

if($("#password").val()=="")
{
	alert("Oops! Password belum diisi!", "warning");
	return false;
} else {
	var data='username='+username+'&password='+password;
	$.ajax({
	type:'POST',
	url:"login/proses",
	data:data,
	success : function(html){
		var check=parseInt(html,10);
		if (check==1){
			setTimeout(function(){
			 location.reload();
			}, 1);
		}else if(check==2){
			alert("User still login!");
		}else{
			alert("Oops! Username atau Password Salah!", "error");
		}
	}
	});
}
});
