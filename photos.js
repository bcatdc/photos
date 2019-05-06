$(document).ready(function(){
    $("span").tooltip();
    $("span").hover(function(){
		smartToggle($(this),'');
    });
	$("span").click(function(){
	 		console.log( $(this).data('id') + ' - ' + $(this).data('tag')  );
			smartToggle($(this),'update');
	});
});

function loadVideo(id,file,type){
	document.getElementById(id).innerHTML = "<div class='grid' style='background-size: contain; background-repeat: no-repeat; '> <video style='width:100%;' controls> <source src='full_res/" + file +"' type='video/" + type + "'></video></div><BR><span style='font-size:10px;'></span>"
}

function updatedb(id,action,tag){
	console.log( action +'-'+ tag +'-'+ id);
	$.ajax({
	        method: "get",
	        url: 'db_update.php',
	        data: {action:action, tag: tag, 'id': id},
	        success: function(data){
	            var result = JSON.parse(data);
	            console.log(result);
	        }
	});
}

function show_map(){
console.log('clicked');
	document.getElementById("geo_overlay").style.display = "block";
    initialize();
}


var fadedout = 1;

function showOverlay(){
	if ( fadedout == 0){
		console.log('wait');
	}else{
		$('#overlay').addClass('showverlay');
		$('#overlay').removeClass('hideverlay');
		fadedout = 0;

		setTimeout(function(){
		  $('#overlay').addClass('hideverlay');
		  $('#overlay').removeClass('showverlay');
		  fadedout = 1;
		}, 4000);
	}
}

function smartToggle(el,update){
		if( el.hasClass("off")){
			el.removeClass('off');
			el.addClass('on');
				if(update == 'update'){
    				var action = 'removetag';
        			console.log( el.data('id') + action +  el.data('tag') );
    				updatedb(el.data('id'), action, el.data('tag'));
				}
    	}else{
			el.removeClass('on');
			el.addClass('off');
    		if(update == 'update'){
				var action = 'addtag';
    			console.log( el.data('id') + action +  el.data('tag') );
				updatedb(el.data('id'), action, el.data('tag'));
			}
    	}
    }
