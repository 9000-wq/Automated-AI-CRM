<!DOCTYPE html>
<html>
<head>
    <title>Call Now</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <style>
        .dialer {
            border: 2px solid #ccc;
            border-radius: 10px;
            padding: 20px;
            margin-top: 30px;
            max-width: 350px;
            margin-left: auto;
            margin-right: auto;
            background-color: #f8f9fa;
        }
        .number-display {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 15px;
        }
        .keypad {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
        }
        .keypad button {
            padding: 15px;
            font-size: 18px;
            border-radius: 5px;
            border: 1px solid #888;
            background-color: #ffffff;
            transition: background-color 0.2s ease;
        }
        .keypad button:hover {
            background-color: #e9ecef;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container py-5">
        <h1 class="text-center mb-4">Mannual Call</h1>

        @if($contact =='')
        <!-- Phone selector -->
        <div id="phoneSelector" class="text-center" >
            <div class="mb-3">
                <label for="phoneDropdown" class="form-label">Select a Contact:</label>
                <select id="phoneDropdown" class="form-select w-50 mx-auto">
                    <option value="" selected>--Choose a number--</option>
                    @foreach($leadcontacts as $leadcontact)
                    <option value="{{$leadcontact}}" >{{$leadcontact}}</option>
                    @endforeach
                   
                </select>
            </div>
            <button class="btn btn-primary" onclick="makeCall()">Call Now</button>
        </div>
        @else
        <!-- Dial pad UI -->
        <div id="dialerUI" >
            <h4 class="text-center">Calling: <span id="numberToCall"></span></h4>
            <div class="dialer">
                <div class="number-display" id="displayNumber">+{{$contact}}</div>
                <div class="keypad">
                    <button class="btn">1</button><button class="btn">2</button><button class="btn">3</button>
                    <button class="btn">4</button><button class="btn">5</button><button class="btn">6</button>
                    <button class="btn">7</button><button class="btn">8</button><button class="btn">9</button>
                    <button class="btn">*</button><button class="btn">0</button><button class="btn">#</button>
                </div>
                
                <div class="row">
                    <div class="col-sm-6">
                        <button class="btn btn-success w-100 mt-4"  id="callButton"> <i class="material-icons">call</i></button>
                    </div>
                    <div class="col-sm-6">
                        <button class="btn btn-danger w-100 mt-4" id="hangupButton" disabled><i class="material-icons">call_end</i></button>
                    </div>
                </div>

            </div>
        </div>
        @endif
    </div>

    <script src="//media.twiliocdn.com/sdk/js/video/releases/2.18.1/twilio-video.min.js"></script>
    <script src="//media.twiliocdn.com/sdk/js/client/v1.13/twilio.min.js"></script>
    <script src="https://media.twiliocdn.com/sdk/js/client/v1.13/twilio.min.js"></script>

    <script>
        
        function makeCall() {
            const selectedNumber = document.getElementById('phoneDropdown').value;
            if (!selectedNumber) {
                alert('Please select a number first');
                return;
            }

            window.location ="{{route('call-screen')}}/{{$leadid}}/"+selectedNumber;
        }


    

  
    let device;
    let connection;

    document.getElementById('callButton').addEventListener('click', async function () {
        const phoneNumber = document.getElementById('displayNumber').textContent.trim();

        try {
            const response = await fetch('{{ route('generate-twilio-token') }}');
            const { token } = await response.json();

            device = new Twilio.Device(token, { debug: true });

            // Setup listeners ONCE
            device.on('ready', function () {
                console.log('Twilio Device is ready');
            });

            device.on('error', function (error) {
                console.error("Twilio Error:", error.message);
            });

            device.on('disconnect', function () {
                console.log("Call ended");
                document.getElementById('hangupButton').disabled = true;
            });

            device.on('connect', function (conn) {
                console.log("Call started to:", conn.parameters.To);
                document.getElementById('hangupButton').disabled = false;
            });

            // Wait for device to be ready, then connect
            device.once('ready', function () {
                device.connect({ To: phoneNumber }); // <- your dynamic number
            });

            device.initialize(); // Force it to prepare connection (only needed if not auto-ready)

        } catch (err) {
            console.error("Token fetch or call failed:", err);
        }
    });

    document.getElementById('hangupButton').addEventListener('click', function () {
        if (device) {
            device.disconnectAll(); // <-- This ends the call
        }
    });

    </script>
</body>
</html>
