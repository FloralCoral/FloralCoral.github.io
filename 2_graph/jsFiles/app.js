$(document).ready(function(){
	$.ajax({
		url:"http://localhost/2_graph/jsFiles/jsonData.json",
		method:"GET",
		success:function(data){
			console.log(data);
			var testDate=[];
			var positiveCases=[];

			for(var i in data){
				testDate.push(data[i].Lab_Collection_Date);
				positiveCases.push(data[i].Daily_Positive_Test_Count);
			}

			//Selects the first parish name from the parish column (all the names in the column should be the same)
			var parishName=data[0].Parish;


			var chartdata={
				labels:testDate,
				datasets:[
					{
						label:'Number of Positive Covid Cases in ' + parishName + ' parish by day',
						backgroundColor: 'rgba(255, 0, 0, 1)' ,
						borderColor: 'rgba(200, 200, 200, 1)' ,
						hoverBackgroundColor: 'rgba(200, 200, 200, 1)' ,
						hoverBorderColor: 'rgba(200, 200, 200, 1)' ,
						data: positiveCases
					}
				]
			};

			var ctx= $("#myParishCanvas");

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