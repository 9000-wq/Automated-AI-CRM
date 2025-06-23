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

        /* Isolated container for call scheduling */
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

        .call-scheduling-container .modal-title-text a {
            color: var(--primary-color);
            text-decoration: none;
        }

        .call-scheduling-container .close,
        .call-scheduling-container .collapse-button {
            color: #6c757d;
            opacity: 0.7;
            font-size: 18px;
            padding: 0 5px;
            cursor: pointer;
        }

        .call-scheduling-container .close:hover,
        .call-scheduling-container .collapse-button:hover {
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

        .call-scheduling-container .input-group-link-parent .btn {
            padding: 6px 8px;
        }

        .call-scheduling-container .input-group-link-parent .form-control {
            height: 32px;
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

        .call-scheduling-container .avatar {
            width: 18px;
            height: 18px;
            border-radius: 50%;
            margin-right: 8px;
            object-fit: cover;
        }

        .call-scheduling-container .avatar-in-input {
            position: absolute;
            right: 60px;
            top: 7px;
        }

        .call-scheduling-container .link-container {
            margin-bottom: 8px;
        }

        .call-scheduling-container .list-group-item {
            padding: 6px 12px;
            border: 1px solid #e9ecef;
            border-radius: var(--border-radius);
            margin-bottom: 5px;
            background-color: #f8f9fa;
            font-size: 13px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .call-scheduling-container .list-group-item .remove-item {
            cursor: pointer;
            color: #6c757d;
        }

        .call-scheduling-container .list-group-item .remove-item:hover {
            color: var(--danger-color);
        }

        .call-scheduling-container .add-team .form-control {
            border-radius: var(--border-radius) 0 0 var(--border-radius);
        }

        .call-scheduling-container .add-team .btn {
            border-radius: 0 var(--border-radius) var(--border-radius) 0;
            padding: 6px 10px;
            background-color: #e9ecef;
            border: 1px solid #ced4da;
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

        .call-scheduling-container .sticked {
            position: relative;
        }

        .call-scheduling-container .headered .panel-heading {
            padding: 8px 15px;
        }

        .call-scheduling-container .first {
            margin-top: 0;
        }

        .call-scheduling-container .last {
            margin-bottom: 0;
        }

        .call-scheduling-container .call-status-badge {
            padding: 4px 8px;
            border-radius: 10px;
            font-size: 12px;
            font-weight: 500;
        }

        .call-scheduling-container .status-planned {
            background-color: #e0f7fa;
            color: #00838f;
        }

        .call-scheduling-container .status-held {
            background-color: #e8f5e9;
            color: #2e7d32;
        }

        .call-scheduling-container .status-not-held {
            background-color: #ffebee;
            color: #c62828;
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

        .call-scheduling-container .card-top-border {
            width: 100%;
            background-color: var(--primary-color);
            height: 10px;
            border-radius: 10px 10px 0px 0px;
        }

        .call-scheduling-container .call-history-container {
            margin-top: 30px;
            background: white;
            border-radius: 8px;
            box-shadow: var(--box-shadow);
            padding: 15px;
        }

        .call-scheduling-container .call-history-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 15px;
            color: #495057;
            display: flex;
            align-items: center;
        }

        .call-scheduling-container .call-history-title i {
            margin-right: 10px;
            color: var(--primary-color);
        }

        .call-scheduling-container .call-history-item {
            padding: 10px 15px;
            border-bottom: 1px solid #e9ecef;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .call-scheduling-container .call-history-item:last-child {
            border-bottom: none;
        }

        .call-scheduling-container .call-history-info {
            flex: 1;
        }

        .call-scheduling-container .call-history-name {
            font-weight: 500;
            margin-bottom: 3px;
        }

        .call-scheduling-container .call-history-meta {
            font-size: 12px;
            color: #6c757d;
            display: flex;
            gap: 15px;
        }

        .call-scheduling-container .call-history-actions button {
            padding: 3px 8px;
            font-size: 12px;
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
                <button type="button" class="btn btn-default btn-xs-wide">
                    <i class="fas fa-expand me-1"></i> Full Form
                </button>
                <button type="button" id="cancel-call" class="btn btn-default btn-xs-wide radius-right">
                    <i class="fas fa-times me-1"></i> Cancel
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
                                                                                        <option value="Not Held">Not
                                                                                            Held</option>
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
                                                                                    <input class="form-control"
                                                                                        type="text"
                                                                                        name="assigned_user_name"
                                                                                        id="assigned-user"
                                                                                        placeholder="Assign user">
                                                                                    <span class="input-group-btn">
                                                                                        <button type="button"
                                                                                            class="btn btn-default btn-icon"
                                                                                            id="assign-to-me"><i
                                                                                                class="fas fa-user"></i></button>
                                                                                        <button type="button"
                                                                                            class="btn btn-default btn-icon"
                                                                                            id="search-user"><i
                                                                                                class="fas fa-angle-up"></i></button>
                                                                                    </span>
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                        <!-- Teams -->
                                                                        <div class="row">
                                                                            <div
                                                                                class="cell form-group col-sm-6 col-md-12">
                                                                                <label
                                                                                    class="control-label">Teams</label>
                                                                                <div class="link-container list-group"
                                                                                    id="teams-container">
                                                                                    <!-- Teams will be added here -->
                                                                                </div>
                                                                                <div class="input-group add-team">
                                                                                    <input class="form-control"
                                                                                        type="text" id="new-team"
                                                                                        placeholder="Select team">
                                                                                    <span class="input-group-btn">
                                                                                        <button type="button"
                                                                                            class="btn btn-default btn-icon"
                                                                                            id="add-team-btn"><span
                                                                                                class="fas fa-plus"></span></button>
                                                                                    </span>
                                                                                </div>
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
                                                                                <div class="link-container list-group"
                                                                                    id="users-container">
                                                                                    <!-- Users will be added here -->
                                                                                </div>
                                                                                <div class="input-group add-team">
                                                                                    <input class="form-control"
                                                                                        type="text" id="new-user"
                                                                                        placeholder="Add user">
                                                                                    <span class="input-group-btn">
                                                                                        <button type="button"
                                                                                            class="btn btn-default btn-icon"
                                                                                            id="add-user-btn"><span
                                                                                                class="fas fa-plus"></span></button>
                                                                                    </span>
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                        <!-- Contacts -->
                                                                        <div class="row">
                                                                            <div
                                                                                class="cell form-group col-sm-6 col-md-12">
                                                                                <label
                                                                                    class="control-label">Contacts</label>
                                                                                <div class="link-container list-group"
                                                                                    id="contacts-container">
                                                                                    <!-- Contacts will be added here -->
                                                                                </div>
                                                                                <div class="input-group add-team">
                                                                                    <input class="form-control"
                                                                                        type="text" id="new-contact"
                                                                                        placeholder="Add contact">
                                                                                    <span class="input-group-btn">
                                                                                        <button type="button"
                                                                                            class="btn btn-default btn-icon"
                                                                                            id="add-contact-btn"><span
                                                                                                class="fas fa-plus"></span></button>
                                                                                    </span>
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                        <!-- Leads -->
                                                                        <div class="row">
                                                                            <div
                                                                                class="cell form-group col-sm-6 col-md-12">
                                                                                <label
                                                                                    class="control-label">Leads</label>
                                                                                <div class="link-container list-group"
                                                                                    id="leads-container">
                                                                                    <!-- Leads will be added here -->
                                                                                </div>
                                                                                <div class="input-group add-team">
                                                                                    <input class="form-control"
                                                                                        type="text" id="new-lead"
                                                                                        placeholder="Add lead">
                                                                                    <span class="input-group-btn">
                                                                                        <button type="button"
                                                                                            class="btn btn-default btn-icon"
                                                                                            id="add-lead-btn"><span
                                                                                                class="fas fa-plus"></span></button>
                                                                                    </span>
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
                    const day = today.getDate().toString().padStart(2, '0');
                    const month = (today.getMonth() + 1).toString().padStart(2, '0');
                    const year = today.getFullYear();
                    const todayStr = `${day}.${month}.${year}`;

                    // Set current time rounded to next 5 minutes
                    const minutes = today.getMinutes();
                    const roundedMinutes = Math.ceil(minutes / 5) * 5;
                    let hours = today.getHours();
                    let newMinutes = roundedMinutes;
                    if (roundedMinutes >= 60) {
                        hours += 1;
                        newMinutes = 0;
                    }
                    const timeStr = `${hours.toString().padStart(2, '0')}:${newMinutes.toString().padStart(2, '0')}`;

                    // Set values
                    $('#date-start').val(todayStr);
                    $('#time-start').val(timeStr);
                    $('#date-end').val(todayStr);

                    calculateEndTime();
                }

                function resetForm() {
                    $('#call-name').val('');
                    $('#call-status').val('Planned');
                    $('#call-direction').val('Outbound');
                    $('#parent-name').val('');
                    $('#parent-type').val('Lead');
                    $('#call-description').val('');
                    $('#assigned-user').val('');

                    setCurrentDateTime();

                    $('#teams-container, #users-container, #contacts-container, #leads-container').empty();

                    $('#new-team, #new-user, #new-contact, #new-lead').val('');
                }

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

                $('#date-start, #time-start, #call-duration').on('change', calculateEndTime);

                function showNotification(message, type) {
                    const notification = $('#notification');
                    notification.text(message);
                    notification.removeClass('success error info').addClass(type);
                    notification.addClass('show');

                    setTimeout(() => {
                        notification.removeClass('show');
                    }, 3000);
                }

                function addListItem(containerId, inputId, type) {
                    const value = $(`#${inputId}`).val().trim();
                    if (value) {
                        $(`#${containerId}`).append(`
                                    <div class="list-group-item">
                                        ${value}
                                        <i class="fas fa-times remove-item"></i>
                                    </div>
                                `);
                        $(`#${inputId}`).val('');
                        bindItemRemoval();
                        showNotification(`${type} added successfully`, 'success');
                    }
                }

                function bindItemRemoval() {
                    $('.remove-item').off('click').on('click', function () {
                        $(this).parent().remove();
                        showNotification('Item removed', 'info');
                    });
                }
        bindItemRemoval();

                    $('#add-team-btn').click(() => addListItem('teams-container', 'new-team', 'Team'));
                    $('#add-user-btn').click(() => addListItem('users-container', 'new-user', 'User'));
                    $('#add-contact-btn').click(() => addListItem('contacts-container', 'new-contact', 'Contact'));
                    $('#add-lead-btn').click(() => addListItem('leads-container', 'new-lead', 'Lead'));

                    $('#assign-to-me').click(function () {
                        $('#assigned-user').val('Your Name');
                        showNotification('Assigned to you', 'success');
                    });

                    $('#clear-parent').click(function () {
                        $('#parent-name').val('');
                        showNotification('Parent cleared', 'info');
                    });

                    $('#search-parent').click(function () {
                        showNotification('Search functionality would open here', 'info');
                    });

                    function collectItems(containerId) {
                        const items = [];
                        $(`#${containerId} .list-group-item`).each(function () {
                            items.push($(this).clone().children().remove().end().text().trim());
                        });
                        return items;
                    }

                    $('#save-call').click(function () {
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
                            'teams': $('#new-team').val(),
                            'users': $('#new-user').val(),
                            'contacts': $('#new-contact').val(),
                            'leads': $('#new-lead').val(),
                            'lead_id': $('#lead_id').val()
                        };

                        // Validate required fields
                        if (!formData.name) {
                            showNotification('Call name is required!', 'error');
                            return;
                        }

                        if (!formData.date_start || !formData.time_start) {
                            showNotification('Start date and time are required!', 'error');
                            return;
                        }

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
                                    if (response.data.lead_id) {
                                        // window.location.href = `/leads/${response.data.lead_id}`;
                                    } else {
                                        resetForm();
                                    }
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

                    $('#cancel-call').click(function () {
                        if (confirm('Are you sure you want to cancel? All changes will be lost.')) {
                            const leadId = $('#lead_id').val();
                            if (leadId) {
                                window.location.href = `/leads/${leadId}`;
                            } else {
                                resetForm();
                                showNotification('Call creation canceled', 'info');
                            }
                        }
                    });

                    // Initialize form
                    setCurrentDateTime();

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