(function($) {
  $(document).ready(function() {
    $('.apdc-edit-comment, .apdc-add-comment').on('click', function(e) {
      e.preventDefault();
      e.stopPropagation();
      var itemId = $(this).data('item-id');
      var orderId = parseInt($(this).data('order-id'), 10);
      var eltComment = $(this).parents('td').find('#apdc-comment-' + itemId);
      var comment = '';
      if (eltComment.length > 0) {
        comment = $(this).parents('td').find('#apdc-comment-' + itemId).html();
      }
      apdcManageItemComment(itemId, comment, orderId);
      return false;
    });
  });
  $(document).on('apdcUpdateComment', function(event, result) {
    if (result.status === 'SUCCESS') {
      jQuery('#apdc-comment-' + result.item_id).html(result.comment);
      if (result.comment !== '') {
        jQuery('#apdc-add-comment-container-' + result.item_id).hide();
        jQuery('#apdc-edit-comment-container-' + result.item_id).show();
      } else {
        jQuery('#apdc-add-comment-container-' + result.item_id).show();
        jQuery('#apdc-edit-comment-container-' + result.item_id).hide();
      }
    }
  });
})(jQuery);
