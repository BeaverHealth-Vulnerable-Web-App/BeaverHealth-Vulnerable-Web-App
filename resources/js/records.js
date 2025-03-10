import { MAX_FILE_SIZE_BYTES } from './utils.js';

/**
 * Validates the file size when a file is selected
 *
 * @param {HTMLInputElement} input
 */
function validateFileSize(input) {
    const file = input.files[0];

    if (file && file.size >= MAX_FILE_SIZE_BYTES) {
        document.getElementById('file-size-error').innerText = 'File size exceeds the maximum limit of 100MiB.';
        document.getElementById('file-size-error').style.display = 'block';
        input.value = '';
    } else {
        document.getElementById('file-size-error').style.display = 'none';
    }
}

document.addEventListener("DOMContentLoaded", function () {
    const fileInput = document.getElementById('medical_record');
    if (fileInput) {
        fileInput.addEventListener('change', function () {
            validateFileSize(this);
        });
    }
});
