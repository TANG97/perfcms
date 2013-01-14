function bbtags(open, close, bb_id, input_id)
		{
			$("#" + bb_id).ready(function () 
			{
				var text = $("#" + input_id).val();
				$("#" + input_id).val(text + open + close);
				$("#" + input_id).focus();
			});
			return false;
		}