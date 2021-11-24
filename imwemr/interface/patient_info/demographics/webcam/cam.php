<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
	<style>

	video {
  	-webkit-transform: scaleX(-1);
  	transform: scaleX(-1);
	}

  .videostream{
    display:inline-block;
  }

  #screenshot-img{
    border:1px solid red; display:inline-block;
  }

  .capture-button{
    display:none;
  }

  #screenshot-button{
  }

	</style>
</head>
<body>
  <h1>Camera Test</h1>
	<div id="screenshot" style="text-align:center;">
		<center>
			<video class="videostream" autoplay="" ></video>
			<img id="screenshot-img" src=""  >
		</center>
		<p>
			<button class="capture-button" >Capture video</button>
			<button id="screenshot-button" disabled="">Take screenshot</button>
		</p>
	</div>

	<script>

	const captureVideoButton = document.querySelector('#screenshot .capture-button');
	const screenshotButton = document.querySelector('#screenshot-button');

	const img = document.querySelector('#screenshot img');
	const video = document.querySelector('#screenshot video');
	const canvas = document.createElement('canvas');

	const constraints = {
	  //video: true
		//video: {width: {min: 1280}, height: {min: 720}}
		video: {
				width: {exact: 300}, height: {exact: 300}, facingMode: "environment"
			}
	};

	captureVideoButton.onclick = function() {
		captureVideoButton.disabled = true;
	  navigator.mediaDevices.getUserMedia(constraints).
	    then(handleSuccess).catch(handleError);
	};

	screenshotButton.onclick = video.onclick = function() {
	  canvas.width = video.videoWidth;
	  canvas.height = video.videoHeight;
		var ctx = canvas.getContext('2d');
	  ctx.translate(300, 0);
	  ctx.scale(-1, 1);
	  ctx.drawImage(video, 0, 0);

	  // Other browsers will fall back to image/png
		let img_data = canvas.toDataURL('image/webp');
		img.src = img_data;			
	};

	function handleSuccess(stream) {
	  screenshotButton.disabled = false;
	  video.srcObject = stream;
	}

	function handleError(error) {
	  console.error('Error: ', error);
	}

	window.onload = function(){
		captureVideoButton.click();
	}

	</script>
</body>
</html>
