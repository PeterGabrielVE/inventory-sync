<?php
/**
 * Plugin Name: Panel de Auditoría de Inventario
 * Description: Agrega un panel responsive al administrador para visualizar logs de inventario (Fase 3 - Opción B).
 * Version: 1.0
 * Author: Tu Nombre
 */

// Bloquear acceso directo
if ( !defined('ABSPATH') ) exit;

/**
 * Registrar menú en el admin
 */
add_action('admin_menu', function() {
    add_menu_page(
        'Auditoría de Inventario',       // Título de la página
        'Auditoría Inventario',          // Nombre en el menú
        'manage_options',                // Capacidad requerida
        'inventory-audit-panel',         // slug
        'render_inventory_audit_page',   // callback
        'dashicons-analytics',           // ícono
        26                               // posición
    );
});

/**
 * Cargar scripts y estilos solo en nuestra página
 */
add_action('admin_enqueue_scripts', function($hook) {
    if ($hook !== 'toplevel_page_inventory-audit-panel') return;
    wp_enqueue_style('inventory-panel-css', plugin_dir_url(__FILE__) . 'assets/panel.css', [], '1.0');
    wp_enqueue_script('jquery');
    wp_enqueue_script('inventory-panel-js', plugin_dir_url(__FILE__) . 'assets/panel.js', ['jquery'], '1.0', true);

    // Pasar URL del endpoint a JS
    wp_localize_script('inventory-panel-js', 'InventoryAudit', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'api_url' => 'http://localhost:8000/api/inventory-logs',
        'nonce' => wp_create_nonce('inventory_audit_nonce')
    ]);
});

/**
 * Renderizar la página dentro de WP Admin
 */
function render_inventory_audit_page() {
    ?>
    <div class="wrap">
        <h1>📊 Panel de Auditoría de Inventario</h1>
        <div id="inventory-audit-root"></div>
    </div>
    <?php
}

/**
 * Crear un endpoint REST como proxy a la API externa
 */
add_action('rest_api_init', function () {
    register_rest_route('inventory/v1', '/logs', [
        'methods'  => 'GET',
        'callback' => 'inventory_audit_proxy',
        'permission_callback' => function () {
            return current_user_can('manage_options');
        },
    ]);
});

function inventory_audit_proxy(WP_REST_Request $request) {
    $from = sanitize_text_field($request->get_param('from'));
    $to = sanitize_text_field($request->get_param('to'));
    $product = sanitize_text_field($request->get_param('product_id'));

    $api_url = 'http://localhost:8000/api/inventory-logs';

    $args = ['timeout' => 20];

    $query = [];
    if ($from) $query['from'] = $from;
    if ($to) $query['to'] = $to;
    if ($product) $query['product_id'] = $product;

    if (!empty($query)) {
        $api_url .= '?' . http_build_query($query);
    }

    $response = wp_remote_get($api_url, $args);

    if (is_wp_error($response)) {
        return new WP_Error('api_error', 'Error al conectar con Laravel', ['status' => 500]);
    }

    $body = wp_remote_retrieve_body($response);
    return rest_ensure_response(json_decode($body, true));
}
