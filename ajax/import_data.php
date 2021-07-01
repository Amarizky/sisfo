<?php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') die();
function echoo($text = "", $bottomBreaks = 1, $topBreaks = 0)
{
    for ($i = 0; $i < $topBreaks; $i++) echo '<br>';
    echo $text;
    for ($i = 0; $i < $bottomBreaks; $i++) echo '<br>';
}
function headerAsKeys($roww)
{
    $header_values = $rows = [];
    foreach ($roww as $k => $r) {
        if ($k === 0) {
            $header_values = $r;
            continue;
        }
        $rows[] = array_combine($header_values, $r);
    }
    return $rows;
}


$target_file = '../upload/' . basename($_FILES['fileInput']['name']);
if (file_exists($target_file)) unlink($target_file);

echoo('<b>Mengunggah file</b>');
if (move_uploaded_file($_FILES['fileInput']['tmp_name'], $target_file)) {
    echoo('File berhasil diunggah...', 2);
}

$baseName = basename($target_file);
if (strcmp($baseName, 'GURU.xlsx') === 0) $table = 'GURU';
else list($table) = explode('-', $baseName);

$isJadwal = (strcmp($table, 'JADWAL') === 0);
$isSiswa = (strcmp($table, 'SISWA') === 0);
$isGuru = (strcmp($table, 'GURU') === 0);

if ($isJadwal) list($table, $tahun, $semester) = explode('-', $baseName);
else if ($isSiswa) {
    list($table, $tahun) = explode('-', $baseName);
    $semester = 'GENAP';
} else {
    $tahun = '';
    $semester = '';
}
$table = str_replace('.xlsx', '', $table);
$tahun = str_replace('.xlsx', '', $tahun);
$semester = str_replace('.xlsx', '', $semester);

if (!$isJadwal && !$isSiswa && !$isGuru) {
    echoo('<b>Silahkan beri nama file sesuai instruksi</>');
    echoo('Proses impor dihentikan.');
    exit();
}

if (($isJadwal || $isSiswa) && !is_numeric($tahun)) {
    echoo('<b>Silahkan beri nama file sesuai instruksi</b>');
    echoo('Proses impor dihentikan.');
    exit();
}

if ($isJadwal && !strcmp($semester, 'GENAP') === 0 && !strcmp($semester, 'GANJIL') === 0) {
    echoo('<b>Silahkan beri nama file sesuai instruksi</b>');
    echoo('Proses impor dihentikan.');
    exit();
}


require_once('../simplexlsx/SimpleXLSX.php');

if ($xlsx = SimpleXLSX::parse($target_file)) {
} else {
    die(SimpleXLSX::parseError());
}


echoo('<b>Memproses file ' . $baseName . '</b>');
echoo('Tabel            : ' . $table);
if ($isJadwal || $isSiswa) echoo('Tahun            : ' . $tahun);
if ($isJadwal) echoo('Semester         : ' . $semester);
echoo('Sheet ditemukan  : ' . implode(', ', $xlsx->sheetNames()), 1);

$table = strtolower($table);
$semester = strtolower($semester);
if ($isJadwal) $sesi = (strcmp($semester, 'ganjil') === 0) ? '1' : '2';
else $sesi = '1,2';

$kampusId = 'SMKNKRPC';
$createDate = date('Y-m-d h:i:s');
$creator = 'admin';
$NA = 'N';

$conn = new mysqli('localhost', 'karangpucung', '#SemarangHebat1', 'sisfo');
if ($conn->connect_error) {
    echoo('Gagal melakukan koneksi ke database');
    die('Error: ' . $conn->connect_error);
}

if ($isJadwal || $isSiswa) {
    echoo("Mencari tahun $tahun di database...", 1, 1);
    $temuTahun = $conn->query('SELECT TahunID FROM tahun WHERE TahunID="' . $tahun . '";');
    if ($temuTahun->num_rows < 1) {
        echoo('Tahun ' . $tahun . ' tidak ditemukan.', 1);
        echoo('Menambahkan tahun ' . $tahun . ' ke database...', 2);

        $stmtTahun = $conn->prepare('INSERT INTO tahun (TahunID, Sesi, ProdiID, Nama, CreateDate, Creator, NA) VALUES (?, ?, ?, ?, ?);');
        $prodiIds = ['AKL', 'BDP', 'TBSM', 'TITL', 'TKRO'];
        $prodiNames = ['Akuntansi dan Keuangan Lembaga', 'Bisnis Daring dan Pemasaran', 'Teknik Bisnis Sepeda Motor', 'Teknik Instalasi Tenaga Listrik', 'Teknik Kendaraan Ringan'];

        for ($i = 0; $i < count($prodiIds); $i++) {
            $stmtTahun->bind_param('sssssss', $tahun, $sesi, $prodiIds[$i], $prodiNames[$i], $createDate, $creator, $NA);
            $stmtTahun->execute();
            $stmtTahun->reset();
        }
    } else {
        echoo("Tahun $tahun ditemukan.", 1);
    }
}

