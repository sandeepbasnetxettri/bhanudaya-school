<?php
// Component for sending push notifications from admin dashboard
?>
<div class="admin-card">
    <h3><i class="fas fa-paper-plane"></i> Send Push Notification</h3>
    <p>Send a test push notification to users</p>
    
    <form id="pushNotificationForm" style="margin-top: 15px;">
        <div class="form-group">
            <label for="notificationTitle">Title:</label>
            <input type="text" id="notificationTitle" name="title" class="form-control" required style="width: 100%; padding: 8px; margin: 5px 0; border: 1px solid #ddd; border-radius: 4px;">
        </div>
        
        <div class="form-group">
            <label for="notificationBody">Message:</label>
            <textarea id="notificationBody" name="body" rows="3" class="form-control" required style="width: 100%; padding: 8px; margin: 5px 0; border: 1px solid #ddd; border-radius: 4px; resize: vertical;"></textarea>
        </div>
        
        <div class="form-group">
            <label for="notificationRecipient">Recipient:</label>
            <select id="notificationRecipient" name="recipient" class="form-control" style="width: 100%; padding: 8px; margin: 5px 0; border: 1px solid #ddd; border-radius: 4px;">
                <option value="all">All Users</option>
                <option value="students">Students Only</option>
                <option value="teachers">Teachers Only</option>
                <option value="parents">Parents Only</option>
            </select>
        </div>
        
        <button type="submit" class="btn btn-primary" style="background: #4CAF50; color: white; border: none; padding: 10px 15px; border-radius: 4px; cursor: pointer; width: 100%;">
            <i class="fas fa-bell"></i> Send Notification
        </button>
    </form>
    
    <div id="notificationResult" style="margin-top: 15px; display: none; padding: 10px; border-radius: 4px;"></div>
</div>

<script>
document.getElementById('pushNotificationForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = {
        title: document.getElementById('notificationTitle').value,
        body: document.getElementById('notificationBody').value,
        recipient: document.getElementById('notificationRecipient').value
    };
    
    // Show loading state
    const submitButton = this.querySelector('button[type="submit"]');
    const originalText = submitButton.innerHTML;
    submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
    submitButton.disabled = true;
    
    fetch('../../api/send-test-notification.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(formData)
    })
    .then(response => response.json())
    .then(data => {
        const resultDiv = document.getElementById('notificationResult');
        resultDiv.style.display = 'block';
        
        if (data.success) {
            resultDiv.className = 'alert alert-success';
            resultDiv.innerHTML = '<i class="fas fa-check-circle"></i> ' + data.message;
            // Reset form
            document.getElementById('pushNotificationForm').reset();
        } else {
            resultDiv.className = 'alert alert-error';
            resultDiv.innerHTML = '<i class="fas fa-exclamation-circle"></i> Error: ' + data.error;
        }
        
        // Restore button
        submitButton.innerHTML = originalText;
        submitButton.disabled = false;
        
        // Hide result after 5 seconds
        setTimeout(() => {
            resultDiv.style.display = 'none';
        }, 5000);
    })
    .catch(error => {
        const resultDiv = document.getElementById('notificationResult');
        resultDiv.style.display = 'block';
        resultDiv.className = 'alert alert-error';
        resultDiv.innerHTML = '<i class="fas fa-exclamation-circle"></i> Error: ' + error.message;
        
        // Restore button
        submitButton.innerHTML = originalText;
        submitButton.disabled = false;
        
        // Hide result after 5 seconds
        setTimeout(() => {
            resultDiv.style.display = 'none';
        }, 5000);
    });
});
</script>