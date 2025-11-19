<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Buat Postingan | Instaclone</title>
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <link rel="shortcut icon" href="<?= base_url('images/favicon.ico') ?>" type="image/x-icon">
    <link href="<?= base_url('css/style.css') ?>" rel="stylesheet">
    <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
</head>

<body>

    <?= $this->include('partials/_sidebar') ?>

    <main class="main-container">

        <div class="create-overlay">

            <button class="close-modal" onclick="window.location.href='<?= site_url('feed/' . $currentUsername) ?>'">
                <i class="fa fa-times"></i>
            </button>

            <form action="<?= site_url('post/store') ?>" method="post" enctype="multipart/form-data" class="create-card"
                id="postForm">

                <div class="create-header">
                    <button type="button" class="header-btn" id="btnBack" style="display:none;">
                        <i class="fa fa-arrow-left"></i>
                    </button>
                    <div style="width: 30px;" id="spacerLeft"></div>

                    <span class="header-title" id="headerTitle">Buat postingan baru</span>

                    <button type="submit" class="header-btn" id="btnShare" style="display:none;">Bagikan</button>
                    <div style="width: 30px;" id="spacerRight"></div>
                </div>

                <div class="create-body">

                    <div class="step-select" id="step1">
                        <div class="upload-icon-circle">
                            <i class="fa fa-picture-o"></i>
                            <i class="fa fa-play-circle-o" style="font-size: 50px; margin-left: -20px;"></i>
                        </div>
                        <p style="font-size: 20px; margin-bottom: 20px; font-weight: 300;">Seret foto dan video di sini
                        </p>

                        <label for="fileToUpload" class="btn-select-computer">Pilih dari komputer</label>
                        <input type="file" name="fileToUpload" id="fileToUpload" accept="image/*" style="display:none;"
                            onchange="handleFileSelect(this)">
                    </div>

                    <div class="step-details" id="step3">
                        <div class="details-image-container">
                            <img id="previewImageSmall" class="preview-image-full" src="" />
                        </div>

                        <div class="details-form-container">
                            <div class="user-info-mini">
                                <img src="<?= base_url(empty($profilePicture) ? 'images/avatar.svg' : $profilePicture) ?>"
                                    class="avatar-mini" />
                                <span class="username-mini"><?= esc($currentUsername) ?></span>
                            </div>
                            <textarea name="discription" class="caption-input"
                                placeholder="Tulis keterangan..."></textarea>

                            <div style="border-top: 1px solid #efefef; margin-top: 10px; padding-top: 10px;">
                                <div
                                    style="display:flex; justify-content:space-between; margin-bottom:10px; color:#262626;">
                                    <span>Tambahkan lokasi</span>
                                    <i class="fa fa-map-marker"></i>
                                </div>
                                <div style="display:flex; justify-content:space-between; color:#262626;">
                                    <span>Aksesibilitas</span>
                                    <i class="fa fa-chevron-down"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </form>
        </div>
    </main>

    <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
    <script src="<?= base_url('js/app.js') ?>"></script>
    <script>
        const step1 = document.getElementById('step1');
        const step3 = document.getElementById('step3');

        const btnBack = document.getElementById('btnBack');
        const btnShare = document.getElementById('btnShare');

        const headerTitle = document.getElementById('headerTitle');
        const spacerLeft = document.getElementById('spacerLeft');
        const spacerRight = document.getElementById('spacerRight');

        const previewSmall = document.getElementById('previewImageSmall');

        function handleFileSelect(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    previewSmall.src = e.target.result;

                    goToStep(3);
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        btnBack.addEventListener('click', function () {
            goToStep(1);
            document.getElementById('fileToUpload').value = ""; // Reset input
        });

        // Logika Perpindahan Tampilan
        function goToStep(step) {
            step1.style.display = 'none';
            step3.style.display = 'none';

            btnBack.style.display = 'none';
            btnShare.style.display = 'none';
            spacerLeft.style.display = 'none';
            spacerRight.style.display = 'none';

            if (step === 1) {
                step1.style.display = 'block';
                headerTitle.innerText = "Buat postingan baru";

                spacerLeft.style.display = 'block';
                spacerRight.style.display = 'block';

                // Ukuran kartu default
                document.querySelector('.create-card').style.maxWidth = "750px";
            }
            else if (step === 3) {

                step3.style.display = 'flex';
                headerTitle.innerText = "Buat postingan baru";

                btnBack.style.display = 'block';
                btnShare.style.display = 'block';

                document.querySelector('.create-card').style.maxWidth = "900px";
            }
        }
    </script>
</body>

</html>