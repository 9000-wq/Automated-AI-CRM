<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Call Scheduling System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-timepicker/0.5.2/css/bootstrap-timepicker.min.css">
    <style>
        :root {
            --primary-color: #3b65ea;
            --secondary-color: #f8f9fa;
            --border-radius: 4px;
            --box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            --success-color: #28a745;
            --danger-color: #dc3545;
            --warning-color: #ffc107;
        }

        .call-scheduling-container {
            background-color: #f5f7fb;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 20px;
            min-height: 100vh;
        }

        .call-scheduling-container .modal-content {
            border-radius: 8px;
            border: none;
            box-shadow: var(--box-shadow);
        }

        .call-scheduling-container .modal-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid #e9ecef;
            padding: 12px 15px;
            border-radius: 8px 8px 0 0;
        }

        .call-scheduling-container .modal-title {
            font-size: 16px;
            font-weight: 600;
            color: #495057;
        }

        .call-scheduling-container .close {
            color: #6c757d;
            opacity: 0.7;
            font-size: 18px;
            padding: 0 5px;
            cursor: pointer;
        }

        .call-scheduling-container .close:hover {
            color: #495057;
            opacity: 1;
        }

        .call-scheduling-container .modal-footer {
            background-color: #f8f9fa;
            border-top: 1px solid #e9ecef;
            padding: 12px 15px;
            border-radius: 0 0 8px 8px;
        }

        .call-scheduling-container .modal-body {
            padding: 15px;
            background-color: #fff;
        }

        .call-scheduling-container .main-btn-group .btn {
            font-size: 13px;
            padding: 5px 12px;
            font-weight: 500;
        }

        .call-scheduling-container .form-group {
            margin-bottom: 15px;
        }

        .call-scheduling-container .control-label {
            font-weight: 500;
            color: #495057;
            font-size: 13px;
            margin-bottom: 5px;
            display: block;
        }

        .call-scheduling-container .required-sign {
            color: #dc3545;
        }

        .call-scheduling-container .form-control {
            font-size: 13px;
            border-radius: var(--border-radius);
            border: 1px solid #ced4da;
            padding: 6px 12px;
            height: 32px;
        }

        .call-scheduling-container .form-control:focus {
            border-color: #86b7fe;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }

        .call-scheduling-container .input-group-container-2 {
            display: flex;
            gap: 5px;
        }

        .call-scheduling-container .input-group {
            flex: 1;
        }

        .call-scheduling-container .input-group .form-control {
            border-radius: var(--border-radius) 0 0 var(--border-radius);
        }

        .call-scheduling-container .input-group-btn {
            display: flex;
        }

        .call-scheduling-container .input-group-btn .btn {
            border-radius: 0 var(--border-radius) var(--border-radius) 0;
            padding: 6px 10px;
            background-color: #e9ecef;
            border: 1px solid #ced4da;
        }

        .call-scheduling-container .input-group-btn .btn i {
            font-size: 13px;
        }

        .call-scheduling-container .input-group-link-parent {
            display: flex;
        }

        .call-scheduling-container .input-group-link-parent .input-group-item {
            flex: 1;
        }

        .call-scheduling-container .input-group-link-parent .input-group-item-middle {
            flex: 2;
            margin: 0 5px;
        }

        .call-scheduling-container .panel {
            margin-bottom: 15px;
            border: 1px solid #e9ecef;
            border-radius: var(--border-radius);
            background-color: #fff;
        }

        .call-scheduling-container .panel-default {
            border-color: #e9ecef;
        }

        .call-scheduling-container .panel-heading {
            background-color: #f8f9fa;
            border-bottom: 1px solid #e9ecef;
            padding: 10px 15px;
            border-radius: var(--border-radius) var(--border-radius) 0 0;
        }

        .call-scheduling-container .panel-title {
            font-size: 14px;
            font-weight: 600;
            color: #495057;
            margin: 0;
        }

        .call-scheduling-container .panel-body {
            padding: 15px;
        }

        .call-scheduling-container .panel-body-form {
            padding: 10px 15px;
        }

        .call-scheduling-container .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .call-scheduling-container .btn-primary:hover {
            background-color: #2a52d8;
            border-color: #2a52d8;
        }

        .call-scheduling-container .btn-success {
            background-color: var(--success-color);
            border-color: var(--success-color);
        }

        .call-scheduling-container .btn-success:hover {
            background-color: #218838;
            border-color: #1e7e34;
        }

        .call-scheduling-container .btn-default {
            background-color: #f8f9fa;
            color: #495057;
            border-color: #ced4da;
        }

        .call-scheduling-container .btn-default:hover {
            background-color: #e9ecef;
            border-color: #ced4da;
        }

        .call-scheduling-container .btn-xs-wide {
            min-width: 80px;
        }

        .call-scheduling-container .radius-left {
            border-radius: var(--border-radius) 0 0 var(--border-radius) !important;
        }

        .call-scheduling-container .radius-right {
            border-radius: 0 var(--border-radius) var(--border-radius) 0 !important;
        }

        .call-scheduling-container .record-grid {
            display: flex;
            gap: 15px;
        }

        .call-scheduling-container .left {
            flex: 3;
        }

        .call-scheduling-container .side {
            flex: 1;
        }

        .call-scheduling-container .extra,
        .call-scheduling-container .bottom {
            margin-top: 15px;
        }

        .call-scheduling-container .first {
            margin-top: 0;
        }

        .call-scheduling-container .last {
            margin-bottom: 0;
        }

        .call-scheduling-container .card-top-border {
            width: 100%;
            background-color: var(--primary-color);
            height: 10px;
            border-radius: 10px 10px 0px 0px;
        }

        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
            padding: 15px 20px;
            border-radius: 4px;
            color: white;
            font-weight: 500;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            transform: translateX(150%);
            transition: transform 0.3s ease-in-out;
        }

        .notification.show {
            transform: translateX(0);
        }

        .notification.success {
            background-color: var(--success-color);
        }

        .notification.error {
            background-color: var(--danger-color);
        }

        .notification.info {
            background-color: var(--primary-color);
        }

        .call-scheduling-container .is-invalid {
            border-color: var(--danger-color) !important;
        }

        .call-scheduling-container .error-message {
            font-size: 12px;
            color: var(--danger-color);
            margin-top: 5px;
        }

        @media (max-width: 992px) {
            .call-scheduling-container .record-grid {
                flex-direction: column;
            }

            .call-scheduling-container .input-group-container-2 {
                flex-direction: column;
            }
        }
    </style>
