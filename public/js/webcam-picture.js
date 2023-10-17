Webcam.set({
    width: 188,
    height: 200,
    dest_width: 640,
    dest_height: 480,
    image_format: 'jpeg',
    jpeg_quality: 120,
    force_flash: false,
    flip_horiz: true,
    fps: 45
});

var isWebcamActive = false;

function toggleWebcam() {
    var webcamButton = document.getElementById('toggle-webcam-button');
    var imagePreview = document.getElementById('profile-image-preview');

    if (isWebcamActive) {
        // Desactivar la webcam
        Webcam.reset();
        webcamButton.value = 'Activar Webcam';
        imagePreview.style.backgroundImage = "url('{{ asset('images/default-image.png') }}')";
    } else {
        // Activar la webcam
        Webcam.attach('#profile-image-preview');
        webcamButton.value = 'Desactivar Webcam';
    }

    isWebcamActive = !isWebcamActive;
}

function takeSnapshot() {
    Webcam.snap(function (data_uri) {
        var imageTag = document.querySelector('.image-tag');
        var imagePreview = document.getElementById('profile-image-preview');

        imageTag.value = data_uri;
        imagePreview.style.backgroundImage = "url('" + data_uri + "')";
        imagePreview.style.backgroundSize = 'cover';
        imagePreview.style.backgroundPosition = 'center';

        // Desactivar la webcam después de tomar la foto
        Webcam.reset();
        document.getElementById('toggle-webcam-button').value = 'Activar Webcam';
        isWebcamActive = false;
    });
}