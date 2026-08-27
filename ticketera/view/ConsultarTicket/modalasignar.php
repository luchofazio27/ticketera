<div id="modalasignar" class="modal fade bd-example-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="modal-close" data-dismiss="modal" aria-label="Close">
                    <i class="font-icon-close-2"></i>
                </button>
                <h4 class="modal-title" id="mdltitulo"></h4>
            </div>
            <form method="post" id="ticket_form">
  <div class="modal-body">
    <!-- Campo oculto necesario -->
    <input type="hidden" id="tick_id" name="tick_id">

    <div class="form-group">
      <label for="soporte_sec_id">Sector Soportista</label>
      <select id="soporte_sec_id" name="soporte_sec_id" class="form-control select2" required>
        <option value="">Seleccionar sector</option>
      </select>
    </div>

    <div class="form-group">
      <label for="usu_asig">Asignar a (opcional)</label>
      <select id="usu_asig" name="usu_asig" class="form-control select2">
        <option value="">Seleccionar usuario</option>
      </select>
    </div>
  </div>

  <div class="modal-footer">
    <button type="button" class="btn btn-rounded btn-default" data-dismiss="modal">Cerrar</button>
    <button type="submit" id="btnguardar" name="action" value="add" class="btn btn-rounded btn-primary">Asignar</button>
  </div>
</form>
        </div>
    </div>
</div>