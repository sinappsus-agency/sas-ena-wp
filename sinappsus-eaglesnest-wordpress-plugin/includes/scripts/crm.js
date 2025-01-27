jQuery(document).ready(function($) {
    // Add user
    $('#add-user-form').on('submit', function(e) {
        e.preventDefault();
        var name = $('#add-user-name').val();
        var email = $('#add-user-email').val();

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'add_user',
                name: name,
                email: email
            },
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert('Failed to add user');
                }
            }
        });
    });

    // Edit user
    $('.edit-user').on('click', function() {
        var userId = $(this).data('id');
        var userName = $(this).closest('tr').find('td:eq(1)').text();
        var userEmail = $(this).closest('tr').find('td:eq(2)').text();

        $('#edit-user-id').val(userId);
        $('#edit-user-name').val(userName);
        $('#edit-user-email').val(userEmail);
        $('#edit-user-modal').show();
    });

    $('#edit-user-form').on('submit', function(e) {
        e.preventDefault();
        var userId = $('#edit-user-id').val();
        var name = $('#edit-user-name').val();
        var email = $('#edit-user-email').val();

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'edit_user',
                user_id: userId,
                name: name,
                email: email
            },
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert('Failed to edit user');
                }
            }
        });
    });

    // Delete user
    $('.delete-user').on('click', function() {
        if (!confirm('Are you sure you want to delete this user?')) {
            return;
        }

        var userId = $(this).data('id');

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'delete_user',
                user_id: userId
            },
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert('Failed to delete user');
                }
            }
        });
    });
});
