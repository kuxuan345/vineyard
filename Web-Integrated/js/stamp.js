// Get initial stamp state
let stampsUsed = stampData.used || 0;
let stampsAvailable = stampData.available || 0;

// Update visual state on page load
window.onload = function() {
    updateStampDisplay();
};

function updateStampDisplay() {
    // Update stamps visual state
    for (let i = 1; i <= 5; i++) {
        const stamp = document.getElementById(`stamp${i}`);
        if (i <= stampsUsed) {
            stamp.classList.add('stamped');
            stamp.querySelector('i').style.color = '#4CAF50';
        }
    }
    
    // Update status message
    const statusElem = document.getElementById('status');
    if (stampsAvailable > 0) {
        statusElem.textContent = `You have ${stampsAvailable} stamps available`;
    } else {
        statusElem.textContent = 'Purchase more items to earn stamps!';
    }
}

function addStamp() {
    if (stampsAvailable <= 0) {
        document.getElementById('status').textContent = 'No stamps available!';
        return;
    }
    
    const statusElem = document.getElementById('status');
    statusElem.textContent = 'Processing...';

    fetch('stamp_action.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            stampsUsed++;
            stampsAvailable--;
            updateStampDisplay();
            statusElem.textContent = data.message;
            statusElem.style.color = '#4CAF50';
        } else {
            statusElem.textContent = data.message;
            statusElem.style.color = '#dc3545';
            console.error('Stamp error:', data.message);
        }
    })
    .catch(error => {
        statusElem.textContent = 'Error processing stamp. Please try again.';
        statusElem.style.color = '#dc3545';
        console.error('Fetch error:', error);
    });
}