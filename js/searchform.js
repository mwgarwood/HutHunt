function validateForm()
{
	//var x = document.forms[0]["area"].value;
	x = $("#area").val();
	if (x == null || x == "")
	{
		errmsg("Please provide a valid city, state or zip code.");
		return false;
	}
	var fields = ["Minimum Price", "Maximum Price", "Square Footage"];
	for (var i = 0; i < fields.length; i++)
	{
		//x = document.forms[0][fields[i]].value;
		x = $("[name='" + fields[i] + "']").val();
		if (!/^ *\d* *$/.test(x))
		{
			errmsg(fields[i] + " should be a valid number.");
			//document.forms[0][fields[i]].value = "";
			return false;
		}
	}
	return true;
}

function errmsg(errstr)
{
	$("#errmsg").html(errstr);
}