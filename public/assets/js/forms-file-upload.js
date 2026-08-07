/**
 * File Upload
 */

"use strict";

(function () {
    // previewTemplate: Updated Dropzone default previewTemplate
    // ! Don't change it unless you really know what you are doing
    const previewTemplate = `<div class="dz-preview dz-file-preview">
<div class="dz-details">
  <div class="dz-thumbnail">
    <img data-dz-thumbnail>
    <span class="dz-nopreview">No preview</span>
    <div class="dz-success-mark"></div>
    <div class="dz-error-mark"></div>
    <div class="dz-error-message"><span data-dz-errormessage></span></div>
    <div class="progress">
      <div class="progress-bar progress-bar-primary" role="progressbar" aria-valuemin="0" aria-valuemax="100" data-dz-uploadprogress></div>
    </div>
  </div>
  <div class="dz-filename" data-dz-name></div>
  <div class="dz-size" data-dz-size></div>
</div>
</div>`;

    // ? Start your code from here

    // Basic Dropzone
    // --------------------------------------------------------------------

    var myDropzone = new Dropzone("#dropzone-basic", {
        paramName: "images",
        maxFilesize: 2,
        url: "/service-reports",
        previewTemplate: previewTemplate,
        previewsContainer: "#previews",
        addRemoveLinks: true,
        autoProcessQueue: false,
        parallelUploads: 1,
        maxFiles: 4,
        acceptedFiles: ".jpeg, .jpg, .png, .gif",
        thumbnailWidth: 900,
        thumbnailHeight: 600,
        timeout: 0,
        init: function () {
            myDropzone = this;

            // when file is dragged in
            this.on("addedfile", function (file) {
                $(".dropzone-drag-area")
                    .removeClass("is-invalid")
                    .next(".invalid-feedback")
                    .hide();
            });
        },
        success: function (file, response) {
            // hide form and show success message
            $("#serviceReports").fadeOut(600);
            setTimeout(function () {
                $("#successMessage").removeClass("d-none");
            }, 600);
        },
    });
    $("#formSubmit").on("click", function (event) {
        event.preventDefault();
        var $this = $(this);

        // show submit button spinner
        $this.children(".spinner-border").removeClass("d-none");

        // validate form & submit if valid
        if ($("#formDropzone")[0].checkValidity() === false) {
            event.stopPropagation();

            // show error messages & hide button spinner
            $("#formDropzone").addClass("was-validated");
            $this.children(".spinner-border").addClass("d-none");

            // if dropzone is empty show error message
            if (!myDropzone.getQueuedFiles().length > 0) {
                $(".dropzone-drag-area")
                    .addClass("is-invalid")
                    .next(".invalid-feedback")
                    .show();
            }
        } else {
            // if everything is ok, submit the form
            myDropzone.processQueue();
        }
    });

    // Multiple Dropzone
    // --------------------------------------------------------------------
    const dropzoneMulti = new Dropzone("#dropzone-multi", {
        previewTemplate: previewTemplate,
        parallelUploads: 1,
        maxFilesize: 5,
        addRemoveLinks: true,
    });
})();
