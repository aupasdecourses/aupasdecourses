if (typeof(apdcItemCommentPopup) === 'undefined') {
  var apdcItemCommentPopup = null;
}
jQuery(document).ready(function() {
  if (apdcItemCommentPopup === null) {
    apdcItemCommentPopup = new ApdcPopup({
      id:'item_comment'
    });
  }
});

var apdcManageItemCommentActionsInited = false;
function apdcManageItemComment(itemId, itemComment, orderId) {
  if (!apdcManageItemCommentActionsInited) {
    apdcManageItemCommentInitActions();
  }
  if (!orderId) {
    orderId = 0;
  }
  orderId = parseInt(orderId, 10);
  if (!itemComment) {
    itemComment = '';
  }
  itemComment = jQuery('<textarea />').html(itemComment).text();
  jQuery('#item-comment-order-id').val(orderId);
  jQuery('#item-comment-id').val(itemId);
  jQuery('#item-comment-textarea').val(itemComment);
  apdcItemCommentPopup.show();
}
function apdcManageItemCommentInitActions() {
  if (jQuery('#save-item-comment').length > 0) {
    apdcManageItemCommentActionsInited = true;
    jQuery('#cancel-item-comment').on('click', function() {
      apdcItemCommentPopup.close();
    });
    jQuery('#save-item-comment').on('click', function() {
      var ajaxUrl = jQuery('#item-comment-ajax-url').val();
      var itemId = jQuery('#item-comment-id').val();
      var comment = jQuery('#item-comment-textarea').val();
      var orderId = parseInt(jQuery('#item-comment-order-id').val(), 10);
      apdcItemCommentPopup.close();
      new Ajax.Request(ajaxUrl, {
          method:'post',
          parameters:{item_id:itemId, item_comment:comment, order_id:orderId},
          onSuccess: function(response) {
            if (orderId > 0) {
              jQuery(document).trigger('apdcUpdateComment', [response.responseJSON]);
            } else {
              order.itemsUpdate();
            }
          }
        }
      );
    });
  }
}
