<?php if ($_POST['showImage'] === 'true') { ?>
    <script type="text/javascript">
    $(document).ready(function () {
        if (sessionStorage.getItem("file") !== null) {
            var result = '<img id="base64image" src="' + sessionStorage.getItem("file") + '"/>';
        } else {
            var result = "<h1>PHOTO TERLEBIH DAHULU</h1>";
        }
        document.getElementById('results').innerHTML = result;
    });
    </script>
    <div id="results" class="text-center"></div>
    <?php } else { ?>
        <script type="text/javascript">
        $(document).ready(function () {
            ShowCam();
        });
        function take_snapshot() {
            Webcam.snap(function (data_uri) {
                sessionStorage.setItem("file", data_uri);
                $('#photoDocument').val(data_uri);
            });
            resetCam();
        }
        function ShowCam() {
            Webcam.set({
               // force_flash: true,
                width: 1000,
                height: 600,
                image_format: 'jpeg',
                jpeg_quality: 70
            });
            Webcam.attach('#my_camera');
        }

        function resetCam(){
            console.log('Close Camera');
            Webcam.reset('#my_camera');
        }

        </script>
        <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" onclick="resetCam()" aria-hidden="true" id="closesnapPhotoModal">×</button>
        <h3 id="snapPhotoModalLabel">SNAP</h3>
        </div>
        <div id="my_camera"></div>
        
        <div class="modal-footer">
        <button class="btn" data-dismiss="modal" aria-hidden="true" onclick="resetCam()" id="closeInsertModal">Close</button>
        <button class="btn btn-primary" data-dismiss="modal" onclick="take_snapshot()">TAKE PHOTO</button>
        </div>
        <?php } ?>
        
        
        <!-- <div class="container" id="Cam" style="width: 320px;"><b>Webcam Preview...</b> -->
        <!-- <form>
        <input type="button" value="Snap It" onClick="take_snapshot()">
        </form>
        </div>
        <div class="container" id="Prev">
        <b>Snap Preview...</b><div id="results"></div>
        </div>
        <div class="container" id="Saved">
        <b>Saved</b><span id="loading"></span><img id="uploaded" src=""/>
        </div> -->
        