$(document).ready(function () {
    var penJadwal = $('#penjelasanJadwal');
    var penSiswa = $('#penjelasanSiswa');
    var penGuru = $('#penjelasanGuru');
    var btnJadwal = $('#btnJadwal');
    var btnSiswa = $('#btnSiswa');
    var btnGuru = $('#btnGuru');
    var btnUpload = $('#btnUpload');
    var drop_zone = $('#drop_zone');
    var fileInput = $('#fileInput');
    var fileInfo = $('#fileInfo');
    var panelImport = $('#panelImport');

    btnJadwal.click(function () {
        if (penJadwal.is(':visible')) {
            penJadwal.hide();
            btnJadwal.removeClass('active');
            panelImport.show();
        } else {
            penSiswa.hide();
            penGuru.hide();
            panelImport.hide();
            penJadwal.show();
            btnJadwal.addClass('active');
            btnSiswa.removeClass('active');
            btnGuru.removeClass('active');
            $('.gapJadwal').height(441 - ($('#tabelJadwal').height() + 17 + 31));
        }
    });
    btnSiswa.click(function () {
        if (penSiswa.is(':visible')) {
            penSiswa.hide();
            btnSiswa.removeClass('active');
            panelImport.show();
        } else {
            penJadwal.hide();
            penGuru.hide();
            panelImport.hide();
            penSiswa.show();
            btnSiswa.addClass('active');
            btnJadwal.removeClass('active');
            btnGuru.removeClass('active');
            $('.gapSiswa').height(441 - ($('#tabelSiswa').height() + 17 + 31));
        }
    });
    btnGuru.click(function () {
        if (penGuru.is(':visible')) {
            penGuru.hide();
            btnGuru.removeClass('active');
            panelImport.show();
        } else {
            penJadwal.hide();
            penSiswa.hide();
            panelImport.hide();
            penGuru.show();
            btnGuru.addClass('active');
            btnJadwal.removeClass('active');
            btnSiswa.removeClass('active');
            $('.gapGuru').height(441 - ($('#tabelGuru').height() + 17 + 31));
        }
    });
    btnSiswa.click();

    drop_zone.on({
        dragover: function (e) {
            e.preventDefault();
        }, dragenter: function (e) {
            e.preventDefault();
            drop_zone.addClass('is-dragged');
        }, dragleave: function (e) {
            e.preventDefault();
            drop_zone.removeClass('is-dragged');
        }, drop: function (e) {
            e.preventDefault();
            drop_zone.removeClass('is-dragged');
            fileInput.prop('files', e.originalEvent.dataTransfer.files);
            fileInput.trigger('change');
        }, click: function () {
            fileInput.trigger('click');
        }
    }, false);

    var uploadOk = false;

    fileInput.on({
        click: function (e) {
            e.stopPropagation();
        },
        change: function (e) {
            e.stopPropagation();
            if (fileInput[0].files.length > 1) {
                fileInfo.text('Silahkan pilih file<br>satu per satu');
                uploadOk = false;
            } else {
                fileInfo.text(fileInput.val().substring(12));
                uploadOk = true;
            }
        }
    });

    btnUpload.click(function (e) {
        e.preventDefault();
        if (uploadOk) {
            btnGuru.removeClass('active');
            btnJadwal.removeClass('active');
            btnSiswa.removeClass('active');
            btnUpload.prop('disabled', true);
            btnJadwal.prop('disabled', true);
            btnSiswa.prop('disabled', true);
            btnGuru.prop('disabled', true);
            fileInput.prop('disabled', true);
            drop_zone.prop('disabled', true);
            penJadwal.hide();
            penSiswa.hide();
            penGuru.hide();
            panelImport.show();
            $('#drop_zone>img').attr('src', 'phpimages/loading.gif');

            var formData = new FormData();
            var files = $('#fileInput')[0].files[0];
            formData.append('fileInput', files);

            $.ajax({
                url: 'ajax/import_data.php',
                type: 'post',
                data: formData,
                processData: false,
                contentType: false
            }).done(function (data) {
                $('#import').html(data);

                btnUpload.prop('disabled', false);
                btnJadwal.prop('disabled', false);
                btnSiswa.prop('disabled', false);
                btnGuru.prop('disabled', false);
                fileInput.prop('disabled', false);
                drop_zone.prop('disabled', false);
                $('#drop_zone>img').attr('src', 'phpimages/file.png');
                fileInfo.text('Unggah file di sini');
                uploadOk = false;
            }).complete(function () {
                $('#uploadFile')[0].reset();
            });
        } else {
            fileInfo.text('Pilih file terlebih dahulu');
        }
    });
});
