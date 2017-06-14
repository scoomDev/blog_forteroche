$(document).ready(function(){

    $('.reply').click(function(e) {
        e.preventDefault();
        var $form = $('#form-comment');
        var $this = $(this);
        var parent_id = $this.attr('data-id');
        var $comment = $('#comment-' + parent_id);
        var dataDepth = $comment.attr('data-depth');
        var $depth = $('#comment_depth');

        if (dataDepth == 0) {
            $depth.val(1);
        } else {
            $depth.val(2);
        }

        $form.find('h4').text('Répondre à ce commentaire');
        $('#comment_parent_id').val(parent_id);
        $comment.after($form);
    })

});