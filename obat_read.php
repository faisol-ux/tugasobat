<?php
    error_reporting(0);
    $conn = mysqli_connect("localhost", "root", "", "rumah sakit");
?>
<html>
    <head></head>
        <body>
            <a href='obat_add.php'>Tambah Data</a>
            <table cellpadding="5" cellspacing="0" border='1'>
                <tr>
                    <td>No.</td>
                    <td>Kode Obat</td>
                    <td>Nama Obat</td>
                    <td>Action</td>
                </tr>
                <?php
                $no= 0;
                $query = mysqli_query($conn, "SELECT * FROM obat order by id_obat ASC");
                while ($data = mysqli_fetch_array($query))
                {
                        $no++;
                ?>
                <tr>
                    <td><?php echo $no; ?></td>
                    <td><?php echo $data["kode_obat"]; ?></td>
                    <td><?php echo $data["nama_obat"]; ?></td>
                    <td><a href="obat_Edit.php?id_obat=<?php echo $data["id_obat"]; ?>">Edit</a> <a href='obat_delete.php?id_obat=<?php echo $data["id_obat"]; ?>'> Hapus</a></td>
                </tr>
                <?php
                }
                ?>
            </table>
        </body>
</html>