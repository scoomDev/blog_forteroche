$(document).ready(function(){
    $(".dropdown-button").dropdown(); 
    $('select').material_select(); 
    $('ul.tabs').tabs();
    $('ul.tabs').tabs('select_tab', 'tab_id'); 
    $('.modal').modal();
    $('.tooltiped').tooltip();
    $('.button-collapse').sideNav();
    $('.parallax').parallax();
    $('.tap-target').tapTarget('open');
    $('.tap-target').tapTarget('close');

    /* LOADER */
    $(".loader").fadeOut("1000");

    $('.loader_btn').click(function(e) {
        $(".loader").css("display", "flex");
    })
})
