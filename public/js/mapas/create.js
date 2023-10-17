$(document).ready(()=>{
    
 //checker de Nro Mapa
 $(document).on('change', '#Nro', function(){

    let nro = $(this).val()

    $.get(checkMapa, {Nro: nro })
        .done(function(response){

            if(response){

                let contenido = `
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <strong> Atención </strong> El numero de mapa ya existe en la base de datos. Se ha deshabilitado en botón de guardar.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                
                `;
            $('#messageMapas').html(contenido)
            $('#crearMapa').prop('disabled', true);

            }else{

                $('#crearMapa').prop('disabled', false); 
            }
            
        });
});
   

});