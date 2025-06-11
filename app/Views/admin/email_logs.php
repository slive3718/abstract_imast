

<?php echo view('admin/common/menu'); ?>
<Style>
    #abstractTable_filter{
        margin-bottom:10px
    }
    body{
        zoom: 0.9
    }
</Style>
<main style="padding-bottom:150px">
    <div class="container-fluid p-2">
        <div class="card p-0 m-0 shadow-lg">
            <div class="card-header  text-white" style="background-color:#2aa69c">Email Logs Table </div>
            <div class="card-body">
                <div class="">
                    <table id="emailLogsTable" class="table table-responsive table-striped table-bordered w-100">
                        <thead class=" table-active" style="">
                        <tr>
                            <th>ID</th>
                            <th>User ID</th>
                            <th>To Email</th>
                            <th>Reference</th>
                            <th>Subject</th>
                            <th>Status</th>
                            <th>Send From</th>
                            <th>Send To</th>
                            <th>Template ID</th>
                            <th>Paper ID</th>
                            <th>Created At</th>
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
    let unique_code = "<?=$unique_code?>"
    $(document).ready(function() {
        $('#emailLogsTable').DataTable({
            processing: true,
            serverSide: true,
            paging: true,
            ajax: {
                url: baseUrlAdmin + 'getAllEmailLogs/'+unique_code,
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
                { data: 10 }
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
    });
</script>