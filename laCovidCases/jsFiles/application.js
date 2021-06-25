$(document).ready(function(){
	$.ajax({
		url:"http://localhost/laCovidCases/data.php",
		method:"GET",
		success:function(data){
			console.log(data);
			var testDate=[];
			var positiveCases=[];

			for(var i in data){
				testDate.push(data[i].Lab_Collection_Date);
				positiveCases.push(data[i].Daily_Positive_Test_Count);
			}

			var chartdata={
				labels:testDate,
				datasets:[
					{
						label:'Number of Positive Covid Cases in Louisiana per day',
						backgroundColor: 'rgba(255, 0, 0, 1)' ,
						borderColor: 'rgba(200, 200, 200, 1)' ,
						hoverBackgroundColor: 'rgba(200, 200, 200, 1)' ,
						hoverBorderColor: 'rgba(200, 200, 200, 1)' ,
						data: positiveCases
					}
				]
			};

			var ctx= $("#mycanvas");

			var barGraph=new Chart(ctx, {
				type:'bar',
				data: chartdata
			});
		},
		error:function(data){
			console.log(data);
		}
	});
});