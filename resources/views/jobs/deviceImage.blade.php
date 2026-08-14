@if( isset($device) && !isset($device['error']) )
    <canvas id="device-image-canvas" width="250" height="250" style="border:2px solid;"></canvas>
    <input type="button" value="clear" id="clr" size="23" onclick="erase()">

    <script type="text/javascript">
        var device_type = {{ $device->model->device_type_id }};
        //var device_info = {{ $device }};
        
        var canvas = document.getElementById('device-image-canvas');
        var context = canvas.getContext('2d');
        var imageObj = new Image();
        var deviceImg;
        var flag = false, prevX = 0, currX = 0, prevY = 0, currY = 0, dot_flag = false;
        var x = "black", y = 2;
        var rect = canvas.getBoundingClientRect();

        w = canvas.width;
        h = canvas.height;
        //alert(rect.left);
        //alert(rect.top);
        
        if( {{$device->model->device_type_id}} == 1 ) {
            deviceImg = "{!! url('images/feature_phone.png'); !!}";
        } else if ( {{$device->model->device_type_id}} == 2 ){
            deviceImg = "{!! url('images/smart_phone.png'); !!}";
        } else {
            deviceImg = "{!! url('images/tablet.png'); !!}";
        }

        imageObj.src = deviceImg;
        imageObj.onload = function() {
            context.drawImage(imageObj, 0, 0);
        };

        canvas.addEventListener("mousemove", function (e) {
            findxy('move', e)
        }, false);
        canvas.addEventListener("mousedown", function (e) {
            findxy('down', e)
        }, false);
        canvas.addEventListener("mouseup", function (e) {
            findxy('up', e)
        }, false);
        canvas.addEventListener("mouseout", function (e) {
            findxy('out', e)
        }, false);

        function findxy(res, e) {
            //alert(rect.top);
            //alert( (e.clientX - rect.left) / (rect.right - rect.left) * canvas.width );
            /*x: (evt.clientX - rect.left) / (rect.right - rect.left) * canvas.width,
            y: (evt.clientY - rect.top) / (rect.bottom - rect.top) * canvas.height*/

            if (res == 'down') {
                prevX = currX;
                prevY = currY;
                //currX = e.clientX - 225 - canvas.offsetLeft;
                currY = e.clientY - 73 - canvas.offsetTop;
                //alert(e.clientX);
                currX = (e.clientX - rect.left) / (rect.right - rect.left) * canvas.width;
                //currY = (e.clientY - rect.top) / (rect.bottom - rect.top) * canvas.height;
        
                flag = true;
                dot_flag = true;
                if (dot_flag) {
                    context.beginPath();
                    context.fillStyle = x;
                    context.fillRect(currX, currY, 2, 2);
                    context.closePath();
                    dot_flag = false;
                }
            }

            if (res == 'up' || res == "out") {
                flag = false;
            }
            
            if (res == 'move') {
                if (flag) {
                    prevX = currX;
                    prevY = currY;
                    //currX = e.clientX - 225 - canvas.offsetLeft;
                    currY = e.clientY - 73 - canvas.offsetTop;
                    currX = (e.clientX - rect.left) / (rect.right - rect.left) * canvas.width;
                    //currY = (e.clientY - rect.top) / (rect.bottom - rect.top) * canvas.height;
                    draw();
                }
            }
        }

        function draw() {
            context.beginPath();
            context.moveTo(prevX, prevY);
            context.lineTo(currX, currY);
            context.strokeStyle = x;
            context.lineWidth = y;
            context.stroke();
            context.closePath();
        }
        
        function erase() {
            context.clearRect(0, 0, w, h);
            //document.getElementById("canvasimg").style.display = "none";

            imageObj.src = deviceImg;
            context.drawImage(imageObj, 0, 0);
        }
    </script>
@endif



