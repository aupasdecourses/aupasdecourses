jQuery(document).ready(function() {
  apdcPopupTest = new ApdcPopup({
    id:'test'
  });

  jQuery('#popup-button-test').on('click', function() {
    apdcPopupTest.hideLoading();
    apdcPopupTest.show();
  });

  console.log(apdcPopupTest.id);
  jQuery(document).on(apdcPopupTest.id + '_apdc_popup_template_received', function() {
    jQuery('#popup-test-cancel').on('click', function() {
      apdcPopupTest.close();
    });

    jQuery('#popup-test-ok').on('click', function() {
      alert('Moi aussi j\'aime bien');
      apdcPopupTest.close();
    });
  });

});
