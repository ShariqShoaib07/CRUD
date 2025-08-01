<?php include 'db.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>Users List</title>
    <link rel="stylesheet" href="style.css">
    <!-- Add DataTables CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
</head>
<body>
<h2>📋 User List</h2>    
<div class="table-container">
    <table id="usersTable" class="display nowrap" style="width:100%">
        <thead>
            <tr>
                <th>ID</th>
                <th>Image</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $sql = "SELECT * FROM userrs";
            $result = $conn->query($sql);
            if ($result->num_rows > 0):
                while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td>
                        <?php if (!empty($row['image'])): ?>
                            <img src="uploads/<?= htmlspecialchars($row['image']) ?>" width="50" style="border-radius: 5px;">
                        <?php else: ?>
                            <div style="width:50px; height:50px; background:#555; border-radius:5px;"></div>
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td><?= htmlspecialchars($row['email']) ?></td>
                    <td><?= htmlspecialchars($row['phone']) ?></td>
                    <td>
                        <div class="actions">
                            <a class="button" href="edit.php?id=<?= $row['id'] ?>">Edit</a>
                            <a class="button" href="delete.php?id=<?= $row['id'] ?>" onclick="return confirm('Are you sure?')">Delete</a>
                        </div>
                    </td>
                </tr>
            <?php endwhile; endif; ?>
        </tbody>
    </table>
</div>
<br>
<div style="text-align: center;">
    <a class="button add-new" href="create.php">+ Add New User</a>
</div>

<!-- Add jQuery and DataTables JS -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script>
    $(document).ready(function() {
        var table = $('#usersTable').DataTable({
            dom: '<"top-controls"<"controls-left"lf><"controls-center"B><"controls-right">>rt<"bottom-controls"ip>',
            buttons: [
                {
                    extend: 'copy',
                    className: 'dt-button dt-buttons-center',
                    text: '<i class="fas fa-copy"></i> Copy'
                },
                {
                    extend: 'csv',
                    className: 'dt-button dt-buttons-center',
                    text: '<i class="fas fa-file-csv"></i> CSV'
                }
            ],
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search users...",
                lengthMenu: "Show _MENU_ users",
                info: "Showing _START_ to _END_ of _TOTAL_ users",
                infoEmpty: "No users found",
                infoFiltered: "(filtered from _MAX_ total users)",
                paginate: {
                    previous: '<i class="fas fa-chevron-left"></i>',
                    next: '<i class="fas fa-chevron-right"></i>'
                }
            },
            responsive: true,
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            searchDelay: 500,
            initComplete: function() {
                // Style inputs
                $('.dataTables_filter input').addClass('dark-search')
                    .attr('title', 'Type at least 3 characters to search');

                // Add clear button (ONCE)
                if (!$('.clear-search').length) {
                    $('.dataTables_filter input').after(
                        '<span class="clear-search" style="display:none; cursor:pointer; margin-left: -20px; color: #aaa;"><i class="fas fa-times"></i></span>'
                    );
                }

                // Handle search input
                $('.dataTables_filter input').off('keyup input').on('keyup input', function() {
                    var searchTerm = this.value.trim();
                    var $clearButton = $(this).next('.clear-search');
                    
                    $clearButton.toggle(searchTerm.length > 0);
                    
                    if (searchTerm.length >= 3) {
                        table.search(searchTerm).draw();
                        $(this).removeClass('too-short');
                    } else if (searchTerm.length === 0) {
                        table.search('').draw();
                        $(this).removeClass('too-short');
                    } else {
                        $(this).addClass('too-short');
                        table.search('').draw();
                    }
                });

                // Handle clear button
                $(document).off('click', '.clear-search').on('click', '.clear-search', function() {
                    var $input = $(this).prev('input');
                    $input.val('').removeClass('too-short').trigger('input');
                    $(this).hide();
                });
            }
        });


        // Floating particles (keep your existing code)
        const particleCount = 30;
        const body = document.body;
        for (let i = 0; i < particleCount; i++) {
            const particle = document.createElement('div');
            particle.classList.add('particle');
            const size = Math.random() * 4 + 2;
            particle.style.width = `${size}px`;
            particle.style.height = `${size}px`;
            particle.style.left = `${Math.random() * 100}vw`;
            particle.style.top = `${Math.random() * 100}vh`;
            const duration = Math.random() * 20 + 10;
            particle.style.animation = `float ${duration}s linear infinite`;
            body.appendChild(particle);
        }
    });
</script>

