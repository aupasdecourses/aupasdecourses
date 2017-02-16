function demoFromHTML() {
     var doc = new jsPDF('p', 'in', 'letter');
     var source = $('#canvas').first();
     var specialElementHandlers = {
         '#bypassme': function(element, renderer) {
             return true;
         }
     };

     doc.fromHTML(
        $('#canvas').get(0), // [Refer Exact code tutorial][2]HTML string or DOM elem ref.
         0.5,    // x coord
         0.5,    // y coord
         {
             'width': 7.5, // max width of content on PDF
             'elementHandlers': specialElementHandlers
         });

     doc.output('dataurl');
}