$(document).ready(function() {
   
    $('#teacherForm').hide();


    $('input[name="registrationType"]').change(function() {
        var selectedType = $(this).val();

      
        if (selectedType === 'student') {
            $('#studentForm').show();
            $('#teacherForm').hide();
            $('#accountTypeTitle').text('Create account as a Student');
            $('#accountTypeDescription').text('Launch your company, create startups with your team, or join exciting projects to attract investors!');
        } else if (selectedType === 'teacher') {
            $('#studentForm').hide();
            $('#teacherForm').show();
            $('#accountTypeTitle').text('Create account as a Mentor');
            $('#accountTypeDescription').text('Empower and inspire as a mentor, guiding our beloved PSUnians to bring their innovative startup projects to life by honing their skills.');
        }
    });
});
