<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="format-detection" content="telephone=no">
    <script src="jquery.js"></script>
</head>
<style>
    .box{width:90%;height:600px;margin:0 auto;border:1px solid #ccc;border-radius:5px;padding:4px;}
    .header{margin-left:20px;}
    .content{height:300px;width:80%;border:1px solid #f00;overflow-y:scroll;margin-left:20px;}
    .sendbox{margin-left:20px;}
    .error{color:#f00;}
    .blue{color:#00f;font-weight:bold;}
    .red{color:#f00;font-weight:bold;}
    .from{text-align:left;}
    .to{text-align:right;}
</style>
<body>
    <div class="box">
        <div class="header">
            Room:<input type="text" name="roomid"/>
        </div>
        <div class="header">
            From:<input type="text" name="from" size="5"/>
            To:<input type="text" name="to" size="5"/>
        </div>
        <div class="sendbox">
            <input type="text" name="msg" />
            <button class="sendbtn">Send</button>
        </div>
        <div class="content">
        </div>
        <div class="error"></div>

    </div>
</body>
<script>
    $(function(){
        var readRecords = false;
        var recordTimer = null;
        recordTimer = setInterval(function(){
            var roomId = $('input[name=roomid]').val()
            var sendTo = $('input[name=to]').val();
            var from = $('input[name=from]').val();
            if((sendTo == '') || (from == '') || (roomId == '')){
                return;
            }
            $.ajax({
                url:'records.php',
                type:'post',
                dataType:"json",
                data:{'to':sendTo,'from':from,'roomId':roomId},
                success:function(data){
                    if(data.status == 0){
                        if(data.msg.length != 0){
                            var msgHtml = '';
                            var obj = null;
                            for(var i in data.msg){
                                obj = data.msg[i];
                                if(obj.from == from){
                                    msgHtml += '<div class="from"><span class="blue">'+data.msg[i].from+':</span>'
                                        + uncompile(data.msg[i].msg,obj.key) + '</div>';
                                }else{
                                    msgHtml += '<div class="to"><span class="red">'+data.msg[i].from+':</span>'
                                        + uncompile(data.msg[i].msg,obj.key) + '</div>';
                                }
                            }
                            $('.content').append(msgHtml);
                        }
                        clearInterval(recordTimer);

                    }else{
                        $('.error').html('记录拉取失败了，请重登;');
                    }
                }
            })
        },1000);
        setInterval(function(){
            var roomId = $('input[name=roomid]').val()
            var sendTo = $('input[name=to]').val();
            var from = $('input[name=from]').val();
            if((sendTo == '') || (from == '') || (roomId == '')){
                return;
            }
            $.ajax({
                url:'read.php',
                type:'post',
                dataType:"json",
                data:{'to':sendTo,'from':from,'roomId':roomId},
                success:function(data){
                    if(data.status == 0){

                        if(data.msg.length != 0){
                            var msgHtml = '';
                            for(var i in data.msg){
                                msgHtml += '<div class="to"><span class="red">'+data.msg[i].from+':</span>'
                                    + uncompile(data.msg[i].msg,data.msg[i].key) + '</div>';
                            }
                            $('.content').append(msgHtml);
                        }


                    }else{
                        $('.error').html('发送失败了，请重发;');
                    }
                }
            })
        },5000);
        $('.sendbtn').click(function () {
            var msg = $('input[name=msg]').val();
            var roomId = $('input[name=roomid]').val()
            var sendTo = $('input[name=to]').val();
            var from = $('input[name=from]').val();
            if((sendTo == '') || (from == '') || (roomId == '') || (msg == '')){
                return;
            }

		var key = parseInt(Math.random()* 10000);
            $.ajax({
                url:'ca.php',
                type:'post',
                dataType:"json",
                data:{'to':sendTo,'msg':compile(msg,key),'from':from,'roomId':roomId,'key':key},
                success:function(data){
                    if(data.status == 0){
                        $('input[name=msg]').val('');
                        var msgHtml = '<div class="from"><span class="blue">'+data.from+':</span>'
                        + uncompile(data.msg,data.key) + '</div>';
                        $('.content').append(msgHtml);
                    }else{
                        $('.error').html('发送失败了，请重发;');
                    }
                }
            })
        });
    });
		function compile(code,key) {
			var c=code.charCodeAt(0)+key;
			for(var i=1;i<code.length;i++){
				c+='|'+(code.charCodeAt(i)+code.charCodeAt(i-1) + key);
			}
			return c;
		}
		function uncompile(code,key) {
			var codeArr = code.split('|');
			var c=String.fromCharCode(codeArr[0]-key);
			var lastCode = codeArr[0]-key;
			for(var i=1;i<codeArr.length;i++){
				c+=String.fromCharCode(codeArr[i]-lastCode - key);
				lastCode = codeArr[i]-lastCode - key;
			}
			return c;
		}
</script>
</html>
