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
function apdcManageItemComment(itemId, itemComment) {
  if (!apdcManageItemCommentActionsInited) {
    apdcManageItemCommentInitActions();
  }
  if (!itemComment) {
    itemComment = '';
  }
  itemComment = jQuery('<textarea />').html(itemComment).text();
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
      apdcItemCommentPopup.close();
      new Ajax.Request(ajaxUrl, {
          method:'post',
          parameters:{item_id:itemId, item_comment:comment},
          onSuccess: function(response) {
            order.itemsUpdate();
          }
        }
      );
    });
  }
}