if ($isSiswa) {
    $cAll = 0;
    $cAllFail = 0;
    for ($i = 0; $i < count($xlsx->sheetNames()); $i++) {
        echoo('<b>Memproses data dari sheet ' . $xlsx->sheetNames()[$i] . '</b>', 1, 1);

        if (count($xlsx->rows($i)) < 1) {
            echoo('Data tidak ditemukan di sheet ini');
            continue;
        }

        $rows = headerAsKeys($xlsx->rows($i));
        $cSheet = 0;
        $cFail = 0;

        foreach ($rows as $row) {
            $cSheet++;
            $cAll++;

            if (empty($row['NIS']) || empty($row['NAMA']) || empty($row['KELAS']) || empty($row['JK'])) {
                echoo('Mengabaikan baris ke-' . $cSheet . ' karena tidak lengkap. (' . $xlsx->sheetNames()[$i] . '-' . $cSheet . ')');
                $cFail++;
                $cAllFail++;
                continue;
            }

            $stmtSiswa = $conn->prepare('INSERT INTO student (
                StudentID, LevelID, Password, TahunID, Nama, 
                StudentStatusID, ProdiID, Kelamin, NA, Creator, 
                CreateDate, KampusID
            ) VALUES (
                ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
            );');

            $studentStatusId = 'A';
            $levelId = '120';
            list($tingkat, $prodiId, $kelas) = explode(' ', $row['KELAS']);
            $kelasId = $tingkat . $prodiId . $kelas;

            $stmtSiswa->bind_param(
                'sssissssssss',
                $row['NIS'],        // StudentID
                $levelId,           // LevelID
                $row['NIS'],        // Password
                $tahun,             // TahunID
                $row['NAMA'],       // Nama
                $studentStatusId,   // StudentStatusID
                $prodiId,           // ProdiID
                $row['JK'],         // Kelamin
                $NA,                // NA
                $creator,           // Creator
                $createDate,        // CreateDate
                $kampusId           // KampusID
            );

            $searchDuplicate = $conn->query('SELECT StudentID FROM student WHERE StudentID="' . $row['NIS'] . '";');
            if ($searchDuplicate->num_rows > 0) {
                echoo('Siswa dengan NIS ' . $row['NIS'] . ' sudah ada. (' . $xlsx->sheetNames()[$i] . '-' . $cSheet . ')');
                $cFail++;
                $cAllFail++;
                continue;
            }

            $stmtSiswa->execute();
            $stmtSiswa->reset();

            $stmtKhs = $conn->prepare('INSERT INTO khs (
                ProdiID, TahunID, Tingkat, Sesi, Kelas, StudentID, StatusStudentID, Creator, CreateDate, NA
            ) VALUES (
                ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
            );');

            $stmtKhs->bind_param(
                'ssssssssss',
                $prodiId,           // ProdiID
                $tahun,             // TahunID
                $tingkat,           // Tingkat
                $sesi,              // Sesi
                $kelasId,           // Kelas
                $row['NIS'],        // StudentID
                $studentStatusId,   // StatusStudentID
                $creator,           // Creator
                $createDate,        // CreateDate
                $NA                 // NA
            );

            $stmtKhs->execute();
            $stmtKhs->reset();
        }
        echoo('Berhasil mengimpor ' . ($cSheet - $cFail) . ' data siswa dari sheet ' . $xlsx->sheetNames()[$i] . ' ke database.');
    }
    echoo('<b>Total ' . ($cAll - $cAllFail) . ' data siswa berhasil dimasukkan ke database.</b>', 1, 2);
} else if ($isGuru) {
    $cAll = 0;
    $cAllFail = 0;
    for ($i = 0; $i < count($xlsx->sheetNames()); $i++) {
        echoo('<b>Memproses data dari sheet ' . $xlsx->sheetNames()[$i] . '</b>', 1, 1);

        if (count($xlsx->rows($i)) < 1) {
            echoo('Data tidak ditemukan di sheet ini');
            continue;
        }

        $rows = headerAsKeys($xlsx->rows($i));
        $cSheet = 0;
        $cFail = 0;

        foreach ($rows as $row) {
            $cSheet++;
            $cAll++;

            if (empty($row['NIK']) || empty($row['NAMA']) || empty($row['KODE']) || empty($row['JK'])) {
                echoo('Mengabaikan baris ke-' . $cSheet . ' karena tidak lengkap. (' . $xlsx->sheetNames()[$i] . '-' . $cSheet . ')');
                $cFail++;
                $cAllFail++;
                continue;
            }

            $stmt = $conn->prepare('INSERT INTO teacher (
                TeacherID, LevelID, AliasCode, NIPPNS,
                Nama, KTP, Password, KelaminID, Creator,
                CreateDate, NA
            ) VALUES (
                ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
            );');

            $levelId = '100';
            $teacherId = (!empty($row['NIP']) ? $row['NIP'] : (!empty($row['NUPTK']) ? $row['NUPTK'] : $row['NIK']));
            $teacherId = str_replace(' ', '', $teacherId);
            $nip = $row['NIP'] ?? null;
            $password = 'karangpucung';

            $stmt->bind_param(
                'sssssssssss',
                $teacherId,     // TeacherID
                $levelId,       // LevelID
                $row['KODE'],   // AliasCode
                $row['NIP'],    // NIPPNS
                $row['NAMA'],   // Nama
                $row['NIK'],    // KTP
                $password,      // Password
                $row['JK'],     // KelaminID
                $creator,       // Creator
                $createDate,    // CreateDate
                $NA             // NA
            );

            $searchDuplicate = $conn->query('SELECT KTP FROM teacher WHERE KTP="' . $row['NIK'] . '";');
            if ($searchDuplicate->num_rows > 0) {
                echoo('Guru dengan NIK ' . $row['NIK'] . ' sudah ada. (' . $cSheet . ')');
                $cFail++;
                $cAllFail++;
                continue;
            }

            $stmt->execute();
            $stmt->reset();
        }
        echoo('Berhasil mengimpor ' . ($cSheet - $cFail) . ' data guru dari sheet ' . $xlsx->sheetNames()[$i] . ' ke database.', 1, 1);
    }
    echoo('<b>Total ' . ($cAll - $cAllFail) . ' data guru berhasil dimasukkan ke database.</b>', 1, 2);
} else if ($isJadwal) {
    $cAll = 0;
    $cAllFail = 0;
    for ($i = 0; $i < count($xlsx->sheetNames()); $i++) {
        echoo('<b>Memproses data dari sheet ' . $xlsx->sheetNames()[$i] . '</b>', 1, 1);

        if (count($xlsx->rows($i)) < 1) {
            echoo('Data tidak ditemukan di sheet ini');
            exit();
        }

        $rows = headerAsKeys($xlsx->rows($i));
        $cSheet = 0;
        $cFail = 0;

        foreach ($rows as $row) {
            $cSheet++;
            $cAll++;

            if (empty($row['KELAS']) || empty($row['HARI']) || empty($row['JAM']) || empty($row['MAPEL']) || empty($row['GURU'])) {
                echoo('Mengabaikan baris ke-' . $cSheet . ' karena tidak lengkap. (' . $xlsx->sheetNames()[$i] . '-' . $cSheet . ')');
                $cFail++;
                $cAllFail++;
                continue;
            }

            if (strcmp($row['MAPEL'], 'UB') === 0) {
                echoo('Mengabaikan Upacara Bendera. (' . $xlsx->sheetNames()[$i] . '-' . $cSheet . ')');
                $cFail++;
                $cAllFail++;
                continue;
            }

            $stmt = $conn->prepare('INSERT INTO jadwal (
                TahunID, Sesi, ProdiID, KelasID, 
                Tingkat, HariID, JamID, JamMulai, 
                JamSelesai, MKID, TeacherID, 
                CreateDate, Creator, NA
            ) VALUES (
                ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
            );');

            $stmt->bind_param(
                'sssssisssissss',
                $tahun,         // TahunID
                $sesi,          // Sesi
                $prodiId,       // ProdiID
                $kelasId,       // KelasID
                $tingkat,       // Tingkat
                $hariId,        // HariID
                $jamId,         // JamID
                $jamMulai,      // JamMulai
                $jamSelesai,    // JamSelesai
                $mkId,          // MKID
                $teacherId,     // TeacherID
                $createDate,    // CreateDate
                $creator,       // Creator
                $NA             // NA
            );

            list($tingkat, $prodiId, $kelas) = explode(' ', $row['KELAS']);
            $kelasId = $tingkat . $prodiId . $kelas;
            $hariId = dayToNum(strtolower($row['HARI']));
            $jamId = $row['JAM'];
            list($jamMulai, $jamSelesai) = jamIdToJam($hariId, $jamId);

            if ($tingkat !== 'XII')
                $cariMKID = 'SELECT MKID FROM mk WHERE ProdiID="' . $prodiId . '" AND (Tingkat="' . $tingkat . '" OR Tingkat LIKE "%' . $tingkat . ',%") AND Singkatan="' . $row['MAPEL'] . '";';
            else
                $cariMKID = 'SELECT MKID FROM mk WHERE ProdiID="' . $prodiId . '" AND (Tingkat="' . $tingkat . '" OR Tingkat LIKE "%' . $tingkat . '%") AND Singkatan="' . $row['MAPEL'] . '";';
            $temuMKID = $conn->query($cariMKID);
            if ($temuMKID->num_rows !== 1) {
                echoo('Mata pelajaran dengan kode "' . $row['MAPEL'] . '" tidak ditemukan. (' . $cSheet . ')');
                continue;
            }
            $hasilMKID = $temuMKID->fetch_assoc();
            $mkId = $hasilMKID['MKID'];

            $cariTeacherId = 'SELECT TeacherID FROM teacher WHERE AliasCode="' . $row['GURU'] . '";';
            $temuTeacherId = $conn->query($cariTeacherId);
            if ($temuTeacherId->num_rows !== 1) {
                echoo('Guru dengan kode "' . $row['GURU'] . '" tidak ditemukan. (' . $xlsx->sheetNames()[$i] . '-' . $cSheet . ')');
                continue;
            }
            $hasilTeacherId = $temuTeacherId->fetch_assoc();
            $teacherId = $hasilTeacherId['TeacherID'];

            $querySearchDupe = "SELECT JadwalID FROM jadwal WHERE TahunID='$tahun' AND Sesi='$sesi' AND KelasID='$kelasId' AND HariID='$hariId' AND JamID='$jamId' AND MKID='$mkId' AND TeacherID='$teacherId';";
            $searchDuplicate = $conn->query($querySearchDupe);
            if ($searchDuplicate->num_rows > 0) {
                echoo('Ditemukan jadwal duplikat. (' . $xlsx->sheetNames()[$i] . '-' . $cSheet . ')');
                $cFail++;
                $cAllFail++;
                continue;
            }

            $stmt->execute();
            $stmt->reset();
        }
        echoo('Berhasil mengimpor ' . ($cSheet - $cFail) . ' data jadwal dari sheet ' . $xlsx->sheetNames()[$i] . ' ke database.');
    }
    echoo('<b>Total ' . ($cAll - $cAllFail) . ' data jadwal berhasil dimasukkan ke database.</b>', 1, 2);
}

