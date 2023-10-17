$(document).ready(()=> {

    new DataTable("#buttons-datatables", {
        searching: false,
        lengthChange: false,
        
        language: {
            emptyTable: "No hay pacientes con los datos buscados",
            paginate: {
                first: "Primera",
                previous: "Anterior",
                next: "Siguiente",
                last: "Última"
            },
            aria: {
                paginate: {
                    first: "Primera",
                    previous: "Anterior",
                    next: "Siguiente",
                    last: "Última"
                }
            },
            info: "Mostrando _START_ a _END_ de _TOTAL_ entradas",
        }
    });
    

});

