<!DOCTYPE html>
<html>
<head>
<script
	src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="js/main.js"></script>
<style type="text/css">
.input-control {
	padding: 5px;
	margin-right: 5px;
}

.input-control textarea {
	width: 100%;
}

#viewDiv {
	padding: 0;
	margin: 0;
	height: 450px;
}

#sample {
    position: absolute;
    top: 10%;
    right: 55%;
    z-index: 2;
}
</style>
<link rel="stylesheet"
	href="https://js.arcgis.com/4.16/esri/themes/light/main.css" />
<script src="https://js.arcgis.com/4.16/"></script>

<script>
require([
    "esri/Map",
    "esri/views/MapView", "esri/layers/GraphicsLayer", "esri/Graphic", "esri/request"
  ], function(Map, MapView, GraphicsLayer, Graphic, esriRequest) {

    var map = new Map({
      basemap: "topo-vector"
    });

    var view = new MapView({
      container: "viewDiv",
      map: map,
      center: [$('#x').val(), $('#y').val()],
      zoom: 16
    });
    var createGraphic = function(data) {
		return new Graphic({
			geometry : data,
			symbol : data.symbol,
			attributes : data,
			popupTemplate : data.popupTemplate
		});
	};
    var json_options = {
			query : {
				f : "json"
			},
			responseType : "json"
		};
    var renderJsonLayer = function() {
		esriRequest('./api.php?dest=json&longitude='
				+ $('#x').val() + '&latitude='
				+ $('#y').val() + '&distance='
				+ $('#d').val() + '&angle='
				+ $('#a').val(), json_options).then(function(response) {
			var graphicsLayer = new GraphicsLayer();
			console.log(response);
			response.data.forEach(function(data){
				graphicsLayer.add(createGraphic(data));
			});
			map.add(graphicsLayer);

		});
	};
	$("button#get").click(function(){
        getData('./api.php?dest=coordinate&longitude='
        	+ $('#x').val() + '&latitude='
        	+ $('#y').val() + '&distance='
        	+ $('#d').val() + '&angle='
        	+ $('#a').val(),
        	'Cannot get new point', function(resp) {
            	let output = resp.toString();
        		if ($('#z').val() != null && $('#z').val() != '') {
        			output += ','+$('#z').val();
        		}
        		$("#result").val($("#result").val()+'\n'+'['+output+'],');
        	});
        map.removeAll();
        renderJsonLayer();
		view.goTo({
			  center: [$('#x').val(), $('#y').val()]
		})
		.catch(function(error) {
		  if (error.name != "AbortError") {
			     console.error(error);
			  }
		});
	});
	$("button#draw").click(function(){
        const json_text = $('#json').val();
        map.removeAll();
        try {
            const json = JSON.parse(json_text);
            let graphicsLayer = new GraphicsLayer();
			console.log(json);
			graphicsLayer.add(createGraphic(json));
			map.add(graphicsLayer);
        } catch (err) {
            alert('Cannot parse text to json!');
        }
		view.goTo({
			  center: [parseFloat($('#x').val()), parseFloat($('#y').val())]
		})
		.catch(function(error) {
		  if (error.name != "AbortError") {
			     console.error(error);
			  }
		});
	});
	view.on("click", function(event) { // Listen for the click event
		  view.hitTest(event).then(function (hitTestResults){ // Search for features where the user clicked
			if(hitTestResults.results) {
    			console.log('{"x":"'+event.mapPoint.longitude+ '", "y": "'+ event.mapPoint.latitude+'},');
    			$('#x').val(event.mapPoint.longitude);
    			$('#y').val(event.mapPoint.latitude);
			}
		  });
		});
  });

</script>
</head>
<body>
<?php
if (isset($_REQUEST['dest']) && $_REQUEST['dest'] == 'coordinate') {
    echo <<<EOI
<div style="display:flex;">
<div style="width:50%;">
    <h2>Current point: (ORANGE point)</h2>
	<div class="input-control">
        <label>(Hint: Click on map to get point)</label><br>
		<label>Longitude(x)</label> <input id="x" value="106.71588264544627">
	</div>
	<div class="input-control">
		<label>Latitude(y)</label> <input id="y" value="10.793631758760538">
	</div>
	<div class="input-control">
		<label>Height(z)</label> <input id="z" placeholder="for adding Z to result">
	</div>
	<h2>Moving it (GREEN point)</h2>
	<div class="input-control">
        <label>(Hint: Leave empty to get current point)</label><br>
		<label>Distance(m)</label> <input id="d">
	</div>
	<div class="input-control">
		<label>Angle(degree 0..360)<br>from North</label> <input id="a">
	</div>
	<br>
	<br>
	<button id="get">Get Point</button>
	<br>
	<br>
	<div class="input-control">
		<textarea id="result" rows="5"></textarea>
	</div>
    <img id="sample" src="img/sample.png" width="200"/>
</div>
<div style="width:50%;" id="viewDiv"></div>
</div>
EOI;
} else if (isset($_REQUEST['dest']) && $_REQUEST['dest'] == 'drawjson') {
    echo <<<EOI
    
<div style="display:flex;">
<div style="width:50%;">
    <h2>Center Map: </h2>
	<div class="input-control">
        <label>(Hint: Click on map to get point)</label><br>
		<label>Longitude(x)</label> <input id="x" value="106.71588264544627">
	</div>
	<div class="input-control">
		<label>Latitude(y)</label> <input id="y" value="10.793631758760538">
	</div>
    <div class="input-control">
        <label>Json</label><br>
		<textarea id="json" rows="17"></textarea>
	</div>
	<br>
	<button id="draw">Draw graph</button>
</div>
<div style="width:50%;" id="viewDiv"></div>
</div>
EOI;
} 
else {
    $supportedDests = array(
        'coordinate', 'drawjson'
    );
    $justifiedDests = array();
    foreach ($supportedDests as $supportedDest) {
        array_push($justifiedDests, "<a href='/tools/?dest=$supportedDest'>$supportedDest</a>");
    }
    echo "Supported dests are: " . implode(", ", $justifiedDests);
}
?>
</body>
</html>