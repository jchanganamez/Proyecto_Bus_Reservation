let selectedSeats = new Set();

function fetchSeats(busId) {
    if (!busId) return;
    fetch(`get_asientos.php?bus_id=${busId}`)
        .then(response => response.text())
        .then(data => {
            document.getElementById('asientos').innerHTML = data;
            openModal();
        })
        .catch(error => console.error('Error al cargar los asientos:', error));
}

function openModal() {
    document.getElementById('asientosModal').classList.remove('hidden');
}

function closeModal() {
    document.getElementById('asientosModal').classList.add('hidden');
}

document.getElementById('asientos').addEventListener('click', e => {
    if (e.target.classList.contains('seat-available')) {
        const seatId = e.target.dataset.seatId;
        if (selectedSeats.has(seatId)) {
            selectedSeats.delete(seatId);
            e.target.classList.remove('bg-green-500', 'text-white');
        } else {
            selectedSeats.add(seatId);
            e.target.classList.add('bg-green-500', 'text-white');
        }
    }
});

document.getElementById('confirmSeats').addEventListener('click', () => {
    document.getElementById('asientos_seleccionados').value = Array.from(selectedSeats).join(',');
    closeModal();
});


