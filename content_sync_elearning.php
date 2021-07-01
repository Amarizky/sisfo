<style>
    .loading {
        border: 4px solid #f3f3f3;
        border-radius: 50%;
        border-top: 4px solid #005DAE;
        width: 40px;
        height: 40px;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }
</style>

<?php
$db = new mysqli('localhost', 'karangpucung', '#SemarangHebat1', 'sisfo');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_POST['aksi'] === "sinkron" && $_POST['sinkron'] === "jadwal" && isset($_POST['sesi']) && isset($_POST['tahun'])) {
        $sesi       = $_POST['sesi'];
        $sesistr    = ($sesi == 1 ? "ganjil" : "genap");
        $tahun      = $_POST['tahun'];

        $syncTable   = "enrol_" . $tahun . "_" . $sesistr;

        $sqls = [
            "TRUNCATE TABLE enrolment;",


            "INSERT INTO enrolment (userid, mkid, role) 
             SELECT 
                 sisfo.student.StudentID AS userid, 
                 sisfo.jadwal.JadwalID AS mkid, 
                 'student' AS role 
             FROM 
                 sisfo.student, 
                 sisfo.khs 
             JOIN sisfo.jadwal ON sisfo.khs.Kelas = sisfo.jadwal.KelasID 
             WHERE 
             (
                 sisfo.student.StudentID = sisfo.khs.StudentID 
                 and (sisfo.khs.TahunID = '$tahun') 
                 AND (sisfo.khs.Sesi = '$sesi')
             );
            ",


            "INSERT INTO enrolment (userid, mkid, role) 
             SELECT 
             lcase(sisfo.jadwal.TeacherID) AS `userid`, 
             sisfo.jadwal.JadwalID AS `mkid`, 
             'editingteacher' AS `role` 
             FROM 
             `sisfo`.`jadwal`, 
             `sisfo`.`master_prodi` 
             WHERE 
             (
                 (`sisfo`.`jadwal`.`TahunID` = '2020') 
                 AND (`sisfo`.`jadwal`.`Sesi` = '2') 
                 AND (`sisfo`.`jadwal`.`TeacherID` <> _latin1 '-') 
                 AND (`sisfo`.`jadwal`.`JadwalID` != 0) 
                 AND REPLACE (`sisfo`.`jadwal`.`ProdiID`, '.', '') 
                     = `sisfo`.`master_prodi`.`ProdiID`
             );
            ",


            "CREATE TABLE IF NOT EXISTS `$syncTable` (SyncID INT NOT NULL, JadwalID INT NOT NULL);",


            "TRUNCATE TABLE `$syncTable`;",


            "INSERT INTO `$syncTable` (JadwalID) SELECT JadwalID FROM `jadwal` WHERE TahunID='$tahun' AND Sesi='$sesi';",

        ];

        // insert users + jadwal ke enrolment
        foreach ($sqls as $sql) {
            if ($db->query($sql) !== TRUE) {
                echo "<script language='javascript'>alert('Error! Silahkan hubungi developer: $db->error')</script>";
            }
        }

        // sinkronisasi dari sisfo ke elearning
        echo '<pre>';
        passthru('php ../moodle/admin/cli/scheduled_task.php --execute="\\enrol_database\\task\\sync_enrolments"');
        echo '</pre>';
    } else if ($_POST['aksi'] === "sinkron" && $_POST['sinkron'] === "pengguna") {
        // sinkronisasi dari sisfo ke elearning
        echo '<pre>';
        passthru('php ../moodle/admin/cli/scheduled_task.php --execute="\\auth_db\task\sync_users"');
        echo '</pre>';
    }
} else {

    if (isset($_GET['semester']) && isset($_GET['tahun'])) :
        $tahun      = $_GET['tahun'];
        $sesi       = $_GET['semester'];
        $sesistr    = ($sesi == 1 ? "ganjil" : "genap");

        if ($db->query("SHOW TABLES LIKE 'enrol_" . $tahun . "_" . $sesistr . "'")->num_rows > 0) {
            $sql_cari = "SELECT j.ProdiID, j.KelasID, j.HariID, j.JamMulai, j.JamSelesai, mk.Nama, t.Nama Guru, e.JadwalID Sync
                        FROM jadwal j JOIN mk ON j.MKID=mk.MKID JOIN teacher t ON j.TeacherID=t.TeacherID LEFT JOIN enrol_" . $tahun . "_" . $sesistr . " e ON j.JadwalID=e.JadwalID
                        WHERE j.TahunID='$tahun' AND j.Sesi='$sesi';";
            
        } else {
            $sql_cari = "SELECT j.ProdiID, j.KelasID, j.HariID, j.JamMulai, j.JamSelesai, mk.Nama, t.Nama Guru, null Sync
                        FROM jadwal j JOIN mk ON j.MKID=mk.MKID JOIN teacher t ON j.TeacherID=t.TeacherID
                        WHERE j.TahunID='$tahun' AND j.Sesi='$sesi';";
        }

        $query_cari = $db->query($sql_cari);

        $i = 1;
        $hari = array('BT', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu');
    endif;

    function url()
    {
        return $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . explode('?', $_SERVER['REQUEST_URI'], 2)[0];
    }
?>

    <link rel="stylesheet" type="text/css" href="DataTables/datatables.min.css" />

    <div class="container">
        <div class="row">
            <div class="col-sm-4" style="padding: 10px; width: 360px;">
                <div class="panel panel-primary">
                    <div class="panel-heading"><b>Sinkronisasi Jadwal</b></div>
                    <div class="panel-body">
                        <form action="" method="GET" class="form-horizontal" style="margin-bottom: 0px;">
                            <input type="hidden" name="aksi" value="cari">
                            <input type="hidden" name="sinkron" value="jadwal">
                            <div class="form-group">
                                <label for="semester" class="control-label col-sm-3" style="padding-top: 3px;">Semester</label>
                                <div class="col-sm-9">
                                    <select name="semester" id="semester" class="form-control" style="width: 100%;" <?= $_GET['aksi'] == "sinkron" ? "disabled" : ""; ?>>
                                        <?php $get_semester = $_GET['semester'] ?? "-"; ?>
                                        <option <?= ($get_semester === "-" ? "selected" : ""); ?> disabled>Pilih semester</option>
                                        <option <?= ($get_semester === "1" ? "selected" : ""); ?> value="1">Ganjil</option>
                                        <option <?= ($get_semester === "2" ? "selected" : ""); ?> value="2">Genap</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="tahun" class="control-label col-sm-3" style="padding-top: 3px;">Tahun</label>
                                <div class="col-sm-9">
                                    <select name="tahun" id="tahun" class="form-control" style="width: 100%;" <?= $_GET['aksi'] == "sinkron" ? "disabled" : ""; ?>>
                                        <?php $get_tahun = $_GET['tahun'] ?? "-"; ?>
                                        <option <?= ($get_tahun === "-" ? "selected" : ""); ?> disabled>Pilih tahun</option>
                                        <?php
                                        $sql_tahun = "SELECT TahunID FROM tahun GROUP BY TahunID ORDER BY TahunID DESC;";
                                        $query_tahun = $db->query($sql_tahun);
                                        while ($data_tahun = $query_tahun->fetch_assoc()) :
                                        ?>
                                            <option <?= ($get_tahun === $data_tahun['TahunID'] ? "selected" : ""); ?> value="<?= $data_tahun['TahunID']; ?>">
                                                <?= $data_tahun['TahunID']; ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                            </div>

                            <div style="float: left;">
                                <button type="submit" class="btn btn-primary" <?= $_GET['aksi'] == "sinkron" ? "disabled" : ""; ?>>Cari Data</button>
                            </div>
                        </form>
                        <?php if (isset($_GET['semester']) && isset($_GET['tahun']) && $_GET['aksi'] === 'cari' && $query_cari->num_rows > 0) : ?>
                            <form action="<?= url(); ?>" method="GET" class="form-horizontal text-center">
                                <input type="hidden" name="aksi" value="sinkron">
                                <input type="hidden" name="sinkron" value="jadwal">
                                <input type="hidden" name="semester" value="<?= $_GET['semester']; ?>">
                                <input type="hidden" name="tahun" value="<?= $_GET['tahun']; ?>">
                                <div style="float: right;">
                                    <button type="submit" class="btn btn-primary" <?= $_GET['aksi'] == "sinkron" ? "disabled" : ""; ?>>Lakukan Sinkronisasi</button>
                                </div>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="col-sm-4" style="padding: 10px; width: 360px;">
                <div class="panel panel-primary">
                    <div class="panel-heading"><b>Sinkronisasi Pengguna</b></div>
                    <div class="panel-body">
                        <form action="<?= url(); ?>" method="GET" class="form-horizontal text-center">
                            <input type="hidden" name="aksi" value="sinkron">
                            <input type="hidden" name="sinkron" value="pengguna">
                            <input type="hidden" name="semester" value="<?= $_GET['semester']; ?>">
                            <input type="hidden" name="tahun" value="<?= $_GET['tahun']; ?>">
                            <button type="submit" class="btn btn-primary" <?= $_GET['aksi'] === "sinkron" ? "disabled" : ""; ?> style="width: 100%;">Lakukan Sinkronisasi</button>
                        </form>
                    </div>
                </div>
            </div>
            <?php
            if (isset($_GET['semester']) && isset($_GET['tahun']) && $_GET['aksi'] === 'sinkron') : ?>
                <div class="col-sm-4">
                    <br>
                    <br>
                    <p id="info1" class="text-center lead">Proses sinkronisasi sedang dilakukan, mohon ditunggu.</p>
                    <p id="info2" class="text-center lead">Dimohon untuk <b>tidak menutup</b> tab ini sampai proses selesai.</p>
                    <div class="text-center" style="display: flex; justify-content: center; align-items: center;">
                        <div class="loading" id="loading"></div>
                        <a href="<?= url() . "?aksi=cari&semester=" . $_GET['semester'] . "&tahun=" . $_GET['tahun']; ?>">
                            <button id="btnKembali" type="submit" class="btn btn-primary" style="display: none;">Kembali</button>
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        <div class="row">
            <div class="col">
                <?php if ($_GET['aksi'] == "sinkron" && isset($_GET['semester']) && isset($_GET['tahun'])) : ?>
                    <div id="loadSync"></div>
                <?php else : ?>
                    <table class="table table-bordered display nowrap" style="width: auto;" id="table-data">
                        <thead>
                            <tr>
                                <th style="width: 100px;" class="text-center">#</th>
                                <th style="width: 60px;">Prodi</th>
                                <th style="width: 105px;">Kelas</th>
                                <th style="width: 145px;">Hari, Jam</th>
                                <th style="width: 355px;">Mata Pelajaran</th>
                                <th style="width: 220px;">Guru</th>
                                <th style="width: 104px;">Sync</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if (isset($_GET['semester']) && isset($_GET['tahun'])) :
                                if ($query_cari->num_rows > 0) :
                                    $i = 1;
                                    while ($data_cari = $query_cari->fetch_array()) :
                            ?>
                                        <tr>
                                            <th scope="row" class="text-center"><?= $i++; ?></th>
                                            <td><?= $data_cari['ProdiID']; ?></td>
                                            <td><?= $data_cari['KelasID']; ?></td>
                                            <td><?= $hari[$data_cari['HariID']] . ", " . substr($data_cari['JamMulai'], 0, 5) . "-" . substr($data_cari['JamSelesai'], 0, 5); ?></td>
                                            <td><?= $data_cari['Nama']; ?></td>
                                            <td><?= $data_cari['Guru']; ?></td>
                                            <td><?= $data_cari['Sync'] ? '✓' : '-' ?></td>
                                        </tr>
                                    <?php
                                    endwhile;
                                else :
                                    ?>
                                    <tr>
                                        <td class="text-center" colspan="7">Data tidak ditemukan</td>
                                    </tr>
                                <?php
                                endif;
                            else :
                                ?>
                                <tr>
                                    <td class="text-center" colspan="8">Silahkan cari data terlebih dahulu</td>
                                </tr>
                            <?php
                            endif;
                            ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- scripts -->
    <script type="text/javascript" src="jquery/jquery-3.6.0.min.js"></script>
    <script type="text/javascript" src="bootstrap3/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="DataTables/datatables.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#table-data').DataTable({
                // dom: 'Bfrtip',
                // buttons: [
                //     'copy', 'csv', 'excel', 'pdf', 'print'
                // ]
            });

            $('#loadSync').load('content_sync_elearning.php', {
                'aksi': 'sinkron',
                'sinkron': get('sinkron'),
                'sesi': $('#semester').val(),
                'tahun': $('#tahun').val()
            }, function() {
                $('#info1').text('Proses sinkronisasi sudah selesai');
                $('#info2').text('Silahkan tekan tombol di bawah untuk kembali');
                $('#loading').hide();
                $('#btnKembali').show();
            });
        });

        function get(name) {
            if (name = (new RegExp('[?&]' + encodeURIComponent(name) + '=([^&]*)')).exec(location.search))
                return decodeURIComponent(name[1]);
        }
    </script>

<?php
}
?>