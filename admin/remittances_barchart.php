<!DOCTYPE html>
<meta charset="utf-8">
<head>
	<style>
		.axis {
			font: 10px sans-serif;
			}
		.axis path, .axis line {
			fill: none;
			stroke: #000;
			shape-rendering: crispEdges;
			}
		.line {
			stroke: #000000;
			stroke-width: 0.5;
			}
		rect, .x .tick{
			cursor:pointer;
			}
		.total:hover {
			fill: #204a87;
			} 
		.total {
			fill: #3465a4;
			}
		.paid:hover {
			fill: #4e9a06;
			} 
		.paid {
			fill: #73d216;
			} 
		.notpaid:hover {
			fill: #a40000;
			} 
		.notpaid {
			fill: #cc0000;
			}
		.legend span{
			margin-right:4px;
			padding:4px;
			}
		.legend span.total{
			background-color:#3465A4;
			}
		.legend span.paid{
			background-color:#73D216;
			}
		.legend span.notpaid{
			background-color:#CC0000;
			}
		svg{
			background-color:#fff !important;
			}
		.label{
			background-color:#f57900 !important;
			}
	</style>
</head>

<body>
	<script src="http://d3js.org/d3.v3.min.js"></script>
<?php
	$options="";$maxyear=0;
	foreach($_SESSION['remittancestotals'] as $year=>$totals){
		if($year>$maxyear){$maxyear=$year;}
		$options.="<option value='$year'>".($year-1)."-".$year."</option>";
		${'data'.$year}=json_encode($_SESSION['remittancestotals'][$year]);
		print "<script>var data".$year."=$.parseJSON(".json_encode(${'data'.$year}).");console.log(data".$year.")</script>";
		}
	print "<script>var maxyear=".$maxyear.";</script>";
