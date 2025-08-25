/**
 * Extended Doctor Profile Validation
 * Comprehensive validation for all profile fields
 */

function validateExtendedProfile() {
    var isValid = true;
    var errors = [];
    
    // Clear previous error messages
    $('.error-message').remove();
    $('.form-control').removeClass('error');
    
    // Basic Information Validation
    var docname = $('input[name="docname"]').val().trim();
    if (docname === '') {
        addError('docname', 'Doctor name is required');
        isValid = false;
    } else if (docname.length < 2) {
        addError('docname', 'Doctor name must be at least 2 characters');
        isValid = false;
    } else if (!/^[a-zA-Z\s.]+$/.test(docname)) {
        addError('docname', 'Doctor name can only contain letters, spaces, and dots');
        isValid = false;
    }
    
    var doccontact = $('input[name="doccontact"]').val().trim();
    if (doccontact === '') {
        addError('doccontact', 'Contact number is required');
        isValid = false;
    } else if (!/^[0-9+\-\s()]+$/.test(doccontact)) {
        addError('doccontact', 'Please enter a valid contact number');
        isValid = false;
    } else if (doccontact.replace(/[^0-9]/g, '').length < 10) {
        addError('doccontact', 'Contact number must be at least 10 digits');
        isValid = false;
    }
    
    var docspecialization = $('select[name="Doctorspecialization"]').val();
    if (!docspecialization || docspecialization === '') {
        addError('Doctorspecialization', 'Please select a specialization');
        isValid = false;
    }
    
    // Years of Experience Validation
    var experience = $('input[name="years_of_experience"]').val();
    if (experience !== '') {
        experience = parseInt(experience);
        if (isNaN(experience) || experience < 0) {
            addError('years_of_experience', 'Years of experience must be a positive number');
            isValid = false;
        } else if (experience > 60) {
            addError('years_of_experience', 'Years of experience cannot exceed 60 years');
            isValid = false;
        }
    }
    
    // Fee Validation
    var docfees = $('input[name="docfees"]').val().trim();
    if (docfees !== '') {
        if (!/^[0-9]+(\.[0-9]{1,2})?$/.test(docfees)) {
            addError('docfees', 'Please enter a valid fee amount');
            isValid = false;
        } else {
            var feeAmount = parseFloat(docfees);
            if (feeAmount < 0) {
                addError('docfees', 'Fee cannot be negative');
                isValid = false;
            } else if (feeAmount > 50000) {
                addError('docfees', 'Fee amount seems unusually high. Please verify.');
                isValid = false;
            }
        }
    }
    
    // Email Validation (readonly but still validate format)
    var docemail = $('input[name="docemail"]').val().trim();
    if (docemail !== '') {
        var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(docemail)) {
            addError('docemail', 'Please enter a valid email address');
            isValid = false;
        }
    }
    
    // Text Area Length Validation
    validateTextAreaLength('education', 1000, 'Educational background');
    validateTextAreaLength('specialization_details', 500, 'Specialization details');
    validateTextAreaLength('bio', 1500, 'Professional biography');
    validateTextAreaLength('research_interests', 1000, 'Research interests');
    validateTextAreaLength('availability_note', 300, 'Availability notes');
    
    // Institution Name Validation
    validateInstitutionName('medical_school', 'Medical school');
    validateInstitutionName('residency', 'Residency program');
    validateInstitutionName('fellowship', 'Fellowship program');
    
    // Tag Fields Validation
    validateTagField('degrees', 'Degrees');
    validateTagField('certifications', 'Certifications');
    validateTagField('board_certifications', 'Board certifications');
    validateTagField('languages_spoken', 'Languages spoken');
    validateTagField('awards_honors', 'Awards and honors');
    validateTagField('professional_memberships', 'Professional memberships');
    
    // Profile Picture Validation
    var profilePicture = $('input[name="profile_picture"]')[0];
    if (profilePicture && profilePicture.files.length > 0) {
        var file = profilePicture.files[0];
        var allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        var maxSize = 5 * 1024 * 1024; // 5MB
        
        if (!allowedTypes.includes(file.type)) {
            addError('profile_picture', 'Please upload only JPG, PNG, or GIF files');
            isValid = false;
        }
        
        if (file.size > maxSize) {
            addError('profile_picture', 'File size must be less than 5MB');
            isValid = false;
        }
        
        // Check image dimensions (optional)
        var img = new Image();
        img.onload = function() {
            if (this.width < 100 || this.height < 100) {
                addError('profile_picture', 'Image should be at least 100x100 pixels for better quality');
            }
        };
        img.src = URL.createObjectURL(file);
    }
    
    // Show summary of errors if any
    if (!isValid) {
        showErrorSummary();
        // Scroll to first error
        var firstError = $('.form-control.error').first();
        if (firstError.length > 0) {
            $('html, body').animate({
                scrollTop: firstError.offset().top - 100
            }, 500);
        }
    }
    
    return isValid;
}

function validateTextAreaLength(fieldName, maxLength, displayName) {
    var field = $('textarea[name="' + fieldName + '"]');
    var value = field.val().trim();
    
    if (value.length > maxLength) {
        addError(fieldName, displayName + ' cannot exceed ' + maxLength + ' characters (current: ' + value.length + ')');
        return false;
    }
    
    // Show character count
    var charCount = field.next('.char-count');
    if (charCount.length === 0) {
        charCount = $('<small class="char-count text-muted"></small>');
        field.after(charCount);
    }
    charCount.text(value.length + '/' + maxLength + ' characters');
    
    return true;
}

