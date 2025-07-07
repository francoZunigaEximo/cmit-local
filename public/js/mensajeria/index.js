$(function() {

    let atributos = {
                
        bold: true,
        italic: true,
        underline: true,
      
        // text alignment
        leftAlign: true,
        centerAlign: true,
        rightAlign: true,
        justify: true,
      
        // lists
        ol: false,
        ul: false,
      
        // title
        heading: false,
      
        // fonts
        fonts: false,
        fontList: ["Arial",
          "Arial Black",
          "Comic Sans MS",
          "Courier New",
          "Geneva",
          "Georgia",
          "Helvetica",
          "Impact",
          "Lucida Console",
          "Tahoma",
          "Times New Roman",
          "Verdana"
        ],
        fontColor: true,
        backgroundColor: false,
        fontSize: true,
      
        // uploads
        imageUpload: false,
        fileUpload: false,
      
        // link
        urls: false,
      
        // tables
        table: false,
      
        // code
        videoEmbed: false,
        removeStyles: true,
        code: false,
      
        // colors
        colors: [],
      
        // dropdowns
        fileHTML: '',
        imageHTML: '',
      
        // translations
        translations: {
          'title': 'Title',
          'white': 'White',
          'black': 'Black',
          'brown': 'Brown',
          'beige': 'Beige',
          'darkBlue': 'Dark Blue',
          'blue': 'Blue',
          'lightBlue': 'Light Blue',
          'darkRed': 'Dark Red',
          'red': 'Red',
          'darkGreen': 'Dark Green',
          'green': 'Green',
          'purple': 'Purple',
          'darkTurquois': 'Dark Turquois',
          'turquois': 'Turquois',
          'darkOrange': 'Dark Orange',
          'orange': 'Orange',
          'yellow': 'Yellow',
          'imageURL': 'Image URL',
          'fileURL': 'File URL',
          'linkText': 'Link text',
          'url': 'URL',
          'size': 'Size',
          'responsive': '<a href="https://www.jqueryscript.net/tags.php?/Responsive/">Responsive</a>',
          'text': 'Text',
          'openIn': 'Open in',
          'sameTab': 'Same tab',
          'newTab': 'New tab',
          'align': 'Align',
          'left': 'Left',
          'justify': 'Justificado',
          'center': 'Center',
          'right': 'Right',
          'rows': 'Rows',
          'columns': 'Columns',
          'add': 'Add',
          'pleaseEnterURL': 'Please enter an URL',
          'videoURLnotSupported': 'Video URL not supported',
          'pleaseSelectImage': 'Please select an image',
          'pleaseSelectFile': 'Please select a file',
          'bold': 'Negrita',
          'italic': 'Cursiva',
          'underline': 'Subrayado',
          'alignLeft': 'Alineción izquierda',
          'alignCenter': 'Alineación central',
          'alignRight': 'Alineación derecha',
          'addOrderedList': 'Ordered list',
          'addUnorderedList': 'Unordered list',
          'addHeading': 'Heading/title',
          'addFont': 'Font',
          'addFontColor': 'Font color',
          'addBackgroundColor': 'Background color',
          'addFontSize': 'Font size',
          'addImage': 'Add image',
          'addVideo': 'Add video',
          'addFile': 'Add file',
          'addURL': 'Add URL',
          'addTable': 'Add table',
          'removeStyles': 'Quitar estilos del texto',
          'code': 'Show HTML code',
          'undo': 'Undo',
          'redo': 'Redo',
          'save': 'Save',
          'close': 'Close'
        },
      
        // privacy
        youtubeCookies: false,
      
        // preview
        preview: false,
      
        // placeholder
        placeholder: '',
      
        // dev settings
        useSingleQuotes: false,
        height: 5,
        heightPercentage: 0,
        adaptiveHeight: false,
        id: "",
        class: "",
        useParagraph: false,
        maxlength: 0,
        maxlengthIncludeHTML: false,
        callback: undefined,
        useTabForNext: false,
        save: false,
        saveCallback: undefined,
        saveOnBlur: 0,
        undoRedo: true
    };

    $('.Cuerpo').richText(atributos);
    $('.Cuerpo2').richText(atributos);

    $(document).on('click', '.enviar', function(e){
        e.preventDefault();

        let facturas = $('#facturas').prop('checked'),
            masivos = $('#masivos').prop('checked'),
            informes = $('#informes').prop('checked');
        
        if(![facturas,masivos,informes].some(validar => validar === true)) {
            toastr.warning("Debe seleccionar algunas de las opciones de envio", '', {timeOut: 1000});
            return;
        }
        
        let ids = [];
        $('input[type="checkbox"][name="Id_masivo"]:checked').each(function() {
            ids.push($(this).val());
        });

        if(ids.length === 0) {
            toastr.warning("No se ha seleccionado ningún cliente", '', {timeOut: 1000});
            return;
        }

        localStorage.setItem('facturas', facturas);
        localStorage.setItem('masivos', masivos);
        localStorage.setItem('informes', informes);
        localStorage.setItem('ids', JSON.stringify(ids));

        $('#modalEnviar').modal('show');
        cargaModelos();
    });

    $(document).on('click', '.enviarMensaje', function(e) {
        e.preventDefault();

        let data = {
            Asunto: $('#Asunto').val(),
            Cuerpo: $('.Cuerpo').val(),
            Facturas: localStorage.getItem('facturas'),
            Masivos: localStorage.getItem('masivos'),
            Informes: localStorage.getItem('informes'),
            Id: JSON.parse(localStorage.getItem('ids')),
        };

        localStorage.removeItem('facturas');
        localStorage.removeItem('masivos');
        localStorage.removeItem('informes');
        localStorage.removeItem('ids');

        swal({
            title: "¿Está seguro que desea enviar los emails solicitados?",
            icon: "warning",
            buttons: ["Cancelar", "Aceptar"]
        }).then((confirmar) => {
            if(confirmar){

                preloader('on');
                $.get(sendEmails, data)
                    .done(function(response){
                        preloader('off');

                        toastr.success(response.msg, '', {timeOut: 1000});
                    })
                    .fail(function(jqXHR){
                        preloader('off');            
                        let errorData = JSON.parse(jqXHR.responseText);            
                        checkError(jqXHR.status, errorData.msg);
                        return;
                    });

            }
        });

            
        
    });

    $(document).on('change', '#modelo, #modelo2', function() {
        let id = $(this).val();
        cargarMensaje(id);
    });

    $(document).on('click', '.editar', function(e) {
        e.preventDefault();

        let id = $(this).data('id');

        if(!id) {
            toastr.warning("No se ha seleccionado ningún registro", '', {timeOut: 1000});
            return;
        }

        window.location.href = window.location.href + '/' + id + '/edit/';
    });

    $('#checkAllMasivo').on('click', function() {
        $('input[type="checkbox"][name="Id_masivo"]:not(#checkAllMasivo)').prop('checked', this.checked);
    });

    $(document).on('click', '.envioIndivivual', function(e){
        cargaModelos();
        $('#Id2').val($(this).data('id'));
    });

    $(document).on('click', '.enviarMensajeInd', function() {
        
        let data = {
            Id: $('#Id2').val(),
            Asunto: $('#Asunto2').val(),
            Cuerpo: $('.Cuerpo2').val(),
            Facturas: $('#facturas2').prop('checked'),
            Masivos: $('#masivos2').prop('checked'),
            Informes: $('#informes2').prop('checked')
        };
        let modelo = $('#modelo2').val();

        if(![data.Facturas, data.Masivos, data.Informes].some(validar => validar === true)) {
            toastr.warning("Debe seleccionar alguna de las opciones de envio", '', {timeOut: 1000});
            return;
        }
        
        swal({
            title: "¿Está seguro que desea enviar los emails solicitados?",
            icon: "warning",
            buttons: ["Cancelar", "Aceptar"]
        }).then((confirmar) => {
            if(confirmar) {
                preloader('on');
                $.get(sendEmails, data)
                    .done(function(response){
                        preloader('off');
                        toastr.success(response.msg, '', {timeOut: 1000});
                    })
                    .fail(function(jqXHR){
                        preloader('off');
                        let errorData = JSON.parse(jqXHR.responseText);            
                        checkError(jqXHR.status, errorData.msg);
                        return;
                    });
            }
        });

            
    });

    $(document).on('click', '.auditoria', function() {
    
        window.location.href = window.location.href + '/auditoria';
    });

    $(document).on('click', '.modelos', function() {
        window.location.href = window.location.href + '/modelos';
    });

    $(document).on('click', '.Testear', function(e){
        e.preventDefault();
        preloader('on');
        $.get(testEmail)
        .done(function(response){
            preloader('off');
            alert(response.msg);
        })
        .fail(function(jqXHR){
            preloader('off');
            let errorData = JSON.parse(jqXHR.responseText);            
            //checkError(jqXHR.status, errorData.msg);
            alert(errorData.msg);
            return;
        });
    });

    function cargaModelos() { 
        $("#modelo, #modelo2").empty();

        $.get(loadModelos)
        .done(function(response){
            let html = '<option value="" selected>Elija una opción...</option>';
            response.forEach(modelo => {
                html += `<option value="${modelo.Id}">${modelo.Nombre}</option>`;
            });

            $('#modelo, #modelo2').html(html);
        })
    }

    function cargarMensaje(id) {
        preloader('on');
        $.get(loadMensaje, {Id: id})
        .done(function(response){
            preloader('off');
            $('#Asunto, #Asunto2').empty().val(response.Asunto);
            $('.richText-editor').trigger('clear');
            $('.richText-editor').trigger('setContent', response.Cuerpo);
        })
    }

});