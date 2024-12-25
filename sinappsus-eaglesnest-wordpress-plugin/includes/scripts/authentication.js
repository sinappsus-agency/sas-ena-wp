jQuery(document).ready(function($) {
    let tokenExpiryTime = null;

    $('#authenticate-button').on('click', function() {
        console.log('Authenticate button clicked');
        const username = $('#ena_sinappsus_username').val();
        const password = $('#ena_sinappsus_password').val();

        console.log('Username:', username);
        console.log('Password:', password);

        $.ajax({
            url: 'https://api-ena.sinappsus.us/api/auth/login',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({ email: username, password: password }),
            success: function(response) {
                console.log('Authentication response:', response);
                $('#ena_sinappsus_jwt_token').val(response.access_token);
                $('#ena_sinappsus_refresh_token').val(response.refresh_token);
                tokenExpiryTime = new Date();
                tokenExpiryTime.setDate(tokenExpiryTime.getDate() + 30);
                $('#ena_sinappsus_token_expiry').val(tokenExpiryTime.toISOString());
                $('#message').text('Authenticated successfully').css('color', 'green');
                updateTimer();
                saveSettings();
            },
            error: function(xhr, status, error) {
                console.log('Authentication error:', error);
                $('#message').text('Authentication failed').css('color', 'red');
            }
        });
    });

    $('#validate-button').on('click', function() {
        console.log('Validate button clicked');
        const token = $('#ena_sinappsus_jwt_token').val();

        console.log('Token:', token);

        $.ajax({
            url: 'https://api-ena.sinappsus.us/api/auth/validate',
            method: 'POST',
            headers: { 'Authorization': 'Bearer ' + token },
            success: function(response) {
                console.log('Validation response:', response);
                $('#message').text('Token is valid').css('color', 'green');
            },
            error: function(xhr, status, error) {
                console.log('Validation error:', error);
                $('#message').text('Token is invalid').css('color', 'red');
            }
        });
    });

    function updateTimer() {
        console.log('Updating timer');
        const now = new Date();
        const expiryTime = new Date($('#ena_sinappsus_token_expiry').val());
        const timeLeft = expiryTime - now;

        console.log('Current time:', now);
        console.log('Token expiry time:', expiryTime);
        console.log('Time left:', timeLeft);

        if (timeLeft > 0) {
            const daysLeft = Math.floor(timeLeft / (1000 * 60 * 60 * 24));
            $('#timer').text('Token expires in ' + daysLeft + ' days');
        } else {
            $('#timer').text('Token has expired');
        }
    }

    function renewToken() {
        console.log('Renewing token');
        const refreshToken = $('#ena_sinappsus_refresh_token').val();

        console.log('Refresh token:', refreshToken);

        $.ajax({
            url: 'https://api-ena.sinappsus.us/api/auth/refresh',
            method: 'POST',
            headers: { 'Authorization': 'Bearer ' + refreshToken },
            success: function(response) {
                console.log('Renewal response:', response);
                $('#ena_sinappsus_jwt_token').val(response.access_token);
                tokenExpiryTime = new Date();
                tokenExpiryTime.setDate(tokenExpiryTime.getDate() + 1);
                $('#ena_sinappsus_token_expiry').val(tokenExpiryTime.toISOString());
                updateTimer();
                saveSettings();
            },
            error: function(xhr, status, error) {
                console.log('Renewal error:', error);
                $('#message').text('Token renewal failed').css('color', 'red');
            }
        });
    }

    function saveSettings() {
        console.log('Saving settings');
        const data = {
            action: 'save_jwt_settings',
            ena_sinappsus_username: $('#ena_sinappsus_username').val(),
            ena_sinappsus_password: $('#ena_sinappsus_password').val(),
            ena_sinappsus_jwt_token: $('#ena_sinappsus_jwt_token').val(),
            ena_sinappsus_refresh_token: $('#ena_sinappsus_refresh_token').val(),
            ena_sinappsus_token_expiry: $('#ena_sinappsus_token_expiry').val(),
        };

        console.log('Settings data:', data);

        $.post(jwtAuth.ajax_url, data, function(response) {
            console.log('Settings saved response:', response);
        });
    }

    // Check if token needs to be renewed
    const now = new Date();
    const expiryTime = new Date($('#ena_sinappsus_token_expiry').val());

    console.log('Initial check - Current time:', now);
    console.log('Initial check - Token expiry time:', expiryTime);

    if (expiryTime - now > 0) {
        console.log('Token is still valid, updating timer...');
        updateTimer();
    }

    // Set interval to check token expiry every day
    setInterval(function() {
        const now = new Date();
        const expiryTime = new Date($('#ena_sinappsus_token_expiry').val());

        console.log('Interval check - Current time:', now);
        console.log('Interval check - Token expiry time:', expiryTime);

        if (expiryTime - now <= 0) {
            console.log('Token has expired, renewing...');
            renewToken();
        } else {
            console.log('Token is still valid, updating timer...');
            updateTimer();
        }
    }, 24 * 60 * 60 * 1000); // Check every 24 hours
});