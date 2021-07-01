<link rel="stylesheet" href="css/content_import_data.css">
<div class="container-fluid">
    <div class="row">
        <div class="col-sm-3">
            <div class="row">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <b>Templat</b>
                    </div>
                    <div class="panel-body">
                        <div class="form-group">
                            <button id="btnSiswa" class="btn btn-primary btn-block">Siswa ➤</button>
                        </div>
                        <div class="form-group">
                            <button id="btnGuru" class="btn btn-primary btn-block">Guru ➤</button>
                        </div>
                        <div class="form-group" style="margin-bottom: 0;">
                            <button id="btnJadwal" class="btn btn-primary btn-block">Jadwal ➤</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <b>Unggah File</b>
                    </div>
                    <div class="panel-body text-center">
                        <form id="uploadFile" method="POST" enctype="multipart/form-data">
                            <div id="drop_zone">
                                <img src="phpimages/file.png" alt="">
                                <p id="fileInfo">Unggah file di sini</p>
                                <input type="file" name="fileInput" id="fileInput" accept=".xlsx">
                            </div>
                            <div style="margin-top: 8px; width: 100%;">
                                <button id="btnUpload" class="btn btn-primary btn-block">Unggah dan Impor</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-3">
        </div>

        <!-- ------------ Templat Siswa ------------ -->
        <div id="penjelasanSiswa" class="col-sm-9">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <b>Penjelasan Templat Siswa</b>
                </div>
                <div class="panel-body">
                    <table id="tabelSiswa">
                        <tr>
                            <td style="width: 100px;"><b>Nama file:</b></td>
                            <td>
                                SISWA-TAHUN.xlsx<br>
                                Misalnya <abbr title="untuk impor data siswa, di tahun 2021">SISWA-2021</abbr>.xlsx
                            </td>
                        </tr>
                        <tr>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td colspan="2"><b>Kolom-kolom yang diperlukan:</b></td>
                        </tr>
                        <tr>
                            <td>NIS:</td>
                            <td>
                                Ditulis NIS siswa<br>
                                Misalnya 1804105
                            </td>
                        </tr>
                        <tr>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td>NAMA:</td>
                            <td>
                                Ditulis nama lengkap siswa dengan huruf kapital<br>
                                Misalnya <abbr title="untuk nama Abi Yuruf Latif Abdilah">ABI YUSUF LATIF ABDILAH</abbr> atau <abbr title="untuk nama Adhani Nanda Fatwa">ADHANI NANDA FATWA</abbr>
                            </td>
                        </tr>
                        <tr>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td>KELAS:</td>
                            <td>
                                Ditulis nama kelas lengkap dengan jurusannya<br>
                                Misalnya <abbr title="X(spasi)AKL(spasi)1">X AKL 1</abbr>, <abbr title="XI(spasi)BDP(spasi)2">XI BDP 2</abbr>, atau <abbr title="XII(spasi)TKRO(spasi)3">XII TKRO 3</abbr>
                            </td>
                        </tr>
                        <tr>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td>JK:</td>
                            <td>
                                Ditulis jenis kelamin siswa dengan singkatannya saja<br>
                                P untuk perempuan dan L untuk laki-laki
                            </td>
                        </tr>
                    </table>
                    <div class="gapSiswa"></div>
                    <p><b>Unduh file:</b></p>
                    <a href="upload/CONTOH-SISWA-2020.xlsx" class="btn btn-primary" style="width: 100px;">Contoh</a>
                    <a href="upload/CONTOH-GURU.xlsx" class="btn btn-primary" style="width: 100px;">Templat</a>
                </div>
            </div>
        </div>

        <!-- ------------ Templat Guru ------------ -->
        <div id="penjelasanGuru" class="col-sm-9">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <b>Penjelasan Templat Guru</b>
                </div>
                <div class="panel-body">
                    <table id="tabelGuru">
                        <tr>
                            <td style="width: 100px;"><b>Nama file:</b></td>
                            <td>
                                GURU.xlsx<br>
                            </td>
                        </tr>
                        <tr>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td colspan="2"><b>Kolom-kolom yang diperlukan:</b></td>
                        </tr>
                        <tr>
                            <td>NIP:</td>
                            <td>
                                Kolom ini bersifat opsional<br>
                                Ditulis Nomor Induk PNS (NIP). Diharapkan untuk menuliskan NIP dengan diberi spasi<br>
                                Misalnya <abbr title="19780715(spasi)200501(spasi)2(spasi)012">19780715 200501 2 012</abbr> atau <abbr title="19660208(spasi)200502(spasi)1(spasi)001">19660208 200502 1 001</abbr>
                            </td>
                        </tr>
                        <tr>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td>NUPTK:</td>
                            <td>
                                Kolom ini bersifat opsional<br>
                                Ditulis Nomor Unik Pendidik dan Tenaga Kependidikan (NUPTK)<br>
                                Misalnya 6459773674130052 atau 6316765666130173
                            </td>
                        </tr>
                        <tr>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td>NIK:</td>
                            <td>
                                Ditulis Nomor Induk Kependudukan (NIK)<br>
                                Misalnya 3301145507780001 atau 3301130802660005
                            </td>
                        </tr>
                        <tr>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td>NAMA:</td>
                            <td>
                                Ditulis nama lengkap guru beserta gelarnya<br>
                                Misalnya "Endang Muryati, S.Pd." atau "Drs. Mafudin" (tanpa kutip)
                            </td>
                        </tr>
                        <tr>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td>KODE:</td>
                            <td>
                                Ditulis kode guru<br>
                                Misalnya <abbr title="untuk Endang Muryati, S.Pd.">EM</abbr> atau <abbr title="untuk Drs. Mafudin">MF</abbr>
                            </td>
                        </tr>
                        <tr>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td>JK:</td>
                            <td>
                                Ditulis jenis kelamin guru dengan singkatannya saja<br>
                                P untuk perempuan dan L untuk laki-laki
                            </td>
                        </tr>
                    </table>
                    <div class="gapGuru"></div>
                    <p><b>Unduh file:</b></p>
                    <a href="upload/CONTOH-GURU.xlsx" class="btn btn-primary" style="width: 100px;">Contoh</a>
                    <a href="upload/TEMPLAT-GURU.xlsx" class="btn btn-primary" style="width: 100px;">Templat</a>
                </div>
            </div>
        </div>

        <!-- ------------ Templat Jadwal ------------ -->
        <div id="penjelasanJadwal" class="col-sm-9">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <b>Penjelasan Templat Jadwal</b>
                </div>
                <div class="panel-body">
                    <table id="tabelJadwal">
                        <tr>
                            <td style="width: 100px;"><b>Nama file:</b></td>
                            <td>
                                JADWAL-TAHUN-SEMESTER.xlsx<br>
                                Misalnya <abbr title="untuk impor data Jadwal, di tahun 2021, di semester genap">JADWAL-2021-GENAP</abbr>.xlsx
                            </td>
                        </tr>
                        <tr>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td colspan="2"><b>Kolom-kolom yang diperlukan:</b></td>
                        </tr>
                        <tr>
                            <td>KELAS:</td>
                            <td>
                                Ditulis nama kelas lengkap dengan jurusannya<br>
                                Misalnya <abbr title="X(spasi)AKL(spasi)1">X AKL 1</abbr>, <abbr title="XI(spasi)BDP(spasi)2">XI BDP 2</abbr>, dan <abbr title="XII(spasi)TKRO(spasi)3">XII TKRO 3</abbr>
                            </td>
                        </tr>
                        <tr>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td>HARI:</td>
                            <td>
                                Ditulis dengan nama hari atau cukup dengan nomor saja<br>
                                Misalnya untuk hari Senin bisa ditulis dengan "Senin" atau "1" (tanpa kutip)
                            </td>
                        </tr>
                        <tr>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td>JAM:</td>
                            <td>
                                Ditulis jam ke berapa mata pelajaran dilaksanakan<br>
                                Misalnya untuk jam ke-2, tulis "2" saja (tanpa kutip)
                            </td>
                        </tr>
                        <tr>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td>MAPEL:</td>
                            <td>
                                Ditulis untuk menuliskan kode mata pelajarannya saja<br>
                                Misalnya <abbr title="untuk Akuntansi Keuangan">AK</abbr> atau <abbr title="untuk Pendidikan Pancasila dan Kewarganegaraan">PPKN</abbr>
                            </td>
                        </tr>
                        <tr>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td>GURU:</td>
                            <td>
                                Ditulis kode guru<br>
                                Misalnya <abbr title="untuk Endang Muryati, S.Pd.">EM</abbr> atau <abbr title="untuk Drs. Mafudin">MF</abbr>
                            </td>
                        </tr>
                    </table>
                    <div class="gapJadwal"></div>
                    <p><b>Unduh file:</b></p>
                    <a href="upload/CONTOH-JADWAL-2020-GENAP.xlsx" class="btn btn-primary" style="width: 100px;">Contoh</a>
                    <a href="upload/TEMPLAT-JADWAL-TAHUN-SEMESTER.xlsx" class="btn btn-primary" style="width: 100px;">Templat</a>
                </div>
            </div>
        </div>

        <!-- ------------ Panel Import ------------ -->
        <div id="panelImport" class="col-sm-9">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <b>Mengimpor Data ke Database</b>
                </div>
                <div class="panel-body">
                    <pre id="import">Proses impor belum dilakukan</pre>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="js/content_import_data.js"></script>