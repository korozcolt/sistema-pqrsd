<div class="faq-form ptb-100">
    <div class="section-title">
        <span>PQR's</span>
        <h2>Verifica el Estado de tu PQRS</h2>
    </div>
    <form id="verifyStatusForm">
        @csrf
        <div class="row">
            <div class="col-lg-12">
                <div class="form-group">
                    <label>Radicado:</label>
                    <input type="text" class="form-control" name="id" id="id" placeholder="Número de Radicado">
                </div>
            </div>

            <div class="text-center">
                <button type="submit" class="faq-form-btn">Consultar</button>
            </div>

        </div>
    </form>

    <div id="status_pqr" class="mt-4">

    </div>

</div>
