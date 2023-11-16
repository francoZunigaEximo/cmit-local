Webcam.set({
    width: 200,
    height: 140,
    dest_width: 640,
    dest_height: 480,
    image_format: 'jpeg',
    jpeg_quality: 120,
    force_flash: false,
    flip_horiz: true,
    fps: 45
});

let isWebcamActive = false;

function toggleWebcam() {
    let imagePreview = document.getElementById('profile-image-preview');

    if (isWebcamActive) {
        Webcam.reset();
        imagePreview.style.backgroundImage = "url('{{ asset('images/default-image.png') }}')";
    } else {
        Webcam.attach('#profile-image-preview');
    }

    isWebcamActive = !isWebcamActive;
}

function takeSnapshot() {
    Webcam.snap(function (data_uri) {
        let imageTag = document.querySelector('.image-tag');
        let imagePreview = document.getElementById('profile-image-preview');

        imageTag.value = data_uri;
        imagePreview.style.backgroundImage = "url('" + data_uri + "')";
        imagePreview.style.backgroundSize = 'cover';
        imagePreview.style.backgroundPosition = 'center';

        // Desactivar la webcam después de tomar la foto
        Webcam.reset();
        isWebcamActive = false;
    });

}