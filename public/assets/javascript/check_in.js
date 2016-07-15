var checkinApp = new Vue({
    el: '#app',
    data: {
        attendees: [],
        searchTerm: '',
        searchResultsCount: 0,
        showScannerModal: false,
        workingAway: false,
        isInit: false,
        isScanning: false,
        videoElement: $('video#scannerVideo')[0],
        canvasElement: $('canvas#QrCanvas')[0],
        scannerDataUrl: '',
        QrTimeout: null,
        canvasContext: $('canvas#QrCanvas')[0].getContext('2d'),
        successBeep: new Audio('/mp3/beep.mp3'),
        scanResult: false,
        scanResultMessage: '',
        scanResultType: null
    },

    created: function () {
        this.fetchAttendees()
    },

    ready: function () {
    },

    methods: {
        fetchAttendees: function () {
            this.$http.post(Attendize.checkInSearchRoute, {q: this.searchTerm}).then(function (res) {
                this.attendees = res.data;
                this.searchResultsCount = (Object.keys(res.data).length);
            }, function () {
                console.log('Failed to fetch attendees')
            });
        },
        toggleCheckin: function (attendee) {

            if(this.workingAway) {
                return;
            }
            this.workingAway = true;
            var that = this;


            var checkinData = {
                checking: attendee.has_arrived ? 'out' : 'in',
                attendee_id: attendee.id,
            };

            this.$http.post(Attendize.checkInRoute, checkinData).then(function (res) {
                if (res.data.status == 'success' || res.data.status == 'error') {
                    if (res.data.status == 'error') {
                        alert(res.data.message);
                    }
                    attendee.has_arrived = checkinData.checking == 'out' ? 0 : 1;
                    that.workingAway = false;
                } else {
                    /* @todo handle error*/
                    that.workingAway = false;
                }
            });

        },
        clearSearch: function () {
            this.searchTerm = '';
            this.fetchAttendees();
        },

        /* QR Scanner Methods */

        QrCheckin: function (attendeeReferenceCode) {

            this.isScanning = false;

            this.$http.post(Attendize.qrcodeCheckInRoute, {attendee_reference: attendeeReferenceCode}).then(function (res) {
                this.successBeep.play();
                this.scanResult = true;
                this.scanResultMessage = res.data.message;
                this.scanResultType = res.data.status;

            }, function (response) {
                this.scanResultMessage = 'Something went wrong! Refresh the page and try again';
            });
        },

        showQrModal: function () {
            this.showScannerModal = true;
            this.initScanner();
        },

        initScanner: function () {

            var that = this;
            this.isScanning = true;
            this.scanResult = false;

            /*
             If the scanner is already initiated clear it and start over.
             */
            if (this.isInit) {
                clearTimeout(this.QrTimeout);
                this.QrTimeout = setTimeout(function () {
                    that.captureQrToCanvas();
                }, 500);
                return;
            }

            qrcode.callback = this.QrCheckin;
            navigator.getUserMedia = navigator.getUserMedia || navigator.webkitGetUserMedia || navigator.mozGetUserMedia;

            navigator.getUserMedia({video: true, audio: false}, function (stream) {

                that.stream = stream;

                if (window.webkitURL) {
                    that.videoElement.src = window.webkitURL.createObjectURL(stream);
                } else {
                    that.videoElement.mozSrcObject = stream;
                }

                that.videoElement.play();

            }, function () { /* error*/
            });

            this.isInit = true;
            this.QrTimeout = setTimeout(function () {
                that.captureQrToCanvas();
            }, 500);

        },
        /**
         * Takes stills from the video stream and sends them to the canvas so
         * they can be analysed for QR codes.
         */
        captureQrToCanvas: function () {

            if (!this.isInit) {
                return;
            }

            this.canvasContext.clearRect(0, 0, 600, 300);

            try {
                this.canvasContext.drawImage(this.videoElement, 0, 0);
                try {
                    qrcode.decode();
                }
                catch (e) {
                    console.log(e);
                    this.QrTimeout = setTimeout(this.captureQrToCanvas, 500);
                }
            }
            catch (e) {
                console.log(e);
                this.QrTimeout = setTimeout(this.captureQrToCanvas, 500);
            }
        },
        closeScanner: function () {
            clearTimeout(this.QrTimeout);
            this.showScannerModal = false;
            track = this.stream.getTracks()[0];
            track.stop();
            this.fetchAttendees();
        }
    }
});

