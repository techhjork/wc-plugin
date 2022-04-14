console.log("hiii")
jQuery(document).ready(function($){
    let search_form = $('#my-search-form');

    search_form.submit(function(event){
        event.preventDefault();
        let search_term = $('#search_term').val();
        let formData = new FormData();
        formData.append('action', 'my_search_func');
        formData.append('search_term', search_term);
        
        $.ajax({
            url: ajaxUrl,
            type: 'post',
            data: formData,
            processData: false,
            contentType: false,
            success: function(responce){
            	console.log(responce)
                $('#my-table-result').html(responce);
            },
            error: function(){
                console.log('error');
            }
        });
    });

});