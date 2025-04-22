<style>
.negrita_verde{
    font-weight: bold;
    color: #0f9d58;
}
</style>

<button type="button" class="btn btn-ligth" data-bs-toggle="modal" data-bs-target="#modalHelper">
    <span class="material-symbols-outlined">
        help
    </span>
</button>

<!-- Modal -->
<div class="modal fade" id="modalHelper" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Ayuda</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                {!! $slot !!}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>