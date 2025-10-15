$(document).ready(function() {

    function showLoading(show) {
        if (show) {
            $(".loading-overlay").fadeIn(200);
        } else {
            $(".loading-overlay").fadeOut(200);
        }
    }

    function loadInventoryLogs(filters = {}) {
        showLoading(true);

        $.ajax({
            url: 'http://127.0.0.1:8000/api/inventory-logs',
            method: 'GET',
            data: filters,
            dataType: 'json',
            success: function(response) {
                const tbody = $('#inventory-log-table tbody');
                tbody.empty();

                if (response.length === 0) {
                    tbody.append('<tr><td colspan="8">No se encontraron registros</td></tr>');
                    return;
                }

                response.forEach(log => {
                    tbody.append(`
                        <tr>
                            <td>${log.id}</td>
                            <td>${log.product_name}</td>
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
                alert('Error al cargar los logs: ' + xhr.statusText);
            },
            complete: function() {
                showLoading(false);
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
            from: $('#from').val(),
            to: $('#to').val()
        };

        loadInventoryLogs(filters);
    });
});
