<?php
    error_reporting(0);
    $conn = mysqli_connect("localhost", "root", "", "rumah sakit");
?>
<html>
    <head></head>
        <body>
            <a href='dokter_add.php'>Tambah Data</a>
            <table cellpadding="5" cellspacing="0" border='1'>
                <tr>
                    <td>No.</td>
                    <td>Username</td>
                    <td>Password</td>
                    <td>Nama Dokter</td>
                    <td>Alamat</td>
                    <td>Tanggal Lahir</td>
                    <td>Jenis Kelamin</td>
                    <td>Spesialis</td>
                    <td>Action</td>
                </tr>
                <?php
                $no= 0;
                $query = mysqli_query($conn, "SELECT * FROM dokter order by id_dokter ASC");
                while ($data = mysqli_fetch_array($query))
                {
                        $no++;
                ?>
                <tr>
                    <td><?php echo $no; ?></td>
                    <td><?php echo $data["username"]; ?></td>
                    <td><?php echo $data["password"]; ?></td>
                    <td><?php echo $data["nama_dokter"]; ?></td>
                    <td><?php echo $data["alamat"]; ?></td>
                    <td><?php echo $data["tgl_lahir"]; ?></td>
                    <td><?php echo $data["jenis_kelamin"]; ?></td>
                    <td><?php echo $data["Spesialis"]; ?></td>
                    <td><a href="dokter_Edit.php?id_dokter=<?php echo $data["id_dokter"]; ?>">Edit</a> <a href='dokter_delete.php?id_dokter=<?php echo $data["id_dokter"]; ?>'> Hapus</a></td>
                </tr>
                <?php
                }
                ?>
            </table>
        </body>
</html>