function validateInstitutionName(fieldName, displayName) {
    var field = $('input[name="' + fieldName + '"]');
    var value = field.val().trim();
    
    if (value !== '') {
        if (value.length < 3) {
            addError(fieldName, displayName + ' name must be at least 3 characters');
            return false;
        }
        
        if (value.length > 200) {
            addError(fieldName, displayName + ' name cannot exceed 200 characters');
            return false;
        }
        
        // Check for valid characters (letters, numbers, spaces, common punctuation)
        if (!/^[a-zA-Z0-9\s\-.,()&']+$/.test(value)) {
            addError(fieldName, displayName + ' name contains invalid characters');
            return false;
        }
    }
    
    return true;
}

function validateTagField(fieldName, displayName) {
    var field = $('input[name="' + fieldName + '"]');
    var value = field.val().trim();
    
    if (value !== '') {
        var tags = value.split(',').map(tag => tag.trim()).filter(tag => tag !== '');
        
        // Check number of tags
        if (tags.length > 10) {
            addError(fieldName, displayName + ' cannot have more than 10 items');
            return false;
        }
        
        // Check individual tag length
        for (var i = 0; i < tags.length; i++) {
            if (tags[i].length < 2) {
                addError(fieldName, 'Each ' + displayName.toLowerCase() + ' item must be at least 2 characters');
                return false;
            }
            if (tags[i].length > 100) {
                addError(fieldName, 'Each ' + displayName.toLowerCase() + ' item cannot exceed 100 characters');
                return false;
            }
        }
        
        // Update field with cleaned tags
        field.val(tags.join(', '));
    }
    
    return true;
}

function addError(fieldName, message) {
    var field = $('[name="' + fieldName + '"]');
    field.addClass('error');
    
    // Add error message
    var errorDiv = $('<div class="error-message text-danger" style="font-size: 12px; margin-top: 5px;"><i class="fa fa-exclamation-triangle"></i> ' + message + '</div>');
    field.parent().append(errorDiv);
}

function showErrorSummary() {
    var errorCount = $('.error-message').length;
    
    // Remove existing summary
    $('#error-summary').remove();
    
    var summaryHtml = '<div id="error-summary" class="alert alert-danger" style="margin-bottom: 20px;">' +
        '<h4><i class="fa fa-exclamation-triangle"></i> Please fix the following errors:</h4>' +
        '<p>Found ' + errorCount + ' error(s) in the form. Please review and correct the highlighted fields.</p>' +
        '</div>';
    
    $('form[name="extendedProfile"]').prepend(summaryHtml);
}

// Real-time validation helpers
function setupRealTimeValidation() {
    // Character count for textareas
    $('textarea').on('input', function() {
        var fieldName = $(this).attr('name');
        var maxLengths = {
            'education': 1000,
            'specialization_details': 500,
            'bio': 1500,
            'research_interests': 1000,
            'availability_note': 300
        };
        
        if (maxLengths[fieldName]) {
            validateTextAreaLength(fieldName, maxLengths[fieldName], fieldName.replace('_', ' '));
        }
    });
    
    // Real-time validation for numeric fields
    $('input[name="years_of_experience"]').on('blur', function() {
        var value = $(this).val();
        if (value !== '') {
            var experience = parseInt(value);
            if (isNaN(experience) || experience < 0 || experience > 60) {
                $(this).addClass('error');
            } else {
                $(this).removeClass('error');
            }
        }
    });
    
    // Real-time validation for contact number
    $('input[name="doccontact"]').on('input', function() {
        var value = $(this).val();
        // Allow only numbers, spaces, +, -, (, )
        $(this).val(value.replace(/[^0-9+\-\s()]/g, ''));
    });
    
    // Real-time validation for fees
    $('input[name="docfees"]').on('input', function() {
        var value = $(this).val();
        // Allow only numbers and decimal point
        $(this).val(value.replace(/[^0-9.]/g, ''));
    });
    
    // Clear errors on input
    $('.form-control').on('input change', function() {
        $(this).removeClass('error');
        $(this).parent().find('.error-message').remove();
        $('#error-summary').remove();
    });
}

// Initialize validation when document is ready
$(document).ready(function() {
    setupRealTimeValidation();
    
    // Override form submission
    $('form[name="extendedProfile"]').on('submit', function(e) {
        if (!validateExtendedProfile()) {
            e.preventDefault();
            return false;
        }
        
        // Show loading state
        var submitBtn = $(this).find('button[type="submit"]');
        var originalText = submitBtn.html();
        submitBtn.html('<i class="fa fa-spinner fa-spin"></i> Updating Profile...').prop('disabled', true);
        
        // Re-enable button after 3 seconds (in case of errors)
        setTimeout(function() {
            submitBtn.html(originalText).prop('disabled', false);
        }, 3000);
    });
});

// CSS for error styling
var errorCSS = `
    <style>
        .form-control.error {
            border-color: #dc3545 !important;
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25) !important;
        }
        
        .error-message {
            animation: fadeIn 0.3s ease-in;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .char-count {
            float: right;
            font-size: 11px;
            color: #6c757d;
        }
        
        .tag-input {
            position: relative;
        }
        
        .tag-input:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }
    </style>
`;

$('head').append(errorCSS);
