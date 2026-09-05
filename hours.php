<?php
include('db.php'); // Include the database connection

// Query to fetch business hours
$sql = "SELECT * FROM hours ORDER BY FIELD(day, 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday')";
$result = $conn->query($sql);
?><center>
<table>
        <thead>
            <tr>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo ucfirst($row['day']); ?></td>
                        <td><?php echo $row['open_time'] ? date('G:i', strtotime($row['open_time'])) : 'Closed'; ?>am-</td>
                        <td><?php echo $row['close_time'] ?  date('G:i', strtotime($row['close_time'])) : 'Closed'; ?>pm</td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr
                    <td colspan="3">No hours available.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</center>