function jamIdToJam($hariId, $jamId)
{
    if ($hariId !== 5) { //jika bukan jumat
        switch ($jamId) {
            case '1':
                return array('07:15:00', '08:00:00');
                break;
            case '2':
                return array('08:00:00', '08:45:00');
                break;
            case '3':
                return array('08:45:00', '09:30:00');
                break;
            case '4':
                return array('09:30:00', '10:15:00');
                break;
            case '5':
                return array('10:30:00', '11:15:00');
                break;
            case '6':
                return array('11:15:00', '12:00:00');
                break;
            case '7':
                return array('12:30:00', '13:15:00');
                break;
            case '8':
                return array('13:15:00', '13:55:00');
                break;
            case '9':
                return array('13:55:00', '14:35:00');
                break;
            case '10':
                return array('14:35:00', '15:15:00');
                break;
            case '11':
                return array('15:15:00', '16:00:00');
                break;
            default:
                return array('00:00:00', '00:00:00');
        }
    } else { //jika jumat
        switch ($jamId) {
            case '1':
                return array('06:50:00', '07:25:00');
                break;
            case '2':
                return array('07:25:00', '08:05:00');
                break;
            case '3':
                return array('08:05:00', '08:45:00');
                break;
            case '4':
                return array('08:45:00', '09:25:00');
                break;
            case '5':
                return array('09:25:00', '10:05:00');
                break;
            case '6':
                return array('10:20:00', '11:00:00');
                break;
            case '7':
                return array('11:00:00', '11:40:00');
                break;
            case '8':
                return array('13:15:00', '13:55:00');
                break;
            case '9':
                return array('13:55:00', '14:35:00');
                break;
            case '10':
                return array('14:35:00', '15:15:00');
                break;
            case '11':
                return array('15:15:00', '16:00:00');
                break;
            default:
                return array('00:00:00', '00:00:00');
        }
    }
}
function dayToNum($day)
{
    if (is_numeric($day)) return $day;
    switch ($day) {
        case 'senin':
            return 1;
            break;
        case 'selasa':
            return 2;
            break;
        case 'rabu':
            return 3;
            break;
        case 'kamis':
            return 4;
            break;
        case 'jumat':
        case "jum'at":
            return 5;
            break;
        case 'sabtu':
            return 6;
            break;
        case 'minggu':
            return 7;
            break;
        default:
            return 0;
    }
}
