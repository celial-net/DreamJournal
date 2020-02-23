<?php
/** @var array $dreamCountData */
?>

<canvas id="myChart" width="800" height="600"></canvas>
<script>
	$(document).ready(function(){
		var ctx = document.getElementById('myChart').getContext('2d');
		var myChart = new Chart(ctx, {
			type: 'bar',
			data: {
				labels: <?=json_encode(array_keys($dreamCountData))?>,
				datasets: [{
					label: 'Dream Count by Category',
					data: <?=json_encode(array_values($dreamCountData))?>,
					backgroundColor: 'rgba(255, 99, 132, 0.2)',
					borderColor: 'rgba(255, 99, 132, 1)',
					borderWidth: 1
				}]
			},
			options: {
				responsive: false,
					scales: {
					yAxes: [{
						ticks: {
							beginAtZero: true
						}
					}]
				}
			}
		});
	});
</script>