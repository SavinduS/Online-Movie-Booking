
        let selectedSeats = [];
        let totalAmount = 0;

        document.addEventListener('DOMContentLoaded', function() {
            const seatButtons = document.querySelectorAll('.seat-btn:not([disabled])');
            const seatList = document.getElementById('seat-list');
            const totalAmountEl = document.getElementById('total-amount');
            const continueBtn = document.getElementById('continue-btn');
            const selectedSeatsInput = document.getElementById('selected-seats-input');
            const totalAmountInput = document.getElementById('total-amount-input');

            seatButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const seatId = this.dataset.seat;
                    const seatPrice = parseInt(this.dataset.price);
                    const seatRow = this.dataset.row;

                    if (this.classList.contains('bg-primary')) {
                        // Deselect seat
                        this.classList.remove('bg-primary', 'text-white');
                        this.classList.add('bg-gray-200');
                        
                        selectedSeats = selectedSeats.filter(seat => seat.id !== seatId);
                        totalAmount -= seatPrice;
                    } else {
                        // Select seat
                        this.classList.remove('bg-gray-200');
                        this.classList.add('bg-primary', 'text-white');
                        
                        selectedSeats.push({
                            id: seatId,
                            price: seatPrice,
                            row: seatRow
                        });
                        totalAmount += seatPrice;
                    }

                    updateBookingSummary();
                });
            });

            function updateBookingSummary() {
                if (selectedSeats.length === 0) {
                    seatList.innerHTML = '<p class="text-center italic">No seats selected</p>';
                    totalAmountEl.textContent = 'Rs. 0';
                    continueBtn.disabled = true;
                } else {
                    // Group seats by row
                    const seatsByRow = {};
                    selectedSeats.forEach(seat => {
                        if (!seatsByRow[seat.row]) {
                            seatsByRow[seat.row] = [];
                        }
                        seatsByRow[seat.row].push(seat.id);
                    });

                    let seatListHTML = '';
                    Object.keys(seatsByRow).sort().forEach(row => {
                        seatListHTML += `<div class="flex justify-between mb-1">
                            <span>${seatsByRow[row].join(', ')}</span>
                            <span>Rs. ${selectedSeats.filter(s => s.row === row).reduce((sum, s) => sum + s.price, 0)}</span>
                        </div>`;
                    });

                    seatList.innerHTML = seatListHTML;
                    totalAmountEl.textContent = `Rs. ${totalAmount.toLocaleString()}`;
                    continueBtn.disabled = false;
                }

                // Update hidden inputs
                selectedSeatsInput.value = JSON.stringify(selectedSeats);
                totalAmountInput.value = totalAmount;
            }
        });
    