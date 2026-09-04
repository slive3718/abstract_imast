

<?php echo view('admin/common/menu'); ?>
<main style="min-height: 900px" class="mb-5">
    <div class="card container">
       <div class="card-header bg-white">
           <h1 class="text-2xl font-bold mb-4">Statistics</h1>
           <p class="">
               This page will display various statistics about the system, such as user activity, submissions, and review progress.
           </p>
       </div>

        <div class="card-body">
            <div class="categories-div">
                <div class="row">
                    <div class="col-md-12 col-sm-6 card shadow p-0">
                        <div class="card-header bg-info">
                            <h2 class="text-xl font-semibold mb-2 text-white">Abstract Submissions</h2>
                            <p class="text-gray-700">
                                This section provides an overview of the paper records in the system, categorized by their respective categories. It shows the total number of papers, how many are complete, and how many are incomplete for each category.
                            </p>
                        </div>
                        <div class="category-card bg-blue-100 p-4 rounded-lg shadow-md mb-4 card-body">
                            <table style="width: 100%; border-collapse: collapse;" class="table table-responsive table-striped border">
                                <thead>
                                <tr>
                                    <th style="text-align: left; padding: 8px; width: 25%;">Categories</th>
                                    <th style="text-align: left; padding: 8px; width: 25%;">Complete</th>
                                    <th style="text-align: left; padding: 8px; width: 25%;">Incomplete</th>
                                    <th style="text-align: left; padding: 8px; width: 25%;">Total</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                    $totalFinalized = 0;
                                    $totalIncomplete = 0;
                                    $total = 0;
                                ?>
                                <?php if(!empty($paperTotalPerCategory)): ?>
                                    <?php foreach($paperTotalPerCategory as $category => $value):
                                        $totalFinalized += $value['total_finalized'];
                                        $totalIncomplete += $value['total_incomplete'];
                                        $total += $value['total'];
                                        ?>
                                        <tr>
                                            <td style="padding: 8px;"><?php echo esc($value['name']); ?></td>
                                            <td style="padding: 8px;"><?php echo esc($value['total_finalized']); ?></td>
                                            <td style="padding: 8px;"><?php echo esc($value['total_incomplete']); ?></td>
                                            <td style="padding: 8px;"><?php echo esc($value['total']); ?></td>
                                        </tr>
                                    <?php endforeach; endif; ?>
                                <tr>
                                    <td>Total</td>
                                    <td><?php echo esc($totalFinalized); ?></td>
                                    <td><?php echo esc($totalIncomplete); ?></td>
                                    <td><?php echo esc($total); ?></td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Add your statistics content here -->
    </div>
</main>