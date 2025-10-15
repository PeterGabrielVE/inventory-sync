$(document).ready(function() {

    function loadInventoryLogs(filters = {}) {
        $.ajax({
            url: '/api/inventory-logs',
            method: 'GET',
            data: filters,
            dataType: 'json',
            success: function(response) {
                const tbody = $('#inventory-log-table tbody');
                tbody.empty();

                if(response.length === 0) {
                    tbody.append('<tr><td colspan="8">No se encontraron registros</td></tr>');
                    return;
                }

                response.forEach(log => {
                    tbody.append(`
                        <tr>
                            <td>${log.id}</td>
                            <td>${log.product_id}</td>
                            <td>${log.old_stock}</td>
                            <td>${log.new_stock}</td>
                            <td>${log.delta}</td>
                            <td>${log.source || ''}</td>
                            <td>${log.note || ''}</td>
                            <td>${log.created_at}</td>
                        </tr>
                    `);
                });
            },
            error: function(xhr) {
                alert('Error al cargar los logs: ' + xhr.responseText);
            }
        });
    }

    // Cargar logs al inicio
    loadInventoryLogs();

    // Filtrar logs
    $('#filter-form').submit(function(e) {
        e.preventDefault();

        const filters = {
            product_id: $('#product_id').val(),
            start_date: $('#start_date').val(),
            end_date: $('#end_date').val()
        };

        loadInventoryLogs(filters);
    });
});
