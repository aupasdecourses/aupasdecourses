$(document).ready(function() {
  $('.merchant-ticket').each(function() {
    var did = $(this).attr('id');
    var value = $(this).attr('value');

    OpenSeadragon({
      id: did,
      prefixUrl: '/indi/web/img/osd/',
      tileSources: {
        type: 'image',
        url: value
      }
    });

  });
});