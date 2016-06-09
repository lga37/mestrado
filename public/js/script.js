//script.js
$(function () {
	$('[data-toggle="tooltip"]').tooltip();
	$('[data-toggle="popover"]').popover();

    $('[data-spy="scroll"]').each(function(){
        var $spy = $(this).scrollspy('refresh');
    }); 


    new Morris.Line({
      // ID of the element in which to draw the chart.
      element: 'grafico1',
      // Chart data records -- each entry in this array corresponds to a point on
      // the chart.
      data: [
        { ano: '2003', alunos: 30 },
        { ano: '2004', alunos: 10 },
        { ano: '2005', alunos: 10 },
        { ano: '2006', alunos: 5 },
        { ano: '2007', alunos: 20 },
        { ano: '2008', alunos: 20 },
        { ano: '2009', alunos: 10 },
        { ano: '2010', alunos: 5 },
        { ano: '2011', alunos: 5 },
        { ano: '2012', alunos: 20 },
      ],
      // The name of the data record attribute that contains x-values.
      xkey: 'ano',
      // A list of names of data record attributes that contain y-values.
      ykeys: ['alunos'],
      // Labels for the ykeys -- will be displayed when you hover over the
      // chart.
      labels: ['Alunos']
    });

    new Morris.Donut({
    element: 'donut-example',
    data: [
        {label: "Economia", value: 12},
        {label: "Informatica", value: 30},
        {label: "Direito", value: 20}
    ]
    });


    new Morris.Area({
      element: 'area-example',
      data: [
        { y: '2006', a: 100, b: 90 },
        { y: '2007', a: 75,  b: 65 },
        { y: '2008', a: 50,  b: 40 },
        { y: '2009', a: 75,  b: 65 },
        { y: '2010', a: 50,  b: 40 },
        { y: '2011', a: 75,  b: 65 },
        { y: '2012', a: 100, b: 90 }
      ],
      xkey: 'y',
      ykeys: ['a', 'b'],
      labels: ['Series A', 'Series B']
    });


    new Morris.Bar({
      element: 'bar-example',
      data: [
        { y: '2006', a: 100, b: 90 },
        { y: '2007', a: 75,  b: 65 },
        { y: '2008', a: 50,  b: 40 },
        { y: '2009', a: 75,  b: 65 },
        { y: '2010', a: 50,  b: 40 },
        { y: '2011', a: 75,  b: 65 },
        { y: '2012', a: 100, b: 90 }
      ],
      xkey: 'y',
      ykeys: ['a', 'b'],
      labels: ['Series A', 'Series B']
    });




    $('.count').each(function () {
        $(this).prop('Counter',0).animate({
            Counter: $(this).text()
        }, 
        {
            duration: 7000,
            easing: 'swing',
            step: function (now) {
                $(this).text(Math.ceil(now));
            }
        });
    });


});//final