</head>
@include('layouts.header')

<body>
    <input type="hidden" id="lead_id" value="{{ $lead ?? '' }}">

    <div class="call-scheduling-container">
        <div class="container-fluid p-0">
            <h1 class="h3 mb-3"><strong>Call Scheduling System</strong></h1>

            <div class="btn-group main-btn-group py-2">
                <button type="button" id="save-call" class="btn btn-primary btn-xs-wide radius-left">
                    <i class="fas fa-save me-1"></i> Save
                </button>
                <button type="button" id="reset-form" class="btn btn-default btn-xs-wide radius-right">
                    <i class="fas fa-undo me-1"></i> Reset
                </button>
            </div>

            <div class="notification" id="notification">
                Call scheduled successfully!
            </div>

            <div class="row">
                <div class="col-xl-12 col-xxl-12 d-flex">
                    <div class="w-100">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="card" style="border-radius:10px;">
                                    <div class="card-top-border"></div>
                                    <div class="card-body">
                                        <div class="modal-content">
                                            <div class="modal-body body" style="overflow: auto; height: auto;">
                                                <div class="edit-container record no-side-margin">
                                                    <div class="edit">
                                                        <div class="record-grid record-grid-small">
                                                            <div class="left">
                                                                <div class="middle">
                                                                    <div class="panel panel-default">
                                                                        <div class="panel-body panel-body-form">
                                                                            <!-- Name Field -->
                                                                            <div class="row">
                                                                                <div class="cell col-sm-12 form-group">
                                                                                    <label class="control-label">
                                                                                        Name <span
                                                                                            class="required-sign">*</span>
                                                                                    </label>
                                                                                    <input type="text"
                                                                                        class="form-control"
                                                                                        id="call-name" name="name"
                                                                                        placeholder="Enter call name">
                                                                                </div>
                                                                            </div>

                                                                            <!-- Status/Direction Fields -->
                                                                            <div class="row">
                                                                                <div class="cell col-sm-6 form-group">
                                                                                    <label
                                                                                        class="control-label">Status</label>
                                                                                    <select name="status"
                                                                                        class="form-control"
                                                                                        id="call-status">
                                                                                        <option value="Planned"
                                                                                            selected>Planned</option>
                                                                                        <option value="Held">Held
                                                                                        </option>
                                                                                       
                                                                                    </select>
                                                                                </div>

                                                                                <div class="cell col-sm-6 form-group">
                                                                                    <label
                                                                                        class="control-label">Direction</label>
                                                                                    <select name="direction"
                                                                                        class="form-control"
                                                                                        id="call-direction">
                                                                                        <option value="Outbound"
                                                                                            selected>Outbound</option>
                                                                                        <option value="Inbound">Inbound
                                                                                        </option>
                                                                                    </select>
                                                                                </div>
                                                                            </div>

                                                                            <!-- Date Start -->
                                                                            <div class="row">
                                                                                <div class="cell col-sm-12 form-group">
                                                                                    <label class="control-label">
                                                                                        Date Start <span
                                                                                            class="required-sign">*</span>
                                                                                    </label>
                                                                                    <div class="field">
                                                                                        <div
                                                                                            class="input-group-container-2">
                                                                                            <div class="input-group">
                                                                                                <input type="text"
                                                                                                    class="form-control datepicker"
                                                                                                    id="date-start"
                                                                                                    name="date_start"
                                                                                                    placeholder="Select date">
                                                                                                <span
                                                                                                    class="input-group-btn">
                                                                                                    <button
                                                                                                        type="button"
                                                                                                        class="btn btn-default btn-icon"><i
                                                                                                            class="far fa-calendar"></i></button>
                                                                                                </span>
                                                                                            </div>
                                                                                            <div class="input-group">
                                                                                                <input type="text"
                                                                                                    class="form-control timepicker"
                                                                                                    id="time-start"
                                                                                                    name="time_start"
                                                                                                    placeholder="Select time">
                                                                                                <span
                                                                                                    class="input-group-btn">
                                                                                                    <button
                                                                                                        type="button"
                                                                                                        class="btn btn-default btn-icon"><i
                                                                                                            class="far fa-clock"></i></button>
                                                                                                </span>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>

                                                                            <!-- Duration -->
                                                                            <div class="row">
                                                                                <div class="cell col-sm-12 form-group">
                                                                                    <label
                                                                                        class="control-label">Duration</label>
                                                                                    <select name="duration"
                                                                                        class="form-control"
                                                                                        id="call-duration">
                                                                                        <option value="300" selected>5m
                                                                                        </option>
                                                                                        <option value="600">10m</option>
                                                                                        <option value="900">15m</option>
                                                                                        <option value="1800">30m
                                                                                        </option>
                                                                                        <option value="2700">45m
                                                                                        </option>
                                                                                        <option value="3600">1h</option>
                                                                                        <option value="7200">2h</option>
                                                                                    </select>
                                                                                </div>
                                                                            </div>

                                                                            <!-- Date End -->
                                                                            <div class="row">
                                                                                <div class="cell col-sm-12 form-group">
                                                                                    <label class="control-label">
                                                                                        Date End <span
                                                                                            class="required-sign">*</span>
                                                                                    </label>
                                                                                    <div class="field">
                                                                                        <div
                                                                                            class="input-group-container-2">
                                                                                            <div class="input-group">
                                                                                                <input type="text"
                                                                                                    class="form-control datepicker"
                                                                                                    id="date-end"
                                                                                                    name="date_end"
                                                                                                    placeholder="Select date">
                                                                                                <span
                                                                                                    class="input-group-btn">
                                                                                                    <button
                                                                                                        type="button"
                                                                                                        class="btn btn-default btn-icon"><i
                                                                                                            class="far fa-calendar"></i></button>
                                                                                                </span>
                                                                                            </div>
                                                                                            <div class="input-group">
                                                                                                <input type="text"
                                                                                                    class="form-control timepicker"
                                                                                                    id="time-end"
                                                                                                    name="time_end"
                                                                                                    placeholder="Select time">
                                                                                                <span
                                                                                                    class="input-group-btn">
                                                                                                    <button
                                                                                                        type="button"
                                                                                                        class="btn btn-default btn-icon"><i
                                                                                                            class="far fa-clock"></i></button>
                                                                                                </span>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>

                                                                            <!-- Parent -->
                                                                            <div class="row">
                                                                                <div class="cell col-sm-12 form-group">
                                                                                    <label
                                                                                        class="control-label">Parent</label>
                                                                                    <div
                                                                                        class="input-group input-group-link-parent">
                                                                                        <span class="input-group-item">
                                                                                            <select
                                                                                                class="form-control radius-left"
                                                                                                name="parent_type"
                                                                                                id="parent-type">
                                                                                                <option value="Lead"
                                                                                                    selected>Lead
                                                                                                </option>
                                                                                                <option value="Contact">
                                                                                                    Contact</option>
                                                                                                <option value="Account">
                                                                                                    Account</option>
                                                                                            </select>
                                                                                        </span>
                                                                                        <span
                                                                                            class="input-group-item input-group-item-middle">
                                                                                            <input class="form-control"
                                                                                                type="text"
                                                                                                name="parent_name"
                                                                                                id="parent-name"
                                                                                                placeholder="Select parent">
                                                                                        </span>
                                                                                        <span class="input-group-btn">
                                                                                            <button type="button"
                                                                                                class="btn btn-default btn-icon"
                                                                                                id="search-parent"><i
                                                                                                    class="fas fa-search"></i></button>
                                                                                            <button type="button"
                                                                                                class="btn btn-default btn-icon"
                                                                                                id="clear-parent"><i
                                                                                                    class="fas fa-times"></i></button>
                                                                                        </span>
                                                                                    </div>
                                                                                </div>
                                                                            </div>

                                                                            <!-- Description -->
                                                                            <div class="row">
                                                                                <div class="cell col-sm-12 form-group">
                                                                                    <label
                                                                                        class="control-label">Description</label>
                                                                                    <textarea
                                                                                        class="form-control auto-height"
                                                                                        id="call-description"
                                                                                        name="description" rows="3"
                                                                                        placeholder="Enter call details..."></textarea>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="side">
                                                                <div class="panel panel-default first">
                                                                    <div class="panel-body panel-body-form">
                                                                        <!-- Assigned User -->
                                                                        <div class="row">
                                                                            <div
                                                                                class="cell form-group col-sm-6 col-md-12">
                                                                                <label class="control-label">
                                                                                    Assigned User <span
                                                                                        class="required-sign">*</span>
                                                                                </label>
                                                                                <div class="input-group">

                                                                                        <select name="assigned_user_name" id="assigned-user" class="form-control" required> 
                                                                                                @if($lead != null)
                                                                                                <option value="{{ $lead->user->id }}" selected>
                                                                                                        {{ $lead->user->name }}
                                                                                                </option>
                                                                                                @endif
                                                                                        </select>
                                                                                   
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                        <!-- Teams -->
                                                                        <div class="row">
                                                                            <div
                                                                                class="cell form-group col-sm-6 col-md-12">
                                                                                <label
                                                                                    class="control-label">Teams</label>
                                                                                <input class="form-control"
                                                                                    type="text" id="teams"
                                                                                    placeholder="Enter teams">
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <!-- Attendees Section -->
                                                                <div
                                                                    class="panel panel-default panel-attendees headered sticked last">
                                                                    <div class="panel-heading">
                                                                        <h4 class="panel-title">Attendees</h4>
                                                                    </div>
                                                                    <div class="panel-body panel-body-form">
                                                                        <!-- Users -->
                                                                        <div class="row">
                                                                            <div
                                                                                class="cell form-group col-sm-6 col-md-12">
                                                                                <label
                                                                                    class="control-label">Users</label>
                                                                                <input class="form-control"
                                                                                    type="text" id="users"
                                                                                    placeholder="Enter users">
                                                                            </div>
                                                                        </div>

                                                                        <!-- Contacts -->
                                                                        <div class="row">
                                                                            <div
                                                                                class="cell form-group col-sm-6 col-md-12">
                                                                                <label
                                                                                    class="control-label">Contacts</label>
                                                                                <input class="form-control"
                                                                                    type="text" id="contacts"
                                                                                    placeholder="Enter contacts">
                                                                            </div>
                                                                        </div>

                                                                        <!-- Leads -->
                                                                        <div class="row">
                                                                            <div
                                                                                class="cell form-group col-sm-6 col-md-12">
                                                                                <label
                                                                                    class="control-label">Leads</label>
                                                                                <input class="form-control"
                                                                                    type="text" id="leads"
                                                                                    placeholder="Enter leads">
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
        <script
            src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
        <script
            src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-timepicker/0.5.2/js/bootstrap-timepicker.min.js"></script>

        <script>
            $(document).ready(function () {
                // Initialize datepickers
                $('.datepicker').datepicker({
                    format: 'dd.mm.yyyy',
                    autoclose: true,
                    todayHighlight: true
                });

                // Initialize timepickers
                $('.timepicker').timepicker({
                    showMeridian: false,
                    minuteStep: 5
                });

                // Set current date and time
                function setCurrentDateTime() {
                    const today = new Date();
                    let day = today.getDate().toString().padStart(2, '0');
                    let month = (today.getMonth() + 1).toString().padStart(2, '0');
                    let year = today.getFullYear();
                    let todayStr = `${day}.${month}.${year}`;

                    // Round time to next 5 minutes
                    const minutes = today.getMinutes();
                    const roundedMinutes = Math.ceil(minutes / 5) * 5;
                    let hours = today.getHours();
                    let newMinutes = roundedMinutes;
                    
                    if (roundedMinutes >= 60) {
                        hours += 1;
                        newMinutes = 0;
                        if (hours >= 24) {
                            hours = 0;
                            // If we cross midnight, increment the date
                            today.setDate(today.getDate() + 1);
                            day = today.getDate().toString().padStart(2, '0');
                            month = (today.getMonth() + 1).toString().padStart(2, '0');
                            year = today.getFullYear();
                            todayStr = `${day}.${month}.${year}`;
                        }
                    }
                    
                    const timeStr = `${hours.toString().padStart(2, '0')}:${newMinutes.toString().padStart(2, '0')}`;

                    // Set values
                    $('#date-start').val(todayStr);
                    $('#time-start').val(timeStr);
                    $('#date-end').val(todayStr);
                    
                    // Calculate end time based on default duration
                    calculateEndTime();
                }

                // Reset form to default values
                function resetForm() {
                    // Clear all input fields
                    $('#call-name').val('');
                    $('#call-description').val('');
                    // $('#assigned-user').val('');
                    $('#teams').val('');
                    $('#users').val('');
                    $('#contacts').val('');
                    $('#leads').val('');
                    
                    // Reset select fields to default values
                    $('#call-status').val('Planned');
                    $('#call-direction').val('Outbound');
                    $('#call-duration').val('300');
                    $('#parent-type').val('Lead');
                    $('#parent-name').val('');
                    
                    // Set current date and time
                    setCurrentDateTime();
                    
                    // Clear any validation errors
                    $('.form-control').removeClass('is-invalid');
                    $('.error-message').remove();
                    
                    // If we have a lead ID, repopulate the parent fields
                    const leadId = $('#lead_id').val();
                    if (leadId) {
                        @if(isset($parentData))
                            $('#parent-type').val('{{ $parentData['type'] }}');
                            $('#parent-name').val('{{ $parentData['name'] }}');
                        @endif
                    }
                }

                // Calculate end time based on start time and duration
                function calculateEndTime() {
                    const dateStart = $('#date-start').val();
                    const timeStart = $('#time-start').val();
                    const duration = parseInt($('#call-duration').val());

                    if (dateStart && timeStart && duration) {
                        const [day, month, year] = dateStart.split('.');
                        const [hours, minutes] = timeStart.split(':');

                        const startDate = new Date(year, month - 1, day, hours, minutes);
                        const endDate = new Date(startDate.getTime() + duration * 1000);

                        const formattedEndDate = `${endDate.getDate().toString().padStart(2, '0')}.${(endDate.getMonth() + 1).toString().padStart(2, '0')}.${endDate.getFullYear()}`;
                        const formattedEndTime = `${endDate.getHours().toString().padStart(2, '0')}:${endDate.getMinutes().toString().padStart(2, '0')}`;

                        $('#date-end').val(formattedEndDate);
                        $('#time-end').val(formattedEndTime);
                    }
                }

                // Show notification message
                function showNotification(message, type) {
                    const notification = $('#notification');
                    notification.text(message);
                    notification.removeClass('success error info').addClass(type);
                    notification.addClass('show');

                    setTimeout(() => {
                        notification.removeClass('show');
                    }, 3000);
                }

                // Validate form fields
                function validateForm() {
                    let isValid = true;
                    
                    // Clear previous validations
                    $('.form-control').removeClass('is-invalid');
                    $('.error-message').remove();
                    
                    // Validate required fields
                    if (!$('#call-name').val()) {
                        $('#call-name').addClass('is-invalid');
                        $('#call-name').after('<div class="error-message">Call name is required</div>');
                        isValid = false;
                    }
                    
                    if (!$('#date-start').val() || !$('#time-start').val()) {
                        if (!$('#date-start').val()) {
                            $('#date-start').addClass('is-invalid');
                            $('#date-start').after('<div class="error-message">Start date is required</div>');
                        }
                        if (!$('#time-start').val()) {
                            $('#time-start').addClass('is-invalid');
                            $('#time-start').after('<div class="error-message">Start time is required</div>');
                        }
                        isValid = false;
                    }
                    
                    if (!$('#assigned-user').val()) {
                        $('#assigned-user').addClass('is-invalid');
                        $('#assigned-user').after('<div class="error-message">Assigned user is required</div>');
                        isValid = false;
                    }
                    
                    return isValid;
                }

                // Event listeners
                $('#date-start, #time-start, #call-duration').on('change', calculateEndTime);

                $('#reset-form').click(function(e) {
                    e.preventDefault();
                    resetForm();
                    showNotification('Form has been reset to default values', 'info');
                });

                $('#clear-parent').click(function () {
                    $('#parent-name').val('');
                    showNotification('Parent cleared', 'info');
                });

                $('#search-parent').click(function () {
                    showNotification('Search functionality would open here', 'info');
                });

                $('#search-user').click(function () {
                    showNotification('User search would open here', 'info');
                });

                $('#save-call').click(function() {
                    if (!validateForm()) {
                        showNotification('Please fix the validation errors', 'error');
                        return;
                    }
                    
                    const formData = {
                        'name': $('#call-name').val(),
                        'status': $('#call-status').val(),
                        'direction': $('#call-direction').val(),
                        'date_start': $('#date-start').val(),  
                        'time_start': $('#time-start').val(),
                        'date_end': $('#date-end').val(),
                        'time_end': $('#time-end').val(),
                        'duration': $('#call-duration').val(),
                        'parent_type': $('#parent-type').val(),
                        'parent_name': $('#parent-name').val(),
                        'description': $('#call-description').val(),
                        'assigned_user_name': $('#assigned-user').val(),
                        'teams': $('#teams').val(),
                        'users': $('#users').val(),
                        'contacts': $('#contacts').val(),
                        'leads': $('#leads').val(),
                        'lead_id': $('#lead_id').val()
                    };

                    // Show loading state
                    const saveBtn = $('#save-call');
                    const originalText = saveBtn.html();
                    saveBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Saving...');

                    // Send AJAX request
                    $.ajax({
                        type: 'POST',
                        url: '{{ route("store.call", ['lead' => $lead ?? null]) }}',
                        data: formData,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function (response) {
                            if (response.success) {
                                showNotification('Call scheduled successfully!', 'success');
                                // Reset form after successful save
                                resetForm();
                            } else {
                                showNotification(response.message || 'Error saving call', 'error');
                            }
                        },
                        error: function (xhr) {
                            let message = 'Error saving call';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                message = xhr.responseJSON.message;
                            }
                            showNotification(message, 'error');
                        },
                        complete: function () {
                            saveBtn.prop('disabled', false).html(originalText);
                        }
                    });
                });

                // Initialize form
                resetForm();

                // Pre-fill parent fields if we have a lead
                const leadId = $('#lead_id').val();
                if (leadId) {
                    @if(isset($parentData))
                        $('#parent-type').val('{{ $parentData['type'] }}');
                        $('#parent-name').val('{{ $parentData['name'] }}');
                    @endif
                }
            });
        </script>
    @endpush

    @include('layouts.footer')
</body>
</html>