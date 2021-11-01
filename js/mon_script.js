$('document').ready(function(){
    // afichage du mdp sur la page connexion
    $('#show-password').on('click',function(){
        var inputType = $('#mdp').attr('type');
        console.log(inputType);

        if(inputType == 'text'){
            $('#mdp').attr('type', 'password');
            $(this).attr('class', 'fas fa-eye-slash');
        } else {
            $('#mdp').attr('type', 'text');
            $('this').attr('class','fas fa-eye');
        }
    });
});
    
