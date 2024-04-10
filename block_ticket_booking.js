document.addEventListener('DOMContentLoaded', function() {
    var submitButton = document.querySelector('[type="submit"]'); // Anpassen nach deinem Formular
    var ticketSlider = document.getElementById('wpforms-123-field_28'); // Anpassen nach deinem Formular

    submitButton.addEventListener('click', function(event) {
        var bookedTickets = parseInt(<?php echo get_option('total_tickets_booked', 0); ?>, 10);
        var totalTickets = parseInt(<?php echo get_option('total_tickets', 400); ?>, 10); // Verwendet die Gesamtanzahl der Tickets aus den WordPress-Einstellungen
        var selectedTickets = parseInt(ticketSlider.value, 10);
        var availableTickets = totalTickets - bookedTickets;

        if (selectedTickets > availableTickets) {
            event.preventDefault(); // Verhindert das Absenden des Formulars
            alert('Es sind nur noch ' + availableTickets + ' Tickets verfügbar.'); // Zeigt eine Warnmeldung an
        }
    });
});
