
    $(document).ready(function () {
       
        $('#studentForm').submit(function () {
            var studentid = $(this).find('input[name="studentid"]').val();
            var email = $(this).find('input[name="email"]').val();
            var password = $(this).find('input[name="password"]').val();
            var confirmPassword = $(this).find('input[name="confirmpassword"]').val();
            var contactNo = $(this).find('input[name="contactno"]').val();

            var errors = [];

            
            if (!studentid.trim()) {
                errors.push('Student ID is required.');
                $('#error-studentid').text('This is required.');
            } else if (!isValidStudentID(studentid)) {
                errors.push('Invalid Student ID format.');
                $('#error-studentid').text('Invalid Student ID format.');
            } else {
                $('#error-studentid').text('');
            }

         
            if (!email.trim()) {
                errors.push('Email is required.');
                $('#error-email').text('This is required.');
            } else if (!isValidEmail(email)) {
                errors.push('Not an institutional email.');
                $('#error-email').text('Not an institutional email.');
            } else {
                $('#error-email').text('');
            }

           
            if (!password.trim()) {
                errors.push('Password is required.');
                $('#error-pw').text('This is required.');
            } else if (!isValidPassword(password)) {
                errors.push('Password does not meet the requirements.');
                $('#error-pw').text('Password does not meet the requirements.');
            } else if(password !== confirmPassword){
                errors.push('Passwords do not match.');
                $('#error-pw').text('Passwords do not match.');
            }else{
                $('#error-pw').text('');
            }

         
            if (!contactNo.trim()) {
                errors.push('Contact Number is required.');
                $('#error-cpn').text('This is required.');
            } else if (!isValidContactNumber(contactNo)) {
                errors.push('Invalid Contact Number.');
                $('#error-cpn').text('Invalid Contact Number.');
            } else {
                $('#error-cpn').text('');
            }
            validatePasswordRequirements($('#password').val());
            
        
            if (errors.length > 0) {
                return false; 
            }

      
            return true;
        });


        $('#teacherForm').submit(function () {
            var temail = $(this).find('input[name="temail"]').val();
            var tpassword = $(this).find('input[name="tpassword"]').val();
            var tconfirmPassword = $(this).find('input[name="tconfirmpassword"]').val();
            var tcontactNo = $(this).find('input[name="tcontactno"]').val();

            var terrors = [];

         
            if (!temail.trim()) {
                terrors.push('Email is required.');
                $('#terror-email').text('This is required.');
            } else if (!isValidEmail(temail)) {
                terrors.push('Not an institutional email.');
                $('#terror-email').text('Not an institutional email.');
            } else {
                $('#terror-email').text('');
            }

       
            if (!tpassword.trim()) {
                terrors.push('Password is required.');
                $('#terror-pw').text('This is required.');
            } else if (!isValidPassword(tpassword)) {
                terrors.push('Password does not meet the requirements.');
                $('#terror-pw').text('Password does not meet the requirements.');
            } else if(tpassword !== tconfirmPassword){
                terrors.push('Passwords do not match.');
                $('#terror-pw').text('Passwords do not match.');
            }else{
                $('#terror-pw').text('');
            }

          
            if (!tcontactNo.trim()) {
                terrors.push('Contact Number is required.');
                $('#terror-cpn').text('This is required.');
            } else if (!isValidContactNumber(tcontactNo)) {
                terrors.push('Invalid Contact Number.');
                $('#terror-cpn').text('Invalid Contact Number.');
            } else {
                $('#terror-cpn').text('');
            }

                 tvalidatePasswordRequirements($('#tpassword').val());

        
            if (terrors.length > 0) {
                return false; 
            }

         
            return true;
        });

        function validatePasswordRequirements(password) {
        
            $('#uppercaseRequirement, #lowercaseRequirement, #numberRequirement, #specialCharRequirement, #lengthRequirement').css('color', 'red');
    
        
            if (/[A-Z]/.test(password)) {
                $('#uppercaseRequirement').css('color', 'green');
            }
    
     
            if (/[a-z]/.test(password)) {
                $('#lowercaseRequirement').css('color', 'green');
            }
    
     
            if (/\d/.test(password)) {
                $('#numberRequirement').css('color', 'green');
            }
    
          
            if (/[@$!%*?&]/.test(password)) {
                $('#specialCharRequirement').css('color', 'green');
            }
    
        
            if (password.length >= 6) {
                $('#lengthRequirement').css('color', 'green');
            }
        }

        function tvalidatePasswordRequirements(password) {
         
            $('#tuppercaseRequirement, #tlowercaseRequirement, #tnumberRequirement, #tspecialCharRequirement, #tlengthRequirement').css('color', 'red');
    
           
            if (/[A-Z]/.test(password)) {
                $('#tuppercaseRequirement').css('color', 'green');
            }
    
         
            if (/[a-z]/.test(password)) {
                $('#tlowercaseRequirement').css('color', 'green');
            }
    
       
            if (/\d/.test(password)) {
                $('#tnumberRequirement').css('color', 'green');
            }
    
            
            if (/[@$!%*?&]/.test(password)) {
                $('#tspecialCharRequirement').css('color', 'green');
            }
    
            if (password.length >= 6) {
                $('#tlengthRequirement').css('color', 'green');
            }
        }

        function isValidStudentID(studentid) {
            var studentidRegex = /^(19|20|21|22|23|24)-[uU][rR]-\d{4}$/;
            return studentidRegex.test(studentid);
        }

        function isValidEmail(email) {
            var emailRegex = /@psu\.edu\.ph$/;
            return emailRegex.test(email);
        }

   
        function isValidPassword(password) {
            var passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{6,}$/;
            return passwordRegex.test(password);
        }

       
        function isValidContactNumber(contactNo) {
            var contactRegex = /^(09|\+639)\d{9}$/;
            return contactRegex.test(contactNo);
        }
    });

