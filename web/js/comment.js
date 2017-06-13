$(document).ready(function(){

    $('.reply').click(function(e) {
        e.preventDefault();
        var $form = $('#form-comment');
        var $this = $(this);
        var parent_id = $this.attr('data-id');
        var $comment = $('#comment-' + parent_id);

        console.log($form)
        console.log($this) 
        console.log(parent_id)
        console.log($comment)
        
        $form.find('h4').text('Répondre à ce commentaire');
        $('#comment_parent_id').val(parent_id);
        $comment.after($form);
    })

});