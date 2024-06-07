$(document).ready(function(){

    toastr.options = {
        "closeButton": true,
        "positionClass": "toast-top-right",
        "progressBar": true,
        "timeOut": "5000"
    };

    $('.Cuerpo').richText({
                
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
    });

    $(document).on('click', '.crear', function(e){
        e.preventDefault();

        if($('#form-create').valid() && confirm("¿Está seguro que desea crear el modelo de mensaje?")) {

            let data = {
                Nombre: $('#Nombre').val(),
                Asunto: $('#Asunto').val(),
                Cuerpo: $('.Cuerpo').val(),
                _token: TOKEN
            };

            preloader('on');
            $.post(crearModelo, data)
                .done(function(response){
                    preloader('off');
                    toastr.success(response.msg);
                    $('#form-create').trigger('reset');
                    $('.Cuerpo').html('');
                    setTimeout(() => {
                        window.location.href = listadoModelo;
                    }, 2000);
                })
                .fail(function(jqXHR){
                    preloader('off');
                    if(jqXHR.status === 403){
                        toastr.warning(jqXHR.responseJSON.msg);
                        return;
                    
                    }else if(jqXHR.status === 500){
                        toastr.warning(jqXHR.responseJSON.msg);
                        return;
                    }else{
                        toastr.error("Error: Consulte con el administrador");
                        console.error(jqXHR);
                    }   
                });
        }
    });

    function preloader(opcion) {
        $('#preloader').css({
            opacity: '0.3',
            visibility: opcion === 'on' ? 'visible' : 'hidden'
        });
    }

});