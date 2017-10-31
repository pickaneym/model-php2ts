Dropzone.options.dropzone = {
    paramName: "file",
    uploadMultiple: false,
    maxFiles: 1,
    acceptedFiles: ".zip",
    createImageThumbnails: false,
    addRemoveLinks: true,
    accept: function (file, done) {
        if (file.name == "justinbieber.jpg") {
            done("Naha, you don't.");
        }
        else {
            done();
        }
    },
    init: function () {
        var dropzone = this;

        dropzone.on("addedfile", function (file) {
            $('#conversion-loader').show();
            $('#placeholder-text').hide();
            this.emit("thumbnail", file, "assets/img/zip.png");
        });

        dropzone.on("processing", function (file) {
            $(".dz-clickable").css({"cursor": "progress"});
            $(".dz-processing").css({"cursor": "progress"});
        });

        dropzone.on("maxfilesreached", function () {
            dropzone.removeEventListeners();
        });

        dropzone.on('removedfile', function (file) {
            dropzone.setupEventListeners();
            $('#ts-models-link').hide();
            $('#placeholder-text').show();
        });

        dropzone.on("success", function (file) {
            $(".dz-clickable").css({"cursor": "pointer"});
            $(".dz-processing").css({"cursor": "pointer"});
            $('#placeholder-text').hide();
            $('#ts-models-link').show();
            $('#conversion-loader').hide();
        });

    }
};
