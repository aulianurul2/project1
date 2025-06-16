<?php
include "../koneksi.php";

$query = mysqli_query($conn, "SELECT * FROM meja");

while ($row = mysqli_fetch_assoc($query)) {
    echo "<div style='margin-bottom:30px;'>";
    echo "<h3>" . htmlspecialchars($row['nomor_meja']) . "</h3>";
    echo '<img src="/projek1/uploads/barcode/' . htmlspecialchars($row['barcode_image']) . '" alt="' . htmlspecialchars($row['nomor_meja']) . '" style="width:200px; height:auto;" />';
    echo "</div>";
}
?>
