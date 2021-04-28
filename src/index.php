<?php
	include('database.inc.php');
?>
<!DOCTYPE html>
<html lang="en">
   <head>
      <meta charset="utf-8">
      <meta name="robots" content="noindex, nofollow">
      <title>Deadline Remainder Assistant</title>
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
	  <link href="public/css/style.css" rel="stylesheet" type="text/css">
      <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
      <script src="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
   </head>
   <body>
      <div class="container">
		 <h2>Deadline Remainder Assistant</h2>
         <div class="row justify-content-md-center mt-4 mb-3">
            <div class="col-md-6">
               <!--start code-->
               <div class="card">
                  <div class="card-body messages-box">
					 <ul class="list-unstyled messages-list">
						<li class="messages-me clearfix start_chat">
							Halo, <b>BowlerHatMan Bot</b> siap membantu Anda!<br><br>
							Cobalah perintah <i>"bantuan"</i> atau <i>"Apa yang bisa asisten lakukan"</i> untuk melihat fitur dan cara penggunaan chatbot.
						</li>
                     </ul>
                  </div>
                  <div class="card-header">
                    <div class="input-group">
					   <input id="input-me" type="text" name="messages" class="form-control input-sm" placeholder="Apa yang bisa saya bantu?" />
					   <span class="input-group-append">
					   <input type="button" class="btn btn-primary" value="Kirim" onclick="send_msg()">
					   </span>
					</div> 
                  </div>
               </div>
               <!--end code-->
            </div>
         </div>
      </div>
      <script type="text/javascript">
		 function getCurrentTime() {
			var now = new Date();
			var hh = now.getHours();
			var min = now.getMinutes();
			var ampm = (hh>=12)?'PM':'AM';
			hh = hh % 12;
			hh = hh?hh:12;
			hh = hh<10?'0' + hh:hh;
			min = min<10?'0' + min:min;
			var time = hh + ":" + min + " " + ampm;
			return time;
		 }
		 
		 function send_msg(){
			jQuery('.start_chat').hide();
			var txt=jQuery('#input-me').val();
			var html='<li class="messages-me clearfix"><span class="message-img"><img src="public/img/Anda.ico" class="avatar-sm rounded-circle"></span><div class="message-body clearfix"><div class="message-header"><strong class="messages-title">Anda</strong> <small class="time-messages text-muted"><span class="fas fa-time"></span> <span class="minutes">' + getCurrentTime() + '</span></small> </div><p class="messages-p">' + txt + '</p></div></li>';
			jQuery('.messages-list').append(html);
			jQuery('#input-me').val('');
			if (txt) {
				jQuery.ajax({
					url:'get_bot_message.php',
					type:'post',
					data:'txt='+txt,
					success:function(result) {
						var html='<li class="messages-you clearfix"><span class="message-img"><img src="public/img/BowlerHatMan_Bot.ico" class="avatar-sm rounded-circle"></span><div class="message-body clearfix"><div class="message-header"><strong class="messages-title">BowlerHatMan Bot</strong> <small class="time-messages text-muted"><span class="fas fa-time"></span> <span class="minutes">' + getCurrentTime() + '</span></small> </div><p class="messages-p">' + result + '</p></div></li>';
						jQuery('.messages-list').append(html);
						jQuery('.messages-box').scrollTop(jQuery('.messages-box')[0].scrollHeight);
					}
				});
			}
		 }
      </script>
   </body>
</html>