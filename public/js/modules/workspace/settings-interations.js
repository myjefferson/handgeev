document.addEventListener('DOMContentLoaded', function() {     
    // Event listeners
    $('#access-private').on('change', function() {
       $('#password-protection-section').addClass('hidden');
    });
    
    $('#access-public').on('change', function() {
        $('#password-protection-section').removeClass('hidden');
    });
    
    $('#checkbox-password-protection').on('change', function() {
        $('#password-field').toggleClass('hidden', !this.checked);
    });
    
    // Inicializar estado
    if(!$('#access-private').prop('checked')){
        $('#password-field').toggleClass('hidden', !$('#checkbox-password-protection').prop('checked'));
        $('#password-protection-section').removeClass('hidden');
    } 
});