?>
	<div>
		<select id="year">
			<?php print $options;?>
		</select>
	</div>
	<div id="viewbarchart" class="chart"></div>

	<script>
		$('#year').val('<?php echo $maxyear;?>');
		$(document).ready(function(){remittancesChart(window['data'+maxyear]);});
		$(window).resize(function() {remittancesChart(window['data'+$('#year').val()]);});
		$('#year').change(function(){remittancesChart(window['data'+$('#year').val()]);});
	</script>

	<script>
		function remittancesChart(data){
			d3.selectAll("svg").remove();

			var maxvalue=0;
			$.each(data,function(index,value){
				if(value[1][0]>maxvalue){maxvalue=value[1][0];}
			});

			var margin={top: 20, right: 20, bottom: 70, left: 60},
					width=$('#viewbarchart').width()-margin.left-margin.right,
					height=300-margin.top-margin.bottom;

			var x=d3.scale.ordinal()
					.rangeRoundBands([0, width], .1)
					.domain([8,9,10,11,12,1,2,3,4,5,6,7]);

			var y=d3.scale.linear()
					.range([height, 0])
					.domain([0,maxvalue]);

			var xAxis=d3.svg.axis()
						.scale(x)
						.orient("bottom")
						.ticks(12)
						.tickFormat(function (d, i) {
							return ['','January','February','March','April','May','June','July','August','September','October','November','December'][d];
							});

			var yAxis=d3.svg.axis()
						.scale(y)
						.orient("left")
						.ticks(10);

			var svg=d3.select("#viewbarchart").append("svg")
					.attr("width", width + margin.left + margin.right)
					.attr("height", height + margin.top + margin.bottom)
					.append("g")
					.attr("transform", "translate(" + margin.left + "," + margin.top + ")");

			svg.append("g")
				.attr("class", "x axis")
				.attr("id", "xaxis")
				.attr("transform", "translate(0," + height + ")")
				.call(xAxis)
				.selectAll("text")
				.style("text-anchor", "end")
				.attr("dx", "-.8em")
				.attr("dy", "-.55em")
				.attr("transform", "rotate(-60)" )
				.on('click',openRemittance);

			svg.append("g")
				.attr("class", "y axis")
				.call(yAxis)
				.append("text")
				.attr("transform", "rotate(-90)")
				.attr("y", 6)
				.attr("dy", ".71em")
				.style("text-anchor", "end")
				.text("Total (€)");

			svg.append("g")
				.attr("transform","translate("+(width-150)+",10)")
				.style("font-size","12px")
				.append("foreignObject")
				.attr("width", 150)
				.attr("height", 20)
				.html("<div class='legend' style='background-color:#fff !important;'><span class='total'>Total</span><span class='paid'>Paid</span><span class='notpaid'>Not paid</span></div>");

			svg.selectAll("bar")
				.data(data)
				.enter()
				.append("rect")
				.attr("class","total")
				.attr("x", function(d) { return x(d[0]); })
				.attr("width", x.rangeBand())
				.attr("y", function(d) { return y(d[1][0]); })
				.attr("height", function(d) { return height - y(d[1][0]); })
				.on('click',openRemittance)
				.on("mouseover", function(d) {
					svg.append("line")
						.attr("class", "line")
						.attr({ x1: x(0), y1: y(d[1][0]), x2: x(d[0]), y2: y(d[1][0]) });
					svg.append("text")
						.attr("class", "label")
						.attr({ x: x(d[0])/2, y: y(d[1][0])-5})
						.text("Total: "+d[1][0] + " €");
					})
				.on("mouseout", function(d) {
					svg.selectAll("line.line,text.label")
						.data([])
						.exit()
						.remove();
					});

			svg.selectAll("bar")
				.data(data)
				.enter()
				.append("rect")
				.attr("class","paid")
				.attr("x", function(d) { return x(d[0]); })
				.attr("width", x.rangeBand())
				.attr("y", function(d) { return y(d[1][1]); })
				.attr("height", function(d) { return height - y(d[1][1]); })
				.on('click',openRemittance)
				.on("mouseover", function(d) {
					svg.append("line")
						.attr("class", "line")
						.attr({ x1: x(0), y1: y(d[1][1]), x2: x(d[0]), y2: y(d[1][1]) });
					svg.append("text")
						.attr("class", "label")
						.attr({ x: x(d[0])/2, y: y(d[1][1])-5})
						.text("Paid: "+d[1][1]  + " €");
					})
				.on("mouseout", function(d) {
					svg.selectAll("line.line,text.label")
						.data([])
						.exit()
						.remove();
					});

			svg.selectAll("bar")
				.data(data)
				.enter()
				.append("rect")
				.attr("class","notpaid")
				.attr("x", function(d) { return x(d[0]); })
				.attr("width", x.rangeBand())
				.attr("y", function(d,i) { return y(d[1][1]) -(height - y(d[1][2])); })
				.attr("height", function(d) { return height - y(d[1][2]); })
				.on('click',openRemittance)
				.on("mouseover", function(d) {
					svg.append("line")
						.attr("class", "line")
						.attr({ x1: x(0), y1: y(d[1][1]), x2: x(d[0]), y2: y(d[1][1]) });
					svg.append("line")
						.attr("class", "line")
						.attr({ x1: x(0), y1: y(d[1][1]) -(height - y(d[1][2])), 
							x2: x(d[0]), y2: y(d[1][1]) -(height - y(d[1][2])) 
							});
					svg.append("text")
						.attr("class", "label")
						.attr({ x: x(d[0])/2, y: y(d[1][1]) -(height - y(d[1][2])) -5})
						.text("Not paid: "+d[1][2]  + " €");
					})
				.on("mouseout", function(d) {
					svg.selectAll("line.line,text.label")
						.data([])
						.exit()
						.remove();
					});
			}

		function openRemittance(d){
				if($.isArray(d)){
					var month = d[0];
					}
				else{var month = d;}
				year=document.getElementById('year').value;
				document.location.href = "admin.php?current=fees_remittance_list.php&month="+month+"&year="+year;
				}
	</script>
</body>
