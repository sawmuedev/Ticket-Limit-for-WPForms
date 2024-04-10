<?php
/*
Plugin Name: Ticket Limit
Plugin URI: 
Description: Limit the number of tickets to a customizable total, dynamically adjust ticket options based on availability, and allow manual adjustment of ticket counts in the backend.
Version: 1.0
Author: Marius Menkel
Author URI: 
*/

add_action('wpforms_process', 'check_ticket_limit', 10, 3);

function check_ticket_limit($fields, $entry, $form_data) {
    $ticket_field_id = '28'; // Replace with your actual ticket field ID
    $ticket_count = absint($fields[$ticket_field_id]['value']);
    $total_tickets_booked = get_option('total_tickets_booked', 0);
    $total_tickets = get_option('total_tickets', 400); // Get the total tickets available from the options table
    $available_tickets = $total_tickets - $total_tickets_booked; // Calculate available tickets

    if (($total_tickets_booked + $ticket_count) > $total_tickets) {
        wpforms()->process->errors[$form_data['id']][$ticket_field_id] = "Es sind nur noch $available_tickets Tickets verfügbar.";
    } else {
        update_option('total_tickets_booked', ($total_tickets_booked + $ticket_count));
    }
}

add_action('admin_menu', 'ticket_limit_admin_menu');

function ticket_limit_admin_menu() {
    add_menu_page('Ticket Limit', 'Ticket Limit', 'manage_options', 'ticket-limit', 'ticket_limit_admin_page', 'dashicons-tickets-alt', 6);
}

function ticket_limit_admin_page() {
    if (isset($_POST['update_ticket_counts'])) {
        check_admin_referer('update_tickets_action', 'update_tickets_nonce');
        update_option('total_tickets', absint($_POST['total_tickets']));
        update_option('total_tickets_booked', absint($_POST['booked_tickets']));
    }

    $total_tickets = get_option('total_tickets', 400);
    $booked_tickets = get_option('total_tickets_booked', 0);
    $available_tickets = $total_tickets - $booked_tickets;

    echo '<div class="wrap"><h1>Ticket Limit Settings</h1><form method="post" action="">';
    wp_nonce_field('update_tickets_action', 'update_tickets_nonce');
    echo '<table class="form-table"><tbody>';
    echo '<tr><th scope="row"><label for="total_tickets">Total Tickets</label></th><td><input type="number" id="total_tickets" name="total_tickets" value="' . esc_attr($total_tickets) . '" /></td></tr>';
    echo '<tr><th scope="row"><label for="booked_tickets">Booked Tickets</label></th><td><input type="number" id="booked_tickets" name="booked_tickets" value="' . esc_attr($booked_tickets) . '" /></td></tr>';
    echo '<tr><th scope="row">Available Tickets</th><td>' . $available_tickets . '</td></tr>';
    echo '</tbody></table>';
    echo '<p class="submit"><input type="submit" name="update_ticket_counts" id="submit" class="button button-primary" value="Save Changes"></p>';
    echo '</form></div>';
}
