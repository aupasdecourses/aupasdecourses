(function($) {
  'use strict';

  $.fn.SearchNeighborhood = function() {

    var search = {
      timeout: null,
      getNeighborhoods: function() {
        return $('section#districts .button-district');
      },
      filterList: function(searchPostcode) {
        if (search.timeout) {
          window.clearTimeout(search.timeout);
        }
        search.timeout = window.setTimeout(function() {
          var neighborhoods = search.getNeighborhoods();
          var nbNeighborhoods = neighborhoods.length;
          neighborhoods.each(function() {
            var neighborhood = $(this);
            var hide = true;
            var postcodes = '' + neighborhood.data('postcodes'); 
            if (postcodes) {
              postcodes = postcodes.split(',');
              for (var idx=0; idx < postcodes.length; ++idx) {
                if (postcodes[idx].indexOf(searchPostcode) > -1) {
                  hide = false;
                  break;
                }
              }
            }
            if (hide) {
              $(this).fadeOut();
              $(this).data('hidden', true);
            } else {
              $(this).fadeIn();
              $(this).data('hidden', false);
            }
            if (!--nbNeighborhoods) {
              search.setGridClasses();
            }
          });
        }, 300);
      },
      setGridClasses: function() {
        var neighborhoods = search.getNeighborhoods().filter(function() {
          return $(this).data('hidden') !== true;
        });
        var nbNeighborhoods = neighborhoods.length;
        if (nbNeighborhoods === 0) {
          $('#no_neighborhood').fadeIn();
          $('#neighborhood-list').fadeOut();
        } else {
          $('#no_neighborhood').fadeOut();
          $('#neighborhood-list').fadeIn();
        }

        var mdNbFullLines = (Math.floor(nbNeighborhoods / 4) * 4);
        var mdNbLeave = nbNeighborhoods - mdNbFullLines;

        var smNbFullLines = (Math.floor(nbNeighborhoods / 3) * 3);
        var smNbLeave = nbNeighborhoods - smNbFullLines;

        var xsNbFullLines = (Math.floor(nbNeighborhoods / 2) * 2);
        var xsNbLeave = nbNeighborhoods - xsNbFullLines;

        var cpt = 1; 
        neighborhoods.each(function() {
          var classMd = '';
          var classSm = '';
          var classXs = '';
          if (cpt <= mdNbFullLines) {
            classMd = 'col-md-3';
          } else {
            switch(mdNbLeave) {
              case 3:
                classMd = 'col-md-4';
                break;
              case 2:
                classMd = 'col-md-6';
                break;
              default:
                classMd = 'col-md-12';
            }
          }

          if (cpt <= smNbFullLines) {
            classSm = 'col-sm-4';
          } else {
            switch(smNbLeave) {
              case 2:
                classSm = 'col-sm-6';
                break;
              default:
                classSm = 'col-sm-12';
            }
          }
          if (cpt <= xsNbFullLines) {
            classXs = 'col-xs-6';
          } else {
            classXs = 'col-xs-12';
          }
          $(this)[0].className = 'button-district ' + classXs + ' ' + classSm + ' ' + classMd;
          cpt++;
        });
      }
    };

    $(this).on('keyup blur change', function() {
      var searchPostcode = $(this).val();
      search.filterList(searchPostcode);
    });
  };
  $(document).ready(function() {
    $('#search_neighborhoods_postcode').SearchNeighborhood();
  });
}(jQuery));
