function spoiler(id)
{
	var obj = "";
	if(document.getElementById)
		obj = document.getElementById(id).style;
	else if(document.all)
		obj = document.all[id];
	else if(document.layers)
		obj = document.layers[id];
	else
		return 1;

	if(obj.display == "")
		obj.display = "none";
	else if(obj.display != "none")
		obj.display = "none";
	else
		obj.display = "block";
}