<style>
    @keyframes float {
        0% { transform: translateY(0) translateX(0); opacity: 0; }
        10% { opacity: 1; }
        90% { opacity: 1; }
        100% { transform: translateY(-100vh) translateX(20px); opacity: 0; }
    }
    
    /* Dark Theme DataTables */
    #usersTable {
        color: #ffffffff;
        border: 1px solid rgba(255, 255, 255, 0.1);
        background-color: rgba(28, 28, 28, 0.7);
    }

    #usersTable thead th {
        background: linear-gradient(135deg, rgba(0,198,255,0.7), rgba(0,114,255,0.7));
        color: #fff;
        border-bottom: none;
        padding: 12px 15px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    #usersTable tbody td {
        padding: 12px 15px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        vertical-align: middle;
    }

    #usersTable tbody tr:hover {
        background-color: rgba(255, 255, 255, 0.08) !important;
    }

    /* Search and Length Menu Styling */
    .dataTables_wrapper .dataTables_filter input.dark-search {
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
        color: #fff;
        padding: 8px 15px;
        border-radius: 8px;
        margin-left: 10px;
        transition: all 0.3s ease;
        backdrop-filter: blur(5px);
    }

    .dataTables_wrapper .dataTables_filter input.dark-search:focus {
        border-color: #ff416c;
        box-shadow: 0 0 0 2px rgba(255, 65, 108, 0.2);
        background: rgba(255, 255, 255, 0.15);
    }

    .dataTables_wrapper .dataTables_length select.dark-select {
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
        color: #fff;
        padding: 6px 12px;
        border-radius: 8px;
        margin: 0 5px;
        backdrop-filter: blur(5px);
    }

    /* Pagination Styling */
    .dataTables_wrapper .dataTables_paginate .paginate_button {
        color: #ddd !important;
        border: 1px solid rgba(255, 255, 255, 0.1) !important;
        background: rgba(255, 255, 255, 0.05) !important;
        margin: 0 3px;
        border-radius: 6px !important;
        padding: 5px 12px !important;
        transition: all 0.3s ease !important;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button.current,
    .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
        background: linear-gradient(135deg, #ff416c, #ff4b2b) !important;
        color: white !important;
        border: none !important;
        box-shadow: 0 2px 5px rgba(0,0,0,0.2) !important;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
        background: rgba(255, 255, 255, 0.1) !important;
        color: white !important;
        border-color: rgba(255, 255, 255, 0.2) !important;
        transform: translateY(-1px);
    }

    /* Info Text Styling */
    .dataTables_info {
        color: rgba(47, 45, 45, 0.7) !important;
        font-size: 0.9em;
        padding-top: 12px !important;
    }

    /* Button Container */
    .dataTables_wrapper .dt-buttons {
        margin-bottom: 15px;
        display: flex;
        gap: 8px;
    }

    /* Export Buttons */
    .dt-button {
        background: linear-gradient(135deg, rgba(0,198,255,0.7), rgba(0,114,255,0.7)) !important;
        color: white !important;
        border: none !important;
        border-radius: 8px !important;
        padding: 8px 16px !important;
        transition: all 0.3s ease !important;
        box-shadow: 0 2px 5px rgba(0,0,0,0.2) !important;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .dt-button:hover {
        background: linear-gradient(135deg, rgba(0,114,255,0.7), rgba(0,198,255,0.7)) !important;
        transform: translateY(-2px) !important;
        box-shadow: 0 4px 8px rgba(0,0,0,0.3) !important;
    }

    .dt-button i {
        margin-right: 6px;
        font-size: 0.9em;
    }

    /* Table container adjustments */
    .table-container {
        padding: 25px !important;
        backdrop-filter: blur(8px);
    }

    .table-container .dataTables_wrapper {
        padding: 0;
        margin: 0;
    }

    /* Improved length menu styling */
    .dataTables_length {
        position: relative;
        z-index: 1;
    }

    .dataTables_length select.dark-select {
        background-color: #2a2a2a !important;
        color: white !important;
        border: 1px solid #444 !important;
        padding: 6px 30px 6px 12px !important;
        border-radius: 6px !important;
        margin: 0 5px !important;
        appearance: none;
        -webkit-appearance: none;
        -moz-appearance: none;
        background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='white'%3e%3cpath d='M7 10l5 5 5-5z'/%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right 8px center;
        background-size: 12px;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .dataTables_length select.dark-select:hover {
        border-color: #666 !important;
    }

    .dataTables_length select.dark-select:focus {
        outline: none;
        border-color: #ff416c !important;
        box-shadow: 0 0 0 2px rgba(255, 65, 108, 0.2) !important;
    }

    .dataTables_length select.dark-select option {
        background-color: #2a2a2a;
        color: white;
        padding: 8px;
    }

    .dataTables_length label {
        color: rgba(255, 255, 255, 0.8);
        font-size: 0.9rem;
        display: flex;
        align-items: center;
        gap: 8px;
    }
</style>

</body>
</html>