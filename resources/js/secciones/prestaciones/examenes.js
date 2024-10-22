import {preloader} from './../../modules/basicos';
import {checkError} from './../../modules/errores';

let idExamen = [];

const valAbrir = [3, 4, 5], valCerrar = [0, 1, 2], valCerrarI = 3;

document.addEventListener('DOMContentLoaded', () => {
    document.addEventListener('click', (e) => {
        if (e.target.classList.contains('addPaquete')) {
            e.preventDefault();
            
            const paqueteSelect = document.getElementById('paquetes');
            const paquete = paqueteSelect.value;
            
            if ([null, undefined, ''].includes(paquete)) {
                toastr.warning("Debe seleccionar un paquete para poder añadirlo en su totalidad");
                return;
            }

            preloader('on');
            fetch(paqueteId, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-Token': TOKEN 
                },
                body: JSON.stringify({
                    IdPaquete: paquete
                })
            })
            .then(response => {
                preloader('off');
                if (!response.ok) {
                    return response.json().then(errorData => {
                        const msg = errorData.msg || 'Ha ocurrido un error';
                        checkError(response.status, msg);
                    });
                }
                return response.json();
            })
            .then(data => {
                let ids = data.examenes.map(item => item.Id);
                saveExamen(ids);
                paqueteSelect.value = ''; // Limpiar el select
            })
            .catch(error => {
                preloader('off');

                console.error('Error:', error);
                checkError(error.message, "Error en la solicitud");
            });
        }
    });
});

document.addEventListener('DOMContentLoaded', () => {
    document.addEventListener('click', (e) => {
        if (e.target.classList.contains('deleteExamenes') || e.target.classList.contains('deleteExamen')) {
            e.preventDefault();

            let ids = [], tieneAdjunto = false, id = e.target.dataset.delete, adjunto, archivos, checkAll; 

            if (e.target.classList.contains('deleteExamenes')) {
                const inputsSeleccionados = document.querySelectorAll('input[name="Id_examenes"]:checked');

                // Itera sobre cada input seleccionado
                inputsSeleccionados.forEach(function(input) {e
                    adjunto = input.dataset.adjunto; 
                    archivos = input.dataset.archivo;


                    if (adjunto == 1 && archivos > 0) {
                        tieneAdjunto = true;
                    } else {
                        ids.push(id);
                    }

                    const checkAllInput = document.getElementById('checkAllExamenes');
                    checkAll = checkAllInput ? checkAllInput.checked : false;
                });
            
            } else if(e.target.classList.contains('deleteExamen')) {
                adjunto = input.dataset.adjunto;
                adjunto === 1 && archivos > 0 ? tieneAdjunto = true : ids.push(input.value);
            }

            if (tieneAdjunto) {
                toastr.warning('El o los examenes seleccionados tienen un reporte adjuntado. El mismo no se podrá eliminar.');
                return;
            }
    
            if(ids.length === 0 && checkAll === false){
                toastr.warning('No hay examenes seleccionados');
                return;
            }  
        
            swal({
                title: "Confirme la eliminación de los examenes",
                icon: "warning",
                buttons: ["Cancelar", "Eliminar"],
            }).then((confirmar) => {
                if (confirmar){
                    
                    preloader('on');
                    fetch(deleteItemExamen, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSTF-Token': TOKEN
                        },
                        body: JSON.stringify({
                            Id: ids,
                        })
                    })
                    .then(response => {
                        preloader('off')

                        if(!response.ok) {
                            return response.json().then(errorData => {
                                checkError(response.status, errorData.msg)
                            })
                        }

                        return response.json();
                    })
                    .then(data => {
                        let estados = [];

                        preloader('off')
                        data.forEach(function(msg) {

                            let tipoRespuesta = {
                                success: 'success',
                                fail: 'info'
                            }
                            
                            toastr[tipoRespuesta[msg.estado]](msg.message, "Atención", { timeOut: 10000 })
                            estados.push(msg.estado)

                            if(estados.includes('success')) {
                                document.getElementById('listaExamenes').innerHTML = '';
                                $('#exam').val([]).trigger('change.select2');
                                $('#addPaquete').val([]).trigger('change.select2');
                                cargarExamen();
                            }
                            
                        });

                    })
                }
            });


        }
     });
});