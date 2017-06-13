$(document).ready(function(){
    $(".dropdown-button").dropdown(); 
    $('select').material_select(); 
    $('ul.tabs').tabs();
    $('ul.tabs').tabs('select_tab', 'tab_id'); 
    $('.modal').modal();
    $('.tooltiped').tooltip();
    $('.button-collapse').sideNav();
})