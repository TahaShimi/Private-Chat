conn.onmessage=function(e){var t=jQuery.parseJSON(e.data);if(200==t.status&&("total_unread_messages"==t.action&&$("#total_unread_messages").text(t.total_unread_messages),"newMessage"==t.action&&($("#total_unread_messages").text(t.total_unread_messages),setTimeout(function(){var e=parseInt($("#total_unread_messages").text());if(e>0){document.getElementById("pageIcon").href="../../assets/images/notify.png";var t=document.title.split("(");document.title=t[0]+" ("+e+")"}else{document.getElementById("pageIcon").href="../../assets/images/favicon.png";t=document.title.split("(");document.title=t[0]}},1e3),"1"==$("#audio_notification_value").val()&&document.getElementById("play").play(),1==$("#browser_notification_value").val()))){let e=window.location.href+"/../../uploads/consultants/"+receiver_avatar;Push.create("New Message From "+t.consultant_fullName,{body:t.message,icon:e,timeout:4e3,vibrate:[200,100,200],tag:"new-message",onClick:function(){window.focus(),this.close()}})}},$.ajax({url:"../customerTrait.php",type:"POST",data:{action:"getBalance",customer_id:customer_id},dataType:"json",success:function(e){200==e.statusCode&&(balance=e.balance,$(".header-balance-text").text(unlimited==1?unlimitedtext:balance))}});