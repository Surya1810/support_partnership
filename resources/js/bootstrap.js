// import 'bootstrap';

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

// import axios from 'axios';
// window.axios = axios;

// window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

import Echo from 'laravel-echo';

import Pusher from 'pusher-js';
window.Pusher = Pusher;

window.Echo = new Echo({
  broadcaster: 'pusher',
  key: '96e9db5369e5078c4b42',
  cluster: 'ap1',
  forceTLS: true
});

// Inisialisasi DataTable
let rfidtable = $('#rfidTable').DataTable({
    "paging": true,
    "processing": true,
    "lengthChange": true,
    "searching": true,
    "info": true,
    "autoWidth": false,
    "responsive": false,
    "ordering": true,
    "serverSide": false,
    "destroy": true,
    "oLanguage": {
        "sEmptyTable": "Waiting scanner data"
    },
    columns: [
        { data: 'rfid_number' },
        { data: 'timestamp' }
    ]
});

let assetTable = $('#assetTable').DataTable({
    "paging": true,
    "processing": true,
    "lengthChange": true,
    "searching": true,
    "info": true,
    "autoWidth": false,
    "responsive": false,
    "ordering": false,
    "scrollX": true,
    "serverSide": false,
    "destroy": true,
});

// Variabel untuk menyimpan jumlah total
let totalRFID = 0;
let totalIsThereTrue = 0;
let totalIsThereFalse = 0;

window.Echo.channel("tag-scanned").listen("TagScanned",(event)=>{
   totalRFID = event.scannedTags.length;

    updateAssetTable(() => {
        updateRFIDTable(event);
    });
});

// ========== FUNGSI UPDATE TABLE ASSET ==========
function updateAssetTable(callback = null) {
    $.getJSON('/api/assets', function(data) {
        assetTable.clear();

        totalIsThereTrue = 0;
        totalIsThereFalse = 0;

        data.forEach((asset) => {
            let rowNode = assetTable.row.add([
                asset.is_there ? ' <strong>FOUND</strong>' : '<strong>MISSING</strong>',
                asset.rfid_number,
                asset.code,
                asset.name,
                asset.condition,
                asset.user.username,
                asset.gedung,
                asset.lantai,
                asset.ruangan
            ]).node();

            if (asset.is_there) {
                totalIsThereTrue++;
                $(rowNode).addClass('bg-success-2');
            } else {
                totalIsThereFalse++;
                $(rowNode).addClass('bg-danger-2');
            }
        });
        assetTable.draw();
        updateTotalDisplay();
        if (callback) callback();
    });
}

function updateRFIDTable(event) {
   rfidtable.clear();
    event.scannedTags.forEach((rfid) => {
        rfidtable.row.add({
            rfid_number: rfid, 
            timestamp: new Date().toLocaleTimeString('id-ID', { 
                hour: '2-digit', minute: '2-digit', second: '2-digit' 
            })
        });
    });
    rfidtable.draw();
}

// Fungsi untuk update tampilan total
function updateTotalDisplay() {
    $('#totalRFID').text(totalRFID);
    $('#totalIsThereTrue').text(totalIsThereTrue);
    $('#totalIsThereFalse').text(totalIsThereFalse);
}

// Panggil pertama kali saat halaman dimuat
updateAssetTable();


