let remainingSpins = availableSpins;

const slots = [
    document.getElementById('slot1'),
    document.getElementById('slot2'),
    document.getElementById('slot3'),
    document.getElementById('slot4'),
    document.getElementById('slot5')
];
const spinButton = document.getElementById('spin-button');
const rewardMessage = document.getElementById('reward-message');

spinButton.addEventListener('click', async () => {
    if (remainingSpins <= 0) {
        rewardMessage.textContent = 'No spins available. Make a purchase to earn spins!';
        spinButton.disabled = true;
        return;
    }

    // Update spin count in database before starting the spin animation
    try {
        const response = await fetch('update_spins.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'action=use_spin'
        });
        
        const data = await response.json();
        
        if (data.error) {
            rewardMessage.textContent = data.error;
            return;
        }

        remainingSpins = data.remaining_spins;
        document.querySelector('p').textContent = `Available Spins: ${remainingSpins}`;

        // Start the spinning animation
        rewardMessage.textContent = 'Spinning...';
        spinButton.disabled = true;

        let spins = 15; // Number of spins (flashes)
        const spinInterval = setInterval(() => {
            slots.forEach(slot => {
                const randomIndex = Math.floor(Math.random() * rewards.length);
                slot.style.backgroundImage = `url(${rewards[randomIndex]})`;
            });
            spins--;

            if (spins === 0) {
                clearInterval(spinInterval);

                // Select the final reward
                const finalIndex = Math.floor(Math.random() * rewards.length);
                slots.forEach(slot => {
                    slot.style.backgroundImage = `url(${rewards[finalIndex]})`;
                });

                rewardMessage.textContent = `${rewardNames[finalIndex]}`;
                
                // Save the reward
                fetch('save_reward.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `reward=${encodeURIComponent(rewardNames[finalIndex])}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.spin_again) {
                        remainingSpins++; // Add back the spin if it's a "spin again" result
                        document.querySelector('p').textContent = `Available Spins: ${remainingSpins}`;
                    }
                    spinButton.disabled = remainingSpins <= 0;
                })
                .catch(error => {
                    console.error('Error saving reward:', error);
                });
            }
        }, 100);

    } catch (error) {
        console.error('Error:', error);
        rewardMessage.textContent = 'An error occurred. Please try again.';
        spinButton.disabled = false;
    }
});