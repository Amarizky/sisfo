<!-- Begin Main Menu -->
<?php

// Generate all menu items
$RootMenu->IsRoot = TRUE;
$RootMenu->AddMenuItem(48, "mmi_home_php", $Language->MenuPhrase("48", "MenuText"), "home.php", -1, "", AllowListMenu('{B4ECA7F4-5928-4768-B0FE-A8227431E424}home.php'), FALSE, TRUE);
$RootMenu->AddMenuItem(45, "mmci_AKADEMIK", $Language->MenuPhrase("45", "MenuText"), "", -1, "", TRUE, FALSE, TRUE);
$RootMenu->AddMenuItem(40, "mmi_tahun", $Language->MenuPhrase("40", "MenuText"), "tahunlist.php", 45, "", AllowListMenu('{B4ECA7F4-5928-4768-B0FE-A8227431E424}tahun'), FALSE, FALSE);
$RootMenu->AddMenuItem(7, "mmi_kurikulum", $Language->MenuPhrase("7", "MenuText"), "kurikulumlist.php", 45, "", AllowListMenu('{B4ECA7F4-5928-4768-B0FE-A8227431E424}kurikulum'), FALSE, FALSE);
$RootMenu->AddMenuItem(5, "mmi_khs", $Language->MenuPhrase("5", "MenuText"), "khslist.php", 45, "", AllowListMenu('{B4ECA7F4-5928-4768-B0FE-A8227431E424}khs'), FALSE, FALSE);
$RootMenu->AddMenuItem(2, "mmi_jadwal", $Language->MenuPhrase("2", "MenuText"), "jadwallist.php", 45, "", AllowListMenu('{B4ECA7F4-5928-4768-B0FE-A8227431E424}jadwal'), FALSE, FALSE);
$RootMenu->AddMenuItem(103, "mmi_sync_elearning_php", $Language->MenuPhrase("103", "MenuText"), "sync_elearning.php", 45, "", AllowListMenu('{B4ECA7F4-5928-4768-B0FE-A8227431E424}sync_elearning.php'), FALSE, TRUE);
$RootMenu->AddMenuItem(42, "mmci_MASTER", $Language->MenuPhrase("42", "MenuText"), "", -1, "", TRUE, FALSE, TRUE);
$RootMenu->AddMenuItem(16, "mmi_master_kampus", $Language->MenuPhrase("16", "MenuText"), "master_kampuslist.php", 42, "", AllowListMenu('{B4ECA7F4-5928-4768-B0FE-A8227431E424}master_kampus'), FALSE, FALSE);
$RootMenu->AddMenuItem(41, "mmi_teacher", $Language->MenuPhrase("41", "MenuText"), "teacherlist.php", 42, "", AllowListMenu('{B4ECA7F4-5928-4768-B0FE-A8227431E424}teacher'), FALSE, FALSE);
$RootMenu->AddMenuItem(39, "mmi_student", $Language->MenuPhrase("39", "MenuText"), "studentlist.php", 42, "", AllowListMenu('{B4ECA7F4-5928-4768-B0FE-A8227431E424}student'), FALSE, FALSE);
$RootMenu->AddMenuItem(100, "mmi_staff", $Language->MenuPhrase("100", "MenuText"), "stafflist.php", 42, "", AllowListMenu('{B4ECA7F4-5928-4768-B0FE-A8227431E424}staff'), FALSE, FALSE);
$RootMenu->AddMenuItem(21, "mmi_master_prodi", $Language->MenuPhrase("21", "MenuText"), "master_prodilist.php", 42, "", AllowListMenu('{B4ECA7F4-5928-4768-B0FE-A8227431E424}master_prodi'), FALSE, FALSE);
$RootMenu->AddMenuItem(4, "mmi_kelas", $Language->MenuPhrase("4", "MenuText"), "kelaslist.php", 42, "", AllowListMenu('{B4ECA7F4-5928-4768-B0FE-A8227431E424}kelas'), FALSE, FALSE);
$RootMenu->AddMenuItem(14, "mmi_master_jamkul", $Language->MenuPhrase("14", "MenuText"), "master_jamkullist.php", 42, "", AllowListMenu('{B4ECA7F4-5928-4768-B0FE-A8227431E424}master_jamkul'), FALSE, FALSE);
$RootMenu->AddMenuItem(22, "mmi_master_sesi", $Language->MenuPhrase("22", "MenuText"), "master_sesilist.php", 42, "", AllowListMenu('{B4ECA7F4-5928-4768-B0FE-A8227431E424}master_sesi'), FALSE, FALSE);
$RootMenu->AddMenuItem(43, "mmci_KEWILAYAHAN", $Language->MenuPhrase("43", "MenuText"), "", 42, "", TRUE, FALSE, TRUE);
$RootMenu->AddMenuItem(25, "mmi_master_wilayah_benua", $Language->MenuPhrase("25", "MenuText"), "master_wilayah_benualist.php", 43, "", AllowListMenu('{B4ECA7F4-5928-4768-B0FE-A8227431E424}master_wilayah_benua'), FALSE, FALSE);
$RootMenu->AddMenuItem(29, "mmi_master_wilayah_negara", $Language->MenuPhrase("29", "MenuText"), "master_wilayah_negaralist.php", 43, "", AllowListMenu('{B4ECA7F4-5928-4768-B0FE-A8227431E424}master_wilayah_negara'), FALSE, FALSE);
$RootMenu->AddMenuItem(30, "mmi_master_wilayah_provinsi", $Language->MenuPhrase("30", "MenuText"), "master_wilayah_provinsilist.php", 43, "", AllowListMenu('{B4ECA7F4-5928-4768-B0FE-A8227431E424}master_wilayah_provinsi'), FALSE, FALSE);
$RootMenu->AddMenuItem(27, "mmi_master_wilayah_kabupatenkota", $Language->MenuPhrase("27", "MenuText"), "master_wilayah_kabupatenkotalist.php", 43, "", AllowListMenu('{B4ECA7F4-5928-4768-B0FE-A8227431E424}master_wilayah_kabupatenkota'), FALSE, FALSE);
$RootMenu->AddMenuItem(28, "mmi_master_wilayah_kecamatan", $Language->MenuPhrase("28", "MenuText"), "master_wilayah_kecamatanlist.php", 43, "", AllowListMenu('{B4ECA7F4-5928-4768-B0FE-A8227431E424}master_wilayah_kecamatan'), FALSE, FALSE);
$RootMenu->AddMenuItem(26, "mmi_master_wilayah_desa", $Language->MenuPhrase("26", "MenuText"), "master_wilayah_desalist.php", 43, "", AllowListMenu('{B4ECA7F4-5928-4768-B0FE-A8227431E424}master_wilayah_desa'), FALSE, FALSE);
$RootMenu->AddMenuItem(47, "mmci_IDENTITAS", $Language->MenuPhrase("47", "MenuText"), "", 42, "", TRUE, FALSE, TRUE);
$RootMenu->AddMenuItem(19, "mmi_master_pekerjaanortu", $Language->MenuPhrase("19", "MenuText"), "master_pekerjaanortulist.php", 47, "", AllowListMenu('{B4ECA7F4-5928-4768-B0FE-A8227431E424}master_pekerjaanortu'), FALSE, FALSE);
$RootMenu->AddMenuItem(20, "mmi_master_pendidikanortu", $Language->MenuPhrase("20", "MenuText"), "master_pendidikanortulist.php", 47, "", AllowListMenu('{B4ECA7F4-5928-4768-B0FE-A8227431E424}master_pendidikanortu'), FALSE, FALSE);
$RootMenu->AddMenuItem(17, "mmi_master_kelamin", $Language->MenuPhrase("17", "MenuText"), "master_kelaminlist.php", 47, "", AllowListMenu('{B4ECA7F4-5928-4768-B0FE-A8227431E424}master_kelamin'), FALSE, FALSE);
$RootMenu->AddMenuItem(10, "mmi_master_darah", $Language->MenuPhrase("10", "MenuText"), "master_darahlist.php", 47, "", AllowListMenu('{B4ECA7F4-5928-4768-B0FE-A8227431E424}master_darah'), FALSE, FALSE);
$RootMenu->AddMenuItem(8, "mmi_master_agama", $Language->MenuPhrase("8", "MenuText"), "master_agamalist.php", 47, "", AllowListMenu('{B4ECA7F4-5928-4768-B0FE-A8227431E424}master_agama'), FALSE, FALSE);
$RootMenu->AddMenuItem(13, "mmi_master_hidup", $Language->MenuPhrase("13", "MenuText"), "master_hiduplist.php", 47, "", AllowListMenu('{B4ECA7F4-5928-4768-B0FE-A8227431E424}master_hidup'), FALSE, FALSE);
$RootMenu->AddMenuItem(11, "mmi_master_golongan", $Language->MenuPhrase("11", "MenuText"), "master_golonganlist.php", 47, "", AllowListMenu('{B4ECA7F4-5928-4768-B0FE-A8227431E424}master_golongan'), FALSE, FALSE);
$RootMenu->AddMenuItem(15, "mmi_master_jenjang", $Language->MenuPhrase("15", "MenuText"), "master_jenjanglist.php", 47, "", AllowListMenu('{B4ECA7F4-5928-4768-B0FE-A8227431E424}master_jenjang'), FALSE, FALSE);
$RootMenu->AddMenuItem(102, "mmi_master_ikatan", $Language->MenuPhrase("102", "MenuText"), "master_ikatanlist.php", 47, "", AllowListMenu('{B4ECA7F4-5928-4768-B0FE-A8227431E424}master_ikatan'), FALSE, FALSE);
$RootMenu->AddMenuItem(101, "mmi_master_bagian", $Language->MenuPhrase("101", "MenuText"), "master_bagianlist.php", 47, "", AllowListMenu('{B4ECA7F4-5928-4768-B0FE-A8227431E424}master_bagian'), FALSE, FALSE);
$RootMenu->AddMenuItem(46, "mmci_STATUS", $Language->MenuPhrase("46", "MenuText"), "", 42, "", TRUE, FALSE, TRUE);
$RootMenu->AddMenuItem(35, "mmi_statuslulus", $Language->MenuPhrase("35", "MenuText"), "statusluluslist.php", 46, "", AllowListMenu('{B4ECA7F4-5928-4768-B0FE-A8227431E424}statuslulus'), FALSE, FALSE);
$RootMenu->AddMenuItem(36, "mmi_master_statussipil", $Language->MenuPhrase("36", "MenuText"), "master_statussipillist.php", 46, "", AllowListMenu('{B4ECA7F4-5928-4768-B0FE-A8227431E424}master_statussipil'), FALSE, FALSE);
$RootMenu->AddMenuItem(23, "mmi_master_statusawal", $Language->MenuPhrase("23", "MenuText"), "master_statusawallist.php", 46, "", AllowListMenu('{B4ECA7F4-5928-4768-B0FE-A8227431E424}master_statusawal'), FALSE, FALSE);
$RootMenu->AddMenuItem(24, "mmi_master_statuskerja", $Language->MenuPhrase("24", "MenuText"), "master_statuskerjalist.php", 46, "", AllowListMenu('{B4ECA7F4-5928-4768-B0FE-A8227431E424}master_statuskerja'), FALSE, FALSE);
$RootMenu->AddMenuItem(44, "mmci_SISTEM", $Language->MenuPhrase("44", "MenuText"), "", 42, "", TRUE, FALSE, TRUE);
$RootMenu->AddMenuItem(12, "mmi_master_hari", $Language->MenuPhrase("12", "MenuText"), "master_harilist.php", 44, "", AllowListMenu('{B4ECA7F4-5928-4768-B0FE-A8227431E424}master_hari'), FALSE, FALSE);
$RootMenu->AddMenuItem(9, "mmi_master_bahasa", $Language->MenuPhrase("9", "MenuText"), "master_bahasalist.php", 44, "", AllowListMenu('{B4ECA7F4-5928-4768-B0FE-A8227431E424}master_bahasa'), FALSE, FALSE);
$RootMenu->AddMenuItem(99, "mmi_audittrail", $Language->MenuPhrase("99", "MenuText"), "audittraillist.php", 44, "", AllowListMenu('{B4ECA7F4-5928-4768-B0FE-A8227431E424}audittrail'), FALSE, FALSE);
$RootMenu->AddMenuItem(95, "mmci_SETTING", $Language->MenuPhrase("95", "MenuText"), "", -1, "", TRUE, FALSE, TRUE);
$RootMenu->AddMenuItem(98, "mmi_users", $Language->MenuPhrase("98", "MenuText"), "userslist.php", 95, "", AllowListMenu('{B4ECA7F4-5928-4768-B0FE-A8227431E424}users'), FALSE, FALSE);
$RootMenu->AddMenuItem(106, "mmi_userlevels", $Language->MenuPhrase("106", "MenuText"), "userlevelslist.php", 95, "", (@$_SESSION[EW_SESSION_USER_LEVEL] & EW_ALLOW_ADMIN) == EW_ALLOW_ADMIN, FALSE, FALSE);
$RootMenu->AddMenuItem(-1, "mmi_logout", $Language->Phrase("Logout"), "logout.php", -1, "", IsLoggedIn());
$RootMenu->AddMenuItem(-1, "mmi_login", $Language->Phrase("Login"), "login.php", -1, "", !IsLoggedIn() && substr(@$_SERVER["URL"], -1 * strlen("login.php")) <> "login.php");
$RootMenu->Render();
?>
<!-- End Main Menu -->
