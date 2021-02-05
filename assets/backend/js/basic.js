$select = $("#states").off("change");
$select.on("change", function(e) {
	var state = $(this).val();
	//alert(state);
	$.ajax({
	 	type: "POST",
	 	url: base_url + "admin/get_city/"+state,
		success: function(data){
		//alert(data);
		$("#cities").html(data);
		},
		error: function(xhr,status,strErr){
		//alert(status);
		}	
	});
});

$(document).ready(function() {
	// Basic
	$('.dropify').dropify();
});

$(function () {
	$("#title").change(function () {
	    if ($(this).val() == "other") {
            $("#title_other").show();
        } else {
            $("#title_other").hide();
        }
	});
});

$(function () {
	$("#title").change(function () {
	    if ($(this).val() == "mp") {
            $("#top").show();
            $("#mla_st").hide();
            $("#mla_di").hide();
            $("#mla_seat").hide();

        } else if ($(this).val() == "mla") {
            $("#top").hide();
            $("#lsabha_const").hide();
            $("#rsabha_state").hide();
            $("#mla_st").show();
            $("#mla_di").show();
            $("#mla_seat").show();
        } else {
            $("#top").hide();
            $("#mla_st").hide();
            $("#mla_di").hide();
            $("#mla_seat").hide();
        }
	});
});

$(function () {
	$("#parliament_type").change(function () {
	    if ($(this).val() == "rajyasabha") {
            $("#rsabha_state").show();
            $("#lsabha_const").hide();
        } else if ($(this).val() == "loksabha") {
        	$("#rsabha_state").hide();
            $("#lsabha_const").show();
        } else {
            $("#rsabha_state").hide();
            $("#lsabha_const").hide();
            
        }
	});
});

$states = $("#mla_state").off("change");
$states.on("change", function(e) {
	var state = $(this).val();
	//alert(state);
	$.ajax({
	 	type: "POST",
	 	url: base_url + "admin/get_dist/"+state,
		success: function(data){
		//alert(data);
		$("#mla_dist").html(data);
		},
		error: function(xhr,status,strErr){
		//alert(status);
		}	
	});
});

$dists = $("#mla_dist").off("change");
$dists.on("change", function(e) {
	var state = $(this).val();
	//alert(state);
	$.ajax({
	 	type: "POST",
	 	url: base_url + "admin/get_aconst/"+state,
		success: function(data){
		//alert(data);
		$("#mla_const").html(data);
		},
		error: function(xhr,status,strErr){
		//alert(status);
		}	
	});
});

