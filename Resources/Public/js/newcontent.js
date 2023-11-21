/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
function makeCElement(element){
  
}

$(document).ready(function(){
 
 $('.nav-tabs a').on('click',function(e){
    e.preventDefault(); 
    $('.nav-tabs a').removeClass('active');
    $('.tab-pane').hide();
    
    $(this).addClass('active');
    
    var tabindex = $('.nav-tabs a').index(this);
  //  alert(tabindex);
    $('.tab-pane').eq(tabindex).show();
 });
 
 
 $('.nav-tabs a').first().trigger('click');
});