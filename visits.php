<?php
// Include your database connection file here
// Example: include('db.php');

// --- IPs to HIDE from the list (still logged elsewhere normally) ---
$excludeIps = [
    '216.110.144.0',
    // '203.0.113.5',
    // '198.51.100.22',
];

// Handle View Data request
$tableName = "visitors";

if (!empty($tableName)) {
    // FIX: remove invalid "WHERE ip_address"
    $dataQuery = "SELECT * FROM $tableName ORDER BY visit_time DESC";
    $dataResult = $conn->query($dataQuery);

    if ($dataResult && $dataResult->num_rows > 0) {
        $data = [];

        while ($row = $dataResult->fetch_assoc()) {
            // Skip rows whose raw IP is in the exclusion list
            if (isset($row['ip_address']) && in_array($row['ip_address'], $excludeIps, true)) {
                continue; // do not display this row
            }

            // Format visit_time (MM/DD/YYYY hh:mm:ss AM/PM)
            if (isset($row['visit_time'])) {
                $row['visit_time'] = date('m/d/Y h:i:s A', strtotime($row['visit_time']));
            }

            // Convert ip_address to a link (use escaped value)
            if (isset($row['ip_address'])) {
                $ipEsc = htmlspecialchars($row['ip_address'], ENT_QUOTES, 'UTF-8');
                $row['ip_address'] = "<a href='tracer.php?IPaddress={$ipEsc}'>{$ipEsc}</a>";
            }

            // Escape all other fields (ip_address already safe as anchor)
            foreach ($row as $k => $v) {
                if ($k !== 'ip_address') {
                    $row[$k] = htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
                }
            }

            $data[] = $row;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Data</title>
    <link rel="stylesheet" href="styles.css">
    <script>
        function filterTable() {
            var filter = (document.getElementById("filterInput").value || "").toLowerCase();
            var table = document.getElementById("dataTable");
            if (!table) return;
            var rows = table.getElementsByTagName("tr");

            for (var i = 1; i < rows.length; i++) {
                var cells = rows[i].getElementsByTagName("td");
                var matchFound = false;
                for (var j = 0; j < cells.length; j++) {
                    if ((cells[j].textContent || '').toLowerCase().includes(filter)) {
                        matchFound = true; break;
                    }
                }
                rows[i].style.display = matchFound ? "" : "none";
            }
        }

        function clearFilter() {
            document.getElementById("filterInput").value = "";
            filterTable();
        }
    </script>


<style>
  /* Fixed-width table so columns behave consistently */
  table.table-fixed { table-layout: fixed; width: 100%; border-collapse: collapse; }
  table.table-fixed th, table.table-fixed td { border: 1px solid #ddd; padding: 8px; vertical-align: top; }

  /* Precise column sizing */
  col.col-1 { width: 55px; }  /*ID */
  col.col-2 { width: 90px; }  /* IP */
  col.col-3 { width: 110px; }   /*location */
  col.col-4 { width: 100px; }  /* time and date */
  col.col-5 { width: 230px; }  /* previous page */
  col.col-6 { width: 100px; }   /* referrer */
  col.col-7 { width: 250px; }   /* current page */
  /* No wrap for date/time *
  .nowrap { white-space: nowrap; }

</style>



</head>

<body>
    <h1>Visitors</h1>

    <input type="text" id="filterInput" placeholder="Search..." oninput="filterTable()" style="width: 50%;">
    <button onclick="filterTable()">Filter</button>
    <button onclick="clearFilter()">Clear</button>

    <?php if (!empty($data)): ?>
        <table id="dataTable" border="1" class="table-fixed">
  <colgroup>
    <col class="col-1" />
    <col class="col-2" />
    <col class="col-3" />
    <col class="col-4" />
    <col class="col-5" />
    <col class="col-6" />
    <col class="col-7" />
  </colgroup>
            <thead>
                <tr>
                    <?php
                    $headers = array_keys($data[0]);
                    foreach ($headers as $header) {
                        echo "<th>" . htmlspecialchars($header, ENT_QUOTES, 'UTF-8') . "</th>";
                    }
                    ?>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($data as $row) {
                    echo "<tr>";
                    foreach ($row as $value) {
                        echo "<td>{$value}</td>";
                    }
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No data found.</p>
    <?php endif; ?>
</body>
</html>
