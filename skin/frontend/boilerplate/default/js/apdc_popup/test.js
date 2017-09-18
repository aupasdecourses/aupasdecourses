jQuery(document).ready(function() {
  apdcPopupTest = new ApdcPopup({
    id:'test',
    getTemplate:true,
    onReady: function() {
      alert('popup test is ready');
      jQuery('#popup-test-cancel').on('click', function() {
        apdcPopupTest.close();
      });

      jQuery('#popup-test-ok').on('click', function() {
        alert('Moi aussi j\'aime bien');
        apdcPopupTest.close();
      });
    }
  });

  jQuery('#popup-button-test').on('click', function() {
    apdcPopupTest.hideLoading();
    apdcPopupTest.show();
  });
});
