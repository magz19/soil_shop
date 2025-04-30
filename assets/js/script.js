// Custom JavaScript for S-Oil Products Store

document.addEventListener('DOMContentLoaded', function() {

    // Quantity increment/decrement on product and cart pages
    const quantityInputs = document.querySelectorAll('.quantity-input');
    
    quantityInputs.forEach(input => {
        const decrementBtn = input.parentElement.querySelector('.decrement-btn');
        const incrementBtn = input.parentElement.querySelector('.increment-btn');
        
        if (decrementBtn) {
            decrementBtn.addEventListener('click', function() {
                let value = parseInt(input.value);
                if (value > 1) {
                    input.value = value - 1;
                    
                    // If there's an update form, submit it
                    const updateForm = this.closest('.update-quantity-form');
                    if (updateForm) {
                        updateForm.submit();
                    }
                }
            });
        }
        
        if (incrementBtn) {
            incrementBtn.addEventListener('click', function() {
                let value = parseInt(input.value);
                input.value = value + 1;
                
                // If there's an update form, submit it
                const updateForm = this.closest('.update-quantity-form');
                if (updateForm) {
                    updateForm.submit();
                }
            });
        }
    });
    
    // Payment method selection
    const paymentOptions = document.querySelectorAll('.payment-option');
    const paymentMethodInput = document.getElementById('payment_method');
    const screenshotUploadSection = document.getElementById('screenshot-upload-section');
    
    paymentOptions.forEach(option => {
        option.addEventListener('click', function() {
            // Remove selected class from all options
            paymentOptions.forEach(opt => opt.classList.remove('selected'));
            
            // Add selected class to clicked option
            this.classList.add('selected');
            
            // Update hidden input value
            const method = this.getAttribute('data-method');
            if (paymentMethodInput) {
                paymentMethodInput.value = method;
            }
            
            // Show/hide screenshot upload section
            if (screenshotUploadSection) {
                if (method === 'gcash') {
                    screenshotUploadSection.style.display = 'block';
                } else {
                    screenshotUploadSection.style.display = 'none';
                }
            }
        });
    });
    
    // Custom file upload button
    const fileInput = document.getElementById('payment_screenshot');
    const fileInputLabel = document.querySelector('.custom-file-upload');
    const fileNameDisplay = document.getElementById('file-name-display');
    
    if (fileInput && fileNameDisplay) {
        fileInput.addEventListener('change', function() {
            if (fileInput.files.length > 0) {
                fileNameDisplay.textContent = fileInput.files[0].name;
                fileNameDisplay.classList.add('file-upload-success');
            } else {
                fileNameDisplay.textContent = '';
                fileNameDisplay.classList.remove('file-upload-success');
            }
        });
    }
    
    // Form validation
    const checkoutForm = document.getElementById('checkout-form');
    
    if (checkoutForm) {
        checkoutForm.addEventListener('submit', function(event) {
            const paymentMethod = paymentMethodInput.value;
            
            // Validate payment screenshot for GCash payments
            if (paymentMethod === 'gcash' && fileInput && fileInput.files.length === 0) {
                event.preventDefault();
                alert('Please upload a payment screenshot');
                return false;
            }
            
            // Additional validation can be added here
        });
    }
    
});