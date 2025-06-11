

<?php echo view('admin/common/menu'); ?>
<Style>
    #abstractTable_filter{
        margin-bottom:10px
    }
    body{
        zoom: 0.9
    }
    #emailLogsTable tbody tr:hover {
        background-color: #f0f0f0; /* Light gray background color */
        cursor: pointer; /* Change cursor to pointer to indicate it's clickable */
    }
</Style>
<main style="padding-bottom:150px">
    <div class="container-fluid p-2">
        <div class="card p-0 m-0 shadow-lg">
            <div class="card-header  text-white" style="background-color:#2aa69c">Group Email Logs Table </div>
            <div class="card-body">
                <div class="">
                    <table id="emailLogsTable" class="table table-responsive table-striped table-bordered w-100">
                        <thead class=" table-active" style="">
                        <tr>

                            <th>Recipient Type</th>
                            <th>Recipient Group</th>
                            <th>Is test?</th>
                            <th>Reference</th>
                            <th>Subject</th>
                            <th>Status</th>
                            <th>Template ID</th>
                            <th>Created At</th>
                            <th>Total Recipients</th>
                            <th>Sent</th>
                            <th>Unique Code</th>
                        </tr>
                        </thead>
                        <tbody id="emailLogsTableBody">
                        <!-- This will be filled by jQuery and Datatables -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</main>


<script>
    let baseUrlAdmin = "<?= base_url() . 'admin/' ?>";

    $(document).ready(function() {
        $('#emailLogsTable').DataTable({
            processing: true,
            serverSide: true,
            paging: true,
            ajax: {
                url: baseUrlAdmin + 'getGroupEmailLogs',
                type: 'POST'
            },
            columns: [
                { data: 0 },
                { data: 1 },
                { data: 2 },
                { data: 3 },
                { data: 4 },
                { data: 5 },
                { data: 6 },
                { data: 7 },
                { data: 8 },
                { data: 9 },
                { data: 10 , visible: false}
            ],
            dom: 'Bfrtip',
            buttons: ['copy', 'csv', 'excel', 'pdf', 'print'],
            buttons: {
                dom: {
                    button: {
                        className: 'btn btn-outline-primary'
                    }
                }
            }
        });
        $('#emailLogsTable tbody').on('click', 'tr', function() {
            // Assuming the first column contains the ID you want to use for navigation
            var data = $('#emailLogsTable').DataTable().row(this).data();
            var emailLogId = data[0]; // Assuming the ID is in the first column

            // return false;
            // Navigate to the details page using the ID
            window.location.href = baseUrlAdmin + 'email_logs/'+data[10];
        });
    });
</script>