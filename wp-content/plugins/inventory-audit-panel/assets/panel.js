jQuery(document).ready(function ($) {
    const $root = $('#inventory-audit-root');

    // Plantilla HTML con tus columnas
    const template = `
        <div class="card header">
            <div class="title">Historial de Movimientos de Inventario</div>
            <div class="filters">
                <label>Desde: <input type="date" id="from"></label>
                <label>Hasta: <input type="date" id="to"></label>
                <label>ID Producto: <input type="text" id="product" placeholder="Opcional"></label>
                <button class="btn-primary" id="filter">Filtrar</button>
            </div>
        </div>
        <div class="card">
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Producto</th>
                            <th>Stock Anterior</th>
                            <th>Stock Nuevo</th>
                            <th>Diferencia</th>
                            <th>Fuente</th>
                            <th>Nota</th>
                            <th>Fecha</th>
                        </tr>
                    </thead>
                    <tbody id="logs-body"></tbody>
                </table>
            </div>
            <div class="mobile-card" id="mobile-logs"></div>
        </div>
    `;

    $root.html(template);

    function loadLogs(params = {}) {
        $('#logs-body').html(`<tr><td colspan="8">Cargando...</td></tr>`);
        $('#mobile-logs').html(`<div class="card-item">Cargando...</div>`);

        $.ajax({
            url: InventoryAudit.api_url, // tu URL de la API
            data: params,
            method: 'GET',
            success: function (data) {
                if (!data || data.length === 0) {
                    $('#logs-body').html(`<tr><td colspan="8">Sin resultados</td></tr>`);
                    $('#mobile-logs').html(`<div class="card-item">Sin resultados</div>`);
                    return;
                }

                // Generar filas de tabla
                const rows = data.map(item => `
                    <tr>
                        <td>${item.id}</td>
                        <td>${item.product_name}</td>
                        <td>${item.old_stock}</td>
                        <td>${item.new_stock}</td>
                        <td>${item.delta}</td>
                        <td>${item.source || ''}</td>
                        <td>${item.note || ''}</td>
                        <td>${item.created_at}</td>
                    </tr>
                `).join('');
                $('#logs-body').html(rows);

                // Generar cards para móvil
                const cards = data.map(item => `
                    <div class="card-item">
                        <div class="mobile-row"><strong>ID:</strong> ${item.id}</div>
                        <div class="mobile-row"><strong>Producto:</strong> ${item.product_name}</div>
                        <div class="mobile-row"><strong>Stock Anterior:</strong> ${item.old_stock}</div>
                        <div class="mobile-row"><strong>Stock Nuevo:</strong> ${item.new_stock}</div>
                        <div class="mobile-row"><strong>Diferencia:</strong> ${item.delta}</div>
                        <div class="mobile-row"><strong>Fuente:</strong> ${item.source || ''}</div>
                        <div class="mobile-row"><strong>Nota:</strong> ${item.note || ''}</div>
                        <div class="mobile-row"><strong>Fecha:</strong> ${item.created_at}</div>
                    </div>
                `).join('');
                $('#mobile-logs').html(cards);
            },
            error: function () {
                $('#logs-body').html(`<tr><td colspan="8">Error al cargar datos</td></tr>`);
                $('#mobile-logs').html(`<div class="card-item">Error al cargar datos</div>`);
            }
        });
    }

    // Filtro
    $('#filter').on('click', function () {
        const params = {
            from: $('#from').val(),
            to: $('#to').val(),
            product_id: $('#product').val().trim(),
        };
        loadLogs(params);
    });

    // Carga inicial
    loadLogs();
});
