//Function use to move the panel 
//open : content panel is open
//Default: content panel is closed
//translation rigth -> left (left -> right)

var open = false;

function translationX(){
    var env = findBootstrapEnvironment()
    console.log(env)
    if(open){
        if(env == 'sm' || env == 'xs') {
            $('.reports-content').removeClass('col-sm-12');
            $('.reports-content').addClass('col-sm-6');
            $('.reports-content').addClass('col-sm-offset-6');
            $('.reports-content')[0].style.display = "block";
        } else {
            $('.reports-content').removeClass('transformationX');
            $('.reports-content').removeClass('col-md-12');
            $('.reports-content').addClass('col-md-9');
            $('.reports-content').removeClass('col-md-offset-6');
            $('.reports-content').addClass('col-md-offset-3');
        }
        open = false;
    } else {

        if(env == 'sm' ){
            $('.reports-content').removeClass('col-sm-6');
            $('.reports-content').removeClass('col-sm-offset-6');
            $('.reports-content').addClass('col-sm-12');
        } else if(env == 'xs'){
            $('.reports-content')[0].style.display = "none";
            $('.reports-content').removeClass('col-sm-offset-6');
        } else {
            $('.reports-content').addClass('transformationX');
            $('.reports-content').removeClass('col-md-9');
            $('.reports-content').addClass('col-md-12');
            $('.reports-content').removeClass('col-md-offset-3');
            $('.reports-content').addClass('col-md-offset-6');
        }

        open = true;
    }
}
function findBootstrapEnvironment() {
    var envs = ['xs', 'sm', 'md', 'lg'];

    var $el = $('<div>');
    $el.appendTo($('body'));

    for (var i = envs.length - 1; i >= 0; i--) {
        var env = envs[i];

        $el.addClass('hidden-'+env);
        if ($el.is(':hidden')) {
            $el.remove();
            return env;
        }
    }
}