<?php
    error_reporting(0);
    $conn = mysqli_connect("localhost", "root", "", "rumah sakit");
?>
<html>
    <head><title>Data Obat</title></head>
    <body>
        <a href='obat_add.php'>Tambah Data</a>
        <table cellpadding="5" cellspacing="0" border='1'>
            <tr>
                <td>No.</td>
                <td>Kode Obat</td>
                <td>Nama Obat</td>
                <td>Frekuensi Peresepan</td>
                <td>Action</td>
            </tr>
            <?php
            $no= 0;
            $query = mysqli_query($conn, "SELECT *, id as id_obat FROM obat ORDER BY id ASC");
            while ($data = mysqli_fetch_array($query))
            {
                $no++;
                $id_ob = $data['id'] ?? $data['id_obat'];
            ?>
            <tr>
                <td><?php echo $no; ?></td>
                <td><?php echo htmlspecialchars($data["kode_obat"]); ?></td>
                <td><?php echo htmlspecialchars($data["nama_obat"]); ?></td>
                <td><?php echo (int)($data["frekuensi_peresepan"] ?? 0); ?> kali</td>
                <td>
                    <a href="obat_Edit.php?id_obat=<?php echo $id_ob; ?>">Edit</a> 
                    <a href='obat_delete.php?id_obat=<?php echo $id_ob; ?>' onclick="return confirm('Hapus obat ini?')">Hapus</a>
                </td>
            </tr>
            <?php
            }
            ?>
        </table>
    </body>
</html>
