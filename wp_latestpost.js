 
 var LP_Count = 0;
 
 jQuery(document).ready(function() {
   jQuery.ajax({
   url: "?lpCommand=getBaseSettings&rand=" + Math.random(),
   dataType: "script",
   success: function(){
       CheckForPosts();
   }
   });

 })
 
 function LP_Countdown(){
	 LP_Count--;
	 jQuery("#LP_Countdown").html(LP_Count);
	 if(LP_Count == 0){
	  jQuery("#LP_Countdown").html("0");
	  if(LP_Animated){
	  jQuery("#LP_Footer").slideUp('slow', function(){
	 	  jQuery("#LP_Div").remove()
	  });
	  } else {
		  jQuery("#LP_Div").remove()
	  }
	 } else {
      setTimeout("LP_Countdown()", 1000);
	 }
 }

 function CheckForPosts(){
	  jQuery.ajax({
         url: "?lpCommand=getPostCount&rand=" + Math.random(),
         success: function(returnCount){
            if(LP_PostCount < returnCount){
		     jQuery.get("?lpCommand=getMiniPost&rand=" + Math.random(), function(data){
			      
				  if(jQuery("#LP_Footer").css('display') != "none"){
				 		  jQuery("#LP_Div").remove()
				  }
				  
                  var LP_CurentHtml = jQuery('body').append('<div id="LP_Div">' + data + '</div>');
				  if(LP_ServerCount > 0){
				   LP_Countdown();
				  }

				  if(LP_Animated){ jQuery("#LP_Footer").slideDown('slow'); } else { jQuery("#LP_Footer").show(); }

				  LP_Count = LP_ServerCount + 1;
             });
			     LP_PostCount = returnCount;
			}
         } 
 });
			setTimeout("CheckForPosts()", LP_ChkTime);
 }

