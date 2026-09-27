<?php
include 'koneksi.php';

$id = "";
$nim = "";
$name = "";
$gender = "";
$major = "";
$hobby_array = array();

if (!($db instanceof PDO)) {
    throw new RuntimeException('Database connection is not available.');
}

// 1. PROSES INSERT & UPDATE (Prepared Statements)
if (isset($_POST['tombol_simpan'])) {
    $id = $_POST['id'];
    $nim = $_POST['nim'];
    $name = $_POST['name'];
    $gender = $_POST['gender'];
    $major = $_POST['major'];
    $hobbies = array();

    if (isset($_POST['hobby']) && is_array($_POST['hobby'])) {
        $hobbies = $_POST['hobby'];
    }

    if (!empty($_POST['hobby_lainnya'])) {
        $hobbies[] = trim($_POST['hobby_lainnya']);
    }

    $hobbies = array_filter($hobbies, 'strlen'); 
    $hobby = implode(", ", $hobbies); 

    if ($id == "") {
        $insert = "INSERT INTO student (nim, name, gender, major, hobby) VALUES (:nim, :name, :gender, :major, :hobby)";
        $prepared = $db->prepare($insert);
        $prepared->execute(array(
            'nim' => $nim,
            'name' => $name,
            'gender' => $gender,
            'major' => $major,
            'hobby' => $hobby
        ));
    } else {
        $update = "UPDATE student SET nim = :nim, name = :name, gender = :gender, major = :major, hobby = :hobby WHERE id = :id";
        $prepared = $db->prepare($update);
        $prepared->execute(array(
            'id' => $id,
            'nim' => $nim,
            'name' => $name,
            'gender' => $gender,
            'major' => $major,
            'hobby' => $hobby
        ));
    }

    header("Location: index.php");
    exit();
}

// 2. AMBIL DATA UNTUK EDIT
if (isset($_GET['action']) && $_GET['action'] == 'edit') {
    $id = $_GET['id'];
    $select = "SELECT * FROM student WHERE id = :id";
    $prepared = $db->prepare($select);
    $prepared->execute(array('id' => $id));
    $data = $prepared->fetch(PDO::FETCH_ASSOC);

    if ($data) {
        $nim = $data['nim'];
        $name = $data['name'];
        $gender = $data['gender'];
        $major = $data['major'];
        $hobby_array = explode(", ", $data['hobby']);
    }
}

// 3. PROSES DELETE
if (isset($_GET['action']) && $_GET['action'] == 'delete') {
    $id = $_GET['id'];
    $delete = "DELETE FROM student WHERE id = :id";
    $prepared = $db->prepare($delete);
    $prepared->execute(array('id' => $id));

    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>CRUD Mahasiswa</title>
</head>
<body>

    <h2>Form Input Mahasiswa</h2>

    <form action="index.php" method="POST">
        <input type="hidden" name="id" value="<?php echo $id; ?>">

        <table cellpadding="5">
            <tr>
                <td>NIM</td>
                <td>:</td>
                <td><input type="text" name="nim" value="<?php echo $nim; ?>" required></td>
            </tr>
            <tr>
                <td>Nama</td>
                <td>:</td>
                <td><input type="text" name="name" value="<?php echo $name; ?>" required></td>
            </tr>
            <tr>
                <td>Jenis Kelamin</td>
                <td>:</td>
                <td>
                    <input type="radio" name="gender" value="Laki-laki" <?php if ($gender == "Laki-laki") echo "checked"; ?> required> Laki-laki
                    <input type="radio" name="gender" value="Perempuan" <?php if ($gender == "Perempuan") echo "checked"; ?> required> Perempuan
                </td>
            </tr>
            <tr>
                <td>Jurusan</td>
                <td>:</td>
                <td>
                    <select name="major" required>
                        <option value="">-- Pilih Jurusan --</option>
                        <option value="Teknik Informatika" <?php if ($major == "Teknik Informatika") echo "selected"; ?>>Teknik Informatika</option>
                        <option value="Teknik Komputer" <?php if ($major == "Teknik Komputer") echo "selected"; ?>>Teknik Komputer</option>
                        <option value="Sistem Informasi" <?php if ($major == "Sistem Informasi") echo "selected"; ?>>Sistem Informasi</option>
                        <option value="Teknologi Informasi" <?php if ($major == "Teknologi Informasi") echo "selected"; ?>>Teknologi Informasi</option>
                        <option value="Pendidikan Teknologi Informasi" <?php if ($major == "Pendidikan Teknologi Informasi") echo "selected"; ?>>Pendidikan Teknologi Informasi</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Hobi</td>
                <td>:</td>
                <td>
                    <input type="checkbox" name="hobby[]" value="Membaca" <?php if (in_array("Membaca", $hobby_array)) echo "checked"; ?>> Membaca
                    <input type="checkbox" name="hobby[]" value="Coding" <?php if (in_array("Coding", $hobby_array)) echo "checked"; ?>> Coding
                    <input type="text" name="hobby[]" value="<?php echo implode(", ", array_diff($hobby_array, ["Membaca", "Coding"])); ?>" placeholder="Hobi lainnya">
                </td>
            </tr>
            <tr>
                <td colspan="3">
                    <button type="submit" name="tombol_simpan">Simpan Data</button>
                    <a href="index.php"><button type="button">Batal / Reset</button></a>
                </td>
            </tr>
        </table>
    </form>

    <hr>

    <h2>Daftar Student</h2>

    <table border="1" cellpadding="8" cellspacing="0">
        <thead>
            <tr>
                <th>ID</th>
                <th>NIM</th>
                <th>Nama</th>
                <th>Jenis Kelamin</th>
                <th>Jurusan</th>
                <th>Hobi</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Fetch seluruh data menggunakan FETCH_OBJ
            $sql = "SELECT * FROM student";
            $query = $db->query($sql);
            $rows = $query->fetchAll(PDO::FETCH_OBJ);

            if (count($rows) > 0) {
                foreach ($rows as $row) {
            ?>
                    <tr>
                        <td><?php echo $row->id; ?></td>
                        <td><?php echo $row->nim; ?></td>
                        <td><?php echo $row->name; ?></td>
                        <td><?php echo $row->gender; ?></td>
                        <td><?php echo $row->major; ?></td>
                        <td><?php echo $row->hobby; ?></td>
                        <td>
                            <a href="index.php?action=edit&id=<?php echo $row->id; ?>">Edit</a> | 
                            <a href="index.php?action=delete&id=<?php echo $row->id; ?>">Hapus</a>
                        </td>
                    </tr>
            <?php
                }
            } else {
                echo '<tr><td colspan="7" align="center">Belum ada data student</td></tr>';
            }
            $db = null;
            ?>
        </tbody>
    </table>

</body>
</html>