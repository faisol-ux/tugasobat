<?php
    error_reporting(0);
    $conn = mysqli_connect("localhost", "root", "", "rumah sakit");
?>
<html>
    <head></head>
        <body>
            <a href='pasien_add.php'>Tambah Data</a>
            <table cellpadding="5" cellspacing="0" border='1'>
                <tr>
                    <td>No.</td>
                    <td>Nomer Registrasi</td>
                    <td>Nama Pasien</td>
                    <td>Alamat</td>
                    <td>Tanggal Lahir</td>
                    <td>Action</td>
                </tr>
                <?php
                $no= 0;
                $query = mysqli_query($conn, "SELECT * FROM pasien order by id_pasien ASC");
                while ($data = mysqli_fetch_array($query))
                {
                        $no++;
                ?>
                <tr>
                    <td><?php echo $no; ?></td>
                    <td><?php echo $data["no_registrasi"]; ?></td>
                    <td><?php echo $data["nama_pasien"]; ?></td>
                    <td><?php echo $data["alamat"]; ?></td>
                    <td><?php echo $data["tgl_lahir"]; ?></td>
                    <td><a href="pasien_Edit.php?id=<?php echo $data["id"]; ?>">Edit</a> <a href='pasien_delete.php?id=<?php echo $data["id"]; ?>'> Hapus</a></td>
                </tr>
                <?php
                }
                ?>
            </table>
        </body>
</html>