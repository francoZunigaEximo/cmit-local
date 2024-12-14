$(document).ready(function(){

    checkProvincia();

    $('#Foto').on('change', function (e) {
        let foto = e.target.files[0];

        let mensaje = {
            'size': 'El tamaño de la imagen de la firma no puede superar los 1MB',
            'tipo': 'La firma debe ser un archivo de imagen de formato JPG, PNG o GIF',
        }

        let contenido = `
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <span></span>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        `;

        if (foto && foto !== '') {
  
            if (!foto.type.startsWith('image/')) {
      
                $('#messageBasico').append(contenido).find('span').text(mensaje['tipo']);
                
                $(this).val('');
                $('#vistaPrevia').css('display', 'none');
                $('.custom-file-label[for="Foto"]').text('Selecciona o arrastra una imagen aquí');
                return;
            }

            if (foto.size > 1024000) {

                $('#messageBasico').append(contenido).find('span').text(mensaje['size']);

                $(this).val('');
                $('#vistaPrevia').css('display', 'none');
                $('.custom-file-label[for="Foto"]').text('Selecciona o arrastra una imagen aquí');
                return;
            }

            var lector = new FileReader();
            lector.onload = function (e) {
                $('#vistaPrevia').attr('src', e.target.result);
                $('#vistaPrevia').css('display', 'block');
            };
            lector.readAsDataURL(foto);
        }
    });

    $('.Firma').richText({

        // text formatting
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
        height: 0,
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
      
      });

    // Evento cuando se selecciona un archivo
    $('#Foto').on('change', function (e) {
        var archivo = e.target.files[0];
        cargarVistaPrevia(archivo);
    });

    // Eventos para arrastrar archivos
    $('label[for="Foto"]').on({
        dragover: function (e) {
            e.preventDefault();
            $(this).addClass('hover');
        },
        dragleave: function (e) {
            e.preventDefault();
            $(this).removeClass('hover');
        },
        drop: function (e) {
            e.preventDefault();
            $(this).removeClass('hover');
            let archivo = e.originalEvent.dataTransfer.files[0];
            cargarVistaPrevia(archivo);
        }
    });

    //Vista previa del sello y firma
    $('.previsualizar').click(function () {

        let imagenSrc = $('#vistaPrevia').attr('src'),
            selloText = $('#Firma').val();

        $('#imagenModal').attr('src', imagenSrc);
        $('#selloModal').html(selloText);

        $('#previsualizarModal').modal('show');
    });


    $('#provincia').change(function() {
        let provincia = $(this).val();

        $.ajax({
            url: getLocalidades,
            type: "GET",
            data: {
                provincia: provincia,
            },
            success: function(response) {
                let localidades = response.localidades;

                $('#localidad').empty().append('<option selected>Elija una opción...</option>');

                localidades.forEach(function(localidad) {
                    $('#localidad').append('<option value="' + localidad.id + '">' + localidad.nombre + '</option>');
                });
            }
        });
    });

    $('#localidad').change(function() {
        var localidadId = $(this).val();

        // Realizar la solicitud Ajax
        $.ajax({
            url: getCodigoPostal,
            type: "GET",
            data: {
                localidadId: localidadId,
            },
            success: function(response) {
                // Actualizar el valor del input de Código Postal
                $('#codigoPostal').val(response.codigoPostal);
            }
        });
    });

    function checkProvincia(){

        let provincia = $('#provincia').val();
        let localidad = $('#localidad').val();

        if (provincia === 0)
        {
            $.ajax({
                url: checkP,
                type: 'GET',
                data: {
                    localidad: localidad,
                },
                success: function(response){
                    
                    let provinciaNombre = response.fillProvincia;
                     
                    let nuevoOption = $('<option>', {
                        value: provinciaNombre,
                        text: provinciaNombre,
                        selected: true,
                    });

                    $('#provincia').append(nuevoOption);
                },
                error: function(xhr){
                    swal('Error', 'No se pudo autocompletar la provincia. Debe cargarlo manualmente.', 'error');
                    console.error(xhr);
                }
            });
        }
    }

    // Función para cargar la vista previa de la imagen
    function cargarVistaPrevia(archivo) {
        if (archivo) {
            let lector = new FileReader();
            lector.onload = function (e) {
                $('#vistaPrevia').attr('src', e.target.result);
                $('#vistaPrevia').css('display', 'block');
            };
            lector.readAsDataURL(archivo);
        }
    }

    

});