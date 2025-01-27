 let echo = window.Echo.channel('listado-efectores');
    // channel.listen('lstEfectores', (e) => {
    //     console.log(e);
    // });

    console.log("Echo: " + echo);

    echo.listen('ListadoProfesionalesEvent', (response) => {
        console.log("Respuesta cruda:", response);

        const parsedData = JSON.parse(response.data); // Decodifica los datos
        const efectores = parsedData.efectores || [];
        console.log("Efectores: ", efectores);
    });